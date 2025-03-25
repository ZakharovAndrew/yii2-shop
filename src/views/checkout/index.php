<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\shop\models\Order;

/** @var yii\web\View $this */
/** @var app\modules\shop\models\Order $model */
/** @var ActiveForm $form */

$this->title = Module::t('Checkout');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'order-form',
        'enableAjaxValidation' => true,
    ]); ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'delivery_method')->dropDownList(
        [
            1 => Yii::t('app', 'Courier delivery'),
            2 => Yii::t('app', 'Pickup from store'),
            3 => Yii::t('app', 'Postal delivery'),
        ],
        ['prompt' => Yii::t('app', 'Select delivery method')]
    ) ?>
    
    <?= $form->field($model, 'postcode')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'address')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit Order'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div><!-- order-create -->