<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/9/26
 * Time: 下午2:46
 * 优码工具
 */

namespace frontend\services;

use frontend\models\User;

class  UcodeService {
    /**
     * @var array
     * 号码长度 集合
     */
    static private  $CODE_LONG =[9];
    /**
     * @var array
     * 号码前缀集合
     */
    static  private $CODE_PREFIX = ["1813"];


    static public function makeCode(){

        $rand1       = rand(1,count(self::$CODE_PREFIX));
        $prefix      = self::$CODE_PREFIX[$rand1-1];   //前缀
        $prefix_long = strlen($prefix);


        $rand2 =rand(1,count(self::$CODE_LONG) );
        $long = self::$CODE_LONG[$rand2-1];            //位数

        $real_long = $long - $prefix_long;            //需要生成的优码的位数
        if($real_long <= 0 ){
            return false;
        }
        return self::make($real_long , $prefix);
    }


    static private function make($real_long,$prefix){

        $numbers = [];
        for($i=1; $i <= $real_long ; $i++){
            $numbers[$i] = rand(0,9);
        }
        if(!self::check($numbers ,$prefix )){

            return   self::make($real_long,$prefix);

        }
        $str ='';
       foreach($numbers as $v){
           $str.= $v;
       }
        return $prefix.$str;
    }

    /**
     * 校验生成的号码
     * @param array $numbers
     * @param  string $prefix
     * @return string
     */
    static private function check(Array $numbers , $prefix){

        $first = (int)substr($prefix , 0, 1);
        $numbers[0] = $first;

        if(self::v1($numbers) && self::v2($numbers) && self::v3($numbers , $prefix)){
            return true;
        }
        return false;
    }

    /**
     * 验证连续号码 123 1234 12345
     * 只适用于9位u码
     */
    static private function v1($numbers){
        $d = [];
        for($i= 1 ; $i< count($numbers) ; $i++){   //正序差
            $d[$i] = $numbers[$i] - $numbers[$i-1];
        }
        $last_d = 0;
        foreach($d as $t_d){
            if($t_d == 1 && $last_d == 1){
                return false;
            }else{
                $last_d = $t_d;
            }
        }
        foreach($d as $t_d){
            if($t_d == -1 && $last_d == -1){
                return false;
            }else{
                $last_d = $t_d;
            }
        }
        return true;
    }



    /**
     * 验证连续相同号码 AA+BB AAA  AAAA ....
     */
    static private function v2($numbers){
        $d = [];

        for($i= 1 ; $i< count($numbers) ; $i++){   //正序差
            $d[$i] = $numbers[$i] - $numbers[$i-1];
        }

        $zero_c = 0;
        foreach($d as $value){
            if($value == 0 ){
                $zero_c++;
            }
        }
        if($zero_c >= 2){
            return false;
        }
        return true;
    }

    /**
     * @param Array  $numbers
     * @param string $prefix
     * 检测数据库是否有相同的u码
     * @return  boolean
     */
    static private function v3($numbers , $prefix){
        $str ='';
        foreach($numbers as $v){
            $str.= $v;
        }
        $code = $prefix.$str;
        $model = User::findOne(['account'=>$code]);
        if(!empty($model)){
            return false;
        }
        return true;

    }


}



