<?php
namespace backend\models;

use yii\base\Model;
use Yii;

/**
 * LoginForm is the model behind the login form.
 *
 */
class PasswordForm extends Model
{
    public $password;
    public $newPassword;
    public $rePassword;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['rePassword', 'password', 'newPassword'], 'required'],
            [['rePassword', 'password', 'newPassword'], 'string'],
            ['rePassword', 'compare', 'compareAttribute'=>'newPassword','message'=>'两次密码输入不一致'],
            ['newPassword', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/', 'message'=>'密码格式错误'],
            ['rePassword', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/', 'message'=>'密码格式错误'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute)
    {
        $identity = (Object) Yii::$app->user->identity;
        if(!Yii::$app->getSecurity()->validatePassword($this->password, $identity->password)){
            $this->addError($attribute, '管理员原密码错误');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => '原密码',
            'newPassword' => '新密码',
            'rePassword' => '重复新密码',
        ];
    }

    public function updateSave()
    {
        if($this->validate()){
            if(Yii::$app->user->id){
                $user = Admin::findOne(Yii::$app->user->id);
                $user->scenario = 'passwordupdate';
                $user->password = $this->newPassword;
//                $user->deleteLoginNum();
                if($user->save()){
                    return true;
                }else{
                    return false;
                }
            }
        }
        return false;
    }

}
