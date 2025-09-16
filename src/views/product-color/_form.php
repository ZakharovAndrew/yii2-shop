<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\shop\models\ProductColor */
/* @var $form yii\widgets\ActiveForm */

?>

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
</div>

    <?php ActiveForm::end(); ?>



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