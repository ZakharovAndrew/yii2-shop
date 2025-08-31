<?php

use yii\db\Migration;

/**
 * Class m240107_180023_create_shop_table
 */
class m240107_180023_create_shop_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop}}', [
            'id' => $this->primaryKey()->comment('Shop ID'),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'url' => $this->string(255)->notNull()->unique(),
            'avatar' => $this->string(255),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('Created at'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('Updated at'),
        ]);

        // Добавляем индекс для поля url для быстрого поиска
        $this->createIndex('idx-shop-url', '{{%shop}}', 'url', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop}}');
    }
}
