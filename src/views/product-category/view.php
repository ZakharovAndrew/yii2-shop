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
<div class="product-category-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="category-description"><?= $model->description ?></div>
    
    <?= $this->render('../catalog/_product_list', [
        'products' => $products
    ]) ?>
    
    <div class="category-description_after"><?= $model->description_after ?></div>
    
    <?php if (!Yii::$app->user->isGuest) {?>
    <p>
        <?= Html::a(Module::t('Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>
    <?php } ?>
</div>
