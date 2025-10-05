<?php

use yii\db\Migration;
use ZakharovAndrew\shop\Module;

/**
 * Add timestamps and status to product_category table
 */
class m240107_180032_add_timestamps_and_status_to_product_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {                
        $this->addColumn('product_category', 'status', $this->integer()->defaultValue(1)->comment('Status'));
        $this->addColumn('product_category', 'created_at', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('product_category', 'updated_at', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('product_category', 'status');
        $this->dropColumn('product_category', 'created_at');
        $this->dropColumn('product_category', 'updated_at');
    }
}