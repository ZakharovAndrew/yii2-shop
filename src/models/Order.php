<?php

namespace app\modules\shop\models;

use yii\db\ActiveRecord;
use Yii;
use ZakharovAndrew\shop\Module;

/**
 * Модель для таблицы `order`.
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
        return '{{%order}}';
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
            [['created_at', 'updated_at'], 'safe'],
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
}