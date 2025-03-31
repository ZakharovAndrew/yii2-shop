<?php

use yii\helpers\Html;
use yii\grid\GridView;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $orders ZakharovAndrew\shop\models\Order[] */

$this->title = Module::t('My Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $orders,
            'sort' => [
                'attributes' => ['id', 'created_at', 'total_sum'],
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]),
        'columns' => [
            [
                'attribute' => 'id',
                'label' => 'Номер заказа',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->id, ['view', 'id' => $model->id]);
                },
            ],
            'created_at:datetime',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->getStatusText();
                },
            ],
            [
                'attribute' => 'total_sum',
                'label' => Module::t('Total'),
                'value' => function($model) {
                    return Yii::$app->formatter->asCurrency($model->total_sum);
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => 'Просмотреть',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>