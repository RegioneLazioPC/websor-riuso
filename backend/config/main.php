<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'name' => '#Web<b>SOR</b>',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log', 'ProtectAdmin', 'FilteredActions'],
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module',
        ],
        'datecontrol' => [
            'class' => 'kartik\datecontrol\Module',
        ]
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'FilteredActions' => [
            'class' => 'common\components\FilteredActions'
        ],
        'ProtectAdmin' => [
            'class' => 'common\components\ProtectAdmin'
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'on ' . \yii\web\User::EVENT_BEFORE_LOGOUT => ['backend\models\UserEvents', 'handleBeforeLogout'],
            'authTimeout' => 3600,
            'on afterLogin' => ['backend\events\AfterLoginEvent', 'handleNewUser'],
        ],
        'session' => [
            'name' => 'advanced-backend',
            'class' => 'yii\web\DbSession',
            'timeout' => 60 * 60,
            //'timeout' => 20,
            'writeCallback' => function ($session) {
                return [
                    'id_user' => Yii::$app->user->id,
                    'last_write' => Yii::$app->formatter->asDatetime('now', 'php:Y-m-d H:i:s'),
                ];
            },
        ],
        'log' => [
            'traceLevel' => 3, //YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@backend/runtime/logs/websor/' . date('Y_m_d') . '.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'logFile' => '@backend/runtime/logs/memory/' . date('Y_m_d') . '.log',
                    'categories' => ['memory'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'logFile' => '@backend/runtime/logs/push_attivazione/' . date('Y_m_d') . '.log',
                    'categories' => ['push_attivazione'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
];
