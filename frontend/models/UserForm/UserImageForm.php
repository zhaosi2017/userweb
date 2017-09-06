<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\UserForm;

use frontend\models\FActiveRecord;
use frontend\models\User;
USE frontend\models\ErrCode;
USE yii;
use frontend\models\Channel;
use yii\db\Transaction;
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
class UserImageForm extends User
{
    public $file;

    public function rules()
    {
        return
            [
                ['file', 'required'],
                [['file'],'file', 'maxSize'=>4000000,'maxFiles' => 1],
                ['file','ValidatorExtension'],
            ];
    }

    public function ValidatorExtension()
    {
        if( !preg_match('/^image/',$this->file->type))
        {
            $this->addError('file','上传文件必须是图片');
        }
//        if(!in_array($this->file->extension,['png','jpg','gif']))
//        {
//
//            $this->addError('file','上传文件格式必须为 png,jpg,gif当中一中');
//        }
    }

    public function upload()
    {
        if ($this->validate()) {
            $identity = Yii::$app->user->identity;
            $account = $identity->account;
            $path = 'uploads/headerImage/' . $account . '.' . $this->file->extension;

            $identity->header_img = $path;
            Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
            $transaction = Yii::$app->db->getTransaction();

            if($identity->save())
            {
                if($this->file->saveAs($path))
                {
                    $transaction->commit();
                    return $this->jsonResponse(['img_url'=>$path],'操作成功','0',ErrCode::SUCCESS);
                }else{
                    $transaction->rollBack();
                    return $this->jsonResponse([],'上传文件失败','1',ErrCode::UPLOAD_FILE_FAILURE);
                }

            }else{
                $transaction->rollBack();
                return $this->jsonResponse([],$identity->getErrors(),'1',ErrCode::DATA_SAVE_ERROR);
            }

        }else{
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }
}