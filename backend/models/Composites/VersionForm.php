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
            ['platform','checkPlatform'],
        ];
    }

    public function checkPlatform()
    {

       $res =  Version::find()->where(['platform'=>$this->platform,'version'=>$this->version])->one();

       if(!empty($res))
       {
           $this->addError('platform','该'.$this->platform.'已经存在该版本号:'.$this->version);
       }
    }





}
