<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\Shop $model */

$this->title = Module::t('Create Store');
$this->params['breadcrumbs'][] = ['label' => Module::t('Stores '), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>