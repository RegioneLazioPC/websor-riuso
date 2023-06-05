<?php

namespace api\modules\v1\controllers;

use common\models\MasMessage;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;

/**
 * Notifiche push per utente app
 *
 */
class NotificationsController extends ActiveController
{
    public $modelClass = 'common\models\MasMessage';
    private $per_page_records = 15;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \api\utils\Authenticator::class,
            'except' => ['options']
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    /**
     * Lista notifiche push ricevute da un utente
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $connection = Yii::$app->getDb();

        /**
         * Query:
         * 
         * SELECT DISTINCT(mas_message.id), alm_allerta_meteo.data_allerta, mas_message.push_text, mas_message_template.push_body, to_timestamp(mas_message.created_at) FROM utl_utente 
         *   LEFT JOIN mas_rubrica ON mas_rubrica.id_anagrafica = utl_utente.id_anagrafica
         *   LEFT JOIN mas_single_send ON mas_single_send.id_rubrica_contatto = mas_rubrica.id AND mas_single_send.channel = 'Push'
         *   LEFT JOIN mas_invio ON mas_invio.id = mas_single_send.id_invio
         *   LEFT JOIN mas_message ON mas_message.id = mas_invio.id_message
         *   LEFT JOIN mas_message_template ON mas_message_template.id = mas_message.id_template
         *   LEFT JOIN alm_allerta_meteo ON alm_allerta_meteo.id = mas_message.id_allerta
         *   WHERE utl_utente.iduser = {id utente};
         */

        $page = (Yii::$app->request->get('page')) ? Yii::$app->request->get('page') : 1;

        $limit = $this->per_page_records;
        $offset = ($page-1) * $this->per_page_records;

        /**
         * Query per conteggio dei record
         */
        
        $count_q = "SELECT COUNT(DISTINCT(mas_message.id)) as num FROM utl_utente 
                LEFT JOIN mas_rubrica ON mas_rubrica.id_anagrafica = utl_utente.id_anagrafica
                LEFT JOIN mas_single_send ON mas_single_send.id_rubrica_contatto = mas_rubrica.id AND mas_single_send.channel = 'Push'
                LEFT JOIN mas_invio ON mas_invio.id = mas_single_send.id_invio
                LEFT JOIN mas_message ON mas_message.id = mas_invio.id_message
                LEFT JOIN mas_message_template ON mas_message_template.id = mas_message.id_template
                LEFT JOIN alm_allerta_meteo ON alm_allerta_meteo.id = mas_message.id_allerta
                WHERE utl_utente.iduser = :user_id AND mas_message.id IS NOT NULL;";

        $command = $connection->createCommand($count_q)
            ->bindValue ( ':user_id', Yii::$app->user->identity->id );
        $count_result = $command->queryAll();

        $total = $count_result[0]['num'];

        $records = [];

        if( $total >= (($page-1)*$this->per_page_records) && $total > 0 ) {

            /**
             * Effettuiamo la query solo se ci sono record
             */
            $q = "SELECT DISTINCT(mas_message.id), alm_allerta_meteo.data_allerta, mas_message.push_text, mas_message_template.push_body, to_timestamp(mas_message.created_at) FROM utl_utente 
                    LEFT JOIN mas_rubrica ON mas_rubrica.id_anagrafica = utl_utente.id_anagrafica
                    LEFT JOIN mas_single_send ON mas_single_send.id_rubrica_contatto = mas_rubrica.id AND mas_single_send.channel = 'Push'
                    LEFT JOIN mas_invio ON mas_invio.id = mas_single_send.id_invio
                    LEFT JOIN mas_message ON mas_message.id = mas_invio.id_message
                    LEFT JOIN mas_message_template ON mas_message_template.id = mas_message.id_template
                    LEFT JOIN alm_allerta_meteo ON alm_allerta_meteo.id = mas_message.id_allerta
                    WHERE utl_utente.iduser = :user_id AND mas_message.id IS NOT NULL
                    ORDER BY to_timestamp(mas_message.created_at) DESC
                    OFFSET :offset LIMIT :limit_record;";

            
            $command = $connection->createCommand($q)
                ->bindValue ( ':offset', $offset )
                ->bindValue ( ':limit_record',$limit )
                ->bindValue ( ':user_id', Yii::$app->user->identity->id );

            $records = $command->queryAll();

            /**
             * Formatto il template / messaggio
             */
            foreach ($records as $key => $record) {
                $records[$key]['notification_text'] = \common\utils\MasMessageManager::returnPlainReplacedMessage( 
                    $record['push_text'], 
                    $record['push_body'], 
                    $record['data_allerta']
                );
            }

        }


        return [
            'data' => $records,
            'page' => $page,
            'page_number' => (($total % $this->per_page_records) == 0) ? $total / $this->per_page_records : intval($total / $this->per_page_records) + 1,
            'total' => $total
        ];
        
    }



}


