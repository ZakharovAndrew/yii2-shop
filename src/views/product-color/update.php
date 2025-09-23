<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\shop\models\ProductColor */

$this->title = Module::t('Update Product Color');
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

    <?= $this->render('_form', ['model' => $model]) ?>

</div>

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