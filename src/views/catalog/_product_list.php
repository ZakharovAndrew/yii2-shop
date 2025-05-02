<?php 

use yii\widgets\LinkPager;
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

<?php if (isset($pagination)) {?>
<?= LinkPager::widget([
    'pagination' => $pagination,
    'linkOptions' => ['class' => 'page-link'],
]) ?>
<?php } ?>