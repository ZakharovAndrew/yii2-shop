<?php

namespace ZakharovAndrew\shop\models;

use Yii;
use ZakharovAndrew\shop\models\Product;
use ZakharovAndrew\shop\models\ProductCategory;

class Sitemap
{
    public $priority = [
        'categories' => '0.8',
        'products' => '0.9',
        'shops' => '0.7'
    ];
    
    /**
     * Generate sitemap
     */
    public function generateSitemap()
    {        
        // Products category
        $data = $this->getCategories();
        
        // Products
        $data = array_merge($data, $this->getProducts());
        
        // Shops
        $data = array_merge($data, $this->getShops());
        
        return $data;
    }
        
    /**
     * Product categories
     */
    private function getCategories()
    {
        $categories = ProductCategory::find()
            ->select(['url', 'updated_at'])
            ->where(['status' => ProductCategory::STATUS_ACTIVE])
            ->asArray()
            ->all();
            
        $data = [];
        
        foreach ($categories as $category) {
            $lastmod = !empty($shop['updated_at']) ? strtotime($shop['updated_at']) : time();
            $data[] = [
                'loc' => Yii::$app->urlManager->createAbsoluteUrl(['/shop/product-category/view', 'url' => $category['url']]),
                'lastmod' => date('c', $lastmod),
                'changefreq' => 'weekly',
                'priority' => $this->priority['categories']
            ];
        }
        
        return $data;
    }
    
    /**
     * Products (active)
     */
    private function getProducts()
    {
        $products = Product::find()
            //->select(['url', 'updated_at', 'created_at'])
            ->select(['url', 'created_at'])
            ->where(['status' => Product::STATUS_ACTIVE])
            ->asArray()
            ->all();
            
        $data = [];
        
        foreach ($products as $product) {
            $lastmod = $product['updated_at'] ?? $product['created_at'] ?? null;
            $data[] = [
                'loc' => Yii::$app->urlManager->createAbsoluteUrl(['/shop/product/view', 'url' => $product['url']]),
                'lastmod' => date('c', !empty($lastmod) ? strtotime($lastmod) : time()),
                'changefreq' => 'weekly',
                'priority' => $this->priority['products']
            ];
        }
        
        return $data;
    }
    
    /**
     * Shops
     */
    private function getShops()
    {
        $shops = Shop::find()
            ->select(['url', 'updated_at'])
            ->asArray()
            ->all();
            
        $data = [];
        
        foreach ($shops as $shop) {
            $lastmod = !empty($shop['updated_at']) ? strtotime($shop['updated_at']) : time();
            $data[] = [
                'loc' => Yii::$app->urlManager->createAbsoluteUrl(['/shop/shop/view', 'url' => $shop['url']]),
                'lastmod' => date('c', $lastmod),
                'changefreq' => 'weekly',
                'priority' => $this->priority['shops']
            ];
        }
        
        return $data;
    }
}