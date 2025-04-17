<?php

use yii\db\Migration;

class m240107_180014_change_foreign_key_order_item extends Migration
{
    public function safeUp()
    {
        $this->dropForeignKey('fk-order_item-product_id', 'order_item');

        $this->addForeignKey(
            'fk-order_item-product_id',
            'order_item',
            'product_id',
            'product',
            'id',
            'CASCADE',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-order_item-product_id', 'order_item');

        $this->addForeignKey(
            'fk-order_item-product_id',
            'order_item',
            'product_id',
            'product',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }
}
