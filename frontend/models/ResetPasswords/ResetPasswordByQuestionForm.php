<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\ResetPasswords;

use frontend\models\ErrCode;
use frontend\models\FActiveRecord;
use frontend\models\Friends\Friends;
use frontend\models\User;
use yii\base\Model;
use yii\db\Transaction;
use frontend\models\Friends\FriendsGroup;
use  frontend\services\SmsService;
use Yii;
use frontend\models\UserPhone;
use frontend\models\UrgentContact;
use frontend\models\SecurityQuestion;
use frontend\models\Question;
/**
 * Class Friends
 * @package frontend\models\Friends
 * @property integer $id
 * @property integer $user_id
 * @property integer $friend_id
 * @property integer $create_at
 * @property integer $group_id
 * @property string  $remark
 * @property string  $extsion
 *
 */
class ResetPasswordByQuestionForm extends User
{


    public $country_code;
    public $phone;
    public $q1;
    public $q2;
    public $q3;
    public $a1;
    public $a2;
    public $a3;


    public function rules()
    {
        return [

            ['country_code', 'integer'],
            [['country_code', 'phone','q1','q2','q3','a1','a2','a3'], 'required'],
            [['a1','a2','a3'],'string','length' => [1, 20]],
            ['country_code', 'match', 'pattern' => '/^[1-9]{1}[0-9]{0,5}$/', 'message' => '{attribute}必须为1到6位纯数字'],
            ['phone', 'match', 'pattern' => '/^[0-9]{4,11}$/', 'message' => '{attribute}必须为4到11位纯数字'],
            ['q1','validateOne'],
            ['q2','validateTwo'],
            ['q3','validateThree'],


        ];
    }


    public function validateOne()
    {
        $model = Question::find()->where(['id'=>$this->q1])->one();
        if(empty($model))
        {
            $this->addError('q1','题库1非法');
        }
    }

    public function validateTwo()
    {
        $model = Question::find()->where(['id'=>$this->q2])->one();
        if(empty($model))
        {
            $this->addError('q2','题库2非法');
        }
        if($this->q2 == $this->q1)
        {
            $this->addError('q2','题2与题1相同');
        }

    }

    public function validateThree()
    {
        $model = Question::find()->where(['id'=>$this->q3])->one();
        if(empty($model))
        {
            $this->addError('q3','题库3非法');
        }
        if($this->q2 == $this->q3)
        {
            $this->addError('q3','题3与题2相同');
        }
        if($this->q3 == $this->q1)
        {
            $this->addError('q3','题3与题1相同');
        }


    }



    public function resetPasswordQuestion()
    {
        if($this->validate(['country_code','phone','a1','a2','a3','q1','q2','q3'])) {
            $user = User::find()->where(['country_code' => $this->country_code, 'phone_number' => $this->phone])->one();
            if (empty($user)) {
                return $this->jsonResponse([], '用户不存在', '1', ErrCode::USER_NOT_EXIST);
            }


            $data['q1']= $this->q1;
            $data['q2']= $this->q2;
            $data['q3']= $this->q3;
            $data['a1']= $this->a1;
            $data['a2']= $this->a2;
            $data['a3']= $this->a3;
            $securityQuestion = new SecurityQuestion();

            $model = SecurityQuestion::find()->where([
                'userid' => $user->id,
//                'q_one' => $this->q1,
//                'q_two' => $this->q2,
//                'q_three' => $this->q3,
//                'a_one' => $this->a1,
//                'a_two' => $this->a2,
//                'a_three' => $this->a3,
            ])->one();

            if (empty($model)) {
                return $this->jsonResponse([], '安全问题没有设置', '1', ErrCode::SECURITY_QUESTION_NOT_SET);
            }
            if($model->q_one != $this->q1)
            {
                return $this->jsonResponse([], '题1和您设置的题1不匹配', '1', ErrCode::FAILURE);

            }
            if($model->q_two != $this->q2)
            {
                return $this->jsonResponse([], '题2和您设置的题2不匹配', '1', ErrCode::FAILURE);

            }
            if($model->q_three != $this->q3)
            {
                return $this->jsonResponse([], '题3和您设置的题3不匹配', '1', ErrCode::FAILURE);

            }
            if($model->a_one != $this->a1)
            {
                return $this->jsonResponse([], '第一个问题没有答对，请重试', '1', ErrCode::FAILURE);

            }
            if($model->a_two != $this->a2)
            {
                return $this->jsonResponse([], '第二个问题没有答对，请重试', '1', ErrCode::FAILURE);

            }
            if($model->a_three != $this->a3)
            {
                return $this->jsonResponse([], '第三个问题没有答对，请重试', '1', ErrCode::FAILURE);

            }
            $redis = Yii::$app->redis;
            $key = $this->country_code . $this->phone . self::REDIS_TOKEN;
            $_tmp = md5($key . time());
            $expire = isset(Yii::$app->params['redis_expire_time']) ? Yii::$app->params['redis_expire_time'] : 120;
            $redis->setex($key, $expire, $_tmp);
            return $this->jsonResponse(['token' => $_tmp], '操作成功', '0', ErrCode::SUCCESS);
        }else{
            return $this->jsonResponse([], $this->getErrors(), '0', ErrCode::SUCCESS);
        }

    }

}