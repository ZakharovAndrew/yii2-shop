<?php

use ZakharovAndrew\shop\models\Product;
use ZakharovAndrew\shop\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Products');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .label {
        display: inline-block;
        padding: 3px 6px;
        font-size: 12px;
        font-weight: bold;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25em;
    }
    .label-success {
        background-color: #5cb85c;
    }
    .label-danger {
        background-color: #d9534f;
    }
</style>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Module::t('Add Product'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'images',
                'format' => 'raw',

                'content' => function ($model) {
                    return '<img src="'.$model->getFirstImage('mini').'">';
                }
            ],
            'name',
            [
                'attribute' => 'category_id',
                'format' => 'raw',

                'content' => function ($model) {
                    return ZakharovAndrew\shop\models\ProductCategory::getCategoriesList()[$model->category_id]['title'] ?? $model->category_id;
                }
            ],
            [
                'attribute' => 'description',
                'format' => 'raw',

                'content' => function ($model) {
                    return (mb_substr(strip_tags($model->description), 0, 50)).'...';
                }
            ],
            [
                'attribute' => 'url',
                'format' => 'raw',
                'content' => function ($model) {
                    return '<a href="'.Url::toRoute(['view', 'url' => $model->url]).'">'.$model->url.'</a>';
                }
            ],
            //'category_id',
            //'user_id',
            //'count_views',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::tag('span', $model->getStatusText(), [
                        'class' => 'label ' . $model->getStatusClass()
                    ]);
                },
                'filter' => Product::getStatuses()
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d.m.Y H:i']
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Product $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
