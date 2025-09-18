<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\shop\models\ProductCategory;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ProductCategory $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerJsFile('https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js');
$script = <<< JS
   
ClassicEditor
    .create( document.querySelector( '#productcategory-description' ))
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

// Регистрируем CSS для отображения цветовых бейджей
$css = <<< CSS
.color-option {
    display: flex;
    align-items: center;
    padding: 5px 0;
}
.color-badge-preview {
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 3px;
    margin-right: 10px;
    border: 1px solid #ddd;
}
.checkbox-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 4px;
}
.checkbox-list label {
    display: flex;
}
CSS;
$this->registerCss($css);
?>
<style>
.ck-editor__editable_inline {
    min-height: 220px;
}
</style>

<div class="product-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position')->textInput() ?>

    <?= $form->field($model, 'parent_id')->dropDownList(ProductCategory::getDropdownGroups($model->id), ['prompt' => '', 'class' => 'form-control form-select']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'description_after')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'availableColorIds')->checkboxList(
        \ZakharovAndrew\shop\models\ProductColor::getActiveColorsList(),
        [
            'item' => function($index, $label, $name, $checked, $value) {
                // Получаем CSS цвет для этого значения
                $color = \ZakharovAndrew\shop\models\ProductColor::findOne($value);
                $cssColor = $color ? $color->css_color : '#ccc';
                
                $checkbox = Html::checkbox($name, $checked, [
                    'value' => $value,
                    'id' => 'color-' . $value,
                ]);
                
                $labelContent = Html::tag('span', '', [
                    'class' => 'color-badge-preview',
                    'style' => "background-color: {$cssColor}"
                ]) . ' ' . Html::encode($label);
                
                $labelTag = Html::label($labelContent, 'color-' . $value, [
                    'class' => 'color-option'
                ]);
                
                return Html::tag('div', $checkbox . $labelTag, [
                   'style' => 'display: flex; gap:15px;align-items: center; margin-bottom: 8px;'
                ]);
            },
            'class' => 'checkbox-list'
        ]
    )->label(Module::t('Available Colors')) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
