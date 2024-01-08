<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\models\ProductCategory;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\Product $model */
/** @var yii\widgets\ActiveForm $form */

$module = Yii::$app->getModule('shop');
// current language
$appLanguage = Yii::$app->language;
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'images')->textInput(['maxlength' => true]) ?>
    
    <?php
    /* additional params */
    foreach (range(1,3) as $i) {
        if (isset($module->params[$i])) {
            echo $form->field($model, 'param'.$i)->textInput(['maxlength' => true])->label($module->params[$i]['title'][$appLanguage]);
        }
    } ?>
    <?= $form->field($model, 'category_id')->dropDownList(ProductCategory::getDropdownGroups(), ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
