<?php

namespace ZakharovAndrew\shop\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use ZakharovAndrew\shop\models\Order;
use ZakharovAndrew\shop\models\OrderItem;

class OrderController extends Controller
{
    /**
     * Просмотр деталей заказа
     * @param int $id ID заказа
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $order = $this->findModel($id);
        
        // Проверяем, что заказ принадлежит текущему пользователю
        if ($order->user_id != Yii::$app->user->id) {
            throw new NotFoundHttpException('Заказ не найден.');
        }
        
        $items = OrderItem::find()->where(['order_id' => $id])->with('product')->all();
        
        return $this->render('view', [
            'order' => $order,
            'items' => $items,
        ]);
    }

    /**
     * Список заказов пользователя
     * @return string
     */
    public function actionIndex()
    {
        $orders = Order::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'orders' => $orders,
        ]);
    }
    
    public function actionCancel($id)
    {
        $order = $this->findModel($id);
        
        foreach ($order->getOrderItems() as $item) {
            $product = $item->product;
            $product->addToStock($item->quantity, Yii::$app->user->id, "Order #".$order->id);
        }
    }

    /**
     * Находит модель заказа по ID
     * @param int $id
     * @return Order
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Заказ не найден.');
    }
}