<?php

use yii\db\Migration;

class m240107_180012_add_status_to_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('product', 'status', $this->integer()->notNull()->defaultValue(1));
    }

    public function down()
    {
        $this->dropColumn('product', 'status');
    }
}