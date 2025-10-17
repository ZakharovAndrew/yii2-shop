<?php

use yii\db\Migration;

/**
 * Class m240107_180035_create_product_tags_tables
 * 
 * Migration for creating both product_tag and product_tag_assignment tables
 */
class m240107_180035_create_product_tags_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create product_tag table first
        $this->createTable('{{%product_tag}}', [
            'id' => $this->primaryKey()->comment('Unique tag ID'),
            'name' => $this->string(100)->notNull()->comment('Tag name'),
            'url' => $this->string(255)->notNull()->comment('SEO-friendly URL'),
            'description' => $this->text()->comment('Tag description'),
            'background_color' => $this->string(7)->notNull()->defaultValue('#007bff')->comment('Background color in HEX'),
            'text_color' => $this->string(7)->notNull()->defaultValue('#ffffff')->comment('Text color in HEX'),
            'position' => $this->integer()->notNull()->defaultValue(0)->comment('Sorting position'),
            'allowed_roles' => $this->text()->comment('JSON array of allowed role IDs'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Tag creation time'),
        ]);

        // Create product_tag_assignment table
        $this->createTable('{{%product_tag_assignment}}', [
            'id' => $this->primaryKey()->comment('Unique assignment ID'),
            'product_id' => $this->integer()->notNull()->comment('Reference to product'),
            'tag_id' => $this->integer()->notNull()->comment('Reference to tag'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Assignment creation time'),
        ]);

        // Create indexes for product_tag table
        $this->createIndex('idx-product_tag-position', '{{%product_tag}}', 'position');
        $this->createIndex('idx-product_tag-name', '{{%product_tag}}', 'name');
        $this->createIndex('idx-product_tag-url', '{{%product_tag}}', 'url');
        $this->createIndex('idx-product_tag-url-unique', '{{%product_tag}}', 'url', true);

        // Create indexes for product_tag_assignment table
        $this->createIndex('idx-product_tag_assignment-product_id', '{{%product_tag_assignment}}', 'product_id');
        $this->createIndex('idx-product_tag_assignment-tag_id', '{{%product_tag_assignment}}', 'tag_id');
        $this->createIndex('idx-product_tag_assignment-unique', '{{%product_tag_assignment}}', ['product_id', 'tag_id'], true);

        // Add foreign key constraints for product_tag_assignment
        $this->addForeignKey(
            'fk-product_tag_assignment-product_id',
            '{{%product_tag_assignment}}',
            'product_id',
            '{{%product}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-product_tag_assignment-tag_id',
            '{{%product_tag_assignment}}',
            'tag_id',
            '{{%product_tag}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Insert sample tags
        $this->batchInsert('{{%product_tag}}', 
            ['name', 'url', 'description', 'background_color', 'text_color', 'position'],
            [
                ['New', 'new', 'New arrivals', '#28a745', '#ffffff', 1],
                ['Popular', 'popular', 'Popular products', '#ffc107', '#212529', 2],
                ['Sale', 'sale', 'Products on sale', '#dc3545', '#ffffff', 3],
                ['Bestseller', 'bestseller', 'Sales leaders', '#fd7e14', '#ffffff', 4],
                ['Recommended', 'recommended', 'Recommended products', '#17a2b8', '#ffffff', 5],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys first
        $this->dropForeignKey('fk-product_tag_assignment-tag_id', '{{%product_tag_assignment}}');
        $this->dropForeignKey('fk-product_tag_assignment-product_id', '{{%product_tag_assignment}}');
        
        // Drop tables
        $this->dropTable('{{%product_tag_assignment}}');
        $this->dropTable('{{%product_tag}}');
    }
}