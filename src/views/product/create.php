<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var app\models\Product $model */

$this->title = Module::t('Create Product');
$this->params['breadcrumbs'][] = ['label' => Module::t('Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->name;
?>
<div class="product-create">

    <h1><?= Html::encode($this->name) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
