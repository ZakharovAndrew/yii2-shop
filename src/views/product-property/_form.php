<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\models\ProductProperty;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\shop\models\ProductProperty */
/* @var $form yii\widgets\ActiveForm */
?>

<style>
.option-row .has-error {
    border-color: #dd4b39;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
}
.option-row .has-error:focus {
    border-color: #dd4b39;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #f8a7a7;
}
/* Стили для блока опций */
.box-info {
    border-top: 3px solid #00c0ef !important;
}

.options-container {
    min-height: 100px;
}

.option-item {
    background: #f8f9fa;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.option-item:hover {
    background: #e8f4fc;
    border-color: #b8dff3;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.option-item .input-group {
    margin-bottom: 0;
}

.option-item .input-group-addon {
    background: #fff;
    border-color: #d2d6de;
    min-width: 40px;
}

.option-item .form-control {
    border-radius: 0 4px 4px 0;
}

.option-item .btn-danger {
    border-radius: 4px;
    padding: 6px 12px;
}

.option-item .btn-danger:hover {
    background: #d73925;
    border-color: #d73925;
}

.empty-options {
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 30px !important;
}

.option-value.has-error,
.option-sort.has-error {
    border-color: #dd4b39;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
}

.option-value.has-error:focus,
.option-sort.has-error:focus {
    border-color: #dd4b39;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #f8a7a7;
}

/* Анимация при добавлении/удалении опций */
.option-item {
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.option-item.removing {
    animation: fadeOut 0.3s ease;
}

@keyframes fadeOut {
    from { opacity: 1; transform: translateY(0); }
    to { opacity: 0; transform: translateY(-10px); }
}

/* Адаптивность */
@media (max-width: 768px) {
    .option-item .col-md-6,
    .option-item .col-md-3 {
        margin-bottom: 10px;
    }
    
    .option-item .btn-danger {
        width: 100%;
    }
}
</style>

<div class="product-property-form">
    
    <?php $form = ActiveForm::begin(); ?>
    <div class="card">
        <div class="card-body">
            
        
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
                    [
                        'prompt' => Module::t('Select Type'),
                        'id' => 'property-type'
                    ]
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

        <!-- Блок для вариантов выпадающего списка -->
        <div id="select-options-block" style="display: none;">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-list"></i> <?= Module::t('Dropdown Options') ?></h3>
                            <div class="box-tools">
                                <button type="button" class="btn btn-sm btn-success" id="add-option-btn">
                                    <i class="fa fa-plus"></i> <?= Module::t('Add Option') ?>
                                </button>
                            </div>
                        </div>
                        <div class="box-body" style="padding: 20px;">
                            <div id="options-container" class="options-container">
                                <?php if (!$model->isNewRecord && $model->isSelectType()): ?>
                                    <?php foreach ($model->options as $index => $option): ?>
                                        <div class="option-row form-group option-item">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-arrows-v"></i>
                                                        </span>
                                                        <input type="text" 
                                                               name="options[<?= $option->id ?>][value]" 
                                                               class="form-control option-value" 
                                                               value="<?= Html::encode($option->value) ?>" 
                                                               placeholder="<?= Module::t('Option value') ?>"
                                                               required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-sort-numeric-asc"></i>
                                                        </span>
                                                        <input type="number" 
                                                               name="options[<?= $option->id ?>][sort_order]" 
                                                               class="form-control option-sort" 
                                                               value="<?= $option->sort_order ?>" 
                                                               placeholder="<?= Module::t('Sort order') ?>"
                                                               min="0">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="button" class="btn btn-danger remove-option">
                                                        <i class="fa fa-trash"></i> <?= Module::t('Remove') ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-options text-muted" style="padding: 15px; text-align: center;">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none"  style="margin-bottom:10px" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="9" stroke="#6c757d" stroke-width="2"/>
                                            <path d="M12 8H12.01M11 12H12V16H13" stroke="#6c757d" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <br>
                                        <?= Module::t('No options added yet. Click "Add Option" to create new options.') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="box-footer" style="background: #f9f9f9;">
                            <small class="text-muted">
                                <i class="fa fa-lightbulb-o"></i> 
                                <?= Module::t('Add options for dropdown list. Each option should have a unique value.') ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        </div> <!-- end card-body -->
    </div>
    <div class="form-group" style="margin-top:15px">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Module::t('Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
// Шаблон для новой опции
$optionTemplate = '
<div class="option-row form-group option-item">
    <div class="row">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-arrows-v"></i>
                </span>
                <input type="text" name="options[new][__index__][value]" class="form-control option-value" placeholder="' . Module::t('Option value') . '" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-addon">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M4 6L8 2L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <path d="M4 10L8 14L12 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>
                </span>
                <input type="number" name="options[new][__index__][sort_order]" class="form-control option-sort" value="0" placeholder="' . Module::t('Sort order') . '" min="0">
            </div>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-danger remove-option">
                <i class="fa fa-trash"></i> ' . Module::t('Remove') . '
            </button>
        </div>
    </div>
</div>';

$deleteConfirm = Module::t('Are you sure you want to delete this option?');
$addOptionError = Module::t('Please add at least one option for dropdown list.');
$typeSelect = ProductProperty::TYPE_SELECT;
$uniqueError = Module::t('Option values must be unique.');
$clickAdd = Module::t('No options added yet. Click "Add Option" to create new options.');

$this->registerJs(<<<JS
    // Функция для показа/скрытия блока опций
    function toggleOptionsBlock() {
        var type = $('#property-type').val();
        if (type == '$typeSelect') {
            $('#select-options-block').slideDown(300);
        } else {
            $('#select-options-block').slideUp(300);
        }
    }

    // Инициализация при загрузке
    $(document).ready(function() {
        toggleOptionsBlock();
        
        // Обработчик изменения типа
        $('#property-type').change(function() {
            toggleOptionsBlock();
        });
        
        // Счетчик для новых опций
        var newOptionIndex = 0;
        
        // Добавление новой опции
        $('#add-option-btn').click(function() {
            // Скрываем сообщение о пустых опциях
            $('.empty-options').remove();
            
            var template = `$optionTemplate`.replace(/__index__/g, newOptionIndex);
            var \$newOption = $(template).hide();
            $('#options-container').append(\$newOption);
            \$newOption.slideDown(200);
            newOptionIndex++;
            
            // Фокус на новое поле
            \$newOption.find('.option-value').focus();
        });
        
        // Удаление опции
        $(document).on('click', '.remove-option', function() {
            if (confirm('$deleteConfirm')) {
                var \$optionRow = $(this).closest('.option-item');
                \$optionRow.addClass('removing');
                
                setTimeout(function() {
                    \$optionRow.slideUp(300, function() {
                        $(this).remove();
                        
                        // Если опций не осталось, показываем сообщение
                        if ($('#options-container .option-item').length === 0) {
                            $('#options-container').html(`<div class="empty-options text-muted" style="padding: 15px; text-align: center;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" style="margin-bottom:10px" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="9" stroke="#6c757d" stroke-width="2"/>
                                    <path d="M12 8H12.01M11 12H12V16H13" stroke="#6c757d" stroke-width="2" stroke-linecap="round"/>
                                </svg><br>$clickAdd</div>`);
                        }
                    });
                }, 300);
            }
        });

        // Валидация: проверка, что для выпадающего списка есть хотя бы одна опция
        // Валидация: проверка, что для выпадающего списка есть хотя бы одна опция
        $('form').on('submit', function(e) {
            var type = $('#property-type').val();
            if (type =='$typeSelect') {
                var optionCount = $('#options-container .option-row').length;
                if (optionCount === 0) {
                    alert('$addOptionError');
                    e.preventDefault();
                    return false;
                }
                
                // Проверяем уникальность значений опций
                var values = [];
                var hasDuplicates = false;
                
                $('#options-container input[name*="[value]"]').each(function() {
                    var value = $(this).val().trim();
                    if (value && values.includes(value)) {
                        hasDuplicates = true;
                        $(this).addClass('has-error');
                    } else {
                        values.push(value);
                        $(this).removeClass('has-error');
                    }
                });
                
                if (hasDuplicates) {
                    alert('$uniqueError');
                    e.preventDefault();
                    return false;
                }
            }
        });
    });
JS
);
?>