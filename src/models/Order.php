<?php

/**
 * Shop Order
 *  
 * @link https://github.com/ZakharovAndrew/yii2-shop/
 * @copyright Copyright (c) 2023-2025 Zakharov Andrew
 */

namespace ZakharovAndrew\shop\models;

use yii\db\ActiveRecord;
use Yii;
use ZakharovAndrew\shop\Module;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $delivery_method
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $middle_name
 * @property string|null $phone
 * @property string|null $postcode
 * @property string|null $city
 * @property string|null $address
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class Order extends ActiveRecord
{
    
    // Order status constants
    const STATUS_NOT_ACCEPTED = 0; // Order not accepted yet
    const STATUS_PROCESSING = 1; // Processing
    const STATUS_REJECTED = 2; // Rejected
    const STATUS_CANCELED_BY_USER = 3; // Canceled by user
    const STATUS_ASSEMBLING = 4; // Assembling
    const STATUS_AWAITING_PAYMENT = 5; // Awaiting payment
    const STATUS_PAID = 6; // Paid
    const STATUS_ASSEMBLED = 7; // Assembled
    const STATUS_SHIPPED = 8; // Shipped to customer
    const STATUS_DELIVERED = 9; // Delivered to customer
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'status'], 'required'],
            [['user_id', 'delivery_method', 'status'], 'integer'],
            [['first_name', 'last_name', 'middle_name', 'phone', 'postcode', 'city', 'address'], 'string', 'max' => 255],
            ['delivery_method', 'in', 'range' => function() {
                return array_keys(self::getDeliveryMethods());
            }, 'message' => 'Выберите корректный способ доставки'],
            [['created_at', 'updated_at'], 'safe'],
            [['total_sum'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Module::t('User ID'),
            'delivery_method' => Module::t('Delivery Method'),
            'first_name' => Module::t('First Name'),
            'last_name' => Module::t('Last Name'),
            'middle_name' => Module::t('Middle Name'),
            'phone' => Module::t('Phone'),
            'postcode' => Module::t('Postcode'),
            'city' => Module::t('City'),
            'address' => Module::t('Address'),
            'status' => Module::t('Status'),
            'created_at' => Module::t('Created At'),
            'updated_at' => Module::t('Updated At'),
        ];
    }

    /**
     * Связь с моделью User.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    
    /**
     * Returns the list of order statuses.
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_NOT_ACCEPTED => Module::t('Order not accepted yet'),
            self::STATUS_PROCESSING => Module::t('Processing'),
            self::STATUS_REJECTED => Module::t('Rejected'),
            self::STATUS_CANCELED_BY_USER => Module::t('Canceled by user'),
            self::STATUS_ASSEMBLING => Module::t('Assembling'),
            self::STATUS_AWAITING_PAYMENT => Module::t('Awaiting payment'),
            self::STATUS_PAID => Module::t('Paid'),
            self::STATUS_ASSEMBLED => Module::t('Assembled'),
            self::STATUS_SHIPPED => Module::t('Shipped to customer'),
            self::STATUS_DELIVERED => Module::t('Delivered to customer'),
        ];
    }
    
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        if (!$insert) {
            $this->updateTotalSum();
        }
    }

    public function updateTotalSum()
    {
        $sum = (float) OrderItem::find()
            ->where(['order_id' => $this->id])
            ->sum('price * quantity');
            
        $this->updateAttributes(['total_sum' => $sum]);
    }
    
    /**
     * Получает текстовое описание статуса заказа
     * @return string
     */
    public function getStatusText()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status] ?? 'Неизвестный статус';
    }
    
    public static function getDeliveryMethods()
    {
        /** @var \ZakharovAndrew\shop\Module $module */
        $module = Yii::$app->getModule('shop');
        
        return $module->deliveryMethods;
    }

    public function getDeliveryMethodText()
    {
        $methods = self::getDeliveryMethods();
        
        return $methods[$this->delivery_method] ?? 'Unknown method of delivery';
    }
    
    public static function getDeliveryPrices()
    {
        /** @var \ZakharovAndrew\shop\Module $module */
        $module = Yii::$app->getModule('shop');
        
        return $module->deliveryPrices;
    }
}