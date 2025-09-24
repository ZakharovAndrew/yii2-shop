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
                    $urlParams = ['url' => $model->url];
                    
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
            </div>
            
            <!-- Информация о выбранных цветах -->
            <?php if (!empty($selectedColors)): ?>
                <div class="selected-colors-info mt-2">
                    <small class="text-muted">
                        <?= Module::t('Selected colors') ?>: 
                        <?php foreach ($availableColors as $color): ?>
                            <?php if (in_array($color->id, $selectedColors)): ?>
                                <span class="badge bg-primary"><?= $color->name ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <a href="<?= Url::to(['/shop/product-category/view', 'url' => $model->url]) ?>" class="text-danger ms-2">
                            <?= Module::t('Clear filter') ?>
                        </a>
                    </small>
                </div>
            <?php endif; ?>
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
