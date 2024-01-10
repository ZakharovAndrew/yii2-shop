<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use ZakharovAndrew\shop\models\ProductCategory;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var app\models\ProductCategory $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('Catalog'), 'url' => ['/shop/catalog/index']];

// collecting a list of categories
foreach (ProductCategory::getCategories($model->id) as $category) {
    // exclude the current category
    if ($category->id !== $model->id) {
        $this->params['breadcrumbs'][] = ['label' => $category->title, 'url' => ['/shop/product-category/view', 'url' => $category->url]];
    }
}
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<style>
    .shop-product-img img {
        width:100%;
        border-radius: 9px 9px 0 0;
    }
    .product-price {
        font-size:24px;
        text-align: center;
    }
    .shop-product-item {
        border: 1px solid #cccccc;
        border-radius: 9px;
        box-sizing: border-box;
        margin-bottom:20px;
    }
    .product-title {
        text-align: center;
        font-size:20px;
    }
    .to-album {
        margin:10px 0;
        text-align: center;
    }
    .to-album button {
        
        border-radius: 20px;
        background-color: #FFC42E;
        border:none;
        padding:10px 25px;
    }
    .category-description {margin: 30px 0}
    
    
</style>
<div class="product-category-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php if (!Yii::$app->user->isGuest) {?>
    <p>
        <?= Html::a(Module::t('Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>
    <?php } ?>
    
    <div class="row product-list">
        <?php foreach($products as $product) {
            echo $this->render('../catalog/_product', [
                'model' => $product,
                'class' => 'col-md-4 col-12 shop-product'
            ]);                
        } ?>
    </div>
    
    <div class="category-description"><?= $model->description ?></div>
    <div class="category-description_after"><?= $model->description_after ?></div>
    

</div>
