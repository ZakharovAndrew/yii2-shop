<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\models\ProductCategory;
use ZakharovAndrew\shop\models\ProductProperty;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\shop\assets\ShopAssets;
ShopAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ProductCategory $model */

$this->title = !empty($model->meta_title) ? $model->meta_title : $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('Catalog'), 'url' => ['/shop/catalog/index']];

// Получаем активные свойства товара
$properties = ProductProperty::getActiveProperties();

//SEO
if (!empty($model->meta_description)) {
    $this->registerMetaTag(['name' => 'description', 'content' => $model->meta_description]);
}
if (!empty($model->meta_keywords)) {
    $this->registerMetaTag(['name' => 'keywords', 'content' => $model->meta_keywords]);
}
$this->registerMetaTag(['name' => 'og:title', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'og:type', 'content' => 'product.group']);
Yii::$app->view->registerLinkTag([
    'rel' => 'canonical', 
    'href' => Yii::$app->urlManager->createAbsoluteUrl(['/shop/product-category/view', 'url' => $model->url])
]);

// collecting a list of categories
foreach (ProductCategory::getCategories($model->id) as $category) {
    // exclude the current category
    if ($category->id !== $model->id) {
        $this->params['breadcrumbs'][] = ['label' => $category->title, 'url' => ['/shop/product-category/view', 'url' => $category->url]];
    }
}
$this->params['breadcrumbs'][] = $model->title;
?>
<div class="product-category-view">

    <?php if (Yii::$app->getModule('shop')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>
    
    <?php if ($model->getSubCategories() != null) {?>
    <div class="swiper links-swiper">
        <div class="swiper-wrapper">
        <?php foreach ($model->getSubCategories() as $subCategory) {?>
            
            <div class="swiper-slide">
                <a href="<?= Url::to(['/shop/product-category/view', 'url' => $subCategory->url]) ?>"><?= $subCategory->title ?></a>
            </div>
        <?php } ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script>
    document.addEventListener('DOMContentLoaded', function() {           
        const linksSwiper = new Swiper('.links-swiper', {
            direction: 'horizontal',
            slidesPerView: 'auto',
            spaceBetween: 10,
            freeMode: true,
            grabCursor: true,
            mousewheel: {
                forceToAxis: true,
            },
            scrollbar: {
                el: '.swiper-scrollbar',
                draggable: true,
            },
            breakpoints: {
                320: {
                    spaceBetween: 8,
                },
                768: {
                    spaceBetween: 12,
                },
                1024: {
                    spaceBetween: 15,
                }
            }
        });
    });
    </script>
    <?php } ?>
    
    <div class="category-description"><?= $model->description ?></div>
</div>
<div class="products-catalog">
    <div class="products-catalog__left">
        <!-- Фильтр по цветам -->
        <div id="close-filter">
            <div class="btn">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="14px" height="14px" viewBox="0 0 50 50" version="1.1">
                    <g id="surface1">
                    <path style=" stroke:none;fill-rule:nonzero;fill:rgb(33 150 243);fill-opacity:1;" d="M 2.722656 5.144531 L 5.152344 2.75 C 6.542969 1.328125 8.867188 1.328125 10.253906 2.75 L 25.003906 17.464844 L 39.753906 2.75 C 41.144531 1.328125 43.46875 1.328125 44.855469 2.75 L 47.25 5.144531 C 48.671875 6.53125 48.671875 8.859375 47.25 10.246094 L 32.535156 24.996094 L 47.25 39.746094 C 48.671875 41.132812 48.671875 43.457031 47.25 44.847656 L 44.855469 47.277344 C 43.46875 48.664062 41.144531 48.664062 39.753906 47.277344 L 25.003906 32.527344 L 10.253906 47.277344 C 8.867188 48.664062 6.542969 48.664062 5.152344 47.277344 L 2.722656 44.847656 C 1.335938 43.457031 1.335938 41.132812 2.722656 39.746094 L 17.472656 24.996094 L 2.722656 10.246094 C 1.335938 8.859375 1.335938 6.53125 2.722656 5.144531 Z M 2.722656 5.144531 "></path>
                    </g>
                </svg>
            </div>
        </div>
        <div class="filter-form-group">
        <?php if (!empty($availableColors)): ?>
            <div class="color-filter mb-4">
                <h4><?= Module::t('Filter by Color') ?></h4>

                <div class="color-filter-wrapper">
                    <div class="color-filter-options">
                        <!-- Colors options  -->
                        <?php foreach ($availableColors as $color): ?>
                            <?php
                            $isActive = in_array($color->id, $selectedColors);
                            $urlParams = ['/shop/product-category/view', 'url' => $model->url, 'sorting' => $sorting, 'filter' => $filter];

                            if ($isActive) {
                                // Удаляем цвет из фильтра
                                $newColors = array_diff($selectedColors, [$color->id]);
                            } else {
                                // Добавляем цвет в фильтр
                                $newColors = array_merge($selectedColors, [$color->id]);
                            }

                            if (!empty($newColors)) {
                                $urlParams['colors'] = $newColors;
                            }
                            ?>
                            <a href="<?= Url::to($urlParams) ?>" 
                               class="color-filter-option <?= $isActive ? 'active' : '' ?>"
                               title="<?= Html::encode($color->name) ?>"
                               style="background-color: <?= $color->css_color ?>">
                            </a>
                        <?php endforeach; ?>
                        <?php if (!empty($selectedColors)): ?>

                            <a href="<?= Url::to(['/shop/product-category/view', 'url' => $model->url]) ?>" class="clear-filter-link">
                                <?= Module::t('Clear filter') ?>
                            </a>
                        <?php endif; ?>
                    </div>


                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($properties)): ?>
        <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['/shop/product-category/view', 'url' => $url, 'colors' => $colors, 'sorting' => $sorting], // явно укажите действие
            ]); ?>
        <?php foreach ($properties as $property): ?>
            <div class="filter__item">
                <div class="propety-header"><?= $property->name ?></div>
                <div>
                    <?php if ($property->isSelectType()): ?>
                    <?= Html:: CheckboxList(
                        'filter['.$property->code.']',
                        $filter[$property->code] ?? null,
                        $property->getOptionsList(),
                        []
                    ) ?>
                    <?php elseif ($property->isTextType()): ?>
                     <?= Html:: CheckboxList(
                        'filter['.$property->code.']',
                        $filter[$property->code] ?? null,
                        $property->getTextList(),
                        []
                    ) ?> 
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <div class="form-group" style="padding-left:15px;">
            <?= Html::submitButton(Module::t('Filter'), ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
        <?php endif; ?>
    </div>
    <div class="products-catalog__right">
        <div class="products-header">
            <div><div class="btn bnt-sm" id="product-catalog-filter">Фильтр</div></div>
            <div class="selected-filters"></div>
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['/shop/product-category/view', 'url' => $url, 'colors' => $colors, 'filter' => $filter],
            ]); ?>
                <?= Html::dropDownList(
                    'sorting',
                    $sorting,
                    ProductCategory::sortingLabels(),
                    [
                        'class' => 'form-control form-select',
                        'onchange' => 'this.form.submit()'
                    ]
                ) ?>
            <?php ActiveForm::end(); ?>
        </div>
    
        <?= $this->render('../catalog/_product_list', [
            'products' => $products,
            'pagination' => $pagination,
            'class' => (Yii::$app->shopSettings->get('mobileProductsPerRow') == 2 ? 'col-md-4 col-6 shop-product' : 'col-md-4 col-12 shop-product')
        ]) ?>
        
        <div class="category-description_after"><?= $model->description_after ?></div>
    
        <?php if (!Yii::$app->user->isGuest) {?>
        <p>
            <?= Html::a(Module::t('Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </p>
        <?php } ?>
    </div>
</div>

<?php
$script = <<< JS
   
$('#product-catalog-filter').on('click', function() {
    $(".products-catalog__left").addClass("products-catalog__left_opened");
});
$('#close-filter').on('click', function() {
    $(".products-catalog__left").removeClass("products-catalog__left_opened");
});

JS;
$this->registerJs($script, yii\web\View::POS_READY);

