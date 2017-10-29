<?php
namespace frontend\models;


use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use frontend\models\FActiveRecord;
use frontend\services\SmsService;


/**
 * User model
 *
 * @property integer $id
 * @property string $account
 */
class UserAppBind extends FActiveRecord
{

    const APP_TYPE_POTATO   = 0;
    const APP_TYPE_TELEGRAM = 1;

    static $typeArr =[
        self::APP_TYPE_POTATO =>'potato',
        self::APP_TYPE_TELEGRAM =>'telegram',
    ];
    const PAGE_NUM = 20; //每页20个数据

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_bindapp';
    }



    public function potatoLists($p)
    {

        $userId = Yii::$app->user->id ;
        $limit =  self::PAGE_NUM;
        $offset = $p == 0 ? 0: self::PAGE_NUM*$p;
        $res = self::find()->where(['user_id'=>$userId,'type'=>UserAppBind::APP_TYPE_POTATO])->offset($offset)->limit($limit)->orderBy('sort asc ,id desc')->all();
        return  $this->jsonResponse($res,'操作成功',0, ErrCode::SUCCESS);

    }


    public function telegramLists($p)
    {

        $userId = Yii::$app->user->id ;
        $limit =  self::PAGE_NUM;
        $offset = $p == 0 ? 0: self::PAGE_NUM*$p;
        $res = self::find()->where(['user_id'=>$userId,'type'=>UserAppBind::APP_TYPE_TELEGRAM])->offset($offset)->limit($limit)->orderBy('sort asc ,id desc')->all();
        return  $this->jsonResponse($res,'操作成功',0, ErrCode::SUCCESS);

    }

}