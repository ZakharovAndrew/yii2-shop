<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var ZakharovAndrew\shop\models\Shop $model */
?>

<?php if ($model->telegramGroups): ?>
    <div class="list-group">
        <?php foreach ($model->telegramGroups as $group): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1"><?= Html::encode($group->title) ?></h6>
                    <small class="text-muted"><?= Html::encode($group->telegram_url) ?></small>
                    <?php if ($group->telegram_chat_id): ?>
                        <br><small class="text-info">Chat ID: <?= Html::encode($group->telegram_chat_id) ?></small>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($group->is_active): ?>
                        <span class="badge badge-success mr-2"><?= Yii::t('app', 'Active') ?></span>
                    <?php else: ?>
                        <span class="badge badge-secondary mr-2"><?= Yii::t('app', 'Inactive') ?></span>
                    <?php endif; ?>
                    <button type="button" class="btn btn-sm btn-outline-danger unlink-telegram-group" 
                            data-group-id="<?= $group->id ?>" title="<?= Yii::t('app', 'Unlink group') ?>">
                        <i class="fas fa-unlink"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        <i class="fas fa-info-circle"></i>
        <?= Yii::t('app', 'No Telegram groups linked to this shop.') ?>
    </div>
<?php endif; ?>