<?php

/**
 * ProductTelegramQueue
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
 * This is the model class for table "product_telegram_queue".
 *
 * @property int $id
 * @property int $product_id
 * @property int $telegram_group_id
 * @property int $priority
 * @property int $status
 * @property int $attempts
 * @property string|null $error_message
 * @property string|null $posted_at
 * @property string $created_at
 * @property string|null $updated_at
 * 
 * @property Product $product
 * @property ShopTelegramGroups $telegramGroup
 * @property Shop $shop через product->shop
 */
class ProductTelegramQueue extends ActiveRecord
{
    const STATUS_PENDING = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_POSTED = 3;
    const STATUS_FAILED = 4;

    const PRIORITY_LOW = 1;
    const PRIORITY_NORMAL = 5;
    const PRIORITY_HIGH = 8;
    const PRIORITY_VIP = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%product_telegram_queue}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'telegram_group_id'], 'required'],
            [['product_id', 'telegram_group_id', 'attempts', 'priority', 'status'], 'integer'],
            [['error_message'], 'string'],
            [['posted_at', 'created_at', 'updated_at'], 'safe'],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_PROCESSING, self::STATUS_POSTED, self::STATUS_FAILED]],
            [['priority'], 'in', 'range' => array_keys(self::getPriorityOptions())],
            [['attempts'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => self::STATUS_PENDING],
            [['priority'], 'default', 'value' => self::PRIORITY_NORMAL],
            
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
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
            'product_id' => Module::t('Product'),
            'telegram_group_id' => Module::t('Telegram Group'),
            'priority' => Module::t('Priority'),
            'status' => Module::t('Status'),
            'attempts' => Module::t('Attempts'),
            'error_message' => Module::t('Error Message'),
            'posted_at' => Module::t('Posted At'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTelegramGroup()
    {
        return $this->hasOne(ShopTelegramGroups::class, ['id' => 'telegram_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShop()
    {
        return $this->hasOne(Shop::class, ['id' => 'shop_id'])
            ->via('product');
    }

    /**
     * Get status options
     * 
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => Module::t('Pending'),
            self::STATUS_PROCESSING => Module::t('Processing'),
            self::STATUS_POSTED => Module::t('Posted'),
            self::STATUS_FAILED => Module::t('Failed'),
        ];
    }

    /**
     * Get priority options
     * 
     * @return array
     */
    public static function getPriorityOptions()
    {
        return [
            self::PRIORITY_LOW => Module::t('Low'),
            self::PRIORITY_NORMAL => Module::t('Normal'),
            self::PRIORITY_HIGH => Module::t('High'),
            self::PRIORITY_VIP => Module::t('VIP'),
        ];
    }

    /**
     * Get priority label with badge
     * 
     * @return string
     */
    public function getPriorityLabel()
    {
        $options = [
            self::PRIORITY_LOW => 'secondary',
            self::PRIORITY_NORMAL => 'info',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_VIP => 'danger',
        ];

        $label = self::getPriorityOptions()[$this->priority] ?? $this->priority;
        $color = $options[$this->priority] ?? 'secondary';

        return '<span class="badge badge-' . $color . '">' . $label . '</span>';
    }

    /**
     * Get status label with badge
     * 
     * @return string
     */
    public function getStatusLabel()
    {
        $options = [
            self::STATUS_PENDING => 'secondary',
            self::STATUS_PROCESSING => 'warning',
            self::STATUS_POSTED => 'success',
            self::STATUS_FAILED => 'danger',
        ];

        $label = self::getStatusOptions()[$this->status] ?? $this->status;
        $color = $options[$this->status] ?? 'secondary';

        return '<span class="badge badge-' . $color . '">' . $label . '</span>';
    }

    /**
     * Add product to telegram queue for all linked shops
     * 
     * @param int $productId
     * @return bool
     */
    public static function addToQueue($productId)
    {
        $product = Product::findOne($productId);
        if (!$product) {
            return false;
        }

        $shop = $product->shop;
        if (!$shop) {
            return false;
        }

        // Определяем приоритет на основе магазина
        $priority = self::getShopPriority($shop);

        $telegramGroups = $shop->getActiveTelegramGroups()->all();

        $successCount = 0;
        foreach ($telegramGroups as $group) {
            // Проверяем, нет ли уже такой задачи в очереди
            $existingTask = self::find()
                ->where([
                    'product_id' => $productId,
                    'telegram_group_id' => $group->id,
                ])
                ->exists();

            if (!$existingTask) {
                $queueItem = new self([
                    'product_id' => $productId,
                    'telegram_group_id' => $group->id,
                    'status' => self::STATUS_PENDING,
                    'priority' => $priority,
                ]);

                if ($queueItem->save()) {
                    $successCount++;
                }
            }
        }

        return $successCount > 0;
    }

    /**
     * Determine shop priority
     * 
     * @param Shop $shop
     * @return int
     */
    protected static function getShopPriority(Shop $shop)
    {
        // Базовая логика - можно доработать
        // Например, проверять наличие VIP статуса у магазина
        return self::PRIORITY_NORMAL;
    }

    /**
     * Get pending tasks for processing
     * 
     * @param int $limit
     * @return array
     */
    public static function getPendingTasks($limit = 10)
    {
        return self::find()
            ->with(['product', 'telegramGroup', 'product.shop'])
            ->where(['status' => self::STATUS_PENDING])
            ->orderBy([
                'priority' => SORT_DESC, // Сначала высокий приоритет
                'created_at' => SORT_ASC // Затем по времени создания
            ])
            ->limit($limit)
            ->all();
    }

    /**
     * Mark task as processing
     * 
     * @return bool
     */
    public function markAsProcessing()
    {
        $this->status = self::STATUS_PROCESSING;
        $this->attempts += 1;
        return $this->save();
    }

    /**
     * Mark task as posted
     * 
     * @return bool
     */
    public function markAsPosted()
    {
        $this->status = self::STATUS_POSTED;
        $this->posted_at = new Expression('NOW()');
        return $this->save();
    }

    /**
     * Mark task as failed
     * 
     * @param string $errorMessage
     * @return bool
     */
    public function markAsFailed($errorMessage = '')
    {
        $this->status = self::STATUS_FAILED;
        $this->error_message = $errorMessage;
        return $this->save();
    }

    /**
     * Check if task can be retried
     * 
     * @return bool
     */
    public function canRetry()
    {
        return $this->status === self::STATUS_FAILED && $this->attempts < 3;
    }

    /**
     * Retry failed task
     * 
     * @return bool
     */
    public function retry()
    {
        if ($this->canRetry()) {
            $this->status = self::STATUS_PENDING;
            
            if ($this->attempts > 1) {
                $this->priority = max(self::PRIORITY_LOW, $this->priority - 1);
            }
            return $this->save();
        }
        return false;
    }
}