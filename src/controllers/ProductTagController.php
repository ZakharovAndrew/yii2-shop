<?php

namespace ZakharovAndrew\shop\controllers;

use Yii;
use ZakharovAndrew\shop\models\Product;
use ZakharovAndrew\shop\models\ProductTag;
use ZakharovAndrew\shop\models\ProductTagSearch;
use yii\web\NotFoundHttpException;
use ZakharovAndrew\user\controllers\ParentController;
use ZakharovAndrew\shop\Module;
use yii\data\Pagination;

/**
 * ProductTagController implements the CRUD actions for ProductTag model.
 */
class ProductTagController extends ParentController
{
    
    /**
     * Lists all ProductTag models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductTagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProductTag model.
     * @param string $url
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($url)
    {
        $model = $this->findModelByUrl($url);
        
        // Check if tag is available for current user
        if (!$model->isAvailableForCurrentUser()) {
            throw new NotFoundHttpException('The requested tag does not exist.');
        }

        // Base products query
        $query = Product::find()
            ->where('id IN (SELECT product_id FROM product_tag_assignment WHERE tag_id = '.$model->id.')')
            ->andWhere(['status' => 1]);
        
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
        
        return $this->render('view', [
            'model' => $model,
            'products' => $products,
            'pagination' => $pages,
        ]);
    }

    /**
     * Creates a new ProductTag model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProductTag();

        if ($model->load(Yii::$app->request->post())) {
            // Handle allowed roles array
            if ($model->allowed_roles && is_array($model->allowed_roles)) {
                $model->setAllowedRolesArray($model->allowed_roles);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Tag created successfully.');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Error creating tag.');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProductTag model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            // Handle allowed roles array
            if ($model->allowed_roles && is_array($model->allowed_roles)) {
                $model->setAllowedRolesArray($model->allowed_roles);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Tag updated successfully.');
                return $this->redirect(['view', 'url' => $model->url]);
            } else {
                Yii::$app->session->setFlash('error', 'Error updating tag.');
            }
        }

        // Convert allowed roles to array for form
        $model->allowed_roles = $model->getAllowedRolesArray();

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProductTag model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('success', 'Tag deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Bulk delete action
     * @return mixed
     */
    public function actionBulkDelete()
    {
        $ids = Yii::$app->request->post('ids');
        
        if (!$ids) {
            Yii::$app->session->setFlash('error', 'No tags selected for deletion.');
            return $this->redirect(['index']);
        }

        $deletedCount = 0;
        foreach ($ids as $id) {
            $model = ProductTag::findOne($id);
            if ($model) {
                if ($model->delete()) {
                    $deletedCount++;
                }
            }
        }

        if ($deletedCount > 0) {
            Yii::$app->session->setFlash('success', "Successfully deleted {$deletedCount} tags.");
        } else {
            Yii::$app->session->setFlash('error', 'Error deleting tags.');
        }

        return $this->redirect(['index']);
    }
    
    /**
     * Move tag position up
     */
    public function actionMoveUp($id)
    {
        $model = $this->findModel($id);
        if ($model->moveUp()) {
            Yii::$app->session->setFlash('success', Module::t('Position updated successfully'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Cannot move tag up'));
        }
        
        if (Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
    }

    /**
     * Move tag position down
     */
    public function actionMoveDown($id)
    {
        $model = $this->findModel($id);
        if ($model->moveDown()) {
            Yii::$app->session->setFlash('success', Module::t('Position updated successfully'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Cannot move tag down'));
        }
        
        if (Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the ProductTag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductTag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductTag::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested tag does not exist.');
    }

    /**
     * Finds the ProductTag model based on its URL.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $url
     * @return ProductTag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByUrl($url)
    {
        if (($model = ProductTag::findByUrl($url)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested tag does not exist.');
    }
}