<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ShopTelegramGroups $model */

$this->title = Module::t('Create Telegram Group');
$this->params['breadcrumbs'][] = ['label' => Module::t('Telegram Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-telegram-groups-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
