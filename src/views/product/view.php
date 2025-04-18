<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
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
//SEO
$this->registerMetaTag(['name' => 'description', 'content' => $last_category->title . ' '. $model->name]);
$this->registerMetaTag(['name' => 'og:image', 'content' => $model->getFirstImage('big')]);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $model->getFirstImage('big')]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary_large_image']);

//$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <style>
      .swiper {
      width: 100%;
      /*height: 100%;*/
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
      /*width: 100%;
      height: 100%;*/
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
  </style>
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  
<style>
    .product-description-block .product-price {font-size:32px;text-align:left}
</style>
<div class="product-view">

    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->hasRole('admin')) {?>
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
                        <div class="swiper-zoom-container">
                            <img src="<?= $img ?>" class="img-fluid"></div>
                        </div>
                    <?php } ?>
                </div>
                <div class="swiper-pagination"></div>
                <?php if (count($model->getImages()) > 1) { ?>
                <!-- If we need navigation buttons -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <?php } ?>
            </div>
            
        </div>
        <div class="col-12 col-md-6 product-description-block">
            <h1 class='product-h1'><?= Html::encode($this->title) ?></h1>
            <p class="product-category"><span><?= Module::t('Category')?></span> <?= Html::a($last_category->title, ['update', '/shop/product-category/view', 'url' => $last_category->url]) ?></p>
            <div class="product-price"><?= number_format($model->price ?? 0, 0, '', ' ' ) ?> ₽</div>
            <div class="to-album add-to-cart" data-id="<?= $model->id ?>">
                <button><?= Module::t('Add to cart') ?></button>
            </div>
            <?= $model->description ?>
            <div class="product-additional-params">
                <?php
                foreach (range(1,3) as $i) {
                    if (isset($module->params[$i])) { ?>
                
                <div class="product-additional-params-<?= $i ?>">
                    <div class="product-additional-param_title"><?= $module->params[$i]['title'][$appLanguage] ?></div>
                    <?= $model->{'param'.$i} ?>
                </div>
                        
                    <?php 
                    }
                } ?>
            </div>
        </div>
    </div>
    
    <?php if (isset($more_products)) { ?>
    <div class="more_products">
        <h3><?= Module::t('Similar products') ?></h3>
    <?= $this->render('../catalog/_product_list', [
        'products' => $more_products,
        'class' => 'col-md-3 col-12'
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