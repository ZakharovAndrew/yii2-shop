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
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $og_title
 * @property string|null $og_description
 * @property string|null $og_image
 * @property string $created_at
 * @property string $updated_at
 * @property array $availableColorIds
 */
class ProductCategory extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    
    const SORTING = [
        'default' => 'position DESC',
        'name_asc' => 'name ASC',
        'name_desc' => 'name DESC',
        'price_desc' => 'price DESC',
        'price_asc' => 'price ASC'
    ];
    
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
            [['position', 'parent_id', 'status'], 'integer'],
            [['description', 'description_after', 'meta_description', 'meta_keywords', 'og_description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'url', 'meta_title', 'og_title'], 'string', 'max' => 255],
            [['og_image'], 'string', 'max' => 500],
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
            'meta_title' => Module::t('Meta Title'),
            'meta_description' => Module::t('Meta Description'),
            'meta_keywords' => Module::t('Meta Keywords'),
            'og_title' => Module::t('OG Title'),
            'og_description' => Module::t('OG Description'),
            'og_image' => Module::t('OG Image'),
            'created_at' => Module::t('Created At'),
            'updated_at' => Module::t('Updated At'),
            'availableColorIds' => Module::t('Available Colors'),
        ];
    }
    
    public static function sortingLabels()
    {
        return [
            'default' => 'По дате добавления',
            'name_asc' => 'По названию (А-Я)',
            'name_desc' => 'По названию (Я-А)',
            'price_desc' => 'Сначала дорогие',
            'price_asc' => 'Сначала дешевые'
        ];
    }
    
    /**
     * Returns category status options
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_INACTIVE => Module::t('Inactive'),
            self::STATUS_ACTIVE => Module::t('Active'),
        ];
    }
    
    /**
     * Gets status text representation
     * @return string
     */
    public function getStatusText()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status] ?? Module::t('Unknown status');
    }
    
    /**
     * Checks if category is active
     * @return bool
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
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
                } else if (isset($cat_title[$category->parent_id])) {
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
    
    public function getSubCategories()
    {
        return Yii::$app->cache->getOrSet('list_subcategories_'.$this->id, function () {
            return ProductCategory::find()
                    ->where(['parent_id' => $this->id])
                    ->orderBy('position')
                    ->all();
        }, 600);
    }

    /**
     * Gets query for [[AvailableColors]].
     */
    public function getAvailableColors()
    {
        $categoryIds = $this->getAllChildCategoryIds();
        if (count($categoryIds) == 1) {
            return $this->hasMany(ProductColor::class, ['id' => 'color_id'])
            //->viaTable('category_colors', ['category_id' => $categoryIds])
            ->viaTable('category_colors', ['category_id' => 'id']);
        }
    
        return ProductColor::find()
            ->distinct()
            ->innerJoin('category_colors', 'product_color.id = category_colors.color_id')
            ->innerJoin('product_category', 'category_colors.category_id = product_category.id')
            ->where(['in', 'product_category.id', $categoryIds])
            ->orderBy(['product_color.position' => SORT_ASC]);        
    }
    
    /**
     * Get all child category IDs including current category
     */
    public function getAllChildCategoryIds()
    {
        return Yii::$app->cache->getOrSet('category_child_ids_' . $this->id, function () {
            $categoryIds = [$this->id];
            $this->getChildCategoryIdsRecursive($this->id, $categoryIds);
            return $categoryIds;
        }, 3600);
    }
    
    /**
     * Recursive function to get all child category IDs
     */
    private function getChildCategoryIdsRecursive($parentId, &$categoryIds)
    {
        $childCategories = self::find()
            ->select('id')
            ->where(['parent_id' => $parentId])
            ->column();

        foreach ($childCategories as $childId) {
            $categoryIds[] = $childId;
            $this->getChildCategoryIdsRecursive($childId, $categoryIds);
        }
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
        return $this->getAvailableColors()->select('product_color.id')->column();
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
        // Clear child categories cache if structure changed
        if (isset($changedAttributes['parent_id']) || $insert) {
            Yii::$app->cache->delete('category_child_ids_' . $this->id);
            // Clear parent category cache if exists
            if ($this->parent_id) {
                Yii::$app->cache->delete('category_child_ids_' . $this->parent_id);
            }
        }
        
        // Update related colors
        if (!$insert) {
            $this->updateAvailableColors();
        }
        
        // Clear various caches
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
                ->all();
        }, 3600);
    }
}
