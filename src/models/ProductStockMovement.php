<?php

namespace ZakharovAndrew\shop\models;

use Yii;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\user\models\User;

class ProductStockMovement extends \yii\db\ActiveRecord
{
    public function rules()
    {
        return [
            [['product_id', 'quantity', 'user_id'], 'required'],
            [['product_id', 'user_id'], 'integer'],
            ['quantity', 'integer'],
            ['comment', 'string', 'max' => 255],
            ['created_at', 'safe'],
        ];
    }
    
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }
}