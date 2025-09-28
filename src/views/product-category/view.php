<?php

use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\shop\models\ProductCategory;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ProductCategory $model */

$this->title = !empty($model->meta_title) ? $model->meta_title : $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('Catalog'), 'url' => ['/shop/catalog/index']];

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

use ZakharovAndrew\shop\assets\ShopAssets;
ShopAssets::register($this);
?>
<div class="product-category-view">

    <?php if (Yii::$app->getModule('shop')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>
    
    <div class="category-description"><?= $model->description ?></div>

    <!-- Фильтр по цветам -->
    <?php if (!empty($availableColors)): ?>
        <div class="color-filter mb-4">
            <h4><?= Module::t('Filter by Color') ?></h4>
            
            <div class="color-filter-wrapper">
                <div class="color-filter-options">
                    <a href="<?= Url::to(['/shop/product-category/view', 'url' => $model->url]) ?>" 
                       class="color-filter-btn <?= empty($selectedColors) ? 'active' : '' ?>">
                        <?= Module::t('All Colors') ?>
                    </a>
                    
                    <!-- Colors options  -->
                    <?php foreach ($availableColors as $color): ?>
                        <?php
                        $isActive = in_array($color->id, $selectedColors);
                        $urlParams = ['/shop/product-category/view', 'url' => $model->url];
                        
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
    
    <?= $this->render('../catalog/_product_list', [
        'products' => $products,
        'pagination' => $pagination,
        'class' => (Yii::$app->getModule('shop')->mobileProductsPerRow == 2 ? 'col-md-4 col-6 shop-product' : 'col-md-4 col-12 shop-product')
    ]) ?>
        
    <div class="category-description_after"><?= $model->description_after ?></div>
    
    <?php if (!Yii::$app->user->isGuest) {?>
    <p>
        <?= Html::a(Module::t('Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>
    <?php } ?>
</div>