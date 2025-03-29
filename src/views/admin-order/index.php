<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Управление заказами';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute' => 'user_id',
                'value' => function($model) {
                    return $model->user ? $model->user->username : 'Гость';
                }
            ],
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->getStatusText();
                },
                'contentOptions' => function($model) {
                    return ['class' => $model->getStatusClass()];
                }
            ],
            'total_sum:currency',
            'created_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update-status}',
                'buttons' => [
                    'update-status' => function($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-refresh"></span>', 
                            ['update-status', 'id' => $model->id],
                            ['title' => 'Изменить статус']
                        );
                    }
                ]
            ],
        ],
    ]); ?>
</div>