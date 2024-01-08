<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use ZakharovAndrew\shop\models\ProductCategory;

/** @var yii\web\View $this */
/** @var app\models\Product $model */

$this->title = $model->title;
foreach (ProductCategory::getCategories($model->category_id) as $category) {
    $this->params['breadcrumbs'][] = ['label' => $category->title, 'url' => ['/shop/product-category/view', 'url' => $category->url]];
}
$last_category = end(ProductCategory::getCategories($model->category_id));

//$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
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
            <img src="<?= $model->images ?>" class="img-fluid">
        </div>
        <div class="col-12 col-md-6">
            <p>Категория: <?= $last_category->title  ?></p>
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
