<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\models\ProductCategory;
use ZakharovAndrew\shop\models\Shop;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\imageupload\ImageUploadWidget;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\Product $model */
/** @var yii\widgets\ActiveForm $form */

$module = Yii::$app->getModule('shop');
// current language
$appLanguage = Yii::$app->language;
$this->registerJsFile('https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js');
$script = <<< JS
   
ClassicEditor
    .create( document.querySelector( '#product-description' ) )
    .catch( error => {
        console.error( error );
    } );

JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
<style>
    .has-error .help-block {color:red}
    .product-form .card {
        padding:0;
        border:0;
        box-shadow: 0 7px 14px 0 rgba(65, 69, 88, 0.1), 0 3px 6px 0 rgba(0, 0, 0, 0.07);
        margin-bottom: 20px
    }
    .product-form .card .card-header {
        background: #f9fafd;
        line-height: 26px;
        font-size:14px;
        color:#617083;
        border:0;
    }
    .product-form .card label {
        font-size:14px;
        letter-spacing:0.266667px;
        line-height:20px;
        margin-bottom:8px;
        font-weight: 500;
    }
    .product-form .card .card-body .form-group:last-child {
        margin-bottom: 0
    }
</style>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <h6 class=" card-header"><?= Module::t('Basic information') ?></h6>
                <div class="card-body">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="card">
                <h6 class=" card-header">Details</h6>
                <div class="card-body">
                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
                    
                    <?= $form->field($model, 'composition')->textarea(['rows' => 1]) ?>
                </div>
            </div>
            
            <div class="card">
                <h6 class=" card-header"><?= Module::t('Images') ?></h6>
                <div class="card-body">
                    <?= $form->field($model, 'images')->widget(ImageUploadWidget::class, ['url' => '123', 'id'=> 'product-images', 'form' => $form])->label(false); ?>
                </div>
            </div>
            
            <div class="card">
                <h6 class=" card-header">Additional</h6>
                <div class="card-body">
                    <?= $form->field($model, 'weight')->textInput(['type' => 'number', 'step' => '0.01']) ?>
    
                    <?php
                    /* additional params */
                    foreach (range(1,3) as $i) {
                        if (isset($module->params[$i])) {
                            echo $form->field($model, 'param'.$i)->textInput(['maxlength' => true])->label($module->params[$i]['title'][$appLanguage]);
                        }
                    } ?>
                    <?= $form->field($model, 'category_id')->dropDownList(ProductCategory::getDropdownGroups(), ['prompt' => '', 'class' => 'form-control form-select']) ?>

                    <?= $form->field($model, 'status')->dropDownList(
                        $model::getStatuses(), 
                        [
                            'prompt' => Module::t('Select status'),
                            'class' => 'form-control form-select'
                        ]
                    ) ?>
                    
                    <?php if ($module->multiStore) {
                        echo $form->field($model, 'shop_id')->dropDownList(
                            Shop::getShopsList(), 
                            [
                                'class' => 'form-control form-select'
                            ]
                        )->label(Module::t('Store'));
                    } ?>
                    
                    <?= $form->field($model, 'rating')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                    
                    <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'video')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div style="position: sticky;z-index: 1015;top:80px">
                <div class="card">
                    <h6 class="card-header">Pricing</h6>
                    <div class="card-body">
                        <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="card">
                    <h6 class="card-header"><?= Module::t('Bulk pricing') ?></h6>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'bulk_price_quantity_1')->textInput([
                                    'type' => 'number',
                                    'min' => 1,
                                    'placeholder' => Module::t('Quantity threshold')
                                ])->label(Module::t('Bulk quantity 1')) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'bulk_price_1')->textInput([
                                    'type' => 'number',
                                    'min' => 0,
                                    'placeholder' => Module::t('Special price')
                                ])->label(Module::t('Bulk price 1')) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'bulk_price_quantity_2')->textInput([
                                    'type' => 'number',
                                    'min' => 1,
                                    'placeholder' => Module::t('Quantity threshold')
                                ])->label(Module::t('Bulk quantity 2')) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'bulk_price_2')->textInput([
                                    'type' => 'number',
                                    'min' => 0,
                                    'placeholder' => Module::t('Special price')
                                ])->label(Module::t('Bulk price 2')) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'bulk_price_quantity_3')->textInput([
                                    'type' => 'number',
                                    'min' => 1,
                                    'placeholder' => Module::t('Quantity threshold')
                                ])->label(Module::t('Bulk quantity 3')) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'bulk_price_3')->textInput([
                                    'type' => 'number',
                                    'min' => 0,
                                    'placeholder' => Module::t('Special price')
                                ])->label(Module::t('Bulk price 3')) ?>
                            </div>
                        </div>

                        <p class="text-muted small"><?= Module::t('Applies when quantity in cart meets or exceeds this value') ?></p>
                    </div>
                </div>
            
            
                <div class="card">
                    <h6 class=" card-header">Stock</h6>
                    <div class="card-body">
                        <?= $form->field($model, 'quantity')->textInput([
                            'type' => 'number',
                            'min' => 0,
                            'disabled' => true, // Запрещаем прямое редактирование
                        ])->label(Module::t('Quantity on stock')) ?>

                        <?php if (!isset($action) || $action != 'create') {?>
                        <div class="form-group">
                            <?= Html::a(Module::t('Add to Stock'), ['update-stock', 'id' => $model->id], [
                                'class' => 'btn btn-primary',
                            ]) ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton((!isset($action) || $action != 'create') ? Module::t('Save') : Module::t('Add Product'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
    <?= ImageUploadWidget::afterForm() ?>

</div>
