<?php

namespace ZakharovAndrew\shop\controllers;

use ZakharovAndrew\shop\models\ProductSearch;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * Main catalog controller
 * @author Andrew Zakharov https://github.com/ZakharovAndrew
 */
class CatalogController extends Controller
{

    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        
        $module = \Yii::$app->getModule('shop');
        $productPerPage = $module->productPerPage ?? 20; // default 20
        
        $dataProvider = $searchModel->searchCatalog($this->request->queryParams, $productPerPage);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
