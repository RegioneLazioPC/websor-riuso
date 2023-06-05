<?php
namespace api\modules\v1\controllers;

use api\modules\v1\models\AppSignup;
use common\models\UtlAnagrafica;
use common\models\utility\UtlContatto;
use common\models\UtlUtente;
use common\models\VolOrganizzazione;
use common\models\VolVolontario;
use Exception;
use Yii;
use yii\base\Controller;
use yii\data\ActiveDataProvider;
use yii\data\ActiveDataFilter;

use sizeg\jwt\JwtHttpBearerAuth;
use sizeg\jwt\Jwt;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use common\models\UtlOperatorePc;
use common\models\LoginForm;
use common\models\User;

use common\models\MasRubrica;

use api\utils\SendMail;
use backend\models\ResetPasswordForm;

use api\utils\ResponseError;
use common\models\ViewUtentiApp;
use common\models\UtlIngaggio;
use yii\rest\ActiveController;

use common\models\UtlAutomezzo;
use common\models\UtlAttrezzatura;
use common\models\UtlIngaggioRlFeedback;

/**
 * Attivazioni Controller
 *
 */
class AttivazioniController extends ActiveController
{
    public $modelClass = 'common\models\UtlIngaggio';

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
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['index']);
        return $actions;
    }

    /**
     * Di default per il metodo options torniamo ok in modo da non avere errori not found dalle chiamate automatiche del browser
     * @return [type] [description]
     */
    public function actionOptions()
    {
        return ['message'=>'ok'];
    }

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'list',
    ];

    /**
     * Lista attivazioni
     * @return [type] [description]
     */
    public function actionIndex()
    {

        $utente_app = $this->isServiceAvaible();
        return new ActiveDataProvider([
            'query' => UtlIngaggio::find()
                ->joinWith(['evento', 'evento.tipologia'])
                ->where([ 'idorganizzazione' => $utente_app->id_organizzazione])
                ->orderBy(['created_at'=>SORT_DESC]),
            'pagination' => [
                'pageSize' => 20
            ]
        ]);
    }

    /**
     * Lista attivazioni da verificare
     * @return [type] [description]
     */
    public function actionToCheck()
    {

        $utente_app = $this->isServiceAvaible();

        return UtlIngaggio::find()
                ->where([ 'idorganizzazione' => $utente_app->id_organizzazione])
                ->andWhere(['rl_to_check'=>1])
                ->andWhere('created_at::date = now()::date')
                ->count();
    }

    /**
     * Singola attivazione
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionView($id)
    {
        $utente_app = $this->isServiceAvaible();
        $attivazione = UtlIngaggio::findOne($id);
        
        $this->canViewAttivazione($attivazione, $utente_app);

        if ($attivazione->rl_to_check == 1) {
            $attivazione->rl_to_check = 0;
            $attivazione->checked_by_rl = 1;
            $attivazione->checked_by_rl_at = date('Y-m-d H:i:s');
            if (!$attivazione->save()) {
                ResponseError::returnMultipleErrors(422, $attivazione->getErrors());
            }
        }

        return $attivazione->toArray(
            [],
            ['attrezzatura','attrezzatura.tipo','automezzo','automezzo.tipo','evento','evento.tipologia','sede','conVolontarioIngaggio','conVolontarioIngaggio.volontario','conVolontarioIngaggio.volontario.anagrafica',
            'feedbackRl'],
            true
        );
    }

    /**
     * Avaible attivazione resources
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionAvaibleResources($id)
    {
        
        $utente_app = $this->isServiceAvaible();
        $attivazione = UtlIngaggio::findOne($id);

        $this->canViewAttivazione($attivazione, $utente_app);

        return $this->getAvaibleResources($attivazione, $utente_app);
    }

    /**
     * Get selectable data from attivazione
     * @param  [type] $attivazione [description]
     * @param  [type] $utente_app  [description]
     * @return [type]              [description]
     */
    private function getAvaibleResources($attivazione, $utente_app)
    {

        $volontari = VolVolontario::find()
            ->where(['id_organizzazione'=>$utente_app->id_organizzazione, 'operativo'=>true])
            ->joinWith('anagrafica')
            ->asArray()
            ->all();

        $risorse = [];
        if (!empty($attivazione->idautomezzo)) {
            $risorse_q = UtlAutomezzo::find()->where(['idorganizzazione'=>$utente_app->id_organizzazione, 'idtipo'=>$attivazione->automezzo->idtipo,'engaged'=>false])
            ->andWhere(['<>', 'utl_automezzo.id', $attivazione->idautomezzo]);

            if (!empty($attivazione->idsede)) {
                $risorse_q->andWhere(['idsede'=>$attivazione->idsede]);
            }
            
            $risorse = $risorse_q->joinWith(['tipo'])
            ->asArray()
            ->all();
        }

        if (!empty($attivazione->idattrezzatura)) {
            $risorse_q = UtlAttrezzatura::find()->where(['idorganizzazione'=>$utente_app->id_organizzazione, 'idtipo'=>$attivazione->attrezzatura->idtipo,'engaged'=>false])
            ->andWhere(['<>', 'utl_attrezzatura.id', $attivazione->idattrezzatura]);

            if (!empty($attivazione->idsede)) {
                $risorse_q->andWhere(['idsede'=>$attivazione->idsede]);
            }
            
            $risorse = $risorse_q->joinWith(['tipo'])
            ->asArray()
            ->all();
        }

        return [
            'risorse' => $risorse,
            'volontari' => $volontari
        ];
    }

    /**
     * Given feedback from RL
     * @return [type] [description]
     */
    public function actionFeedback($id)
    {
        $utente_app = $this->isServiceAvaible();
        $attivazione = UtlIngaggio::findOne($id);

        $this->canEditAttivazione($attivazione, $utente_app);

        $trans = Yii::$app->db->beginTransaction();

        try {
            if (!empty($attivazione->feedbackRl)) {
                ResponseError::returnSingleError(422, "Feedback già fornito");
            }

            $feed = new UtlIngaggioRlFeedback;
            $feed->stato = UtlIngaggioRlFeedback::getStatoFromClientAction()[Yii::$app->request->post('action')];

            if (Yii::$app->request->post('action') == 'confirm') {
                $avaibles = $this->getAvaibleResources($attivazione, $utente_app);

                if (Yii::$app->request->post('risorsa')) {
                    $selected = Yii::$app->request->post('risorsa');
                    foreach ($avaibles['risorse'] as $resource) {
                        if ($resource['id'] == $selected['id']) {
                            $feed->risorsa = $selected;
                            break;
                        }
                    }
                }

                if (Yii::$app->request->post('volontari')) {
                    $indexed_volontari = [];
                    foreach ($avaibles['volontari'] as $v) {
                        $indexed_volontari[$v['id']] = $v;
                    }

                    $listed_volontari = [];
                    foreach (Yii::$app->request->post('volontari') as $volontario) {
                        $data = $indexed_volontari[$volontario['id_volontario']];
                        $data['refund'] = isset($volontario['refund']) && $volontario['refund'] ? true : false;
                        $listed_volontari[] = $data;
                    }

                    $feed->volontari = $listed_volontari;
                }
            } else {
                if (empty(Yii::$app->request->post('motivazione_rifiuto')) || !in_array(Yii::$app->request->post('motivazione_rifiuto'), array_keys(UtlIngaggioRlFeedback::getMotivazioniRifiuto()))) {
                    ResponseError::returnSingleError(422, "Inserisci le motivazioni del rifiuto");
                }

                $feed->motivazione_rifiuto = Yii::$app->request->post('motivazione_rifiuto');
            }

            $org = VolOrganizzazione::findOne(Yii::$app->user->identity->id_organizzazione);

            $feed->note = Yii::$app->request->post('note');
            $feed->id_ingaggio = $attivazione->id;
            $feed->rl_codfiscale = $utente_app->codfiscale;
            $feed->num_elenco_territoriale = $org->ref_id;
            if (!$feed->save()) {
                ResponseError::returnMultipleErrors(422, $feed->getErrors());
            }

            $attivazione->rl_feedback_to_check = 1;
            $attivazione->feedback_by_rl = 1;
            $attivazione->feedback_by_rl_at = date('Y-m-d H:i:s');
            if (!$attivazione->save()) {
                ResponseError::returnMultipleErrors(422, $attivazione->getErrors());
            }

            $trans->commit();

            return ['message'=>'ok'];
        } catch (\Exception $e) {
            $trans->rollBack();
            throw $e;
        }
    }

    /**
     * Verifica se l'utente ha accesso alle funzionalità
     * @return boolean [description]
     */
    private function isServiceAvaible()
    {

        $disabled_rl = \common\models\app\AppConfig::getKValue('prevent_rl_app', 'prevent');
        if ($disabled_rl && $disabled_rl == 'Si') {
            ResponseError::returnSingleError(404, "Funzione disabilitata");
        }


        $_utente_app = ViewUtentiApp::find()
                ->where([
                    'id_user'=>Yii::$app->user->identity->id,
                    'id_utl_utente' => Yii::$app->user->identity->id_utl_utente,
                    'id_organizzazione' => Yii::$app->user->identity->id_organizzazione
                ])->one();
        
        if (!$_utente_app ||
            empty($_utente_app->id_utl_utente) ||
            empty($_utente_app->id_organizzazione) ||
            $_utente_app->rappresentante_legale != 1) {
            ResponseError::returnSingleError(404, "Utente non abilitato");
        }

        return $_utente_app;
    }

    /**
     * Verify is mine
     * @param  UtlIngaggio $attivazione [description]
     * @param  [type]      $utente_app  [description]
     * @return [type]                   [description]
     */
    private function canViewAttivazione(UtlIngaggio $attivazione, $utente_app)
    {
        
        if ($attivazione->idorganizzazione != $utente_app->id_organizzazione) {
            ResponseError::returnSingleError(422, "Non puoi visualizzare questa attivazione");
        }
    }

    /**
     * Verify if is mine and date is today
     * @param  UtlIngaggio $attivazione [description]
     * @param  [type]      $utente_app  [description]
     * @return [type]                   [description]
     */
    private function canEditAttivazione(UtlIngaggio $attivazione, $utente_app)
    {
        
        $date_attivazione = substr($attivazione->created_at, 0, 10);
        $today = new \DateTime();

        if ($attivazione->idorganizzazione != $utente_app->id_organizzazione ||
            $date_attivazione != $today->format('Y-m-d')
        ) {
            ResponseError::returnSingleError(422, "Non puoi modficare questa attivazione");
        }
    }
}
