<?php

use yii\db\Migration;

/**
 * Class m240107_180004_add_total_sum_to_order_table
 */
class m240107_180004_add_total_sum_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'total_sum', $this->decimal(10, 2)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'total_sum');
    }
}