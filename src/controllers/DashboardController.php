<?php

namespace ZakharovAndrew\shop\controllers;

use ZakharovAndrew\shop\models\Product;
use ZakharovAndrew\user\controllers\ParentController;

class DashboardController extends ParentController
{
    public $controller_id = 2007;
    
    /**
     * Store Dashboard
     *
     * @return string
     */
    public function actionIndex()
    {
        $addedProduct = Product::find()
                ->select('hour(created_at) as h, count(*) as cnt')
                ->where(['>', 'created_at', date('Y-m-d 00:00:00')])
                ->groupBy('hour(created_at)')
                ->asArray()
                ->all();
        
        $updatedProduct = Product::find()
                ->select('hour(updated_at) as h, count(*) as cnt')
                ->where(['>', 'updated_at', date('Y-m-d 00:00:00')])
                ->andWhere('updated_at <> created_at')
                ->groupBy('hour(updated_at)')
                ->asArray()
                ->all();
        
        return $this->render('index', [
            'addedProduct' => $addedProduct,
            'updatedProduct' => $updatedProduct,
        ]);
    }

}
