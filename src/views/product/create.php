<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var app\models\Product $model */

$this->title = Module::t('Create Product');
$this->params['breadcrumbs'][] = ['label' => Module::t('Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'action' => 'create',
        'shop_id' => $shop_id
    ]) ?>

</div>
