<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\SettingsSearch $model */
/** @var ActiveForm $form */
?>

<div class="settings-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'key') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'name') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'type')->dropDownList([
                '' => 'All Types',
                'string' => 'String',
                'integer' => 'Integer', 
                'boolean' => 'Boolean',
                'json' => 'JSON',
            ]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'value') ?>
        </div>
        <div class="col-md-2">
            <div class="form-group" style="margin-top: 30px">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>