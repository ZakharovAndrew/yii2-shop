<?php

namespace ZakharovAndrew\shop\controllers;

use Yii;
use ZakharovAndrew\shop\models\Product;
use ZakharovAndrew\shop\models\ProductSearch;
use ZakharovAndrew\shop\models\ProductProperty;
use ZakharovAndrew\shop\models\ProductPropertyValue;
use ZakharovAndrew\shop\models\ProductCategory;
use ZakharovAndrew\shop\models\Shop;
use ZakharovAndrew\user\controllers\ParentController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends ParentController
{
    public $controller_id = 4001;
    
    public $full_access_actions = ['view'];

    /**
     * Lists all Product models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($url)
    {
        $model = $this->findModelByUrl($url);
        
        // increase the number of views
        $model->count_views++;
        $model->save();

        return $this->render('view', [
            'model' => $model,
            'more_products' => $model->getMoreProducts(6)
        ]);
    }
    

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $shop_id store ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionCreate($shop_id = null)
    {
        $model = new Product();
        
        if (!empty($shop_id)) {
            if (Shop::findOne(['id' => $shop_id]) == null) {
                throw new NotFoundHttpException('The requested page does not exist!');
            }
            $model->shop_id = $shop_id;
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // saving properties
                $this->saveProductProperties($model, Yii::$app->request->post('properties', []));
            
                return $this->redirect(['view', 'url' => $model->url]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'shop_id' => $shop_id
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            // saving properties
            $this->saveProductProperties($model, Yii::$app->request->post('properties', []));
            
            return $this->redirect(['view', 'url' => $model->url]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    /**
     * Сохраняет свойства товара
     */
    protected function saveProductProperties($product, $propertiesData)
    {
        foreach ($propertiesData as $propertyId => $value) {
            $property = ProductProperty::findOne($propertyId);
            if (!$property) continue;

            // Ищем существующее значение
            $propertyValue = ProductPropertyValue::find()
                ->where(['product_id' => $product->id, 'property_id' => $propertyId])
                ->one();

            if (!$propertyValue) {
                $propertyValue = new ProductPropertyValue();
                $propertyValue->product_id = $product->id;
                $propertyValue->property_id = $propertyId;
            }

            // Устанавливаем значение в зависимости от типа свойства
            $propertyValue->setValue($value);

            // Сохраняем только если есть значение или свойство обязательно
            if (!empty($value) || $property->is_required) {
                $propertyValue->save();
            } else {
                // Удаляем пустое значение, если свойство не обязательно
                if (!$propertyValue->isNewRecord) {
                    $propertyValue->delete();
                }
            }
        }
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        $model->status = 0;
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Товар удален');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при удаление товара');
        }

        return $this->redirect(['index']);
    }
    
    public function actionUpdateStock($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $quantity = Yii::$app->request->post('Product')['quantity'] ?? 0;
            $comment = Yii::$app->request->post('comment');

            try {
                $model->addToStock($quantity, Yii::$app->user->id, $comment);
                Yii::$app->session->setFlash('success', 'Stock updated successfully');
                return $this->redirect(['view', 'url' => $model->url]);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('update-stock', [
            'model' => $model,
        ]);
    }
    
    public function actionStockMovements($id)
    {
        $model = $this->findModel($id);

        return $this->render('stock-movements', [
            'model' => $model,
        ]);
    }

    public function actionGetColorsByCategory($category_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $category = ProductCategory::findOne($category_id);
        if (!$category) {
            return ['success' => false, 'message' => 'Category not found'];
        }
        
        $colors = $category->getCachedAvailableColors();
        $colorsData = [];
        
        foreach ($colors as $color) {
            $colorsData[] = [
                'id' => $color->id,
                'name' => $color->name,
                'css_color' => $color->css_color
            ];
        }
        
        if (empty($colorsData)) {
            return [
                'success' => true,
                'colors' => [],
                'message' => 'No colors available for this category'
            ];
        }
        
        return [
            'success' => true,
            'colors' => $colorsData
        ];
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    protected function findModelByUrl($url)
    {
        if (($model = Product::findOne(['url' => $url])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
