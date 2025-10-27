<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ShopTelegramGroups $model */
/** @var yii\data\ActiveDataProvider $shopsDataProvider */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('Telegram Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-telegram-groups-view">

    <?php if (Yii::$app->getModule('shop')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="row">
        <div class="col-md-6">
            <!-- Основная информация -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary"></i>
                        <?= Module::t('Basic Information') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            //'id',
                            'title',
                            [
                                'attribute' => 'telegram_url',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return Html::a($model->telegram_url, $model->telegram_url, [
                                        'target' => '_blank',
                                        'rel' => 'noopener noreferrer',
                                        'class' => 'text-break'
                                    ]);
                                },
                            ],
                            [
                                'attribute' => 'telegram_chat_id',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->telegram_chat_id) {
                                        return Html::encode($model->telegram_chat_id);
                                    }
                                    return Module::t('Not set');
                                },
                            ],
                            [
                                'attribute' => 'is_active',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->is_active ? 
                                        Module::t('Yes'):
                                        Module::t('No');
                                },
                            ],
                            [
                                'attribute' => 'created_at',
                                'format' => 'datetime',
                            ],
                            [
                                'attribute' => 'updated_at',
                                'format' => 'datetime',
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Информация о разрешениях -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shield-alt text-warning"></i>
                        <?= Module::t('Bot Permissions') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($model->getPermissionsArray())): ?>
                        <div class="row">
                            <?php foreach ($model->getPermissionsArray() as $key => $label): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <?php if ($model->getPermissionsArray()[$key]): ?>
                                            <i class="fas fa-check-circle text-success mr-2"></i>
                                            <span class="text-success"><?= $key ?></span>
                                        <?php else: ?>
                                            <i class="fas fa-times-circle text-secondary mr-2"></i>
                                            <span class="text-muted"><?= $key ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?= Yii::t('app', 'Permissions not set. Make sure the bot is added to the group and has admin rights.') ?>
                        </div>
                        <div class="text-center">
                            <?= Html::a('<i class="fas fa-robot"></i> ' . Yii::t('app', 'Add Bot to Group'), $model->telegram_url, [
                                'class' => 'btn btn-primary btn-sm',
                                'target' => '_blank'
                            ]) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- Связанные магазины -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card" id="shops">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-store text-success"></i>
                        <?= Yii::t('app', 'Linked Shops') ?>
                        <span class="badge badge-primary ml-2"><?= $model->getShops()->count() ?></span>
                    </h5>
                    <?= Html::a('<i class="fas fa-plus"></i> ' . Yii::t('app', 'Link Shop'), ['link-shop', 'id' => $model->id], ['class' => 'btn btn-success btn-sm']) ?>
                </div>
                <div class="card-body">
                    <?php if ($model->getShops()->count() > 0): ?>
                        <?php Pjax::begin(['id' => 'shops-pjax']); ?>
                        <?= GridView::widget([
                            'dataProvider' => $shopsDataProvider,
                            'layout' => "{items}\n{pager}",
                            'tableOptions' => ['class' => 'table table-hover'],
                            'columns' => [
                                [
                                    'attribute' => 'name',
                                    'format' => 'raw',
                                    'value' => function ($shop) {
                                        return Html::a(Html::encode($shop->name), ['/shop/shop/view', 'id' => $shop->id], [
                                            'data-pjax' => 0
                                        ]);
                                    },
                                ],
                                [
                                    'attribute' => 'url',
                                    'format' => 'raw',
                                    'value' => function ($shop) {
                                        return Html::a($shop->url, ['/shop/shop/view', 'url' => $shop->url], [
                                            'data-pjax' => 0
                                        ]);
                                    },
                                ],
                                [
                                    'attribute' => 'city',
                                    'format' => 'raw',
                                    'value' => function ($shop) {
                                        return $shop->city ? Html::encode($shop->city) : '<span class="text-muted">' . Yii::t('app', 'Not set') . '</span>';
                                    },
                                ],
                                [
                                    'attribute' => 'created_at',
                                    'format' => 'datetime',
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => Yii::t('app', 'Actions'),
                                    'headerOptions' => ['style' => 'width: 100px;'],
                                    'contentOptions' => ['class' => 'text-center'],
                                    'template' => '{unlink}',
                                    'buttons' => [
                                        'unlink' => function ($url, $shop, $key) use ($model) {
                                            return Html::a('<i class="fas fa-unlink text-danger"></i>', 
                                                ['unlink-shop', 'id' => $model->id, 'shop_id' => $shop->id], 
                                                [
                                                    'title' => Yii::t('app', 'Unlink Shop'),
                                                    'data' => [
                                                        'confirm' => Yii::t('app', 'Are you sure you want to unlink this shop?'),
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
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-store fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted"><?= Yii::t('app', 'No shops linked') ?></h5>
                            <p class="text-muted"><?= Yii::t('app', 'This Telegram group is not linked to any shops yet.') ?></p>
                            <?= Html::a('<i class="fas fa-link"></i> ' . Yii::t('app', 'Link First Shop'), ['link-shop', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
// Переключение статуса
$(document).on('click', '.toggle-status', function() {
    var \$button = $(this);
    var url = \$button.data('url');
    
    $.post(url, function(data) {
        window.location.reload();
    }).fail(function() {
        alert('Error updating status');
    });
});
JS;

$this->registerJs($js);
?>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}
.badge {
    font-size: 0.75em;
}
.d-grid gap-2 .btn {
    text-align: left;
    padding: 0.75rem 1rem;
}
.table td {
    vertical-align: middle;
}
</style>