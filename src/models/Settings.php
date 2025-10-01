<?php

namespace ZakharovAndrew\shop\models;

use Yii;
use yii\db\ActiveRecord;
use ZakharovAndrew\shop\Module;

/**
 * Shop settings model
 * 
 * @property int $id Unique setting ID
 * @property string $key Setting key name
 * @property string|null $name Human readable setting name
 * @property string|null $value Setting value
 * @property string $type Value type: string, integer, boolean, json
 * @property string $created_at Record creation timestamp
 * @property string $updated_at Record update timestamp
 */
class Settings extends ActiveRecord
{
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_JSON = 'json';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_settings}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key'], 'required'],
            [['value', 'name'], 'string'],
            [['type'], 'string'],
            [['key'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 255],
            [['key'], 'unique'],
            [['type'], 'in', 'range' => [self::TYPE_STRING, self::TYPE_INTEGER, self::TYPE_BOOLEAN, self::TYPE_JSON]],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => Module::t('Key'),
            'name' => Module::t('Name'),
            'value' => Module::t('Value'),
            'type' => Module::t('Type'),
            'created_at' => Module::t('Created At'),
            'updated_at' => Module::t('Updated At'),
        ];
    }

    /**
     * Get setting value by key
     * @param string $key Setting key
     * @param mixed $default Default value if setting not found
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::findOne(['key' => $key]);
        
        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Get setting with all attributes by key
     * @param string $key Setting key
     * @return Settings|null
     */
    public static function getSetting($key)
    {
        return self::findOne(['key' => $key]);
    }

    /**
     * Set setting value
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @param string|null $name Human readable name (optional)
     * @return bool
     */
    public static function setValue($key, $value, $name = null)
    {
        $setting = self::findOne(['key' => $key]);
        
        if (!$setting) {
            $setting = new self();
            $setting->key = $key;
            $setting->type = self::detectType($value);
            if ($name !== null) {
                $setting->name = $name;
            }
        }

        $setting->value = self::prepareValue($value, $setting->type);
        if ($name !== null) {
            $setting->name = $name;
        }
        
        return $setting->save();
    }

    /**
     * Get multiple settings at once
     * @param array $keys Array of setting keys
     * @return array
     */
    public static function getMultiple($keys)
    {
        $settings = self::find()
            ->where(['key' => $keys])
            ->indexBy('key')
            ->all();
        
        $result = [];
        foreach ($keys as $key) {
            if (isset($settings[$key])) {
                $result[$key] = self::castValue($settings[$key]->value, $settings[$key]->type);
            } else {
                $result[$key] = null;
            }
        }
        
        return $result;
    }

    /**
     * Set multiple settings at once
     * @param array $settings Array of key-value pairs
     * @return bool
     */
    public static function setMultiple($settings)
    {
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            foreach ($settings as $key => $data) {
                if (is_array($data)) {
                    $value = $data['value'] ?? null;
                    $name = $data['name'] ?? null;
                } else {
                    $value = $data;
                    $name = null;
                }
                
                if (!self::setValue($key, $value, $name)) {
                    $transaction->rollBack();
                    return false;
                }
            }
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * Check if setting exists
     * @param string $key Setting key
     * @return bool
     */
    public static function exists($key)
    {
        return self::find()->where(['key' => $key])->exists();
    }

    /**
     * Remove setting
     * @param string $key Setting key
     * @return bool
     */
    public static function remove($key)
    {
        $setting = self::findOne(['key' => $key]);
        if ($setting) {
            return $setting->delete();
        }
        return true;
    }

    /**
     * Get all settings as key-value array
     * @return array
     */
    public static function getAll()
    {
        $settings = self::find()->all();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = [
                'value' => self::castValue($setting->value, $setting->type),
                'name' => $setting->name,
                'type' => $setting->type,
            ];
        }
        
        return $result;
    }

    /**
     * Get all settings with full information
     * @return array
     */
    public static function getAllSettings()
    {
        return self::find()
            ->orderBy(['key' => SORT_ASC])
            ->all();
    }

    /**
     * Get settings for form (key => name)
     * @return array
     */
    public static function getSettingsForDropdown()
    {
        $settings = self::find()
            ->select(['key', 'name'])
            ->where(['IS NOT', 'name', null])
            ->orderBy(['name' => SORT_ASC])
            ->all();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->name ?: $setting->key;
        }
        
        return $result;
    }

    /**
     * Cast value based on type
     * @param string $value
     * @param string $type
     * @return mixed
     */
    public static function castValue($value, $type)
    {
        switch ($type) {
            case self::TYPE_INTEGER:
                return (int)$value;
            case self::TYPE_BOOLEAN:
                return (bool)$value;
            case self::TYPE_JSON:
                return json_decode($value, true) ?: [];
            default:
                return $value;
        }
    }

    /**
     * Prepare value for saving
     * @param mixed $value
     * @param string $type
     * @return string
     */
    private static function prepareValue($value, $type)
    {
        switch ($type) {
            case self::TYPE_JSON:
                return json_encode($value);
            case self::TYPE_BOOLEAN:
                return $value ? '1' : '0';
            default:
                return (string)$value;
        }
    }

    /**
     * Detect value type
     * @param mixed $value
     * @return string
     */
    private static function detectType($value)
    {
        if (is_int($value)) {
            return self::TYPE_INTEGER;
        } elseif (is_bool($value)) {
            return self::TYPE_BOOLEAN;
        } elseif (is_array($value)) {
            return self::TYPE_JSON;
        } else {
            return self::TYPE_STRING;
        }
    }

    /**
     * Before save event handler
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // Auto-detect type if not set
        if ($insert && empty($this->type)) {
            $this->type = self::detectType($this->value);
        }

        return true;
    }

    /**
     * Get human readable name (fallback to key if name is empty)
     * @return string
     */
    public function getDisplayName()
    {
        return $this->name ?: $this->key;
    }
}