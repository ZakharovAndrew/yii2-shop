<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use ZakharovAndrew\shop\models\Shop;
use ZakharovAndrew\shop\models\ProductCategory;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var app\models\Product $model */

$module = Yii::$app->getModule('shop');
// current language
$appLanguage = Yii::$app->language;

$this->title = $model->name;
$categories = ProductCategory::getCategories($model->category_id);
foreach ($categories as $category) {
    $this->params['breadcrumbs'][] = ['label' => $category->title, 'url' => ['/shop/product-category/view', 'url' => $category->url]];
}
$last_category = end($categories);

// Получаем свойства товара с их значениями
$propertiesWithValues = $model->getPropertiesWithValues();

//SEO
$this->registerMetaTag(['name' => 'description', 'content' => ($last_category->title ?? '') . ' '. $model->name. ($model->color ? ' '. Module::t('Color'). ' ' . $model->color->name : '')]);
$this->registerMetaTag(['name' => 'og:image', 'content' => $model->getFirstImage('big')]);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $model->getFirstImage('big')]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary_large_image']);

\yii\web\YiiAsset::register($this);

$mobileProductsPerRowStyle = [
    1 => 'col-md-4 col-12 shop-product',
    2 => 'col-md-4 col-6 shop-product',
    3 => 'col-md-4 col-4 shop-product',
];
?>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <style>
      .swiper {
      width: 100%;
    }

    .swiper-slide {
      text-align: center;
      font-size: 18px;
      background: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .swiper-slide img {
      display: block;
      object-fit: cover;
    }
    @media (min-width: 1200px) {
        .product-h1 {
            font-size: 2rem;
        }
    }
    .product-description-block .to-album {
        text-align: left;
    }
    .product-category span {
        color:#8a8b8b;
    }
    .product-category a {
        text-decoration: none;
    }
    .product-category a:hover {
        text-decoration: underline;
    }
    
    .bulk-pricing-table {
    margin-top: 10px;
    width: 100%;
    border-collapse: collapse;
}
.bulk-pricing-table th {
    background-color: #f8f9fa;
    padding: 8px;
    text-align: center;
    border: 1px solid #dee2e6;
}
.bulk-pricing-table td {
    padding: 8px;
    text-align: center;
    border: 1px solid #dee2e6;
}
.bulk-pricing-table .text-success {
    color: #28a745;
    font-weight: bold;
}
.bulk-pricing-table .text-danger {
    color: #dc3545;
}
.out_of_stock {
    font-size:20px;margin:10px 0 20px
}
.product_in_stock {
    font-size:14px;
    color:red;
}
.videoContainer
{
    position:absolute;
    height:100%;
    width:100%;
    overflow: hidden;
}
.videoContainer video
{
    height: -webkit-fill-available;
    width: -webkit-fill-available;
}

/* Стили для свойств товара */
.product-properties {
    margin-top: 30px;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
}

.product-properties-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 20px;
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

.property-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.2s ease;
}

.property-item:hover {
    background-color: rgba(255, 255, 255, 0.5);
}

.property-item:last-child {
    border-bottom: none;
}

.property-name {
    font-weight: 600;
    color: #495057;
    flex: 0 0 40%;
    padding-right: 15px;
}

.property-value {
    flex: 0 0 60%;
    text-align: right;
    color: #333;
    font-weight: 500;
}

.property-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.property-badge-yes {
    background-color: #d4edda;
    color: #155724;
}

.property-badge-no {
    background-color: #f8d7da;
    color: #721c24;
}

/* Адаптивность для свойств */
@media (max-width: 768px) {
    .property-item {
        flex-direction: column;
        align-items: flex-start;
        padding: 15px 0;
    }
    
    .property-name {
        flex: none;
        width: 100%;
        margin-bottom: 5px;
        font-size: 14px;
    }
    
    .property-value {
        flex: none;
        width: 100%;
        text-align: left;
        font-size: 14px;
    }
    
    .product-properties {
        padding: 15px;
    }
}

/* Цвет бейдж для цвета товара */
.color-value-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 20px;
    background: white;
    border: 1px solid #dee2e6;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.color-swatch {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px #dee2e6;
}

.color-name {
    font-weight: 500;
}
  </style>
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  
<style>
    .product-description-block .product-price {font-size:32px;text-align:left}
</style>
<div class="product-view">

    <?php if (!Yii::$app->user->isGuest &&  Shop::canEdit($model->shop_id)) {?>
    <p>
        <?= Html::a(Module::t('Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Module::t('Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a(Module::t('Stock Movements'), ['stock-movements', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>
    <?php } ?>
    
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($model->getImages('big') as $img) { ?>
                    <div class="swiper-slide">
                        <div class="swiper-zoom-container"><img src="<?= $img ?>" class="img-fluid" alt="<?= $model->name ?>"></div>
                    </div>
                    <?php } ?>
                    <?php if (!empty($model->video)) { ?>
                    <div class="swiper-slide" style="height:auto">
                        <div class="videoContainer">
                            <video src="<?= $model->video ?>" controls width="100%" height="auto">Sorry, your browser doesn't support embedded videos!</video>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div class="swiper-pagination"></div>
                <?php if (count($model->getImages()) > 1) { ?>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <?php } ?>
            </div>
        </div>
        <div class="col-12 col-md-6 product-description-block">
            <h1 class='product-h1'><?= Html::encode($this->title) ?></h1>
            
            <!-- product color -->
            <?php if ($model->color): ?>
            <div class="property-item" style="border: none; padding: 5px 0;">
                <div class="property-name"><?= Module::t('Color') ?>:</div>
                <div class="property-value">
                    <span class="color-value-badge">
                        <span class="color-swatch" style="background-color: <?= $model->color->css_color ?>"></span>
                        <span class="color-name"><?= Html::encode($model->color->name) ?></span>
                    </span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($last_category)) {?>
            <p class="product-category">
                <span><?= Module::t('Category')?></span> 
                <?= Html::a($last_category->title, ['/shop/product-category/view', 'url' => $last_category->url]) ?>
            </p>
            <?php } ?>
            
            <div class="product-price"><?= number_format($model->price ?? 0, 0, '', ' ' ) ?> ₽</div>
            
            <!-- Блок оптовых цен -->
            <?php if ($model->bulk_price_quantity_1 || $model->bulk_price_quantity_2 || $model->bulk_price_quantity_3): ?>
            <div class="bulk-pricing">
                <div class="bulk-pricing-title"><?= Module::t('Bulk discounts') ?></div>
                <table class="table table-bordered bulk-pricing-table">
                    <thead>
                        <tr>
                            <th><?= Module::t('Quantity in order') ?></th>
                            <th><?= Module::t('Discounted price') ?></th>
                            <th><?= Module::t('Discount') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $bulkPrices = [
                            ['quantity' => $model->bulk_price_quantity_1, 'price' => $model->bulk_price_1],
                            ['quantity' => $model->bulk_price_quantity_2, 'price' => $model->bulk_price_2],
                            ['quantity' => $model->bulk_price_quantity_3, 'price' => $model->bulk_price_3]
                        ];

                        usort($bulkPrices, function($a, $b) {
                            return $a['quantity'] <=> $b['quantity'];
                        });

                        foreach ($bulkPrices as $item): 
                            if ($item['quantity'] && $item['price']): 
                                $discountPercent = round(100 - ($item['price'] * 100 / $model->price));
                        ?>
                        <tr>
                            <td><?= Module::t('From') ?> <?= $item['quantity'] ?></td>
                            <td><?= number_format($item['price'], 0, '', ' ') ?> ₽</td>
                            <td class="text-success"><?= $discountPercent ?>%</td>
                        </tr>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <div style="display: flex">
                <?php $isFavorite = $model->isInFavorites(); ?>
                <?php if ($model->quantity == 0) { ?>
                <div class="out_of_stock"><?= Module::t('Out of stock') ?></div>
                <?php } else { ?>
                <div class="to-album add-to-cart" data-id="<?= $model->id ?>">
                    <button><?= Module::t('Add to cart') ?></button>
                </div>
                <?php } ?>
                <button class="favorite-btn favorite-toggle<?= $isFavorite ? ' fav-active' : '' ?>"
                data-product-id="<?= $model->id ?>"
                data-is-favorite="<?= $isFavorite ? '1' : '0' ?>" title="<?= $isFavorite ? Module::t('In Favorites') : Module::t('Add to Favorites') ?>">
                    <svg class="heart-svg" viewBox="0 0 24 24">
                        <path class="heart-path" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </button>
            </div>
            
            <?php if ($model->quantity > 1 && $model->quantity <6) { ?>
            <div class="product_in_stock"><?= Module::t('Left') ?> <?= $model->quantity ?> шт.</div>
            <?php } ?>
            
            <?php if ($model->composition): ?>
            <div class="product-composition">
                <h3><?= Module::t('Composition') ?></h3>
                <p><?= nl2br(Html::encode($model->composition)) ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (isset($model->shop->avatarUrl)) {?>
            <div class="shop-avatar-wrap__mini">
                <img src="<?= $model->shop->avatarUrl ?>" alt="<?= $model->shop->name ?>">
                <div><a href="<?= Url::to(['/shop/shop/view', 'url' => $model->shop->url])?>"><?= $model->shop->name ?></a>
                <div class="pavilion2"><?= $model->shop->city ?></div></div>
            </div>
            <?php } ?>
            
            <!-- Блок динамических свойств товара -->
            <?php if (!empty($propertiesWithValues)): ?>
            <div class="product-properties">
                <h3 class="product-properties-title"><?= Module::t('Product Specifications') ?></h3>
                <?php foreach ($propertiesWithValues as $item): 
                    $property = $item['property'];
                    $value = $item['value'];
                    $formattedValue = $value->getFormattedValue();
                ?>
                <div class="property-item">
                    <div class="property-name"><?= Html::encode($property->name) ?></div>
                    <div class="property-value">
                        <?php if ($property->isCheckboxType()): ?>
                            <span class="property-badge property-badge-<?= $formattedValue === 'Да' ? 'yes' : 'no' ?>">
                                <?= $formattedValue ?>
                            </span>
                        <?php else: ?>
                            <?= Html::encode($formattedValue) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div class="product-description"><?= $model->description ?></div>
        </div>
    </div>
    
    <?php if (isset($more_products)) { ?>
    <div class="more_products">
        <h3><?= Module::t('Similar products') ?></h3>
        <?= $this->render('../catalog/_product_list', [
            'products' => $more_products,
            'class' => $mobileProductsPerRowStyle[Yii::$app->shopSettings->get('mobileProductsPerRow')] ?? $mobileProductsPerRowStyle[1]
        ]);?>
    </div>
    <?php } ?>    

</div>

<!-- Initialize Swiper -->
<script>
    var swiper = new Swiper(".mySwiper", {
        zoom: true,
        pagination: {
            el: ".swiper-pagination",
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
</script>