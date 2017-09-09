<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:34
 */
namespace frontend\models\Friends;
use frontend\models\User;
use Yii;
use frontend\models\ErrCode;
use frontend\models\FActiveRecord;
use yii\db\Transaction;

/**
 * Class FriendsRequest
 * @package frontend\models\Friends
 * @property  integer $id
 * @property  integer $from_id
 * @property  integer $to_id
 * @property  integer $status
 * @property  integer $create_at
 * @property  string  $note
 */
class FriendsRequest extends FActiveRecord {

    const NORMAL_STATUS = 0;// 发送请求
    const AGREE_STATUS  = 1;// 同意
    const REFUSE_STATUS = 2;// 拒绝

    const FIRST_NUM = 20; //首次显示20条好友请求
    const OTHER_NUM = 10;//其他每次只显示10条


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'friends_request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['from_id','to_id','status','create_at'] ,'integer'],
            ['note','string','max'=>255],
        ];
    }

    public function getFriendsRequest($data)
    {
        $userId = Yii::$app->user->id;

        $page = isset($data['p']) && $data['p'] > 0 ? (int)$data['p'] : 0;
        $limit =  $page == 0 ?  self::FIRST_NUM : self::OTHER_NUM;
        $offset = $page == 0 ? 0: self::FIRST_NUM+self::OTHER_NUM*($page-1);

        $data  = self::find()->where(['to_id'=>$userId])
            ->orWhere(['from_id'=>$userId])
            ->select(['id','note','from_id','status','to_id','update_at'])
        ->offset($offset)->limit($limit)->orderBy('update_at desc,id desc')->distinct()->all();
        $tmp = [];
        if(!empty($data))
        {
            foreach ($data as $k=>$v)
            {
                $_v['id'] = $v['id'];
                $_v['note'] = $v['note'];
                $_v['from_id'] = $v['from_id'];
                $_v['status'] = $v['status'];
                $_v['to_id'] = $v['to_id'];

                if($_v['to_id'] == $userId)
                {
                    $_tmp =  User::find()->where(['id'=>$v['from_id']])->select(['nickname','account'])->one();
                    $_v['direction'] = '1';// 被邀请者
                }
                if($_v['from_id'] == $userId)
                {
                    $_tmp =  User::find()->where(['id'=>$v['to_id']])->select(['nickname','account'])->one();
                    $_v['direction'] = '0';// 邀请者
                }
                $_v['nickname'] = isset($_tmp['nickname']) ?$_tmp['nickname']:'';
                $_v['account'] = isset($_tmp['account']) ?$_tmp['account']:'';
                $tmp[$k]=$_v;
            }
        }



        return $this->jsonResponse($tmp,'操作成功',0,ErrCode::SUCCESS);
    }






}