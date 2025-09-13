<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use ZakharovAndrew\shop\Module;

/* @var $this yii\web\View */
/* @var $model ZakharovAndrew\shop\models\ProductProperty */
/* @var $newOption ZakharovAndrew\shop\models\ProductPropertyOption */

$this->title = Module::t('Manage Options for: {name}', ['name' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Module::t('Product Properties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('Options');
?>
<div class="product-property-options">

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                    <div class="box-tools pull-right">
                        <?= Html::a('<i class="fa fa-arrow-left"></i> ' . Module::t('Back to Property'), ['view', 'id' => $model->id], ['class' => 'btn btn-default btn-sm']) ?>
                    </div>
                </div>
                <div class="box-body">

                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success">
                            <?= Yii::$app->session->getFlash('success') ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><?= Module::t('Add New Option') ?></h3>
                                </div>
                                <div class="box-body">
                                    <?php $form = ActiveForm::begin([
                                        'action' => ['add-option', 'property_id' => $model->id],
                                        'id' => 'add-option-form',
                                    ]); ?>

                                    <?= $form->field($newOption, 'value')->textInput(['maxlength' => true])->label(Module::t('Option Value')) ?>
                                    <?= $form->field($newOption, 'sort_order')->textInput(['type' => 'number'])->label(Module::t('Sort Order')) ?>

                                    <div class="form-group">
                                        <?= Html::submitButton(Module::t('Add Option'), ['class' => 'btn btn-success']) ?>
                                    </div>

                                    <?php ActiveForm::end(); ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><?= Module::t('Existing Options') ?></h3>
                                </div>
                                <div class="box-body">
                                    <?php if ($model->options): ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th><?= Module::t('Value') ?></th>
                                                        <th><?= Module::t('Sort Order') ?></th>
                                                        <th width="100"><?= Module::t('Actions') ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($model->options as $option): ?>
                                                        <tr>
                                                            <td><?= Html::encode($option->value) ?></td>
                                                            <td><?= $option->sort_order ?></td>
                                                            <td>
                                                                <?= Html::a(
                                                                    '<i class="fa fa-pencil"></i>',
                                                                    '#',
                                                                    [
                                                                        'class' => 'btn btn-xs btn-primary edit-option',
                                                                        'data' => [
                                                                            'id' => $option->id,
                                                                            'value' => $option->value,
                                                                            'sort' => $option->sort_order,
                                                                        ]
                                                                    ]
                                                                ) ?>
                                                                <?= Html::a(
                                                                    '<i class="fa fa-trash"></i>',
                                                                    ['delete-option', 'id' => $option->id],
                                                                    [
                                                                        'class' => 'btn btn-xs btn-danger',
                                                                        'data' => [
                                                                            'confirm' => Module::t('Are you sure you want to delete this option?'),
                                                                            'method' => 'post',
                                                                        ]
                                                                    ]
                                                                ) ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted"><?= Module::t('No options found') ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<?php
// Модальное окно для редактирования опции
$this->registerJs(<<<JS
    $(document).on('click', '.edit-option', function(e) {
        e.preventDefault();
        
        var optionId = $(this).data('id');
        var optionValue = $(this).data('value');
        var optionSort = $(this).data('sort');
        
        // Заполняем форму редактирования
        $('#edit-option-id').val(optionId);
        $('#edit-option-value').val(optionValue);
        $('#edit-option-sort').val(optionSort);
        
        // Показываем модальное окно
        $('#editOptionModal').modal('show');
    });
    
    $('#edit-option-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.post($(this).attr('action'), formData, function(response) {
            if (response.success) {
                $('#editOptionModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + JSON.stringify(response.errors));
            }
        });
    });
JS
);
?>

<!-- Модальное окно для редактирования опции -->
<div class="modal fade" id="editOptionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?= Module::t('Edit Option') ?></h4>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'edit-option-form',
                'action' => Url::to(['update-option']),
            ]); ?>
            <div class="modal-body">
                <input type="hidden" id="edit-option-id" name="Option[id]">
                
                <div class="form-group">
                    <label><?= Module::t('Option Value') ?></label>
                    <input type="text" id="edit-option-value" name="Option[value]" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label><?= Module::t('Sort Order') ?></label>
                    <input type="number" id="edit-option-sort" name="Option[sort_order]" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Module::t('Cancel') ?></button>
                <button type="submit" class="btn btn-primary"><?= Module::t('Save Changes') ?></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>