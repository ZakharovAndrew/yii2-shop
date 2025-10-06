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
 * @property int $position
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
    
    public $changeOptions = true;

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
            [['type', 'position'], 'integer'],
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
            'position' => Module::t('Sort Order'),
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
            ->orderBy(['position' => SORT_ASC]);
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
            ->orderBy(['position' => SORT_ASC])
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
            ->orderBy(['position' => SORT_ASC])
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
        return (int)$this->type === self::TYPE_SELECT;
    }

    /**
     * Check if property is checkbox type
     * @return bool
     */
    public function isCheckboxType()
    {
        return (int)$this->type === self::TYPE_CHECKBOX;
    }

    /**
     * Check if property is date type
     * @return bool
     */
    public function isDateType()
    {
        return (int)$this->type === self::TYPE_DATE;
    }

    /**
     * Check if property is year type
     * @return bool
     */
    public function isYearType()
    {
        return (int)$this->type === self::TYPE_YEAR;
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
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Сохраняем опции для выпадающего списка
        if ($this->isSelectType()) {
            if ($this->changeOptions) {
                $this->saveOptions();
            }
        } else {
            // Удаляем все опции, если тип изменился не на SELECT
            ProductPropertyOption::deleteAll(['property_id' => $this->id]);
        }
    }

    /**
     * Сохранение опций выпадающего списка
     */
    public function saveOptions()
    {
        $postOptions = Yii::$app->request->post('options', []);
        
        // Удаляем опции, которые были удалены из формы
        $existingOptionIds = [];
        foreach ($postOptions as $key => $options) {
            if (is_numeric($key)) { // Существующие опции
                $existingOptionIds[] = $key;
            }
        }
        
        // Удаляем опции, которых нет в форме
        if (!empty($existingOptionIds)) {
            ProductPropertyOption::deleteAll([
                'and',
                ['property_id' => $this->id],
                ['not in', 'id', $existingOptionIds]
            ]);
        } else {
            ProductPropertyOption::deleteAll(['property_id' => $this->id]);
        }
        
        // Сохраняем существующие опции
        foreach ($postOptions as $optionId => $optionData) {
            if (is_numeric($optionId)) { // Существующая опция
                $option = ProductPropertyOption::findOne($optionId);
                if ($option && $option->property_id == $this->id) {
                    $option->value = $optionData['value'] ?? '';
                    $option->sort_order = $optionData['sort_order'] ?? 0;
                    $option->save();
                }
            } else if ($optionId === 'new') { // Новые опции
                foreach ($optionData as $newOption) {
                    $option = new ProductPropertyOption();
                    $option->property_id = $this->id;
                    $option->value = $newOption['value'] ?? '';
                    $option->sort_order = $newOption['sort_order'] ?? 0;
                    $option->save();
                }
            }
        }
    }

    /**
     * Получить опции в формате для формы
     */
    public function getOptionsForForm()
    {
        $options = [];
        foreach ($this->options as $option) {
            $options[$option->id] = [
                'value' => $option->value,
                'sort_order' => $option->sort_order
            ];
        }
        return $options;
    }

    /**
     * Get default type
     * @return int
     */
    public static function getDefaultType()
    {
        return self::TYPE_TEXT;
    }
    
    /**
     * Move property position up
     * @return bool
     */
    public function moveUp()
    {
        $previous = self::find()
            ->where(['<', 'position', $this->position])
            ->orderBy(['position' => SORT_DESC])
            ->one();
            
        if ($previous) {
            $tempPosition = $this->position;
            $this->position = $previous->position;
            $previous->position = $tempPosition;
            
            return $this->save(false) && $previous->save(false);
        }
        
        return false;
    }

    /**
     * Move property position down
     * @return bool
     */
    public function moveDown()
    {
        $next = self::find()
            ->where(['>', 'position', $this->position])
            ->orderBy(['position' => SORT_ASC])
            ->one();
            
        if ($next) {
            $tempPosition = $this->position;
            $this->position = $next->position;
            $next->position = $tempPosition;
            
            return $this->save(false) && $next->save(false);
        }
        
        return false;
    }
}