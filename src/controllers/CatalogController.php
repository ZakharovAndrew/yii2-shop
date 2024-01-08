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
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Product models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->searchCatalog($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }    
}
