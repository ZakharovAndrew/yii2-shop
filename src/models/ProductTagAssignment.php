<?php

namespace ZakharovAndrew\shop\models;

use Yii;
use ZakharovAndrew\shop\Module;

/**
 * This is the model class for table "product_tag_assignment".
 *
 * @property int $id
 * @property int $product_id
 * @property int $tag_id
 * @property string $created_at
 * 
 * @property Product $product
 * @property ProductTag $tag
 */
class ProductTagAssignment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_tag_assignment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'tag_id'], 'required'],
            [['product_id', 'tag_id'], 'integer'],
            [['created_at'], 'safe'],
            
            // Unique constraint - prevent duplicate assignments
            [['product_id', 'tag_id'], 'unique', 'targetAttribute' => ['product_id', 'tag_id']],
            
            // Foreign key validations
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductTag::class, 'targetAttribute' => ['tag_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => Module::t('Product'),
            'tag_id' => Module::t('Tag'),
            'created_at' => Module::t('Created At'),
        ];
    }

    /**
     * Gets query for [[Product]].
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Gets query for [[Tag]].
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(ProductTag::class, ['id' => 'tag_id']);
    }

    /**
     * Check if assignment exists for product and tag
     * @param int $productId
     * @param int $tagId
     * @return bool
     */
    public static function assignmentExists($productId, $tagId)
    {
        return static::find()
            ->where(['product_id' => $productId, 'tag_id' => $tagId])
            ->exists();
    }

    /**
     * Create assignment if it doesn't exist
     * @param int $productId
     * @param int $tagId
     * @return bool
     */
    public static function createAssignment($productId, $tagId)
    {
        if (self::assignmentExists($productId, $tagId)) {
            return true; // Assignment already exists
        }
        
        $assignment = new self([
            'product_id' => $productId,
            'tag_id' => $tagId,
        ]);
        
        return $assignment->save();
    }

    /**
     * Remove assignment
     * @param int $productId
     * @param int $tagId
     * @return bool
     */
    public static function removeAssignment($productId, $tagId)
    {
        $assignment = static::find()
            ->where(['product_id' => $productId, 'tag_id' => $tagId])
            ->one();
            
        if ($assignment) {
            return $assignment->delete();
        }
        
        return true; // Assignment doesn't exist
    }

    /**
     * Get all tag IDs for a product
     * @param int $productId
     * @return array
     */
    public static function getTagIdsForProduct($productId)
    {
        return static::find()
            ->select('tag_id')
            ->where(['product_id' => $productId])
            ->column();
    }

    /**
     * Get all product IDs for a tag
     * @param int $tagId
     * @return array
     */
    public static function getProductIdsForTag($tagId)
    {
        return static::find()
            ->select('product_id')
            ->where(['tag_id' => $tagId])
            ->column();
    }

    /**
     * Get assignments count for a product
     * @param int $productId
     * @return int
     */
    public static function getAssignmentsCountForProduct($productId)
    {
        return static::find()
            ->where(['product_id' => $productId])
            ->count();
    }

    /**
     * Get assignments count for a tag
     * @param int $tagId
     * @return int
     */
    public static function getAssignmentsCountForTag($tagId)
    {
        return static::find()
            ->where(['tag_id' => $tagId])
            ->count();
    }

    /**
     * Remove all assignments for a product
     * @param int $productId
     * @return int Number of deleted assignments
     */
    public static function removeAllAssignmentsForProduct($productId)
    {
        return static::deleteAll(['product_id' => $productId]);
    }

    /**
     * Remove all assignments for a tag
     * @param int $tagId
     * @return int Number of deleted assignments
     */
    public static function removeAllAssignmentsForTag($tagId)
    {
        return static::deleteAll(['tag_id' => $tagId]);
    }

    /**
     * Get assignments with product and tag data
     * @param int $productId
     * @return ProductTagAssignment[]
     */
    public static function getAssignmentsWithDataForProduct($productId)
    {
        return static::find()
            ->with(['product', 'tag'])
            ->where(['product_id' => $productId])
            ->all();
    }

    /**
     * Get assignments with product and tag data for tag
     * @param int $tagId
     * @return ProductTagAssignment[]
     */
    public static function getAssignmentsWithDataForTag($tagId)
    {
        return static::find()
            ->with(['product', 'tag'])
            ->where(['tag_id' => $tagId])
            ->all();
    }

    /**
     * Before save handler
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->created_at)) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            return true;
        }
        return false;
    }

    /**
     * After save handler - clear related caches
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Clear cache for product tags
        Yii::$app->cache->delete('product_tags_' . $this->product_id);
        
        // Clear cache for tag products count
        Yii::$app->cache->delete('tag_products_count_' . $this->tag_id);
    }

    /**
     * After delete handler - clear related caches
     */
    public function afterDelete()
    {
        parent::afterDelete();
        
        // Clear cache for product tags
        Yii::$app->cache->delete('product_tags_' . $this->product_id);
        
        // Clear cache for tag products count
        Yii::$app->cache->delete('tag_products_count_' . $this->tag_id);
    }
}