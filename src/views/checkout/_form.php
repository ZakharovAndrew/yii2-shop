<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\models\ProductCategory;
use ZakharovAndrew\shop\Module;

use ZakharovAndrew\shop\assets\ShopAssets;
ShopAssets::register($this);
?>

<div class="row">
    <!-- Left Column - Checkout Form -->
    <div class="col-md-8">
        <?php $form = ActiveForm::begin([
            'id' => 'checkout-form',
            'enableAjaxValidation' => true,
            'options' => ['class' => 'needs-validation']
        ]); ?>

        <!-- Contact Information Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Контактная информация</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'last_name')->textInput([
                            'maxlength' => true,
                            'placeholder' => Module::t('Last Name'),
                            'required' => true
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'first_name')->textInput([
                            'maxlength' => true,
                            'placeholder' => Module::t('First Name'),
                            'required' => true
                        ]) ?>
                    </div>
                </div>

                <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '+7 (999) 999-99-99',
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => '+7 (___) ___-__-__',
                        'required' => true
                    ]
                ]) ?>

                <?php if ($isGuest) { ?>
                    <?= $form->field($model, 'email')->textInput([
                        'placeholder' => 'example@site.com',
                    ])->hint('Для создания аккаунта и отслеживания заказа') ?>
                <?php } ?>
            </div>
        </div>

        <!-- Delivery Information Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Доставка</h3>
            </div>
            <div class="card-body">
                <?= $form->field($model, 'delivery_method')->dropDownList(
                    $deliveryMethods,
                    [
                        'prompt' => '-- '.Module::t('Select delivery method').' --',
                        'class' => 'form-control',
                        'required' => true
                    ]
                ) ?>

                <div id="delivery-fields">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <?= $form->field($model, 'city')->textInput([
                                'maxlength' => true,
                                'placeholder' => Module::t('City'),
                                'required' => true
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'postcode')->textInput([
                                'maxlength' => true,
                                'placeholder' => 'Почтовый индекс',
                                'required' => true
                            ]) ?>
                        </div>
                    </div>
                    <?= $form->field($model, 'address')->textarea([
                        'rows' => 3,
                        'placeholder' => 'Улица, дом, квартира',
                        'required' => true
                    ]) ?>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="form-group">
            <?= Html::submitButton('Подтвердить заказ', [
                'class' => 'btn btn-primary btn-lg btn-block',
                'name' => 'checkout-button'
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <!-- Right Column - Order Summary -->
    <div class="col-md-4">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header">
                <h3>Ваш заказ</h3>
            </div>
            <div class="card-body">
                <!-- Products Subtotal -->
                <div class="d-flex justify-content-between mb-2">
                    <span>Товары:</span>
                    <span id="cart-total" data-value="<?= $totalSum['total'] ?>">
                        <?php if (isset($totalSum['total_without_discount'])) { ?>
                        <span class="cost-without-discount"><?= Yii::$app->formatter->asCurrency($totalSum['total_without_discount']) ?></span><br>
                        <?php } ?>
                        <?= Yii::$app->formatter->asCurrency($totalSum['total']) ?>
                    </span>
                </div>

                <!-- Delivery Cost (dynamic) -->
                <div class="d-flex justify-content-between mb-2" id="delivery-row" style="display: none;">
                    <span>Доставка:</span>
                    <span id="delivery-price" data-value="0">
                        <?= Yii::$app->formatter->asCurrency(0) ?>
                    </span>
                </div>

                <hr>

                <!-- Grand Total -->
                <div class="d-flex justify-content-between font-weight-bold">
                    <span><?= Module::t('Total') ?>:</span>
                    <span id="order-total" data-value="<?= $totalSum['total'] ?>">
                        <?= Yii::$app->formatter->asCurrency($totalSum['total']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>