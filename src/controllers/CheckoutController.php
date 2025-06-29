<?php

namespace ZakharovAndrew\shop\controllers;

use ZakharovAndrew\shop\models\Order;
use ZakharovAndrew\shop\models\OrderItem;
use ZakharovAndrew\shop\models\Cart;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\user\models\User;
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
        $cart = new Cart();
        
        if ($cart->isEmpty()) {
            Yii::$app->session->setFlash('warning', Module::t('Your cart is empty'));
            return $this->redirect(['/shop/cart/index']);
        }

        $cartItems = $cart->getCart();
        foreach ($cartItems as $item) {
            if (!$item->product->canSubtractFromStock($item->quantity)) {
                Yii::$app->session->setFlash('error', $item->product->name.' - Нет необходимого количества товара');
                return $this->redirect('/shop/cart/index');
            }
        }
    
        // Redirect guests to login page with return URL
        /*if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login', 'returnUrl' => Yii::$app->request->url]);
        }*/

        // Initialize new order with default values
        $model = new Order([
            //'user_id' => Yii::$app->user->id,
            'status' => Order::STATUS_NOT_ACCEPTED,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Handle AJAX validation requests
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        // Process form submission
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->user->isGuest) {
                // Создаем временного пользователя
                $user = $this->createTemporaryUser($model);

                if (!$user) {
                    Yii::$app->session->setFlash('error', Module::t('Failed to create user'));
                    return $this->refresh();
                }

                Yii::$app->user->login($user, 3600*24*30); // Авторизуем на 30 дней*/
            }

            $model->user_id = Yii::$app->user->id;

            if ($model->save(false)) {

                // Создаем элементы заказа
                foreach ($cartItems as $item) {
                    $orderItem = new OrderItem([
                        'order_id' => $model->id,
                        'product_id' => $item->product->id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->getActualPrice($item->quantity),
                        'price_without_discount' => $item->product->price,
                    ]);

                    $item->product->subtractFromStock($item->quantity, Yii::$app->user->id,  "Order #".$model->id);

                    if (!$orderItem->save()) {
                        throw new \Exception('Не удалось сохранить элемент заказа');
                    }
                }
                $model->delivery_cost = Order::getDeliveryPrices()[$model->delivery_method];
                $model->updateTotalSum();

                $model->save(false);

                // Очищаем корзину
                $cart->clearCart();

                // Success: redirect to order confirmation
                Yii::$app->session->setFlash('success', Module::t('Your order has been placed successfully!'));
                return $this->redirect(['/shop/order/view', 'id' => $model->id]);
            } else {
                // Save failed
                Yii::$app->session->setFlash('error', 'Error processing your order'.var_export($model->getErrors(), true));
            }
                
        }
        
        // Render checkout form
        return $this->render('index', [
            'model' => $model,
            'totalSum' => $cart->getTotalSum(),
            'deliveryMethods' => Order::getDeliveryMethods(),
            'isGuest' => Yii::$app->user->isGuest,
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
            'formattedPrice' => (is_numeric($price) ? Yii::$app->formatter->asCurrency($price) : $price),
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
    
    private function createTemporaryUser($order)
    {
        $password = User::genPassword(8);
        
        $user = new User([
            'username' => 'user'.time(),
            'name' => $order->first_name . ' '. $order->last_name,
            'email' => $order->email,
            'phone' => str_replace(['-', ' ', '(', ')'], '', $order->phone),
            'status' => User::STATUS_USER,
        ]);
        
        $user->setPassword($password);

        // Trying to send the password to the email and save the password
        if (!$user->setPassword($password) || !$user->save()/* || !$user->sendPasswordEmail($password)*/) {
            throw new \Exception('Ошибка создания временного аккаунта');
        }

        return $user;
    }
    
}
