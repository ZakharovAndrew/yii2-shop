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
?>
<style>
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
.ck-editor__editable_inline {
    min-height: 220px;
}
.meta-fields-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    border-left: 4px solid #007bff;
}
.meta-fields-section h3 {
    margin-top: 0;
    color: #007bff;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 10px;
}
.og-fields-section {
    background: #f0f8ff;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    border-left: 4px solid #28a745;
}
.og-fields-section h3 {
    margin-top: 0;
    color: #28a745;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 10px;
}
.form-hint {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
    font-style: italic;
}
</style>

<div class="product-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            
            <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
            
            <?= $form->field($model, 'parent_id')->dropDownList(
                ProductCategory::getDropdownGroups($model->id), 
                ['prompt' => '', 'class' => 'form-control form-select']
            ) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'position')->textInput(['type' => 'number']) ?>
        </div>
    </div>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'description_after')->textarea(['rows' => 6]) ?>

    <!-- Секция мета-полей -->
    <div class="meta-fields-section">
        <h3><?= Module::t('SEO Settings') ?></h3>
        
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'meta_title')->textInput(['maxlength' => true])->hint(Module::t('Recommended length: 50-60 characters')) ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'meta_description')->textarea(['rows' => 4])->hint(Module::t('Recommended length: 150-160 characters')) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'meta_keywords')->textarea(['rows' => 4])->hint(Module::t('Keywords separated by commas')) ?>
            </div>
        </div>
    </div>

    <!-- Секция Open Graph полей -->
    <div class="og-fields-section">
        <h3><?= Module::t('Open Graph Settings') ?></h3>
        
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'og_title')->textInput(['maxlength' => true])->hint(Module::t('Title for social media sharing')) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'og_image')->textInput(['maxlength' => true])->hint(Module::t('URL to image for social media sharing')) ?>
            </div>
        </div>
        
        <?= $form->field($model, 'og_description')->textarea(['rows' => 4])->hint(Module::t('Description for social media sharing')) ?>
    </div>

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