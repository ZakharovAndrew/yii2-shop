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
 * @property int|null $price
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
            [['category_id', 'user_id', 'count_views', 'price'], 'integer'],
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
            'price' => Module::t('Price'),
            'created_at' => 'Created At',
        ];
    }
    
    /**
     * Get a list of images
     * @param string $size
     * @return array
     */
    public function getImages($size = 'medium')
    {
        $module = Yii::$app->getModule('shop');
        
        if ($this->images == '') {
            return [$module->defaultProductImage];
        }
        
        $result = [];
        $arr = explode(',', $this->images);
        
        foreach ($arr as $item) {
            if (!empty($item)) {
                $result[] = $module->uploadWebDir . $item.'_img_'.$size.'.jpg';
            }
        }
        
        return $result;
    }


    /**
     * Get the first image of a given size
     * @param string $size
     * @return type
     */
    public function getFirstImage($size = 'medium')
    {
        $module = Yii::$app->getModule('shop');
        
        if ($this->images == '') {
            return $module->defaultProductImage;
        }
        
        $images = explode(',', $this->images);
        return $module->uploadWebDir . $images[0].'_img_'.$size.'.jpg';
    }
    
    public function getMoreProducts($count = 6)
    {
        return self::find()
                ->where(['category_id' => $this->category_id])
                ->andWhere(['!=', 'id', $this->id])
                ->limit($count)
                ->all();
    }
    
    public static function getPriceByID($id)
    {
        $product = static::findOne($id);
        return $product ? $product->price : null;
  
    }
}
