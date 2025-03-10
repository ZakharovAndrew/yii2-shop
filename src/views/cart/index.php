<?php
use yii\helpers\Html;

$this->title = 'Корзина';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cart-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (empty($cartItems)): ?>
    <div class="empty-basket text-center"><img src="/empty-basket.png" class="img-responsive img-fluid"><h3>Корзина пустая</h3><p>Добавьте товары из магазина</p></div>
        <p>Ваша корзина пуста.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Количество</th>
                    <th>Цена</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?= Html::encode($item['product']->title) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= $item['product']->price ?></td>
                        <td><?= $item['product']->price * $item['quantity'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?= Html::a('Очистить корзину', ['cart/clear'], ['class' => 'btn btn-danger']) ?>
    <?php endif; ?>
</div>