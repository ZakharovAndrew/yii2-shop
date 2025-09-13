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

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                    <div class="box-tools pull-right">
                        <?= Html::a('<i class="fa fa-plus"></i> ' . Module::t('Create Property'), ['create'], ['class' => 'btn btn-success btn-sm']) ?>
                    </div>
                </div>
                <div class="box-body">
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
            </div>
        </div>
    </div>

</div>