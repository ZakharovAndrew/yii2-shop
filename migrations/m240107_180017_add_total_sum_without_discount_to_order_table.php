<?php

use yii\db\Migration;

/**
 * Class m240107_180017_add_total_sum_without_discount_to_order_table
 */
class m240107_180017_add_total_sum_without_discount_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'total_sum_without_discount', $this->decimal(10, 2)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'total_sum_without_discount');
    }
}