<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\shop\assets\ShopAssets;
ShopAssets::register($this);

/** @var $this View */
/** @var $favorites \ZakharovAndrew\shop\models\Product[] */

$this->title = Module::t('My Favorites');
$this->params['breadcrumbs'][] = $this->title;

$module = Yii::$app->getModule('shop');
$products = $dataProvider->getModels();

$mobileProductsPerRowStyle = [
    1 => 'col-md-4 col-12 shop-product',
    2 => 'col-md-4 col-6 shop-product',
    3 => 'col-md-4 col-4 shop-product',
];
?>
<div class="favorite-list">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php if (empty($products)): ?>
        <div class="alert alert-info">
            <?= Module::t('You have no favorite products yet.') ?>
        </div>
    <?php else: ?>
        <?= $this->render('../catalog/_product_list', [
        'products' => $products,
        'pagination' => $dataProvider->pagination,
        'class' => $mobileProductsPerRowStyle[Yii::$app->shopSettings->get('mobileProductsPerRow')] ?? $mobileProductsPerRowStyle[1]
    ]) ?>
    <?php endif; ?>
</div>

<?php
$js = <<<JS
$('.remove-favorite').on('click', function() {
    var productId = $(this).data('product-id');
    var \$productItem = $(this).closest('.product-item');
    
    $.post('/shop/favorite/remove', {id: productId}, function(response) {
        if (response.success) {
            \$productItem.fadeOut(300, function() {
                $(this).remove();
                updateFavoritesCount(response.favoritesCount);
                
                // If no products left, show empty message
                if ($('.product-item').length === 0) {
                    $('.favorite-list .row').html(
                        '<div class="alert alert-info">' + 
                        'You have no favorite products yet.' +
                        '</div>'
                    );
                }
            });
        } else {
            alert('Error: ' + response.message);
        }
    });
});

function updateFavoritesCount(count) {
    $('.favorites-count').text(count);
}
JS;

$this->registerJs($js);
?>