<?php
namespace frontend\models\WhiteLists;


use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use frontend\models\FActiveRecord;
use frontend\services\SmsService;
use frontend\models\ErrCode;
use frontend\models\User;
use frontend\models\Friends\Friends;


/**
 * User model
 *
 * @property integer $id
 * @property string $account
 */
class WhiteList extends FActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'white_list';
    }

    public function rules()
    {
        return [
            [['uid', 'white_uid'], 'integer'],
        ];

    }

    public function attributeLabels()
    {
        return [
            'uid' => '用户id',
            'white_uid' => '白名单用户',
        ];
    }


    public function lists()
    {
        $userId = Yii::$app->user->id ;
        $res = self::find()->select('white_uid')->where(['uid'=>$userId])->all();
        $data = [];
        if(!empty($res))
        {
            foreach ($res as $k => $v)
            {
                $_friends = Friends::find()->select('remark')->where(['user_id'=>$userId,'friend_id'=>$v['white_uid']])->one();
                $_user = User::find()->select(['nickname','account','header_img'])->where(['id'=>$v['white_uid']])->one();
                if(!empty($_friends) && !empty($_user)) {
                    $data[$k]['account'] = $_user->account;
                    $data[$k]['remark'] = $_friends['remark'] ? $_friends['remark'] : $_user['nickname'];
                    $data[$k]['header_url'] = $_user['header_img'] ?  Yii::$app->params['frontendBaseDomain'].$_user['header_img'].'?v='.time():'';
                }
            }
        }
        return  $this->jsonResponse($data,'操作成功',0, ErrCode::SUCCESS);

    }


}