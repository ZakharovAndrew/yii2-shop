<?php

/**
 * Shop
 *  
 * @link https://github.com/ZakharovAndrew/yii2-shop/
 * @copyright Copyright (c) 2023-2025 Zakharov Andrew
 */

namespace ZakharovAndrew\shop\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use ZakharovAndrew\shop\Module;

/**
 * This is the model class for table "shop".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $url
 * @property string|null $avatar
 * @property int $created_at
 * @property int $updated_at
 */
class Shop extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'url'], 'required'],
            [['description'], 'string'],
            [['name', 'url', 'avatar'], 'string', 'max' => 255],
            [['url'], 'unique'],
            [['url'], 'match', 'pattern' => '/^[a-z0-9\-]+$/', 'message' => 'URL может содержать только латинские буквы, цифры и дефисы'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название магазина',
            'description' => Module::t('Description'),
            'url' => 'URL',
            'avatar' => 'Аватарка',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    public function getProducts()
    {
        return $this->hasMany(Product::class, ['shop_id' => 'id']);
    }
    
    /**
     * Полный URL аватарки
     */
    public function getAvatarUrl()
    {
        if ($this->avatar) {
            return Yii::getAlias('@web/uploads/shops/') . $this->avatar;
        }
        return null;
    }
    
    /**
     * Get a list of shops 
     */
    public static function getShopsList()
    {
        return Yii::$app->cache->getOrSet('list_shops', function () {
            return ArrayHelper::map(self::find()->select(['id', 'name'])->asArray()->all(), 'id', 'name');
        }, 60);
    }
}
