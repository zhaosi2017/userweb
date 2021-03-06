<?php
namespace frontend\controllers;
use frontend\models\Reports\ActiveApp;
use frontend\models\Reports\ActiveDay;
use frontend\models\Reports\ActiveOne;
use Yii;
use frontend\models\Channel;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use frontend\models\User;
use frontend\models\ErrCode;


class StatisticsController extends AuthController
{

    public $enableCsrfValidation = false;


    public function behaviors()
    {

        $self = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['active-one','active-app'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['post'],
                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors, $self);
    }

    public function actionIndex()
    {
        try{
            $data = $this->getRequestContent();
            $activeDay = new ActiveDay();
            return $activeDay->statistics();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

    /**
     * app,只使用一次
     */
    public function actionActiveOne()
    {
        try{
            $data = Yii::$app->request->getHeaders();
            $activeDay = new ActiveOne();
            return $activeDay->writeLogs($data);
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }


    public function actionActiveApp()
    {
        try{
            $data = Yii::$app->request->getHeaders();
            $activeDay = new ActiveApp();
            return $activeDay->writeLogs($data);
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }



}


