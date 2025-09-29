<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\models\ProductCategory;
use ZakharovAndrew\shop\models\Shop;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\imageupload\ImageUploadWidget;
use ZakharovAndrew\shop\models\ProductProperty;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\Product $model */
/** @var yii\widgets\ActiveForm $form */

$module = Yii::$app->getModule('shop');
// current language
$appLanguage = Yii::$app->language;
$this->registerJsFile('https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js');

// Получаем доступные цвета для выбранной категории
$availableColors = [];
if ($model->category_id) {
    $category = ProductCategory::findOne($model->category_id);
    if ($category) {
        $availableColors = $category->getCachedAvailableColors();
    }
}

// All colors
$allColors = \ZakharovAndrew\shop\models\ProductColor::find()
    ->where(['is_active' => true])
    ->orderBy(['position' => SORT_ASC])
    ->all();

$script = <<< JS
   
ClassicEditor
    .create( document.querySelector( '#product-description' ) )
    .catch( error => {
        console.error( error );
    } );

// Функция для обновления доступных цветов при изменении категории
function updateAvailableColors(categoryId) {
    $.get('/shop/product/get-colors-by-category', {category_id: categoryId}, function(data) {
        var colorsContainer = $('#color-selection-container');
        colorsContainer.empty();
        
        if (data.colors && data.colors.length > 0) {
            $.each(data.colors, function(index, color) {
                var isSelected = ($('#product-color_id').val() == color.id);
                var colorHtml = '<div class="color-option' + (isSelected ? ' selected' : '') + '" data-color-id="' + color.id + '" style="background-color: ' + color.css_color + '; ' + (isSelected ? 'box-shadow: 0 0 0 1px '+color.css_color : '') + '" title="' + color.name + '"></div>';
                colorsContainer.append(colorHtml);
            });
            $('#color-selection-container').show();
        } else {
            colorsContainer.html('<div class="text-muted">' + data.message + '</div>');
            $('#color-selection-container').show();
        }
    }).fail(function() {
        $('#color-selection-container').html('<div class="text-danger">Ошибка загрузки цветов</div>').show();
    });
}

// Обработчик изменения категории
$('#product-category_id').on('change', function() {
    var categoryId = $(this).val();
    if (categoryId) {
        updateAvailableColors(categoryId);
    } else {
        $('#color-selection-container').hide();
    }
});

// Обработчик клика по цвету
$(document).on('click', '.color-option', function() {
    var colorId = $(this).data('color-id');
    $('#product-color_id').val(colorId);
    
    // Снимаем выделение со всех цветов
    $('.color-option').removeClass('selected').css('box-shadow', '0 0 0 1px #0000001a');
    
    // Выделяем выбранный цвет
    $(this).addClass('selected').css('box-shadow', '0 0 0 1px #000');
});

// init color
$(document).ready(function() {
    var initialCategoryId = $('#product-category_id').val();
    if (initialCategoryId) {
        updateAvailableColors(initialCategoryId);
    }
});

  
$("#product-url").on('change keyup', function () {
    $("#product-url").val($("#product-url").val().replaceAll(' ','-'));
});

JS;
$this->registerJs($script, yii\web\View::POS_READY);

// Получаем активные свойства товара
$properties = ProductProperty::getActiveProperties();
$propertyValues = [];
if (!$model->isNewRecord) {
    foreach ($model->propertyValues as $value) {
        $propertyValues[$value->property_id] = $value;
    }
}

?>
<style>
    .has-error .help-block {color:red}
    .product-form .card {
        padding:0;
        border:0;
        box-shadow: 0 7px 14px 0 rgba(65, 69, 88, 0.1), 0 3px 6px 0 rgba(0, 0, 0, 0.07);
        margin-bottom: 20px
    }
    .product-form .card .card-header {
        background: #f9fafd;
        line-height: 26px;
        font-size:14px;
        color:#617083;
        border:0;
    }
    .product-form .card label {
        font-size:14px;
        letter-spacing:0.266667px;
        line-height:20px;
        margin-bottom:8px;
        font-weight: 500;
    }
    .product-form .card .card-body .form-group:last-child {
        margin-bottom: 0
    }
    /* Стили для свойств товара */
    .property-field {
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #007bff;
    }
    .property-field.required {
        border-left-color: #dc3545;
    }
    .property-field .property-name {
        font-weight: 600;
        margin-bottom: 8px;
        color: #495057;
    }
    .property-field .property-type {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 10px;
    }
    .property-field .form-control {
        background: #fff;
    }
    /* Стили для выбора цвета */
    .color-selection {
        margin-top: 15px;
    }
    .color-options {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    .color-option {
        width: 40px;
        height: 40px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 4px solid #fff;
        box-shadow: 0 0 0 1px #0000001a;
    }
    .color-option:hover {
        transform: scale(1.1);
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    .color-option.selected {
        box-shadow: 0 0 0 1px #000;
        /*transform: scale(1.15);*/
    }
    .color-selection-label {
        font-weight: 500;
        margin-bottom: 8px;
        display: block;
    }
    .no-colors-message {
        color: #6c757d;
        font-style: italic;
    }
</style>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <h6 class=" card-header"><?= Module::t('Basic information') ?></h6>
                <div class="card-body">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="card">
                <h6 class=" card-header">Details</h6>
                <div class="card-body">
                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
                    
                    <?= $form->field($model, 'composition')->textarea(['rows' => 1]) ?>
                </div>
            </div>
            
            <div class="card">
                <h6 class=" card-header"><?= Module::t('Images') ?></h6>
                <div class="card-body">
                    <?= $form->field($model, 'images')->widget(ImageUploadWidget::class, ['url' => '123', 'id'=> 'product-images', 'form' => $form])->label(false); ?>
                </div>
            </div>
            
            <!-- Блок свойств товара -->
            <?php if (!empty($properties)): ?>
            <div class="card">
                <h6 class="card-header"><?= Module::t('Product Properties') ?></h6>
                <div class="card-body">
                    <?php foreach ($properties as $property): ?>
                        <?php
                        $value = $propertyValues[$property->id] ?? null;
                        $fieldName = "properties[{$property->id}]";
                        $fieldId = "property-{$property->id}";
                        ?>
                        
                        <div class="property-field <?= $property->is_required ? 'required' : '' ?>">
                            <div class="property-name">
                                <?= Html::encode($property->name) ?>
                                <?php if ($property->is_required): ?>
                                    <span class="text-danger">*</span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($property->isSelectType()): ?>
                                <!-- Выпадающий список -->
                                <?= Html::dropDownList(
                                    $fieldName,
                                    $value ? $value->option_id : null,
                                    $property->getOptionsList(),
                                    [
                                        'class' => 'form-control form-select',
                                        'id' => $fieldId,
                                        'prompt' => Module::t('Select option'),
                                        'required' => $property->is_required
                                    ]
                                ) ?>
                                
                            <?php elseif ($property->isCheckboxType()): ?>
                                <!-- Чекбокс -->
                                <?= Html::checkbox(
                                    $fieldName,
                                    $value ? $value->value_bool : false,
                                    [
                                        'id' => $fieldId,
                                        'class' => 'form-check-input',
                                        'value' => 1
                                    ]
                                ) ?>
                                <?= Html::label(Module::t('Yes'), $fieldId, ['class' => 'form-check-label']) ?>
                                
                            <?php elseif ($property->isDateType()): ?>
                                <!-- Дата -->
                                <?= Html::input(
                                    'date',
                                    $fieldName,
                                    $value ? $value->value_date : '',
                                    [
                                        'class' => 'form-control',
                                        'id' => $fieldId,
                                        'required' => $property->is_required
                                    ]
                                ) ?>
                                
                            <?php elseif ($property->isYearType()): ?>
                                <!-- Год -->
                                <?= Html::input(
                                    'number',
                                    $fieldName,
                                    $value ? $value->value_int : '',
                                    [
                                        'class' => 'form-control',
                                        'id' => $fieldId,
                                        'min' => 1900,
                                        'max' => date('Y') + 10,
                                        'step' => 1,
                                        'placeholder' => 'YYYY',
                                        'required' => $property->is_required
                                    ]
                                ) ?>
                                
                            <?php else: ?>
                                <!-- Текстовое поле -->
                                <?= Html::textInput(
                                    $fieldName,
                                    $value ? $value->value_text : '',
                                    [
                                        'class' => 'form-control',
                                        'id' => $fieldId,
                                        'placeholder' => Module::t('Enter value'),
                                        'required' => $property->is_required
                                    ]
                                ) ?>
                            <?php endif; ?>
                            
                            <?php if ($property->is_required): ?>
                                <div class="text-muted small mt-1">
                                    <i class="fa fa-exclamation-circle"></i> <?= Module::t('Required field') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            
            
            <div class="card">
                <h6 class=" card-header">Additional</h6>
                <div class="card-body">
                    <?= $form->field($model, 'weight')->textInput(['type' => 'number', 'step' => '0.01']) ?>
    
                    <?= $form->field($model, 'category_id')->dropDownList(ProductCategory::getDropdownGroups(), ['prompt' => '', 'class' => 'form-control form-select']) ?>

<!-- Поле для выбора цвета (скрытое) -->
                    <?= $form->field($model, 'color_id')->hiddenInput(['id' => 'product-color_id'])->label(false) ?>

                    <!-- Контейнер для отображения цветов -->
                    <div class="color-selection">
                        <span class="color-selection-label"><?= Module::t('Select Color') ?></span>
                        <div id="color-selection-container" class="color-options" style="display: none;">
                            <?php if (!empty($availableColors)): ?>
                                <?php foreach ($availableColors as $color): ?>
                                    <div class="color-option <?= $model->color_id == $color->id ? 'selected' : '' ?>" 
                                         data-color-id="<?= $color->id ?>" 
                                         style="background-color: <?= $color->css_color ?>;  <?= $model->color_id == $color->id ? 'box-shadow: 0 0 0 1px #000' : '' ?>"
                                         title="<?= Html::encode($color->name) ?>">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-colors-message">
                                    <?= Module::t('Select a category to see available colors') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?= $form->field($model, 'status')->dropDownList(
                        $model::getStatuses(), 
                        [
                            'prompt' => Module::t('Select status'),
                            'class' => 'form-control form-select'
                        ]
                    ) ?>
                    
                    <?php if ($module->multiStore && empty($shop_id)) {
                        echo $form->field($model, 'shop_id')->dropDownList(
                            Shop::getShopsList(), 
                            [
                                'class' => 'form-control form-select'
                            ]
                        )->label(Module::t('Store'));
                    } else if ($module->multiStore && !empty($shop_id)) {
                        echo $form->field($model, 'shop_id')->hiddenInput()->label(false);
                    } ?>
                    
                    <?= $form->field($model, 'rating')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                    
                    <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'video')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div style="position: sticky;z-index: 1015;top:80px">
                <div class="card">
                    <h6 class="card-header">Pricing</h6>
                    <div class="card-body">
                        <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="card">
                    <h6 class="card-header"><?= Module::t('Bulk pricing') ?></h6>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'bulk_price_quantity_1')->textInput([
                                    'type' => 'number',
                                    'min' => 1,
                                    'placeholder' => Module::t('Quantity threshold')
                                ])->label(Module::t('Bulk quantity 1')) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'bulk_price_1')->textInput([
                                    'type' => 'number',
                                    'min' => 0,
                                    'placeholder' => Module::t('Special price')
                                ])->label(Module::t('Bulk price 1')) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'bulk_price_quantity_2')->textInput([
                                    'type' => 'number',
                                    'min' => 1,
                                    'placeholder' => Module::t('Quantity threshold')
                                ])->label(Module::t('Bulk quantity 2')) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'bulk_price_2')->textInput([
                                    'type' => 'number',
                                    'min' => 0,
                                    'placeholder' => Module::t('Special price')
                                ])->label(Module::t('Bulk price 2')) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'bulk_price_quantity_3')->textInput([
                                    'type' => 'number',
                                    'min' => 1,
                                    'placeholder' => Module::t('Quantity threshold')
                                ])->label(Module::t('Bulk quantity 3')) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'bulk_price_3')->textInput([
                                    'type' => 'number',
                                    'min' => 0,
                                    'placeholder' => Module::t('Special price')
                                ])->label(Module::t('Bulk price 3')) ?>
                            </div>
                        </div>

                        <p class="text-muted small"><?= Module::t('Applies when quantity in cart meets or exceeds this value') ?></p>
                    </div>
                </div>
            
                <div class="card">
                    <h6 class=" card-header">Stock</h6>
                    <div class="card-body">
                        <?= $form->field($model, 'quantity')->textInput([
                            'type' => 'number',
                            'min' => 0,
                            'disabled' => true,
                        ])->label(Module::t('Quantity on stock')) ?>

                        <?php if (!isset($action) || $action != 'create') {?>
                        <div class="form-group">
                            <?= Html::a(Module::t('Add to Stock'), ['update-stock', 'id' => $model->id], [
                                'class' => 'btn btn-primary',
                            ]) ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton((!isset($action) || $action != 'create') ? Module::t('Save') : Module::t('Add Product'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
    <?= ImageUploadWidget::afterForm() ?>

</div>
