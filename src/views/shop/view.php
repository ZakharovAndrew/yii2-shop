<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\Shop $model */

$this->title = $model->name;

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="shop-description"><?= $model->description ?></div>
    
    <?= $this->render('../catalog/_product_list', [
        'products' => $products,
        'pagination' => $pagination
    ]) ?>
    
    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->hasRole('admin')) {?>
    <p>
        <?= Html::a(Module::t('Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>
    <?php } ?>
</div>
