<?php

use yii\db\Migration;

class m240107_180016_add_price_without_discount_to_order_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order_item', 'price_without_discount', $this->decimal(10, 2)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order_item', 'price_without_discount');
    }
}