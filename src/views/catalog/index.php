<?php

use ZakharovAndrew\shop\Module;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$module = Yii::$app->getModule('shop');
$products = $dataProvider->getModels();


if (!empty($module->catalogPageID)) {
    $page = \ZakharovAndrew\pages\models\Pages::findOne($module->catalogPageID);
    //SEO
    if (!empty($page->meta_description)) {
        $this->registerMetaTag(['name' => 'description', 'content' => $page->meta_description]);
    }
    if (!empty($page->meta_keywords)) {
        $this->registerMetaTag(['name' => 'keywords', 'content' => $page->meta_keywords]);
    }
}

//SEO
Yii::$app->view->registerLinkTag([
    'rel' => 'canonical', 
    'href' => Yii::$app->urlManager->createAbsoluteUrl(['/shop/catalog/index'])
]);

$this->title = (!empty($page) ? $page->title : $module->catalogTitle);  
$this->params['breadcrumbs'][] = $this->title;

$mobileProductsPerRowStyle = [
    1 => 'col-md-4 col-12 shop-product',
    2 => 'col-md-4 col-6 shop-product',
    3 => 'col-md-4 col-4 shop-product',
];
?>

<div class="catalog-index">
    
    <?php if (Yii::$app->getModule('shop')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>
    
    <?= $this->render('_product_list', [
        'products' => $products,
        'pagination' => $dataProvider->pagination,
        'class' => $mobileProductsPerRowStyle[Yii::$app->shopSettings->get('mobileProductsPerRow')] ?? $mobileProductsPerRowStyle[1]
    ]) ?>
    
    <?php if (!empty($page)) {        
        echo $page->content;
        
        // edit link
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->hasRole('admin')) {
            echo '<p>' . Html::a(Module::t('Edit'), ['/pages/default/update', 'id' => $page->id], ['class' => 'btn btn-success']) . '</p>';
        }
    } ?>

</div>
