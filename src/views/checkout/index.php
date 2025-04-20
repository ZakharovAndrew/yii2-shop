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

    <?= $this->render('_form', [
        'model' => $model,
        'isGuest' => $isGuest,
        'deliveryMethods' => $deliveryMethods,
        'totalSum' => $totalSum
    ]) ?>
</div>
