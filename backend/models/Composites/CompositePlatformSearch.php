<?php
namespace backend\models\Composites;
use frontend\models\User;
use yii\data\ActiveDataProvider;
use frontend\models\Versions\Version;

class CompositePlatformSearch extends Version
{
    public $search_keywords;
    public $search_type;

    public function rules()
    {
        return [
            [['search_type', 'search_keywords'], 'safe'],
        ];
    }


    public function search($params)
    {
        $query = Version::find();

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
//            'create_at' => $this->create_at,
//            'update_at' => $this->update_at,
        ]);

        /*
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'img_url', $this->img_url]);
        */



        $this->search_type ==1 && $query->andFilterWhere(['=', 'platform', Version::PLATFORM_IOS]);
        $this->search_type ==2 && $query->andFilterWhere(['=', 'platform', Version::PLATFORM_ANDROID]);

        strlen($this->search_keywords)>0 && $query->andFilterWhere(['=', 'version', $this->search_keywords]);

        return $dataProvider;
    }


    public function searchIds($searchWords, $field='nickname')
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