<?php

namespace common\models\app\config;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\base\DynamicModel;

class Keys
{

    public static $app_keys = [
            'last_mgo_sync' => [
                'label' => 'SYNC MGO',
                'description' => 'Data ora ultimo sync MGO',
                'editable' => false,
                'form_values' => [
                    [
                        'key' => 'date',
                        'type' => 'datetime',
                        'required' => false
                    ]
                ]
            ],
            'attivazioni' => [
                'label' => 'Strategia push attivazioni',
                'description' => 'Strategia per invio push di notifica per le attivazioni',
                'editable' => true,
                'form_values' => [
                    [
                        'key' => 'strategia_invio_push',
                        'type' => 'select',
                        'options' => [
                            'NESSUNA', 'MAS', 'WEBSOR'
                        ],
                        'required' => false
                    ],
                    [
                        'key' => 'mas_message_type',
                        'type' => 'string',
                        'required' => false
                    ]
                ]
            ],
            'ios_push' => [
                'label' => 'Configurazione push ios',
                'description' => 'Configurazione parametri per invio push IOS',
                'editable' => true,
                'form_values' => [
                    [
                        'key' => 'environment',
                        'type' => 'select',
                        'options' => [
                            'sandbox', 'production'
                        ],
                        'required' => true
                    ],
                    [
                        'key' => 'retry_time',
                        'type' => 'integer',
                        'required' => true
                    ],
                    [
                        'key' => 'topic',
                        'type' => 'string',
                        'required' => true
                    ],
                    [
                        'key' => 'team_id',
                        'type' => 'string',
                        'required' => true
                    ],
                    [
                        'key' => 'key',
                        'type' => 'string',
                        'required' => true
                    ],
                    [
                        'key' => 'certificate',
                        'type' => 'file',
                        'required' => false
                    ]
                ]
            ],
            'android_push' => [
                'label' => 'Configurazione push android',
                'description' => 'Configurazione parametri per invio push Android',
                'editable' => true,
                'form_values' => [
                    [
                        'key' => 'api_key',
                        'type' => 'string',
                        'required' => true
                    ]
                ]
            ],
            'prevent_rl_app' => [
                'label' => 'Blocca rappr. legali in app',
                'description' => 'Rimuovi funzionalità di conferma attivazione in app',
                'editable' => true,
                'form_values' => [
                    [
                        'key' => 'prevent',
                        'type' => 'select',
                        'options' => [
                            'Si', 'No'
                        ],
                        'required' => true
                    ]
                ]
            ]
        ];
    
    public static function getKeyList() {
        return self::$app_keys;
    }


    public static function getDynamicModel($key) {
        
        $key_values = \common\models\app\AppConfig::findOne(['key'=>$key]);
        if(!$key_values) {
            $values = [];
        } else {
            foreach (json_decode($key_values->value, true) as $__key => $value) {
                $values[$__key] = $value;
            }
        }

        $model_keys = ['editable'];
        
        if(isset(self::$app_keys[$key]['form_values']) && !empty(self::$app_keys[$key]['form_values'])) {
            foreach (self::$app_keys[$key]['form_values'] as $value) {
                $model_keys[] = $value['key'];
            }
        }


        $model = new DynamicModel($model_keys);
        
        if(isset(self::$app_keys[$key]['form_values']) && !empty(self::$app_keys[$key]['form_values'])) {
            foreach (self::$app_keys[$key]['form_values'] as $value) {
                
                switch($value['type']) {
                    case 'select':
                        $model->addRule($value['key'], 'string');
                    break;
                    default:
                        $model->addRule($value['key'], $value['type']);
                    break;
                }

                if($value['required']) $model->addRule($value['key'], 'required');

            }
        }
        
        $model->attributes = $values;
        $model->editable = self::$app_keys[$key]['editable'] ?? false;

        return $model;

    }

}
