<?php

use yii\db\Migration;

class m240107_180018_add_columns_to_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('product', 'composition', $this->text());
        $this->addColumn('product', 'weight', $this->decimal(10, 2)->Null());
    }

    public function down()
    {
        $this->dropColumn('product', 'composition');
        $this->dropColumn('product', 'weight');
    }
}