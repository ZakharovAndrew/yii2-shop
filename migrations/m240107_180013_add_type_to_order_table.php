<?php

use yii\db\Migration;

/**
 * Class m240107_180005_add_delivery_cost_to_order_table
 */
class m240107_180013_add_type_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'type', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'type');
    }
}