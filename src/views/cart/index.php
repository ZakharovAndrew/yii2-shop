<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\models\Product;
use ZakharovAndrew\shop\Module;

$this->title = 'Корзина';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .img-cart {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        vertical-align: middle;
        width: 4rem;
        height: 4rem;
        line-height: 4rem;
    }
    .count-buttons {
        position: relative;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        display: flex;
        justify-content: center;
        box-sizing: border-box;
        background-color: #fff;
        width: fit-content;
    }
    .count-buttons__wrapper {
        display: inline-block;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
    }
    .count-buttons__button {
        box-shadow: none;
        color: #afafaf;
        font-size: 18px;
        font-weight: 400;
        overflow: hidden;
        padding: 0;
        line-height: 1.5;
        outline: none;
        height: 100%;
        width: 40px;
        border: none;
        background: rgba(0, 0, 0, 0);
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .cart-product {
        padding: 0 8px;
    }
    .cart-row {
        border-top: 1px solid #dfe2e1;
        padding-top: .75rem!important;
        padding-bottom: .75rem!important;
    }
    .cart-product{
        margin-left: .75rem !important;
    }
</style>
<div class="cart-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (empty($cartItems)): ?>
    <div class="empty-basket text-center"><img src="/empty-basket.png" class="img-responsive img-fluid"><h3>Корзина пустая</h3><p>Добавьте товары из магазина</p></div>
        <p>Ваша корзина пуста.</p>
    <?php else: ?>
            <?php

            /* 
             * список товаров в корзине
             */
            $sumProduct = 0;
            $sumCost = 0;
            foreach ($cartItems as $item) { ?>
            <div id="cart-row-<?= $item->product_id ?>" class="row cart-row">
                <div class="col-lg-9 col-md-9 col-9">
                    <div style="display:flex">
                        <?php
                        $product = Product::findOne($item->product_id);
                        if ($product) {
                            echo '<img src="'.$product->getFirstImage().'" class="img-cart">';
                        } else {
                            echo $item->product_id;
                        }
                        ?>
                        <div class="cart-product" data-id="<?= $product->id ?>">
                            <div style="color:#21313c">
                                <?= $product->title ?>
                            </div>
                            
                            

                            <div>
                                <div class="count-buttons">
                                    <div class="count-buttons__wrapper">
                                        <div class="count-buttons__button" onclick="shop.minusCart(<?= $product->id ?>, false)">-</div>
                                    </div>
                                    <div class="product-counter" id="product-counter-<?= $product->id ?>"><?= $item->quantity ?></div>
                                    <div class="count-buttons__wrapper">
                                        <div class="count-buttons__button" onclick="shop.addCart(<?= $product->id ?>, false)">+</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
                
                <div class="text-center col-md-3 col-3">
                    <div class="cost" id="product-cost-<?= $product->id ?>" data-cost="<?= $product->price ?>"><?= $product->price * $item->quantity ?> ₽</div>
                    <div onclick="shop.removeFromCart(<?= $item->product_id ?>)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                            </div>
                </div>
            </div>
                 
            <?php 
                $sumProduct += $item->quantity;
                $sumCost += $product->price * $item->quantity;
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
                    <span id="products-cost"><?= $sumCost ?></span> ₽
                </div>
            </div>
        <?= Html::a('Очистить корзину', ['cart/clear'], ['class' => 'btn btn-danger']) ?>
    <?php endif; ?>
</div>