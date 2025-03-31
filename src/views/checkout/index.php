<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use ZakharovAndrew\shop\Module;

/**
 * @var yii\web\View $this
 * @var ZakharovAndrew\shop\models\Order $model
 * @var array $deliveryMethods Array of available delivery methods
 */
$this->title = Module::t('Checkout');
$this->params['breadcrumbs'][] = ['label' => Module::t('Cart'), 'url' => ['/shop/cart/index']];
$this->params['breadcrumbs'][] = $this->title;

// JavaScript for dynamic delivery price calculation
$deliveryPriceJs = <<<JS
$(document).on('change', '#order-delivery_method', function() {
    const methodId = $(this).val();
    if (!methodId) return;
    
    $.post('/shop/checkout/get-delivery-price', {
        method_id: methodId,
        _csrf: yii.getCsrfToken()
    })
    .done(function(response) {
        if (response.success) {
            $('#delivery-price').html(response.formattedPrice);
            $('#delivery-row').show();
            updateOrderTotal();
        }
    })
    .fail(function() {
        console.error('Failed to get delivery price');
    });
});

function updateOrderTotal() {
    const productsTotal = parseFloat($('#cart-total').data('value'));
    const deliveryPrice = parseFloat($('#delivery-price').html()) || 0;
    const grandTotal = productsTotal + deliveryPrice;
        
        console.log('deliveryPrice', deliveryPrice);
    
    $('#order-total').html(
        new Intl.NumberFormat('ru-RU', {
            style: 'currency',
            currency: 'RUB'
        }).format(grandTotal)
    ).data('value', grandTotal);
}

JS;
$this->registerJs($deliveryPriceJs);
?>

<style>
    .help-block {
        color: red;
    }
    .card {
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
    }
    .form-control {
        border-radius: 4px;
    }
    .btn-primary {
        background-color: #007bff;
        border: none;
        padding: 12px;
    }
</style>

<div class="checkout-index">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php if ($isGuest): ?>
        <div class="guest-notice alert alert-info">
            <h4><i class="fas fa-info-circle"></i> Вы оформляете заказ как гость</h4>
            <p>Укажите email, чтобы мы могли:</p>
            <ul>
                <li>Создать для вас аккаунт</li>
                <li>Отправить данные о заказе</li>
                <li>Сохранить историю покупок</li>
            </ul>
        </div>
        <?php endif; ?>

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
                                'placeholder' => 'Фамилия',
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
                            'prompt' => '-- Выберите способ доставки --',
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
                        <span id="cart-total" data-value="<?= $totalSum ?>">
                            <?= Yii::$app->formatter->asCurrency($totalSum) ?>
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
                        <span id="order-total" data-value="<?= $totalSum ?>">
                            <?= Yii::$app->formatter->asCurrency($totalSum) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
