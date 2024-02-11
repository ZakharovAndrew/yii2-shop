<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var app\models\ProductCategory $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerJsFile('https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js');
$script = <<< JS
   
ClassicEditor
    .create( document.querySelector( '#productcategory-description' ) )
    .catch( error => {
        console.error( error );
    } );
        
ClassicEditor
    .create( document.querySelector( '#productcategory-description_after' ) )
    .catch( error => {
        console.error( error );
    } );

JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>

<div class="product-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position')->textInput() ?>

    <?= $form->field($model, 'parent_id')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'description_after')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
