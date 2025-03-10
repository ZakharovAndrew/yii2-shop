<?php

use yii\db\Migration;

class m240107_180002_add_columns_to_product_table extends Migration
{
    public function up()
    {

        $this->addColumn('product', 'price', $this->integer());

        $this->addColumn('product', 'param1', $this->string(255));
        $this->addColumn('product', 'param2', $this->string(255));
        $this->addColumn('product', 'param3', $this->string(255));
    }

    public function down()
    {
        $this->dropColumn('product', 'price');
        $this->dropColumn('product', 'param1');
        $this->dropColumn('product', 'param2');
        $this->dropColumn('product', 'param3');
    }
}