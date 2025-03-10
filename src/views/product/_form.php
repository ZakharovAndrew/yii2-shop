<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\models\ProductCategory;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\imageupload\ImageUploadWidget;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\Product $model */
/** @var yii\widgets\ActiveForm $form */

$module = Yii::$app->getModule('shop');
// current language
$appLanguage = Yii::$app->language;
$this->registerJsFile('https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js');
$script = <<< JS
   
ClassicEditor
    .create( document.querySelector( '#product-description' ) )
    .catch( error => {
        console.error( error );
    } );

JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'images')->widget(ImageUploadWidget::class, ['url' => '123', 'id'=> 'product-images', 'form' => $form]); ?>
    
    <?php
    /* additional params */
    foreach (range(1,3) as $i) {
        if (isset($module->params[$i])) {
            echo $form->field($model, 'param'.$i)->textInput(['maxlength' => true])->label($module->params[$i]['title'][$appLanguage]);
        }
    } ?>
    <?= $form->field($model, 'category_id')->dropDownList(ProductCategory::getDropdownGroups(), ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
    <?= ImageUploadWidget::afterForm() ?>

</div>
