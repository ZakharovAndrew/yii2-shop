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
 * @property string|null $composition
 * @property float|null $weight
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
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    
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
            [['name', 'url', 'images', 'price'], 'required'],
            [['description', 'composition'], 'string'],
            [['weight'], 'number'],
            [['category_id', 'user_id', 'count_views', 'price', 'status', 
              'bulk_price_quantity_1', 'bulk_price_1', 
              'bulk_price_quantity_2', 'bulk_price_2', 
              'bulk_price_quantity_3', 'bulk_price_3'], 'integer'],
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
            'composition' => Module::t('Composition'),
            'weight' => Module::t('Weight'), 
            'url' => Module::t('Url'),
            'images' => Module::t('Images'),
            'category_id' => Module::t('Category'),
            'user_id' => 'User ID',
            'count_views' => Module::t('Count Views'),
            'price' => Module::t('Price'),
            'quantity' => Module::t('Quantity'),
            'status' => Module::t('Status'),
            'created_at' => 'Created At',
            'bulk_price_quantity_1' => Module::t('Bulk quantity 1'),
            'bulk_price_1' => Module::t('Bulk price 1'),
            'bulk_price_quantity_2' => Module::t('Bulk quantity 2'),
            'bulk_price_2' => Module::t('Bulk price 2'),
            'bulk_price_quantity_3' => Module::t('Bulk quantity 3'),
            'bulk_price_3' => Module::t('Bulk price 3'),
        ];
    }
    
    /**
     * Get status list
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_DELETED => Module::t('Deleted'),
            self::STATUS_ACTIVE => Module::t('Active'),
        ];
    }
    
    /**
     * Get actual price based on quantity
     * @param int $quantity
     * @return int
     */
    public function getActualPrice($quantity)
    {
        // Check bulk prices in descending order (from highest quantity)
        if ($this->bulk_price_quantity_3 && $quantity >= $this->bulk_price_quantity_3 && $this->bulk_price_3) {
            return $this->bulk_price_3;
        }
        
        if ($this->bulk_price_quantity_2 && $quantity >= $this->bulk_price_quantity_2 && $this->bulk_price_2) {
            return $this->bulk_price_2;
        }
        
        if ($this->bulk_price_quantity_1 && $quantity >= $this->bulk_price_quantity_1 && $this->bulk_price_1) {
            return $this->bulk_price_1;
        }
        
        return $this->price;
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
                ->andWhere(['status' => 1])
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
    
    public function canSubtractFromStock($quantity)
    {
        return $this->quantity >= $quantity;
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
            throw new \Exception('Not enough stock');
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
    
    public function getCategory()
    {
        return $this->hasOne(ProductCategory::class, ['id' => 'category_id']);
    }
     
    /**
     * Gets most viewed products
     * @param int $limit
     * @return Product[]
     */
    public static function getPopularProducts($limit = 5)
    {
        return self::find()
            ->orderBy(['count_views' => SORT_DESC])
            ->limit($limit)
            ->all();
    }
}
