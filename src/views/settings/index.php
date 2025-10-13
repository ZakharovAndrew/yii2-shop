<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\Module;
use ZakharovAndrew\shop\models\Settings;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\shop\models\SettingsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Shop Settings';
$this->params['breadcrumbs'][] = $this->title;

$shopSettings = Yii::$app->shopSettings;
$allSettings = $shopSettings->getAll();
?>
<div class="settings-index">

    <?php if (Yii::$app->getModule('shop')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="row mb-3">
        <div class="col-md-12">
            <?= Html::a('Admin Panel', ['admin'], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Module::t('Clear Cache'), ['clear-cache'], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Are you sure you want to clear settings cache?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <!-- Quick Settings Update Form -->
    <?php $form = \yii\widgets\ActiveForm::begin([
        'action' => ['bulk-update'],
        'method' => 'post',
        'id' => 'quick-settings-form',
    ]); ?>
    <div class="card">
        <div class="card-body">
            <?php if ($dataProvider->getCount() > 0): ?>
                <div class="row">
                    <?php 
                    $settingsModels = $dataProvider->getModels();
                    foreach ($settingsModels as $index => $setting): 
                        $currentValue = $allSettings[$setting->key] ?? $setting->value;
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="setting-card card h-100">
                            <div class="card-header py-2">
                                <h6 class="mb-0">
                                    <?= $setting->getDisplayName() ?>
                                    <!-- <small class="text-muted float-right"><?= $setting->key ?></small> -->
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if ($setting->type === Settings::TYPE_BOOLEAN): ?>
                                    <div class="form-group mb-0">
                                        <?= Html::dropDownList(
                                            "Settings[{$setting->key}]",
                                            $currentValue,
                                            [0 => Module::t('No'), 1 => Module::t('Yes')],
                                            ['class' => 'form-control form-select', 'id' => "setting-{$setting->key}"]
                                        ) ?>
                                    </div>
                                <?php elseif ($setting->type === Settings::TYPE_JSON): ?>
                                    <div class="form-group mb-0">
                                        <?php
                                        $jsonValue = $currentValue;
                                        if (is_array($jsonValue)) {
                                            $jsonValue = json_encode($jsonValue, JSON_PRETTY_PRINT);
                                        }
                                        ?>
                                        <?= Html::textarea(
                                            "Settings[{$setting->key}]",
                                            $jsonValue,
                                            [
                                                'class' => 'form-control', 
                                                'id' => "setting-{$setting->key}", 
                                                'rows' => 4,
                                                'placeholder' => 'Enter JSON data...'
                                            ]
                                        ) ?>
                                        <small class="form-text text-muted">JSON format required</small>
                                    </div>
                                <?php elseif ($setting->type === Settings::TYPE_INTEGER): ?>
                                    <div class="form-group mb-0">
                                        <?= Html::input(
                                            'number',
                                            "Settings[{$setting->key}]",
                                            $currentValue,
                                            [
                                                'class' => 'form-control', 
                                                'id' => "setting-{$setting->key}",
                                                'step' => '1'
                                            ]
                                        ) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="form-group mb-0">
                                        <?= Html::input(
                                            'text',
                                            "Settings[{$setting->key}]",
                                            $currentValue,
                                            [
                                                'class' => 'form-control', 
                                                'id' => "setting-{$setting->key}",
                                                'placeholder' => 'Enter value...'
                                            ]
                                        ) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-tag"></i> Type: <?= ucfirst($setting->type) ?>
                                        <?php if ($setting->name): ?>
                                            | <i class="fas fa-info-circle"></i> <?= Html::encode($setting->name) ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                    <h4>No Settings Found</h4>
                    <p class="text-muted">There are no settings configured yet.</p>
                    <?= Html::a('Go to Admin Panel to Create Settings', ['admin'], [
                        'class' => 'btn btn-primary'
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="form-group mt-4">
        <?= Html::submitButton(Module::t('Update All Settings'), [
            'class' => 'btn btn-success btn-lg'
        ]) ?>
        <?= Html::a(Module::t('Cancel'), ['index'], [
            'class' => 'btn btn-outline-danger'
        ]) ?>
    </div>
    <?php \yii\widgets\ActiveForm::end(); ?>

</div>

<?php
// JavaScript for form functionality
$this->registerJs(<<<JS
        
    // JSON validation for JSON fields
    $('textarea[id^="setting-"]').on('blur', function() {
        var textarea = $(this);
        var value = textarea.val().trim();
        
        if (value !== '') {
            try {
                JSON.parse(value);
                textarea.removeClass('is-invalid').addClass('is-valid');
            } catch (e) {
                textarea.removeClass('is-valid').addClass('is-invalid');
                // Show error tooltip
                textarea.attr('title', 'Invalid JSON: ' + e.message).tooltip('show');
            }
        } else {
            textarea.removeClass('is-invalid is-valid');
            textarea.tooltip('hide');
        }
    });
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
JS
);
?>

<style>
.setting-card {
    transition: all 0.3s ease;
}

.setting-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.form-control.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
}

.form-control.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
}

.card-header h6 {
    font-size: 0.9rem;
    font-weight: 600;
}
</style>