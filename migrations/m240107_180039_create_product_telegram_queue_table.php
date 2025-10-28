<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_telegram_queue}}`.
 */
class m240107_180039_create_product_telegram_queue_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_telegram_queue}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'telegram_group_id' => $this->integer()->notNull(),
            'priority' => $this->integer()->notNull()->defaultValue(1), // 1-10, где 10 - высший приоритет
            'status' => $this->smallInteger()->notNull()->defaultValue(1), // 1=pending, 2=processing, 3=posted, 4=failed
            'attempts' => $this->integer()->defaultValue(0),
            'error_message' => $this->text()->null(), 
            'posted_at' => $this->timestamp()->null(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null(),
        ]);

        $this->createIndex(
            'idx-product_telegram_queue-product_id',
            '{{%product_telegram_queue}}',
            'product_id'
        );

        $this->createIndex(
            'idx-product_telegram_queue-telegram_group_id',
            '{{%product_telegram_queue}}',
            'telegram_group_id'
        );

        $this->createIndex(
            'idx-product_telegram_queue-status',
            '{{%product_telegram_queue}}',
            'status'
        );

        $this->createIndex(
            'idx-product_telegram_queue-priority',
            '{{%product_telegram_queue}}',
            'priority'
        );

        // Составной индекс для поиска задач для обработки (сортировка по приоритету)
        $this->createIndex(
            'idx-product_telegram_queue-pending-priority',
            '{{%product_telegram_queue}}',
            ['status', 'priority', 'created_at']
        );

        $this->addForeignKey(
            'fk-product_telegram_queue-product_id',
            '{{%product_telegram_queue}}',
            'product_id',
            '{{%product}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-product_telegram_queue-telegram_group_id',
            '{{%product_telegram_queue}}',
            'telegram_group_id',
            '{{%shop_telegram_groups}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Уникальный индекс чтобы избежать дублирования задач
        $this->createIndex(
            'idx-product_telegram_queue-unique',
            '{{%product_telegram_queue}}',
            ['product_id', 'telegram_group_id', 'status'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product_telegram_queue-product_id', '{{%product_telegram_queue}}');
        $this->dropForeignKey('fk-product_telegram_queue-telegram_group_id', '{{%product_telegram_queue}}');
        
        $this->dropTable('{{%product_telegram_queue}}');
    }
}