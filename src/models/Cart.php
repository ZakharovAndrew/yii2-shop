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
    private $sessionKey = 'cart';
    
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
        $cart = Yii::$app->session->get($this->sessionKey, []);
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }
        
        if ($cart[$productId] == 0) {
            unset($cart[$productId]);
        }
        
        Yii::$app->session->set($this->sessionKey, $cart);
        
        return $cart[$productId] ?? 0;
    }
    
    private function addToDatabaseCart($userId, $productId, $quantity)
    {
        $cartItem = Cart::findOne(['user_id' => $userId, 'product_id' => $productId]);
        if ($cartItem) {
            $cartItem->quantity += $quantity;
        } else {
            $cartItem = new Cart();
            $cartItem->user_id = $userId;
            $cartItem->product_id = $productId;
            $cartItem->quantity = $quantity;
        }
        $cartItem->save();
        
        if ($cartItem->quantity == 0) {
            $cartItem->delete();
            
            return 0;
        }
        
        return $cartItem->quantity;
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
        $cart = Yii::$app->session->get($this->sessionKey, []);
        $products = [];
        foreach ($cart as $productId => $quantity) {
            $product = Product::findOne($productId);
            if ($product) {
                $products[] = [
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
            Yii::$app->session->remove($this->sessionKey);
        } else {
            Cart::deleteAll(['user_id' => Yii::$app->user->id]);
        }
    }
}