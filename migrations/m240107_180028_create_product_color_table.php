<?php

use yii\db\Migration;
use ZakharovAndrew\shop\Module;

/**
 * Class m240107_180028_create_product_color_table
 */
class m240107_180028_create_product_color_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('product_color', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->comment('Name'),
            'code' => $this->string(50)->notNull()->unique()->comment('Code'),
            'css_color' => $this->string(7)->notNull()->comment('CSS Color'),
            'is_active' => $this->boolean()->defaultValue(true)->comment('Active'),
            'position' => $this->integer()->defaultValue(0)->comment('Position'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // add column color_id to product table
        $this->addColumn('product', 'color_id', $this->integer()->after('video')->comment('ID цвета товара'));

        //  product.color_id -> product_color.id
        $this->addForeignKey(
            'fk-product-color_id',
            'product',
            'color_id',
            'product_color',
            'id',
            'SET NULL'
        );

        $this->createIndex(
            'idx-product_color-is_active',
            'product_color',
            'is_active'
        );

        $this->createIndex(
            'idx-product_color-position',
            'product_color',
            'position'
        );

        $this->insertBasicColors();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product-color_id', 'product');
        $this->dropColumn('product', 'color_id');
        $this->dropTable('product_color');
    }

    /**
     * Insert Basic Colors
     */
    protected function insertBasicColors()
    {
        $basicColors = [
            [Module::t('White'), 'white', '#ffffff', 1],
            [Module::t('Gray'), 'gray', '#808080', 2],
            [Module::t('Black'), 'black', '#000000', 3],
            [Module::t('Blue'), 'blue', '#007aff', 4],
            [Module::t('Red'), 'red', '#ff3b30', 5],
            [Module::t('Orange'), 'orange', '#ff9500', 6],
        ];

        foreach ($basicColors as $colorData) {
            $this->insert('product_color', [
                'name' => $colorData[0],
                'code' => $colorData[1],
                'css_color' => $colorData[2],
                'is_active' => true,
                'position' => $colorData[3],
            ]);
        }
    }
}