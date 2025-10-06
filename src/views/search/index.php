<?php

use ZakharovAndrew\shop\models\Product;
use ZakharovAndrew\shop\Module;
use yii\helpers\Html;

$this->title = 'Результат поиска '.$q;
?>

<div class="catalog-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= $this->render('../catalog/_product_list', [
        'products' => $products,
        'pagination' => $pagination,
        'class' => (Yii::$app->shopSettings->get('mobileProductsPerRow') == 2 ? 'col-md-4 col-6 shop-product' : 'col-md-4 col-12 shop-product')
    ]) ?>
</div>