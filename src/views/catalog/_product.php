<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
/* @var $form yii\widgets\ActiveForm */

$show_opt_cost = false;
?>

<div class="<?= $class ?? 'col-md-2 col-6 shop-product'?>">
    <div class="shop-product-item">
        <?php if (!empty($model->id)) {?>
        <a href="<?= Url::to(['/shop/product/view', 'url' => $model->url]) ?>">
            <div class="shop-product-img"><img src="<?= $model->getFirstImage() ?>" alt="<?= trim($model->title) ?>"></div>
        </a>
        <div class="product-title"><?= $model->title ?></div>
        <div class="product-price"><?= number_format($model->cost, 0, '', ' ' ) ?> ₽<?= ((isset($model->cost_opt) && $show_opt_cost ) ? '<span class="float-right"><span>(опт)</span> '.$model->cost_opt . ' ₽</span>' : '') ?></div>
        
        
        <div class="to-album" data-id="<?= $model->id ?>">
            <button>В корзину</button>
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
