<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\shop\assets\ShopAssets;
ShopAssets::register($this);

$bootstrapVersion = Yii::$app->getModule('user')->bootstrapVersion;
$classModal = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Modal";
$classButtonDropdown = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\ButtonDropdown";

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\Shop $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerJsFile('https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js');
?>

<div class="shop-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'telegram')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'whatsapp')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?php if (\Yii::$app->user->identity->isAdmin()) {?>
    <!-- Секция для Telegram групп -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fab fa-telegram text-primary"></i>
                <?= Module::t('Telegram Groups') ?>
            </h5>
        </div>
        <div class="card-body">
            <div id="linked-telegram-groups">
                <div class="mb-3">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-bs-toggle="modal" data-target="#telegramGroupsModal" data-bs-target="#telegramGroupsModal">
                        <i class="fas fa-plus"></i> <?= Module::t('Add Telegram Group') ?>
                    </button>
                </div>
                
                <div id="telegram-groups-list">
                    <!-- Здесь будет динамически загружаться список привязанных групп -->
                    <?php if (!$model->isNewRecord): ?>
                        <?= $this->render('../shop-telegram-groups/_telegram_groups_list', ['model' => $model]) ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <?= Module::t('Save the shop first to be able to link Telegram groups.') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php }?>

    <?= $form->field($model, 'description_after_products')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'avatar')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<!-- modal "Telegram groups" -->
<?php $classModal::begin([
        'id' => 'telegramGroupsModal',
        'options' =>  ['class' => 'modal modal-lg'],
        ($bootstrapVersion==3 ? 'header' : 'title') => '<h2>'.Module::t('Select Telegram Group').'</h2>',
        'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal" data-bs-dismiss="modal">' . Module::t('Close') . '</button><button type="button" class="btn btn-primary" id="link-selected-groups">
                    <i class="fas fa-link"></i>'.  Module::t('Link Selected Groups').'</button>'
    ]) ?>

    <div class="modal-body">
        <!-- Поиск групп -->
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control" id="telegram-group-search" 
                       placeholder="<?= Module::t('Search by title or URL...') ?>">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="search-telegram-groups">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Список доступных групп -->
        <div id="telegram-groups-container">
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only"><?= Yii::t('app', 'Loading...') ?></span>
                </div>
            </div>
        </div>
    </div>

    <?php $classModal::end() ?>

<?php
$url_unlink_from_shop = Url::to(['/shop/shop-telegram-groups/unlink-from-shop']);
$url_link_to_shop = Url::to(['/shop/shop-telegram-groups/link-to-shop']);
$url_list_available = Url::to(['/shop/shop-telegram-groups/list-available']);
$url_shop_groups = Url::to(['/shop/shop-telegram-groups/shop-groups', 'id' => $model->id]);

$script = <<< JS
// Initialize CKEditor for description
if (document.querySelector('#shop-description')) {
    ClassicEditor
        .create(document.querySelector('#shop-description'))
        .catch(error => {
            console.error(error);
        });
}

// Initialize CKEditor for description after products
if (document.querySelector('#shop-description_after_products')) {
    ClassicEditor
        .create(document.querySelector('#shop-description_after_products'))
        .catch(error => {
            console.error(error);
        });
}

// Загрузка Telegram групп при открытии модального окна
$('#telegramGroupsModal').on('show.bs.modal', function () {
    loadTelegramGroups();
});

// Поиск Telegram групп
$('#search-telegram-groups').on('click', function() {
    loadTelegramGroups($('#telegram-group-search').val());
});

$('#telegram-group-search').on('keypress', function(e) {
    if (e.which === 13) {
        loadTelegramGroups($(this).val());
    }
});

// Функция загрузки Telegram групп
function loadTelegramGroups(searchQuery = '') {
    $('#telegram-groups-container').html(`
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    `);
    
    $.get('$url_list_available', {
        shop_id: {$model->id},
        search: searchQuery
    }, function(data) {
        $('#telegram-groups-container').html(data);
    }).fail(function() {
        $('#telegram-groups-container').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                Error loading Telegram groups
            </div>
        `);
    });
}

// Привязка выбранных групп
$('#link-selected-groups').on('click', function() {
    var selectedGroups = [];
    $('.telegram-group-checkbox:checked').each(function() {
        selectedGroups.push($(this).val());
    });
    
    if (selectedGroups.length === 0) {
        alert('Please select at least one group');
        return;
    }
    
    var \$button = $(this);
    \$button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Linking...');
    
    $.post('$url_link_to_shop', {
        shop_id: {$model->id},
        group_ids: selectedGroups
    }, function(response) {
        if (response.success) {
            $('#telegramGroupsModal').modal('hide');
            // Обновляем список привязанных групп
            $.get('$url_shop_groups', function(data) {
                $('#telegram-groups-list').html(data);
            });
        } else {
            alert('Error: ' + (response.message || 'Unknown error'));
        }
    }).fail(function() {
        alert('Server error');
    }).always(function() {
        \$button.prop('disabled', false).html('<i class="fas fa-link"></i> Link Selected Groups');
    });
});

// Удаление привязанной группы
$(document).on('click', '.unlink-telegram-group', function(e) {
    e.preventDefault();
    
    if (!confirm('Are you sure you want to unlink this group?')) {
        return false;
    }
    
    var \$button = $(this);
    var groupId = \$button.data('group-id');
    
    \$button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
    
    $.post('$url_unlink_from_shop', {
        shop_id: {$model->id},
        group_id: groupId
    }, function(response) {
        if (response.success) {
            // Обновляем список привязанных групп
            $.get('$url_shop_groups', function(data) {
                $('#telegram-groups-list').html(data);
            });
        } else {
            alert('Error: ' + (response.message || 'Unknown error'));
        }
    }).fail(function() {
        alert('Server error');
    });
});
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>