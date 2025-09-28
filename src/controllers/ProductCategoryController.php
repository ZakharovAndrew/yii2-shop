<?php

namespace ZakharovAndrew\shop\controllers;

use ZakharovAndrew\shop\models\Product;
use ZakharovAndrew\shop\models\ProductCategory;
use ZakharovAndrew\shop\models\ProductCategorySearch;
use yii\data\Pagination;
use ZakharovAndrew\user\controllers\ParentController;
use yii\web\NotFoundHttpException;

/**
 * ProductCategoryController implements the CRUD actions for ProductCategory model.
 */
class ProductCategoryController extends ParentController
{
    public $full_access_actions = ['view'];
    
    public $controller_id = 2002;
    
    /**
     * Lists all ProductCategory models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductCategorySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays products by category
     * @param string $url category URL
     * @param array $colors array of color IDs for filtering
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($url, array $colors = [])
    {
        $model = $this->findModelByUrl($url);
        
        // Ensure colors is an array and remove empty values
        if (!is_array($colors)) {
            $colors = [$colors];
        }
        $selectedColors = array_filter($colors);
        
        // Get available colors for this category
        $availableColors = $model->getCachedAvailableColors();
        
        // Base products query
        $query = Product::find()
            ->where(['or', 
                ['category_id' => $model->id], 
                'category_id IN (SELECT id FROM product_category WHERE parent_id = '.$model->id.')'
            ])
            ->andWhere(['status' => 1]);
        
        // Apply color filter if colors are selected
        if (!empty($selectedColors)) {
            $query->andWhere(['color_id' => $selectedColors]);
        }
        
        $query->orderBy('position DESC');
                
        // Create query copy
        $countQuery = clone $query;
        $module = \Yii::$app->getModule('shop');
        $productPerPage = $module->productPerPage ?? 20; // default 20
        // Initialize Pagination, show 48 items per page
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $productPerPage]);
        // Make URL parameters SEO-friendly
        $pages->pageSizeParam = false;
        $products = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        
        return $this->render('view', [
            'model' => $model,
            'products' => $products,
            'pagination' => $pages,
            'availableColors' => $availableColors,
            'selectedColors' => $selectedColors
        ]);
    }

    /**
     * Creates a new ProductCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ProductCategory();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'url' => $model->url]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProductCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'url' => $model->url]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProductCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProductCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ProductCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductCategory::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    protected function findModelByUrl($url)
    {
        if (($model = ProductCategory::findOne(['url' => $url])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
