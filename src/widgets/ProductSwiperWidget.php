<?php

namespace ZakharovAndrew\shop\widgets;

use yii\base\Widget;
use ZakharovAndrew\shop\models\Product;

class ProductSwiperWidget extends Widget
{
    public $productIds = []; // Array of product IDs to display
    public $categoryId; // Category ID (alternative method)
    public $limit = 14; // Number of products to show
    public $title = 'New Arrivals'; // Block title
    public $viewFile = '@vendor/zakharov-andrew/yii2-shop/src/views/catalog/_product';
    public $id = 'product-swiper'; // Main div block ID
    public $swiperId = 'swiper-products'; // Swiper container ID
    public $orderBy = 'created_at DESC'; // Sorting order

    public function run()
    {
        $products = $this->getProducts();

        if (empty($products)) {
            return ''; // Return empty string if no products found
        }

        return $this->render('product-swiper', [
            'products' => $products,
            'viewFile' => $this->viewFile,
            'title' => $this->title,
            'id' => $this->id,
            'swiperId' => $this->swiperId,
        ]);
    }

    /**
     * Get products based on provided parameters
     * @return array
     */
    protected function getProducts()
    {
        $query = Product::find()
            ->where(['status' => 1]);

        // If specific product IDs are provided
        if (!empty($this->productIds)) {
            $query->andWhere(['id' => $this->productIds]);
        } 
        // If category ID is provided
        elseif (!empty($this->categoryId)) {
            $query->andWhere(['in', 'category_id', $this->getCategoryIds()]);
        }

        return $query->limit($this->limit)
            ->orderBy($this->orderBy)
            ->all();
    }

    /**
     * Get category ID and all its subcategories
     * @return array
     */
    protected function getCategoryIds()
    {
        // Here you can implement logic to get all subcategories
        // Currently using simplified version from your code
        return is_array($this->categoryId) ? $this->categoryId : [$this->categoryId];
    }
}