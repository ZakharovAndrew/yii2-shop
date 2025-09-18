<?php

namespace ZakharovAndrew\shop\models;

use Yii;

/**
 * This is the model class for table "category_colors".
 *
 * @property int $id
 * @property int $category_id
 * @property int $color_id
 * @property string $created_at
 *
 * @property ProductCategory $category
 * @property ProductColor $color
 */
class CategoryColors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category_colors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'color_id'], 'required'],
            [['category_id', 'color_id'], 'integer'],
            [['created_at'], 'safe'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductCategory::class, 'targetAttribute' => ['category_id' => 'id']],
            [['color_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductColor::class, 'targetAttribute' => ['color_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'color_id' => 'Color ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Category]].
     */
    public function getCategory()
    {
        return $this->hasOne(ProductCategory::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Color]].
     */
    public function getColor()
    {
        return $this->hasOne(ProductColor::class, ['id' => 'color_id']);
    }
}
