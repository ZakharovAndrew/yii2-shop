<?php

use yii\db\Migration;

/**
 * Class m240107_180003_create_order_item_table
 */
class m240107_180003_create_order_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('order_item', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Создаем индекс для поля order_id
        $this->createIndex(
            'idx-order_item-order_id',
            '{{%order_item}}',
            'order_id'
        );

        // Создаем индекс для поля product_id
        $this->createIndex(
            'idx-order_item-product_id',
            '{{%order_item}}',
            'product_id'
        );

        // Добавляем внешние ключи
        $this->addForeignKey(
            'fk-order_item-order_id',
            '{{%order_item}}',
            'order_id',
            '{{%order}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-order_item-product_id',
            '{{%order_item}}',
            'product_id',
            '{{%product}}',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Удаляем внешние ключи сначала
        $this->dropForeignKey(
            'fk-order_item-order_id',
            '{{%order_item}}'
        );

        $this->dropForeignKey(
            'fk-order_item-product_id',
            '{{%order_item}}'
        );

        // Удаляем таблицу
        $this->dropTable('{{%order_item}}');
    }
}