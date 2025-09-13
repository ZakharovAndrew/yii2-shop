<?php

use yii\db\Migration;

/**
 * Class m240107_180027_create_product_properties
 */
class m240107_180027_create_product_properties extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Properties table
        $this->createTable('product_property', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('Property name'),
            'code' => $this->string(50)->notNull()->unique()->comment('Unique property code'),
            'type' => $this->tinyInteger(1)->notNull()->comment('Property type (1-text, 2-select, 3-year, 4-date, 5-checkbox)'),
            'sort_order' => $this->integer()->defaultValue(0)->comment('Sort order'),
            'is_required' => $this->boolean()->defaultValue(false)->comment('Is required'),
            'is_active' => $this->boolean()->defaultValue(true)->comment('Is active'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Options table for select properties
        $this->createTable('product_property_option', [
            'id' => $this->primaryKey(),
            'property_id' => $this->integer()->notNull()->comment('Property ID'),
            'value' => $this->string(255)->notNull()->comment('Option value'),
            'sort_order' => $this->integer()->defaultValue(0)->comment('Sort order'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-product_property_option-property_id',
            'product_property_option',
            'property_id',
            'product_property',
            'id',
            'CASCADE'
        );

        // Property values table
        $this->createTable('product_property_value', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull()->comment('Product ID'),
            'property_id' => $this->integer()->notNull()->comment('Property ID'),
            'value_text' => $this->text()->comment('Text value'),
            'value_int' => $this->integer()->comment('Integer value'),
            'value_date' => $this->date()->comment('Date value'),
            'value_bool' => $this->boolean()->comment('Boolean value'),
            'option_id' => $this->integer()->comment('Selected option ID'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-product_property_value-product_id',
            'product_property_value',
            'product_id',
            'product',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-product_property_value-property_id',
            'product_property_value',
            'property_id',
            'product_property',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-product_property_value-option_id',
            'product_property_value',
            'option_id',
            'product_property_option',
            'id',
            'SET NULL'
        );

        // Add index for fast search
        $this->createIndex(
            'idx-product_property_value-product_property',
            'product_property_value',
            ['product_id', 'property_id'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product_property_value-option_id', 'product_property_value');
        $this->dropForeignKey('fk-product_property_value-property_id', 'product_property_value');
        $this->dropForeignKey('fk-product_property_value-product_id', 'product_property_value');
        $this->dropForeignKey('fk-product_property_option-property_id', 'product_property_option');

        $this->dropTable('product_property_value');
        $this->dropTable('product_property_option');
        $this->dropTable('product_property');
    }
}