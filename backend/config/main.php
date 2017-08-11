<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'language'=>'zh-CN',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'redactor' => 'yii\redactor\RedactorModule',
    ],
    'defaultRoute'=>'default/index',
    'components' => [
        'request' => [
//            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => '0PZDdlzm_yBNXEaaw-YreetUMInaQnZG',
        ],
        'user' => [
            'identityClass' => 'backend\models\Admin',
            'enableAutoLogin' => true,
            'loginUrl' => ['/login/index'],
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'db'=>require(__DIR__ . '/db.php'),
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'ip2region' => [
            'class' => '\xiaogouxo\ip2region\Geolocation',
            'mode' => 'SEARCH_BTREE',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],

    ],
    'params' => $params,

];
