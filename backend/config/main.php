<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);
ini_set('max_execution_time', 3600);
Yii::setAlias('@coco01', 'http://www.coco01.net/');
return [
    'id' => 'kiwi-app',
    'name'=>'Kiwi Tools',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log','gii'],
    'modules' => [
      'gii' => [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'] // adjust this to your needs
      ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'kiwi-backend',
            'timeout' => 657567576,
        ],
        'log' => [
            //'traceLevel' => YII_DEBUG ? 3 : 0,
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
         'view' => [
            'theme' => [
                'pathMap' => ['@app/views' => '@app/themes/adminlte'],
                'baseUrl' => '@web/../themes/adminlte',
            ],
        ],
        
        'urlManager' => [
            'rules' => [
                '<controller:post>/<id:\w+>' => 'post/site-redirect',
            ],
        ],
        
    ],
    'params' => $params,
];
