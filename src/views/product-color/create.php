<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\shop\models\ProductColor */

$this->title = Module::t('Create Product Color');
$this->params['breadcrumbs'][] = ['label' => Module::t('Product Colors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// SVG иконки
$svgIcons = [
    'plus' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M8 3V13M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>',
    'arrow-left' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>'
];
?>
<div class="product-color-create">

    <h1><?= Html::encode($this->title) ?></h1>
    

                    <p>
                        <?= Html::a(
                            '<span class="custom-icon">' . $svgIcons['arrow-left'] . '</span> ' . Module::t('Back to List'),
                            ['index'],
                            ['class' => 'btn btn-default btn-sm']
                        ) ?>
                    </p>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8">
                            <?= $this->render('_form', [
                                'model' => $model,
                            ]) ?>
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
// JavaScript для live preview цвета
$this->registerJs(<<<JS
$(document).ready(function() {
    // Функция для обновления preview
    function updateColorPreview() {
        var color = $('#productcolor-css_color').val();
        if (!color) color = '#ffffff';
        
        // Проверяем формат HEX
        var hexRegex = /^#([A-Fa-f0-9]{6})$/;
        if (!hexRegex.test(color)) {
            color = '#ffffff';
        }
        
        // Обновляем preview
        $('.preview-box').css('background-color', color);
        $('#previewHex').text(color);
        
        // Вычисляем контрастный цвет для текста
        var textColor = getContrastColor(color);
        $('.preview-text').css('color', textColor);
        $('#previewTextColor').text(textColor);
    }
    
    // Функция для вычисления контрастного цвета
    function getContrastColor(hexColor) {
        // Убираем # если есть
        hexColor = hexColor.replace('#', '');
        
        // Конвертируем HEX в RGB
        var r = parseInt(hexColor.substr(0, 2), 16);
        var g = parseInt(hexColor.substr(2, 2), 16);
        var b = parseInt(hexColor.substr(4, 2), 16);
        
        // Вычисляем luminance
        var luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
        
        // Возвращаем черный или белый в зависимости от luminance
        return luminance > 0.5 ? '#000000' : '#ffffff';
    }
    
    // Обновляем preview при изменении цвета
    $('#productcolor-css_color').on('input', function() {
        updateColorPreview();
    });
    
    // Инициализируем preview
    updateColorPreview();
    
    // Валидация HEX формата
    $('#productcolor-css_color').on('blur', function() {
        var value = $(this).val();
        var hexRegex = /^#([A-Fa-f0-9]{6})$/;
        
        if (value && !hexRegex.test(value)) {
            alert('Пожалуйста, введите цвет в формате HEX: #RRGGBB');
            $(this).val('').focus();
        }
    });
    
    // Автоматическое добавление # если забыли
    $('#productcolor-css_color').on('focus', function() {
        var value = $(this).val();
        if (value && !value.startsWith('#')) {
            $(this).val('#' + value);
        }
    });
});
JS
);
?>