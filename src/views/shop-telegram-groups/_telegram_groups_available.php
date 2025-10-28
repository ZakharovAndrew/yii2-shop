<?php

use yii\helpers\Html;
use ZakharovAndrew\shop\Module;

/** @var ShopTelegramGroups[] $groups */
/** @var Shop $shop */
?>

<style>
    .telegram-group-checkbox {
        margin: 1em 1em 1em 0;
    }
    #telegram-groups-container .list-group-item:hover {
        background: #f5fbff;
    }
    .telegram-group-checkbox:checked + label {
        background: red;
    }
</style>

<?php if (empty($groups)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <?= Module::t('No available Telegram groups found.') ?>
    </div>
<?php else: ?>
    <div class="list-group">
        <?php foreach ($groups as $group): ?>
            <label class="list-group-item d-flex align-items-center">
                <div>
                    <input type="checkbox" class="telegram-group-checkbox" value="<?= $group->id ?>">
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1"><?= Html::encode($group->title) ?></h6>
                    <small class="text-muted"><?= Html::encode($group->telegram_url) ?></small>
                    <?php if ($group->telegram_chat_id): ?>
                        <br><small class="text-success">Chat ID: <?= Html::encode($group->telegram_chat_id) ?></small>
                    <?php endif; ?>
                </div>
                <div class="text-right">
                    <?= Html::a('', $group->telegram_url, [
                        'target' => '_blank',
                        'class' => 'btn-social icon-telegram',
                        'title' => Module::t('Open in Telegram')
                    ]) ?>
                </div>
            </label>
        <?php endforeach; ?>
    </div>
<?php endif; ?>