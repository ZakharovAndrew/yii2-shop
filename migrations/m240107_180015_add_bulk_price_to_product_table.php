<?php

use yii\db\Migration;

class m240107_180015_add_bulk_price_to_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('product', 'bulk_price_quantity_1', $this->integer()->null());
        $this->addColumn('product', 'bulk_price_1', $this->integer()->null());
        $this->addColumn('product', 'bulk_price_quantity_2', $this->integer()->null());
        $this->addColumn('product', 'bulk_price_2', $this->integer()->null());
        $this->addColumn('product', 'bulk_price_quantity_3', $this->integer()->null());
        $this->addColumn('product', 'bulk_price_3', $this->integer()->null());
    }

    public function down()
    {
        $this->dropColumn('product', 'bulk_price_quantity_1');
        $this->dropColumn('product', 'bulk_price_1');
        $this->dropColumn('product', 'bulk_price_quantity_2');
        $this->dropColumn('product', 'bulk_price_2');
        $this->dropColumn('product', 'bulk_price_quantity_3');
        $this->dropColumn('product', 'bulk_price_3');
    }
}