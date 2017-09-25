<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\Friends;

use frontend\models\ErrCode;
use frontend\models\FActiveRecord;
use frontend\models\User;
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
class Friends extends FActiveRecord {

    const FRIENDS_LIMIT = 20;
    const GROUP_EMPTY = 0; //表示好友没有被分到自己的建立的分组里面


    const IS_NEW_FRIEND = 0; //新朋友
    const NOT_IS_NEW_FRIEND = 1;//不是新朋友
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'friends';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['user_id','friend_id','create_at','group_id','direction','link_time'] ,'integer'],
            ['remark','string','max'=>64],
            ['extsion','string'],
        ];
    }

    public function lists()
    {
        $userId = \Yii::$app->user->id;
        $newFriend = self::find()->where(['user_id'=>$userId,'is_new_friend'=>self::IS_NEW_FRIEND])->count();


        $recent_friends = self::find()->where(['user_id'=>$userId])->orderBy('link_time desc,id desc')->limit(SELF::FRIENDS_LIMIT)->all() ;
        $_recent = [];
        if(!empty($recent_friends))
        {
            foreach ($recent_friends as $k => $v)
            {
                $_user = User::findOne($v->friend_id);
                if(empty($_user))
                {
                    continue;
                }
                $_recent[$k]['nickname'] = $v['remark'] ? $v['remark'] : $_user['nickname'];
                $_recent[$k]['account']  = $_user['account'];
                $_recent[$k]['msg'] ='';
                $_recent[$k]['channel'] =$_user['channel'];
                $_recent[$k]['header_url'] = $_user['header_img']? \Yii::$app->params['frontendBaseDomain'].$_user['header_img'] : '';
                if($v->link_time == 0)
                {
                    $_recent[$k]['msg'] = $v->direction == 0 ? '已经接受你的请求':'已接受该用户的请求';
                }
            }
        }



        $friends = self::find()->where(['user_id'=>$userId])->all();

        $settlesRes = [];
        if(!empty($friends)) {
            //YII_BASE_PATH.'/js/pydic.js' 这个文件是新华字典的汉字字典
            $pydic = file_get_contents(YII_BASE_PATH.'/js/pydic.js');
            $pydic = mb_convert_encoding($pydic,'UTF-8');

            foreach ($friends as $k => $sett) {
                $_name = $sett['remark'];
                $_u = User::findOne($sett->friend_id);
                if(empty($_u))
                {
                    continue;
                }
                if (empty($_name)) {
                    $_name = isset($_u->nickname) ? $_u->nickname : '';
                }
                if (empty($_name)) {
                    $_other = [
                        "nickname"=>'',
                        'account'=>isset($_u->account) ? $_u->account :'',
                        'header_url'=>isset($_u->header_img) && $_u->header_img ? \Yii::$app->params['frontendBaseDomain'].$_u->header_img :'',
                        'channel'=>isset($_u->channel) ? $_u->channel :'',
                    ];
                    $settlesRes['other'][]= $_other;
                    continue;
                }

                $sett['remark'] = $_name;
                $snameFirstChar = $this->_getMyFirstCharter($_name,$pydic); //取出门店的第一个汉字的首字母
                $_tmp = [
                    "nickname"=>$sett['remark'],
                    'account'=>isset($_u->account) ? $_u->account :'',
                    'header_url'=>isset($_u->header_img) && $_u->header_img? \Yii::$app->params['frontendBaseDomain'].$_u->header_img :'',
                    'channel'=>isset($_u->channel) ? $_u->channel :'',
                ];
                $settlesRes[$snameFirstChar][] = $_tmp;
            }
            ksort($settlesRes); //对数据进行ksort排序，以key的值升序排序
        }
        return $this->jsonResponse(['newFriend'=>$newFriend,'recent'=>$_recent, 'friend'=>$settlesRes],'操作成功',0,ErrCode::SUCCESS);


    }

    //获取新朋友列表
    public function newFriendList()
    {
        $userId = \Yii::$app->user->id;
        $new_friends = self::find()->where(['user_id'=>$userId,'is_new_friend'=>self::IS_NEW_FRIEND])->all();
        $_friend = [];
        if(!empty($new_friends)){
            foreach ($new_friends as $k => $f){
                $_u = User::findOne([$f->friend_id]);
                if(!empty($_u)) {
                    $_friend[$k]['nickname'] = $f->remark ? $f->remark : $_u->nickname;
                    $_friend[$k]['account'] = $_u->account;
                    $_friend[$k]['header_url'] = $_u->header_img;

                }
            }

            self::updateAll(['is_new_friend'=>self::NOT_IS_NEW_FRIEND],['user_id'=>$userId,'is_new_friend'=>self::IS_NEW_FRIEND]);

        }
        return $this->jsonResponse($_friend,'操作成功',0,ErrCode::SUCCESS);
    }

    public function newFriendNum()
    {
        $userId = \Yii::$app->user->id;
        $_friend = self::find()->where(['user_id'=>$userId,'is_new_friend'=>self::IS_NEW_FRIEND])->count();
        if($_friend)
        {

            self::updateAll(['is_new_friend'=> self::NOT_IS_NEW_FRIEND],['user_id'=>$userId,'is_new_friend'=>self::IS_NEW_FRIEND]);
        }

        return $this->jsonResponse(['new-friend-num'=>$_friend],'操作成功',0,ErrCode::SUCCESS);
    }



    private function _getMyFirstCharter($str,$pydic){
        //简体 繁体 等
        if(empty($str) ){return 'other';}

        if(preg_match('/^[a-zA-Z]$/',$str{0}))
        {
            return strtoupper($str{0});
        }
        $_char=ord($str{0});

//        if($_char>=ord('A')&&$_char<=ord('z')) return strtoupper($str{0});

        $_str=mb_substr($str,0,1,'UTF-8');//获取第一个字符

        if(is_numeric($_str))
        {
            return 'other';
        }
        $postion =  strripos($pydic,$_str);

        if($postion)
        {
            $t = substr($pydic,$postion+3,1);
            $t = strtoupper($t);
            return $t;
        }
        return 'other';

    }



    private function _getFirstCharter($str){
        //只能处理简体中文

        if(empty($str)){return 'other';}
        $fchar=ord($str{0});
        if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
        $s1 = mb_convert_encoding($str,'GB18030');
        $s2 = mb_convert_encoding($s1,'UTF-8');
        $s= $s2 == $str? $str: $s1;

        if(!isset($s{1}))
        {
            return 'other';
        }

        $asc=ord($s{0})*256+ord($s{1})-65536;


        if($asc>=-20319&&$asc<=-20284) return 'A';
        if($asc>=-20283&&$asc<=-19776) return 'B';
        if($asc>=-19775&&$asc<=-19219) return 'C';
        if($asc>=-19218&&$asc<=-18711) return 'D';
        if($asc>=-18710&&$asc<=-18527) return 'E';
        if($asc>=-18526&&$asc<=-18240) return 'F';
        if($asc>=-18239&&$asc<=-17923) return 'G';
        if($asc>=-17922&&$asc<=-17418) return 'H';
        if($asc>=-17417&&$asc<=-16475) return 'J';
        if($asc>=-16474&&$asc<=-16213) return 'K';
        if($asc>=-16212&&$asc<=-15641) return 'L';
        if($asc>=-15640&&$asc<=-15166) return 'M';
        if($asc>=-15165&&$asc<=-14923) return 'N';
        if($asc>=-14922&&$asc<=-14915) return 'O';
        if($asc>=-14914&&$asc<=-14631) return 'P';
        if($asc>=-14630&&$asc<=-14150) return 'Q';
        if($asc>=-14149&&$asc<=-14091) return 'R';
        if($asc>=-14090&&$asc<=-13319) return 'S';
        if($asc>=-13318&&$asc<=-12839) return 'T';
        if($asc>=-12838&&$asc<=-12557) return 'W';
        if($asc>=-12556&&$asc<=-11848) return 'X';
        if($asc>=-11847&&$asc<=-11056) return 'Y';
        if($asc>=-11055&&$asc<=-10247) return 'Z';


        return 'other';
    }


    public function getGroupList()
    {
        $userId = \Yii::$app->user->id;
        //获取用户自己的分组
        $_friendGroup = FriendsGroup::findAll(['user_id'=>$userId]);
        $data = [];
        $tmp = [];
        if(!empty($_friendGroup))
        {
            foreach ($_friendGroup as $k =>$_group)
            {
                //获取该分组下所有的好友
                $_friend = Friends::findAll(['group_id'=>$_group->id]);
                $_f = [];
                if(!empty($_friend))
                {
                    foreach ($_friend as $i =>$f)
                    {
                        //获取分组下某一个具体的好友信息
                        $_user = User::findOne($f->friend_id);
                        if(empty($_user))
                        {
                            continue;
                        }
                        $_f[$i]['account']  = $_user->account;
                        $_f[$i]['nickname'] = !empty($f->remark)?  $f->remark : $_user->nickname;
                        $_f[$i]['header_url'] = $_user->header_img? \Yii::$app->params['frontendBaseDomain'].$_user->header_img : '';
                    }
                }

                $tmp[$k] = [
                    'friend'=>$_f,
                    'group_info'=>$_group,
                ];
            }

        }
        $data['group'] = $tmp;
        //获取所有的好友，且不在自己建立的分组下
        $other = Friends::findAll(['user_id'=>$userId,'group_id'=>self::GROUP_EMPTY]);
        $_other = [];
        if(!empty($other))
        {
            foreach ($other as $j => $o) {
                $_user = User::findOne($o->friend_id);
                if (empty($_user)) {
                    continue;
                }
                $_other[$j]['account'] = $_user->account;
                $_other[$j]['nickname'] = !empty($o->remark) ? $o->remark : $_user->nickname;
                $_other[$j]['header_url'] = $_user->header_img? \Yii::$app->params['frontendBaseDomain'].$_user->header_img : '';;
            }

        }
        $data['other'] = $_other;

        return $this->jsonResponse($data,'操作成功','0',ErrCode::SUCCESS);
    }


}