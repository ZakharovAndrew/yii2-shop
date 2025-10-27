<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\shop\models\ShopTelegramGroups;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ShopTelegramGroups $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="shop-telegram-groups">

    <?php $form = ActiveForm::begin(); ?>
            
    <?= $form->field($model, 'telegram_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_active')->dropDownList(
        [1 => Module::t('Yes'), 0 => Module::t('No')], 
        ['prompt' => '', 'class' => 'form-control form-select']
    ) ?>
       

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>