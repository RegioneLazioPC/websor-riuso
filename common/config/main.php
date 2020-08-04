<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'Europe/Berlin',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'traceLevel' => 0,//YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error','warning'],
                    'logFile' => '@backend/runtime/logs/mgo_sync/'.date('Y_m_d').'.log',
                    'categories' => ['sync'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error','warning'],
                    'logFile' => '@backend/runtime/logs/mas/'.date('Y_m_d').'.log',
                    'categories' => ['mas'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => [ 'error','warning'],
                    'logFile' => '@backend/runtime/logs/websor/'.date('Y_m_d').'.log'                    
                ]                 
            ],
        ],        
    ],
];
