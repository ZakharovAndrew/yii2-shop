<?php

namespace ZakharovAndrew\shop\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use ZakharovAndrew\shop\models\Settings;
use ZakharovAndrew\shop\models\SettingsSearch;

/**
 * Settings controller for managing shop settings
 */
class SettingsController extends Controller
{
    public $controller_id = 2005;
    
    /**
     * Lists all settings with quick update form.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SettingsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Admin panel for settings management (CRUD operations).
     *
     * @return string
     */
    public function actionAdmin()
    {
        $searchModel = new SettingsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $model = new Settings();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Setting created successfully.');
                return $this->redirect(['admin']);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single setting.
     * @param int $id ID
     * @return string
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing setting.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Setting updated successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing setting.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Setting deleted successfully.');

        return $this->redirect(['admin']);
    }

    /**
     * Bulk update settings from a form.
     * @return \yii\web\Response
     */
    public function actionBulkUpdate()
    {
        $postData = $this->request->post();
        
        if (isset($postData['Settings'])) {
            $settingsToUpdate = [];
            
            foreach ($postData['Settings'] as $key => $value) {
                $settingsToUpdate[$key] = ['value' => $value];
            }
            
            if (Settings::setMultiple($settingsToUpdate)) {
                Yii::$app->shopSettings->clearCache();
                Yii::$app->session->setFlash('success', 'Settings updated successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Error updating settings.');
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Clear settings cache.
     * @return \yii\web\Response
     */
    public function actionClearCache()
    {
        Yii::$app->shopSettings->clearCache();
        Yii::$app->session->setFlash('success', 'Settings cache cleared successfully.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Settings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Settings the loaded model
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Settings::findOne($id)) !== null) {
            return $model;
        }

        throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
    }
}