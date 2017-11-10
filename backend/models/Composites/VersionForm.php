<?php
namespace backend\models\Composites;

use yii\base\Model;
use Yii;
use frontend\models\Versions\Version;

/**
 * LoginForm is the model behind the login form.
 *
 */
class VersionForm extends Version
{


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['platform','version', 'info', 'url'], 'required'],
            [['platform', 'version', 'info','url'], 'string'],
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
