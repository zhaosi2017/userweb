<?php
namespace frontend\models\BlackLists;


use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use frontend\models\FActiveRecord;
use frontend\services\SmsService;
use frontend\models\Friends\Friends;
use frontend\models\User;
use frontend\models\ErrCode;


/**
 * User model
 *
 * @property integer $id
 * @property string $account
 */
class BlackList extends FActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'black_list';
    }

    public function rules()
    {
        return [
            [['uid', 'black_uid'], 'integer'],
        ];

    }

    public function attributeLabels()
    {
        return [
            'uid' => '用户id',
            'black_uid' => '黑名单用户',
        ];
    }


    public function lists()
    {
        $userId = Yii::$app->user->id ;
        $res = self::find()->select('black_uid')->where(['uid'=>$userId])->all();
        $data = [];
        if(!empty($res))
        {
            foreach ($res as $k => $v)
            {
                $_friends = Friends::find()->select('remark')->where(['user_id'=>$userId,'friend_id'=>$v['black_uid']])->one();
                $_user = User::find()->select(['nickname','account'])->where(['id'=>$v['black_uid']])->one();
                if(!empty($_friends) && !empty($_user)) {
                    $data[$k]['account'] = $_user->account;
                    $data[$k]['remark'] = $_friends['remark'] ? $_friends['remark'] : $_user['nickname'];
                }
            }
        }
        return  $this->jsonResponse($data,'操作成功',0, ErrCode::SUCCESS);

    }

}