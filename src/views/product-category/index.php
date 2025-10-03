<?php

use ZakharovAndrew\shop\models\ProductCategory;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\shop\assets\ShopAssets;
ShopAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ProductCategorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Product Categories');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="product-category-index">

    <?php if (Yii::$app->getModule('shop')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <p>
        <?= Html::a(Module::t('Create Product Category'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'title',
            [
                'attribute' => 'url',
                'format' => 'raw',
                'content' => function ($model) {
                    return '<a href="'.Url::toRoute(['view', 'url' => $model->url]).'">'.$model->url.'</a>';
                }
            ],
            'position',
            'parent_id',
            [
                'template' => '{update} {delete}',
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, ProductCategory $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
