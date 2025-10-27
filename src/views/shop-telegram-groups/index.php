<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use ZakharovAndrew\shop\models\ShopTelegramGroups;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ShopTelegramGroupsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */

$this->title = Module::t('Telegram Groups');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-telegram-groups-index">

    <?php if (Yii::$app->getModule('shop')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="stat-card">
                                <h3 class="text-primary"><?= $stats['total'] ?></h3>
                                <small class="text-muted"><?= Module::t('Total Groups') ?></small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-card">
                                <h3 class="text-success"><?= $stats['active'] ?></h3>
                                <small class="text-muted"><?= Module::t('Active') ?></small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-card">
                                <h3 class="text-warning"><?= $stats['inactive'] ?></h3>
                                <small class="text-muted"><?= Module::t('Inactive') ?></small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-card">
                                <h3 class="text-info"><?= $stats['with_shops'] ?></h3>
                                <small class="text-muted"><?= Module::t('With Shops') ?></small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-card">
                                <h3 class="text-secondary"><?= $stats['without_shops'] ?></h3>
                                <small class="text-muted"><?= Module::t('Without Shops') ?></small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-card">
                                <h3 class="text-dark"><?= $stats['active'] ?></h3>
                                <small class="text-muted"><?= Module::t('Available') ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title mb-0"><?= Module::t('Manage Telegram Groups') ?></h4>
                </div>
                <div class="col-md-6" style="text-align:end">
                    <?= Html::a(Module::t('Create Telegram Group'), ['create'], ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Форма поиска -->
            <div class="collapse mb-4" id="searchForm">
                <div class="card card-body">
                    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
                </div>
            </div>

            <?php Pjax::begin(['id' => 'telegram-groups-pjax']); ?>

            

            <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => null, // Фильтрация через форму поиска
                'tableOptions' => ['class' => 'table table-hover'],
                'columns' => [
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width: 60px;'],
                        'contentOptions' => ['class' => 'text-дуае'],
                    ],
                    [
                        'attribute' => 'title',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(Html::encode($model->title), ['view', 'id' => $model->id], [
                                'data-pjax' => 0
                            ]);
                        },
                    ],
                    [
                        'attribute' => 'telegram_url',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a($model->telegram_url, $model->telegram_url, [
                                'target' => '_blank',
                                'rel' => 'noopener noreferrer'
                            ]);
                        },
                    ],
                    [
                        'attribute' => 'telegram_chat_id',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->telegram_chat_id ? 
                                Html::encode($model->telegram_chat_id): 
                                Module::t('Not set');
                        },
                    ],
                    [
                        'attribute' => 'shop_count',
                        'label' => Module::t('Shops'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            $count = $model->getShops()->count();
                            if ($count > 0) {
                                return Html::a($count, ['view', 'id' => $model->id, '#' => 'shops'], [
                                    'class' => 'badge badge-primary',
                                    'data-pjax' => 0,
                                    'title' => Module::t('View linked shops')
                                ]);
                            }
                            return '0';
                        },
                        'headerOptions' => ['style' => 'width: 100px;'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'is_active',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $icon = $model->is_active ? 'check text-success' : 'times text-danger';
                            $label = $model->is_active ? Module::t('Active') : Module::t('Inactive');
                            
                            return Html::button('<i class="fas fa-' . $icon . '"></i> ' . $label, [
                                'class' => 'btn btn-sm btn-outline-' . ($model->is_active ? 'success' : 'danger') . ' toggle-active',
                                'data-url' => Url::to(['toggle-active', 'id' => $model->id]),
                                'data-pjax' => 1,
                            ]);
                        },
                        'headerOptions' => ['style' => 'width: 120px;'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'headerOptions' => ['style' => 'width: 180px;'],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => Module::t('Actions'),
                        'headerOptions' => ['style' => 'width: 150px;'],
                        'contentOptions' => ['class' => 'text-center'],
                        'template' => '{view} {update} {link-shop} {delete}',
                        'buttons' => [                            
                            'link-shop' => function ($url, $model, $key) {
                                return Html::a('<i class="fas fa-link"></i>', ['link-shop', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-outline-info',
                                    'title' => Module::t('Link Shop'),
                                    'data-pjax' => 0,
                                ]);
                            },
                            'delete' => function ($url, $model, $key) {
                                $shopCount = $model->getShops()->count();
                                $options = [
                                    'title' => Module::t('Delete'),
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                ];
                                
                                if ($shopCount > 0) {
                                    $options['class'] = ' disabled';
                                    $options['title'] = Module::t('Cannot delete - group has linked shops');
                                    $options['data-toggle'] = 'tooltip';
                                    $options['onclick'] = 'return false;';
                                } else {
                                    $options['data-confirm'] = Module::t('Are you sure you want to delete this group?');
                                }
                                
                                
                                /** Defaults to FontAwesome 5 free svg icons.
                                * @see https://fontawesome.com
                                */
                                return Html::a('<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"/></svg>', $url, $options);
                            },
                        ],
                    ],
                ],
            ]); ?>
                </div>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>

<?php
$js = <<<JS
// Переключение статуса активности
$(document).on('click', '.toggle-active', function() {
    var \$button = $(this);
    var url = \$button.data('url');
    
    $.post(url, function(data) {
        $.pjax.reload({container: '#telegram-groups-pjax'});
    }).fail(function() {
        alert('Error updating status');
    });
});

JS;

$this->registerJs($js);
?>

<style>
.stat-card {
    padding: 10px;
}
.stat-card h3 {
    margin: 0;
    font-weight: bold;
}
.bulk-action.disabled {
    opacity: 0.5;
    pointer-events: none;
}
.table td {
    vertical-align: middle;
}
</style>