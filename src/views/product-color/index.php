<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $searchModel ZakharovAndrew\shop\models\ProductColorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('Product Colors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-color-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Module::t('Create Color'), ['create'], ['class' => 'btn btn-success btn-sm']) ?>
    </p>



    <div class="box-body">
        <?php Pjax::begin(['id' => 'color-pjax']); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->getColorBadge() . ' ' . Html::encode($model->name);
                    },
                ],
                'code',
                [
                    'attribute' => 'css_color',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::tag('span', $model->css_color, [
                            'style' => 'font-family: monospace;'
                        ]);
                    },
                ],
                [
                    'attribute' => 'position',
                    'contentOptions' => ['style' => 'text-align: center;'],
                ],
                [
                    'attribute' => 'is_active',
                    'format' => 'raw',
                    'value' => function($model)  {
                        $toggle = $model->is_active ? '<svg width="38" height="20" viewBox="0 0 38 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="1" y="1" width="36" height="18" rx="9" fill="#34C759" stroke="#34C759" stroke-width="2"/>
        <circle cx="26" cy="10" r="6" fill="white"/>
        <path d="M23 10L25 12L29 8" stroke="#34C759" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>' : '<svg width="38" height="20" viewBox="0 0 38 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="1" y="1" width="36" height="18" rx="9" fill="#E9E9EA" stroke="#E9E9EA" stroke-width="2"/>
        <circle cx="12" cy="10" r="6" fill="white"/>
        <path d="M9 10L11 12L15 8" stroke="#E9E9EA" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>';
                        $label = $model->is_active ? Module::t('Yes') : Module::t('No');
                        // ['toggle-status', 'id' => $model->id]
                        return Html::a(
                                '<div style="display: flex; align-items: center; gap: 8px; "><span class="iphone-toggle">' . $toggle . '</span>
                            <span style="font-size: 12px; color: #6c757d;">' . $label . '</span></div>',
                                ['toggle-status', 'id' => $model->id],
                                [
                                    'title' => Module::t('Toggle Status'),
            'class' => 'btn btn-xs btn-link toggle-btn',
            'style' => 'padding: 2px; border: none; background: transparent;text-decoration:none',
            'data' => [
                'confirm' => Module::t('Are you sure you want to change the status?'),
                'method' => 'post',
                'pjax' => 1,
            ],
                                ]
                            );
                    },
                    'filter' => [0 => Module::t('No'), 1 => Module::t('Yes')],
                    'contentOptions' => ['style' => 'text-align: center;'],
                ],
                'created_at:datetime',
                'updated_at:datetime',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{up} {down} {view} {update} {delete}',
                    'buttons' => [
                        'up' => function($url, $model, $key) {
                            if ($model->position > 1) {
                                return Html::a(
                                    '<svg width="12" height="12" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 4L4 8M8 4L12 8M8 4V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                                    ['move-up', 'id' => $model->id],
                                    [
                                        'title' => Module::t('Move up'),
                                        'class' => 'btn btn-xs btn-default',
                                        'data' => [
                                            'method' => 'post',
                                            'pjax' => 1,
                                        ],
                                    ]
                                );
                            }
                            return '';
                        },
                        'down' => function($url, $model, $key) {
                            $maxPosition = \ZakharovAndrew\shop\models\ProductColor::find()->max('position');
                            if ($model->position < $maxPosition) {
                                return Html::a(
                                    '<svg width="12" height="12" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 12L4 8M8 12L12 8M8 12V4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                                    ['move-down', 'id' => $model->id],
                                    [
                                        'title' => Module::t('Move down'),
                                        'class' => 'btn btn-xs btn-default',
                                        'data' => [
                                            'method' => 'post',
                                            'pjax' => 1,
                                        ],
                                    ]
                                );
                            }
                            return '';
                        },
                        'toggle' => function($url, $model) {
                            return Html::a(
                                
                                ['toggle-status', 'id' => $model->id],
                                [
                                    'title' => Module::t('Toggle Status'),
            'class' => 'btn btn-xs btn-link toggle-btn',
            'style' => 'padding: 2px; border: none; background: transparent;',
            'data' => [
                'confirm' => Module::t('Are you sure you want to change the status?'),
                'method' => 'post',
                'pjax' => 1,
            ],
                                ]
                            );
                        },
                    ],
                    'contentOptions' => ['style' => 'white-space: nowrap;'],
                ],
            ],
        ]); ?>

        <?php Pjax::end(); ?>
    </div>

</div>

<style>
.color-badge {
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 4px;
    margin-right: 8px;
    vertical-align: middle;
    border: 1px solid #ddd;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.btn-xs {
    padding: 4px 5px;
    font-size: 12px;
    line-height: 1;
    border-radius: 3px;
    margin: 0 2px;
    transition: all 0.2s ease;
}

.btn-xs:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}

.btn-default {
    background: #f8f9fa;
    border-color: #ddd;
}

.btn-default:hover {
    background: #e9ecef;
    border-color: #ccc;
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
}

.box-primary {
    border-top: 3px solid #3c8dbc;
    border-radius: 8px;
}

.box-header {
    background: linear-gradient(135deg, #3c8dbc 0%, #367fa9 100%);
    color: white;
    border-radius: 8px 8px 0 0;
}

.box-title {
    font-weight: 600;
}

/* Адаптивность */
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }
    
    .box-tools {
        margin-top: 10px;
        float: none !important;
        text-align: center;
    }
}

/* Стили для iPhone toggle switch */
.iphone-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.1s ease;
}

.iphone-toggle:hover {
    transform: scale(1.05);
}

.iphone-toggle-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.toggle-btn {
    transition: all 0.2s ease;
    border-radius: 6px;
}

.toggle-btn:hover {
    background: rgba(0, 0, 0, 0.05) !important;
    transform: translateY(-1px);
}

.toggle-btn:active {
    transform: translateY(0);
}

/* Анимация переключения */
@keyframes toggleOn {
    0% { transform: scale(0.95); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@keyframes toggleOff {
    0% { transform: scale(0.95); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.iphone-toggle {
    animation: toggleOff 0.2s ease;
}

.iphone-toggle:hover {
    animation: toggleOn 0.2s ease;
}

/* Адаптация размеров для таблицы */
.custom-icon.iphone-toggle-btn svg {
    width: 32px;
    height: 18px;
}

/* Для ячейки статуса */
.iphone-toggle svg {
    width: 38px;
    height: 20px;
}

/* Эффекты при наведении на всю строку */
.table tbody tr:hover .iphone-toggle {
    transform: scale(1.02);
}

.table tbody tr:hover .toggle-btn {
    background: rgba(0, 0, 0, 0.03);
}

/* Современные тени и эффекты */
.iphone-toggle svg {
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    transition: filter 0.2s ease;
}

.iphone-toggle:hover svg {
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.15));
}

/* Адаптивность для мобильных */
@media (max-width: 768px) {
    .custom-icon.iphone-toggle-btn svg {
        width: 28px;
        height: 16px;
    }
    
    .iphone-toggle svg {
        width: 32px;
        height: 18px;
    }
    
    .table tbody tr td:nth-child(5) { /* Статус */
        text-align: center !important;
    }
    
    .table tbody tr td:nth-child(5) > div {
        justify-content: center !important;
    }
}

/* Минимальная ширина для колонки действий */
.table th:last-child,
.table td:last-child {
    min-width: 180px;
}

/* Красивые границы для toggle в ячейке статуса */
.table td:nth-child(5) {
    background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
}

.table tbody tr:hover td:nth-child(5) {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}
</style>

<?php
// После стилей добавляем JavaScript
$this->registerJs(<<<JS
// Анимация переключения статуса
$(document).on('click', '.toggle-btn', function(e) {
    var \$btn = $(this);
    var \$icon = \$btn.find('.iphone-toggle-btn');
    
    // Минимальная анимация перед подтверждением
    \$icon.css('transform', 'scale(0.9)');
    setTimeout(function() {
        \$icon.css('transform', 'scale(1)');
    }, 150);
});

// Эффект при наведении на toggle
$(document).on('mouseenter', '.iphone-toggle', function() {
    $(this).css('transform', 'scale(1.05)');
}).on('mouseleave', '.iphone-toggle', function() {
    $(this).css('transform', 'scale(1)');
});

// После PJAX обновления
$(document).on('pjax:complete', function() {
    // Переинициализация эффектов
    $('.iphone-toggle').css('transform', 'scale(1)');
});
JS
, yii\web\View::POS_READY
);
?>