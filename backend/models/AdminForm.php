<?php
namespace backend\models;

use Yii;

/**
 * Class AdminForm
 * @package backend\models
 */
class AdminForm extends Admin
{
    public $role_name;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
         return [
             [['account','nickname','password', 'role_name'], 'required'],
             ['account', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/','message'=>'账号至少包含8个字符，至少包括以下2种字符：大写字母、小写字母、数字、符号'],
             ['password', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/','message'=>'密码至少包含8个字符，至少包括以下2种字符：大写字母、小写字母、数字、符号'],
             [['nickname'],'string','length'=>[2,20],'message'=>'昵称至少输入2个汉字'],
             [['account', 'nickname', 'remark', 'auth_key','password'], 'string'],
             [['status', 'create_id', 'update_id', 'create_at', 'update_at'], 'integer'],
             [['login_ip'], 'string', 'max' => 64],
             ['account','validateExist','on'=>['addadmin']],
             ['account', 'updateValidateExist', 'on'=>['updateadmin']],
             ['remark','required','on'=>['updateadmin']],
             ['role_name','validateRole'],
         ];
    }

    /**
     * 验证角色是否合法.
     *
     * @param $attribute
     */
    public function validateRole($attribute)
    {
        $roles = Yii::$app->authManager->getRoles();
        $allRolesArray = array_keys($roles);
        if (array_diff($this->role_name, $allRolesArray)) {
            $this->addError($attribute, '角色不存在!');
        }
    }

    /**
     * @return string
     */
    public function getRoleName()
    {
        $data =  Yii::$app->authManager->getRolesByUser($this->id);
        return array_keys($data);
    }

}
