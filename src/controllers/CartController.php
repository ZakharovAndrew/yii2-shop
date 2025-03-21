<?php

namespace ZakharovAndrew\shop\controllers;

use Yii;
use yii\web\Controller;
use ZakharovAndrew\shop\models\Cart;
use ZakharovAndrew\shop\models\Product;
use yii\web\Response;

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
        $quantity = Yii::$app->request->post('quantity', 1); // Получаем количество (по умолчанию 1)

        if (!$productId) {
            return ['success' => false, 'message' => 'Product ID is required.'];
        }

        // Находим товар в базе данных
        $product = Product::findOne($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        // Добавляем товар в корзину
        $cart = new Cart();
        $product_quantity = $cart->addToCart($product->id, $quantity);

        return [
            'success' => true,
            'message' => 'Product added to cart.',
            'quantity' => $product_quantity,
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

        // Находим товар в базе данных
        $product = Product::findOne($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        // Добавляем товар в корзину
        $cart = new Cart();
        $product_quantity = $cart->addToCart($product->id, -1);

        return [
            'success' => true,
            'quantity' => $product_quantity,
        ];
    }

    public function actionIndex()
    {
        $cart = new Cart();
        $cartItems = $cart->getCart();
        return $this->render('index', [
            'cartItems' => $cartItems,
        ]);
    }
    
    /**
     * Удаление товара из корзины через AJAX.
     *
     * @return array
     */
    public function actionRemove()
    {
        Yii::$app->response->format = Response::FORMAT_JSON; // Устанавливаем формат ответа JSON

        $productId = Yii::$app->request->post('productId'); // Получаем ID товара из POST-запроса

        if (!$productId) {
            return ['success' => false, 'message' => 'Product ID is required.'];
        }

        // Удаляем товар из корзины
        $cart = new Cart();
        $cart->removeFromCart($productId);

        return [
            'success' => true,
            'message' => 'Product removed from cart.',
            'cartCount' => $cart->getTotalQuantity(), // Общее количество товаров в корзине
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