<?php

namespace ZakharovAndrew\shop\controllers;

use Yii;
use ZakharovAndrew\shop\models\Product;
use ZakharovAndrew\shop\models\Shop;
use ZakharovAndrew\shop\models\ShopSearch;
use ZakharovAndrew\user\controllers\ParentController;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;

/**
 * ShopController implements the CRUD actions for Shop model.
 */
class ShopController extends ParentController
{
    
    public $controller_id = 2001;
    
    public $full_access_actions = ['view'];

    /**
     * Lists all Shop models.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->identity->isAdmin()) {
            if (!Yii::$app->user->identity->hasRole('shop_owner')) {
                throw new NotFoundHttpException('The requested page does not exist.');
            } else {
                // check store
                $store = Yii::$app->user->identity->getRoleSubjectsArray("shop_owner");
                
                if (!isset($store[0])) {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
                
                $model = $this->findModel($store[0]);
                
                return $this->redirect(['view', 'url' => $model->url]);
            }
        }
        $searchModel = new ShopSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays products of store
     * @param string $url link to store
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($url)
    {
        $model = $this->findModelByUrl($url);
        
        $query = Product::find()->where(['shop_id' => $model->id])->andWhere(['status' => 1])->orderBy('position DESC');
                
        // делаем копию выборки
        $countQuery = clone $query;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 48]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;
        $products = $query->offset($pages->offset)
            ->limit($pages->limit)
            //->orderBy(Product::getSortby($sortby))
            //->orderBy('datetime_at desc')
            ->all();
        
        return $this->render('view', [
            'model' => $model,
            'products' => $products,
            'pagination' => $pages
        ]);
    }

    /**
     * Creates a new Shop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Shop();

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
     * Updates an existing Shop model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        if (!Yii::$app->user->identity->isAdmin()) {
            if (!Yii::$app->user->identity->hasRole('shop_owner')) {
                throw new NotFoundHttpException('The requested page does not exist.');
            } else {
                // check store
                $store = Yii::$app->user->identity->getRoleSubjectsArray("shop_owner");
                
                if (!is_array($store) || !in_array($id, $store)) {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }
        }

        if ($this->request->isPost && $model->load($this->request->post())) {
            if ($model->save()) {
        
                Yii::$app->session->setFlash('success', 'Данные обновлены.');
                return $this->redirect(['view', 'url' => $model->url]);
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка обновления'.var_export($model->getErrors(), true));
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Shop model.
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
     * Finds the Shop model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Shop the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Shop::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    protected function findModelByUrl($url)
    {
        if (($model = Shop::findOne(['url' => $url])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
