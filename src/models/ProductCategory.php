<?php

namespace ZakharovAndrew\shop\models;

use Yii;
use ZakharovAndrew\shop\Module;

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
 * @property array $availableColorIds
 */
class ProductCategory extends \yii\db\ActiveRecord
{
    public $availableColorIds = []; // for selected colors

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
            [['availableColorIds'], 'safe'],
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
            'url' => Module::t('Url'),
            'position' => Module::t('Position'),
            'parent_id' => Module::t('Parent Category'),
            'description' => Module::t('Description'),
            'description_after' => Module::t('Description after goods'),
            'availableColorIds' => Module::t('Available Colors'),
        ];
    }
    
    public static function getDropdownGroups($exclude = null)
    {
        return Yii::$app->cache->getOrSet('list_categories_dropdown'.($exclude ?? ''), function () use ($exclude) {
            $model = (isset($exclude)) ? self::find()->where(['NOT IN', 'id', $exclude])->all() : self::find()->all();
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
        return Yii::$app->cache->getOrSet('list_category_group2', function () {           
            $model = ProductCategory::find()->select(['id','title','parent_id'])->all();
            $items = [];
        
            foreach ($model as $category) {
                $items[$category->id]['data'] = $category;
            }

            foreach ($items as $row) {
                $data = $row['data'];

                $parentKey = !isset($data->parent_id) || empty($data->parent_id) ?
                    0 : $data->parent_id;

                $items[$parentKey]['items'][$data->id] = &$items[$data->id];
            }

            return $this->getMenu($items[0]['items']);            
        }, 3600);
    }
    
    public static function getMenuItems($items)
    {
        $result = [];
        foreach ($items as $row) {
            if (isset($row['items'])) {
                $result[$row['data']->title] = static::getMenuItems($row['items']);
            } else {
                $result[$row['data']->title] = $row['data']->id;
            }
        }
        
        return $result;
    }
    
    /**
     * Get a list of categories 
     */
    public static function getCategories($category_id)
    {
        return Yii::$app->cache->getOrSet('list_product_categories_'.$category_id, function () use ($category_id) {
            $result = [];
            while ($category_id !== NULL) {
                $category = ProductCategory::findOne(['id' => $category_id]);
                
                if ($category) {
                    $result[] = $category;
                }
                $category_id = $category->parent_id ?? null;
            }

            return array_reverse($result);
        }, 60);
    }
    
    public static function getCategoriesList()
    {
        return Yii::$app->cache->getOrSet('list_product_categories', function () {
            $category = ProductCategory::find()->asArray()->all();
            return \yii\helpers\ArrayHelper::index($category, 'id');
        }, 600);
    }

    /**
     * Gets query for [[AvailableColors]].
     */
    public function getAvailableColors()
    {
        return $this->hasMany(ProductColor::class, ['id' => 'color_id'])
            ->viaTable('category_colors', ['category_id' => 'id']);
    }
    
    /**
     * Get available colors relation
     */
    public function getCategoryColors()
    {
        return $this->hasMany(CategoryColors::class, ['category_id' => 'id']);
    }

    /**
     * Get list of available color IDs
     */
    public function getAvailableColorIds()
    {
        return $this->getAvailableColors()->select('id')->column();
    }

    /**
     * Get available colors list for dropdown
     */
    public static function getAvailableColorsList($categoryId = null)
    {
        if ($categoryId) {
            $category = self::findOne($categoryId);
            if ($category) {
                return $category->getAvailableColors()
                    ->select(['name', 'id'])
                    ->indexBy('id')
                    ->column();
            }
        }
        
        return ProductColor::getActiveColorsList();
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        // Обновляем связанные цвета
        if (!$insert) {
            $this->updateAvailableColors();
        }
        
        Yii::$app->cache->delete('list_category_group2');
        Yii::$app->cache->delete('list_product_categories');
        Yii::$app->cache->delete('list_categories_dropdown');
        Yii::$app->cache->delete('category_colors_' . $this->id);
        
        return parent::afterSave($insert, $changedAttributes);
        
    }

    /**
     * After find - load available color IDs
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->availableColorIds = $this->getAvailableColorIds();
    }

    /**
     * Update available colors for category
     */
    public function updateAvailableColors()
    {
        // Удаляем старые связи
        CategoryColors::deleteAll(['category_id' => $this->id]);
        
        // Добавляем новые связи
        if (!empty($this->availableColorIds)) {
            foreach ($this->availableColorIds as $colorId) {
                $categoryColor = new CategoryColors([
                    'category_id' => $this->id,
                    'color_id' => $colorId,
                ]);
                $categoryColor->save();
            }
        }
    }

    /**
     * Check if color is available for this category
     */
    public function isColorAvailable($colorId)
    {
        return in_array($colorId, $this->getAvailableColorIds());
    }

    /**
     * Get available colors with cache
     */
    public function getCachedAvailableColors()
    {
        return Yii::$app->cache->getOrSet('category_colors_' . $this->id, function () {
            return $this->getAvailableColors()
                ->where(['is_active' => true])
                ->orderBy(['position' => SORT_ASC])
                ->all();
        }, 3600);
    }
}
