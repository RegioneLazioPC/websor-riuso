<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'Europe/Berlin',
    'bootstrap' => ['admin', 'FilteredActions'],
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            'mainLayout' => '@app/views/layouts/main.php',
        ]
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'FilteredActions' => [
            'class' => 'common\components\FilteredActions'
        ],
        'log' => [
            'traceLevel' => 0, //YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@backend/runtime/logs/mgo_sync/' . date('Y_m_d') . '.log',
                    'categories' => ['sync'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@backend/runtime/logs/mas/' . date('Y_m_d') . '.log',
                    'categories' => ['mas'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@backend/runtime/logs/cap/' . date('Y_m_d') . '.log',
                    'categories' => ['cap'],
                ],
                // default
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@backend/runtime/logs/websor/' . date('Y_m_d') . '.log'
                ]
            ],
        ],
    ],
];
