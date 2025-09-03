<?php

use yii\db\Migration;

class m240107_180024_add_whatsapp_to_shop_table extends Migration
{
    public function up()
    {
        $this->addColumn('shop', 'whatsapp', $this->string(255));
    }

    public function down()
    {
        $this->dropColumn('shop', 'whatsapp');
    }
}
