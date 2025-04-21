<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\models\Product;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\shop\assets\ShopAssets;
ShopAssets::register($this);

$this->title = Module::t('Cart');
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $item ZakharovAndrew\shop\models\Product */
/* @var $pagination yii\data\Pagination */
?>

<div class="cart-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (empty($cartItems)): ?>
    <div class="empty-basket text-center">
        <img src="/empty-basket.png" class="img-responsive img-fluid">
        <h3><?= Module::t('Your cart is empty') ?></h3>
        <p><?= Module::t('Add products from the store') ?></p>
    </div>
    <?php else: ?>
        <?php
        /* 
         * List of product
         */
        $sumProduct = 0;
        $totalSum = 0;
        foreach ($cartItems as $item) { ?>
        <div id="cart-row-<?= $item->product->id ?>" class="row cart-row">
            <div class="col-lg-9 col-md-9 col-9">
                <div style="display:flex">
                    <?php
                    echo '<img src="'.$item->product->getFirstImage().'" class="img-cart">';
                    ?>
                    <div class="cart-product" data-id="<?= $item->product->id ?>">
                        <div style="color:#21313c">
                            <?= $item->product->name ?>
                        </div>

                        <div>
                            <div class="count-buttons">
                                <div class="count-buttons__wrapper">
                                    <div class="count-buttons__button" onclick="shop.minusCart(<?= $item->product->id ?>, false)">-</div>
                                </div>
                                <div class="product-counter" id="product-counter-<?= $item->product->id ?>"><?= $item->quantity ?></div>
                                <div class="count-buttons__wrapper">
                                    <div class="count-buttons__button" onclick="shop.addCart(<?= $item->product->id ?>, false)">+</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="text-center col-md-3 col-3">
                <div class="cost cost-without-discount" id="product-cost-without-discount-<?= $item->product->id ?>" style="<?= $item->product->price * $item->quantity ==  $item->product->getActualPrice($item->quantity) * $item->quantity ? 'display:none' : '';?>"><?= $item->product->price * $item->quantity ?> ₽</div>
                <div class="cost" id="product-cost-<?= $item->product->id ?>" data-cost="<?= $item->product->getActualPrice($item->quantity) ?>"><?= $item->product->getActualPrice($item->quantity) * $item->quantity ?> ₽</div>
                <div onclick="shop.removeFromCart(<?= $item->product->id ?>)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                </div>
            </div>
        </div>

        <?php 
            $sumProduct += $item->quantity;
            $totalSum += $item->product->getActualPrice($item->quantity) * $item->quantity;
            ?>
        <?php } ?>
        <div class="row cart-row">
            <div class="col-lg-6 col-md-5 col-5">
                <b><?= Module::t('Total') ?></b>
            </div>
            <div class="col-lg-3 col-md-3 col-4">
                <span id="products-counter"><?= $sumProduct ?></span> шт.
            </div>
            <div class="text-center col-md-3 col-3">
                <span id="products-cost"><?= $totalSum ?></span> ₽
            </div>
        </div>
        
        <div class="cart-actions text-right">
            <?= Html::a(Module::t('Clear cart'), ['cart/clear'], [
                'class' => 'btn btn-outline-danger mr-2',
                'data-confirm' => Module::t('Are you sure you want to clear your cart?')
            ]) ?>
            <?= Html::a(Module::t('Checkout'), ['/shop/checkout/index'], ['class' => 'btn btn-success']) ?>
        </div>
    <?php endif; ?>
</div>