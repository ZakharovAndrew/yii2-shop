<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use ZakharovAndrew\shop\models\ProductProperty;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\shop\assets\ShopAssets;
ShopAssets::register($this);


/* @var $this yii\web\View */
/* @var $searchModel ZakharovAndrew\shop\models\ProductPropertySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('Product Properties');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.btn-default {
    background: #f8f9fa;
    border-color: #ddd;
}

.btn-default:hover {
    background: #e9ecef;
    border-color: #ccc;
}
</style>
<div class="product-property-index">
    
    <?php if (Yii::$app->getModule('shop')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>
    
    <p>
        <?= Html::a(Module::t('Create Property'), ['create'], ['class' => 'btn btn-success btn-sm']) ?>
    </p>


    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'code',
            [
                'attribute' => 'type',
                'value' => function($model) {
                    return $model->getTypeName();
                },
                'filter' => ProductProperty::getTypesList(),
            ],
            [
                'attribute' => 'is_required',
                'format' => 'boolean',
                'filter' => [0 => Module::t('No'), 1 => Module::t('Yes')],
            ],
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'value' => function($model) {
                    $icon = $model->is_active ? 'check text-success' : 'times text-danger';
                    $label = $model->is_active ? Module::t('Yes') : Module::t('No');
                    return '<i class="fa fa-'.$icon.'"></i> ' . $label;
                },
                'filter' => [0 => Module::t('No'), 1 => Module::t('Yes')],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{up} {down}',
                'buttons' => [
                    'up' => function($url, $model, $key) {
                        if ($model->position > 1) {
                            return Html::a(
                                '<span class="icon-arrow-up"></span>',
                                ['move-up', 'id' => $model->id],
                                [
                                    'title' => Module::t('Move up'),
                                    'class' => 'btn btn-move-down btn-default',
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
                        $maxPosition = \ZakharovAndrew\shop\models\ProductProperty::find()->max('position');
                        if ($model->position < $maxPosition) {
                            return Html::a(
                                '<span class="icon-arrow-down"></span>',
                                ['move-down', 'id' => $model->id],
                                [
                                    'title' => Module::t('Move down'),
                                    'class' => 'btn btn-move-down btn-default',
                                    'data' => [
                                        'method' => 'post',
                                        'pjax' => 1,
                                    ],
                                ]
                            );
                        }
                        return '';
                    },
                ],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {options} {toggle} {delete}',
                'buttons' => [
                    'options' => function($url, $model) {
                        if ($model->isSelectType()) {
                            return Html::a(
                                '<i class="fa fa-list"></i>',
                                ['options', 'id' => $model->id],
                                [
                                    'title' => Module::t('Manage Options'),
                                    'class' => 'btn btn-xs btn-info'
                                ]
                            );
                        }
                        return '';
                    },
                    'toggle' => function($url, $model) {
                        return Html::a(
                            '<i class="fa fa-toggle-' . ($model->is_active ? 'on' : 'off') . '"></i>',
                            ['toggle-status', 'id' => $model->id],
                            [
                                'title' => Module::t('Toggle Status'),
                                'class' => 'btn btn-xs btn-warning',
                                'data' => [
                                    'confirm' => Module::t('Are you sure you want to change the status?'),
                                    'method' => 'post',
                                ],
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>


</div>