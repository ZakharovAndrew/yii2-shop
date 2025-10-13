<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\Shop $model */

$this->title = $model->name;

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php if ($model->avatar): ?>
        <div class="shop-avatar mb-3">
            <?= Html::img($model->avatarUrl, ['class' => 'img-fluid', 'alt' => $model->name, 'style' => 'max-width: 200px;']) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($model->description): ?>
        <div class="shop-description card mb-3">
            <div class="card-body">
                <?= $model->description ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($model->city || $model->address || $model->telegram || $model->whatsapp) : ?>
    <div class="shop-contacts card mb-3">
        <div class="card-body">
            <h5 class="card-title"><?= Module::t('Contacts') ?></h5>
            <div class="row">
                <?php if ($model->city): ?>
                    <div class="col-md-6">
                        <strong><?= Module::t('City') ?>:</strong> <?= Html::encode($model->city) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($model->address): ?>
                    <div class="col-md-6">
                        <strong><?= Module::t('Address') ?>:</strong> <?= Html::encode($model->address) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($model->telegram): ?>
                    <div class="col-md-6 mt-2">
                        <strong>Telegram:</strong> 
                        <?= Html::a($model->telegram, 'https://t.me/' . $model->telegram, ['target' => '_blank']) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($model->whatsapp): ?>
                    <div class="col-md-6 mt-2">
                        <strong>WhatsApp:</strong> 
                        <?= Html::a($model->whatsapp, 'https://wa.me/' . preg_replace('/[^0-9]/', '', $model->whatsapp), ['target' => '_blank']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?= $this->render('../catalog/_product_list', [
        'products' => $products,
        'pagination' => $pagination
    ]) ?>
    
    <?php if ($model->description_after_products): ?>
        <div class="shop-description-after card mt-3">
            <div class="card-body">
                <?= $model->description_after_products ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!Yii::$app->user->isGuest && $model->canEdit): ?>
        <div class="mt-3">
            <?= Html::a(Module::t('Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Module::t('Add Product'), ['product/create', 'shop_id' => $model->id], ['class' => 'btn btn-success']) ?>
        </div>
    <?php endif; ?>
</div>