<?php
namespace backend\models\CallRecords;
use frontend\models\CallRecord\CallRecord;
use frontend\models\User;
use Yii;
use yii\data\ActiveDataProvider;
class CallRecordSearch extends CallRecord
{

    public $search_keywords;
    public $search_type;
    public $search_status;
    public $search_call_type;
    public $start_date;
    public $end_date;

    public function rules()
    {
        return [
            [['search_type', 'search_keywords','search_status','search_call_type','start_date','end_date'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = CallRecord::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'call_time' => SORT_DESC,
                ]
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
            'call_time' => $this->call_time,
        ]);

        /*
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'img_url', $this->img_url]);
        */

        if(empty($this->start_date) && !empty($this->end_date)){
            $query->andFilterWhere(['<=', 'call_time',  strtotime($this->end_date)+24*60*60]);
        }

        if(!empty($this->start_date) && empty($this->end_date)){
            $query->andFilterWhere(['>=', 'call_time',  strtotime($this->start_date)]);
        }

        if(!empty($this->start_date) && !empty($this->end_date) ){
            if($this->start_date > $this->end_date){
                $tmp = $this->end_date;
                $this->end_date = $this->start_date;
                $this->start_date  = $tmp;
            }
            $query->andFilterWhere(['between', 'call_time', strtotime($this->start_date), strtotime($this->end_date)+24*60*60]);
        }

        $this->search_type ==1 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['in', 'active_call_uid', $this->searchIds($this->search_keywords,'account')]);
        $this->search_type ==2 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['in', 'unactive_call_uid', $this->searchIds($this->search_keywords,'account')]);
        array_key_exists($this->search_status,CallRecord::$status_map) &&  $query->andFilterWhere(['=','status', $this->search_status]);
        $this->search_call_type && array_key_exists($this->search_call_type,CallRecord::$type_map) &&  $query->andFilterWhere(['=','type', $this->search_call_type]);
        //var_dump($this->search_call_type,$this->search_status);die;
        return $dataProvider;
    }


    public function searchIds($searchWords, $field='name')
    {
        $ids = [0];
        $query = User::find()->select([$field,'id'])->all();
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