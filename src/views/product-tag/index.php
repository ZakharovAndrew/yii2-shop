<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\shop\assets\ShopAssets;
ShopAssets::register($this);

/** @var $this yii\web\View */
/** @var $searchModel ZakharovAndrew\shop\models\ProductTagSearch */
/** @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('Product Tags');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-tag-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Module::t('Create Product Tag'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::button(Module::t('Bulk Delete'), [
            'class' => 'btn btn-danger',
            'id' => 'bulk-delete-btn',
            'style' => 'display: none;'
        ]) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'ids',
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(
                        '<span style="' . $model->getTagStyle() . '" class="badge">' . 
                        Html::encode($model->name) . '</span>',
                        ['view', 'url' => $model->url],
                        ['title' => 'View tag']
                    );
                },
            ],
            'url',

            [
                'attribute' => 'allowed_roles',
                'value' => function ($model) {
                    $allowedRoles = $model->getAllowedRolesArray();
                    if (empty($allowedRoles)) {
                        return Module::t('All roles');
                    }
                    return implode(', ', $model->getAllowedRoleNames());
                },
            ],
            'created_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['style' => 'width: 79px;'],
                'template' => '{up} {down}',
                'buttons' => [
                    'up' => function($url, $model, $key) {
                        if ($model->position > 1) {
                            return Html::a(
                                '<span class="icon-arrow-up"></span>',
                                ['move-up', 'id' => $model->id],
                                [
                                    'title' => Module::t('Move up'),
                                    'class' => 'btn btn-move-up btn-arrow',
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
                        $maxPosition = \ZakharovAndrew\shop\models\ProductTag::find()->max('position');
                        if ($model->position < $maxPosition) {
                            return Html::a(
                                '<span class="icon-arrow-down"></span>',
                                ['move-down', 'id' => $model->id],
                                [
                                    'title' => Module::t('Move down'),
                                    'class' => 'btn btn-move-down btn-arrow',
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
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            ['view', 'url' => $model->url],
                            ['title' => 'View tag page']
                        );
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
$js = <<<JS
    // Bulk delete functionality
    $(document).on('change', 'input[name="ids[]"]', function() {
        var checkedCount = $('input[name="ids[]"]:checked').length;
        if (checkedCount > 0) {
            $('#bulk-delete-btn').show();
        } else {
            $('#bulk-delete-btn').hide();
        }
    });

    $('#bulk-delete-btn').on('click', function() {
        var ids = $('input[name="ids[]"]:checked').map(function() {
            return $(this).val();
        }).get();

        if (ids.length > 0 && confirm('Are you sure you want to delete selected tags?')) {
            $.post('bulk-delete', {ids: ids}, function() {
                $.pjax.reload({container: '#p0'});
            });
        }
    });
JS;
$this->registerJs($js);
?>