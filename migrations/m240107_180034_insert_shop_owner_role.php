<?php

use yii\db\Migration;
use ZakharovAndrew\shop\Module;

/**
 * Class m240107_180034_insert_shop_owner_role
 */
class m240107_180034_insert_shop_owner_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('roles', [
            'title' => Module::t('Shop Owner'),
            'code' => 'shop_owner',
            'description' => Module::t('Shop owner role with full access rights'),
            'function_to_get_all_subjects' => 'ZakharovAndrew\shop\models\Shop::getShopsList',
            'parameters' => '{"2001":"index,update,view", "2004":"create,update,get-colors-by-category"}'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('roles', ['code' => 'shop_owner']);
    }
}