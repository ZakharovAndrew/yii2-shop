<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\shop\models\ProductColor */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="product-color-form">
                    <?php $form = ActiveForm::begin([]); ?>

                    <div class="form-group">
                        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                    </div>

                    <div class="form-group">
                        <?= $form->field($model, 'code')->textInput([
                            'maxlength' => true,
                            'placeholder' => Module::t('Auto-generated from name')
                        ]) ?>
                    </div>

                    <div class="form-group">
                        <?= $form->field($model, 'css_color')->input('color') ?>
                    </div>

                    <div class="form-group">

                        <div class="checkbox" style="margin-top: 8px;">
                            <?= $form->field($model, 'is_active')->checkbox([
                                'label' => Module::t('Active color'),
                                'labelOptions' => ['style' => 'padding-left: 5px; font-weight: normal;']
                            ]) ?>
                        </div>

                    </div>

                    <div class="form-group">
                        <?= Html::submitButton( Module::t('Save'), ['class' => 'btn btn-success']) ?>
                        <?= Html::a(Module::t('Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>

                    
            </div>
            <div class="col-md-4">
                <div class="color-preview-wrapper">
                    <h4><?= Module::t('Color Preview') ?></h4>
                    <div class="color-preview" id="colorPreview">
                        <div class="preview-box" style="background-color: #ffffff;">
                            <span class="preview-text"><?= Module::t('Sample Text') ?></span>
                        </div>
                        <div class="preview-info">
                            <div class="info-row">
                                <span class="info-label"><?= Module::t('Hex:') ?></span>
                                <span class="info-value" id="previewHex">#FFFFFF</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label"><?= Module::t('Text Color:') ?></span>
                                <span class="info-value" id="previewTextColor">#000000</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<style>
.custom-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    vertical-align: middle;
}

.custom-icon svg {
    width: 1em;
    height: 1em;
}

.box-primary {
    border-top: 3px solid #3c8dbc;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.box-header {
    background: linear-gradient(135deg, #3c8dbc 0%, #367fa9 100%);
    color: white;
    border-radius: 8px 8px 0 0;
    padding: 15px 20px;
}

.box-title {
    font-weight: 600;
    margin: 0;
}

.color-preview-wrapper {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #e9ecef;
}

.color-preview {
    text-align: center;
}

.preview-box {
    width: 100%;
    height: 120px;
    border-radius: 8px;
    border: 2px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.preview-text {
    font-weight: 600;
    font-size: 16px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.preview-info {
    background: white;
    padding: 12px;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    padding: 4px 0;
}

.info-row:last-child {
    margin-bottom: 0;
}

.info-label {
    font-weight: 600;
    color: #495057;
    font-size: 12px;
}

.info-value {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 12px;
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 4px;
    border: 1px solid #e9ecef;
}


/* Адаптивность */
@media (max-width: 768px) {
    .col-md-8, .col-md-4 {
        width: 100%;
        float: none;
    }
    
    .color-preview-wrapper {
        margin-top: 20px;
    }
    
    .box-tools {
        margin-top: 10px;
        float: none !important;
        text-align: center;
    }
}
</style>


<?php
// JavaScript для автоматического генерации code из name
$this->registerJs(<<<JS
$(document).ready(function() {
    // Автоматическая генерация code из name
    $('#productcolor-name').on('blur', function() {
        var name = $(this).val();
        var codeField = $('#productcolor-code');
        
        if (name && !codeField.val()) {
            // Генерируем code из name (транслитерация + slug)
            var code = name.toLowerCase()
                .replace(/[а-яё]/g, function(ch) {
                    var charMap = {
                        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',
                        'е': 'e', 'ё': 'yo', 'ж': 'zh', 'з': 'z', 'и': 'i',
                        'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n',
                        'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't',
                        'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'ts', 'ч': 'ch',
                        'ш': 'sh', 'щ': 'sch', 'ъ': '', 'ы': 'y', 'ь': '',
                        'э': 'e', 'ю': 'yu', 'я': 'ya'
                    };
                    return charMap[ch] || ch;
                })
                .replace(/[^a-z0-9]+/g, '_')
                .replace(/^_+|_+$/g, '');
                
            codeField.val(code);
        }
    });
    
    // Автодобавление # к HEX цвету
    $('#productcolor-css_color').on('blur', function() {
        var value = $(this).val();
        if (value && !value.startsWith('#')) {
            $(this).val('#' + value);
        }
    });
    
    // Удаление # при фокусе для удобства редактирования
    $('#productcolor-css_color').on('focus', function() {
        var value = $(this).val();
        if (value && value.startsWith('#')) {
            $(this).val(value.substring(1));
        }
    });
});
JS
);