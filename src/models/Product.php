<?php

namespace ZakharovAndrew\shop\models;

use Yii;

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
            [['category_id', 'user_id', 'count_views'], 'integer'],
            [['created_at'], 'safe'],
            [['title', 'url', 'images'], 'string', 'max' => 255],
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
            'description' => 'Description',
            'url' => 'Url',
            'images' => 'Images',
            'category_id' => 'Category ID',
            'user_id' => 'User ID',
            'count_views' => 'Count Views',
            'created_at' => 'Created At',
        ];
    }
}
