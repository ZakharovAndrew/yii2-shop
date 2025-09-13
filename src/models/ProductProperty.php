<?php

namespace ZakharovAndrew\shop\models;

use Yii;
use ZakharovAndrew\shop\Module;

/**
 * This is the model class for table "product_property".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $type
 * @property int $sort_order
 * @property bool $is_required
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ProductPropertyOption[] $options
 * @property ProductPropertyValue[] $values
 */
class ProductProperty extends \yii\db\ActiveRecord
{
    const TYPE_TEXT = 1;
    const TYPE_SELECT = 2;
    const TYPE_YEAR = 3;
    const TYPE_DATE = 4;
    const TYPE_CHECKBOX = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_property';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code', 'type'], 'required'],
            [['type', 'sort_order'], 'integer'],
            [['is_required', 'is_active'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 50],
            [['code'], 'unique'],
            ['type', 'in', 'range' => array_keys(self::getTypesList())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Module::t('Name'),
            'code' => Module::t('Code'),
            'type' => Module::t('Type'),
            'sort_order' => Module::t('Sort Order'),
            'is_required' => Module::t('Required'),
            'is_active' => Module::t('Active'),
            'created_at' => Module::t('Created At'),
            'updated_at' => Module::t('Updated At'),
        ];
    }

    /**
     * Get all available types list
     * @return array
     */
    public static function getTypesList()
    {
        return [
            self::TYPE_TEXT => Module::t('Text Field'),
            self::TYPE_SELECT => Module::t('Dropdown List'),
            self::TYPE_YEAR => Module::t('Year'),
            self::TYPE_DATE => Module::t('Date'),
            self::TYPE_CHECKBOX => Module::t('Checkbox'),
        ];
    }

    /**
     * Get type name
     * @return string
     */
    public function getTypeName()
    {
        $types = self::getTypesList();
        return $types[$this->type] ?? Module::t('Unknown Type');
    }

    /**
     * Gets query for [[Options]].
     * @return \yii\db\ActiveQuery
     */
    public function getOptions()
    {
        return $this->hasMany(ProductPropertyOption::class, ['property_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC]);
    }

    /**
     * Gets query for [[Values]].
     * @return \yii\db\ActiveQuery
     */
    public function getValues()
    {
        return $this->hasMany(ProductPropertyValue::class, ['property_id' => 'id']);
    }

    /**
     * Get active properties
     * @return array
     */
    public static function getActiveProperties()
    {
        return self::find()
            ->where(['is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC])
            ->all();
    }

    /**
     * Get properties by type
     * @param int $type
     * @return array
     */
    public static function getPropertiesByType($type)
    {
        return self::find()
            ->where(['type' => $type, 'is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC])
            ->all();
    }

    /**
     * Get options list for dropdown
     * @return array
     */
    public function getOptionsList()
    {
        $options = [];
        foreach ($this->options as $option) {
            $options[$option->id] = $option->value;
        }
        return $options;
    }

    /**
     * Check if property is select type
     * @return bool
     */
    public function isSelectType()
    {
        return $this->type === self::TYPE_SELECT;
    }

    /**
     * Check if property is checkbox type
     * @return bool
     */
    public function isCheckboxType()
    {
        return $this->type === self::TYPE_CHECKBOX;
    }

    /**
     * Check if property is date type
     * @return bool
     */
    public function isDateType()
    {
        return $this->type === self::TYPE_DATE;
    }

    /**
     * Check if property is year type
     * @return bool
     */
    public function isYearType()
    {
        return $this->type === self::TYPE_YEAR;
    }

    /**
     * Check if property is text type
     * @return bool
     */
    public function isTextType()
    {
        return $this->type === self::TYPE_TEXT;
    }

    /**
     * Generate unique code for property
     * @param string $name
     * @return string
     */
    public function generateUniqueCode($name)
    {
        $baseCode = \yii\helpers\Inflector::slug($name, '_');
        $code = $baseCode;
        $counter = 1;

        while (self::find()->where(['code' => $code])->exists()) {
            $code = $baseCode . '_' . $counter++;
        }

        return $code;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->code)) {
                $this->code = $this->generateUniqueCode($this->name);
            }
            return true;
        }
        return false;
    }

    /**
     * Get default type
     * @return int
     */
    public static function getDefaultType()
    {
        return self::TYPE_TEXT;
    }
}