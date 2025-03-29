<?php

namespace ZakharovAndrew\shop\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use ZakharovAndrew\shop\models\Order;
use yii\web\NotFoundHttpException;

class AdminOrderController extends Controller
{
    /**
     * Список всех заказов
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Order::find()->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Просмотр деталей заказа
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $order = $this->findModel($id);

        return $this->render('view', [
            'order' => $order,
        ]);
    }

    /**
     * Изменение статуса заказа
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateStatus($id)
    {
        $order = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $newStatus = Yii::$app->request->post('status');
            if (array_key_exists($newStatus, Order::getStatuses())) {
                $order->status = $newStatus;
                if ($order->save()) {
                    Yii::$app->session->setFlash('success', 'Статус заказа успешно обновлен');
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка при обновлении статуса');
                }
            }
        }

        return $this->redirect(['view', 'id' => $order->id]);
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