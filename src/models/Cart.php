<?php

/**
 * Shop Cart
 *  
 * @link https://github.com/ZakharovAndrew/yii2-shop/
 * @copyright Copyright (c) 2023-2025 Zakharov Andrew
 */

namespace ZakharovAndrew\shop\models;

use Yii;
use yii\db\ActiveRecord;

class Cart extends ActiveRecord
{
    const SESSION_KEY = 'cart';
    
    public static function tableName()
    {
        return 'cart';
    }

    public function rules()
    {
        return [
            [['user_id', 'product_id', 'quantity'], 'required'],
            [['user_id', 'product_id', 'quantity'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }
    
    public function getUser ()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function addToCart($productId, $quantity = 1)
    {
        if (Yii::$app->user->isGuest) {
            return $this->addToSessionCart($productId, $quantity);
        }
        
        return $this->addToDatabaseCart(Yii::$app->user->id, $productId, $quantity);
    }

    private function addToSessionCart($productId, $quantity)
    {
        $cart = Yii::$app->session->get(self::SESSION_KEY, []);
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }
        
        if ($cart[$productId] == 0) {
            unset($cart[$productId]);
        }
        
        Yii::$app->session->set(self::SESSION_KEY, $cart);
        
        $product = Product::findOne($productId);
        $currentQuantity = $cart[$productId] ?? 0;
        
        return [
            'quantity' => $currentQuantity,
            'price' => $product ? $product->getActualPrice($currentQuantity) : 0,
            'total_without_discount' => $product ? $product->price * $currentQuantity : 0,
            'total' => $product ? $product->getActualPrice($currentQuantity) * $currentQuantity : 0
        ];
    }
    
    private function addToDatabaseCart($userId, $productId, $quantity)
    {
        $cartItem = Cart::findOne(['user_id' => $userId, 'product_id' => $productId]);
        $product = Product::findOne($productId);
        
        if ($cartItem) {
            $cartItem->quantity += $quantity;
        } else {
            $cartItem = new Cart();
            $cartItem->user_id = $userId;
            $cartItem->product_id = $productId;
            $cartItem->quantity = $quantity;
        }
        
        if ($cartItem->quantity <= 0) {
            $cartItem->delete();
            $currentQuantity = 0;
        } else {
            $cartItem->save();
            $currentQuantity = $cartItem->quantity;
        }
        
        return [
            'quantity' => $currentQuantity,
            'price' => $product ? $product->getActualPrice($currentQuantity) : 0,
            'total_without_discount' => $product ? $product->price * $currentQuantity : 0,
            'total' => $product ? $product->getActualPrice($currentQuantity) * $currentQuantity : 0
        ];
    }
    
    /**
     * Удаляет товар из корзины
     * 
     * @param int $productId product ID
     * @return bool
     */
    public function removeFromCart($productId)
    {
        if (Yii::$app->user->isGuest) {
            return $this->removeFromSessionCart($productId);
        }
        
        return $this->removeFromDatabaseCart(Yii::$app->user->id, $productId);
    }

    private function removeFromSessionCart($productId)
    {
        $cart = Yii::$app->session->get(self::SESSION_KEY, []);
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Yii::$app->session->set(self::SESSION_KEY, $cart);
            return true;
        }
        
        return false;
    }
    
    private function removeFromDatabaseCart($userId, $productId)
    {
        $cartItem = Cart::findOne(['user_id' => $userId, 'product_id' => $productId]);
        
        if ($cartItem) {
            return (bool)$cartItem->delete();
        }
        
        return false;
    }

    public function getCart()
    {
        if (Yii::$app->user->isGuest) {
            return $this->getSessionCart();
        } else {
            return $this->getDatabaseCart(Yii::$app->user->id);
        }
    }

    private function getSessionCart()
    {
        $cart = Yii::$app->session->get(self::SESSION_KEY, []);
        $products = [];
        foreach ($cart as $productId => $quantity) {
            $product = Product::findOne($productId);
            if ($product) {
                $products[] = (object)[
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            }
        }
        return $products;
    }

    private function getDatabaseCart($userId)
    {
        return Cart::find()->where(['user_id' => $userId])->with('product')->all();
    }

    public static function clearCart()
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->remove(static::SESSION_KEY);
        } else {
            Cart::deleteAll(['user_id' => Yii::$app->user->id]);
        }
    }

    public function getTotalQuantity()
    {
        $items = $this->getCart();
        $total = 0;
        
        foreach ($items as $item) {
            $quantity = $item->quantity ?? 1;
            $total += $quantity;
        }
        
        return $total;
    }
    
    public function getTotalSum()
    {
        $items = $this->getCart();
        $total = 0;
        $totalWithoutDiscount = 0;
        
        foreach ($items as $item) {
            $product = $item->product ?? $item;
            $quantity = $item->quantity ?? 1;
             
            $total += $product->getActualPrice($quantity) * $quantity;
            $totalWithoutDiscount += $product->price * $quantity;
        }
        
        return [
            'total' => $total,
            'total_without_discount' => $totalWithoutDiscount
        ];
    }
    
    /**
     * Проверяет, пуста ли корзина
     * @return bool
     */
    public function isEmpty()
    {
        if (Yii::$app->user->isGuest) {
            // Для гостей - проверяем сессию
            $sessionItems = Yii::$app->session->get(self::SESSION_KEY, []);
            return empty($sessionItems);
        } else {
            // Для авторизованных - проверяем БД
            return !$this->getDatabaseCart(Yii::$app->user->id);
        }
    }
}
