<?php

namespace ZakharovAndrew\shop\models;

use Yii;
use ZakharovAndrew\shop\Module;

/**
 * This is the model class for table "product_property_value".
 *
 * @property int $id
 * @property int $product_id
 * @property int $property_id
 * @property string|null $value_text
 * @property int|null $value_int
 * @property string|null $value_date
 * @property bool|null $value_bool
 * @property int|null $option_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Product $product
 * @property ProductProperty $property
 * @property ProductPropertyOption $option
 */
class ProductPropertyValue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_property_value';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'property_id'], 'required'],
            [['product_id', 'property_id', 'value_int', 'option_id'], 'integer'],
            [['value_text'], 'string'],
            [['value_date'], 'safe'],
            [['value_bool'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['product_id', 'property_id'], 'unique', 'targetAttribute' => ['product_id', 'property_id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductProperty::class, 'targetAttribute' => ['property_id' => 'id']],
            [['option_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductPropertyOption::class, 'targetAttribute' => ['option_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => Module::t('Product'),
            'property_id' => Module::t('Property'),
            'value_text' => Module::t('Text Value'),
            'value_int' => Module::t('Integer Value'),
            'value_date' => Module::t('Date Value'),
            'value_bool' => Module::t('Boolean Value'),
            'option_id' => Module::t('Option'),
            'created_at' => Module::t('Created At'),
            'updated_at' => Module::t('Updated At'),
        ];
    }

    /**
     * Gets query for [[Product]].
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Gets query for [[Property]].
     * @return \yii\db\ActiveQuery
     */
    public function getProperty()
    {
        return $this->hasOne(ProductProperty::class, ['id' => 'property_id']);
    }

    /**
     * Gets query for [[Option]].
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(ProductPropertyOption::class, ['id' => 'option_id']);
    }

    /**
     * Get formatted value for display
     * @return string|null
     */
    public function getFormattedValue()
    {
        $property = $this->property;

        if ($property->isSelectType() && $this->option) {
            return $this->option->value;
        }

        if ($property->isCheckboxType()) {
            return $this->value_bool ? Module::t('Yes') : Module::t('No');
        }

        if ($property->isDateType() && $this->value_date) {
            return Yii::$app->formatter->asDate($this->value_date);
        }

        if ($property->isYearType() && $this->value_int) {
            return $this->value_int;
        }

        if ($property->isTextType() && $this->value_text) {
            return $this->value_text;
        }

        return null;
    }

    /**
     * Set value based on property type
     * @param mixed $value
     */
    public function setValue($value)
    {
        $property = $this->property;

        if ($property->isSelectType()) {
            $this->option_id = $value;
        } elseif ($property->isCheckboxType()) {
            $this->value_bool = (bool)$value;
        } elseif ($property->isDateType()) {
            $this->value_date = $value;
        } elseif ($property->isYearType()) {
            $this->value_int = (int)$value;
        } elseif ($property->isTextType()) {
            $this->value_text = $value;
        }
    }
}