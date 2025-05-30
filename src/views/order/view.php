<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $order ZakharovAndrew\shop\models\Order */
/* @var $items ZakharovAndrew\shop\models\OrderItem[] */

$this->title = Module::t('Order').' #' . $order->id;
$this->params['breadcrumbs'][] = ['label' => Module::t('My Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Module::t('Order Information') ?></h3>
                </div>
                <div class="panel-body">
                    <?= DetailView::widget([
                        'model' => $order,
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'status',
                                'value' => $order->getStatusText(),
                            ],
                            'created_at:datetime',
                            'updated_at:datetime',
                            [
                                'attribute' => 'total_sum',
                                'value' => Yii::$app->formatter->asCurrency($order->total_sum),
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Данные доставки</h3>
                </div>
                <div class="panel-body">
                    <?= DetailView::widget([
                        'model' => $order,
                        'attributes' => [
                            'first_name',
                            'last_name',
                            'phone',
                            [
                                'attribute' => 'delivery_method',
                                'value' => $order->getDeliveryMethodText(),
                            ],
                            'city',
                            'postcode',
                            'address',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Состав заказа</h3>
        </div>
        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels' => $items,
                    'pagination' => false,
                ]),
                'columns' => [
                    [
                        'attribute' => 'product.title',
                        'label' => 'Товар',
                        'format' => 'raw',
                        'value' => function($model) {
                            return Html::a($model->product->name, ['/shop/product/view', 'url' => $model->product->url]);
                        },
                    ],
                    [
                        'attribute' => 'product.price',
                        'label' => Module::t('Price'),
                        'format' => 'raw',
                        'value' => function($model) {
                            if (isset($model->price_without_discount)) {
                                return '<span style="text-decoration: line-through">'.Yii::$app->formatter->asCurrency($model->price_without_discount) . '</span> '. Yii::$app->formatter->asCurrency($model->price);
                            }
                            return Yii::$app->formatter->asCurrency($model->price);
                        },
                    ],
                    'quantity',
                    [
                        'label' => 'Сумма',
                        'value' => function($model) {
                            return Yii::$app->formatter->asCurrency($model->price * $model->quantity);
                        },
                    ],
                ],
                'summary' => '',
            ]) ?>

            <div class="text-right total-sum">
                <strong><?= Module::t('Delivery Cost')?>: <?= Yii::$app->formatter->asCurrency($order->delivery_cost) ?></strong><br>
                <strong><?= Module::t('Total') ?>: <?= Yii::$app->formatter->asCurrency($order->total_sum) ?></strong>
            </div>
        </div>
    </div>
</div>