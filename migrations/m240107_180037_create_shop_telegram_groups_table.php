<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_telegram_groups}}`.
 */
class m240107_180037_create_shop_telegram_groups_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_telegram_groups}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(500)->notNull(),
            'telegram_url' => $this->string(500)->notNull(),
            'telegram_chat_id' => $this->string(255)->null(), // для хранения ID чата после подключения
            'permissions' => $this->string(),
            'is_active' => $this->boolean()->defaultValue(true),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_telegram_groups}}');
    }
}