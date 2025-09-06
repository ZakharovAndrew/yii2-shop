<?php

use yii\db\Migration;

/**
 * Class m240107_180025_insert_first_shop
 */
class m240107_180025_insert_first_shop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Добавляем первую запись в таблицу shop
        $this->insert('{{%shop}}', [
            'name' => 'First shop',
            'description' => 'This is the first automatically created store from the migration.',
            'url' => 'first-shop',
            'avatar' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Удаляем добавленную запись
        $this->delete('{{%shop}}', ['url' => 'first-shop']);
    }
}