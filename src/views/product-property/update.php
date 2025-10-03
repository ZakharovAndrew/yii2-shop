<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\shop\models\ProductProperty */

$this->title = Module::t('Update Product Property: {name}', ['name' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Module::t('Product Properties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Module::t('Update');
?>
<div class="product-property-update">

    <?php if (Yii::$app->getModule('shop')->showTitle) {?><h3><?= Html::encode($this->title) ?></h3><?php } ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>