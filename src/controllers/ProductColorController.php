<?php
/**
 * Shop: Product Color
 *  
 * @link https://github.com/ZakharovAndrew/yii2-shop/
 * @copyright Copyright (c) 2023-2025 Zakharov Andrew
 */
namespace ZakharovAndrew\shop\controllers;

use Yii;
use ZakharovAndrew\shop\models\ProductColor;
use ZakharovAndrew\shop\models\ProductColorSearch;
use yii\web\NotFoundHttpException;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\user\controllers\ParentController;

/**
 * ProductColorController implements the CRUD actions for ProductColor model.
 */
class ProductColorController extends ParentController
{
    public $controller_id = 2006;

    /**
     * Lists all ProductColor models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductColorSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ProductColor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ProductColor();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProductColor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProductColor model.
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
     * Toggle color status
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->is_active = !$model->is_active;
        
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', Module::t('Status updated successfully'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Failed to update status'));
        }
        
        return $this->redirect(['index']);
    }
    
    /**
     * Move color position up
     */
    public function actionMoveUp($id)
    {
        $model = $this->findModel($id);
        if ($model->moveUp()) {
            Yii::$app->session->setFlash('success', Module::t('Position updated successfully'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Cannot move color up'));
        }
        
        if (Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
    }

    /**
     * Move color position down
     */
    public function actionMoveDown($id)
    {
        $model = $this->findModel($id);
        if ($model->moveDown()) {
            Yii::$app->session->setFlash('success', Module::t('Position updated successfully'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Cannot move color down'));
        }
        
        if (Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
    }


    /**
     * Finds the ProductColor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ProductColor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductColor::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('The requested page does not exist.'));
    }
}