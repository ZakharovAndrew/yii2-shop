<?php

use yii\db\Migration;

class m240107_180022_add_shop_id_to_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('product', 'shop_id', $this->integer()->defaultValue(null));
    }

    public function down()
    {
        $this->dropColumn('product', 'shop_id');
    }
}
