<?php

namespace ZakharovAndrew\shop\models;

use Yii;
use ZakharovAndrew\shop\Module;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name
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
            [['name', 'url', 'images'], 'required'],
            [['description'], 'string'],
            [['category_id', 'user_id', 'count_views', 'price'], 'integer'],
            [['created_at'], 'safe'],
            ['quantity', 'integer'],
            [['name', 'url', 'images', 'param1', 'param2', 'param3'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Module::t('Name'),
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
    
    /**
     * Добавляет количество товара на склад
     */
    public function addToStock($quantity, $userId, $comment = null)
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }
        
        $this->quantity += $quantity;
        if (!$this->save(false)) {
            throw new \RuntimeException('Failed to update product quantity');
        }

        $movement = new ProductStockMovement([
            'product_id' => $this->id,
            'quantity' => $quantity,
            'user_id' => $userId,
            'comment' => $comment,
        ]);

        if (!$movement->save()) {
            throw new \RuntimeException('Failed to save stock movement');
        }
    }
    
    /**
     * Уменьшает количество товара на складе
     */
    public function subtractFromStock($quantity, $userId, $comment = null)
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }
        
        if ($this->quantity < $quantity) {
            throw new \RuntimeException('Not enough stock');
        }
        

        $this->quantity -= $quantity;
        if (!$this->save(false)) {
            throw new \RuntimeException('Failed to update product quantity');
        }

        $movement = new ProductStockMovement([
            'product_id' => $this->id,
            'quantity' => -$quantity,
            'user_id' => $userId,
            'comment' => $comment,
        ]);

        if (!$movement->save()) {
            throw new \RuntimeException('Failed to save stock movement');
        }

        return true;
    }
    
    public function getStockMovements()
    {
        return $this->hasMany(ProductStockMovement::class, ['product_id' => 'id']);
    }
}
