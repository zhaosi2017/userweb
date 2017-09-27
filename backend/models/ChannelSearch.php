<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Channel;

/**
 * ChannelSearch represents the model behind the search form about `backend\models\Channel`.
 */
class ChannelSearch extends Channel
{

    /**
     * 增加属性.
     *
     * @return array
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['search_type', 'search_keywords']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'create_at', 'update_at'], 'integer'],
            [['name', 'img_url', 'gray_img_url'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Channel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'create_at' => $this->create_at,
            'update_at' => $this->update_at,
        ]);

        /*
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'img_url', $this->img_url]);
        */
        $this->search_type ==1 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['like', 'name', $this->search_keywords]);

        return $dataProvider;
    }
}
