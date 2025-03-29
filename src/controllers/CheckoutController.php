<?php

namespace ZakharovAndrew\shop\controllers;

use ZakharovAndrew\shop\models\Order;
use ZakharovAndrew\shop\models\OrderItem;
use ZakharovAndrew\shop\models\Cart;
use ZakharovAndrew\shop\Module;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\web\BadRequestHttpException;

class CheckoutController extends Controller
{
    /**
     * Checkout process - main action
     * @return string|Response
     * @throws BadRequestHttpException
     */
    public function actionIndex()
    {
        // Redirect guests to login page with return URL
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login', 'returnUrl' => Yii::$app->request->url]);
        }

        // Initialize new order with default values
        $model = new Order([
            'user_id' => Yii::$app->user->id,
            'status' => Order::STATUS_NOT_ACCEPTED,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $cart = new Cart();
        $cartItems = $cart->getCart();

        // Handle AJAX validation requests
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        // Process form submission
        if ($model->load(Yii::$app->request->post())) {
            $availableMethods = array_keys(Order::getDeliveryMethods());
            
            // Validate selected delivery method
            if (!in_array($model->delivery_method, $availableMethods)) {
                Yii::$app->session->setFlash('error', Module::t('Invalid delivery method selected'));
            } elseif ($model->save()) {
                
                // Создаем элементы заказа
                foreach ($cartItems as $item) {
                    $orderItem = new OrderItem([
                        'order_id' => $model->id,
                        'product_id' => $item->product->id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price, // Сохраняем текущую цену
                    ]);

                    if (!$orderItem->save()) {
                        throw new \Exception('Не удалось сохранить элемент заказа');
                    }
                }
                $model->delivery_cost = Order::getDeliveryPrices()[$model->delivery_method];
                $model->updateTotalSum();
                
                $model->save();

                // Очищаем корзину
                $cart->clearCart();
                
                // Success: redirect to order confirmation
                Yii::$app->session->setFlash('success', 'Your order has been placed successfully!');
                return $this->redirect(['/shop/order/view', 'id' => $model->id]);
            } else {
                // Save failed
                Yii::$app->session->setFlash('error', 'Error processing your order');
            }
        }

        // user Cart
        $cart = new Cart();
        
        // Render checkout form
        return $this->render('index', [
            'model' => $model,
            'totalSum' => $cart->getTotalSum(),
            'deliveryMethods' => Order::getDeliveryMethods(),
        ]);
    }

    /**
     * AJAX endpoint for delivery price calculation
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionGetDeliveryPrice()
    {
        // Only allow AJAX requests
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException('Only AJAX requests allowed');
        }

        $methodId = Yii::$app->request->post('method_id');
        $methods = Order::getDeliveryMethods();
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Validate delivery method
        if (!isset($methods[$methodId])) {
            return ['success' => false, 'error' => 'Invalid delivery method'];
        }

        // Calculate and return price
        $price = $this->calculateDeliveryPrice($methodId);

        return [
            'success' => true,
            'price' => $price,
            'formattedPrice' => Yii::$app->formatter->asCurrency($price),
        ];
    }

    /**
     * Calculate delivery price based on method
     * @param int $methodId
     * @return float
     */
    protected function calculateDeliveryPrice($methodId)
    {
        $prices = Order::getDeliveryPrices();

        return $prices[$methodId] ?? 0;
    }
}