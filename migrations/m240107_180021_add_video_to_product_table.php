<?php

use yii\db\Migration;

class m240107_180021_add_video_to_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('product', 'video', $this->string(255));
    }

    public function down()
    {
        $this->dropColumn('product', 'video');
    }
}
