<?php

namespace backend\models;

//use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ManagerLoginLogs;
/**
 * LoginLogsSearch represents the model behind the search form about `app\modules\admin\models\ManagerLoginLogs`.
 */
class LoginLogsSearch extends ManagerLoginLogs
{

    public $start_date;

    public $end_date;

    public $search_type;

    public $search_keywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'uid', 'status'], 'integer'],
            [['login_time', 'login_ip', 'start_date', 'end_date', 'search_type', 'search_keywords'], 'safe'],
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
        $query = ManagerLoginLogs::find();

        // add conditions that should always apply here
        $query->joinWith('admin');

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
            'uid' => $this->uid,
            'manager_login_logs.status' => $this->status,
            'login_time' => $this->login_time,
        ]);

        if(empty($this->start_date) && !empty($this->end_date)){
            $query->andFilterWhere(['<=', 'manager_login_logs.login_time',  date('Y-m-d', strtotime($this->end_date)+24*60*60)]);
        }

        if(!empty($this->start_date) && empty($this->end_date)){
            $query->andFilterWhere(['>=', 'manager_login_logs.login_time',  $this->start_date]);
        }

        if(!empty($this->start_date) && !empty($this->end_date) ){
            if($this->start_date > $this->end_date){
                $tmp = $this->end_date;
                $this->end_date = $this->start_date;
                $this->start_date  = $tmp;
            }
            $query->andFilterWhere(['between', 'manager_login_logs.login_time', $this->start_date, date('Y-m-d', strtotime($this->end_date)+24*60*60)]);
        }
        // !empty($this->status) && $query->andFilterWhere(['=', 'manager_login_logs.status', $this->status]);

        $this->search_type ==1 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['in', 'admin.id', (new AdminSearch())->searchIds($this->search_keywords, 'account')]);
        $this->search_type ==2 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['in', 'admin.id', (new AdminSearch())->searchIds($this->search_keywords, 'nickname')]);
        $this->search_type ==3 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['like', 'manager_login_logs.login_ip', $this->search_keywords]);

        return $dataProvider;
    }

    public function searchIds($searchWords, $name)
    {
        $ids = [0];
        $query = $this::find()->select([$name,'id'])->all();
        foreach ($query as $row)
        {
            $pos = strpos($row[$name],$searchWords);
            if(is_int($pos)){
                $ids[] = $row['id'];
            }
        }
        return $ids;
    }
}
