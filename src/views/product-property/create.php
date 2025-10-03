<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\shop\models\ProductProperty */

$this->title = Module::t('Create Product Property');
$this->params['breadcrumbs'][] = ['label' => Module::t('Product Properties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-property-create">

    <?php if (Yii::$app->getModule('shop')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>