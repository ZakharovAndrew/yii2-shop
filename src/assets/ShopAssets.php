<?php
/**
 * @link https://github.com/ZakharovAndrew/yii2-user
 * @copyright Copyright (c) 2024 Zakharov Andrey
 */

namespace ZakharovAndrew\shop\assets;

use yii\web\AssetBundle;

class ShopAssets extends AssetBundle
{
    public $sourcePath = '@vendor/zakharov-andrew/yii2-shop/src/assets';

    public $css = [
        'css/style_v1.css',
    ];

    public $js = [
        'js/shop.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        //'yii\bootstrap5\BootstrapAsset',
    ];
}