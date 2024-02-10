<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use ZakharovAndrew\shop\models\ProductCategory;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var app\models\Product $model */



$this->title = $model->title;
$categories = ProductCategory::getCategories($model->category_id);
foreach ($categories as $category) {
    $this->params['breadcrumbs'][] = ['label' => $category->title, 'url' => ['/shop/product-category/view', 'url' => $category->url]];
}
$last_category = end($categories);
//SEO
$this->registerMetaTag(['name' => 'description', 'content' => $last_category->title . ' '. $model->title]);
$this->registerMetaTag(['name' => 'og:image', 'content' => $model->getFirstImage('big')]);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $model->getFirstImage('big')]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary_large_image']);

//$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<style>
    .product-view .product-price {font-size:32px;}
</style>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php if (!Yii::$app->user->isGuest) {?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php } ?>
    
    <div class="row">
        
    
        <div class="col-12 col-md-6">
            <img src="<?= $model->getFirstImage('big') ?>" class="img-fluid">
        </div>
        <div class="col-12 col-md-6">
            <div class="product-price"><?= number_format($model->cost, 0, '', ' ' ) ?> ₽</div>
            <p><?= Module::t('Category')?>: <?= $last_category->title ?></p>
            <?= $model->description ?>
        </div>
    </div>
    
    

    <?php /*DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'description:ntext',
            'url:url',
            'images',
            'category_id',
            'user_id',
            'count_views',
            'created_at',
        ],
    ])*/ ?>

</div>
