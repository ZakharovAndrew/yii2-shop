<?php

use yii\db\Migration;

/**
 * Handles the creation of table `product_category`.
 */
class m240107_171311_create_product_category_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'product_category',
            [
                'id' => $this->primaryKey(),
                'title' => $this->string()->notNull(),
                'url' => $this->string()->notNull(),
                'position' => $this->integer(),
                'parent_id' => $this->integer(),
                'description' => $this->text(),
                'description_after' => $this->text()
            ]
        );
        
        // creates index for column `url`
        $this->createIndex(
            'idx-category-url',
            'product_category',
            'url'
        );
    }

    public function down()
    {
        $this->dropTable('product_category');
    }
}
