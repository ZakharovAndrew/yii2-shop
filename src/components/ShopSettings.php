<?php

namespace ZakharovAndrew\shop\components;

use Yii;
use yii\base\Component;
use ZakharovAndrew\shop\models\Settings;

class ShopSettings extends Component
{
    private $_settings = [];
    
    public function init()
    {
        parent::init();
        $this->loadSettings();
    }
    
    public function loadSettings()
    {
        $this->_settings = Yii::$app->cache->getOrSet('shop_settings', function() {
            return Settings::find()
                ->select(['key', 'value', 'type'])
                ->indexBy('key')
                ->asArray()
                ->all();
        });
    }
    
    public function get($key, $default = null)
    {
        if (isset($this->_settings[$key])) {
            return Settings::castValue(
                $this->_settings[$key]['value'], 
                $this->_settings[$key]['type']
            );
        }
        
        return $default;
    }
    
    public function set($key, $value)
    {
        $result = Settings::setValue($key, $value);
        
        if ($result) {
            Yii::$app->cache->delete('shop_settings');
            $this->loadSettings();
        }
        
        return $result;
    }
    
    public function getAll()
    {
        $result = [];
        foreach ($this->_settings as $key => $setting) {
            $result[$key] = $this->get($key);
        }
        return $result;
    }
    
    /**
     * Clear all settings cache
     * @return bool
     */
    public function clearCache()
    {
        try {
            // Delete the main settings cache
            Yii::$app->cache->delete('shop_settings');
            
            // Reload settings from database
            $this->loadSettings();
            
            return true;
        } catch (\Exception $e) {
            Yii::error('Failed to clear shop settings cache: ' . $e->getMessage(), __METHOD__);
            return false;
        }
    }
}