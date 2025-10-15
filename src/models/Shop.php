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
 * @property string|null $city
 * @property string|null $avatar
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property string|null $address
 * @property string|null $telegram
 * @property string|null $description_after_products
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
            [['description', 'description_after_products'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by'], 'integer'],
            [['name', 'url', 'avatar', 'whatsapp', 'city', 'telegram'], 'string', 'max' => 255],
            [['address'], 'string', 'max' => 500],
            [['url'], 'unique'],
            [['url'], 'match', 'pattern' => '/^[a-z0-9\-]+$/', 'message' => Module::t('URL can contain only Latin letters, numbers and hyphens')],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => \Yii::$app->user->identityClass, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
       return [
            'id' => 'ID',
            'name' => Module::t('Store Name'),
            'description' => Module::t('Description'),
            'url' => 'URL',
            'avatar' => Module::t('Avatar'),
            'created_at' => Module::t('Created At'),
            'updated_at' => Module::t('Updated At'),
            'created_by' => Module::t('Created By'),
            'address' => Module::t('Address'),
            'telegram' => 'Telegram',
            'description_after_products' => Module::t('Description After Products'),
            'city' => Module::t('City'),
            'whatsapp' => 'WhatsApp',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(\Yii::$app->user->identityClass, ['id' => 'created_by']);
    }

    /**
     * Get shop products
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['shop_id' => 'id']);
    }
    
    /**
     * Get full avatar URL
     * 
     * @return string|null
     */
    public function getAvatarUrl()
    {
        if ($this->avatar) {
            return Yii::getAlias('@web/uploads/shops/') . $this->avatar;
        }
        return null;
    }
    
    /**
     * Get shops list for dropdown
     * 
     * @return array
     */
    public static function getShopsList()
    {
        return Yii::$app->cache->getOrSet('list_shops', function () {
            return ArrayHelper::map(self::find()->select(['id', 'name'])->asArray()->all(), 'id', 'name');
        }, 60);
    }
    
    /**
     * Check if current user can edit the shop
     * 
     * @param int $shopId Shop ID to check
     * @return bool
     */
    public static function canEdit($shopId)
    {
        // Admin can edit any shop
        if (Yii::$app->user->identity->isAdmin()) {
            return true;
        }
        
        // Shop owner can edit only their shops
        if (Yii::$app->user->identity->hasRole('shop_owner')) {
            $userShops = Yii::$app->user->identity->getRoleSubjectsArray("shop_owner");
            if (is_array($userShops) && in_array($shopId, $userShops)) {
                return true;
            }
        }
        
        // Creator can edit their shop
        $shop = self::findOne($shopId);
        if ($shop && $shop->created_by == Yii::$app->user->id) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if current user can edit this shop instance
     * 
     * @return bool
     */
    public function getCanEdit()
    {
        return self::canEdit($this->id);
    }
}
