<?php

namespace ZakharovAndrew\shop\controllers;

use Yii;
use ZakharovAndrew\shop\models\ProductProperty;
use ZakharovAndrew\shop\models\ProductPropertySearch;
use ZakharovAndrew\shop\models\ProductPropertyOption;
use ZakharovAndrew\user\controllers\ParentController;
use yii\web\NotFoundHttpException;
use ZakharovAndrew\shop\Module;

/**
 * ProductPropertyController implements the CRUD actions for ProductProperty model.
 */
class ProductPropertyController extends ParentController
{
    public $controller_id = 2003;
    
    /**
     * Lists all ProductProperty models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductPropertySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProductProperty model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProductProperty model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ProductProperty();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', Module::t('Property created successfully'));
                return $this->redirect(['index']);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProductProperty model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Module::t('Property updated successfully'));
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProductProperty model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', Module::t('Property deleted successfully'));

        return $this->redirect(['index']);
    }

    /**
     * Manage options for SELECT type properties
     */
    public function actionOptions($id)
    {
        $model = $this->findModel($id);
        
        if (!$model->isSelectType()) {
            Yii::$app->session->setFlash('error', Module::t('Options are available only for dropdown list properties'));
            return $this->redirect(['view', 'id' => $id]);
        }

        // Create empty option for adding new ones
        $newOption = new ProductPropertyOption();
        $newOption->property_id = $id;

        if (Yii::$app->request->isPost) {
            // Handle bulk options update
            $postOptions = Yii::$app->request->post('Option', []);
            
            foreach ($postOptions as $optionData) {
                if (!empty($optionData['id'])) {
                    $option = ProductPropertyOption::findOne($optionData['id']);
                } else {
                    $option = new ProductPropertyOption();
                    $option->property_id = $id;
                }
                
                if ($option && $option->load($optionData, '') && $option->save()) {
                    // Option saved
                }
            }
            
            Yii::$app->session->setFlash('success', Module::t('Options saved successfully'));
            return $this->refresh();
        }

        return $this->render('options', [
            'model' => $model,
            'newOption' => $newOption,
        ]);
    }

    /**
     * Add new option via AJAX
     */
    public function actionAddOption($property_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $option = new ProductPropertyOption();
        $option->property_id = $property_id;
        $option->load(Yii::$app->request->post());
        
        if ($option->save()) {
            return ['success' => true, 'id' => $option->id];
        }
        
        return ['success' => false, 'errors' => $option->errors];
    }

    /**
     * Delete option
     */
    public function actionDeleteOption($id)
    {
        $option = ProductPropertyOption::findOne($id);
        if ($option) {
            $property_id = $option->property_id;
            $option->delete();
            Yii::$app->session->setFlash('success', Module::t('Option deleted'));
            return $this->redirect(['options', 'id' => $property_id]);
        }
        
        throw new NotFoundHttpException(Module::t('Option not found'));
    }

    /**
     * Toggle property status (active/inactive)
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->is_active = !$model->is_active;
        $model->changeOptions = false;
        
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', Module::t('Status updated successfully'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Failed to update status'));
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Update sort order via AJAX
     */
    public function actionUpdateSort()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $post = Yii::$app->request->post();
        if (isset($post['items']) && is_array($post['items'])) {
            foreach ($post['items'] as $position => $id) {
                $model = ProductProperty::findOne($id);
                if ($model) {
                    $model->sort_order = $position;
                    $model->save(false);
                }
            }
            return ['success' => true];
        }
        
        return ['success' => false];
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
            Yii::$app->session->setFlash('error', Module::t('Cannot move property up'));
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
            Yii::$app->session->setFlash('error', Module::t('Cannot move property down'));
        }
        
        if (Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the ProductProperty model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ProductProperty the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductProperty::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('The requested page does not exist.'));
    }
}