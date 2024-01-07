<?php

namespace ZakharovAndrew\shop\models;

use Yii;

/**
 * This is the model class for table "product_category".
 *
 * @property int $id
 * @property string $title
 * @property string $url
 * @property int|null $position
 * @property int|null $parent_id
 * @property string|null $description
 * @property string|null $description_after
 */
class ProductCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'url'], 'required'],
            [['position', 'parent_id'], 'integer'],
            [['description', 'description_after'], 'string'],
            [['title', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'url' => 'Url',
            'position' => 'Position',
            'parent_id' => 'Parent ID',
            'description' => 'Description',
            'description_after' => 'Description After',
        ];
    }
    
    public static function getDropdownGroups()
    {
        return Yii::$app->cache->getOrSet('list_categories_dropdown', function () {
            $model = self::find()->all();
            $result = [];
            $cat_title = [];
            foreach ($model as $category) {
                $cat_title[$category->id] = $category->title;
                if (empty($category->parent_id)) {
                    $result[$category->title] = [$category->id => $category->title];
                }
            }
            foreach ($model as $category) {
                if (empty($category->parent_id)) {
                    //$result[$category->title] = [$category->id => $category->title];
                } else {
                    $result[$cat_title[$category->parent_id]][$category->id] = $category->title;
                }
            }

            return $result;
        }, 3600);
    }
    
    public static function getCategoryGroups()
    {
        return Yii::$app->cache->getOrSet('list_category_group', function () {
            $model = self::find()->all();
            $result = [];
            foreach ($model as $category) {
                if (empty($category->parent_id)) {
                    $result[$category->id]['parent'] = $category;
                } else {
                    $result[$category->parent_id]['child'][] = $category;
                }
            }
            
            return $result;
        }, 3600);
    }
}
