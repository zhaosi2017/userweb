<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\Friends;

use frontend\models\ErrCode;
use frontend\models\UrgentContact;
use frontend\models\User;
use frontend\models\UserPhone;
use Yii;
use frontend\models\WhiteLists\WhiteList;
use frontend\models\BlackLists\BlackList;

class FriendsSearch extends User
{
    public $search_word;

    public function rules()
    {
        return [
          ['search_word','string'],
            ['search_word','safe'],
            ['account','match','pattern'=>'/^[0-9]{7,9}$/','message'=>'{attribute}必须为9位纯数字'],
        ];
    }

    //根据用户的优码 和昵称 搜索用户 以便添加好友 目前是模糊匹配 以后看需要可以该精确匹配
    public function searchUser()
    {
        if(empty($this->search_word)){
            return $this->jsonResponse([],'搜索的关键字不能为空','1',ErrCode::SEARCH_WORDS_EMPTY);
        }
       if($this->validate('search_word'))
       {
           $data =  User::find()
               ->select(['id','nickname','account','header_img'])
              ->orWhere(['like','account',$this->search_word])
              //->orWhere(['like','nickname',$this->search_word])->distinct()->all() ;
             //  ->orWhere(['account'=>$this->search_word])
               ->limit(10)
               ->distinct()->all();
              //->orWhere(['nickname'=>$nickName])->distinct()->all() ;
           $tmp = [];
           $userId = Yii::$app->user->id;
           $_friends = Friends::find()->where(['user_id'=>$userId])->indexBy('friend_id')->all();
           if(!empty($data))
           {
                foreach ($data as $k => $v)
                {


                    $tmp[$k]['id'] = $v['id'];
                    $tmp[$k]['nickname'] = $v['nickname'];
                    $tmp[$k]['account'] = $v['account'];
                    $tmp[$k]['header_url'] = $v['header_img'] ? \Yii::$app->params['frontendBaseDomain'].$v['header_img'] : '';
                    $tmp[$k]['is_friend'] =isset($_friends[$v['id']]) ? true : false;
                    $tmp[$k]['is_self'] = $userId == $v['id'] ? true : false;
                }
           }else{
               $data = User::find()
                        ->select(['id','nickname','account','header_img'])
                        ->distinct()->all();
                $i = 0;
                foreach($data as $item){
                    if($item->nickname == $this->search_word){

                        $tmp[$i]['id'] = $item->id;
                        $tmp[$i]['nickname'] = $item->nickname;
                        $tmp[$i]['account'] = $item->account;
                        $tmp[$i]['header_url'] = $item->header_img;
                        $tmp[$i]['is_friend'] =isset($_friends[$item->id]) ? true : false;
                        $tmp[$i]['is_self'] = $userId == $item->id ? true : false;
                        $i++;

                    }
                }

           }
           return $this->jsonResponse($tmp,'操作成功','0',ErrCode::SUCCESS);
       }else{
           return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
       }

    }

    public function friendDetail()
    {
        if(empty($this->account)){
            return $this->jsonResponse([],'优码不能为空','1',ErrCode::ACCOUNT_EMPTY);
        }
        if($this->validate('account'))
        {
            $user = User::find()
                ->select(['id','nickname','account','channel','header_img'])
                ->Where(['account'=>$this->account])
               ->one();
            if(empty($user))
            {
                return $this->jsonResponse([],'用户不存在','1',ErrCode::USER_NOT_EXIST);
            }
            $userId = \Yii::$app->user->id;
            $_friend = Friends::find()->where(['user_id'=>$userId,'friend_id'=>$user->id])->one();

            $white =   WhiteList::findOne(['white_uid'=>$user->id,'uid'=>\Yii::$app->user->id]);

            $black =  BlackList::findOne(['black_uid'=>$user->id,'uid'=>\Yii::$app->user->id]);

            $userPhoneNum =  UserPhone::find()->where(['user_id'=>$user->id])->count();
            $urgentContactNum =  UrgentContact::find()->where(['user_id'=>$user->id])->count();
            $data['id']=$user['id'];
            $data['nickname']= $user['nickname'];
            $data['account']=$user['account'];
            $data['remark'] = isset($_friend['remark']) && $_friend['remark'] ? $_friend['remark']: '';
            $data['channel']=$user['channel'];
            $data['header_url']= $user['header_img'] ? \Yii::$app->params['frontendBaseDomain'].$user['header_img'] :'';
            $data['userPhoneNum']=$userPhoneNum;
            $data['white_status'] =empty($white)? 0 : 1;
            $data['black_status'] = empty($black)? 0 :1;
            $data['urgentContactNum']=$urgentContactNum;
            $data['is_friend'] = empty($_friend) ? false : true;
            $data['is_self'] = $user->id == $userId ? true : false;

            return $this->jsonResponse($data,'操作成功','0',ErrCode::SUCCESS);
        }else{
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }
}
