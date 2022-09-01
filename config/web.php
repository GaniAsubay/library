<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'mongodb' => [
            'class' => '\yii\mongodb\Connection',
            'dsn' => 'mongodb://127.0.0.1:27017/?compressors=disabled&gssapiServiceName=mongodb',
            'defaultDatabaseName' => 'test',
        ],
        'response' => [
            'format' => \yii\web\Response::FORMAT_JSON
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'api',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
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
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'GET authors' => 'authors/index',
                'GET authors/statistic' => 'authors/statistic',
                'GET authors/<id>' => 'authors/view',
                'PUT authors/<id>' => 'authors/update',
                'DELETE authors/<id>' => 'authors/delete',
                'GET books/<id>' => 'books/view',
                'PUT books/<id>' => 'books/update',
                'DELETE books/<id>' => 'books/delete',
                ['class' => 'yii\rest\UrlRule', 'controller' => 'authors'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'books'],
            ],
        ]
    ],
    'params' => $params,
];

return $config;
