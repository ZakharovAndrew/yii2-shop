<?php

namespace ZakharovAndrew\shop\models;

use Yii;
use ZakharovAndrew\shop\Module;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $url
 * @property string $images
 * @property int|null $category_id
 * @property int|null $user_id
 * @property int|null $count_views
 * @property int|null $cost
 * @property string|null $created_at
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'url', 'images'], 'required'],
            [['description'], 'string'],
            [['category_id', 'user_id', 'count_views', 'cost'], 'integer'],
            [['created_at'], 'safe'],
            [['title', 'url', 'images', 'param1', 'param2', 'param3'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => Module::t('Title'),
            'description' => Module::t('Description'),
            'url' => Module::t('Url'),
            'images' => Module::t('Images'),
            'category_id' => Module::t('Category'),
            'user_id' => 'User ID',
            'count_views' => Module::t('Count Views'),
            'cost' => Module::t('Cost'),
            'created_at' => 'Created At',
        ];
    }
    
    /**
     * Получить первую картинку заданного размера
     * @param string $size
     * @return type
     */
    public function getFirstImage($size = 'medium')
    {
        $module = Yii::$app->getModule('shop');
        
        if ($this->images == '') {
            return '/img/no-photo.jpg';
        }
        
        $images = explode(',', $this->images);
        return $module->uploadWebDir . $images[0].'_img_'.$size.'.jpg';
    }
}
