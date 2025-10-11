<?php

use yii\db\Migration;
use ZakharovAndrew\shop\Module;

/**
 * Migration for creating shop settings table
 */
class m240107_180031_create_shop_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_settings}}', [
            'id' => $this->primaryKey()->comment('Unique setting ID'),
            'key' => $this->string(100)->notNull()->unique()->comment('Setting key name'),
            'name' => $this->string(255)->comment('Human readable setting name'),
            'value' => $this->text()->comment('Setting value'),
            'type' => $this->string(20)->notNull()->defaultValue('string')->comment('Value type: string, integer, boolean, json'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('Record creation timestamp'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('Record update timestamp'),
        ]);

        // Create index for faster key searches
        $this->createIndex('idx-shop_settings-key', '{{%shop_settings}}', 'key');
        
        // Insert default settings
        $this->batchInsert('{{%shop_settings}}', 
            ['key', 'name', 'value', 'type'], 
            [
                ['productPerPage', Module::t('Products Per Page'), '100', 'integer'],
                ['catalogTitle', Module::t('Catalog Title'), Module::t('Catalog Title'), 'string'],
                ['storeName', Module::t('Store Name'), 'My Store', 'string'],
                ['showWholesalePrices', Module::t('Show Wholesale Prices'), '0', 'boolean'],
                ['mobileProductsPerRow', Module::t('Mobile Products Per Row'), '1', 'integer'],
                ['multiStore', Module::t('Multi-Store Support'), '0', 'boolean'],
                ['defaultProductImage', 'Default Product Image', '/img/no-photo.jpg', 'string'],
                ['catalogPageID', 'Catalog Page ID', '', 'string'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-shop_settings-key', '{{%shop_settings}}');
        $this->dropTable('{{%shop_settings}}');
    }
}