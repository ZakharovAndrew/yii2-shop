<?php

namespace ZakharovAndrew\shop\models;

use Yii;
use ZakharovAndrew\shop\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Inflector;
use ZakharovAndrew\user\models\UserRoles;

/**
 * This is the model class for table "product_tag".
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string|null $description
 * @property string $background_color
 * @property string $text_color
 * @property int $position
 * @property string|null $allowed_roles
 * @property string|null $created_at
 * 
 * @property ProductTagAssignment[] $productTagAssignments
 * @property Product[] $products
 */
class ProductTag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'background_color', 'text_color'], 'required'],
            [['description'], 'string'],
            [['position'], 'integer'],
            [['position'], 'default', 'value' => 0],
            [['allowed_roles', 'created_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['url'], 'string', 'max' => 255],
            [['url'], 'unique', 'message' => Module::t('This URL is already in use')],
            [['background_color', 'text_color'], 'string', 'max' => 7],
            [['background_color', 'text_color'], 'match', 'pattern' => '/^#[0-9A-Fa-f]{6}$/'],
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
            'url' => Module::t('URL'),
            'description' => Module::t('Description'),
            'background_color' => Module::t('Background Color'),
            'text_color' => Module::t('Text Color'),
            'position' => Module::t('Position'),
            'allowed_roles' => Module::t('Allowed Roles'),
            'created_at' => Module::t('Created At'),
        ];
    }

    /**
     * Gets query for [[ProductTagAssignments]].
     */
    public function getProductTagAssignments()
    {
        return $this->hasMany(ProductTagAssignment::class, ['tag_id' => 'id']);
    }

    /**
     * Gets query for [[Products]].
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->via('productTagAssignments');
    }

    /**
     * Get list of all tags for dropdown
     * @return array
     */
    public static function getTagsList()
    {
        return Yii::$app->cache->getOrSet('product_tags_list', function () {
            $tags = static::find()
                ->orderBy(['position' => SORT_ASC, 'name' => SORT_ASC])
                ->asArray()
                ->all();
            
            return ArrayHelper::map($tags, 'id', 'name');
        }, 3600);
    }

    /**
     * Get tags available for current user based on roles with caching
     * @return ProductTag[]
     */
    public static function getAvailableTags()
    {
        $cacheKey = 'available_tags_' . (Yii::$app->user->isGuest ? 'guest' : Yii::$app->user->id);

        return Yii::$app->cache->getOrSet($cacheKey, function () {
            // Get all tags ordered by position and name
            $allTags = static::find()
                ->orderBy(['position' => SORT_ASC, 'name' => SORT_ASC])
                ->all();

            // For guests - return only tags without role restrictions
            if (Yii::$app->user->isGuest) {
                return array_filter($allTags, function($tag) {
                    return empty($tag->allowed_roles) || $tag->allowed_roles === '[]';
                });
            }

            // For authenticated users - get their roles
            $userRoles = UserRoles::getUserRoles(Yii::$app->user->id);
            $userRoleIds = ArrayHelper::getColumn($userRoles, 'id');

            // If user has no specific roles, return only tags without restrictions
            if (empty($userRoleIds)) {
                return array_filter($allTags, function($tag) {
                    return empty($tag->allowed_roles) || $tag->allowed_roles === '[]';
                });
            }

            // Filter tags based on user roles
            return array_filter($allTags, function($tag) use ($userRoleIds) {
                return static::isTagAvailableForRoles($tag, $userRoleIds);
            });
        }, 300); // Cache for 5 minutes
    }
    
    /**
     * Check if tag is available for specific user roles
     * @param ProductTag $tag
     * @param array $userRoleIds
     * @return bool
     */
    private static function isTagAvailableForRoles($tag, $userRoleIds)
    {
        // Tag is available for all if no role restrictions
        if (empty($tag->allowed_roles) || $tag->allowed_roles === '[]') {
            return true;
        }

        // Parse allowed roles from JSON
        $allowedRoles = Json::decode($tag->allowed_roles);
        if (empty($allowedRoles)) {
            return true;
        }

        // Check if user has at least one of the allowed roles
        return !empty(array_intersect($allowedRoles, $userRoleIds));
    }
    
    /**
     * Check if tag is available for current user
     * @return bool
     */
    public function isAvailableForCurrentUser()
    {
        // Available for all if no role restrictions
        if (empty($this->allowed_roles) || $this->allowed_roles === '[]') {
            return true;
        }

        // For guests - not available if there are restrictions
        if (Yii::$app->user->isGuest) {
            return false;
        }

        // For authenticated users - check their roles
        $userRoles = Yii::$app->user->identity->getRoles();
        $userRoleIds = ArrayHelper::getColumn($userRoles, 'id');

        return static::isTagAvailableForRoles($this, $userRoleIds);
    }


    /**
     * Get allowed roles as array
     * @return array
     */
    public function getAllowedRolesArray()
    {
        if (empty($this->allowed_roles) || $this->allowed_roles === '[]') {
            return [];
        }
        
        return Json::decode($this->allowed_roles) ?: [];
    }

    /**
     * Set allowed roles from array
     * @param array $roles
     */
    public function setAllowedRolesArray($roles)
    {
        $this->allowed_roles = Json::encode($roles);
    }

    /**
     * Get CSS style for tag display
     * @return string
     */
    public function getTagStyle()
    {
        return "background-color: {$this->background_color}; color: {$this->text_color};";
    }

    /**
     * Get CSS classes for tag display
     * @return string
     */
    public function getTagCssClass()
    {
        return 'product-tag';
    }

    /**
     * Generate unique URL from tag name
     * @return string
     */
    public function generateUniqueUrl()
    {
        $baseUrl = Inflector::slug($this->name);
        $url = $baseUrl;
        $counter = 1;

        while (self::find()->where(['url' => $url])->andWhere(['!=', 'id', $this->id])->exists()) {
            $url = $baseUrl . '-' . $counter++;
        }

        return $url;
    }

    /**
     * Get tag page URL
     * @return string
     */
    public function getUrl()
    {
        return \yii\helpers\Url::to(['/shop/tag/view', 'url' => $this->url]);
    }

    /**
     * Find tag by URL
     * @param string $url
     * @return ProductTag|null
     */
    public static function findByUrl($url)
    {
        return static::find()->where(['url' => $url])->one();
    }

    /**
     * Get active products for this tag
     * @return \yii\db\ActiveQuery
     */
    public function getActiveProducts()
    {
        return $this->getProducts()
            ->andWhere(['product.status' => Product::STATUS_ACTIVE])
            ->orderBy(['product.position' => SORT_DESC]);
    }

    /**
     * Get products count for this tag
     * @return int
     */
    public function getProductsCount()
    {
        return $this->getProducts()
            ->andWhere(['product.status' => Product::STATUS_ACTIVE])
            ->count();
    }
    
    /**
     * Get tags available for specific roles
     * @param array $roleIds
     * @return ProductTag[]
     */
    public static function getTagsForRoles($roleIds)
    {
        $allTags = static::find()
            ->orderBy(['position' => SORT_ASC, 'name' => SORT_ASC])
            ->all();

        return array_filter($allTags, function($tag) use ($roleIds) {
            return static::isTagAvailableForRoles($tag, $roleIds);
        });
    }

    /**
     * Check if this tag has any role restrictions
     * @return bool
     */
    public function hasRoleRestrictions()
    {
        return !empty($this->allowed_roles) && $this->allowed_roles !== '[]';
    }

    /**
     * Get list of role names that can access this tag
     * @return array
     */
    public function getAllowedRoleNames()
    {
        $allowedRoleIds = $this->getAllowedRolesArray();
        if (empty($allowedRoleIds)) {
            return ['All roles'];
        }

        $roleNames = [];
        foreach ($allowedRoleIds as $roleId) {
            $role = \ZakharovAndrew\user\models\Roles::findOne($roleId);
            if ($role) {
                $roleNames[] = $role->title;
            }
        }

        return $roleNames;
    }
    
    /**
     * Move tag position up
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
     * Move tag position down
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

    /**
     * Before save handler
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->url)) {
                $this->url = $this->generateUniqueUrl();
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
     * After save handler - clear cache
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->cache->delete('product_tags_list');
    }

    /**
     * After delete handler - clear cache
     */
    public function afterDelete()
    {
        parent::afterDelete();
        Yii::$app->cache->delete('product_tags_list');
    }
}