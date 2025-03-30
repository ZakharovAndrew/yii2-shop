<?php

use yii\db\Migration;

/**
 * Class m240107_180006_add_email_to_order_table
 */
class m240107_180006_add_email_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'email', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'email');
    }
}