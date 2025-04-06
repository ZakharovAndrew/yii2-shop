<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Update Stock: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update Stock';

?>
<div class="product-update-stock">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="product-form">
        <?php $form = ActiveForm::begin(); ?>
        
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'quantity')->textInput([
                    'type' => 'number',
                    'min' => 1,
                    'value' => 1
                ]) ?>
            </div>
            <div class="col-md-6">
                <label>Comment</label>
                <?= Html::input('text', 'comment', '', ['class' => 'form-control']) ?>
            </div>
        </div>
        
        <div class="form-group">
            <?= Html::submitButton('Add to Stock', ['class' => 'btn btn-success']) ?>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>

</div>