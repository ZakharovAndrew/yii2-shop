<?php

/**
 * ShopTelegramGroupsSearch
 *  
 * @link https://github.com/ZakharovAndrew/yii2-shop/
 * @copyright Copyright (c) 2023-2025 Zakharov Andrew
 */

namespace ZakharovAndrew\shop\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use ZakharovAndrew\shop\models\ShopTelegramGroups;

/**
 * ShopTelegramGroupsSearch represents the model behind the search form of `ZakharovAndrew\shop\models\ShopTelegramGroups`.
 */
class ShopTelegramGroupsSearch extends ShopTelegramGroups
{
    public $shop_count;
    public $linked_shops;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'is_active'], 'integer'],
            [['title', 'telegram_url', 'telegram_chat_id', 'permissions', 'created_at', 'updated_at'], 'safe'],
            [['shop_count'], 'integer'],
            [['linked_shops'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'shop_count' => Yii::t('app', 'Linked Shops Count'),
            'linked_shops' => Yii::t('app', 'Linked Shops'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ShopTelegramGroups::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
                'attributes' => [
                    'id',
                    'title',
                    'telegram_url',
                    'is_active',
                    'created_at',
                    'updated_at',
                    'shop_count' => [
                        'asc' => ['shop_count' => SORT_ASC],
                        'desc' => ['shop_count' => SORT_DESC],
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'is_active' => $this->is_active,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'telegram_url', $this->telegram_url])
            ->andFilterWhere(['like', 'telegram_chat_id', $this->telegram_chat_id])
            ->andFilterWhere(['like', 'permissions', $this->permissions]);

        // Фильтр по дате создания
        if ($this->created_at) {
            $query->andFilterWhere(['>=', 'created_at', $this->created_at]);
        }

        // Фильтр по количеству связанных магазинов
        if ($this->shop_count !== null && $this->shop_count !== '') {
            $query->leftJoin('shop_to_telegram_groups stg', 'shop_telegram_groups.id = stg.telegram_group_id')
                  ->groupBy('shop_telegram_groups.id')
                  ->having(['COUNT(stg.shop_id)' => $this->shop_count]);
        }

        // Фильтр по связанным магазинам
        if ($this->linked_shops) {
            $query->joinWith(['shops shops'])
                  ->andFilterWhere(['like', 'shops.name', $this->linked_shops]);
        }

        return $dataProvider;
    }

    /**
     * Получить статистику по группам
     * 
     * @return array
     */
    public function getGroupsStats()
    {
        $stats = [
            'total' => ShopTelegramGroups::find()->count(),
            'active' => ShopTelegramGroups::find()->where(['is_active' => true])->count(),
            'inactive' => ShopTelegramGroups::find()->where(['is_active' => false])->count(),
            'with_shops' => ShopTelegramGroups::find()
                ->select('shop_telegram_groups.id')
                ->innerJoinWith('shopLinks')
                ->distinct()
                ->count(),
            'without_shops' => ShopTelegramGroups::find()
                ->select('shop_telegram_groups.id')
                ->leftJoin('shop_to_telegram_groups stg', 'shop_telegram_groups.id = stg.telegram_group_id')
                ->where(['stg.id' => null])
                ->count(),
        ];

        return $stats;
    }

    /**
     * Получить группы с количеством связанных магазинов
     * 
     * @return \yii\db\ActiveQuery
     */
    public static function getGroupsWithShopCount()
    {
        return ShopTelegramGroups::find()
            ->select([
                'shop_telegram_groups.*',
                'shop_count' => 'COUNT(stg.shop_id)'
            ])
            ->leftJoin('shop_to_telegram_groups stg', 'shop_telegram_groups.id = stg.telegram_group_id')
            ->groupBy('shop_telegram_groups.id');
    }

    /**
     * Поиск групп по разрешениям
     * 
     * @param string $permission
     * @return ActiveDataProvider
     */
    public function searchByPermission($permission, $params = [])
    {
        $this->load($params);

        $query = ShopTelegramGroups::find()
            ->where(['like', 'permissions', '"' . $permission . '"']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['title' => SORT_ASC],
            ],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Применяем дополнительные фильтры
        $query->andFilterWhere([
            'id' => $this->id,
            'is_active' => $this->is_active,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'telegram_url', $this->telegram_url]);

        return $dataProvider;
    }

    /**
     * Поиск активных групп для автопостинга
     * 
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchActiveForAutoposting($params = [])
    {
        $this->load($params);

        $query = ShopTelegramGroups::find()
            ->where(['is_active' => true])
            ->andWhere(['like', 'permissions', '"can_autopost"']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['title' => SORT_ASC],
            ],
            'pagination' => false,
        ]);

        return $dataProvider;
    }

    /**
     * Получить список групп для выпадающего списка
     * 
     * @param bool $activeOnly Только активные
     * @return array
     */
    public static function getList($activeOnly = true)
    {
        $query = ShopTelegramGroups::find()
            ->select(['id', 'title', 'telegram_url'])
            ->orderBy(['title' => SORT_ASC]);

        if ($activeOnly) {
            $query->where(['is_active' => true]);
        }

        $groups = $query->asArray()->all();

        $result = [];
        foreach ($groups as $group) {
            $result[$group['id']] = $group['title'] . ' (' . $group['telegram_url'] . ')';
        }

        return $result;
    }
}