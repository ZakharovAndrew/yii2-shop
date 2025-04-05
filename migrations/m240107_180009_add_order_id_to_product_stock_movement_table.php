<?php

use yii\db\Migration;

class m240107_180009_add_order_id_to_product_stock_movement_table extends Migration
{
    public function up()
    {
        $this->addColumn('product', 'order_id', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('product', 'order_id');
    }
}