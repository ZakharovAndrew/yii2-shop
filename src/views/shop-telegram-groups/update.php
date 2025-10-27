<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ShopTelegramGroups $model */

$this->title = Module::t('Update Telegram Group') . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('Telegram Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Module::t('Update');
?>
<div class="shop-telegram-groups-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
