# Yii2 Shop

[![Latest Stable Version](https://poser.pugx.org/zakharov-andrew/yii2-shop/v/stable)](https://packagist.org/packages/zakharov-andrew/yii2-shop)
[![Total Downloads](https://poser.pugx.org/zakharov-andrew/yii2-shop/downloads)](https://packagist.org/packages/zakharov-andrew/yii2-shop)
[![License](https://poser.pugx.org/zakharov-andrew/yii2-shop/license)](https://packagist.org/packages/zakharov-andrew/yii2-shop)
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)

Yii2 shop module.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ composer require zakharov-andrew/yii2-shop
```
or add

```
"zakharov-andrew/yii2-shop": "*"
```

to the ```require``` section of your ```composer.json``` file.

Subsequently, run

```
./yii migrate/up --migrationPath=@vendor/zakharov-andrew/yii2-shop/migrations
```

in order to create the settings table in your database.

Or add to console config

```php
return [
    // ...
    'controllerMap' => [
        // ...
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => [
                '@console/migrations', // Default migration folder
                '@vendor/zakharov-andrew/yii2-shop/src/migrations'
            ]
        ]
        // ...
    ]
    // ...
];
```

## Usage

Add this to your main configuration's modules array

```
    'modules' => [
        'shop' => [
            'class' => 'ZakharovAndrew\shop\Module',
            'catalogTitle' => 'Catalog Title',
            'bootstrapVersion' => 5, // if use bootstrap 5
            'params' => [
                '1' => [
                    'title' => [
                        'en' => 'Weight',
                        'ru' => 'Вес'
                    ]
                ]
            ],
            'uploadWebDir' => '/web/path/to/upload/dir/'
        ],
        'imageupload' => [
            'class' => 'ZakharovAndrew\imageupload\Module',
            'uploadDir' => '/path/to/upload/dir/',
        ],
        // ...
    ],
```
**Note**: the maximum number of additional parameters is 3. Change the value of **uploadDir** to the directory for uploading images. Uses the [yii2-image-upload-widget](https://github.com/ZakharovAndrew/yii2-image-upload-widget) module to upload images.

Add this to your main configuration's urlManager array

```
'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // ...
                'catalog/<url:[\w\-]+>' => 'shop/product-category/view',
                'catalog' => 'shop/catalog/index',
                'product/<url:[\w\d\-]+>' => 'shop/product/view',
                // ...
            ],
        ],
```

## License

**yii2-shop** it is available under a MIT License. Detailed information can be found in the `LICENSE.md`.
