<?php
namespace frontend\models\Versions;


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
class Version extends FActiveRecord
{


    const PLATFORM_ANDROID = 'android';//平台-安卓
    const PLATFORM_IOS = 'ios';//平台-ios

    /** 客户端平台数组
     * @var array
     */
    public static $platArr = [
            self::PLATFORM_ANDROID,
            self::PLATFORM_IOS,
        ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'version';
    }

    public function rules()
    {
        return [

            [['url', 'version', 'platform','info'], 'safe'],
        ];
    }

}
