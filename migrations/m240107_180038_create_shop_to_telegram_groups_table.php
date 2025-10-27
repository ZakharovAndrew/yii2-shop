<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_to_telegram_groups}}`.
 */
class m240107_180038_create_shop_to_telegram_groups_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_to_telegram_groups}}', [
            'id' => $this->primaryKey(),
            'shop_id' => $this->integer()->notNull(),
            'telegram_group_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-shop_to_telegram_groups-shop_id',
            '{{%shop_to_telegram_groups}}',
            'shop_id',
            '{{%shop}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-shop_to_telegram_groups-telegram_group_id',
            '{{%shop_to_telegram_groups}}',
            'telegram_group_id',
            '{{%shop_telegram_groups}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'idx-shop_to_telegram_groups-unique',
            '{{%shop_to_telegram_groups}}',
            ['shop_id', 'telegram_group_id'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-shop_to_telegram_groups-shop_id', '{{%shop_to_telegram_groups}}');
        $this->dropForeignKey('fk-shop_to_telegram_groups-telegram_group_id', '{{%shop_to_telegram_groups}}');
        $this->dropTable('{{%shop_to_telegram_groups}}');
    }
}