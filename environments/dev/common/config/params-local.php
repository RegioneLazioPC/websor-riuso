<?php
return [
    'proxyUrl'=>'',
    'laziocreaserver' => false,
    'adminEmail' => '{email admin}', // email amministratore
    'approvazioneElicotteroEmail' => '{email approvazioni elicottero}', // indirizzo ricevente richieste di approvazione elicotteri
    'feedbackApprovazioneElicotteroMail' => '{email feedback approvazioni elicottero}', // email ricevente feedback delle approvazioni degli elicotteri
    'coauMail' => '{indirizzo email Coau}', // indirizzo email del coau
    'coauMailCC' => '{indirizzo email CC Coau}', // indirizzo email in CC del coau
    'supportEmail' => '{indirizzo email supporto}', // indirizzo email del supporto
    'user.passwordResetTokenExpire' => '{scadenza token}',//es. 86400
    'audittrail.table' => 'tbl_audit_trail',
    'lat'=>'{float: latitudine centro}', // latitudine di base delle mappe
    'lng'=>'{float: latitudine centro}', // longitudine di base delle mappe
    'gmap_ne_latlng' => [
        'lat' => 44.02935859495429, // lat bbox nord est
        'lng' => 11.212921142578123, // lon bbox nord est
    ],
    'gmap_sw_latlng' => [
        'lat' => 42.79237748061047, // lat bbox sud ovest
        'lng' => 14.074859619140625, // lon bbox sud ovest
    ],
    'zoom'=>'{int: livello base zoom}', // livello base zoom
    'APP_NAME' => '{Nome app}', // nome app mobile
    'REGION_NAME' => '{Nome regione}', // aggiunto per messaggio mail
    'MAIL_APP_SENDER' => '{Firma mail per app}', // Firma nel footer delle email relative all'app
    'region_filter_operator'=>'=', 
    'region_filter_id'=>'{int: id regione}', // id regione da tabella loc_regione
    'google_key'=>"{chiave api google (necessaria abilitazione maps e routing)}", // chiave api google
    'geoserver_map_url' => '{path layer tile geoserver}',
    'geoserver_services_url' => '{url wms e wfs geoserver}',
    'geoserver_layers' => [ // array di layer, (vedere readme)
    ],
    'exclude_geoserver_from_map' => true,
    'secret-key' => '{chiave segreta token}', // chiave per generazione token
    'iss' => '{url}', // parametri da utilizzare nel jwt
    'aud' => '{url}', // parametri da utilizzare nel jwt
    'tid' => '{url}', // parametri da utilizzare nel jwt
    'sync_credentials' => [
        'user' => '{user mgo services}', // credenziali per sincronizzazione con MGO
        'pwd' => '{password mgo services}' // credenziali per sincronizzazione con MGO
    ],
    'encryption' => [
        'key' => '{token encryption key}' // chiave cifratura
    ],
    'elicopters' => [
        'host' => '{host dati elicotteri}', // host servizi per dati elicotteri
        'api_key' => '{chiave api elicotteri}' // chiave api servizi elicotteri
    ],
    'mas_host' => '{host servizi mas}',
    'mas_username' => '{user mas}',
    'mas_password' => '{password mas}',
    'mas_token' => '{token mas}',
    'segnalazioni_da_lavorare' => '{app|all}', // app || all
    'sync_everbridge' => false, 
    'everbridge' => [
        'EVERBRIDGE_USER' => '{user everbridge}',
        'EVERBRIDGE_PASSWORD' => '{password everbridge}',
        'EVERBRIDGE_ORGANIZATION_ID' => '{organization id everbridge}',
        'EVERBRIDGE_RECORD_TYPE_ID' => '{record type everbridge}',
        'const_configuration' => [
            'paths' => [
                'email' => [
                    'allerta' => [
                        
                    ],
                    'messaggistica' => [
                        
                    ]
                ],
                'sms' => [
                    'allerta' => [
                        
                    ],
                    'messaggistica' => [
                        
                    ]
                ],
                'fax' => [
                    'allerta' => [
                        
                    ],
                    'messaggistica' => [
                        
                    ]
                ]
            ],
            'recordTypeId' => '{record type everbridge}',
            'organizationId' => '{organization id everbridge}'
        ]
    ],
    'base_mas_callback' => '{url a servizio feedback nel modulo mas qui in websor ES: http://websorapi:80/mas/feedback}',
    'ADDRESSES_FILE_NAME' => '{nome file indirizzi: ES. addresses.csv}'
];