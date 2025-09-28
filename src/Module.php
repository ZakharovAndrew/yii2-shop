<?php

/**
 * Yii2 Shop
 * *************
 * Yii2 shop with database module with GUI manager supported.
 *  
 * @link https://github.com/ZakharovAndrew/yii2-shop/
 * @copyright Copyright (c) 2023-2025 Zakharov Andrew
 */
 
namespace ZakharovAndrew\shop;

use Yii;

/**
 * Yii2 Shop Module 
 */
class Module extends \yii\base\Module
{
    public $deliveryMethods = [
        1 => 'Courier delivery',
        2 => 'Pickup from store',
        3 => 'Postal delivery'
    ];
    
    public $deliveryPrices = [
        1 => '100',
        2 => '110',
        3 => '120'
    ];
    
    public $bootstrapVersion = '';
    public $catalogTitle = 'Catalog Title';
    public $uploadWebDir = '';
    
    public $catalogPageID = null;
    public $productPerPage = 100;
 
    public $useTranslite = false;

    public $mobileProductsPerRow = 1;

    /**
     * @var boolean Multi-store support 
     */
    public $multiStore = false;
    
    /**
     * @var string Path to the default image for a product
     */
    public $defaultProductImage = '/img/no-photo.jpg';
    
    /**
     * @var string show H1
     */
    public $showTitle = true;
    
    /**
     * @var string Store name
     */
    public $storeName = 'My Store';
    
    /**
     *
     * @var string source language for translation 
     */
    public $sourceLanguage = 'en-US';
    
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'ZakharovAndrew\shop\controllers';

    /**
     * {@inheritdoc}
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        
        self::registerTranslations();
    }
    
    /**
     * Registers the translation files
     */
    protected static function registerTranslations()
    {
        if (isset(Yii::$app->i18n->translations['extension/yii2-shop/*'])) {
            return;
        }
        
        Yii::$app->i18n->translations['extension/yii2-shop/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@vendor/zakharov-andrew/yii2-shop/src/messages',
            'on missingTranslation' => ['app\components\TranslationEventHandler', 'handleMissingTranslation'],
            'fileMap' => [
                'extension/yii2-shop/shop' => 'shop.php',
            ],
        ];
    }

    /**
     * Translates a message. This is just a wrapper of Yii::t
     *
     * @see Yii::t
     *
     * @param $category
     * @param $message
     * @param array $params
     * @param null $language
     * @return string
     */
    public static function t($message, $params = [], $language = null)
    {
        static::registerTranslations();
        
        $category = 'shop';
        
        return Yii::t('extension/yii2-shop/' . $category, $message, $params, $language);
    }
    
}
