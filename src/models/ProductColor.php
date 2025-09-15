<?php

namespace ZakharovAndrew\shop\models;

use Yii;
use ZakharovAndrew\shop\Module;

/**
 * This is the model class for table "product_color".
 */
class ProductColor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_color';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code', 'css_color'], 'required'],
            [['is_active'], 'boolean'],
            [['position'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 50],
            [['css_color'], 'string', 'max' => 7],
            [['code'], 'unique'],
            ['css_color', 'match', 'pattern' => '/^#([a-f0-9]{6})$/i'],
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
            'css_color' => Module::t('CSS Color'),
            'is_active' => Module::t('Active'),
            'position' => Module::t('Position'),
            'created_at' => Module::t('Created At'),
            'updated_at' => Module::t('Updated At'),
        ];
    }

    /**
     * Gets query for [[Products]].
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['color_id' => 'id']);
    }

    /**
     * Get active colors list for dropdown
     */
    public static function getActiveColorsList()
    {
        return self::find()
            ->where(['is_active' => true])
            ->orderBy(['position' => SORT_ASC])
            ->select(['name', 'id'])
            ->indexBy('id')
            ->column();
    }

    /**
     * Generate unique code from name
     */
    public function generateUniqueCode($name)
    {
        $baseCode = \yii\helpers\Inflector::slug($name, '_');
        $code = $baseCode;
        $counter = 1;

        while (self::find()->where(['code' => $code])->andWhere(['!=', 'id', $this->id])->exists()) {
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
            
            if (empty($this->position)) {
                $maxPosition = self::find()->max('position');
                $this->position = $maxPosition ? $maxPosition + 1 : 1;
            }
            
            return true;
        }
        return false;
    }

    /**
     * Get CSS style for color display
     */
    public function getCssStyle()
    {
        return "background-color: {$this->css_color};";
    }

    /**
     * Get color badge HTML
     */
    public function getColorBadge()
    {
        return \yii\helpers\Html::tag('span', '', [
            'class' => 'color-badge',
            'style' => $this->getCssStyle(),
            'title' => $this->name,
        ]);
    }
    
    /**
     * Get contrasting color (black or white) for text
     * @return string
     */
    public function getContrastColor()
    {
        // Convert hex to RGB
        $hex = str_replace('#', '', $this->css_color);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Calculate luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        // Return black or white depending on luminance
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }
    
    /**
     * Move color position up
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
     * Move color position down
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