<?php

use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\shop\models\ProductCategory;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ProductCategory $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('Catalog'), 'url' => ['/shop/catalog/index']];

// collecting a list of categories
foreach (ProductCategory::getCategories($model->id) as $category) {
    // exclude the current category
    if ($category->id !== $model->id) {
        $this->params['breadcrumbs'][] = ['label' => $category->title, 'url' => ['/shop/product-category/view', 'url' => $category->url]];
    }
}
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-category-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="category-description"><?= $model->description ?></div>

    <!-- Фильтр по цветам -->
    <?php if (!empty($availableColors)): ?>
        <div class="color-filter mb-4">
            <h4><?= Module::t('Filter by Color') ?></h4>
            <div class="color-filter-options">
                <!-- Кнопка "Все цвета" -->
                <a href="<?= Url::to(['/shop/product-category/view', 'url' => $model->url]) ?>" 
                   class="btn btn-sm btn-outline-secondary <?= empty($selectedColors) ? 'active' : '' ?>">
                    <?= Module::t('All Colors') ?>
                </a>
                
                <!-- Цветовые опции -->
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
                       style="background-color: <?= $color->css_color ?>; border: 2px solid <?= $isActive ? '#007bff' : '#ddd' ?>">
                    </a>
                    
                <?php endforeach; ?>
                
                <?php if (!empty($selectedColors)): ?>
                    <small class="text-muted">
                        <a href="<?= Url::to(['/shop/product-category/view', 'url' => $model->url]) ?>" class="text-danger ms-2">
                            <?= Module::t('Clear filter') ?>
                        </a>
                    </small>
                <?php endif; ?>
            </div>
            
        </div>
    <?php endif; ?>
    
    <?= $this->render('../catalog/_product_list', [
        'products' => $products,
        'pagination' => $pagination
    ]) ?>
        
    <div class="category-description_after"><?= $model->description_after ?></div>
    
    <?php if (!Yii::$app->user->isGuest) {?>
    <p>
        <?= Html::a(Module::t('Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>
    <?php } ?>
</div>

<style>
.color-filter-options {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    margin-top: 10px;
}
.color-filter-option {
    display: inline-block;
    width: 35px;
    height: 35px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}
.color-filter-option:hover {
    transform: scale(1.1);
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
.color-filter-option.active {
    border-color: #007bff !important;
    box-shadow: 0 0 15px rgba(0,123,255,0.4);
    transform: scale(1.15);
}
.selected-colors-info {
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}
.badge {
    margin-right: 5px;
}
</style>
