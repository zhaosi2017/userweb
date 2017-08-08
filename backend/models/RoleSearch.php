<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * RoleSearch represents the model behind the search form about `app\modules\admin\models\Role`.
 */
class RoleSearch extends Role
{
    public $search_type;

    public $search_keywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'create_id', 'update_id', 'create_at', 'update_at'], 'integer'],
            [['name', 'remark', 'search_type', 'search_keywords'], 'safe'],
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
        $query = Role::find();

        $query->andWhere(['role.status'=>Yii::$app->requestedAction->id == 'index' ? 0 : 1]);
        // add conditions that should always apply here

        $query->joinWith('creator')->joinWith('updater');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            /*'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'defaultOrder' => [
                    'create_at' => SORT_DESC,
                ]
            ],*/
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
            'create_id' => $this->create_id,
            'update_id' => $this->update_id,
            'create_at' => $this->create_at,
            'update_at' => $this->update_at,
        ]);

        $this->search_type ==1 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['like', 'role.name', $this->search_keywords]);
        $this->search_type ==2 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['like', 'role.remark', $this->search_keywords]);
        $this->search_type ==3 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['in', 'creator.id', $this->searchIds($this->search_keywords)]);
        $this->search_type ==4 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['in', 'updater.id', $this->searchIds($this->search_keywords)]);

        return $dataProvider;
    }

    public function searchIds($searchWords)
    {
        $ids = [0];
        $query = Admin::find()->select(['account','id'])->all();
        foreach ($query as $row)
        {
            $pos = strpos($row['account'],$searchWords);
            if(is_int($pos)){
                $ids[] = $row['id'];
            }
        }
        return $ids;
    }

}
