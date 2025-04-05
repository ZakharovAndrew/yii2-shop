<?php

namespace ZakharovAndrew\shop\models;

use Yii;
use ZakharovAndrew\shop\Module;

class ProductStockMovement extends \yii\db\ActiveRecord
{
    public function rules()
    {
        return [
            [['product_id', 'quantity', 'user_id', 'order_id'], 'required'],
            [['product_id', 'user_id', 'order_id'], 'integer'],
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
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord && empty($this->created_at)) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            return true;
        }
        return false;
    }
}