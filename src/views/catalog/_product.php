<?php

use yii\helpers\Url;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\shop\models\Product */
/* @var $form yii\widgets\ActiveForm */

$show_opt_price = false;
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
        <?php } else { ?> 
        <a href="<?= Url::to(['product/create']) ?>">
            <div class="shop-product-img">
                <img src="/uploaded_files/add-tovar.jpg" width="100%">
            </div>
        </a>
        <?php } ?>
        
    </div>
</div>
