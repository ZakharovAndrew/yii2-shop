<?php

use yii\db\Migration;

class m240107_180020_add_position_to_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('product', 'position', $this->integer()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn('product', 'position');
    }
}