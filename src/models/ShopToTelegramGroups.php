<?php

/**
 * ShopToTelegramGroups
 *  
 * @link https://github.com/ZakharovAndrew/yii2-shop/
 * @copyright Copyright (c) 2023-2025 Zakharov Andrew
 */

namespace ZakharovAndrew\shop\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use ZakharovAndrew\shop\Module;

/**
 * This is the model class for table "shop_to_telegram_groups".
 *
 * @property int $id
 * @property int $shop_id
 * @property int $telegram_group_id
 * @property string $created_at
 * 
 * @property Shop $shop
 * @property ShopTelegramGroups $telegramGroup
 */
class ShopToTelegramGroups extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_to_telegram_groups}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shop_id', 'telegram_group_id'], 'required'],
            [['shop_id', 'telegram_group_id'], 'integer'],
            [['created_at'], 'safe'],
            
            [['shop_id', 'telegram_group_id'], 'unique', 'targetAttribute' => ['shop_id', 'telegram_group_id'], 'message' => Module::t('This Telegram group is already linked to this shop')],
            
            // foreign keys
            [['shop_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::class, 'targetAttribute' => ['shop_id' => 'id']],
            [['telegram_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShopTelegramGroups::class, 'targetAttribute' => ['telegram_group_id' => 'id']],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_id' => Module::t('Shop'),
            'telegram_group_id' => Module::t('Telegram Group'),
            'created_at' => Module::t('Created At'),
            'shopName' => Module::t('Shop Name'),
            'telegramGroupTitle' => Module::t('Telegram Group'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShop()
    {
        return $this->hasOne(Shop::class, ['id' => 'shop_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTelegramGroup()
    {
        return $this->hasOne(ShopTelegramGroups::class, ['id' => 'telegram_group_id']);
    }

    /**
     * Get shop name (virtual attribute)
     * 
     * @return string|null
     */
    public function getShopName()
    {
        return $this->shop ? $this->shop->name : null;
    }

    /**
     * Get telegram group title (virtual attribute)
     * 
     * @return string|null
     */
    public function getTelegramGroupTitle()
    {
        return $this->telegramGroup ? $this->telegramGroup->title : null;
    }

    /**
     * Get telegram group URL (virtual attribute)
     * 
     * @return string|null
     */
    public function getTelegramGroupUrl()
    {
        return $this->telegramGroup ? $this->telegramGroup->telegram_url : null;
    }

    /**
     * Check if the linked telegram group is active
     * 
     * @return bool
     */
    public function isTelegramGroupActive()
    {
        return $this->telegramGroup ? $this->telegramGroup->is_active : false;
    }

    /**
     * Create link between shop and telegram group
     * 
     * @param int $shopId
     * @param int $telegramGroupId
     * @return bool
     */
    public static function createLink($shopId, $telegramGroupId)
    {
        // Check if link already exists
        if (self::find()->where(['shop_id' => $shopId, 'telegram_group_id' => $telegramGroupId])->exists()) {
            return true; // Link already exists
        }

        $model = new self([
            'shop_id' => $shopId,
            'telegram_group_id' => $telegramGroupId,
        ]);

        return $model->save();
    }

    /**
     * Remove link between shop and telegram group
     * 
     * @param int $shopId
     * @param int $telegramGroupId
     * @return bool
     */
    public static function removeLink($shopId, $telegramGroupId)
    {
        $model = self::find()
            ->where(['shop_id' => $shopId, 'telegram_group_id' => $telegramGroupId])
            ->one();

        if ($model) {
            return $model->delete();
        }

        return false;
    }

    /**
     * Get all shop IDs linked to a telegram group
     * 
     * @param int $telegramGroupId
     * @return array
     */
    public static function getShopIdsByGroup($telegramGroupId)
    {
        return self::find()
            ->select('shop_id')
            ->where(['telegram_group_id' => $telegramGroupId])
            ->column();
    }

    /**
     * Get all telegram group IDs linked to a shop
     * 
     * @param int $shopId
     * @return array
     */
    public static function getTelegramGroupIdsByShop($shopId)
    {
        return self::find()
            ->select('telegram_group_id')
            ->where(['shop_id' => $shopId])
            ->column();
    }

    /**
     * Check if link exists
     * 
     * @param int $shopId
     * @param int $telegramGroupId
     * @return bool
     */
    public static function linkExists($shopId, $telegramGroupId)
    {
        return self::find()
            ->where(['shop_id' => $shopId, 'telegram_group_id' => $telegramGroupId])
            ->exists();
    }

    /**
     * Get all active links for a shop (where telegram group is active)
     * 
     * @param int $shopId
     * @return array
     */
    public static function getActiveLinksForShop($shopId)
    {
        return self::find()
            ->alias('stg')
            ->innerJoinWith('telegramGroup tg')
            ->where(['stg.shop_id' => $shopId, 'tg.is_active' => true])
            ->all();
    }

    /**
     * Get all active links for a telegram group
     * 
     * @param int $telegramGroupId
     * @return array
     */
    public static function getActiveLinksForGroup($telegramGroupId)
    {
        return self::find()
            ->alias('stg')
            ->innerJoinWith('telegramGroup tg')
            ->where(['stg.telegram_group_id' => $telegramGroupId, 'tg.is_active' => true])
            ->all();
    }
}