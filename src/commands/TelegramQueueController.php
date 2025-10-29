<?php

/**
 * TelegramQueueController
 *  
 * @link https://github.com/ZakharovAndrew/yii2-shop/
 * @copyright Copyright (c) 2023-2025 Zakharov Andrew
 */

namespace ZakharovAndrew\shop\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use ZakharovAndrew\shop\models\ProductTelegramQueue;
use ZakharovAndrew\shop\models\ShopTelegramGroups;

/**
 * Telegram queue management controller
 */
class TelegramQueueController extends Controller
{
    /**
     * @var int Limit of tasks to process in one run
     */
    public $limit = 10;
    
    /**
     * @var bool Whether to retry failed tasks
     */
    public $retry = false;
    
    /**
     * @var int Sleep between posts in seconds
     */
    public $sleep = 2;
    
    /**
     * @var bool Verbose output
     */
    public $verbose = false;

    /**
     * {@inheritdoc}
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'limit', 'retry', 'sleep', 'verbose'
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function optionAliases()
    {
        return array_merge(parent::optionAliases(), [
            'l' => 'limit',
            'r' => 'retry',
            's' => 'sleep',
            'v' => 'verbose',
        ]);
    }

    /**
     * Process telegram queue - send posts to telegram groups
     * 
     * @return int
     */
    public function actionProcess()
    {
        $this->stdout("Starting telegram queue processing...\n", Console::BOLD);
        
        $processed = 0;
        $successful = 0;
        $failed = 0;
        
        // Get pending tasks
        $tasks = ProductTelegramQueue::getPendingTasks($this->limit);
        
        if (empty($tasks)) {
            $this->stdout("No pending tasks found.\n", Console::FG_YELLOW);
            return ExitCode::OK;
        }
        
        $this->stdout("Found " . count($tasks) . " tasks to process.\n", Console::FG_GREEN);
        
        foreach ($tasks as $task) {
            $processed++;
            
            $this->stdout("\nProcessing task #{$task->id}: ", Console::BOLD);
            $this->stdout("Product '{$task->product->name}' → {$task->telegramGroup->title}\n");
            
            try {
                // Mark as processing
                if (!$task->markAsProcessing()) {
                    throw new \Exception("Failed to mark task as processing");
                }
                
                // Send to telegram
                $result = $this->sendToTelegram($task);
                
                if ($result['success']) {
                    $task->markAsPosted();
                    $this->stdout("✓ Posted successfully", Console::FG_GREEN);
                    $successful++;
                } else {
                    $task->markAsFailed($result['message']);
                    $this->stdout("✗ Failed: " . $result['message'], Console::FG_RED);
                    $failed++;
                }
                
            } catch (\Exception $e) {
                $task->markAsFailed($e->getMessage());
                $this->stdout("✗ Error: " . $e->getMessage(), Console::FG_RED);
                $failed++;
            }
            
            // Sleep between posts to avoid rate limiting
            if ($processed < count($tasks)) {
                $this->stdout("\nSleeping for {$this->sleep} seconds...");
                sleep($this->sleep);
                $this->stdout(" done.\n");
            }
        }
        
        $this->stdout("\n" . str_repeat('=', 50) . "\n", Console::BOLD);
        $this->stdout("Processing completed:\n", Console::BOLD);
        $this->stdout("Total processed: {$processed}\n");
        $this->stdout("Successful: {$successful}\n", Console::FG_GREEN);
        $this->stdout("Failed: {$failed}\n", $failed > 0 ? Console::FG_RED : Console::FG_GREEN);
        
        return $failed === 0 ? ExitCode::OK : ExitCode::SOFTWARE;
    }

    /**
     * Retry failed tasks
     * 
     * @return int
     */
    public function actionRetry()
    {
        $this->stdout("Retrying failed tasks...\n", Console::BOLD);
        
        $tasks = ProductTelegramQueue::find()
            ->where(['status' => ProductTelegramQueue::STATUS_FAILED])
            ->andWhere(['<', 'attempts', 3])
            ->orderBy(['updated_at' => SORT_ASC])
            ->limit($this->limit)
            ->all();
            
        if (empty($tasks)) {
            $this->stdout("No failed tasks to retry.\n", Console::FG_YELLOW);
            return ExitCode::OK;
        }
        
        $this->stdout("Found " . count($tasks) . " failed tasks to retry.\n", Console::FG_GREEN);
        
        $retried = 0;
        $successful = 0;
        
        foreach ($tasks as $task) {
            if ($task->retry()) {
                $this->stdout("Retried task #{$task->id}\n", Console::FG_GREEN);
                $retried++;
            } else {
                $this->stdout("Failed to retry task #{$task->id}\n", Console::FG_RED);
            }
        }
        
        $this->stdout("Retried {$retried} tasks.\n", Console::BOLD);
        
        return ExitCode::OK;
    }

    /**
     * Show queue statistics
     * 
     * @return int
     */
    public function actionStats()
    {
        $this->stdout("Telegram Queue Statistics\n", Console::BOLD);
        $this->stdout(str_repeat('=', 30) . "\n");
        
        $stats = [
            'pending' => ProductTelegramQueue::find()->where(['status' => ProductTelegramQueue::STATUS_PENDING])->count(),
            'processing' => ProductTelegramQueue::find()->where(['status' => ProductTelegramQueue::STATUS_PROCESSING])->count(),
            'posted' => ProductTelegramQueue::find()->where(['status' => ProductTelegramQueue::STATUS_POSTED])->count(),
            'failed' => ProductTelegramQueue::find()->where(['status' => ProductTelegramQueue::STATUS_FAILED])->count(),
            'total' => ProductTelegramQueue::find()->count(),
        ];
        
        $this->stdout("Pending:   {$stats['pending']}\n", Console::FG_YELLOW);
        $this->stdout("Processing: {$stats['processing']}\n", Console::FG_BLUE);
        $this->stdout("Posted:    {$stats['posted']}\n", Console::FG_GREEN);
        $this->stdout("Failed:    {$stats['failed']}\n", Console::FG_RED);
        $this->stdout("Total:     {$stats['total']}\n", Console::BOLD);
        
        // Show recent failed tasks
        $recentFailed = ProductTelegramQueue::find()
            ->where(['status' => ProductTelegramQueue::STATUS_FAILED])
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit(5)
            ->all();
            
        if (!empty($recentFailed)) {
            $this->stdout("\nRecent failed tasks:\n", Console::BOLD);
            foreach ($recentFailed as $task) {
                $this->stdout("  #{$task->id}: {$task->product->name} → {$task->telegramGroup->title} - {$task->error_message}\n", Console::FG_RED);
            }
        }
        
        return ExitCode::OK;
    }

    /**
     * Clean up old processed tasks
     * 
     * @param int $days Keep tasks older than this number of days
     * @return int
     */
    public function actionCleanup($days = 30)
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $this->stdout("Cleaning up tasks older than {$days} days...\n", Console::BOLD);
        
        $deleted = ProductTelegramQueue::deleteAll([
            'and',
            ['status' => ProductTelegramQueue::STATUS_POSTED],
            ['<', 'posted_at', $date]
        ]);
        
        $this->stdout("Deleted {$deleted} old tasks.\n", Console::FG_GREEN);
        
        return ExitCode::OK;
    }

    /**
     * Test telegram posting for specific product
     * 
     * @param int $productId
     * @param int $groupId
     * @return int
     */
    public function actionTest($productId, $groupId)
    {
        $this->stdout("Testing telegram posting...\n", Console::BOLD);
        
        $product = \ZakharovAndrew\shop\models\Product::findOne($productId);
        $group = ShopTelegramGroups::findOne($groupId);
        
        if (!$product) {
            $this->stdout("Product not found.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
        
        if (!$group) {
            $this->stdout("Telegram group not found.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
        
        $this->stdout("Product: {$product->name}\n");
        $this->stdout("Group: {$group->title} ({$group->telegram_url})\n");
        
        // Create temporary queue item
        $task = new ProductTelegramQueue([
            'product_id' => $productId,
            'telegram_group_id' => $groupId,
            'status' => ProductTelegramQueue::STATUS_PENDING,
        ]);
        
        $result = $this->sendToTelegram($task);
        
        if ($result['success']) {
            $this->stdout("✓ Test successful: " . $result['message'] . "\n", Console::FG_GREEN);
            return ExitCode::OK;
        } else {
            $this->stdout("✗ Test failed: " . $result['message'] . "\n", Console::FG_RED);
            return ExitCode::SOFTWARE;
        }
    }

    /**
     * Send product to telegram group
     * 
     * @param ProductTelegramQueue $task
     * @return array
     */
    protected function sendToTelegram(ProductTelegramQueue $task)
    {
        $product = $task->product;
        $group = $task->telegramGroup;
        $shop = $product->shop;
        
        // Prepare message content
        $message = $this->prepareTelegramMessage($product, $shop);
        
        // Here you would integrate with your Telegram bot API
        // This is a placeholder implementation
        
        try {
            // Simulate API call to Telegram
            $result = $this->callTelegramApi($group, $message, $product);
            
            return [
                'success' => true,
                'message' => 'Message sent successfully',
                'message_id' => $result['message_id'] ?? null
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Prepare telegram message content
     * 
     * @param \ZakharovAndrew\shop\models\Product $product
     * @param \ZakharovAndrew\shop\models\Shop $shop
     * @return string
     */
    protected function prepareTelegramMessage($product, $shop)
    {
        $message = "";
        
        // Product name and price
        $message .= "🛍️ *{$product->name}*\n\n";
        
        // Price
        $message .= "💵 *Цена:* {$product->price} руб.\n";
        
        // Description (truncate if too long)
        if (!empty($product->description)) {
            $description = strip_tags($product->description);
            if (mb_strlen($description) > 200) {
                $description = mb_substr($description, 0, 200) . '...';
            }
            $message .= "📝 {$description}\n\n";
        }
        
        // Shop info
        if ($shop) {
            $message .= "🏪 *Магазин:* {$shop->name}\n";
        }
        
        // Product URL
        $productUrl = Yii::$app->urlManager->createAbsoluteUrl(['/shop/product/view', 'url' => $product->url]);
        $message .= "\n🔗 [Смотреть товар]({$productUrl})";
        
        return $message;
    }

    /**
     * Call Telegram API to send message
     * 
     * @param ShopTelegramGroups $group
     * @param string $message
     * @return array
     * @throws \Exception
     */
    protected function callTelegramApi(ShopTelegramGroups $group, $message, $product)
    {
        // This is a placeholder for actual Telegram Bot API integration
        // You need to implement this based on your Telegram bot setup

        $botToken = Yii::$app->getModule('user')->telegramToken ?? null;
        $uploadWebDir = Yii::$app->getModule('shop')->uploadWebDir ?? null;
        $chatId = $group->telegram_chat_id;
        
        if (!$botToken) {
            throw new \Exception('Telegram bot token not configured');
        }
        
        if (!$uploadWebDir) {
            throw new \Exception('uploadWebDir not configured');
        }
        
        if (!$chatId) {
            throw new \Exception('Telegram chat ID not set for this group');
        }
        
        
        $telegram = new \ZakharovAndrew\user\models\Telegram($botToken);
                
        if ($this->verbose) {
            $this->stdout("Sending to Telegram API message: " . $message . "\n", Console::FG_CYAN);
        }

        $images = [];
        foreach ($product->getImages() as $image) {
            $images[] = Yii::$app->urlManager->createAbsoluteUrl(['/'.$image]);
        }
        
        $result = $telegram->sendMediaGroup($chatId, $images, $message, 'markdown');
        
        if ($this->verbose) {
            $this->stdout("ChatID:{$chatId}\nResult: ".var_export($result)."\n", Console::FG_CYAN);
        }
        
        return [
            'message_id' => $result['data']['message_id'] ?? null,
            'chat' => ['id' => $chatId]
        ];
    }
}