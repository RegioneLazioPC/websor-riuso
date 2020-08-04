<?php
use yii\helpers\Html;


?>

<aside class="main-sidebar" style="padding-top: 83px;">

    <section class="sidebar">


        <?php
        $menuItems = [['label' => 'Sistema Gestione Emergenze', 'options' => ['class' => 'header']]];

        $menuItems = [
            [
                'label' => 'Cartografia',
                'icon' => 'map-marker',
                'options' => [
                    'class' => 'active carto-link',
                ],
                'url' => ['/sistema-cartografico?can_add=1&visible_organizations=1&visible_reports=1']
            ]
        ];
        
        if (Yii::$app->user->identity->multipleCan([
            'listEventi',
            'listEventiChiusi',
            'listEventiArchiviati']
        )) {
            $menuItems[] = [
                    'label' => 'Eventi',
                    'icon' => 'warning',
                    'options' => ['class' => 'active'],
                    'url' => '#',
                    'items' => [
                        ['label' => 'Crea evento', 'icon' => 'plus', 'url' => ['/evento/create'], 'visible' => Yii::$app->user->can('createEvento')],
                        ['label' => 'Lista eventi', 'icon' => 'list', 'url' => ['/evento/index']],
                        ['label' => 'Lista eventi chiusi', 'icon' => 'ban', 
                            'url' => ['/evento/closed'],
                            'visible' => Yii::$app->user->can('listEventiChiusi')
                        ],
                        ['label' => 'Lista eventi archiviati', 'icon' => 'archive', 
                            'url' => ['/evento/archived'],
                            'visible' => Yii::$app->user->can('listEventiArchiviati')
                        ],
                        ['label' => 'Mappa eventi', 'icon' => 'map', 'url' => ['/evento/map']]
                    ],
                ];
        }

        if (Yii::$app->user->can('listSegnalazioni')) {

            $menuItems[] = [
                'label' => 'Segnalazioni',
                'icon' => 'bell',
                'url' => '#',
                'options' => ['class' => 'active'],
                'items' => [
                    ['label' => 'Crea segnalazione', 'icon' => 'plus', 'url' => ['/segnalazione/create'], 'visible' => Yii::$app->user->can('createSegnalazione')],
                    ['label' => 'Lista segnalazioni', 'icon' => 'list', 'url' => ['/segnalazione/index']],
                    ['label' => 'Lista segnalazioni lavorate', 'icon' => 'ban', 'url' => ['/segnalazione/lavorate']],
                    ['label' => 'Mappa segnalazioni', 'icon' => 'map', 'url' => ['/segnalazione/map']]
                ],
            ];
        }

        if(Yii::$app->user->identity->multipleCan(['viewLogSegnalazioni', 'viewLogEventi', 'viewLogIngaggi',
            'viewLogRichiesteElicottero',
            'viewLogRichiesteDos',
            'viewLogRichiesteCanadair']
        )) $menuItems[] = [
            'label' => 'Monitoraggio attività',
            'icon' => 'cog',
            'url' => '#',
            'options' => ['class' => 'active'],
            'items' => [
                ['label' => 'Segnalazioni', 'icon' => 'bell', 'url' => ['/logs/segnalazioni'], 'visible' => Yii::$app->user->can('viewLogSegnalazioni')],
                ['label' => 'Eventi', 'icon' => 'warning', 'url' => ['/logs/eventi'], 'visible' => Yii::$app->user->can('viewLogEventi')],
                ['label' => 'Attivazioni', 'icon' => 'file', 'url' => ['/logs/attivazioni'], 'visible' => Yii::$app->user->can('viewLogIngaggi')],
                ['label' => 'Richieste Canadair', 'icon' => 'plane', 'url' => ['/logs/richieste-canadair'], 'visible' => Yii::$app->user->can('viewLogRichiesteCanadair')],
                ['label' => 'Richieste Dos', 'icon' => 'fire-extinguisher', 'url' => ['/logs/richieste-dos'], 'visible' => Yii::$app->user->can('viewLogRichiesteDos')],
                ['label' => 'Richieste Elicottero', 'icon' => 'info-circle', 'url' => ['/logs/richieste-elicottero'], 'visible' => Yii::$app->user->can('viewLogRichiesteElicottero')]
            ]
        ];


        if(Yii::$app->user->identity->multipleCan(['createOperatore', 'viewOperatore'])) $menuItems[] = [
                'label' => 'Personale Web SOR/SOP',
                'icon' => 'user',
                'url' => '#',
                'options' => ['class' => 'active'],
                'items' => [
                    ['label' => 'Aggiungi operatore', 'icon' => 'user-plus', 'url' => ['/operatorepc/create'], 'visible' => Yii::$app->user->can('createOperatore')],
                    ['label' => 'Lista operatori', 'icon' => 'list', 'url' => ['/operatorepc'], 'visible' => Yii::$app->user->can('viewOperatore')]
                ]
            ];

        if(Yii::$app->user->identity->multipleCan(['viewOrganizzazione', 'viewTipoOrganizzazione', 'viewSpecializzazione', 'viewAutomezzo', 'viewAttrezzatura', 'viewVolontario'])) $menuItems[] = [
                'label' => 'Associazioni Volontariato',
                'icon' => 'ambulance',
                'url' => '#',
                'options' => ['class' => 'active'],
                'items' => [
                    ['label' => 'Lista organizzazioni', 'icon' => 'list', 'url' => ['/organizzazione-volontariato'], 'visible' => Yii::$app->user->can('viewOrganizzazione')],
                    ['label' => 'Tipi di organizzazioni', 'icon' => 'cogs', 'url' => ['/tipo-organizzazione'], 'visible' => Yii::$app->user->can('viewTipoOrganizzazione')],
                    ['label' => 'Specializzazioni', 'icon' => 'thumbs-up', 'url' => ['/specializzazioni'], 'visible' => Yii::$app->user->can('viewSpecializzazione')],
                    ['label' => 'Lista mezzi', 'icon' => 'car', 'url' => ['/automezzo'], 'visible' => Yii::$app->user->can('viewAutomezzo')],
                    ['label' => 'Lista attrezzature', 'icon' => 'cog', 'url' => ['/attrezzatura'], 'visible' => Yii::$app->user->can('viewAttrezzatura')],
                    ['label' => 'Volontari', 'icon' => 'user', 'url' => ['/volontari/index'], 'visible' => Yii::$app->user->can('viewVolontario')],
                ]
            ];  

        if(Yii::$app->user->identity->multipleCan(['Admin'])) $menuItems[] = [
                'label' => 'Altre organizzazioni',
                'icon' => 'building',
                'url' => '#',
                'options' => ['class' => 'active'],
                'items' => [
                    ['label' => 'Lista enti pubblici', 'url' => ['/ente'], 'visible' => Yii::$app->user->can('Admin')],
                    ['label' => 'Tipi di enti', 'url' => ['/ente/tipo-ente'], 'visible' => Yii::$app->user->can('Admin')],
                    ['label' => 'Strutture', 'url' => ['/struttura'], 'visible' => Yii::$app->user->can('Admin')],
                    ['label' => 'Tipi di strutture', 'url' => ['/struttura/tipo-struttura'], 'visible' => Yii::$app->user->can('Admin')]
                ]
            ];   

        if (Yii::$app->user->can('exportData')) {
            $menuItems[] = [
                    'label' => 'Reportistica',
                    'icon' => 'file',
                    'options' => ['class' => 'active'],
                    'url' => '#',
                    'items' => [
                        ['label' => 'Attivazioni', 'url' => ['/report/attivazioni'], 'visible' => true],
                        ['label' => 'Attivazioni volontari', 'url' => ['/report/attivazioni-volontari'], 'visible' => true],
                        ['label' => 'Eventi', 'url' => ['/report/eventi'], 'visible' => true],
                        ['label' => 'Interventi', 'url' => ['/report/interventi'], 'visible' => true],
                        ['label' => 'Interventi/odv', 'url' => ['/report/interventi-odv'], 'visible' => true],
                        ['label' => 'Interventi/tipologia', 'url' => ['/report/interventi-tipologia'], 'visible' => true],
                        ['label' => 'Interventi/rifiutati', 'url' => ['/report/interventi-rifiutati'], 'visible' => true],
                        ['label' => 'Mezzi', 'url' => ['/report/mezzi'], 'visible' => true],
                        ['label' => 'Elicotteri', 'url' => ['/report/elicotteri-per-intervento'], 'visible' => true],
                        ['label' => 'Incendi COAU', 'url' => ['/report/coau'], 'visible' => true],
                        ['label' => 'Dettaglio voli', 'url' => ['/report/dettaglio-voli'], 'visible' => true]
                    ],
                ];
        }
                 

        if(Yii::$app->user->identity->multipleCan(['viewTipoAutomezzo', 'viewTipoAttrezzatura', 'viewAggregatore', 'viewCategoria', 'viewTipoEvento', 'viewTipoRisorsaMeta'])) $menuItems[] = [
                'label' => 'Alberatura tipologie',
                'icon' => 'align-left',
                'url' => '#',
                'options' => ['class' => 'active'],
                'items' => [
                    ['label' => 'Tipi di mezzi', 'icon' => 'ambulance', 'url' => ['/tipo-automezzo'], 'visible' => Yii::$app->user->can('viewTipoAutomezzo')],
                    ['label' => 'Tipi di attrezzature', 'icon' => 'cogs', 'url' => ['/tipo-attrezzatura'], 'visible' => Yii::$app->user->can('viewTipoAttrezzatura')],
                    ['label' => 'Meta risorse', 'icon' => 'cogs', 'url' => ['/meta-risorse'], 'visible' => Yii::$app->user->can('viewTipoRisorsaMeta')],
                    ['label' => 'Tipi mezzi/attrezzature', 'icon' => 'car', 'url' => ['/aggregatori'], 'visible' => Yii::$app->user->can('viewAggregatore')],
                    ['label' => 'Categorie', 'icon' => 'align-left', 'url' => ['/categoria-automezzo-attrezzatura'], 'visible' => Yii::$app->user->can('viewCategoria')],
                    ['label' => 'Tipi evento', 'icon' => 'fire', 'url' => ['/tipi-evento'], 'visible' => Yii::$app->user->can('viewTipoEvento')]
                ]
            ];

        if(Yii::$app->user->identity->multipleCan(['listAppUser', 'createAppUser'])) $menuItems[] = [
                'label' => 'App',
                'icon' => 'user',
                'url' => '#',
                'options' => ['class' => 'active'],
                'items' => [
                    ['label' => 'Inserisci', 'icon' => 'plus', 'url' => ['/utenti/create'], 'visible' => Yii::$app->user->can('createAppUser')],
                    ['label' => 'Utenti', 'icon' => 'list', 'url' => ['/utenti'], 'visible' => Yii::$app->user->can('listAppUser')],
                    
                ]
            ];

        if(Yii::$app->user->identity->multipleCan([
            'listAllerte',
            'createAllerta',
            'Admin'
        ])) $menuItems[] = [
                'label' => 'Allerte',
                'icon' => 'bell',
                'url' => '#',
                'options' => ['class' => 'active'],
                'items' => [
                    ['label' => 'Crea allerta meteo', 'icon' => 'plus', 'url' => ['/allerta-meteo/send-allerta'], 'visible' => Yii::$app->user->can('createAllerta')],
                    ['label' => 'Allerte meteo', 'icon' => 'cloud', 'url' => ['/allerta-meteo'], 'visible' => Yii::$app->user->can('listAllerte')],
                    ['label' => 'Zone di allerta/comune', 'icon' => 'info', 'url' => ['/allerta-meteo/zone-allerta'], 'visible' => Yii::$app->user->can('Admin')]
                ]
            ];

        if(Yii::$app->user->identity->multipleCan([
            'listMasMessage',
            'createMasMessage'
        ])) $menuItems[] = [
                'label' => 'Messaggi',
                'icon' => 'comments',
                'url' => '#',
                'options' => ['class' => 'active'],
                'items' => [
                    ['label' => 'Crea messaggio', 'icon' => 'plus', 'url' => ['/mas/create'], 'visible' => Yii::$app->user->can('createMasMessage')],
                    ['label' => 'Lista messaggi', 'icon' => 'list', 'url' => ['/mas/index'], 'visible' => Yii::$app->user->can('listMasMessage')]
                ]
            ];

        if(Yii::$app->user->identity->multipleCan([
            'listMasTemplate',
            'createMasTemplate'
        ])) $menuItems[] = [
                'label' => 'Template messaggi',
                'icon' => 'square',
                'url' => '#',
                'options' => ['class' => 'active'],
                'items' => [
                    ['label' => 'Crea nuovo', 'icon' => 'plus', 'url' => ['/mas/create-template'], 'visible' => Yii::$app->user->can('createMasTemplate')],
                    ['label' => 'Lista', 'icon' => 'list', 'url' => ['/mas/index-template'], 'visible' => Yii::$app->user->can('listMasTemplate')],
                    
                ]
            ];

        if(Yii::$app->user->identity->multipleCan([
            'listMasRubrica',
            'listRubricaGroup'
        ])) $menuItems[] = [
                'label' => 'Rubrica',
                'icon' => 'users',
                'url' => '#',
                'options' => ['class' => 'active'],
                'items' => [
                    ['label' => 'Contatti', 'icon' => 'user', 'url' => ['/mas/index-rubrica'], 'visible' => Yii::$app->user->can('listMasRubrica')],
                    ['label' => 'Gruppi', 'icon' => 'th', 'url' => ['/rubrica-group/index'], 'visible' => Yii::$app->user->can('listRubricaGroup')],
                    
                ]
            ];

        

        if(Yii::$app->user->identity->multipleCan(['manageRbac'])) $menuItems[] = [
                'label' => 'Admin',
                'icon' => 'cogs',
                'url' => '#',
                'options' => ['class' => 'active'],
                'items' => [
                    ['label' => 'Users', 'icon' => 'users', 'url' => ['/admin/user']],
                    ['label' => 'Assegnazioni', 'icon' => 'link', 'url' => ['/admin/assignment']],
                    ['label' => 'Ruoli', 'icon' => 'legal', 'url' => ['/admin/role']],
                    ['label' => 'Permessi', 'icon' => 'sliders', 'url' => ['/admin/permission']],
                ],
            ];
        
        ?>

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => $menuItems
            ]
        ) ?>

    </section>

</aside>
