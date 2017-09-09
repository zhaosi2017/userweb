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
        $time = strtotime(date('Y-m-d',time()));

        $newFriend = false;


        $recent_frinds = self::find()->where(['user_id'=>$userId])->orderBy('link_time desc')->limit(SELF::FRIENDS_LIMIT)->all() ;
        $_recent = [];
        if(!empty($recent_frinds))
        {
            foreach ($recent_frinds as $k => $v)
            {
                $_user = User::findOne($v->friend_id);
                $_recent[$k]['nickname'] = $v['remark'] ? $v['remark'] : $_user['nickname'];
                $_recent[$k]['account']  = $_user['account'];
                $_recent[$k]['msg'] ='';
                $_recent[$k]['header_url'] = $_user['header_img'];
                if($v->link_time == $v->create_at)
                {
                    $newFriend = true;
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

                if (empty($_name)) {
                    $_name = isset($_u->nickname) ? $_u->nickname : '';
                }
                if (empty($_name)) {
                    $settlesRes['other'][$sett['id']]['nickname'] = '';//以这个首字母作为key
                    $settlesRes['other'][$sett['id']]['account'] = isset($_u->account) ? $_u->account :'';
                    $settlesRes['other'][$sett['id']]['header_url'] = isset($_u->header_img)? $_u->header_img :'';
                    continue;
                }

                $sett['remark'] = $_name;
                $snameFirstChar = $this->_getMyFirstCharter($_name,$pydic); //取出门店的第一个汉字的首字母
                $settlesRes[$snameFirstChar][$sett['id']]['nickname'] = $sett['remark'];//以这个首字母作为key
                $settlesRes[$snameFirstChar][$sett['id']]['account'] = isset($_u->account) ? $_u->account :'';//以这个首字母作为key
                $settlesRes[$snameFirstChar][$sett['id']]['header_url'] = isset($_u->header_img)? $_u->header_img :'';

            }
            ksort($settlesRes); //对数据进行ksort排序，以key的值升序排序
        }
        return $this->jsonResponse(['newFriend'=>$newFriend,'recent'=>$_recent, 'friend'=>$settlesRes],'操作成功',0,ErrCode::SUCCESS);


    }

    //获取新朋友列表
    public function newFriendList()
    {
        $userId = \Yii::$app->user->id;
        $new_friends = self::find()->where(['user_id'=>$userId])->andWhere("`link_time`=`create_at`")->all();
        $_friend = [];
        if(!empty($new_friends)){
            $time = time();
            foreach ($new_friends as $k => $f){
                $user = self::findOne($f['id']);
                $user->link_time = $time;
                if(!$user->save())
                {
                    return $this->jsonResponse([],$user->getErrors(),1,ErrCode::DATA_SAVE_ERROR);
                }
                $_u = User::findOne([$f->friend_id]);
                if($_u) {
                    $_friend[$k]['nickname'] = $f->remark ? $f->remark : $_u->nickname;
                    $_friend[$k]['account'] = $_u->account;
                    $_friend[$k]['header_url'] = $_u->header_img;

                }
            }

        }
        return $this->jsonResponse($_friend,'操作成功',0,ErrCode::SUCCESS);
    }

    public function newFriendNum()
    {
        $userId = \Yii::$app->user->id;
        $new_friends = self::find()->where(['user_id'=>$userId,'link_time'=>0])->all();
        $_friend = 0;
        if(!empty($new_friends)){
            $time = time();
            foreach ($new_friends as $k => $f){
                $user = self::findOne($f['id']);
                $user->link_time = $time;
                if(!$user->save())
                {
                    return $this->jsonResponse([],$user->getErrors(),1,ErrCode::DATA_SAVE_ERROR);
                }
                $_friend +=1;
            }

        }
        return $this->jsonResponse(['new-friend-num'=>$_friend],'操作成功',0,ErrCode::SUCCESS);
    }



    private function _getMyFirstCharter($str,$pydic){
        //简体 繁体 等
        if(empty($str)){return 'other';}
        $_char=ord($str{0});
        if($_char>=ord('A')&&$_char<=ord('z')) return strtoupper($str{0});
        $_str=mb_substr($str,0,1,'UTF-8');//获取第一个字符
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





}