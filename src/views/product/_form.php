<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\models\ProductCategory;
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

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
    
    <!-- Секция оптовых цен -->
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><?= Module::t('Bulk pricing') ?></h3>
            <p class="text-muted mb-0"><?= Module::t('Set prices for different quantity ranges') ?></p>
        </div>
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
    <!-- Конец секции оптовых цен -->
    
    <?= $form->field($model, 'quantity')->textInput([
        'type' => 'number',
        'min' => 0,
        'disabled' => true, // Запрещаем прямое редактирование
    ])->label(Module::t('Quantity on stock')) ?>
    
    <?php if (!isset($action) || $action != 'create') {?>
    <div class="form-group">
        <?= Html::a(Module::t('Add to Stock'), ['update-stock', 'id' => $model->id], [
            'class' => 'btn btn-primary',
            'style' => 'margin-bottom: 20px;'
        ]) ?>
    </div>
    <?php } ?>

    <?= $form->field($model, 'images')->widget(ImageUploadWidget::class, ['url' => '123', 'id'=> 'product-images', 'form' => $form]); ?>
    
    <?= $form->field($model, 'composition')->textarea(['rows' => 3]) ?>
    
    <?= $form->field($model, 'weight')->textInput(['type' => 'number', 'step' => '0.01']) ?>
    
    <?php
    /* additional params */
    foreach (range(1,3) as $i) {
        if (isset($module->params[$i])) {
            echo $form->field($model, 'param'.$i)->textInput(['maxlength' => true])->label($module->params[$i]['title'][$appLanguage]);
        }
    } ?>
    <?= $form->field($model, 'category_id')->dropDownList(ProductCategory::getDropdownGroups(), ['prompt' => '', 'class' => 'form-control form-select']) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
    <?= ImageUploadWidget::afterForm() ?>

</div>
