<?php

use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\shop\assets\ShopAssets;
ShopAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ProductTag $model */

$this->title = !empty($model->meta_title) ? $model->meta_title : $model->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('Catalog'), 'url' => ['/shop/catalog/index']];
?>

<div class="product-tag-view">

    <?php if (Yii::$app->getModule('shop')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>
    
    <?= $this->render('../catalog/_product_list', [
        'products' => $products,
        'pagination' => $pagination,
        'class' => (Yii::$app->shopSettings->get('mobileProductsPerRow') == 2 ? 'col-md-4 col-6 shop-product' : 'col-md-4 col-12 shop-product')
    ]) ?>
</div>