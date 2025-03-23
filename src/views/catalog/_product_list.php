<?php 

use ZakharovAndrew\shop\assets\ShopAssets;
ShopAssets::register($this);

$script = <<< JS
        
$(".add-to-cart").on('click', function () {
    let id = $(this).data('id');
    shop.addCart(id)  
});
    
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>

<style>
    .shop-product-img img {
        width:100%;
        border-radius: 9px 9px 0 0;
    }
    .product-price {
        font-size:24px;
        text-align: center;
    }
    .shop-product-item {
        border: 1px solid #cccccc;
        border-radius: 9px;
        box-sizing: border-box;
        margin-bottom:20px;
    }
    .product-title {
        text-align: center;
        padding: 0 5px;
        font-size:20px;
    }
    .to-album {
        margin:10px 0;
        text-align: center;
    }
    .to-album button {
        border-radius: 20px;
        background-color: #FFC42E;
        border:none;        
        padding: 9px 25px;
        font-family: 'Roboto Condensed', sans-serif;
    }
    .category-description {margin: 30px 0}
    
    
</style>
<div class="row product-list">
    <?php foreach($products as $product) {
        echo $this->render('../catalog/_product', [
            'model' => $product,
            'class' => ($class ?? 'col-md-4 col-12') . ' shop-product'
        ]);
    } ?>
</div>