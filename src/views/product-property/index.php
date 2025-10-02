<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use ZakharovAndrew\shop\models\ProductProperty;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $searchModel ZakharovAndrew\shop\models\ProductPropertySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('Product Properties');
$this->params['breadcrumbs'][] = $this->title;
?>
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
            'sort_order',
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
                'template' => '{view} {update} {options} {toggle} {delete}',
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