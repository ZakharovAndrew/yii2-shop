<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_favorite}}`.
 */
class m240107_180036_create_product_favorite_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_favorite}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Add foreign keys
        $this->addForeignKey(
            'fk-product_favorite-user_id',
            '{{%product_favorite}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-product_favorite-product_id',
            '{{%product_favorite}}',
            'product_id',
            '{{%product}}',
            'id',
            'CASCADE'
        );

        // Add unique index to prevent duplicates
        $this->createIndex(
            'idx-product_favorite-user_product',
            '{{%product_favorite}}',
            ['user_id', 'product_id'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product_favorite-user_id', '{{%product_favorite}}');
        $this->dropForeignKey('fk-product_favorite-product_id', '{{%product_favorite}}');
        $this->dropTable('{{%product_favorite}}');
    }
}