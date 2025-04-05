<?php

use yii\db\Migration;

class m240107_180007_add_quantity_to_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('product', 'quantity', $this->integer()->notNull()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn('product', 'quantity');
    }
}