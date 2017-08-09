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
        $query = Agency::find()->select([
            'Agency.id',
            'Agency.parent_id',
            'Agency.status',
            'Agency.level',
            'Agency.time',
            'Agency.code',
            'Agency.create_time',
            'Agency.update_time',
            'admin.name as superior_name',
        ]);
//            ->where([
//            'agency.status'=>\Yii::$app->requestedAction->id == 'index' ? 0 : 1,
//        ]);

        $query->joinWith('admin');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'create_time' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->setSort([
            'attributes' => [
                /* 其它字段不要动 */
                /*  下面这段是加入的 */
                /*=============*/
//                'superior_name' => [
//                    'asc' => ['superior.sup_id' => SORT_ASC],
//                    'desc' => ['superior.sup_id' => SORT_DESC],
//                    'label' => 'sup_id'
//                ],
                /*=============*/
            ]
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
            'parent_id' => $this->parent_id,
            'time' => $this->time,

        ]);

        $this->search_type ==1 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['in', 'agency.id', $this->searchIds($this->search_keywords)]);
        $this->search_type ==2 && strlen($this->search_keywords)>0 && $query->andFilterWhere(['in', 'admin.id', $this->searchIds($this->search_keywords)]);
        return $dataProvider;
    }

    public function searchIds($searchWords)
    {
        $ids = [0];
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
