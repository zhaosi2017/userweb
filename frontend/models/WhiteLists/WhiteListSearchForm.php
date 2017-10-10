<?php
namespace frontend\models\WhiteLists;


use frontend\models\ErrCode;
use frontend\models\User;
use frontend\models\WhiteLists\WhiteList;
use yii\base\Model;
use frontend\models\Friends\Friends;
use frontend\models\BlackLists\BlackList;
use Yii;
class WhiteListSearchForm extends WhiteList
{

    const PAGE_NUM = 20; //每页20个数据

    public function lists($p)
    {
        $userId = Yii::$app->user->id ;
        $limit =  self::PAGE_NUM;
        $offset = $p == 0 ? 0: self::PAGE_NUM*$p;
        $res = self::find()->select('white_uid')->where(['uid'=>$userId])->offset($offset)->limit($limit)->orderBy('id desc')->all();
        $data = [];
        if(!empty($res))
        {
            foreach ($res as $k => $v)
            {
                $_friends = Friends::find()->select('remark')->where(['user_id'=>$userId,'friend_id'=>$v['white_uid']])->one();
                $_user = User::find()->select(['nickname','account','header_img'])->where(['id'=>$v['white_uid']])->one();
                if(  !empty($_user)) {
                    $data[$k]['account'] = $_user->account;
                    $data[$k]['remark'] = isset($_friends['remark']) && $_friends['remark'] ? $_friends['remark'] : $_user['nickname'];
                    $data[$k]['header_url'] = $_user['header_img'] ?  Yii::$app->params['frontendBaseDomain'].$_user['header_img'] :'';
                }
            }
        }
        return  $this->jsonResponse($data,'操作成功',0, ErrCode::SUCCESS);

    }
}
