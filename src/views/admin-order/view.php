<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use ZakharovAndrew\shop\models\Order;

/* @var $this yii\web\View */
/* @var $order ZakharovAndrew\shop\models\Order */

$this->title = 'Заказ #' . $order->id;
$this->params['breadcrumbs'][] = ['label' => 'Управление заказами', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Информация о заказе</h3>
                </div>
                <div class="panel-body">
                    <?= DetailView::widget([
                        'model' => $order,
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'status',
                                'value' => function($model) {
                                    return Html::tag('span', $model->getStatusText(), [
                                        'class' => 'label ' . $model->getStatusClass()
                                    ]);
                                },
                                'format' => 'raw'
                            ],
                            //'total_sum:currency',
                            'created_at:datetime',
                            'updated_at:datetime',
                        ],
                    ]) ?>

                    <div class="form-group">
                        <?= Html::beginForm(['update-status', 'id' => $order->id], 'post') ?>
                        <?= Html::dropDownList('status', $order->status, Order::getStatuses(), [
                            'class' => 'form-control form-select',
                            'prompt' => 'Выберите новый статус'
                        ]) ?>
                        <br>
                        <?= Html::submitButton('Обновить статус', ['class' => 'btn btn-primary']) ?>
                        <?= Html::endForm() ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Данные покупателя</h3>
                </div>
                <div class="panel-body">
                    <?= DetailView::widget([
                        'model' => $order,
                        'attributes' => [
                            'first_name',
                            'last_name',
                            'phone',
                            'email',
                            [
                                'attribute' => 'delivery_method',
                                'value' => $order->getDeliveryMethodText()
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
                'dataProvider' => new \yii\data\ActiveDataProvider([
                    'query' => $order->getOrderItems(),
                    'pagination' => false,
                ]),
                'columns' => [
                    [
                        'attribute' => 'product.name',
                        'label' => 'Товар',
                        'format' => 'raw',
                        'value' => function($model) {
                            return Html::a($model->product->name, ['/shop/product/view', 'url' => $model->product->url]);
                        },
                    ],
                    'price:currency',
                    'quantity',
                    [
                        'label' => 'Сумма',
                        'value' => function($model) {
                            return Yii::$app->formatter->asCurrency($model->price * $model->quantity);
                        }
                    ],
                ],
                'summary' => '',
            ]) ?>
            <div class="text-right total-sum">
                <strong>Стоимость доставки: <?= Yii::$app->formatter->asCurrency($order->delivery_cost) ?></strong><br>
                <strong>Итого: <?= Yii::$app->formatter->asCurrency($order->total_sum) ?></strong>
            </div>
        </div>
    </div>
</div>