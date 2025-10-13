<?php

use yii\db\Migration;

class m240107_180035_add_fields_to_shop_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%shop}}', 'created_by', $this->integer());
        $this->addColumn('{{%shop}}', 'address', $this->string(500));
        $this->addColumn('{{%shop}}', 'telegram', $this->string(255));
        $this->addColumn('{{%shop}}', 'description_after_products', $this->text());

        $this->addForeignKey(
            'fk-shop-created_by',
            '{{%shop}}',
            'created_by',
            '{{%users}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-shop-created_by', '{{%shop}}');
        $this->dropColumn('{{%shop}}', 'created_by');
        $this->dropColumn('{{%shop}}', 'address');
        $this->dropColumn('{{%shop}}', 'telegram');
        $this->dropColumn('{{%shop}}', 'description_after_products');
    }
}