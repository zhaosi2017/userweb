<?php
namespace frontend\services\Translates;


class TranslateService
{
    private $url = 'https://translation.googleapis.com/language/translate/v2?key=AIzaSyAV_rXQu5ObaA9_rI7iqL4EDB67oXaH3zk';
    private $source = 'en';
    private $target = 'zh-cn';
    private $text;

    protected  static  $tranArr = [
        'en-US'=>'en',
        'zh-CN'=>'zh-cn',
        'zh-TW'=>'zh-tw',
        'ko-KR'=>'ko',
        'ja-JP'=>'ja',
        'km-CB'=>'km',
    ];

    public function __construct($text, $target)
    {

        if(!array_key_exists($target,self::$tranArr))
        {
            throw new \Exception('目前暂时不支持该语言的翻译');
        }
        if(substr($text,0,10) == '[HTTP 400]')
        {
            $text = str_replace('[HTTP 400]','',$text);
        }
        $this->text = $text;
        $this->target = self::$tranArr[$target];
    }

    public function translate()
    {
        if($this->target == 'en')
        {
            return $this->text;
        }
        $data = $this->sendPost();
        if($data['status']==1)
        {
            return false;
        }

        return $data['msg'];
    }

    private function sendPost()
    {
        $res = ['status'=>0,'msg'=>''];
        try {
            $url = 'https://translation.googleapis.com/language/translate/v2?key=AIzaSyAV_rXQu5ObaA9_rI7iqL4EDB67oXaH3zk';
            $data = ["q" => $this->text, "source" => $this->source, 'target' => $this->target, 'format' => 'text'];
            $json = json_encode($data);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                array('Content-Type:application/json',
                    'Content-Length: ' . strlen($json))
            );
            $result = curl_exec($ch);

            curl_close($ch);
            $result = json_decode($result, true);
            $res['msg'] = isset($result['data']) ? $result['data']['translations']['0']['translatedText'] : '';
            return $res;
        }catch (\Exception $e)
        {
            $res['status'] = 1;
            $res['msg']=$e->getMessage();
            return $res;
        }

    }


}