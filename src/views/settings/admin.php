<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use ZakharovAndrew\shop\models\Settings;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\SettingsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var ZakharovAndrew\shop\models\Settings $model */

$this->title = 'Settings Admin Panel';
$this->params['breadcrumbs'][] = $this->title;

$shopSettings = Yii::$app->shopSettings;
?>
<div class="settings-admin">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Admin Panel:</strong> Full settings management. Use with caution.
                <?= Html::a('Go to Quick Settings', ['index'], ['class' => 'alert-link float-right']) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Create New Setting Form -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle"></i> Create New Setting
                    </h5>
                </div>
                <div class="card-body">
                    <?php $form = \yii\widgets\ActiveForm::begin([
                        'action' => ['admin'],
                        'method' => 'post',
                    ]); ?>

                    <?= $form->field($model, 'key')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'e.g., siteName, maxItems, etc.'
                    ]) ?>

                    <?= $form->field($model, 'name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Human readable name'
                    ]) ?>

                    <?= $form->field($model, 'type')->dropDownList([
                        Settings::TYPE_STRING => 'String',
                        Settings::TYPE_INTEGER => 'Integer',
                        Settings::TYPE_BOOLEAN => 'Boolean',
                        Settings::TYPE_JSON => 'JSON',
                    ], ['prompt' => 'Select type...']) ?>

                    <?= $form->field($model, 'value')->textarea([
                        'rows' => 3,
                        'placeholder' => 'Enter setting value...'
                    ]) ?>

                    <div class="form-group">
                        <?= Html::submitButton('<i class="fas fa-save"></i> Create Setting', [
                            'class' => 'btn btn-success'
                        ]) ?>
                        <?= Html::resetButton('<i class="fas fa-undo"></i> Reset', [
                            'class' => 'btn btn-outline-secondary'
                        ]) ?>
                    </div>

                    <?php \yii\widgets\ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <!-- Settings List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list"></i> All Settings
                    </h5>
                </div>
                <div class="card-body">
                    <?php Pjax::begin(); ?>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            [
                                'attribute' => 'key',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return Html::a(Html::encode($model->key), ['view', 'id' => $model->id], [
                                        'data-pjax' => 0,
                                    ]);
                                },
                            ],
                            [
                                'attribute' => 'name',
                                'value' => function ($model) {
                                    return $model->getDisplayName();
                                },
                            ],
                            [
                                'attribute' => 'value',
                                'format' => 'raw',
                                'value' => function ($model) use ($shopSettings) {
                                    $value = $shopSettings->get($model->key);
                                    
                                    if ($model->type === Settings::TYPE_BOOLEAN) {
                                        return $value ? 
                                            '<span class="badge badge-success">Yes</span>' : 
                                            '<span class="badge badge-secondary">No</span>';
                                    } elseif ($model->type === Settings::TYPE_JSON) {
                                        if (is_array($value)) {
                                            return '<code>' . Html::encode(json_encode($value)) . '</code>';
                                        }
                                        return '<code>' . Html::encode($value) . '</code>';
                                    } else {
                                        return Html::encode($value);
                                    }
                                },
                            ],
                            [
                                'attribute' => 'type',
                                'filter' => [
                                    Settings::TYPE_STRING => 'String',
                                    Settings::TYPE_INTEGER => 'Integer',
                                    Settings::TYPE_BOOLEAN => 'Boolean',
                                    Settings::TYPE_JSON => 'JSON',
                                ],
                                'value' => function ($model) {
                                    return ucfirst($model->type);
                                },
                            ],
                            [
                                'attribute' => 'updated_at',
                                'format' => 'datetime',
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {update} {delete}',
                                'buttons' => [
                                    'view' => function ($url, $model) {
                                        return Html::a('<i class="fas fa-eye"></i>', $url, [
                                            'title' => 'View',
                                            'class' => 'btn btn-sm btn-outline-primary',
                                        ]);
                                    },
                                    'update' => function ($url, $model) {
                                        return Html::a('<i class="fas fa-edit"></i>', $url, [
                                            'title' => 'Update',
                                            'class' => 'btn btn-sm btn-outline-success',
                                        ]);
                                    },
                                    'delete' => function ($url, $model) {
                                        return Html::a('<i class="fas fa-trash"></i>', $url, [
                                            'title' => 'Delete',
                                            'class' => 'btn btn-sm btn-outline-danger',
                                            'data' => [
                                                'confirm' => 'Are you sure you want to delete this setting?',
                                                'method' => 'post',
                                            ],
                                        ]);
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