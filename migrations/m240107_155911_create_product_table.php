<?php

use yii\db\Migration;

/**
 * Handles the creation of table `product`.
 */
class m240107_155911_create_product_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'product',
            [
                'id' => $this->primaryKey(),
                'title' => $this->string()->notNull(),
                'description' => $this->text(),
                'url' => $this->string()->notNull(),
                'images' => $this->string()->notNull(),
                'category_id' => $this->integer()->defaultValue(1),
                'user_id' => $this->integer(),
                'count_views' => $this->integer(),
                'created_at' => $this->dateTime()->defaultValue( new \yii\db\Expression('NOW()') ),
            ]
        );
        
        // creates index for column `url`
        $this->createIndex(
            'idx-product-url',
            'product',
            'url'
        );
    }

    public function down()
    {
        $this->dropTable('product');
    }
}
