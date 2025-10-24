<?php 

use yii\widgets\LinkPager;
use ZakharovAndrew\shop\assets\ShopAssets;
ShopAssets::register($this);
?>

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