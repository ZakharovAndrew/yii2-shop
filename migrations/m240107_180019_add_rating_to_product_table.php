<?php

use yii\db\Migration;

class m240107_180019_add_rating_to_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('product', 'rating', $this->decimal(3,2)->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn('product', 'rating');
    }
}