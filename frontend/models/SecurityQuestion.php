<?php
namespace frontend\models;


use function PHPSTORM_META\elementType;
use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use frontend\models\FActiveRecord;



/**
 * User model
 *
 * @property integer $id
 * @property string $account
 */
class SecurityQuestion extends FActiveRecord
{

    /*******题库常量*******/
    const QUESTION_ONE   = 1;
    const QUESTION_TWO   = 2;
    const QUESTION_THREE = 3;
    const QUESTION_FOUR  = 4;
    const QUESTION_FIVE  = 5;
    const QUESTION_SIX   = 6;
    const QUESTION_SEVEN = 7;

    /*******获取题库函数*******/
    public static function getQuestions()
    {
        return [
            self::QUESTION_ONE    => '您上一间公司叫什么?',
            self::QUESTION_TWO    => '您最亲的人手机号后4位是什么？',
            self::QUESTION_THREE  => '您的小学全名叫什么？',
            self::QUESTION_FOUR   => '圣经里您记得最清晰的一句话是什么？',
            self::QUESTION_FIVE   => '手机里您最不会删除的程序是什么？',
            self::QUESTION_SIX    => '您网购买过最贵的商品是什么？',
            self::QUESTION_SEVEN  => '您的电脑硬盘多少G?',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'security_question';
    }

    public function rules()
    {
        return [
            [['q_one','q_two','q_three','a_one','a_two','a_three'],'required'],
            [['q_one','q_two','q_three'],'integer'],
            [['a_one','a_two','a_three'],'string'],
            ['q_one','validateOne'],
            ['q_two','validateTwo'],
            ['q_three','validateThree'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'q_one'=>'题1',
            'q_two'=>'题2',
            'q_three'=>'题3',
            'a_one' =>'密保答案1',
            'a_two' =>'密保答案2',
            'a_three' =>'密保答案3',
        ];
    }

    public function validateOne()
    {
        if($this->q_one == $this->q_two)
        {
            $this->addError('q_one','题库1 Id 不能与题库2 Id 一样');
        }
        if($this->q_one == $this->q_three)
        {
            $this->addError('q_one','题库1 Id 不能与题库3 Id 一样');
        }
        if(!array_key_exists($this->q_one,self::getQuestions()))
        {
            $this->addError('q_one','题库1非法');
        }
    }

    public function validateTwo()
    {
        if($this->q_two == $this->q_three)
        {
            $this->addError('q_two','题库2 Id 不能与题库3 Id 一样');
        }
        if(!array_key_exists($this->q_two,self::getQuestions()))
        {
            $this->addError('q_two','题库2非法');
        }
    }

    public function validateThree()
    {
        if(!array_key_exists($this->q_three,self::getQuestions()))
        {
            $this->addError('q_three','题库3非法');
        }
    }

    public function updateSecurityQuestion($data)
    {
        $userId = Yii::$app->user->id;
        $q1 = isset($data['q1']) ? $data['q1']: '';
        $q2 = isset($data['q2']) ? $data['q2']: '';
        $q3 = isset($data['q3']) ? $data['q3']: '';
        $a1 = isset($data['a1']) ? $data['a1']: '';
        $a2 = isset($data['a2']) ? $data['a2']: '';
        $a3 = isset($data['a3']) ? $data['a3']: '';
        $model = self::findOne($userId);
        if(empty($model))
        {
            $model = new SecurityQuestion();
            $model->userid = $userId;
        }
        $model->q_one = $q1;
        $model->q_two = $q2;
        $model->q_three = $q3;
        $model->a_one = $a1;
        $model->a_two = $a2;
        $model->a_three = $a3;
        if($model->validate()) {
            if ($model->save()) {
                return $this->jsonResponse([], '操作成功', 0, ErrCode::SUCCESS);
            } else {
                return $this->jsonResponse([], $model->getErrors(), 1, ErrCode::DATA_SAVE_ERROR);
            }
        }else{
            return $this->jsonResponse([], $model->getErrors(), 1, ErrCode::VALIDATION_NOT_PASS);
        }

    }

}
