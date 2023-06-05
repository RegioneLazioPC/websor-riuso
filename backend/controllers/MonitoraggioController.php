<?php
namespace backend\controllers;

use Yii;

use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\InvalidRouteException;
use common\models\UplMedia;
use common\models\UplTipoMedia;
use yii\web\UploadedFile;
use yii\web\Response;
use yii\helpers\Url;


use yii\db\Query;
use yii\db\Expression;

use common\models\MasInvio;
use common\models\ConMasInvioContact;

use common\utils\MasHttpServices;
use common\models\MasMessage;

/**
 * Monitoraggio Controller API
 *
 * Controller di servizi http di monitoraggio per il MAS
 *
 * @author Fabio Rizzo
 *
 */
class MonitoraggioController extends Controller
{
    
    public function actions()
    {
        $actions = parent::actions();
        
        return $actions;
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'deleteTemplate' => ['POST'],
                    'delete' => ['POST']
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user) {
                        Yii::error(json_encode(Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId())));
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['mas-running', 'mas-log', 'mas-invio-running',
                        'mas-contacts', 'mas-attempt',
                        'mas-invio-messages',
                        'contacts', 'grouped-contacts',
                        'resend',
                        'mas-process-message', 'mas-process-reverify', 'mas-stop-process-message', 'mas-restart-process-message'
                        ],
                        'permissions' => ['sendMasMessage']
                    ],
                ],

            ],
        ];
    }

    /**
     * Reinvia messaggio
     * @return [type] [description]
     */
    public function actionResend()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = MasInvio::findOne(Yii::$app->request->post('id_invio'));

        if (!empty($model->mas_ref_id)) {
            $ids = [];
            if (Yii::$app->request->post('action') == 'selected') {
                $ids = json_decode(Yii::$app->request->post('contacts'), true);
            }
            
            \common\utils\MasV2Dispatcher::resend($model, Yii::$app->request->post('action'), $ids);
            
            return ['message'=>'ok'];
        } else {
            $id_invio = $model->id;
            switch (Yii::$app->request->post('action')) {
                case 'all':
                    $contatti = ConMasInvioContact::find()
                    ->select([
                    'id','id_rubrica_contatto','tipo_rubrica_contatto','valore_rubrica_contatto','ext_id','everbridge_identifier','channel','vendor','valore_riferimento'
                    ])
                    ->addSelect(new Expression(intval($model->id).' as id_invio'))
                    ->addSelect(new Expression('0 as status'))
                    ->addSelect(new Expression(time().' as created_at'))
                    ->addSelect(new Expression(time().' as updated_at'))
                    ->where(['id_invio'=>$id_invio]);//->all();
                    break;
                case 'not_sent':
                    $contatti = ConMasInvioContact::find()
                    ->select([
                    'id','id_rubrica_contatto','tipo_rubrica_contatto','valore_rubrica_contatto','ext_id','everbridge_identifier','channel','vendor','valore_riferimento'
                    ])
                    ->addSelect(new Expression(intval($model->id).' as id_invio'))
                    ->addSelect(new Expression('0 as status'))
                    ->addSelect(new Expression(time().' as created_at'))
                    ->addSelect(new Expression(time().' as updated_at'))
                //->innerJoin('mas_single_send','mas_single_send.status = 0 && mas_single_send.id_con_mas_invio_contact = con_mas_invio_contact.id')
                    ->where(['con_mas_invio_contact.id_invio'=>$id_invio])
                    ->andWhere('(SELECT COUNT(id) FROM mas_single_send WHERE 
                    mas_single_send.id_con_mas_invio_contact = con_mas_invio_contact.id AND 
                    (
                        ( (mas_single_send.channel IN (\'Pec\',\'Fax\',\'Sms\')) AND mas_single_send.status = 3 ) OR 
                        ( (mas_single_send.channel NOT IN ( \'Pec\', \'Fax\', \'Sms\') ) AND (mas_single_send.status IN (2,3)) )
                    )
                ) = 0');
                //->all();
                    break;
                case 'selected':
                    $ids = json_decode(Yii::$app->request->post('contacts'), true);
                
                    $contatti = ConMasInvioContact::find()
                    ->select([
                    'id','id_rubrica_contatto','tipo_rubrica_contatto','valore_rubrica_contatto','ext_id','everbridge_identifier','channel','vendor','valore_riferimento'
                    ])
                    ->addSelect(new Expression(intval($model->id).' as id_invio'))
                    ->addSelect(new Expression('0 as status'))
                    ->addSelect(new Expression(time().' as created_at'))
                    ->addSelect(new Expression(time().' as updated_at'))
                    ->where(['id'=>$ids]);//->all();
                    break;
            }

            $q_s = $contatti->createCommand()->getRawSql();
            $all_contacts = $contatti->asArray()->all();
            
            $dispatch = new \common\utils\MasDispatcher($all_contacts, $model);
            $res = $dispatch->initialize();

            return $res;
        }
    }

    public function actionContacts()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        
        $invio = MasInvio::findOne(Yii::$app->request->get('id_invio'));
        if (!$invio) {
            throw new \yii\web\HttpException(404, "Invio non trovato");
        }

        if (!empty($invio->mas_ref_id)) {
            $reset = (Yii::$app->request->get('reset')) ? Yii::$app->request->get('reset') : 0;
            \common\utils\MasV2Dispatcher::updateMessageFeedback($invio, $reset);

            $records = \common\models\ConMasInvioContact::find()
                ->joinWith(['masV2SingleSends', 'contatto'])
                ->where(['con_mas_invio_contact.id_invio'=>Yii::$app->request->get('id_invio')])
                ->asArray()
                ->all();
            $ret = [];
            foreach ($records as $record) {
                $record['tipologia_riferimento'] = $record['contatto'] ? $record['contatto']['tipologia_riferimento'] : null;
                $ret[] = $record;
            }

            return ['data'=>$ret];
        } else {
            $records = \common\models\ConMasInvioContact::find()
                ->joinWith(['masSingleSendsWithoutDuplicates', 'contatto'])
                ->where(['con_mas_invio_contact.id_invio'=>Yii::$app->request->get('id_invio')])
                ->asArray()
                ->all();

            $ret = [];
            foreach ($records as $record) {
                $record['tipologia_riferimento'] = $record['contatto'] ? $record['contatto']['tipologia_riferimento'] : null;
                $ret[] = $record;
            }

            return ['data'=>$ret];
        }
    }


    private function ciclateGroupedContacts($list, $key)
    {
        foreach ($list as $contact) {
            try {
                $to_return = [
                    'id' => $contact['id'],
                    'id_invio' => $contact['id_invio'],
                    'id_rubrica_contatto' => $contact['id_rubrica_contatto'],
                    'valore_rubrica_contatto' => $contact['valore_rubrica_contatto'],
                    'tipo_rubrica_contatto' => $contact['tipo_rubrica_contatto'],
                    'tipologia_riferimento' => $contact['contatto']['tipologia_riferimento'],
                    'valore_riferimento' => $contact['valore_riferimento'],
                    'ext_id' => $contact['ext_id'],
                    'everbridge_identifier' => $contact['everbridge_identifier'],
                    'raggiunto' => 'No'
                ];
                $channels = ['Pec','Sms','Email','Push','Fax'];
                foreach ($channels as $ch) {
                    $const_mas_status = ($ch == 'Pec' || $ch == 'Fax' || $ch == 'Sms') ?
                    MasMessage::STATUS_RECEIVED : MasMessage::STATUS_SEND;

                    $ch_sent = array_filter($contact[$key], function ($el) use ($ch, $const_mas_status) {
                        return $el['channel'] == $ch && in_array($el['status'], $const_mas_status);
                    });
                    $to_return['sent_'.$ch] = ( count($ch_sent) > 0 ) ? 'Si' : 'No';
                    if (count($ch_sent) > 0) {
                        $to_return['raggiunto'] = 'Si';
                    }
                }
                yield $to_return;
            } catch (\Exception $e) {
                Yii::error('Errore contatto in monitoraggio contatti raggruppati ' . $e->getMessage(), 'websor');
                yield $contact;
            }
        }
    }

    public function actionGroupedContacts()
    {

        $invio = MasInvio::findOne(Yii::$app->request->get('id_invio'));
        if (!$invio) {
            throw new \yii\web\HttpException(404, "Invio non trovato");
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!empty($invio->mas_ref_id)) {
            \common\utils\MasV2Dispatcher::updateMessageFeedback($invio);


            $indexed = [];
            $list = \common\models\ConMasInvioContact::find()
                ->from(['t' => '(SELECT * FROM con_mas_invio_contact)'])
                ->joinWith(['masV2SingleSends', 'contatto'])
                ->where(['t.id_invio'=>Yii::$app->request->get('id_invio')])
                ->asArray()
                ->all();
            
            
            foreach ($list as $record) {
                if (!isset($indexed[
                    $record['id_rubrica_contatto'] . "_" .
                    $record['tipo_rubrica_contatto'] . "_" .
                    $record['id_invio']
                ])) {
                    $indexed[
                        $record['id_rubrica_contatto'] . "_" .
                        $record['tipo_rubrica_contatto'] . "_" .
                        $record['id_invio']
                    ] = $record;
                } else {
                    if (is_array($record['masV2SingleSends']) && count($record['masV2SingleSends']) > 0) {
                        $indexed[
                            $record['id_rubrica_contatto'] . "_" .
                            $record['tipo_rubrica_contatto'] . "_" .
                            $record['id_invio']
                        ]['masV2SingleSends'] = array_merge($indexed[
                            $record['id_rubrica_contatto'] . "_" .
                            $record['tipo_rubrica_contatto'] . "_" .
                            $record['id_invio']
                        ]['masV2SingleSends'], $record['masV2SingleSends']);//[0];
                    }
                }
            }
            $list = array_values($indexed);
            $key = 'masV2SingleSends';
        } else {
            $list = \common\models\ConMasInvioContact::find()
                ->from(['t' => '(SELECT distinct on (id_rubrica_contatto, tipo_rubrica_contatto, id_invio) * FROM con_mas_invio_contact)'])
                ->joinWith(['masSingleSendsAggregated', 'contatto'])
                ->where(['t.id_invio'=>Yii::$app->request->get('id_invio')])
                ->asArray()
                ->all();
            $key = 'masSingleSendsAggregated';
        }
        
        $return_data = [];
        foreach ($this->ciclateGroupedContacts($list, $key) as $result) {
            $return_data[] = $result;
        }
        

        return [
            'data' => $return_data
        ];
    }

    /**
     * Ritorna se  consumer stanno girando
     * @return [type] [description]
     */
    public function actionMasRunning()
    {
        return MasHttpServices::isRunning();
    }

    /**
     * Ritorna se il singolo invio sta girando
     *
     * GET id_invio
     *
     * @return [type] [description]
     */
    public function actionMasInvioRunning()
    {
        return MasHttpServices::getInvioStatus(Yii::$app->request->get('id_invio'));
    }

    /**
     * Ritorna se il singolo invio sta girando
     *
     * GET id_invio
     *
     * @return [type] [description]
     */
    public function actionMasInvioMessages()
    {
        return MasHttpServices::getInvioMessages(Yii::$app->request->get('id_invio'));
    }

    /**
     * Ritorna il json con i log del mas
     *
     * GET id_invio
     * GET channel
     *
     * @return [type] [description]
     */
    public function actionMasLog()
    {
        return MasHttpServices::getLogs(Yii::$app->request->get('id_invio'), Yii::$app->request->get('channel'));
    }

    /**
     * Ritorna i dettagli sui singoli tentativi di invio per contatto
     *
     * GET id_invio
     *
     * @return [type] [description]
     */
    public function actionMasAttempt()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $contatti = MasHttpServices::getContacts(Yii::$app->request->get('id_invio'));
        $result = [];
        $cs = json_decode($contatti, true);
        foreach ($cs['data'] as $contatto) {
            $result[] = [
                '_id_message' => $contatto['_id_message'],
                'stato' => $contatto['stato'],
                'contatto' => $contatto['ref']['valore_rubrica_contatto'],
                'ref' => $contatto['ref']['valore_riferimento'],
                'verification_identifier' => $contatto['verification_identifier'],
                'channel' => $contatto['ref']['channel'],
                'add_time' => $contatto['add_time'],
                'sent_time' => $contatto['sent_time'],
                'feedback_time' => $contatto['feedback_time'],
            ];
        }
        return ['data'=>$result];
    }

    /**
     * Ritorna i dettagli sui singoli tentativi di invio per contatto
     *
     * GET id_message
     *
     * @return [type] [description]
     */
    public function actionMasProcessMessage()
    {
        return MasHttpServices::processManually(Yii::$app->request->get('id_message'));
    }

    /**
     * Riverifica un messaggio
     *
     * GET id_message
     *
     * @return [type] [description]
     */
    public function actionMasProcessReverify()
    {
        return MasHttpServices::reverifyMessage(Yii::$app->request->get('id_message'));
    }

    /**
     * Ritorna i dettagli sui singoli tentativi di invio per contatto
     *
     * GET id_message
     *
     * @return [type] [description]
     */
    public function actionMasStopProcessMessage()
    {
        return MasHttpServices::stopProcess(Yii::$app->request->get('id_message'));
    }

    /**
     * Ritorna i dettagli sui singoli tentativi di invio per contatto
     *
     * GET id_message
     *
     * @return [type] [description]
     */
    public function actionMasRestartProcessMessage()
    {
        return MasHttpServices::restartProcess(Yii::$app->request->get('id_message'));
    }
}
