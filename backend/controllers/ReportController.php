<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\UtlIngaggioSearch;
use yii\data\ArrayDataProvider;

use common\models\reportistica\FilterModel;
use common\models\reportistica\ViewReportAttivazioni;
use common\models\reportistica\ViewReportAttivazioniVolontari;
/**
 * Per prendere le date
 * fare il cast in ::DATE dei timestamp altrimenti le prende solo minori
 */
/**
 * RichiestaDosController implements the CRUD actions for RichiestaDos model.
 */
class ReportController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if(Yii::$app->user){
                        Yii::error(json_encode( Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId()) ));
                        Yii::$app->user->logout();                        
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['attivazioni', 'attivazioni-volontari' ,'eventi','interventi','interventi-odv','interventi-tipologia','interventi-rifiutati','mezzi','elicotteri-per-intervento','coau','dettaglio-voli'],
                        'permissions' => ['exportData']
                    ]
                ],

            ],
        ];
    }



    private function buildFilters( $params_map, $model = null )
    {

        $filter_model = ($model) ? $model : new FilterModel;
        
        $filter_model->load( Yii::$app->request->get() );
        $filter_string = '';
        $filter_params = [];
        
        if( $filter_model->validate() ) {
            
            foreach ( $filter_model->getAttributes() as $param => $value) {                   
                
                if( isset($params_map[$param]) && !empty($filter_model->$param) ) {
                    $filter_string .= ' AND ' . $params_map[$param];
                    $filter_params[':' . $param] = $value;
                }
                
            }

        } 

        
        return [
            'filter_model' => $filter_model,
            'filter_string' => $filter_string,
            'filter_params' => $filter_params
        ];

    }


    /**
     * Mostra report
     * @return [type] [description]
     */
    public function actionAttivazioni()
    {
        $ingaggiSearchModel = new ViewReportAttivazioni();
        $ingaggiDataProvider = $ingaggiSearchModel->search(Yii::$app->request->queryParams);

        return $this->render('attivazioni', [
            'ingaggiSearchModel' => $ingaggiSearchModel,
            'ingaggiDataProvider' => $ingaggiDataProvider
        ]);
    }

    /**
     * Attivazioni volontari
     * @return [type] [description]
     */
    public function actionAttivazioniVolontari()
    {
        $ingaggiSearchModel = new ViewReportAttivazioniVolontari();
        $ingaggiDataProvider = $ingaggiSearchModel->search(Yii::$app->request->queryParams);

        return $this->render('attivazioni-volontari', [
            'ingaggiSearchModel' => $ingaggiSearchModel,
            'ingaggiDataProvider' => $ingaggiDataProvider
        ]);
    }


    public function actionEventi()
    {
        $connection = Yii::$app->getDb();
        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from e.dataora_evento) = :year',
            'month' => 'extract( \'month\' from e.dataora_evento) = :month',
            'date_from' => '(e.dataora_evento >= :date_from OR e.closed_at >= :date_from)',
            'date_to' => '(e.dataora_evento <= :date_to OR e.closed_at <= :date_from)',
            'pr' => 'c.provincia_sigla = :pr',
            'comune' => 'c.id = :comune',
            'tipologia' => 'pt.id = :tipologia',
            'sottotipologia' => 't.id = :sottotipologia',
        ]);

        // filters:
        /*
            AND extract( 'year' from e.dataora_evento) = :year 2019 
            AND extract( 'month' from e.dataora_evento) = :month 5
            AND e.dataora_evento >= '2019-05-01' :date_from
            AND e.dataora_evento <= '2019-05-10' :date_to
            AND t.id = 91 :subtype
            AND pt.id = 90 :type
            AND c.provincia_sigla = 'LT' :pr
            AND c.id = 4889 :cid
        */


        $q = "WITH t as (SELECT 
                c.provincia_sigla,
                c.comune,  
                t.id as sottotipologia,
                pt.id as tipologia,
                count(e.id) as totale
                FROM loc_comune c
                LEFT JOIN utl_evento e ON e.idcomune = c.id AND e.idparent is null
                LEFT JOIN utl_tipologia t ON t.id = e.sottotipologia_evento
                LEFT JOIN utl_tipologia pt ON pt.id = t.idparent
                WHERE c.id_regione = 12 AND e.idparent is null " . $filters['filter_string'] . "
                GROUP BY c.id, pt.id, t.id
                ORDER BY c.provincia_sigla, c.comune ASC)
            SELECT provincia_sigla as provincia, ''::TEXT as comune, sottotipologia, tipologia, sum(totale) FROM t 
            GROUP BY provincia, sottotipologia, tipologia
            UNION
            SELECT provincia_sigla as provincia, comune, sottotipologia, tipologia, sum(totale) FROM t 
            GROUP BY provincia_sigla, comune, sottotipologia, tipologia
            ORDER BY provincia ASC, comune ASC;";

        $command = $connection->createCommand($q);

        if ( !empty($filters['filter_params']) ) {
            $command->bindValues($filters['filter_params']);
        }
        
        $result = $command->queryAll();

        $data = [];
        $tipologie = [];

        foreach ( $result as $q_row ) {

            if ( !isset( $data[$q_row['provincia']] ) ) $data[ $q_row['provincia'] ] = [
                'totale' => 0,
                'comuni' => [],
                'tipologie' => []
            ];

            if ( empty( $q_row['comune'] ) ) {
                // sto mettendo la provincia
                if(!isset( $data[ $q_row['provincia']]['tipologie'][$q_row['tipologia']] )) {
                    $data[ $q_row['provincia'] ]['tipologie'][$q_row['tipologia']] = [
                        'sottotipologie' => [],
                        'totale' => 0
                    ];
                }

                if(!isset( $data[ $q_row['provincia']]['tipologie'][$q_row['tipologia']]['sottotipologie'][$q_row['sottotipologia']]  )) {
                    $data[ $q_row['provincia']]['tipologie'][$q_row['tipologia']]['sottotipologie'][$q_row['sottotipologia']] = $q_row['sum'];

                }

                $data[ $q_row['provincia']]['totale'] += $q_row['sum'];
                $data[ $q_row['provincia']]['tipologie'][$q_row['tipologia']]['totale'] += $q_row['sum'];

            } else {
                // sto mettendo il comune
                if(!isset( $data[ $q_row['provincia']]['comuni'][$q_row['comune']] )) {
                    $data[ $q_row['provincia']]['comuni'][$q_row['comune']] = [
                        'tipologie' => [],
                        'totale' => 0
                    ];
                }

                if(!isset( $data[ $q_row['provincia']]['comuni'][$q_row['comune']]['tipologie'][$q_row['tipologia']] )) {
                    $data[ $q_row['provincia']]['comuni'][$q_row['comune']]['tipologie'][$q_row['tipologia']] = [
                        'sottotipologie' => [],
                        'totale' => 0
                    ];
                }

                if(!isset( $data[ $q_row['provincia']]['comuni'][$q_row['comune']]['tipologie'][$q_row['tipologia']]['sottotipologie'][$q_row['sottotipologia']]  )) {
                    $data[ $q_row['provincia']]['comuni'][$q_row['comune']]['tipologie'][$q_row['tipologia']]['sottotipologie'][$q_row['sottotipologia']] = $q_row['sum'];
                }

                $data[ $q_row['provincia']]['comuni'][$q_row['comune']]['tipologie'][$q_row['tipologia']]['totale'] += $q_row['sum'];

                $data[ $q_row['provincia']]['comuni'][$q_row['comune']]['totale'] += $q_row['sum'];

            }
        }

        $provider = [];

        foreach ($data as $provincia => $value) {
            $_provincia = [
                'provincia' => $provincia,
                'comune' => '',
                'totale' => $value['totale']
            ];

            foreach ($value['tipologie'] as $tipologia => $_value) {
                $_provincia['totale_'.$tipologia] = $_value['totale'];
                foreach ($_value['sottotipologie'] as $sottotipologia => $__value) {
                    $_provincia['totale_'.$sottotipologia] = $__value;
                }
            }

            $provider[] = $_provincia;

            foreach ($value['comuni'] as $comune => $_value) {
                $comune = [
                    'provincia' => $provincia,
                    'comune' => $comune,
                    'totale' => $_value['totale']
                ];

                foreach ($_value['tipologie'] as $tipologia => $__value) {
                    $comune['totale_'.$tipologia] = $__value['totale'];
                    foreach ($__value['sottotipologie'] as $sottotipologia => $___value) {
                        $comune['totale_'.$sottotipologia] = $___value;
                    }
                }

                $provider[] = $comune;
            }
        }

        
        $datProvider = new ArrayDataProvider([
            'allModels' => $provider,
            'pagination' => false            
        ]);

        return $this->render('eventi', [
            'dataProvider' => $datProvider,
            'tipologie' => $tipologie,
            'filter_model' => $filters['filter_model']
        ]);
    }



    public function actionInterventi()
    {

        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from e.dataora_evento) = :year',
            'month' => 'extract( \'month\' from e.dataora_evento) = :month',
            'date_from' => 'e.dataora_evento >= :date_from',
            'date_to' => 'e.dataora_evento <= :date_to',
            'pr' => 'c.provincia_sigla = :pr',
            'comune' => 'c.id = :comune',
            'stato_ingaggio' => 'i.stato = :stato_ingaggio',
            'tipologia' => 'pt.id = :tipologia',
            'sottotipologia' => 't.id = :sottotipologia',
        ]);

        $connection = Yii::$app->getDb();

        // filters:
        /*
            AND extract( 'year' from e.dataora_evento) = :year 2019 
            AND extract( 'month' from e.dataora_evento) = :month 5
            AND e.dataora_evento >= '2019-05-01' :date_from
            AND e.dataora_evento <= '2019-05-10' :date_to
            AND c.provincia_sigla = 'LT' :pr
            AND c.id = 4889 :cid
        */
       // stato non ha senso perchè farebbe un filtro su colonna, non serve
       
        $q = "WITH t as (
                SELECT 
                    c.provincia_sigla,
                    c.comune,
                    count( i.id ) filter (WHERE e.stato = 'Chiuso') as totale_comune,
                    count( i.id ) filter (where i.stato = 3) as chiuso,
                    count( i.id ) filter (where i.stato = 0) as in_attesa,
                    count( i.id ) filter (where i.stato = 1) as confermato,
                    count( i.id ) filter (where i.stato = 2) as rifiutato,
                    count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 1) as fuori_orario,
                    count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 2) as non_risponde,
                    count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 3) as mezzo_non_disponibile,
                    count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 4) as squadra_non_disponibile,
                    count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 6) as impegnata,
                    count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 5) as altro
                    FROM loc_comune c 
                    LEFT JOIN utl_evento e ON e.idcomune = c.id 
                    LEFT JOIN utl_tipologia t ON t.id = e.sottotipologia_evento
                    LEFT JOIN utl_tipologia pt ON pt.id = t.idparent
                    LEFT JOIN utl_ingaggio i ON i.idevento = e.id
                    WHERE c.id_regione = 12 " . $filters['filter_string'] . "
                    GROUP BY c.provincia_sigla, c.id )
            SELECT  provincia_sigla, ''::text as comune, SUM( t.totale_comune ) as totale_provincia, 0 as totale_comune,
                SUM( chiuso ) as chiuso,
                SUM( rifiutato ) as rifiutato,
                SUM( in_attesa ) as in_attesa,
                SUM( confermato ) as confermato,
                SUM( fuori_orario ) as fuori_orario,
                SUM( non_risponde ) as non_risponde,
                SUM( mezzo_non_disponibile ) as mezzo_non_disponibile,
                SUM( squadra_non_disponibile ) as squadra_non_disponibile,
                SUM( impegnata ) as impegnata,
                SUM( altro ) as altro
              FROM  t
              GROUP BY provincia_sigla
              UNION 
              SELECT  provincia_sigla, comune, 0 as totale_provincia, SUM( t.totale_comune ) as totale_comune,
                SUM( chiuso ) as chiuso,
                SUM( rifiutato ) as rifiutato,
                SUM( in_attesa ) as in_attesa,
                SUM( confermato ) as confermato,
                SUM( fuori_orario ) as fuori_orario,
                SUM( non_risponde ) as non_risponde,
                SUM( mezzo_non_disponibile ) as mezzo_non_disponibile,
                SUM( squadra_non_disponibile ) as squadra_non_disponibile,
                SUM( impegnata ) as impegnata,
                SUM( altro ) as altro  
             FROM t
              GROUP BY provincia_sigla, comune
              ORDER BY provincia_sigla, comune ASC";

        $command = $connection->createCommand($q);

        if ( !empty($filters['filter_params']) ) {
            $command->bindValues($filters['filter_params']);
        }
        
        $data = $command->queryAll();

        $datProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => false            
        ]);

        return $this->render('interventi', [
            'dataProvider' => $datProvider,
            'filter_model' => $filters['filter_model']
        ]);
    }

    public function actionInterventiOdv()
    {

        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from e.dataora_evento) = :year',
            'month' => 'extract( \'month\' from e.dataora_evento) = :month',
            'date_from' => 'e.dataora_evento >= :date_from',
            'date_to' => 'e.dataora_evento <= :date_to',
            'pr' => 'c.provincia_sigla = :pr',
            'comune' => 'c.id = :comune',
            'stato_ingaggio' => 'i.stato = :stato_ingaggio',
            'odv' => 'v.ref_id = :odv',
            'tipologia' => 'pt.id = :tipologia',
            'sottotipologia' => 't.id = :sottotipologia',
        ]);

        $connection = Yii::$app->getDb();

        $q = "
        SELECT 
        v.ref_id as num_elenco_territoriale,
        v.denominazione,
        v.id as id_organizzazione,
        count( i.id ) filter (WHERE e.stato = 'Chiuso') as totale,
        count( i.id ) filter (where i.stato = 3) as chiuso,
        count( i.id ) filter (where i.stato = 0) as in_attesa,
        count( i.id ) filter (where i.stato = 1) as confermato,
        count( i.id ) filter (where i.stato = 2) as rifiutato,
        count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 1) as fuori_orario,
        count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 2) as non_risponde,
        count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 3) as mezzo_non_disponibile,
        count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 4) as squadra_non_disponibile,
        count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 6) as impegnata,
        count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 5) as altro
        FROM utl_ingaggio i 
        LEFT JOIN utl_evento e ON e.id = i.idevento
        LEFT JOIN loc_comune c ON c.id = e.idcomune
        LEFT JOIN utl_tipologia t ON t.id = e.sottotipologia_evento
        LEFT JOIN utl_tipologia pt ON pt.id = t.idparent
        LEFT JOIN vol_organizzazione v ON v.id = i.idorganizzazione
        WHERE v.id is not null " . $filters['filter_string'] . "
        GROUP BY v.id
        ORDER BY v.ref_id ASC";

        $command = $connection->createCommand($q);
        
        if ( !empty($filters['filter_params']) ) {
            $command->bindValues($filters['filter_params']);
        }
        
        $data = $command->queryAll();

        $datProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => false            
        ]);

        return $this->render('interventi-odv', [
            'dataProvider' => $datProvider,
            'filter_model' => $filters['filter_model']
        ]);
    }

    public function actionInterventiRifiutati()
    {

        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from i.created_at) = :year',
            'month' => 'extract( \'month\' from i.created_at) = :month',
            'date_from' => 'i.created_at >= :date_from',
            'date_to' => 'i.created_at <= :date_to',
            'pr' => 'c.provincia_sigla = :pr',
            'comune' => 'c.id = :comune',
            'odv' => 'v.ref_id = :odv'
        ]);

        $connection = Yii::$app->getDb();

        $q = "
        SELECT 
        v.ref_id as num_elenco_territoriale,
        v.denominazione,
        v.id as id_organizzazione,
        i.created_at,
        i.closed_at,
        a.targa,
        t.tipologia,
        st.tipologia as sottotipologia,
        e.num_protocollo,
        i.motivazione_rifiuto,
        i.note
        FROM utl_ingaggio i 
        LEFT JOIN utl_evento e ON e.id = i.idevento
        LEFT JOIN loc_comune c ON c.id = e.idcomune
        LEFT JOIN utl_evento f ON f.idparent = e.id /* f = fronte */
        LEFT JOIN utl_tipologia t ON t.id = e.tipologia_evento
        LEFT JOIN utl_tipologia st ON st.id = e.sottotipologia_evento
        LEFT JOIN vol_organizzazione v ON v.id = i.idorganizzazione
        LEFT JOIN utl_automezzo a ON a.id = i.idautomezzo
        WHERE v.id is not null AND i.stato = 2 " . $filters['filter_string'] . "
        ORDER BY v.ref_id ASC";

        $command = $connection->createCommand($q);
        
        if ( !empty($filters['filter_params']) ) {
            $command->bindValues($filters['filter_params']);
        }
        
        $data = $command->queryAll();

        $datProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => false            
        ]);

        return $this->render('interventi-rifiutati', [
            'dataProvider' => $datProvider,
            'filter_model' => $filters['filter_model']
        ]);
    }

    public function actionInterventiTipologia()
    {

        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from e.dataora_evento) = :year',
            'month' => 'extract( \'month\' from e.dataora_evento) = :month',
            'date_from' => 'e.dataora_evento >= :date_from',
            'date_to' => 'e.dataora_evento <= :date_to',
            'pr' => 'c.provincia_sigla = :pr',
            'comune' => 'c.id = :comune',
            'stato_ingaggio' => 'i.stato = :stato_ingaggio',
            'tipologia' => 't.id = :tipologia',
            'sottotipologia' => 'st.id = :sottotipologia',
        ]);

        $connection = Yii::$app->getDb();

        $q = "WITH all_record as (SELECT 
            t.tipologia as tipologia,
            st.tipologia as sottotipologia,
            count( i.id ) filter (WHERE e.stato = 'Chiuso') as totale,
            count( i.id ) filter (where i.stato = 3) as chiuso,
            count( i.id ) filter (where i.stato = 0) as in_attesa,
            count( i.id ) filter (where i.stato = 1) as confermato,
            count( i.id ) filter (where i.stato = 2) as rifiutato,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 1) as fuori_orario,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 2) as non_risponde,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 3) as mezzo_non_disponibile,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 4) as squadra_non_disponibile,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 6) as impegnata,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 5) as altro
            FROM utl_ingaggio i 
            LEFT JOIN utl_evento e ON e.id = i.idevento
            LEFT JOIN loc_comune c ON c.id = e.idcomune
            LEFT JOIN utl_tipologia t ON t.id = e.tipologia_evento
            LEFT JOIN utl_tipologia st ON st.id = e.sottotipologia_evento
            LEFT JOIN vol_organizzazione v ON v.id = i.idorganizzazione
            WHERE t.id is not null " . $filters['filter_string'] . " 
            GROUP BY t.id, st.id
            UNION ALL 
            SELECT 
            t.tipologia as tipologia,
            ' - ' as sottotipologia,
            count( i.id ) filter (WHERE e.stato = 'Chiuso') as totale,
            count( i.id ) filter (where i.stato = 3) as chiuso,
            count( i.id ) filter (where i.stato = 0) as in_attesa,
            count( i.id ) filter (where i.stato = 1) as confermato,
            count( i.id ) filter (where i.stato = 2) as rifiutato,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 1) as fuori_orario,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 2) as non_risponde,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 3) as mezzo_non_disponibile,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 4) as squadra_non_disponibile,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 6) as impegnata,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 5) as altro
            FROM utl_ingaggio i 
            LEFT JOIN utl_evento e ON e.id = i.idevento
            LEFT JOIN loc_comune c ON c.id = e.idcomune
            LEFT JOIN utl_tipologia t ON t.id = e.tipologia_evento
            LEFT JOIN utl_tipologia st ON st.id = e.sottotipologia_evento
            LEFT JOIN vol_organizzazione v ON v.id = i.idorganizzazione
            WHERE t.id is not null " . $filters['filter_string'] . "
            GROUP BY t.id) 
            SELECT * FROM all_record ORDER BY tipologia ASC, sottotipologia ASC";

        $command = $connection->createCommand($q);

        if ( !empty($filters['filter_params']) ) {
            $command->bindValues($filters['filter_params']);
        }
        
        $data = $command->queryAll();

        $datProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => false            
        ]);

        return $this->render('interventi-tipologia', [
            'dataProvider' => $datProvider,
            'filter_model' => $filters['filter_model']
        ]);
    }

    /**
     * La query non basta, vanno ripresi in php e elaborati, il problema è che vanno messi in colonna
     * @return [type] [description]
     */
    public function actionMezzi()
    {

        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from i.created_at) = :year',
            'month' => 'extract( \'month\' from i.created_at) = :month',
            'day' => 'extract( \'day\' from i.created_at) = :day',
            'date_from' => 'i.created_at >= :date_from',
            'date_to' => 'i.created_at <= :date_to',
            'pr' => 'c.provincia_sigla = :pr',
            'comune' => 'c.id = :comune',
            'tipo_mezzo' => 'tipo.id = :tipo_mezzo',
            'tipologia' => 'pt.id = :tipologia',
            'sottotipologia' => 't.id = :sottotipologia',
        ]);

        $connection = Yii::$app->getDb();

        // filters
        /*
        AND extract( 'year' from i.created_at) = 2019 :year
        AND extract( 'month' from i.created_at) = 6 :month
        AND i.created_at >= '2019-06-01' :date_from
        AND i.created_at <= '2019-06-30' :date_to
        AND c.provincia_sigla = 'LT' :pr
        AND c.id = 4889 :cid
         */
        $q = "WITH t as (SELECT 
                        extract( 'year' from i.created_at) as anno,
                        extract( 'month' from i.created_at) as mese,
                        extract( 'day' from i.created_at) as giorno,
                        STRING_AGG( DISTINCT( agg.descrizione), ', ' ) as tipologia,
                        count(DISTINCT(i.id)) as total
                        FROM utl_ingaggio i 
                        LEFT JOIN utl_automezzo a ON a.id = i.idautomezzo
                        LEFT JOIN utl_automezzo_tipo tipo ON tipo.id = a.idtipo
                        LEFT JOIN con_aggregatore_tipologie_tipologie con ON con.id_tipo_automezzo = tipo.id 
                        LEFT JOIN utl_aggregatore_tipologie agg ON agg.id = con.id_aggregatore
                        LEFT JOIN utl_evento e ON e.id = i.idevento
                        LEFT JOIN utl_tipologia t ON t.id = e.sottotipologia_evento
                        LEFT JOIN utl_tipologia pt ON pt.id = t.idparent
                        LEFT JOIN loc_comune c ON c.id = e.idcomune
                        WHERE agg.descrizione is not null AND i.stato != 3 " . $filters['filter_string'] . "
                        GROUP BY i.created_at
                        ORDER BY anno DESC, mese DESC)
             SELECT anno, mese::INTEGER, giorno::INTEGER, tipologia, sum(total) FROM t 
             GROUP BY anno, mese, giorno, tipologia
             UNION
             SELECT anno, mese::INTEGER, null as giorno, tipologia, sum(total) FROM t 
             GROUP BY anno, mese, tipologia
             UNION
             SELECT anno, null as mese, null as giorno, tipologia, sum(total) FROM t 
             GROUP BY anno, tipologia
             ORDER BY anno DESC, mese DESC, giorno DESC;";

        $command = $connection->createCommand($q);
        

        if ( !empty($filters['filter_params']) ) {
            $command->bindValues($filters['filter_params']);
        }
        
        $result = $command->queryAll();

        $data = [];
        $provider = [];
        $tipologie = [];
        /**
         * Aggreghiamo i risultati in modo da avere un array nestato con anno -> mesi e per ognuno la somma
         */
        foreach ( $result as $q_row ) {
            if ( !in_array($q_row['tipologia'], $tipologie) ) $tipologie[] = $q_row['tipologia'];

            if(!isset($data[$q_row['anno']])) $data[$q_row['anno']] = [
                'totale' => 0,
                'mesi' => []
            ];
            // vediamo se sta splittando un anno o un mese
            if(empty($q_row['mese'])) {
                // è anno
                if( !isset( $data[$q_row['anno']][$q_row['tipologia']] ) ) $data[$q_row['anno']][$q_row['tipologia']] = $q_row['sum'];
                $data[$q_row['anno']]['totale'] += intval($q_row['sum']);
            } else {

                if( !isset( $data[$q_row['anno']]['mesi'][$q_row['mese']] ) ) $data[$q_row['anno']]['mesi'][$q_row['mese']] = [
                        'totale'=>0,
                        'giorni' => []
                    ];
                
                if(empty($q_row['giorno'])) {
                    // è mese
                    if( !isset( $data[$q_row['anno']]['mesi'][$q_row['mese']][$q_row['tipologia']] ) ) $data[$q_row['anno']]['mesi'][$q_row['mese']][$q_row['tipologia']] = $q_row['sum'];

                    $data[$q_row['anno']]['mesi'][$q_row['mese']]['totale'] += intval($q_row['sum']);
                } else {
                    // è giorno
                    if( !isset( $data[$q_row['anno']]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']] ) ) {
                        $data[$q_row['anno']]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']] = [
                            'totale' => 0
                        ];
                    } 

                    if( !isset( $data[$q_row['anno']]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']][$q_row['tipologia']] ) ) {
                        $data[$q_row['anno']]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']][$q_row['tipologia']] = $q_row['sum'];
                    }

                    $data[$q_row['anno']]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']]['totale'] += intval($q_row['sum']);

                }
                
            }
        }


        /**
         * Ora ho un formato utilizzabile, procedo a creare il data provider
         */
        foreach ($data as $anno => $righe) {
            $el = [
                'anno' => $anno,
                'mese' => null
            ];
            foreach ($righe as $key => $value) {
                if($key != 'mesi') $el[$key] = $value;
            }

            $provider[] = $el;

            foreach ($righe['mesi'] as $mese => $mese_righe) {
                $el = [
                    'anno' => $anno,
                    'mese' => $mese
                ];
                foreach ($mese_righe as $key => $value) {
                    $el[$key] = $value;
                }

                $provider[] = $el;


                foreach ($mese_righe['giorni'] as $giorno => $giorno_righe) {
                    $el = [
                        'anno' => $anno,
                        'mese' => $mese,
                        'giorno' => $giorno
                    ];

                    foreach ($giorno_righe as $key => $value) {
                        $el[$key] = $value;
                    }

                    $provider[] = $el;

                }

            }
        }



        $datProvider = new ArrayDataProvider([
            'allModels' => $provider,
            'pagination' => false            
        ]);

        return $this->render('mezzi', [
            'dataProvider' => $datProvider,
            'tipologie' => $tipologie,
            'filter_model' => $filters['filter_model']
        ]);
    }


    public function actionElicotteriPerIntervento()
    {
        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from i.created_at) = :year',
            'month' => 'extract( \'month\' from i.created_at) = :month',
            'date_from' => 'i.created_at >= :date_from',
            'date_to' => 'i.created_at <= :date_to',
            'tipologia' => 'pt.id = :tipologia',
            'sottotipologia' => 't.id = :sottotipologia',
            'pr' => 'c.provincia_sigla = :pr',
            'comune' => 'c.id = :comune'
        ]);

        $connection = Yii::$app->getDb();

        // filters
        /*
        AND extract( 'year' from i.created_at) = 2019 
        AND extract( 'month' from i.created_at) = 5
        AND i.created_at >= '2019-05-01'
        AND i.created_at <= '2019-05-30'
        AND t.id = 92
        AND pt.id = 90
        AND c.provincia_sigla = 'RM'
        AND c.id = 4773
         */

        $q = "WITH t as (SELECT 
                extract( 'year' from i.created_at) as anno,
                extract( 'month' from i.created_at) as mese,
                extract( 'day' from i.created_at) as giorno,
                c.provincia_sigla,
                c.comune, 
                count(DISTINCT(i.id)) as total
                FROM utl_ingaggio i 
                LEFT JOIN utl_automezzo a ON a.id = i.idautomezzo
                LEFT JOIN utl_automezzo_tipo tipo ON tipo.id = a.idtipo
                LEFT JOIN utl_evento e ON e.id = i.idevento
                LEFT JOIN utl_tipologia t ON t.id = e.sottotipologia_evento
                LEFT JOIN utl_tipologia pt ON pt.id = t.idparent
                LEFT JOIN loc_comune c ON c.id = e.idcomune
                WHERE tipo.is_mezzo_aereo = true AND tipo.descrizione ilike 'Elicottero' " . $filters['filter_string'] . "
                GROUP BY anno, mese, giorno, t.id, c.comune, c.provincia_sigla
                ORDER BY anno DESC, mese DESC, c.provincia_sigla DESC, c.comune DESC)
            SELECT anno, mese::INTEGER, giorno::INTEGER, provincia_sigla as provincia, comune, sum(total) FROM t 
            GROUP BY anno, mese, giorno, provincia_sigla, comune
            UNION
            SELECT anno, mese::INTEGER, giorno::INTEGER, provincia_sigla as provincia, ''::TEXT as comune, sum(total) FROM t 
            GROUP BY anno, mese, giorno, provincia_sigla
            UNION
            SELECT anno, mese::INTEGER, giorno::INTEGER, ''::TEXT as provincia, ''::TEXT as comune, sum(total) FROM t 
            GROUP BY anno, mese, giorno
            UNION
            SELECT anno, mese::INTEGER, null as giorno, provincia_sigla as provincia, comune, sum(total) FROM t 
            GROUP BY anno, mese, provincia_sigla, comune
            UNION
            SELECT anno, mese::INTEGER, null as giorno, provincia_sigla as provincia, ''::TEXT as comune, sum(total) FROM t 
            GROUP BY anno, mese, provincia_sigla
            UNION
            SELECT anno, mese::INTEGER, null as giorno, ''::TEXT as provincia, ''::TEXT as comune, sum(total) FROM t 
            GROUP BY anno, mese
            UNION
            SELECT anno, null as mese, null as giorno, ''::TEXT as provincia, ''::TEXT as comune, sum(total) FROM t 
            GROUP BY anno
            ORDER BY anno DESC, mese DESC, giorno DESC, provincia ASC, comune ASC;";

        $command = $connection->createCommand($q);
        
        if ( !empty($filters['filter_params']) ) {
            $command->bindValues($filters['filter_params']);
        }
        
        $result = $command->queryAll();

        $provider = [];
        $comuni = [];
        $data = [];
        $province = [];
        foreach ( $result as $q_row ) {
            
            if ( !empty( $q_row['anno'] ) && empty( $q_row['mese'] ) ) $data[ $q_row['anno'] ] = [ 
                'total' => $q_row['sum'], 
                'mesi' => []
            ];

            if ( !empty( $q_row['mese'] ) && empty( $q_row['giorno'] ) && empty( $q_row['provincia'] ) ) {
                $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ] = [
                    'total' => $q_row['sum'],
                    'giorni' => [],
                    'province' => []
                ];
            }

            if ( !empty( $q_row['giorno'] ) && empty( $q_row['provincia'] ) ) {
                $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ]['giorni'][$q_row['giorno']] = [
                    'total' => $q_row['sum'],
                    'province' => []
                ];
            }

            if( !empty( $q_row['provincia'] ) && empty($q_row['comune']) ) {
                
                if(empty($q_row['giorno'])) {

                    $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ][ 'total_provincia_'.$q_row['provincia'] ] = $q_row['sum'];
                    $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ]['province'][ $q_row['provincia'] ] = [
                        'comuni' => [], 
                        'total'=> $q_row['sum'] 
                    ];

                    // default
                    if(!isset($data[ $q_row['anno'] ]['total_provincia_'.$q_row['provincia']])) $data[ $q_row['anno'] ]['total_provincia_'.$q_row['provincia']] = 0;

                    $data[ $q_row['anno'] ]['total_provincia_'.$q_row['provincia']] += intval($q_row['sum']);

                    if(!isset($data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['total_provincia_'.$q_row['provincia']])) $data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['total_provincia_'.$q_row['provincia']] = intval($q_row['sum']);
                    
                    if(!isset($province[$q_row['provincia']])) $province[$q_row['provincia']] = ['comuni'=>[]];
                
                } else {
                    // inserisco i dati del giorno
                    $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ]['giorni'][$q_row['giorno']][ 'total_provincia_'.$q_row['provincia'] ] = $q_row['sum'];

                    $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ]['giorni'][$q_row['giorno']]['province'][ $q_row['provincia'] ] = [
                        'comuni' => [], 
                        'total'=> $q_row['sum'] 
                    ];
                }
            }

            if( !empty( $q_row['comune'] ) ) {

                if(empty($q_row['giorno'])) {
                    $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ]['province'][ $q_row['provincia'] ]['comuni'][ $q_row['comune'] ] = $q_row['sum'];

                    if(!isset($data[ $q_row['anno'] ]['total_comune_' . self::normalize($q_row['comune']) ])) $data[ $q_row['anno'] ]['total_comune_' . self::normalize($q_row['comune']) ] = 0;

                    if(!isset($data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['total_comune_' . self::normalize($q_row['comune']) ])) $data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['total_comune_' . self::normalize($q_row['comune']) ] = 0;
                    
                    $data[ $q_row['anno'] ]['total_comune_' . self::normalize($q_row['comune']) ] += intval($q_row['sum']);
                    $data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['total_comune_' . self::normalize($q_row['comune']) ] += intval($q_row['sum']);

                } else {
                    $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ]['giorni'][$q_row['giorno']]['province'][ $q_row['provincia'] ]['comuni'][ $q_row['comune'] ] = $q_row['sum'];

                    if(!isset($data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']]['total_comune_' . self::normalize($q_row['comune']) ])) $data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']]['total_comune_' . self::normalize($q_row['comune']) ] = 0;

                    $data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']]['total_comune_' . self::normalize($q_row['comune']) ] += intval($q_row['sum']);
                }

                if(!in_array($q_row['comune'], $province[$q_row['provincia']]['comuni'])) {
                    $province[$q_row['provincia']]['comuni'][] = $q_row['comune'];
                }
            }

        }

        
        
        foreach ($data as $anno => $dati) {
            
            $year = [
                'anno' => $anno,
                'total' => $dati['total']
            ];

            $month_row = [];
            $day_row = [];
            
            foreach ($dati['mesi'] as $m_key => $m_value) {

                $month = [
                    'anno' => $anno,
                    'mese' => $m_key,
                    'total' => $m_value['total']
                ];

                

                foreach ($province as $key => $value) {
                    foreach ($value['comuni'] as $_key) {
                        $year[ 'total_comune_' . self::normalize($_key) ] = isset($dati['total_comune_' . self::normalize($_key)]) ? $dati['total_comune_' . self::normalize($_key)] : 0;

                        $month[ 'total_comune_' . self::normalize($_key) ] = isset($dati['mesi'][$m_key]['total_comune_' . self::normalize($_key)]) ? $dati['mesi'][$m_key]['total_comune_' . self::normalize($_key)] : 0;
                    }

                    $year[ 'total_provincia_' . self::normalize($key) ] = isset($dati['total_provincia_' . self::normalize($key)]) ? $dati['total_provincia_' . self::normalize($key)] : 0;

                    $month[ 'total_provincia_' . self::normalize($key) ] = isset($dati['mesi'][$m_key]['total_provincia_' . self::normalize($key)]) ? $dati['mesi'][$m_key]['total_provincia_' . self::normalize($key)] : 0;

                }

                $month_row[] = $month;

                // i giorni li indicizziamo per mese
                $day_row[$m_key] = [];
                
                foreach ($m_value['giorni'] as $giorno => $giorno_row) {
                    $day = [
                        'anno' => $anno,
                        'mese' => $m_key,
                        'giorno' => $giorno,
                        'total' => $giorno_row['total']
                    ];
                    foreach ($province as $key => $value) {
                        foreach ($value['comuni'] as $_key) {

                            $day[ 'total_comune_' . self::normalize($_key) ] = isset($dati['mesi'][$m_key]['giorni'][$giorno]['total_comune_' . self::normalize($_key)]) ? $dati['mesi'][$m_key]['giorni'][$giorno]['total_comune_' . self::normalize($_key)] : 0;
                        
                        }
                        

                        $day[ 'total_provincia_' . self::normalize($key) ] = isset($dati['mesi'][$m_key]['giorni'][$giorno]['total_provincia_' . self::normalize($key)]) ? $dati['mesi'][$m_key]['giorni'][$giorno]['total_provincia_' . self::normalize($key)] : 0;

                    }
                    

                    $day_row[$m_key][] = $day;
                    
                }


            }

            $provider[] = $year;
            foreach ( $month_row as $month ) {
                $provider[] = $month;
                if(isset($day_row[$month['mese']])) $provider = array_merge($provider, $day_row[$month['mese']] );
            }


        }

        

        $datProvider = new ArrayDataProvider([
            'allModels' => $provider,
            'pagination' => false            
        ]);

        return $this->render('elicotteri', [
            'dataProvider' => $datProvider,
            'province' => $province,
            'filter_model' => $filters['filter_model']
        ]);


    }

    /**
     * Normalizza il nome dei comuni, funzione banale
     * @param  [type] $string [description]
     * @return [type]         [description]
     */
    public static function normalize($string) {
        return preg_replace("/[^a-zA-Z0-9-]/", "_", $string );
    }


    public function actionCoau()
    {
        $model = new FilterModel;
        if(empty(Yii::$app->request->get('FilterModel')) || empty(Yii::$app->request->get('FilterModel')['dataora'])) {
            $model->dataora = date('Y-m-d H:i');
        }

        $filters = $this->buildFilters([
            'dataora' => '
            (e.dataora_evento::DATE = :dataora::DATE OR 
            (e.dataora_evento::DATE <= :dataora::DATE AND e.closed_at::DATE >= :dataora::DATE) OR 
            (e.dataora_evento::DATE <= :dataora::DATE AND e.closed_at is null)
            )',
            'pr' => 'l.provincia_sigla = :pr'
        ], $model);

        $connection = Yii::$app->getDb();

        // filters
        /*
        AND extract( 'year' from e.dataora_evento) = :year 
        AND extract( 'month' from e.dataora_evento) = :month
        AND e.dataora_evento >= :date_from
        AND e.dataora_evento <= :date_to
        AND l.provincia_sigla = :pr
         */

        $q = "WITH t as (
            SELECT 
                /*extract( 'year' from e.dataora_evento) as anno,
                extract( 'month' from e.dataora_evento) as mese,*/
                l.provincia_sigla as provincia,
                count(DISTINCT(e.id)) as total,
                count( DISTINCT(e.id) ) filter ( where t.tipologia = 'Incendio' AND st.tipologia ilike 'boschivo' ) as num_boschivo,
                count( DISTINCT(e.id) ) filter ( where t.tipologia = 'Incendio' AND st.tipologia not ilike 'boschivo' ) as num_non_boschivo,
                count( DISTINCT(e.id) ) filter ( where t.tipologia = 'Incendio' AND st.tipologia ilike 'boschivo' AND e.closed_at <= :dataora ) as num_boschivo_chiuso, 
                count( DISTINCT(e.id) ) filter ( where t.tipologia = 'Incendio' AND st.tipologia ilike 'boschivo' AND e.closed_at > :dataora ) as num_boschivo_aperto, 
                (count(DISTINCT(e.id)) filter (WHERE el.id is not null AND el.engaged = true and el.edited = 1 AND c.id is null)) as solo_regionali
            FROM utl_evento e
            LEFT JOIN richiesta_canadair c ON c.idevento = e.id AND c.engaged = true AND c.edited = 1
            LEFT JOIN richiesta_elicottero el ON el.idevento = e.id AND el.engaged = true AND el.edited = 1
            LEFT JOIN utl_tipologia t ON t.id = e.tipologia_evento
            LEFT JOIN utl_tipologia st ON st.id = e.sottotipologia_evento
            LEFT JOIN loc_comune l ON l.id = e.idcomune
            WHERE l.id_regione = 12 AND e.idparent is null 
            " . $filters['filter_string'] . "
            GROUP BY /*anno, mese,*/ provincia)
            SELECT 
                provincia,/* anno, mese,*/
                total, num_boschivo, num_non_boschivo, num_boschivo_chiuso, num_boschivo_aperto, solo_regionali
            FROM t
            WHERE num_boschivo > 0 OR num_non_boschivo > 0 OR num_boschivo_chiuso > 0 OR num_boschivo_aperto > 0 OR solo_regionali > 0
            ORDER BY provincia ASC/*, anno DESC, mese DESC*/
            ;";

        $keys = [
                'num_boschivo',
                'solo_regionali',
                'num_boschivo_chiuso',
                'num_boschivo_aperto',
                'num_non_boschivo',
            ];

        $command = $connection->createCommand($q);
        
        if ( !empty($filters['filter_params']) ) {
            $command->bindValues($filters['filter_params']);
        }

        
        $result = $command->queryAll();
        $indices = [];
        $data = [];
        $last_row = [
            'regione' => 'Lazio',
            'children' => [],
            'provincia' => 'Totale',
            'num_boschivo' => 0,
            'solo_regionali' => 0,
            'num_boschivo_chiuso' => 0,
            'num_boschivo_aperto' => 0,
            'num_non_boschivo' => 0,
        ];
        // usiamo dei semplici indici di riferimento per prendere i figli successivamente
        $n = 0;
        foreach ($result as $row_q) {
            $result[$n]['regione'] = 'Lazio';
            foreach ($keys as $k) $last_row[$k] += intval($row_q[$k]);
            $n++;
            
        }

        $result[] = $last_row;
        //$data[] = $last_row;

        $datProvider = new ArrayDataProvider([
            'allModels' => $result,//$data,
            'pagination' => false            
        ]);

        return $this->render('coau', [
            'dataProvider' => $datProvider,
            'filter_model' => $filters['filter_model']
        ]);

    }


    public function actionDettaglioVoli()
    {
        $model = new FilterModel;
        if(empty(Yii::$app->request->get('FilterModel')) || empty(Yii::$app->request->get('FilterModel')['year'])) {
            $model->year = date('Y');
        }

        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from v.start_local_timestamp) = :year',
            'date_from' => 'v.start_local_timestamp >= :date_from',
            'date_to' => 'v.start_local_timestamp <= :date_to',
        ], $model);

        $connection = Yii::$app->getDb();

        $q = "WITH t as (
            SELECT 
            device_id,
            device_name,
            extract( 'year' from v.start_local_timestamp) as anno,
            extract( 'month' from v.start_local_timestamp) as mese, 
            stop_local_timestamp,
            start_local_timestamp,
            ore_di_volo
            FROM utl_arka_voli v
            WHERE 1=1 " . $filters['filter_string'] . "
            )
            SELECT anno, null as mese, null::integer as giorno, device_id, device_name, sum( ((stop_local_timestamp - start_local_timestamp)) )
            FROM t
            GROUP BY anno, device_id, device_name
            UNION
            SELECT anno, mese, null::integer as giorno, device_id, device_name, sum( ((stop_local_timestamp - start_local_timestamp)) )
            FROM t
            GROUP BY anno, mese, device_id, device_name
            UNION
            SELECT anno, mese, extract('day' from start_local_timestamp)::integer as giorno, device_id, device_name, sum( ((stop_local_timestamp - start_local_timestamp)) )
            FROM t
            GROUP BY anno, mese, giorno, device_id, device_name
            ORDER BY anno DESC, mese DESC, giorno DESC;";

        $command = $connection->createCommand($q);

        if ( !empty($filters['filter_params']) ) {
            $command->bindValues($filters['filter_params']);
        }
        
        $result = $command->queryAll();

        $indices = [];
        $data = [];
        $parent_indexes = [];
        $elicotteri = [];
        $datas = [];
        $final = [];
        // usiamo dei semplici indici di riferimento per prendere i figli successivamente
        $k = 0;

        foreach ($result as $row_q) { 
            
            $elicotteri[$row_q['device_id']] = $row_q['device_name'];
            // siamo nel gruppo del totale mensile per elicottero
            // imposto l'indice per poter mettere il figlio dopo
            if(empty($row_q['giorno']) && !empty($row_q['mese'])) {
                $parent_indexes[$row_q['device_id']] = $k;
            }

            if(!empty($row_q['giorno'])) {
                if(empty($result[ $parent_indexes[$row_q['device_id']] ]['children'])) $result[ $parent_indexes[$row_q['device_id']] ]['children'] = [];
                $result[ $parent_indexes[$row_q['device_id']] ]['children'][] = $row_q;
                unset($result[$k]);
            }

            if(!isset( $datas[$row_q['anno'] . "_" . $row_q['mese'] . "_" . $row_q['giorno'] ]  )) {
                $datas[$row_q['anno'] . "_" . $row_q['mese'] . "_" . $row_q['giorno'] ] = [
                    'anno' => $row_q['anno'],
                    'mese' => $row_q['mese'],
                    'giorno' => $row_q['giorno'],
                    'device_' . $row_q['device_id'] => $row_q['sum']
                ];
            } else {
                $datas[$row_q['anno'] . "_" . $row_q['mese'] . "_" . $row_q['giorno'] ]['device_' . $row_q['device_id'] ] = $row_q['sum'];
            }

            if(empty($row_q['giorno'])) {
                
                $final[
                    $row_q['anno'] . "_" . $row_q['mese'] . "_" . $row_q['giorno']
                ] = $datas[ $row_q['anno'] . "_" . $row_q['mese'] . "_" . $row_q['giorno'] ];
            
            } else {
                //echo "metto figlio di " . $row_q['anno'] . "_" . $row_q['mese'] . "_" . $row_q['giorno'] . "\n";
                if(!isset(
                    $final[ $row_q['anno'] . "_" . $row_q['mese'] . "_" ]['children']
                )) {
                    //echo "setto\n";
                    $final[$row_q['anno'] . "_" . $row_q['mese'] . "_"]['children'] = [];
                    
                }

                
                $final[ $row_q['anno'] . "_" . $row_q['mese'] . "_" ]['children'][] = $datas[$row_q['anno'] . "_" . $row_q['mese'] . "_" . $row_q['giorno'] ];
                

            }

            $k++;
            
        }


        $datProvider = new ArrayDataProvider([
            'allModels' => $final,//$result,
            'pagination' => false            
        ]);

        return $this->render('dettaglio-voli', [
            'dataProvider' => $datProvider,
            'filter_model' => $filters['filter_model'],
            'elicotteri' => $elicotteri
        ]);

    }
    
}
