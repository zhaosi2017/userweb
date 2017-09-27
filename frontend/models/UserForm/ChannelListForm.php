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
class ChannelListForm extends User
{
    public $account;

    public function rules()
    {
        return
            [
                ['account','safe']
            ];
    }


    public function lists()
    {
        $data = Channel::find()->select(['id', 'name', 'img_url'])->all();
        if(!empty($this->account))
        {
           $_user =  User::find()->select('channel')->where(['account'=>$this->account])->one();
        }

        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $v['img_url'] = $v['img_url']? Yii::$app->params['fileBaseDomain'] . $v['img_url']:'';
                $data[$k] = $v;
            }
        }
    }


}