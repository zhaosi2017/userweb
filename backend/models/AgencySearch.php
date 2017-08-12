<?php

namespace backend\models;

//use Yii;
use backend\models\Agency;
use Symfony\Component\Yaml\Tests\A;
use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * CompanySearch represents the model behind the search form about `app\modules\user\models\Company`.
 */
class AgencySearch extends Agency
{

    public $superior_name;

    public $search_type;

    public $search_keywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_id'], 'integer'],
            [['name', 'create_time', 'update_time', 'superior_name', 'search_type', 'search_keywords'], 'safe'],
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

        $status = \Yii::$app->requestedAction->id == 'index' ? 0 : 1;
        $status = isset($params['status']) && $params['status'] === 0 ? 0: $status;

        $query = Agency::find()
            ->where([
            'agency.status'=>$status,
        ]);

        $query->joinWith('admin');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'create_at' => SORT_DESC,
                ]
            ],
        ]);

//        $dataProvider->setSort([
//            'attributes' => [
//                /* 其它字段不要动 */
//                /*  下面这段是加入的 */
//                /*=============*/
////                'superior_name' => [
////                    'asc' => ['superior.sup_id' => SORT_ASC],
////                    'desc' => ['superior.sup_id' => SORT_DESC],
////                    'label' => 'sup_id'
////                ],
//                /*=============*/
//            ]
//        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,


        ]);


        $this->search_type ==1 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['in', 'agency.id', $this->searchIds($this->search_keywords)]);
        $this->search_type ==2 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['in', 'agency.parent_id', $this->searchParents($this->search_keywords)]);
        $this->search_type ==3 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['in', 'agency.id', $this->searchIds($this->search_keywords,'code')]);

        isset($params['status']) && $params['status'] === 0 && $query->orFilterWhere(['in','agency.id',$this->seachIDS($this->search_keywords)]);
        return $dataProvider;
    }

    public function seachIDS($searchWords)
    {
        $ids = [0];
        $query = $this::find()->select(['code','id'])->where(['status'=>Agency::NORMAL_STATUS])->all();
        foreach ($query as $row)
        {
            $pos = strpos($row['code'],$searchWords);
            if(is_int($pos)){
                $ids[] = $row['id'];
            }
        }
        return $ids;
    }

    public function searchIds($searchWords,$name='name')
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

    public function searchParents($searchWords)
    {
        $ids = [-1];
        if($searchWords == self::TOP_AGENCY)
        {
            $ids=[0];
            return $ids;
        }
        $query = $this::find()->select(['name','id'])->all();
        foreach ($query as $row)
        {
            $pos = strpos($row['name'],$searchWords);
            if(is_int($pos)){
                $ids[] = $row['id'];
            }
        }
        return $ids;
    }
}
