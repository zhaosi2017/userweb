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
        $model = Question::find()->where(['id'=>$this->q_one,'type'=>Question::GROUP_ONE])->one();
        if(empty($model))
        {
            $this->addError('q_one','题库1非法');
        }
    }

    public function validateTwo()
    {
        $model = Question::find()->where(['id'=>$this->q_two,'type'=>Question::GROUP_TWO])->one();
        if(empty($model))
        {
            $this->addError('q_one','题库2非法');
        }
    }

    public function validateThree()
    {
        $model = Question::find()->where(['id'=>$this->q_three,'type'=>Question::GROUP_THREE])->one();
        if(empty($model))
        {
            $this->addError('q_one','题库3非法');
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

    public function checkSecurityQuestion($data)
    {
        $this->q_one = isset($data['q1']) ? $data['q1']:'';
        $this->q_two = isset($data['q2']) ? $data['q2']: '';
        $this->q_three = isset($data['q3']) ? $data['q3']: '';
        $this->a_one  = isset($data['a1']) ? $data['a1']: '';
        $this->a_two = isset($data['a2']) ? $data['a2']: '';
        $this->a_three = isset($data['a3']) ? $data['a3']: '';
        if($this->validate())
        {
            return true;
        }else{
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }

}
