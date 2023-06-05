<?php 
use common\models\UtlTipologia;

require_once '_header.php';


$cols = [
            [
                'attribute' => 'created_at',
                'label' => 'Inizio',
                
            ],
            [
                'attribute' => 'closed_at',
                'label' => 'Fine',
                'value' => function($model) {
                    return ($model['stato'] == 'Rifiutato' && empty($model['closed_at'])) ? $model['created_at'] : $model['closed_at'];
                }
            ],
            [
                'label' => 'Durata',
                'attribute' => 'durata'
            ],
            [
                'attribute' => 'mese',
                'label' => 'Mese',
                
            ],
            [
                'attribute' => 'anno',
                'label' => 'Anno',
                
            ],
            [
                'attribute' => 'num_protocollo',
                'label' => 'N.Protocollo'
            ],
            [
                'attribute' => 'tipologia',
                'label' => 'Tipologia',
                
            ],
            [
                'attribute' => 'sottotipologia',
                'label' => 'Sottotipologia',
                
            ],
            [
                'label' => 'Gestore',
                'attribute' => 'id_gestore',
                'value' => function($data){
                    return @$data->gestore;
                }
            ],
            [
                'label' => 'COC',
                'attribute' => 'coc',
            ],            
            [
                'label' => 'Indirizzo/luogo',
                'attribute' => 'indirizzo',
            ],      
            [
                'label' => 'Comune',
                'attribute' => 'comune',
            ],       
            [
                'label' => 'Provincia',
                'attribute' => 'provincia_sigla',
                
            ], 
            [
                'label' => 'Mezzo',
                'attribute' => 'tipo_automezzo',
            ],
            [
                'attribute' => 'targa',
                'label' => 'Targa'
            ],
            [
                'label' => 'Attrezzatura',
                'attribute' => 'tipo_attrezzatura'
                
            ],
            [
                'label' => 'Tipologia mezzo/attrezzatura',
                'attribute' => 'aggregatore',
                'value' => function($data){
                    $ret = [];
                    if(!empty($data->aggregatore_automezzi)) return $data->aggregatore_automezzi;
                    if(!empty($data->aggregatore_attrezzature)) return $data->aggregatore_attrezzature;

                    return "";
                }
            ], 
            [
                'label' => 'Identificativo organizzazione',
                'attribute' => 'num_elenco_territoriale'
            ],  
            [
                'label' => 'Organizzazione',
                'attribute' => 'organizzazione'
            ],  
            [
                'label' => 'Sede',
                'attribute' => 'indirizzo_sede'
            ],   
            [
                'label' => 'Tipo sede',
                'attribute' => 'tipo_sede'
            ], 
                            
            [
                'label' => 'Stato',
                'attribute' => 'stato'
            ],        
            [
                'label' => 'Note',
                'attribute' => 'note',
            ],
            [
                'attribute' => 'lat',
                'label' => 'Lat WGS84',
            ],
            [
                'attribute' => 'lon',
                'label' => 'Lon WGS84',
            ],            
        ];

require_once '_data.php';
?>