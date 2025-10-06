<?php

use yii\db\Migration;

/**
 * Class m240107_180033_rename_sort_order_to_position
 */
class m240107_180033_rename_sort_order_to_position extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('product_property', 'sort_order', 'position');
        
        $this->alterColumn('product_property', 'position', $this->integer()->defaultValue(0)->comment('Position for sorting'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('product_property', 'position', 'sort_order');
        
        $this->alterColumn('product_property', 'sort_order', $this->integer()->defaultValue(0)->comment('Sort order'));
    }
}