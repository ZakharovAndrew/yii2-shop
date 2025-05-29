<?php

namespace ZakharovAndrew\shop\controllers;

use Yii;
use yii\web\Controller;
use ZakharovAndrew\shop\models\Cart;
use ZakharovAndrew\shop\models\Product;
use yii\web\Response;
use ZakharovAndrew\shop\Module;

class CartController extends Controller
{
    /**
     * Добавление товара в корзину через AJAX.
     *
     * @return array
     */
    public function actionAdd()
    {
        Yii::$app->response->format = Response::FORMAT_JSON; // Устанавливаем формат ответа JSON

        $productId = Yii::$app->request->post('productId'); // Получаем ID товара из POST-запроса
        $quantity = Yii::$app->request->post('quantity', 1);

        if (!$productId) {
            return ['success' => false, 'message' => 'Product ID is required.'];
        }

        // Находим товар в базе данных
        $product = Product::findOne($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }
        
        $cart = new Cart();
        
        // проверяем достаточно ли продукта на складе
        if ($product->quantity < $cart->getProductQuantity($productId) + $quantity) {
            return ['success' => false, 'message' => Module::t('We don’t have enough items in stock at the moment.')];
        }

        // Add product to cart
        $result = $cart->addToCart($product->id, $quantity);

        return [
            'success' => true,
            'message' => 'Product added to cart.',
            'quantity' => $result['quantity'],
            'price' => $result['price'],
            'total_without_discount' => $result['total_without_discount'],
            'total' => $result['total'],
            'cartTotal' => $cart->getTotalSum()
            
            //'cartCount' => $cart->getTotalQuantity(), // Общее количество товаров в корзине
        ];
    }
    
    public function actionDecrease()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $productId = Yii::$app->request->post('productId');

        if (!$productId) {
            return ['success' => false, 'message' => 'Product ID is required.'];
        }

        // find product
        $product = Product::findOne($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        // Add product to cart
        $cart = new Cart();
        $result = $cart->addToCart($product->id, -1);

        return [
            'success' => true,
            'message' => 'Product added to cart.',
            'quantity' => $result['quantity'],
            'price' => $result['price'],
            'total_without_discount' => $result['total_without_discount'],
            'total' => $result['total'],
            'cartTotal' => $cart->getTotalSum()
            //'cartCount' => $cart->getTotalQuantity(), // Общее количество товаров в корзине
        ];
    }

    public function actionIndex()
    {
        $cart = new Cart();
        $cartItems = $cart->getCart();
        
        return $this->render('index', [
            'cartItems' => $cartItems
        ]);
    }
    
    /**
     * Remove from cart
     *
     * @return array
     */
    public function actionRemove()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $productId = Yii::$app->request->post('productId');

        if (!$productId) {
            return ['success' => false, 'message' => 'Product ID is required.'];
        }

        // remove from cart
        $cart = new Cart();
        $cart->removeFromCart($productId);

        return [
            'success' => true,
            'message' => 'Product removed from cart.',
        ];
    }
    
    public function actionClear()
    {
        Cart::clearCart();
        return  $this->redirect('index');
    }

    /**
     * Получение количества товаров в корзине через AJAX.
     *
     * @return array
     */
    public function actionGetCount()
    {
        Yii::$app->response->format = Response::FORMAT_JSON; // Устанавливаем формат ответа JSON

        $cart = new Cart();
        return [
            'success' => true,
            'cartCount' => $cart->getTotalQuantity(), // Общее количество товаров в корзине
        ];
    }
}