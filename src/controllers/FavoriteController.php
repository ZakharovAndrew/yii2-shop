<?php

namespace ZakharovAndrew\shop\controllers;

use Yii;
use yii\web\Response;
use ZakharovAndrew\user\controllers\ParentController;
use ZakharovAndrew\shop\models\Product;
use ZakharovAndrew\shop\models\ProductSearch;

class FavoriteController extends ParentController
{
    /**
     * Actions that require authorization
     * @var array 
     */
    public $auth_access_actions = ['add', 'remove', 'toggle', 'list'];

    /**
     * Add product to favorites
     */
    public function actionAdd($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $product = Product::findOne($id);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        if ($product->addToFavorites()) {
            return [
                'success' => true, 
                'message' => 'Product added to favorites',
                'favoritesCount' => Product::getFavoritesCount()
            ];
        }
        
        return ['success' => false, 'message' => 'Error adding to favorites'];
    }

    /**
     * Remove product from favorites
     */
    public function actionRemove($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $product = Product::findOne($id);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        if ($product->removeFromFavorites()) {
            return [
                'success' => true, 
                'message' => 'Product removed from favorites',
                'favoritesCount' => Product::getFavoritesCount()
            ];
        }
        
        return ['success' => false, 'message' => 'Error removing from favorites'];
    }

    /**
     * Toggle product favorite status
     */
    public function actionToggle($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $product = Product::findOne($id);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        $newStatus = $product->toggleFavorite();
        
        return [
            'success' => true,
            'isFavorite' => $newStatus,
            'favoritesCount' => Product::getFavoritesCount(),
            'message' => $newStatus ? 'Added to favorites' : 'Removed from favorites'
        ];
    }

    /**
     * List user's favorite products
     */
    public function actionList()
    {
        $searchModel = new ProductSearch();
        
        $productPerPage = \Yii::$app->shopSettings->get('productPerPage', 20);
        
        $dataProvider = $searchModel->searchFavorite($this->request->queryParams, $productPerPage);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Get favorites count (for AJAX)
     */
    public function actionCount()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return [
            'success' => true,
            'count' => Product::getFavoritesCount()
        ];
    }
}