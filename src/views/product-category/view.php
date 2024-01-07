<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\ProductCategory $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Catalog', 'url' => ['catalog']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <div class="category-description"><?= $model->description ?></div>
    <div class="category-description_after"><?= $model->description_after ?></div>
    

</div>
