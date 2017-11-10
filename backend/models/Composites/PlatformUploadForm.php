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
            $path = Yii::getAlias('@versions') . '/' . date("Ymd");
            if(!is_dir($path) || !is_writable($path)){
                FileHelper::createDirectory($path, 0777, true);
            }


            $filePath = $path .'/' . Yii::$app->request->post('model','') . '_' .md5(uniqid() . mt_rand(10000,99999999)) . '.' . $this->url->extension;
            if ($this->url->saveAs($filePath)) {
                return $this->parseImageUrl($filePath);
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