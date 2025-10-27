<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ShopTelegramGroupsSearch $model */
/** @var ActiveForm $form */
?>

<div class="shop-telegram-groups-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'id') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'title') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'telegram_url') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'telegram_chat_id') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'is_active')->dropDownList([
                '' => Yii::t('app', 'All'),
                1 => Yii::t('app', 'Active'),
                0 => Yii::t('app', 'Inactive')
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'shop_count') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'linked_shops') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'created_at') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-search"></i> ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-times"></i> ' . Yii::t('app', 'Reset'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>