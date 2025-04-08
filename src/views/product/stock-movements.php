<?php

use ZakharovAndrew\shop\models\Product;
use ZakharovAndrew\shop\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\Product $model */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Stock Movements'; //Module::t('Stock Movements');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>User</th>
            <th>Quantity</th>
            <th>Comment</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($model->stockMovements as $movement): ?>
        <tr>
            <td><?= Yii::$app->formatter->asDatetime($movement->created_at) ?></td>
            <td><?= Html::encode($movement->user->username) ?></td>
            <td><?= $movement->quantity > 0 ? '+' : '' ?><?= $movement->quantity ?></td>
            <td><?= Html::encode($movement->comment) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>