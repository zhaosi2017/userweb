<?php
namespace frontend\models;


use function PHPSTORM_META\elementType;
use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use frontend\models\FActiveRecord;



/**
 * SecurityQuestion model
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
            [['q_one','q_two','q_three'],'required','message'=>'请选择{attribute}'],
            [['a_one','a_two','a_three'],'required','message'=>'请回答{attribute}'],
            [['q_one','q_two','q_three'],'integer'],
            [['a_one','a_two','a_three'],'string','length' => [1, 20]],
            ['q_one','validateOne'],
            ['q_two','validateTwo'],
            ['q_three','validateThree'],
           ['a_one','validateAOne'],
//            ['a_two','validateATwo'],
//            ['a_three','validateAThree'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'q_one'=>'题1',
            'q_two'=>'题2',
            'q_three'=>'题3',
            'a_one' =>'密保问题1',
            'a_two' =>'密保问题2',
            'a_three' =>'密保问题3',
        ];
    }

    public function validateOne()
    {
        $model = Question::find()->where(['id'=>$this->q_one])->one();
        if(empty($model))
        {
            $this->addError('q_one','题库1非法');
        }
    }

    public function validateTwo()
    {
        $model = Question::find()->where(['id'=>$this->q_two])->one();
        if(empty($model))
        {
            $this->addError('q_two','题库2非法');
        }
        if($this->q_two == $this->q_one)
        {
            $this->addError('q_two','题2与题1相同');
        }

    }

    public function validateThree()
    {
        $model = Question::find()->where(['id'=>$this->q_three])->one();
        if(empty($model))
        {
            $this->addError('q_three','题库3非法');
        }
        if($this->q_two == $this->q_three)
        {
            $this->addError('q_three','题3与题2相同');
        }
        if($this->q_three == $this->q_one)
        {
            $this->addError('q_three','题3与题1相同');
        }


    }


    public function validateAOne()
    {

        if( preg_match("/\/|\`|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\（|\）|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\\' | \`|\-|\=|\\\|\||\s+/",$this->a_one))
        {
            $this->addError('a_one','答案1应不允许输入标点、空格、特殊字符');
        }
    }

    public function validateATwo()
    {
        if( preg_match("/\/|\`|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\（|\）|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\\' | \`|\-|\=|\\\|\||\s+/",$this->a_two))
        {
            $this->addError('a_two','答案2应不允许输入标点、空格、特殊字符');
        }

    }

    public function validateAThree()
    {
        if( preg_match("/\/|\`|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\（|\）|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\\' | \`|\-|\=|\\\|\||\s+/",$this->a_three))
        {
            $this->addError('a_three','答案3应不允许输入标点、空格、特殊字符');
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
        $model = self::findOne(['userid'=>$userId]);
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
