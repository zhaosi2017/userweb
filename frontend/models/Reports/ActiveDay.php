<?php
namespace frontend\models\Reports;


use frontend\models\ErrCode;
use frontend\models\User;
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
class ActiveDay extends FActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'active_day';
    }


    public function statistics()
    {
        $userId = Yii::$app->user->id;
        $_user = User::find()->select('country_code')->where(['id'=>$userId])->one();
        $active = new ActiveDay();
        $_active = $active->find()->where(['user_id'=>$userId,'active_time'=>date('Y-m-d')])->one();
        if(!empty($_active)){
            return $this->jsonResponse([],'操作成功',0,ErrCode::SUCCESS);
        }
        $active->user_id = $userId;
        $active->country_code = isset($_user->country_code) ? $_user->country_code :null;
        $active->active_time = date('Y-m-d');
        $active->create_at = time();
        if($active->save())
        {
            return $this->jsonResponse([],'操作成功',0,ErrCode::SUCCESS);
        }else{
            return $this->jsonResponse([],'操作失败',1,ErrCode::FAILURE);
        }
    }

}
