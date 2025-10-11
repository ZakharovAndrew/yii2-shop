<?php

use yii\helpers\Html;
use yii\web\View;

// Static variable to track if Swiper is already registered
static $swiperRegistered = false;

if (!$swiperRegistered) {
    $this->registerJsFile(
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        ['position' => View::POS_HEAD]
    );
    $this->registerCssFile(
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        ['position' => View::POS_HEAD]
    );
    $swiperRegistered = true;
} 

?>

<div class="container" style="background: #fff;border-radius: 11px;" id="<?= $id ?>">
    <div class="block-section__header">
        <div class="block-section__title-wrapper">
            <h2 class="block-section__title"><?= Html::encode($title) ?></h2>
        </div>
        <div class="custom-slider__buttons">
            <button type="button" id="<?= $swiperId ?>-left-toggle" tabindex="0" aria-label="Previous slide" aria-disabled="false">
                <div class="icon-arrow"></div>
            </button>
            <button type="button" id="<?= $swiperId ?>-right-toggle" tabindex="0" aria-label="Next slide" aria-disabled="false" style="transform: rotate(180deg);">
                <div class="icon-arrow"></div>
            </button>
        </div>
    </div>
    
    <div class="block-section__body" id="<?= $swiperId ?>">
        <div class="swiper-wrapper">
            <?php foreach ($products as $product) {
                echo $this->render($viewFile, [
                    'model' => $product,
                    'class' => 'shop-product swiper-slide'
                ]);
            } ?>
        </div>
    </div>
</div>

<?php
// Initialize Swiper for this widget
$js = <<<JS
// Initialize Swiper for $swiperId
new Swiper('#$swiperId', {
    slidesPerView: 'auto',
    spaceBetween: 12,
    navigation: {
        nextEl: '#$swiperId-right-toggle',
        prevEl: '#$swiperId-left-toggle',
    },
    breakpoints: {
        320: {
            slidesPerView: 2,
            spaceBetween: 20
        },
        768: {
            slidesPerView: 4,
            spaceBetween: 20
        },
        1200: {
            slidesPerView: 6,
            spaceBetween: 24,
        },
    },
});
JS;

$this->registerJs($js);
?>