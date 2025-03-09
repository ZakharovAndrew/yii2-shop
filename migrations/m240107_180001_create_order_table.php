<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order`.
 */
class m240107_180001_create_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('order', [
            'id' => $this->primaryKey()->comment('Order ID'),
            'user_id' => $this->bigInteger()->notNull()->comment('User ID'),
            'delivery_method' => $this->integer()->defaultValue(null)->comment('Delivery method'),
            'first_name' => $this->string(255)->defaultValue(null)->comment('First name'), // Имя
            'last_name' => $this->string(255)->defaultValue(null)->comment('Last name'), // Фамилия
            'middle_name' => $this->string(255)->defaultValue(null)->comment('Middle name'), // Отчество
            'phone' => $this->string(20)->defaultValue(null)->comment('Phone number'),
            'postcode' => $this->string(255)->defaultValue(null)->comment('Postal code'),
            'city' => $this->string(255)->defaultValue(null)->comment('City'),
            'address' => $this->string(255)->defaultValue(null)->comment('Address'),
            'status' => $this->integer()->notNull()->comment('Order status'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('Created at'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('Updated at'),
        ]);

        // Index for user_id field
        $this->createIndex('idx-order-user_id', 'order', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop the table
        $this->dropTable('order');
    }
}

