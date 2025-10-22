<?php

use yii\db\Migration;

/**
 * Add timestamps to product table
 */
class m240107_180036_add_updated_at_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {                
        $this->addColumn('product', 'updated_at', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('product', 'updated_at');
    }
}