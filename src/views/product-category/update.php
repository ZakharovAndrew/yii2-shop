<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProductCategory $model */

$this->title = 'Update Product Category: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Product Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'url' => $model->url]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
