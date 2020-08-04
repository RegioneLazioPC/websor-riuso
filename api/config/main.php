<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'basePath' => '@app/modules/v1',
            'class' => 'api\modules\v1\Module'
        ],
        'snc' => [
            'basePath' => '@app/modules/snc',
            'class' => 'api\modules\snc\Module'
        ],
        'map' => [
            'basePath' => '@app/modules/map',
            'class' => 'api\modules\map\Module'
        ],
        'mas' => [
            'basePath' => '@app/modules/mas',
            'class' => 'api\modules\mas\Module'
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@api/runtime/logs/common/'.date('Y_m_d').'.log',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error'],
                    'categories' => ['api'],
                    //'logFile' => '@app/runtime/logs/api.log',
                    'logFile' => '@api/runtime/logs/app/'.date('Y_m_d').'.log',
                    'maxFileSize' => 1024 * 2,
                    //'maxLogFiles' => 50,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['sync'],
                    'logFile' => '@api/runtime/logs/mgo_sync/'.date('Y_m_d').'.log',
                    'maxFileSize' => 1024 * 2
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error'],
                    'categories' => ['map'],
                    'logFile' => '@api/runtime/logs/map/'.date('Y_m_d').'.log',
                    'maxFileSize' => 1024 * 2,
                    //'maxLogFiles' => 50,
                ],
            ],
        ],

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'request' => [
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'jwt' => [
            'class' => 'sizeg\jwt\Jwt',
            'key'   => $params['secret-key'],
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'format' => yii\web\Response::FORMAT_JSON,
            'on beforeSend' => function ($event) {

                $headers = Yii::$app->response->headers;

                $headers->add('Access-Control-Allow-Origin', '*');
                $headers->add('Access-Control-Allow-Headers', 'Authorization');
                $headers->add('Access-Control-Allow-Headers', 'Content-Type');
                $headers->add('Access-Control-Allow-Headers', 'Range');
                //$headers->add('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT', 'DELETE');
                
                $headers->add('Access-Control-Allow-Methods', 'POST');
                $headers->add('Access-Control-Allow-Methods', 'PUT');
                $headers->add('Access-Control-Allow-Methods', 'GET');
                $headers->add('Access-Control-Allow-Methods', 'DELETE');
                $headers->add('Access-Control-Allow-Methods', 'OPTIONS');

                $headers->add('Access-Control-Request-Method', 'POST');
                $headers->add('Access-Control-Request-Method', 'PUT');
                $headers->add('Access-Control-Request-Method', 'GET');
                $headers->add('Access-Control-Request-Method', 'DELETE');
                $headers->add('Access-Control-Request-Method', 'OPTIONS');

                $headers->add('Access-Control-Request-Headers', 'X-Wsse');

                $headers->add('Access-Control-Allow-Credentials', true);
                $headers->add('Access-Control-Max-Age', 3600);
                $headers->add('Access-Control-Expose-Headers', 'X-Pagination-Current-Page');
                $headers->add('Access-Control-Expose-Headers', 'Accept-Ranges');
                $headers->add('Access-Control-Expose-Headers', 'Content-Encoding');
                $headers->add('Access-Control-Expose-Headers', 'Content-Length');
                $headers->add('Access-Control-Expose-Headers', 'Content-Range');

                
                
            },
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    // APP CONFIG SERVICE
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/auth',
                    'except' => ['patch', 'head'],
                    'extraPatterns' => [
                        'GET confirm' => 'confirm',
                        'GET profile' => 'profile',
                        'POST login' => 'login',
                        'GET reset' => 'reset',
                        'POST reset' => 'reset',
                        'POST recovery' => 'recovery',
                        'OPTIONS <url:.*>' => 'options',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/segnalazione',
                    'extraPatterns' => [
                        'POST list-by-user' => 'list-by-user',
                        //'POST check-is-correct-region' => 'check-is-correct-region'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/utenti',
                    'extraPatterns' => [
                        //'POST login' => 'login',
                        //'GET sendsms/{id}' => 'send-sms',
                        //'GET is-user-active/{id}' => 'is-user-active',
                        //'POST reset-password/{token}' => 'reset-password',
                        //'POST request-new-password' => 'request-new-password',
                        //'POST send-sms-by-phone-number' => 'send-sms-by-phone-number',
                        //'POST check-sms-code' => 'check-sms-code'
                    ],
                    'tokens' => [
                        '{token}' => '<token>',
                        '{id}' => '<id:\d+>'
                    ]

                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/evento',
                    'extraPatterns' => [
                        'GET tipologia' => 'tipologia',
                        'GET list-by-geo' => 'list-by-geo',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/notifications',
                    'extraPatterns' => [
                        //'GET /' => 'index'                        
                    ]
                ],
                /*
                
                    Utilizzare modulo "map"
                    [
                        'class' => 'yii\rest\UrlRule',
                        'pluralize' => false,
                        'controller' => 'v1/map',
                        'extraPatterns' => [
                            'POST segnalazione/to-event/<id>' => 'change-to-event',
                            'POST create/event' => 'create-event',
                            'POST create/segnalazione' => 'create-segnalazione',
                            'POST eventi/<event_id>' => 'eventi-set-lat-lon',
                            'GET all' => 'get-all',
                            'GET segnalazione/attach-event/<id>/<idEvento>' => 'attach-evento',
                            'GET eventi/<lat>/<lon>' => 'event-near',
                            'GET eventi' => 'eventi',
                            'GET segnalazioni' => 'segnalazioni',
                            'GET sede/<id>' => 'get-sede',
                            'GET ingaggi/<idevento>' => 'get-ingaggi',
                            'GET fronti/<idevento>' => 'get-fronti',
                            'GET segnalazioni/<idevento>' => 'get-segnalazioni',
                            'GET ingaggia' => 'engage',
                            'GET associazioni' => 'get-associazioni',
                            'GET query' => 'query',
                            'GET radio' => 'radio',
                            'GET elicotteri' => 'elicotteri',
                            'GET address' => 'address',
                        ]
                    ],
                */
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/media',
                    'extraPatterns' => [
                        //'GET view-media/{id}' => 'view-media',
                        'GET image/<image>' => 'view-media-from-web',
                        'GET image/markers/<image>' => 'view-media-from-web-markers',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'snc/sync',
                    'extraPatterns' => [
                        'POST add' => 'add',
                    ]
                ],
                
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'mas/feedback',
                    'extraPatterns' => [
                        'POST mas/<id>/<token>' => 'verify'
                    ]
                ],

                /**
                 * Servizi mappa cartografia di websor
                 */
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'map/media',
                    'extraPatterns' => [
                        'GET view-media/{id}' => 'view-media'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'map/auth',
                    'except' => ['patch', 'head'],
                    'extraPatterns' => [
                        'OPTIONS <url:.*>' => 'options',
                        'POST login' => 'login',
                        'GET profile' => 'profile'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'map/stub',
                    'except' => ['patch', 'head'],
                    'extraPatterns' => [
                        'OPTIONS <url:.*>' => 'options',
                        'GET layers' => 'layers',
                        'GET data' => 'data'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'map/autocomplete',
                    'except' => ['patch', 'head'],
                    'extraPatterns' => [
                        'OPTIONS <url:.*>' => 'options',
                        'GET search' => 'search'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'map/evento',
                    'except' => ['patch', 'head'],
                    'extraPatterns' => [
                        'OPTIONS <url:.*>' => 'options',
                        'GET {id}' => 'view',
                        'POST {id}/change-position' => 'change-position'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'map/segnalazione',
                    'except' => ['patch', 'head'],
                    'extraPatterns' => [
                        'OPTIONS <url:.*>' => 'options',
                        'GET {id}' => 'view',
                        'POST {id}/change-position' => 'change-position',
                        'POST {id}/attach-event/<idEvento>' => 'attach-evento',
                        'POST {id}/change-to-event' => 'change-to-event'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'map/organizzazione',
                    'except' => ['patch', 'head'],
                    'extraPatterns' => [
                        'OPTIONS <url:.*>' => 'options',
                        'GET {id}' => 'view',
                        'GET all' => 'all'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'map/ingaggi',
                    'except' => ['patch', 'head'],
                    'extraPatterns' => [
                        'OPTIONS <url:.*>' => 'options',
                        'GET search' => 'search',
                        'GET engage' => 'engage'
                    ]
                ],
            ],
        ]
    ],
    'params' => $params,
];