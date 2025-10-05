<?php

namespace ZakharovAndrew\shop\controllers;

use Yii;
use yii\web\Controller;
use ZakharovAndrew\shop\models\Product;
use ZakharovAndrew\shop\models\ProductCategory;
use yii\data\Pagination;

class SearchController extends Controller
{
    public function actionIndex($q = null, $sorting = 'default')
    {       
        if (empty($q)) {
            return $this->render('no-query');
        }
        
        $module = Yii::$app->getModule('shop');
        
        if (is_callable($module->searchTransformFunction)) {
            $q = call_user_func($module->searchTransformFunction, $q);
        }
        
        // Base products query
        $query = Product::find()
            ->where(['LIKE', 'name', $q])
            ->andWhere(['status' => 1]);
        
        // sorting
        $query->orderBy(ProductCategory::SORTING[$sorting] ?? ProductCategory::SORTING['default']);
        
        // Create query copy
        $countQuery = clone $query;
        $productPerPage = \Yii::$app->shopSettings->get('productPerPage', 20);
        // Initialize Pagination, show 48 items per page
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $productPerPage]);
        // Make URL parameters SEO-friendly
        $pages->pageSizeParam = false;
        $products = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        
        return $this->render('index', [
            'products' => $products,
            'pagination' => $pages,
            'q' => $q
        ]);
    }
}
