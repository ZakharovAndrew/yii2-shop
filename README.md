# 🚀 Yii2 Shop: Launch Your Online Store in Minutes!

<div align="center">

[![Latest Stable Version](https://poser.pugx.org/zakharov-andrew/yii2-shop/v/stable)](https://packagist.org/packages/zakharov-andrew/yii2-shop)
[![Total Downloads](https://poser.pugx.org/zakharov-andrew/yii2-shop/downloads)](https://packagist.org/packages/zakharov-andrew/yii2-shop)
[![License](https://poser.pugx.org/zakharov-andrew/yii2-shop/license)](https://packagist.org/packages/zakharov-andrew/yii2-shop)
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)

</div>

<p align="center">
Yii2 shop module. A module for creating an online store based on Yii2. This module provides basic functionality for managing products, categories, orders, and other elements of an e-commerce store.
</p>

<p align="center">
  <a href="#-features">Features</a> •
  <a href="#-installation">Installation</a> •
  <a href="#-usage">Usage</a> •
  <a href="#-contributing">Contributing</a> •
  <a href="#-license">License</a>
</p>

<p align="center">
  <a href="README.ru.md">🇷🇺 Русская версия</a>
</p>

---

## ✨ Features

- **📦 Product Management**
  - Create, edit, and delete products.
  - Add attributes and specifications to products.
  - Upload product images.
  - Manage product stock levels.

- **🗂️ Categories and Filters**
  - Hierarchical category system.
  - Support for subcategories.
  - Filter products by attributes (color, size, price, etc.).
  - Flexible filter configuration for categories.

- **📝 Order Management**
  - Create and manage orders.
  - Order statuses (new, processing, delivered, canceled, etc.).
  - Order history and change tracking.
  - Support for various delivery methods.

- **🛒 Shopping Cart and Checkout**
  - Add products to the cart.
  - Edit the cart (change quantities, remove products).
  - Checkout with customer details.
  - Coupon and discount support *(planned for future updates)*.

- **👤 User and Role Management**
  - User registration and authentication.
  - Role and permission management (admin, manager, customer).
  - User account with order history.

- **🔍 Search and SEO**
  - Search products by name, description, and attributes.
  - SEO optimization: meta tags, human-readable URLs (slug).
  - Sitemap generation (sitemap.xml).

- **🌐 Multilingual Support**
  - Support for multiple languages in the store interface.

- **📊 Analytics and Reports**
  - Sales reports.
  - Product popularity analysis.
  - Export data to CSV, Excel, and other formats.

- **⚙️ Store Settings**
  - Flexible configuration of core store parameters.
  - Email notification settings for customers and administrators.


## 🚀 Installation

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

## 🛠 Usage

Add this to your main configuration's modules array

```php
    'modules' => [
        'shop' => [
            'class' => 'ZakharovAndrew\shop\Module',
            'catalogTitle' => 'Catalog Title',
            'storeName' => 'My Store',
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

## 👥 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
