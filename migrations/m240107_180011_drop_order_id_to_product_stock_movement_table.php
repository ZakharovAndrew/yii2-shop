<?php

use yii\db\Migration;

class m240107_180011_drop_order_id_to_product_stock_movement_table extends Migration
{
    public function up()
    {
        $this->dropColumn('product', 'order_id');
    }

    public function down()
    {
        $this->addColumn('product', 'order_id', $this->integer());
    }
}