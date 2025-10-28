<?php

/**
 * ShopTelegramGroups
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
 * This is the model class for table "shop_telegram_groups".
 *
 * @property int $id
 * @property string $title
 * @property string $telegram_url
 * @property string|null $telegram_chat_id
 * @property string|null $permissions
 * @property bool $is_active
 * @property string $created_at
 * @property string|null $updated_at
 * 
 * @property ShopToTelegramGroups[] $shopLinks
 * @property Shop[] $shops
 */
class ShopTelegramGroups extends ActiveRecord
{
    private $_permissionsArray = [];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_telegram_groups}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['telegram_url'], 'required'],
            [['permissions'], 'string'],
            [['is_active'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'telegram_url'], 'string', 'max' => 500],
            [['telegram_chat_id'], 'string', 'max' => 255],
            [['telegram_url'], 'url', 'validSchemes' => ['https', 'http']],
            [['telegram_url'], 'match', 'pattern' => '/t\.me\/|web\.telegram\.org\/a\/|telegram\.me\//', 'message' => Module::t('URL must be a valid Telegram group link')],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => Module::t('Group Title'),
            'telegram_url' => Module::t('Telegram Group URL'),
            'telegram_chat_id' => Module::t('Telegram Chat ID'),
            'permissions' => Module::t('Permissions'),
            'is_active' => Module::t('Active'),
            'created_at' => Module::t('Created At'),
            'updated_at' => Module::t('Updated At'),
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
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        
        if (!empty($this->permissions)) {
            $arr = json_decode($this->permissions, true);
            $this->_permissionsArray =  $arr;
            //var_dump($arr, $this->permissions);die();
        } else {
            $this->_permissionsArray = [];
        }
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // encode permissions
            if (!empty($this->_permissionsArray)) {
                $this->permissions = json_encode($this->_permissionsArray, JSON_UNESCAPED_UNICODE);
            } else {
                $this->permissions = null;
            }
            
            return true;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopLinks()
    {
        return $this->hasMany(ShopToTelegramGroups::class, ['telegram_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShops()
    {
        return $this->hasMany(Shop::class, ['id' => 'shop_id'])
            ->via('shopLinks');
    }

    /**
     * Extract username from Telegram URL
     * 
     * @return string|null
     */
    public function getTelegramUsername()
    {
        if (preg_match('/t\.me\/([a-zA-Z0-9_]+)/', $this->telegram_url, $matches)) {
            return $matches[1];
        } elseif (preg_match('/telegram\.me\/([a-zA-Z0-9_]+)/', $this->telegram_url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Get permissions as array
     * 
     * @return array
     */
    public function getPermissionsArray()
    {
        return $this->_permissionsArray;
    }

    /**
     * Check if group has specific permission
     * 
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        return in_array($permission, $this->_permissionsArray);
    }

    /**
     * Get active groups for autoposting
     * 
     * @return array
     */
    public static function getActiveGroups()
    {
        return self::find()
            ->where(['is_active' => true])
            ->all();
    }
    
    public function getParams()
    {
        $token = Yii::$app->getModule('user')->telegramToken;
        $telegram = new \ZakharovAndrew\user\models\Telegram($token);
        
        $result = $telegram->getChatIdByLink($this->telegram_url);
        if ($result['success']) {
            
            $data = $result['data'];
            //var_dump( $data['permissions']);
            
            $this->title = $data['title'];
            $this->_permissionsArray = $data['permissions'];
            $this->telegram_chat_id = (string)$data['id'];
        }
    }
    
    public function sendPost($message)
    {
        $token = Yii::$app->getModule('user')->telegramToken;
        $telegram = new \ZakharovAndrew\user\models\Telegram($token);
        
        $telegram->sendMessage($this->telegram_chat_id, $message);
    }
}