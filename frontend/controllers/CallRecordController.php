<?php
namespace frontend\controllers;

use frontend\models\WhiteLists\WhiteListForm;
use frontend\models\UserForm\WhiteListSwitchForm;
use frontend\models\WhiteLists\WhiteList;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use frontend\models\ErrCode;
use frontend\models\CallRecord\CallRecord;
use frontend\models\CallRecord\CallRecordDetail;

class CallRecordController extends AuthController
{

    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        $self = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [ 'detail', 'lists'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'lists' => ['post'],
                    'detail' => ['post']

                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors, $self);
    }


    public function actionLists()
    {

        try{
            $data = $this->getRequestContent();
            $callRecord = new CallRecord();
            $p = isset($data['p']) && $data['p']>0 ? (int)$data['p'] : 0;
            return $callRecord->lists($p);
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }


    }

    public function actionDetail()
    {
        try{
            $data = $this->getRequestContent();
            $callRecord = new CallRecordDetail() ;
            $callRecord->cid = isset($data['cid']) ? $data['cid'] :'';
            return $callRecord->detail();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }



}
