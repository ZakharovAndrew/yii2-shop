<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\models\Settings;
use ZakharovAndrew\shop\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\Settings $model */

$this->title = 'Update Setting: ' . $model->getDisplayName();
$this->params['breadcrumbs'][] = ['label' => 'Shop Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getDisplayName(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="settings-update">
    
    <?php if (Yii::$app->getModule('shop')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?php $form = ActiveForm::begin(); ?>
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'key')->textInput([
                                'maxlength' => true,
                                'readonly' => true,
                                'class' => 'form-control bg-light'
                            ])->hint('Setting key cannot be changed after creation') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'type')->textInput([
                                'readonly' => true,
                                'class' => 'form-control bg-light'
                            ])->hint('Type cannot be changed after creation') ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?php if ($model->type === Settings::TYPE_BOOLEAN): ?>
                        <?= $form->field($model, 'value')->dropDownList([
                            '0' => Module::t('No'),
                            '1' => Module::t('Yes')
                        ], ['class' => 'form-control form-select']) ?>
                    
                    <?php elseif ($model->type === Settings::TYPE_JSON): ?>
                        <?= $form->field($model, 'value')->textarea([
                            'rows' => 6,
                            'class' => 'form-control',
                            'placeholder' => 'Enter valid JSON data...'
                        ])->hint('Must be valid JSON format') ?>
                    
                    <?php elseif ($model->type === Settings::TYPE_INTEGER): ?>
                        <?= $form->field($model, 'value')->textInput([
                            'type' => 'number',
                            'class' => 'form-control',
                            'step' => '1'
                        ]) ?>
                    
                    <?php else: ?>
                        <?= $form->field($model, 'value')->textarea([
                            'rows' => 3,
                            'class' => 'form-control',
                            'placeholder' => 'Enter setting value...'
                        ]) ?>
                    <?php endif; ?>

                    <div class="form-group">
                        <?= Html::submitButton('<i class="fas fa-save"></i> Update Setting', [
                            'class' => 'btn btn-success'
                        ]) ?>
                        <?= Html::a('<i class="fas fa-times"></i> Cancel', ['admin'], [
                            'class' => 'btn btn-outline-secondary'
                        ]) ?>
                        <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this setting?',
                                'method' => 'post',
                            ],
                        ]) ?>
                    </div>

                </div>
            </div>
                    <?php ActiveForm::end(); ?>
        </div>

</div>

<?php
// JavaScript for JSON validation
if ($model->type === Settings::TYPE_JSON) {
    $this->registerJs(<<<JS
        $('#settings-value').on('blur', function() {
            var textarea = $(this);
            var value = textarea.val().trim();
            
            if (value !== '') {
                try {
                    JSON.parse(value);
                    textarea.removeClass('is-invalid').addClass('is-valid');
                    $('#json-error').remove();
                } catch (e) {
                    textarea.removeClass('is-valid').addClass('is-invalid');
                    $('#json-error').remove();
                    textarea.after('<div id="json-error" class="invalid-feedback">Invalid JSON: ' + e.message + '</div>');
                }
            } else {
                textarea.removeClass('is-invalid is-valid');
                $('#json-error').remove();
            }
        });
        
        // Validate on page load
        $('#settings-value').trigger('blur');
JS
    );
}
?>

<style>
.form-control.bg-light {
    background-color: #f8f9fa !important;
    cursor: not-allowed;
}

pre {
    font-size: 0.8rem;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.25rem;
}

.badge {
    font-size: 0.75rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header h5, .card-header h6 {
    font-weight: 600;
}

dl dt {
    font-weight: 600;
    font-size: 0.875rem;
    color: #6c757d;
}

dl dd {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}
</style>