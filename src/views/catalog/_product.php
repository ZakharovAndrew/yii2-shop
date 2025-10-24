<?php

use yii\helpers\Url;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\shop\models\Product */
/* @var $form yii\widgets\ActiveForm */

$show_opt_price = false;
$isFavorite = $model->isInFavorites();
?>

<div class="<?= $class ?? 'col-md-2 col-6 shop-product'?>">
    <div class="shop-product-item">
        <?php if (!empty($model->id)) {?>
        <a href="<?= Url::to(['/shop/product/view', 'url' => $model->url]) ?>">
            <div class="shop-product-img"><img src="<?= $model->getFirstImage() ?>" alt="<?= trim($model->name) ?>"></div>
        </a>
        <div class="product-title"><?= $model->name ?></div>
        <div class="product-price"><?= number_format(($model->price ?? 0), 0, '', ' ' ) ?> ₽<?= ((isset($model->price_opt) && $show_opt_price ) ? '<span class="float-right"><span>(опт)</span> '.$model->price_opt . ' ₽</span>' : '') ?></div>
        
        
            <div class="to-album add-to-cart" data-id="<?= $model->id ?>">
                <button><?= Module::t('Add to cart') ?></button>
            </div>
            <button class="favorite-btn favorite-toggle<?= $isFavorite ? ' fav-active' : '' ?>"
            data-product-id="<?= $model->id ?>"
            data-is-favorite="<?= $isFavorite ? '1' : '0' ?>" title="<?= $isFavorite ? Module::t('In Favorites') : Module::t('Add to Favorites') ?>">
                <svg class="heart-svg" viewBox="0 0 24 24">
                    <path class="heart-path" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </button>
        
        <?php } else { ?> 
        <a href="<?= Url::to(['product/create']) ?>">
            <div class="shop-product-img">
                <img src="/uploaded_files/add-tovar.jpg" width="100%">
            </div>
        </a>
        <?php } ?>
        
    </div>
</div>
