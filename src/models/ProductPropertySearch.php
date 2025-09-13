<?php

namespace ZakharovAndrew\shop\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use ZakharovAndrew\shop\Module;

/**
 * ProductPropertySearch represents the model behind the search form of `ZakharovAndrew\shop\models\ProductProperty`.
 */
class ProductPropertySearch extends ProductProperty
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'sort_order'], 'integer'],
            [['name', 'code', 'created_at', 'updated_at'], 'safe'],
            [['is_required', 'is_active'], 'boolean'],
        ];
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ProductProperty::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort_order' => SORT_ASC,
                    'name' => SORT_ASC,
                ]
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
            'type' => $this->type,
            'sort_order' => $this->sort_order,
            'is_required' => $this->is_required,
            'is_active' => $this->is_active,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'code', $this->code]);

        // Filter by date range
        if (!empty($this->created_at)) {
            $query->andFilterWhere(['>=', 'created_at', $this->created_at]);
        }
        
        if (!empty($this->updated_at)) {
            $query->andFilterWhere(['>=', 'updated_at', $this->updated_at]);
        }

        return $dataProvider;
    }

    /**
     * Get search form attributes labels
     * @return array
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
     * Get filter options for type dropdown
     * @return array
     */
    public function getTypeFilterOptions()
    {
        return ['' => Module::t('All Types')] + ProductProperty::getTypesList();
    }

    /**
     * Get filter options for boolean fields
     * @return array
     */
    public function getBooleanFilterOptions()
    {
        return [
            '' => Module::t('All'),
            1 => Module::t('Yes'),
            0 => Module::t('No'),
        ];
    }
}