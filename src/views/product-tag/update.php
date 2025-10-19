<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ProductCategory $model */

$this->title = Module::t('Update Product Tag') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('Product Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'url' => $model->url]];
$this->params['breadcrumbs'][] = Module::t('Update');
?>
<div class="product-tag-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>