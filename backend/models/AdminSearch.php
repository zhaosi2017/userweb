<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Admin;
/**
 * ManagerSearch represents the model behind the search form about `app\modules\admin\models\Manager`.
 */
class AdminSearch extends Admin
{
    public $search_type;

    public $search_keywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'role_id', 'status', 'create_id', 'update_id', 'create_at', 'update_at'], 'integer'],
            [['account', 'nickname', 'remark', 'login_ip', 'search_type', 'search_keywords'], 'safe'],
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
        $query = Admin::find();

        $action_id = Yii::$app->requestedAction->id;

        if($action_id == 'index'){
            $query->andWhere(['!=','status',1]);
        }
        if($action_id == 'trash'){
            $query->andWhere(['status' => 1]);
        }

        // add conditions that should always apply here

//        $query->joinWith('creator')->joinWith('updater');

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
            'role_id' => $this->role_id,
            'status' => $this->status,
            'create_id' => $this->create_id,
            'update_id' => $this->update_id,
            'create_at' => $this->create_at,
            'update_at' => $this->update_at,
        ]);

        $this->search_type ==1 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['in', 'admin.id', $this->searchIds($this->search_keywords,'account')]);
        $this->search_type ==2 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['in', 'admin.id', $this->searchIds($this->search_keywords,'nickname')]);
        $this->search_type ==3 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['like', 'admin.login_ip', $this->search_keywords]);

        return $dataProvider;
    }

    public function searchIds($searchWords, $field='name')
    {
        $ids = [0];
        $query = $this::find()->select([$field,'id'])->all();
        foreach ($query as $row)
        {
            $pos = strpos($row[$field],$searchWords);
            if(is_int($pos)){
                $ids[] = $row['id'];
            }
        }
        return $ids;
    }

}
