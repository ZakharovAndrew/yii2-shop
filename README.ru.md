<div align="center">
 
# 🚀 Yii2 Shop

[![Latest Stable Version](https://poser.pugx.org/zakharov-andrew/yii2-shop/v/stable)](https://packagist.org/packages/zakharov-andrew/yii2-shop)
[![Total Downloads](https://poser.pugx.org/zakharov-andrew/yii2-shop/downloads)](https://packagist.org/packages/zakharov-andrew/yii2-shop)
[![License](https://poser.pugx.org/zakharov-andrew/yii2-shop/license)](https://packagist.org/packages/zakharov-andrew/yii2-shop)
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)



</div>

<p align="center">
Модуль для создания интернет-магазина на базе Yii2. Этот модуль предоставляет базовый функционал для управления товарами, категориями, заказами и другими элементами интернет-магазина.
</p>

<p align="center">
  <a href="#-основные-возможности">Основные возможности</a> •
  <a href="#-установка">Установка</a> •
  <a href="#-вклад-в-проект">Вклад в проект</a> •
  <a href="#-лицензия">Лицензия</a>
</p>

<p align="center">
  <a href="README.md">🇺🇸 English</a>
</p>

---

## ✨ Основные возможности

- **📦 Управление товарами**
  - Создание, редактирование и удаление товаров.
  - Возможность добавления атрибутов и характеристик товаров.
  - Загрузка изображений для товаров.
  - Управление наличием товаров (остатки на складе).

- **🗂️ Категории и фильтры**
  - Иерархическая система категорий.
  - Возможность создания подкатегорий.
  - Фильтрация товаров по характеристикам (цвет, размер, цена и т.д.).
  - Гибкая настройка фильтров для категорий.

- **📝 Управление заказами**
  - Создание и управление заказами.
  - Статусы заказов (новый, в обработке, доставлен, отменен и т.д.).
  - История изменений заказов.
  - Поддержка различных способов доставки.

- **🛒 Корзина и оформление заказа**
  - Добавление товаров в корзину.
  - Редактирование корзины (изменение количества, удаление товаров).
  - Оформление заказа с указанием данных покупателя.
  - Поддержка купонов и скидок *(в будущем)*.

- **👤 Пользователи и роли**
  - Регистрация и авторизация пользователей.
  - Управление ролями и правами доступа (администратор, менеджер, покупатель).
  - Личный кабинет пользователя с историей заказов.

- **🔍 Поиск и SEO**
  - Поиск товаров по названию, описанию и характеристикам.
  - SEO-оптимизация: настройка мета-тегов, ЧПУ (человеко-понятные URL).
  - Генерация карты сайта (sitemap.xml).

- **🌐 Мультиязычность**
  - Поддержка нескольких языков для интерфейса магазина.

- **📊 Аналитика и отчеты**
  - Отчеты по продажам.
  - Анализ популярности товаров.
  - Экспорт данных в CSV, Excel и другие форматы.

- **⚙️ Настройки магазина**
  - Гибкая настройка основных параметров магазина.
  - Настройка email-уведомлений для покупателей и администраторов.


## 🚀 Установка

Предпочтительный способ установки этого расширения — через [composer](http://getcomposer.org/download/).

Вы можете выполнить следующую команду:

```
$ composer require zakharov-andrew/yii2-shop
```
или добавить

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

## 🛠 Usage

Add this to your main configuration's modules array

```php
    'modules' => [
        'shop' => [
            'class' => 'ZakharovAndrew\shop\Module',
            'catalogTitle' => 'Catalog Title',
            'bootstrapVersion' => 5, // if use bootstrap 5
            'params' => [
                '1' => [
                    'title' => [
                        'en-US' => 'Weight',
                        'ru' => 'Вес'
                    ]
                ]
            ],
            'deliveryMethods' => [
                1 => 'Courier delivery',
                2 => 'Pickup from store',
                3 => 'Postal delivery'
            ],
            'defaultProductImage' => '/images/default-product-image.jpg', // Path to the default image for a product
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

```php
'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // ...
                'catalog/<url:[\w\-]+>' => 'shop/product-category/view',
                'catalog' => 'shop/catalog/index',
                'product/<url:[\w\d\-]+>' => 'shop/product/view',
                'cart' => 'shop/cart/index',
                'checkout' => 'shop/checkout/index',
                'admin/orders' => 'shop/admin-order/index',
                'admin/orders/<id:\d+>' => 'shop/admin-order/view',
                'admin/orders/update-status/<id:\d+>' => 'shop/admin-order/update-status',
                // ...
            ],
        ],
```

## 👥 Вклад в проект

Вклады приветствуются! Пожалуйста, не стесняйтесь отправлять Pull Request.

1. Сделайте форк репозитория
2. Создайте новую ветку для своей фичи (`git checkout -b feature/amazing-feature`)
3. Закоммитьте изменения (`git commit -m 'Добавлена потрясающая фича'`)
4. Запушьте ветку (`git push origin feature/amazing-feature`)
5. Откройте Pull Request

## 📄 Лицензия

Этот проект лицензирован под лицензией MIT – см. файл [LICENSE](LICENSE) для подробностей.
