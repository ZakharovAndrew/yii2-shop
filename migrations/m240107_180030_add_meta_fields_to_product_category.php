<?php

use yii\db\Migration;

/**
 * Class m240107_180030_add_meta_fields_to_product_category
 */
class m240107_180030_add_meta_fields_to_product_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('product_category', 'meta_title', $this->string(255)->after('description_after'));
        $this->addColumn('product_category', 'meta_description', $this->text()->after('meta_title'));
        $this->addColumn('product_category', 'meta_keywords', $this->text()->after('meta_description'));
        $this->addColumn('product_category', 'og_title', $this->string(255)->after('meta_keywords'));
        $this->addColumn('product_category', 'og_description', $this->text()->after('og_title'));
        $this->addColumn('product_category', 'og_image', $this->string(500)->after('og_description'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('product_category', 'meta_title');
        $this->dropColumn('product_category', 'meta_description');
        $this->dropColumn('product_category', 'meta_keywords');
        $this->dropColumn('product_category', 'og_title');
        $this->dropColumn('product_category', 'og_description');
        $this->dropColumn('product_category', 'og_image');
    }
}