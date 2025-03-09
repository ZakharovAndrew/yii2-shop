<?php
use yii\db\Migration;

/**
 * Handles the creation of table `cart`.
 */
class m240107_180000_create_cart_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('cart', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('User ID'),
            'product_id' => $this->integer()->notNull()->comment('Product ID'),
            'quantity' => $this->integer()->defaultValue(1)->comment('Quantity'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' =>  $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-cart-user_id', '{{%cart}}', 'user_id');
        $this->createIndex('idx-cart-product_id', '{{%cart}}', 'product_id');

        $this->addForeignKey('fk-cart-product_id', '{{%cart}}', 'product_id', 'product', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('cart');
    }
}