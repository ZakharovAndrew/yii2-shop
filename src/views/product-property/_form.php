<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\models\ProductProperty;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\shop\models\ProductProperty */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-property-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'type')->dropDownList(
                ProductProperty::getTypesList(),
                ['prompt' => Module::t('Select Type')]
            ) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'sort_order')->textInput(['type' => 'number']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'is_required')->checkbox() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'is_active')->checkbox() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Module::t('Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>