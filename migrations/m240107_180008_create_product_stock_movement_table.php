<?php

use yii\db\Migration;

class m240107_180008_create_product_stock_movement_table extends Migration
{
    public function up()
    {
        $this->createTable('product_stock_movement', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'comment' => $this->string(),
        ]);

        $this->addForeignKey(
            'fk-product_stock_movement-product_id',
            'product_stock_movement',
            'product_id',
            'product',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-product_stock_movement-user_id',
            'product_stock_movement',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('product_stock_movement');
    }
}