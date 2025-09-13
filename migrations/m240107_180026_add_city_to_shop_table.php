<?php

use yii\db\Migration;

class m240107_180026_add_city_to_shop_table extends Migration
{
    public function up()
    {
        $this->addColumn('shop', 'city', $this->string(255));
    }

    public function down()
    {
        $this->dropColumn('shop', 'city');
    }
}
