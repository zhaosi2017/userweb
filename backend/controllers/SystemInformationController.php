<?php
/**
 * Created by PhpStorm.
 * User: nengliu
 * Date: 2017/10/3
 * Time: 下午2:36
 */
namespace backend\controllers;

use probe\Factory;
use Yii;
use yii\web\Response;
use backend\models\logreader\LogFile;
use backend\models\logreader\Reader;

class SystemInformationController extends PController
{
    public $dirs = ['@app/runtime/logs','@frontend/runtime/logs'];

    public function actionIndex()
    {
        $provider = Factory::create();
        if ($provider) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                if ($key = Yii::$app->request->get('data')) {
                    switch($key){
                        case 'cpu_usage':
                            return $provider->getCpuUsage();
                            break;
                        case 'memory_usage':
                            return ($provider->getTotalMem() - $provider->getFreeMem()) / $provider->getTotalMem();
                            break;
                    }
                }
            } else {
                return $this->render('index', ['provider' => $provider]);
            }
        } else {
            return $this->render('fail');
        }
    }

    public function actionLog()
    {
        $files = [];
        foreach ($this->dirs as $dir)
        {

            $path = Yii::getAlias($dir);
            if(is_dir($path))
            {
                $list = scandir($path);
                foreach ($list as $item)
                {
                    if (preg_match('|\.log$|', $item))
                    {
                        $model = new Reader($path, $item);
                        if($model !== false) $files[] = $model;
                    }
                }
            }
        }

        usort($files, [LogFile::className(), 'sort']);

        return $this->render('log', [
            'files' => $files,
        ]);
    }

}