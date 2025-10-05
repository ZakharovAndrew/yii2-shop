<?php

namespace ZakharovAndrew\shop\models;

use Yii;
use ZakharovAndrew\shop\models\Product;
use ZakharovAndrew\shop\models\ProductCategory;

class Sitemap
{
    /**
     * Generate sitemap
     */
    private function generateSitemap()
    {
        $data = [];
        
        // Products ategory
        $data = array_merge($data, $this->getCategories());
        
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
            ->asArray()
            ->all();
            
        $data = [];
        
        foreach ($categories as $category) {
            $data[] = [
                'loc' => Yii::$app->urlManager->createAbsoluteUrl(['/shop/product-category/view', 'url' => $category['url']]),
                'lastmod' => date('c', $category['updated_at'] ?? time()),
                'changefreq' => 'weekly',
                'priority' => '0.8'
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
            ->select(['url', 'updated_at', 'created_at'])
            ->where(['status' => Product::STATUS_ACTIVE])
            ->asArray()
            ->all();
            
        $data = [];
        
        foreach ($products as $product) {
            $data[] = [
                'loc' => Yii::$app->urlManager->createAbsoluteUrl(['/shop/product/view', 'url' => $product['url']]),
                'lastmod' => date('c', $product['updated_at'] ?? $product['created_at'] ?? time()),
                'changefreq' => 'weekly',
                'priority' => '0.9'
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
            $data[] = [
                'loc' => Yii::$app->urlManager->createAbsoluteUrl(['/shop/shop/view', 'url' => $shop['url']]),
                'lastmod' => date('c', $shop['updated_at'] ?? time()),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ];
        }
        
        return $data;
    }
}