<?php

namespace frontend\models\Versions;

use frontend\models\ErrCode;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class VersionForm extends Version
{
    public $platform ;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['platform'], 'required'],
            ['platform','platformValidate'],
        ];
    }


    public function platformValidate()
    {
        $this->platform = strtolower($this->platform);

        if(!in_array($this->platform, Version::$platArr))
        {
            return $this->addError('platform','平台参数非法');
        }
    }


    public function checkUpdate()
    {
        if($this->validate())
        {
            $_version =  Version::find()->where(['platform'=>$this->platform])->orderBy('id desc')->one();
            if(empty($_version))
            {
                return $this->jsonResponse([],$this->platform . '已是最新版本', '1', ErrCode::THE_PLATFORM_VERSION_NO_DATA);
            }
            $data = [];
            $data['version'] = $_version['version'];
            $data['platform'] = $_version['platform'];
            $data['content'] = $_version['info'];
            $data['url']    = $_version['url'];
            if($_version['platform']==Version::PLATFORM_ANDROID)
            {
                $data['url'] = Yii::$app->params['fileVersionBaseDomain'].'/'.$_version['url'];
            }

            return $this->jsonResponse($data,'操作成功', '0', ErrCode::SUCCESS);
        }else{
            return $this->jsonResponse([],$this->getErrors(), '1', ErrCode::VALIDATION_NOT_PASS);
        }
    }
}
