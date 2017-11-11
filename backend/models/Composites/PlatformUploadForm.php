<?php
/**
 * Created by PhpStorm.
 * Users: nengliu
 * Date: 2017/8/30
 * Time: 下午4:41
 */
namespace backend\models\Composites;

use yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class PlatformUploadForm extends Model
{
    /**
     * @var $imageFile UploadedFile[];
     */
    public $imageFile;
    public $imageGrayFile;
    public $url;

    public function rules()
    {
        return [
            // 数据验证.
            // [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg'],
            [['url'],'safe'],
        ];
    }

    /**
     * 上传图片.
     *
     * @return bool|string
     */
    public function upload()
    {

        if ($this->validate()) {
            $time =  date("Ymd");
            $path = Yii::getAlias('@versions') . '/' .$time;
            if(!is_dir($path) || !is_writable($path)){
                FileHelper::createDirectory($path, 0777, true);
            }

            $_tmp = '/' . Yii::$app->request->post('model','') . '_' .md5(uniqid() . mt_rand(10000,99999999)) . '.' . $this->url->extension;
            $tmp = Yii::getAlias('@versions-relative').'/' . $time.$_tmp;

            $filePath = $path .$_tmp;
//            file_put_contents('/tmp/my.log',$filePath.PHP_EOL,8);
//            file_put_contents('/tmp/my.log',$tmp.PHP_EOL,8);

            if ($this->url->saveAs($filePath)) {
                return $tmp;
            }
        }

        return false;
    }

    /**
     * 这里在upload中定义了上传目录根目录别名，以及图片域名将/var/www/html/gushanxia/upload/20160626/file.png 转化为 http://statics.gushanxia.com/20160626/file.png.
     *
     * @param string $filePath 图片相对路径.
     *
     * @return string
     */
    private function parseImageUrl($filePath)
    {
        if(strpos($filePath, Yii::getAlias('@upload')) !== false){
            return str_replace(Yii::getAlias('@upload'),'',$filePath);
        }else{
            return $filePath;
        }
    }

}