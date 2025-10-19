<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\models\Roles;
use ZakharovAndrew\shop\Module;

/** @var $this yii\web\View */
/** @var $model ZakharovAndrew\shop\models\ProductTag */
/** @var $form yii\widgets\ActiveForm */
?>

<div class="product-tag-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            
            <?= $form->field($model, 'url')->textInput(['maxlength' => true])
                ->hint(Module::t('Leave empty to auto-generate from name')) ?>
            
            <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>
        </div>
        
        <div class="col-md-6">
            <?= $form->field($model, 'background_color')->textInput([
                'maxlength' => true,
                'type' => 'color'
            ]) ?>
            
            <?= $form->field($model, 'text_color')->textInput([
                'maxlength' => true,
                'type' => 'color'
            ]) ?>
            
            <?= $form->field($model, 'allowed_roles')->checkboxList(
                Roles::getRolesList(),
                [
                    'prompt' => 'Select roles...',
                    'multiple' => true,
                ]
            )->hint(Module::t('Leave empty to make tag available for all roles')) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>