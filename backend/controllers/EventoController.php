<?php

namespace backend\controllers;

use common\models\cap\CapExposedMessage;
use common\models\cap\CapMessages;
use paragraph1\phpFCM\Recipient\Device;
use yii\web\Response;
use common\models\ComComunicazioni;
use common\models\ConEventoExtra;
use common\models\ConEventoSegnalazione;
use common\models\ConOperatoreEvento;
use common\models\ConOperatoreTask;
use common\models\ConOperatoreTaskSearch;
use common\models\LocComune;
use common\models\LocIndirizzo;
use common\models\LocCivico;
use common\models\MyHelper;
use common\models\RichiestaCanadair;
use common\models\RichiestaCanadairSearch;
use common\models\RichiestaDos;
use common\models\RichiestaDosSearch;
use common\models\RichiestaElicottero;
use common\models\RichiestaElicotteroSearch;
use common\models\RichiestaMezzoAereo;
use common\models\User;
use common\models\UtlExtraSegnalazione;
use common\models\UtlOperatorePc;
use common\models\UtlSegnalazione;
use common\models\UtlSegnalazioneSearch;
use common\models\UtlIngaggio;
use common\models\UtlIngaggioSearch;
use common\models\ViewVolontariAttivazioni;
use common\models\UtlTipologia;
use common\models\UtlUtente;
use common\models\ViewRisorseSchieramenti;
use yii\data\ArrayDataProvider;
use common\models\geo\GeoQuery;

use common\models\UtlAutomezzo;
use common\models\UtlAttrezzatura;
use Exception;
use Yii;
use common\models\UtlEvento;
use common\models\UtlEventoSearch;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\debug\models\timeline\DataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;

use common\models\EvtSchedaCoc;
use common\models\UplTipoMedia;
use common\models\UplMedia;
use yii\web\UploadedFile;
use common\models\ConSchedaCocDocumenti;
use common\models\SalaComunaleCap;
use common\models\SalaOperativaEsterna;
use GuzzleHttp\Client;
use kartik\mpdf\Pdf;

/**
 * EventoController implements the CRUD actions for UtlEvento model.
 */
class EventoController extends Controller
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
                    if (Yii::$app->user) {
                        Yii::error(json_encode(Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId())));
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['schieramento-list','activate-schieramento'],
                        'permissions' => ['activateSchieramento']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'map', 'view', 'list-eventi-map', 'monitoraggio'],
                        'permissions' => ['listEventi']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['closed'],
                        'permissions' => ['listEventiChiusi']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['archived'],
                        'permissions' => ['listEventiArchiviati']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'permissions' => ['viewEvento']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['list-segnalazioni'],
                        'permissions' => ['listSegnalazioni']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view-segnalazione'],
                        'permissions' => ['viewSegnalazione']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['send-mail-dos'],
                        'permissions' => ['createRichiestaDos']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update-dos'],
                        'permissions' => ['updateRichiestaDos']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create-elicottero'],
                        'permissions' => ['createRichiestaElicottero']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update-elicottero'],
                        'permissions' => ['updateRichiestaElicottero', 'updatePartialRichiestaElicottero']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['annulla-richiesta-elicottero'],
                        'permissions' => ['annullateRichiestaElicottero']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['send-riepilogo-coau', 'scheda-coau'],
                        'permissions' => ['sendRichiestaElicotteroToCOAU']
                    ],
                    [

                        'allow' => true,
                        'actions' => ['create-scheda-coc'],
                        'permissions' => ['createSchedaCoc']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update-scheda-coc', 'add-scheda-coc-documento'],
                        'permissions' => ['updateSchedaCoc']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['send-mail-canadair'],
                        'permissions' => ['createRichiestaCanadair']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update-canadair'],
                        'permissions' => ['updateRichiestaCanadair']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'create-task-mattinale'
                        ],
                        'permissions' => ['createTaskEvento']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update-task'],
                        'permissions' => ['updateTaskEvento']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['validate-task-mattinale', 'validate-create-task-mattinale'],
                        'permissions' => ['createTaskEvento', 'updateTaskEvento']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['assign-event'],
                        'permissions' => ['assignEvento']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['remove-event', 'delete'],
                        'permissions' => ['removeEvento']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['public-event'],
                        'permissions' => ['publicEvento']
                    ],

                    [
                        'allow' => true,
                        'actions' => ['gestione-evento'],
                        'permissions' => [
                            'createTaskEvento', 'updateTaskEvento', 'createIngaggio', 'updateIngaggio',
                            'createRichiestaCanadair', 'createRichiestaElicottero', 'createRichiestaDos',
                            'updateRichiestaCanadair', 'updateRichiestaElicottero', 'updateRichiestaDos'
                        ]
                    ],

                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'permissions' => ['createEvento']
                    ],

                    [
                        'allow' => true,
                        'actions' => ['update', 'archive', 'assegna-sala-operativa-esterna'],
                        'permissions' => ['updateEvento', 'closeEvento', 'openClosedEvento']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'elicotteri-in-volo-html',
                            'get-sottotipologia', 'get-indirizzi', 'get-civici', 'search-indirizzo', 'search-toponimo', 'get-comune'
                        ],
                        'roles' => ['@']
                    ],
                ],

            ],
        ];
    }

    public function actionListEventiMap()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return UtlEvento::find()
            ->where(['!=', 'stato', 'Chiuso'])
            ->andWhere(['is_public' => 1])
            ->joinWith(['tipologia'])
            ->asArray()
            ->all();
    }

    /**
     * Lists all UtlEvento models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UtlEventoSearch();

        if (Yii::$app->user->can('operatore')) {
            $dataProvider = $searchModel->searchByOperatore(
                ['UtlEventoSearch' =>
                array_merge(
                    isset(Yii::$app->request->queryParams['UtlEventoSearch']) ?
                        Yii::$app->request->queryParams['UtlEventoSearch'] :
                        [],
                    ['idparent' => 0]
                )]
            );
        } else {
            $dataProvider = $searchModel->search(
                ['UtlEventoSearch' =>
                array_merge(
                    isset(Yii::$app->request->queryParams['UtlEventoSearch']) ?
                        Yii::$app->request->queryParams['UtlEventoSearch'] :
                        [],
                    ['idparent' => 0]
                )]
            );
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all UtlEvento models closed.
     * @return mixed
     */
    public function actionClosed()
    {
        $searchModel = new UtlEventoSearch();
        $dataProvider = $searchModel->searchClosed(
            ['UtlEventoSearch' =>
            array_merge(
                isset(Yii::$app->request->queryParams['UtlEventoSearch']) ?
                    Yii::$app->request->queryParams['UtlEventoSearch'] :
                    [],
                ['idparent' => 0]
            )]
        );

        return $this->render('closed', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all UtlEvento models archived.
     * @return mixed
     */
    public function actionArchived()
    {
        $searchModel = new UtlEventoSearch();
        $dataProvider = $searchModel->searchArchived(
            ['UtlEventoSearch' =>
            array_merge(
                isset(Yii::$app->request->queryParams['UtlEventoSearch']) ?
                    Yii::$app->request->queryParams['UtlEventoSearch'] :
                    [],
                ['idparent' => 0]
            )]
        );

        return $this->render('archived', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Archive Event
     * @return mixed
     */
    public function actionArchive()
    {
        $data = Yii::$app->request->post('DynamicModel');

        if (empty($data['data_dal']) || empty($data['data_al'])) {
            return $this->redirect(['closed']);
        }


        $data_dal = \DateTime::createFromFormat('d-m-Y', $data['data_dal']);
        if (is_bool($data_dal)) {
            return $this->redirect(['closed']);
        }

        $data_al = \DateTime::createFromFormat('d-m-Y', $data['data_al']);
        if (is_bool($data_al)) {
            return $this->redirect(['closed']);
        }



        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("
            UPDATE utl_evento SET archived = 1 WHERE 
            closed_at >= :data_dal AND closed_at <= :data_al AND stato = 'Chiuso'", [
            ':data_dal' => $data_dal->format('Y-m-d'),
            ':data_al' => $data_al->format('Y-m-d')
        ]);

        $result = $command->queryAll();

        return $this->redirect(['archived']);
    }

    /**
     * Map all UtlEvento models.
     * @return mixed
     */
    public function actionMap()
    {
        $searchModel = new UtlEventoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('map', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UtlEvento model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);


        $tasksSearchModel = new ConOperatoreTaskSearch();
        $tasksDataProvider = $tasksSearchModel->searchByEvento($id, Yii::$app->request->queryParams);

        $segnalazioniSearchModel = new UtlSegnalazioneSearch();
        $segnalazioniDataProvider = $segnalazioniSearchModel->searchByEvento($id, Yii::$app->request->queryParams);

        $ingaggiSearchModel = new UtlIngaggioSearch();
        $ingaggiDataProvider = $ingaggiSearchModel->searchByEvento($id, Yii::$app->request->queryParams);

        $volontariSearchModel = new ViewVolontariAttivazioni();
        $volontariDataProvider = (!empty($model->idparent)) ?
            $volontariSearchModel->searchByFronte($id, Yii::$app->request->queryParams) :
            $volontariSearchModel->searchByEvento($id, Yii::$app->request->queryParams);

        $ricElicotteroSearchModel = null;
        $ricElicotteroDataProvider = null;
        $dosSearchModel = null;
        $dosDataProvider = null;
        $ricCanadairSearchModel = null;
        $ricCanadairDataProvider = null;

        if ($model->stato == 'Chiuso') {
            // Model richiesta DOS
            $dosSearchModel = new RichiestaDosSearch();
            $dosDataProvider = $dosSearchModel->searchByEvento($id, Yii::$app->request->queryParams);

            // Model richiesta elicottero
            $ricElicotteroSearchModel = new RichiestaElicotteroSearch();
            $ricElicotteroDataProvider = $ricElicotteroSearchModel->searchByEvento($id, Yii::$app->request->queryParams);

            // Model richiesta canadair
            $ricCanadairSearchModel = new RichiestaCanadairSearch();
            $ricCanadairDataProvider = $ricCanadairSearchModel->searchByEvento($id, Yii::$app->request->queryParams);
        }

        return $this->render('view', [
            'model' => $model,
            'tasksSearchModel' => $tasksSearchModel,
            'tasksDataProvider' => $tasksDataProvider,
            'segnalazioniSearchModel' => $segnalazioniSearchModel,
            'segnalazioniDataProvider' => $segnalazioniDataProvider,
            'ingaggiSearchModel' => $ingaggiSearchModel,
            'ingaggiDataProvider' => $ingaggiDataProvider,
            'volontariSearchModel' => $volontariSearchModel,
            'volontariDataProvider' => $volontariDataProvider,
            'ricElicotteroSearchModel' => $ricElicotteroSearchModel,
            'ricElicotteroDataProvider' => $ricElicotteroDataProvider,
            'dosSearchModel' => $dosSearchModel,
            'dosDataProvider' => $dosDataProvider,
            'ricCanadairSearchModel' => $ricCanadairSearchModel,
            'ricCanadairDataProvider' => $ricCanadairDataProvider,
            'geoQueries' => $this->getGeoQueries($model)
        ]);
    }

    /**
     * Displays tasks of a single UtlEvento model.
     * @param integer $id
     * @deprecated
     * @return mixed
     */
    public function actionListOperatori($id)
    {
        // Prendo gli operatori loggati sul sistema by session
        $connection = Yii::$app->db;
        $dataLimite = date('Y-m-d H:i:s', strtotime("-1 hour")); // considera l'ultima ora come limite per filtrare gli operatori online
        $model = $connection->createCommand("SELECT id_user FROM session WHERE (id_user is not null) AND (last_write > '$dataLimite')");
        $operatoriIds = $model->queryColumn();

        $operatori = UtlOperatorePc::find()->where(['iduser' => $operatoriIds])->joinWith('salaoperativa')->all();
        return $this->render('list-operatori', [
            'operatori' => $operatori,
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Save scheda mezzo aereo.
     * If send is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionNewPlane()
    {
        Yii::$app->response->format = 'json';

        $model = new RichiestaMezzoAereo();
        $model->idevento = Yii::$app->request->post('UtlEvento')['id'];
        $model->idingaggio = Yii::$app->request->post('idingaggio');
        $model->idoperatore = Yii::$app->user->identity->operatore->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ['code' => '200', 'message' => 'Success'];
        } else {
            return ['code' => '500', 'message' => 'Errore'];
        }
    }


    /**
     * Send mail for DOS UtlIngaggio model.
     * If send is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSendMailDos()
    {
        $model = new ComComunicazioni();
        $evento = $this->findModel(Yii::$app->request->post('UtlEvento')['id']);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //Nuova richiesta ed invio mail
            $content = "<p>Evento n.protocollo: " . @$evento->num_protocollo . "<br>";
            $content .= "Tipologia: " . @$evento->tipologia->tipologia . "<br>Sotto Tipologia: " . @$evento->sottotipologia->tipologia . "<br>";
            $content .= "Comune: " .  @$evento->comune->comune . "<br>Indirizzo: " . @$evento->indirizzo . "<br>";
            $content .= "Data e ora creazione evento: " . @Yii::$app->formatter->asDatetime($evento->dataora_evento) . "</p>";



            if (!empty($model->contenuto)) {
                $content .= "<p><strong>NOTE AGGIUNTIVE</strong><br>{$model->contenuto}</p>";
            }

            $message = Yii::$app->mailer->compose()
                ->setFrom(Yii::$app->params['adminEmail'])
                ->setTo($model->contatto)
                ->setSubject($model->oggetto)
                ->setHtmlBody($content)
                ->send();

            $mdRichiestaDos = new RichiestaDos();
            $mdRichiestaDos->idevento = Yii::$app->request->post('UtlEvento')['id'];
            $mdRichiestaDos->idingaggio = Yii::$app->request->post('idingaggio');
            $mdRichiestaDos->idoperatore = Yii::$app->user->identity->operatore->id;
            $mdRichiestaDos->idcomunicazione = $model->id;

            if (!($mdRichiestaDos->save())) {
                Yii::$app->session->setFlash('error', "Errore invio dati, verificare i campi");
                return $this->redirect("gestione-evento?idEvento={$mdRichiestaDos->idevento}&tab=dos");
            }

            //Salvo dati giornale evento
            $diarioEvento = new ConOperatoreTask();
            $diarioEvento->idfunzione_supporto = 5; //DATI CABLATI NEL DB
            $diarioEvento->idtask = 6; //DATI CABLATI NEL DB
            $diarioEvento->idevento = $mdRichiestaDos->idevento;
            $diarioEvento->note = $model->oggetto;
            $diarioEvento->idoperatore = Yii::$app->user->identity->operatore->id;

            if (!($diarioEvento->save())) {
                Yii::$app->session->setFlash('error', "Errore invio dati, verificare i campi");
                return $this->redirect("gestione-evento?idEvento={$mdRichiestaDos->idevento}&tab=dos");
            }

            Yii::$app->session->setFlash('success', "Salvataggio avvenuto correttamente");
            return $this->redirect("gestione-evento?idEvento={$mdRichiestaDos->idevento}&tab=dos");
        } else {
            Yii::$app->session->setFlash('error', "Errore invio dati, verificare i campi");
            return $this->redirect("gestione-evento?idEvento={$evento->id}&tab=dos");
        }
    }

    /**
     * Crea richiesta elicottero
     * @param integer $id
     * @return mixed
     */
    public function actionCreateElicottero()
    {
        $elicottero = new RichiestaElicottero();

        $elicottero->idevento = Yii::$app->request->post('UtlEvento')['id'];
        $elicottero->idoperatore = Yii::$app->user->identity->operatore->id;

        $evento = UtlEvento::findOne($elicottero->idevento);

        //Nuova richiesta Elicottero invio mail al funzionario responsabile
        $content = "<p>Evento n.protocollo: " . @$evento->num_protocollo . "<br>";
        $content .= "Tipologia: " . @$evento->tipologia->tipologia . "<br>Sotto Tipologia: " . @$evento->sottotipologia->tipologia . "<br>";
        $content .= "Comune: " .  @$evento->comune->comune . "<br>Indirizzo: " . @$evento->indirizzo . "<br>";
        $content .= "Data e ora creazione evento: " . @Yii::$app->formatter->asDatetime($evento->dataora_evento) . "</p>";

        $message = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo(Yii::$app->params['approvazioneElicotteroEmail'])
            ->setSubject('Richiesta Elicottero - Evento ' . @$evento->num_protocollo)
            ->setHtmlBody($content)
            ->send();

        if (!$message || $message == 0) {
            Yii::error("Errore richiesta elicottero invio mail, uso il secondo dispatcher");
            $message = Yii::$app->mailer_throwback->compose()
                ->setFrom(Yii::$app->params['adminEmail'])
                ->setTo(Yii::$app->params['approvazioneElicotteroEmail'])
                ->setSubject('Richiesta Elicottero - Evento ' . @$evento->num_protocollo)
                ->setHtmlBody($content)
                ->send();
        }


        if ($elicottero->load(Yii::$app->request->post()) && $elicottero->save()) {
            $diarioEvento = new ConOperatoreTask();
            $diarioEvento->idfunzione_supporto = 5; //DATI CABLATI NEL DB (che schifo danie)
            $diarioEvento->idtask = 13; //DATI CABLATI NEL DB
            $diarioEvento->idevento = $elicottero->idevento;
            $diarioEvento->note = empty(Yii::$app->request->post('RichiestaElicottero')['id']) ? 'Nuova richiesta Elicottero' : 'Aggiornata scheda Elicottero';
            if (!empty($elicottero->note)) {
                $diarioEvento->note .= " Note: " . $elicottero->note;
            }

            if (!empty(Yii::$app->request->post('RichiestaElicottero')['id'])) {
                $diarioEvento->note .= ($elicottero->engaged) ? " - ingaggiato - " : " - rifiutato - ";
            }
            $diarioEvento->idoperatore = Yii::$app->user->identity->operatore->id;

            if (!($diarioEvento->save())) {
                Yii::$app->session->setFlash('error', "Errore invio dati, verificare i campi");
                return $this->redirect("gestione-evento?idEvento={$elicottero->idevento}&tab=elicottero");
            }

            Yii::$app->session->setFlash('success', "Salvataggio avvenuto correttamente, è stata inviata una email al dirigente");
            return $this->redirect("gestione-evento?idEvento={$elicottero->idevento}&tab=elicottero");
        } else {
            return $this->renderPartial('_form_ric_elicottero', [
                'elicottero' => $elicottero,
                'model' => $evento
            ]);
        }
    }

    /**
     * Send mail for Canadair RichiestaCanadair model.
     * If send is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSendMailCanadair()
    {
        $model = new ComComunicazioni();
        $evento = $this->findModel(Yii::$app->request->post('UtlEvento')['id']);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //Nuova richiesta invio mail
            $content = "<p>Evento n.protocollo: " . @$evento->num_protocollo . "<br>";
            $content .= "Tipologia: " . @$evento->tipologia->tipologia . "<br>Sotto Tipologia: " . @$evento->sottotipologia->tipologia . "<br>";
            $content .= "Comune: " .  @$evento->comune->comune . "<br>Indirizzo: " . @$evento->indirizzo . "<br>";
            $content .= "Data e ora creazione evento: " . @Yii::$app->formatter->asDatetime($evento->dataora_evento) . "</p>";

            if (!empty($model->contenuto)) {
                $content .= "<p><strong>NOTE AGGIUNTIVE</strong><br>{$model->contenuto}</p>";
            }

            $message = Yii::$app->mailer->compose()
                ->setFrom(Yii::$app->params['adminEmail'])
                ->setTo($model->contatto)
                ->setSubject($model->oggetto)
                ->setHtmlBody($content)
                ->send();

            $mdRichiestaCanadair = new RichiestaCanadair();
            $mdRichiestaCanadair->idevento = Yii::$app->request->post('UtlEvento')['id'];
            $mdRichiestaCanadair->idoperatore = Yii::$app->user->identity->operatore->id;
            $mdRichiestaCanadair->idcomunicazione = $model->id;


            if (!($mdRichiestaCanadair->save())) {
                Yii::$app->session->setFlash('error', "Errore invio dati, verificare i campi");
                return $this->redirect("gestione-evento?idEvento={$mdRichiestaCanadair->idevento}&tab=canadair");
            }

            //Salvo dati giornale evento
            $diarioEvento = new ConOperatoreTask();
            $diarioEvento->idfunzione_supporto = 5; //DATI CABLATI NEL DB
            $diarioEvento->idtask = 12; //DATI CABLATI NEL DB
            $diarioEvento->idevento = $mdRichiestaCanadair->idevento;
            $diarioEvento->note = $model->oggetto;
            if (!empty($model->contenuto)) {
                $diarioEvento->note .= " Note: " . $model->contenuto;
            }

            $diarioEvento->idoperatore = Yii::$app->user->identity->operatore->id;

            if (!($diarioEvento->save())) {
                Yii::$app->session->setFlash('error', "Errore invio dati, verificare i campi");
                return $this->redirect("gestione-evento?idEvento={$mdRichiestaCanadair->idevento}&tab=canadair");
            }

            Yii::$app->session->setFlash('success', "Salvataggio avvenuto correttamente");
            return $this->redirect("gestione-evento?idEvento={$mdRichiestaCanadair->idevento}&tab=canadair");
        } else {
            Yii::$app->session->setFlash('error', "Errore invio dati, verificare i campi");
            return $this->redirect("gestione-evento?idEvento={$evento->id}&tab=canadair");
        }
    }

    public function actionCreateSchedaCoc($id_evento)
    {
        // Model mattinale
        $scheda = new EvtSchedaCoc();
        $scheda->id_evento = $id_evento;

        $post_data = Yii::$app->request->post();

        try {
            if (!empty($post_data['EvtSchedaCoc']['data_apertura'])) {
                $data_apertura = \DateTime::createFromFormat('d-m-Y H:i', $post_data['EvtSchedaCoc']['data_apertura']);
                if (!$data_apertura) {
                    throw new \Exception("Data apertura non valida", 1);
                }

                $post_data['EvtSchedaCoc']['data_apertura'] = $data_apertura->format('Y-m-d H:i');
            }

            if (!empty($post_data['EvtSchedaCoc']['data_chiusura'])) {
                $data_chiusura = \DateTime::createFromFormat('d-m-Y H:i', $post_data['EvtSchedaCoc']['data_chiusura']);
                if (!$data_chiusura) {
                    throw new \Exception("Data chiusura non valida", 1);
                }

                $post_data['EvtSchedaCoc']['data_chiusura'] = $data_chiusura->format('Y-m-d H:i');
            }

            if ($scheda->load($post_data) && $scheda->save()) {
                $diarioEvento = new ConOperatoreTask();
                $diarioEvento->idfunzione_supporto = 5; //DATI CABLATI NEL DB (che schifo danie)
                $diarioEvento->idtask = 13; //DATI CABLATI NEL DB
                $diarioEvento->idevento = $scheda->id_evento;
                $diarioEvento->note = "Creazione scheda COC";
                if (!empty($scheda->note)) {
                    $diarioEvento->note .= " Note: " . $scheda->note;
                }

                $diarioEvento->idoperatore = Yii::$app->user->identity->operatore->id;

                if (!($diarioEvento->save())) {
                    Yii::$app->session->setFlash('error', "Errore invio dati, verificare i campi");
                    return $this->redirect("gestione-evento?idEvento={$scheda->id_evento}&tab=coc");
                }


                Yii::$app->session->setFlash('success', "Salvataggio avvenuto correttamente");
                return $this->redirect("gestione-evento?idEvento={$scheda->id_evento}&tab=coc");
            } else {
                Yii::$app->session->setFlash('error', $scheda->getErrors());
                return $this->redirect("gestione-evento?idEvento={$scheda->id_evento}&tab=coc");
            }
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect("gestione-evento?idEvento={$scheda->id_evento}&tab=coc");
        }
    }

    public function actionUpdateSchedaCoc($id, $id_evento)
    {
        // Model mattinale
        $scheda = EvtSchedaCoc::findOne($id);

        if (!$scheda) {
            Yii::$app->session->setFlash('error', "Scheda non trovata");
            return $this->redirect("gestione-evento?idEvento={$scheda->id_evento}");
        }

        $post_data = Yii::$app->request->post();

        if (!empty($post_data['EvtSchedaCoc']['data_apertura'])) {
            $data_apertura = \DateTime::createFromFormat('d-m-Y H:i', $post_data['EvtSchedaCoc']['data_apertura']);
            $post_data['EvtSchedaCoc']['data_apertura'] = $data_apertura->format('Y-m-d H:i');
        }

        if (!empty($post_data['EvtSchedaCoc']['data_chiusura'])) {
            $data_chiusura = \DateTime::createFromFormat('d-m-Y H:i', $post_data['EvtSchedaCoc']['data_chiusura']);
            $post_data['EvtSchedaCoc']['data_chiusura'] = $data_chiusura->format('Y-m-d H:i');
        }

        if ($scheda->load($post_data) && $scheda->save()) {
            $diarioEvento = new ConOperatoreTask();
            $diarioEvento->idfunzione_supporto = 5; //DATI CABLATI NEL DB (che schifo danie)
            $diarioEvento->idtask = 13; //DATI CABLATI NEL DB
            $diarioEvento->idevento = $scheda->id_evento;
            $diarioEvento->note = "Aggiornamento scheda COC";
            if (!empty($scheda->note)) {
                $diarioEvento->note .= " Note: " . $scheda->note;
            }

            $diarioEvento->idoperatore = Yii::$app->user->identity->operatore->id;

            if (!($diarioEvento->save())) {
                Yii::$app->session->setFlash('error', "Errore invio dati, verificare i campi");
                return $this->redirect("gestione-evento?idEvento={$scheda->id_evento}&tab=coc");
            }

            Yii::$app->session->setFlash('success', "Salvataggio avvenuto correttamente");
            return $this->redirect("gestione-evento?idEvento={$scheda->id_evento}&tab=coc");
        } else {
            Yii::$app->session->setFlash('error', $scheda->getErrors());
            return $this->redirect("gestione-evento?idEvento={$scheda->id_evento}&tab=coc");
        }
    }

    public function actionAddSchedaCocDocumento($id_evento, $id_scheda)
    {
        $scheda = EvtSchedaCoc::findOne($id_scheda);

        if (!$scheda || $scheda->id_evento != $id_evento) {
            Yii::$app->session->setFlash('error', "Scheda non trovata");
            return $this->redirect("gestione-evento?idEvento={$scheda->id_evento}");
        }

        $con = new ConSchedaCocDocumenti();

        $file = UploadedFile::getInstance($con, 'attachment');

        try {
            $tipo = UplTipoMedia::find()->where(['descrizione' => 'Allegato segnalazione'])->one();
            if (empty($tipo)) {
                $tipo = new UplTipoMedia();
                $tipo->descrizione = 'Allegato scheda COC';
                $tipo->save();
            }

            $valid_files = ['application/pdf'];

            $media = new UplMedia();
            $media->uploadFile($file, $tipo->id, $valid_files, "File non valido, inserisci un pdf");
            $media->refresh();

            $con->load(Yii::$app->request->post());
            $con->id_upl_media = $media->id;
            $con->id_scheda_coc = $scheda->id;
            if ($con->save()) {
                $diarioEvento = new ConOperatoreTask();
                $diarioEvento->idfunzione_supporto = 5; //DATI CABLATI NEL DB (che schifo danie)
                $diarioEvento->idtask = 13; //DATI CABLATI NEL DB
                $diarioEvento->idevento = $scheda->id_evento;
                $diarioEvento->note = "Inserimento documento scheda COC";
                if (!empty($con->note)) {
                    $diarioEvento->note .= " Note: " . $con->note;
                }

                $diarioEvento->idoperatore = Yii::$app->user->identity->operatore->id;

                if (!($diarioEvento->save())) {
                    Yii::$app->session->setFlash('error', "Errore invio dati, verificare i campi");
                    return $this->redirect("gestione-evento?idEvento={$scheda->id_evento}&tab=coc");
                }


                return $this->redirect("gestione-evento?idEvento={$scheda->id_evento}&tab=coc");
            } else {
                Yii::$app->session->setFlash('error', $con->getErrors());
                return $this->redirect("gestione-evento?idEvento={$scheda->id_evento}&tab=coc");
            }
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect("gestione-evento?idEvento={$scheda->id_evento}&tab=coc");
        }
    }

    /**
     * New task mattinale
     * @param integer $id
     * @return mixed
     */
    public function actionCreateTaskMattinale()
    {
        // Model mattinale
        $taskEvento = new ConOperatoreTask();
        $taskEvento->is_task = true;

        if ($taskEvento->load(Yii::$app->request->post())) {
            $taskEvento->idoperatore = (!empty(Yii::$app->user->identity->operatore)) ? Yii::$app->user->identity->operatore->id : null;
            $taskEvento->manual_flag = 1;
            if (!$taskEvento->save()) {
                throw new \Exception(json_encode($taskEvento->getErrors()), 1);
            }

            $evento = $taskEvento->getEvento()->one();
            if ($evento && $evento->stato == 'Chiuso') {
                return $this->redirect("view?id={$evento->id}");
            } else {
                return $this->redirect("gestione-evento?idEvento={$taskEvento->idevento}");
            }
        } else {
            $evento = $this->findModel($taskEvento->idevento);
            return $this->renderPartial('_form_create_task', [
                'evento' => $evento,
                'model' => $taskEvento
            ]);
        }
    }

    /**
     * Valida la creazione nuova attività
     * @return [type] [description]
     */
    public function actionValidateCreateTaskMattinale()
    {
        $post = Yii::$app->request->post();

        if (!empty($post)) :
            Yii::$app->response->format = Response::FORMAT_JSON;

            $taskEvento = new ConOperatoreTask();
            $taskEvento->is_task = true;
            $taskEvento->load($post);

            return ActiveForm::validate($taskEvento);
        endif;
    }

    /**
     * Update dos
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateDos($id)
    {
        $dos = RichiestaDos::findOne($id);
        $evento = UtlEvento::findOne($dos->idevento);

        $dos->edited = 1;
        if ($dos->load(Yii::$app->request->post()) && $dos->save()) {
            $diarioEvento = new ConOperatoreTask();
            $diarioEvento->idfunzione_supporto = 5; //DATI CABLATI NEL DB
            $diarioEvento->idtask = 6; //DATI CABLATI NEL DB
            $diarioEvento->idevento = $dos->idevento;
            $diarioEvento->note = 'Aggiornata scheda DOS ';
            if ($dos->codicedos) {
                $diarioEvento->note .= '- Codice DOS: ' . $dos->codicedos;
            }
            if ($dos->motivo_rifiuto) {
                $diarioEvento->note .= ' - ' . $dos->motivo_rifiuto;
            }
            $diarioEvento->idoperatore = Yii::$app->user->identity->operatore->id;

            if (!($diarioEvento->save())) {
                Yii::$app->session->setFlash('error', "Errore invio dati, verificare i campi");
                return $this->redirect("gestione-evento?idEvento={$dos->idevento}&tab=dos");
            }

            Yii::$app->session->setFlash('success', "Salvataggio avvenuto correttamente");
            return $this->redirect("gestione-evento?idEvento={$dos->idevento}&tab=dos");
        } else {
            return $this->renderPartial('_form_update_dos', [
                'dos' => $dos
            ]);
        }
    }

    /**
     * Update dos
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateElicottero($id)
    {
        $elicottero = RichiestaElicottero::findOne($id);
        $evento = UtlEvento::findOne($elicottero->idevento);

        try {
            if ($elicottero->deleted == 1 && Yii::$app->request->method == 'POST') {
                throw new Exception("Richiesta annullata, non si può modificare", 1);
            }

            $is_dirigente = Yii::$app->user->can('updateRichiestaElicottero');
            $is_giving_feedback = ($elicottero->edited && $elicottero->edited == 1) ? true : false;

            $is_this_engaged = false;
            if ((
                    isset(Yii::$app->request->post('RichiestaElicottero')['engaged']) &&
                    Yii::$app->request->post('RichiestaElicottero')['engaged']
                ) || $elicottero->engaged
            ) {
                $is_this_engaged = true;
            }

            if ($is_dirigente) {
                $elicottero->scenario = RichiestaElicottero::SCENARIO_UPDATE;

                if (Yii::$app->request->method == 'POST' &&
                    (
                        (isset(Yii::$app->request->post('RichiestaElicottero')['engaged']) && Yii::$app->request->post('RichiestaElicottero')['engaged']) || $elicottero->engaged
                    ) &&
                    empty(Yii::$app->request->post('RichiestaElicottero')['id_elicottero'])
                ) {
                    throw new \Exception("Seleziona un elicottero", 1);
                }


                if (Yii::$app->request->isPost) {
                    $elicottero->edited = 1; // per fix richieste
                }
            } else {
                // scenario aggiornamento parziale
                $elicottero->scenario = RichiestaElicottero::SCENARIO_PARTIAL_UPDATE;
            }

            $data = Yii::$app->request->post();

            if (!empty($data['RichiestaElicottero']['date']) ||
                !empty($data['RichiestaElicottero']['hour']) ||
                !empty($data['RichiestaElicottero']['minutes'])
            ) {
                $data['RichiestaElicottero']['hour'] = $data['RichiestaElicottero']['hour'] == 0 ? "0" : $data['RichiestaElicottero']['hour'];

                $data['RichiestaElicottero']['minutes'] = $data['RichiestaElicottero']['minutes'] == 0 ? "00" : $data['RichiestaElicottero']['minutes'];

                $save_data = true;
                if (empty($data['RichiestaElicottero']['date']) ||
                    empty($data['RichiestaElicottero']['hour']) ||
                    empty($data['RichiestaElicottero']['minutes'])
                ) {
                    if ($is_this_engaged) {
                        throw new \Exception("Inserisci la data completa di data, ore e minuti", 1);
                    } else {
                        $save_data = false;
                    }
                }

                $ore = ($data['RichiestaElicottero']['hour'] < 10) ? "0" . intval($data['RichiestaElicottero']['hour']) : "" . $data['RichiestaElicottero']['hour'];
                $minuti = ($data['RichiestaElicottero']['minutes'] < 10) ? "0" . intval($data['RichiestaElicottero']['minutes']) : "" . $data['RichiestaElicottero']['minutes'];

                if ($save_data) {
                    $dt = \DateTime::createFromFormat('d-m-Y H:i', $data['RichiestaElicottero']['date'] . " " . $ore . ":" . $minuti);
                    $data['RichiestaElicottero']['dataora_decollo'] = $dt->format('Y-m-d H:i');
                }
            }

            if (!empty($data['RichiestaElicottero']['date_arrivo_stimato']) ||
                !empty($data['RichiestaElicottero']['hour_arrivo_stimato']) ||
                !empty($data['RichiestaElicottero']['minutes_arrivo_stimato'])
            ) {
                $data['RichiestaElicottero']['hour_arrivo_stimato'] = $data['RichiestaElicottero']['hour_arrivo_stimato'] == 0 ? "0" : $data['RichiestaElicottero']['hour_arrivo_stimato'];

                $data['RichiestaElicottero']['minutes_arrivo_stimato'] = $data['RichiestaElicottero']['minutes_arrivo_stimato'] == 0 ? "00" : $data['RichiestaElicottero']['minutes_arrivo_stimato'];

                $save_data = true;
                if (empty($data['RichiestaElicottero']['date_arrivo_stimato']) ||
                    empty($data['RichiestaElicottero']['hour_arrivo_stimato']) ||
                    empty($data['RichiestaElicottero']['minutes_arrivo_stimato'])
                ) {
                    if ($is_this_engaged) {
                        throw new \Exception("Inserisci la data di arrivo completa di data, ore e minuti", 1);
                    } else {
                        $save_data = false;
                    }
                }

                $ore = ($data['RichiestaElicottero']['hour_arrivo_stimato'] < 10) ? "0" . intval($data['RichiestaElicottero']['hour_arrivo_stimato']) : "" . $data['RichiestaElicottero']['hour_arrivo_stimato'];
                $minuti = ($data['RichiestaElicottero']['minutes_arrivo_stimato'] < 10) ? "0" . intval($data['RichiestaElicottero']['minutes_arrivo_stimato']) : "" . $data['RichiestaElicottero']['minutes_arrivo_stimato'];
                //$dt = \DateTime::createFromFormat('d-m-Y H:i', $data['RichiestaElicottero']['dataora_decollo']);
                if ($save_data) {
                    $dt = \DateTime::createFromFormat('d-m-Y H:i', $data['RichiestaElicottero']['date_arrivo_stimato'] . " " . $ore . ":" . $minuti);
                    $data['RichiestaElicottero']['dataora_arrivo_stimato'] = $dt->format('Y-m-d H:i');
                }
            }

            if (!empty($data['RichiestaElicottero']['date_atterraggio']) ||
                !empty($data['RichiestaElicottero']['hour_atterraggio']) ||
                !empty($data['RichiestaElicottero']['minutes_atterraggio'])
            ) {
                $data['RichiestaElicottero']['hour_atterraggio'] = $data['RichiestaElicottero']['hour_atterraggio'] == 0 ? "0" : $data['RichiestaElicottero']['hour_atterraggio'];

                $data['RichiestaElicottero']['minutes_atterraggio'] = $data['RichiestaElicottero']['minutes_atterraggio'] == 0 ? "00" : $data['RichiestaElicottero']['minutes_atterraggio'];

                $save_data = true;
                if (empty($data['RichiestaElicottero']['date_atterraggio']) ||
                    empty($data['RichiestaElicottero']['hour_atterraggio']) ||
                    empty($data['RichiestaElicottero']['minutes_atterraggio'])
                ) {
                    if ($is_this_engaged) {
                        throw new \Exception("Inserisci la data di arrivo completa di data, ore e minuti", 1);
                    } else {
                        $save_data = false;
                    }
                }

                $ore = ($data['RichiestaElicottero']['hour_atterraggio'] < 10) ? "0" . intval($data['RichiestaElicottero']['hour_atterraggio']) : "" . $data['RichiestaElicottero']['hour_atterraggio'];
                $minuti = ($data['RichiestaElicottero']['minutes_atterraggio'] < 10) ? "0" . intval($data['RichiestaElicottero']['minutes_atterraggio']) : "" . $data['RichiestaElicottero']['minutes_atterraggio'];

                if ($save_data) {
                    $dt = \DateTime::createFromFormat('d-m-Y H:i', $data['RichiestaElicottero']['date_atterraggio'] . " " . $ore . ":" . $minuti);
                    $data['RichiestaElicottero']['dataora_atterraggio'] = $dt->format('Y-m-d H:i');
                }
            }


            $evento = UtlEvento::findOne($elicottero->idevento);
            $lastUpdateDate = $elicottero->updated_at;


            if ($elicottero->load($data) && $elicottero->save()) {
                if ($is_dirigente) {
                    //In caso di ingaggio e se presente codice eleicottero invio mail
                    if (!empty($elicottero->engaged)) {
                        $subject = "Comunicazione autorizzazione intervento Elicottero";
                        $content = "<p>Sigla: " . @$elicottero->codice_elicottero . "<br>";
                        $content .= "Missione: " . @$elicottero->tipo_intervento . "<br>";
                        $content .= "Zona di impiego: " .  @$evento->comune->comune . "<br>Indirizzo: " . @$evento->indirizzo . "<br>";
                        $content .= "Data e ora decollo: " . @Yii::$app->formatter->asDatetime($lastUpdateDate) . "</p>";
                    } else {
                        $subject = "Comunicazione diniego autorizzazione intervento Elicottero";
                        $content = "<p>Sigla: " . @$elicottero->codice_elicottero . "<br>";
                        $content .= "Missione: " . @$elicottero->tipo_intervento . "<br>";
                        $content .= "Zona di impiego: " .  @$evento->comune->comune . "<br>Indirizzo: " . @$evento->indirizzo . "<br>";
                        $content .= "Azione: Diniego richiesta</p>";
                    }

                    if (!$is_giving_feedback) {
                        $message = Yii::$app->mailer->compose()
                            ->setFrom(Yii::$app->params['adminEmail'])
                            ->setTo(Yii::$app->params['feedbackApprovazioneElicotteroMail'])
                            ->setSubject($subject)
                            ->setHtmlBody($content)
                            ->send();
                    }
                }


                $diarioEvento = new ConOperatoreTask();
                $diarioEvento->idfunzione_supporto = 5; //DATI CABLATI NEL DB
                $diarioEvento->idtask = 13; //DATI CABLATI NEL DB
                $diarioEvento->idevento = $elicottero->idevento;
                $diarioEvento->note = 'Aggiornata scheda Elicottero';

                if ($elicottero->codice_elicottero) {
                    $diarioEvento->note .= ' - Codice Richiesta Elicottero: ' . $elicottero->codice_elicottero;
                }
                if ($elicottero->motivo_rifiuto) {
                    $diarioEvento->note .= ' - ' . $elicottero->motivo_rifiuto;
                }
                if (!empty($elicottero->note)) {
                    $diarioEvento->note .= " Note: " . $elicottero->note;
                }
                if ($is_dirigente) {
                    $diarioEvento->note .= ($elicottero->engaged) ? " - ingaggiato - " : " - rifiutato - ";
                }

                $diarioEvento->idoperatore = Yii::$app->user->identity->operatore->id;

                if (!($diarioEvento->save())) {
                    Yii::$app->session->setFlash('error', "Errore invio dati, verificare i campi");
                    return $this->redirect("gestione-evento?idEvento={$elicottero->idevento}&tab=elicottero");
                }

                return $this->redirect("gestione-evento?idEvento={$evento->id}&tab=elicottero");
            } else {
                return $this->renderPartial('_form_ric_elicottero', [
                    'model' => $elicottero,
                    'evento' => $evento
                ]);
            }
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect("gestione-evento?idEvento={$elicottero->idevento}&tab=elicottero");
        }
    }

    public function actionAnnullaRichiestaElicottero($id)
    {
        $elicottero = RichiestaElicottero::findOne($id);
        if (!$elicottero) {
            throw new \yii\web\HttpException(422, "Richiesta non trovata");
        }

        $elicottero->deleted = 1;
        if (!$elicottero->save()) {
            Yii::$app->session->setFlash('error', $elicottero->getErrors());
        }

        return $this->redirect("gestione-evento?idEvento={$elicottero->idevento}&tab=elicottero");
    }

    public function actionSchedaCoau($id_evento, $id_richiesta)
    {
        $evento = UtlEvento::findOne($id_evento);
        $richiesta = RichiestaElicottero::findOne($id_richiesta);

        $content = $this->renderPartial('_partial_scheda_coau.php', [
            'evento' => $evento,
            'richiesta' => $richiesta,
            'is_applicativo' => false
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Richiesta di accreditamento'],
            'methods' => []
        ]);

        return $pdf->render();
    }

    public function actionSendRiepilogoCoau()
    {
        $id_evento = Yii::$app->request->post('id_evento');
        $id_richiesta = Yii::$app->request->post('id_richiesta');

        $evento = UtlEvento::findOne($id_evento);
        if (!$evento) {
            throw new NotFoundHttpException();
        }

        $richiesta = RichiestaElicottero::findOne($id_richiesta);
        if (!$richiesta) {
            throw new NotFoundHttpException();
        }

        if (empty(@Yii::$app->request->post('RichiestaElicottero')['id_anagrafica_funzionario'])) {
            Yii::$app->session->setFlash('error', 'Inserisci il funzionario');
            return $this->redirect("gestione-evento?idEvento={$evento->id}&tab=elicottero");
        }

        $richiesta->scenario = \common\models\RichiestaElicottero::SCENARIO_SEND_COAU;

        if (empty($richiesta->id_anagrafica_funzionario)) {
            $richiesta->id_anagrafica_funzionario = Yii::$app->request->post('RichiestaElicottero')['id_anagrafica_funzionario'];
            $richiesta->load(Yii::$app->request->post());
            if (!$richiesta->save()) {
                Yii::$app->session->setFlash('error', 'Errore aggiornamento funzionario ' . json_encode($richiesta->getErrors));
                return $this->redirect("gestione-evento?idEvento={$evento->id}&tab=elicottero");
            }
        }

        $content = $this->renderPartial('_partial_scheda_coau.php', [
            'evento' => $evento,
            'richiesta' => $richiesta,
            'is_applicativo' => false
        ]);

        $base_path = Yii::getAlias('@backend');
        if (!is_dir("{$base_path}/uploads")) {
            mkdir("{$base_path}/uploads");
        }
        if (!is_dir("{$base_path}/uploads/coau")) {
            mkdir("{$base_path}/uploads/coau");
        }
        if (!is_dir("{$base_path}/uploads/coau/" . $id_evento)) {
            mkdir("{$base_path}/uploads/coau/" . $id_evento);
        }

        $_name = time() . '.pdf';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_FILE,
            'filename' => $base_path . '/uploads/coau/' . $id_evento . '/' . $_name,
            'content' => $content,
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Richiesta di accreditamento'],
            'methods' => []
        ]);

        $pdf->render();

        $subject = 'COMUNICAZIONE IMPIEGO AEREOMOBILI REGIONALI';
        $message = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo(Yii::$app->params['coauMail'])
            ->setSubject($subject);

        if (!empty(Yii::$app->params['coauMailCC'])) {
            $message->setCc(Yii::$app->params['coauMailCC']);
        }

        $message->setHtmlBody($content)
            ->attach($base_path . '/uploads/coau/' . $id_evento . '/' . $_name)
            ->send();
        if (!$message) {
            Yii::$app->session->setFlash('error', 'Errore invio mail coau');
            return $this->redirect("gestione-evento?idEvento={$evento->id}&tab=elicottero");
        } else {
            Yii::$app->session->setFlash('success', 'Scheda inviata');
            return $this->redirect("gestione-evento?idEvento={$evento->id}&tab=elicottero");
        }
    }

    /**
     * Update dos
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateCanadair($id)
    {
        $canadair = RichiestaCanadair::findOne($id);
        $evento = UtlEvento::findOne($canadair->idevento);

        $canadair->edited = 1; // per fix richieste
        if ($canadair->load(Yii::$app->request->post()) && $canadair->save()) {
            $diarioEvento = new ConOperatoreTask();
            $diarioEvento->idfunzione_supporto = 5; //DATI CABLATI NEL DB
            $diarioEvento->idtask = 12; //DATI CABLATI NEL DB
            $diarioEvento->idevento = $canadair->idevento;
            $diarioEvento->note = 'Aggiornata scheda Canadair ';
            $diarioEvento->idoperatore = Yii::$app->user->identity->operatore->id;

            if (!empty($canadair->motivo_rifiuto)) {
                $diarioEvento->note .= " Note: " . $canadair->motivo_rifiuto;
            }
            $diarioEvento->note .= ($canadair->engaged) ? " - ingaggiato - " : " - rifiutato - ";

            if (!($diarioEvento->save())) {
                Yii::$app->session->setFlash('error', "Errore invio dati, verificare i campi");
                return $this->redirect("gestione-evento?idEvento={$canadair->idevento}&tab=canadair");
            }

            Yii::$app->session->setFlash('success', "Salvataggio avvenuto correttamente");
            return $this->redirect("gestione-evento?idEvento={$canadair->idevento}&tab=canadair");
        } else {
            return $this->renderPartial('_form_update_canadair', [
                'canadair' => $canadair
            ]);
        }
    }



    /**
     * Assign event to operator
     * @param integer $id
     * @return mixed
     */
    public function actionAssignEvent()
    {
        $data = Yii::$app->request->post();
        $eventoOperatore = new ConOperatoreEvento();

        $eventoOperatore->idoperatore = $data['idoperatore'];
        $eventoOperatore->idevento = $data['idevento'];
        $eventoOperatore->save(false);

        return $this->redirect(['index']);
    }

    /**
     * Remove event to operator
     * @param integer $id
     * @return mixed
     */
    public function actionRemoveEvent()
    {
        $data = Yii::$app->request->post();
        $eventoOperatore = ConOperatoreEvento::find()->where(['idoperatore' => $data['idoperatore'], 'idevento' => $data['idevento']])->one();

        if (!empty($eventoOperatore)) {
            $eventoOperatore->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * Public event
     * @param integer $id
     * @return mixed
     */
    public function actionPublicEvent($id, $public)
    {
        $model = $this->findModel($id);
        $model->is_public = $public;
        $model->save(false);


        return $this->redirect(['index']);
    }

    /**
     * Create task
     * @param integer $idEvento
     * @return mixed
     */
    public function actionGestioneEvento($idEvento)
    {
        $evento = UtlEvento::findOne($idEvento);


        $tasksSearchModel = new ConOperatoreTaskSearch();
        $tasksDataProvider = $tasksSearchModel->searchByEvento($idEvento, Yii::$app->request->queryParams);

        $ingaggiSearchModel = new UtlIngaggioSearch();
        $ingaggiDataProvider = $ingaggiSearchModel->searchByEvento($idEvento, Yii::$app->request->queryParams);

        $dosSearchModel = new RichiestaDosSearch();
        $dosDataProvider = $dosSearchModel->searchByEvento($idEvento, Yii::$app->request->queryParams);

        $ricElicotteroSearchModel = new RichiestaElicotteroSearch();
        $ricElicotteroDataProvider = $ricElicotteroSearchModel->searchByEvento($idEvento, Yii::$app->request->queryParams);

        $ricCanadairSearchModel = new RichiestaCanadairSearch();
        $ricCanadairDataProvider = $ricCanadairSearchModel->searchByEvento($idEvento, Yii::$app->request->queryParams);

        return $this->render('gestione-evento', [
            'tasksSearchModel' => $tasksSearchModel,
            'tasksDataProvider' => $tasksDataProvider,
            'ricElicotteroSearchModel' => $ricElicotteroSearchModel,
            'ricElicotteroDataProvider' => $ricElicotteroDataProvider,
            'dosSearchModel' => $dosSearchModel,
            'dosDataProvider' => $dosDataProvider,
            'ricCanadairSearchModel' => $ricCanadairSearchModel,
            'ricCanadairDataProvider' => $ricCanadairDataProvider,
            'utente' => Yii::$app->user->identity->operatore,
            'evento' => $evento,
            'ingaggiSearchModel' => $ingaggiSearchModel,
            'ingaggiDataProvider' => $ingaggiDataProvider,
            'geoQueries' => $this->getGeoQueries($evento)
        ]);
    }

    /**
     * Update task
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateTask($id)
    {
        $taskEvento = ConOperatoreTask::findOne($id);
        $taskEvento->id = $id;
        $taskEvento->idoperatore = Yii::$app->user->identity->operatore->id;
        $evento = UtlEvento::findOne(['id' => $taskEvento->idevento]);

        if ($taskEvento->load(Yii::$app->request->post()) && $taskEvento->save()) {
            return $this->redirect(['index', 'idEvento' => $taskEvento->idevento]);
        } else {
            return $this->render('update-task', [
                'model' => $taskEvento,
                'utente' => Yii::$app->user->identity->operatore,
                'evento' => $evento
            ]);
        }
    }

    /**
     * Creates a new UtlEvento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UtlEvento();
        $model->scenario = UtlEvento::SCENARIO_CREATE;
        if (Yii::$app->request->get('idparent')) {
            $model->idparent = Yii::$app->request->get('idparent');
        }
        $tipoItems = ArrayHelper::map(UtlTipologia::find()
            ->where(['idparent' => null])
            ->andWhere(
                '(valido_dal is null OR valido_dal <= CURRENT_DATE) AND (valido_al is null OR valido_al >= CURRENT_DATE)'
            )
            ->orderBy(['tipologia' => SORT_ASC])
            ->all(), 'id', 'tipologia');
        $query = ConOperatoreTask::find()->with('operatore', 'task', 'funzioneSupporto')->where(['idevento' => $model->id]);
        $tasksSearchModel = new ConOperatoreTaskSearch();
        $tasksDataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $segnalazioniSearchModel = new UtlSegnalazioneSearch();
        $segnalazioniDataProvider = $segnalazioniSearchModel->searchByEvento("0", Yii::$app->request->queryParams);

        $ingaggiSearchModel = new UtlIngaggioSearch();
        $ingaggiDataProvider = $ingaggiSearchModel->searchByEvento("0", Yii::$app->request->queryParams);

        //Variabile che mostra/nasconde i campi lat e lon nel form in caso di problemi di geolocalizzazione
        $showLatLon = false;

        if ($model->load(Yii::$app->request->post())) {
            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();
            try {
                $params = Yii::$app->request->post('UtlEvento');
                if (empty($model->idcomune)) {
                    throw new Exception("Seleziona il comune");
                }
                if (empty($model->lat) || empty($model->lon)) {
                    throw new Exception("Inserisci le coordinate selezionando un indirizzo/toponimo o manualmente");
                }


                $evento_address = Yii::$app->request->post('UtlEvento')['address_type'];
                $post_evento = Yii::$app->request->post('UtlEvento');


                if (!\common\utils\GeometryFunctions::verifyLatLonInComune($model->lat, $model->lon, $model->idcomune)) {
                    $comune_corretto = \common\utils\GeometryFunctions::getComuneByLatLon($model->lat, $model->lon);
                    if ($comune_corretto) {
                        throw new Exception("Le coordinate inserite rientrano nel comune di " . $comune_corretto['comune']);
                    } else {
                        throw new Exception("Le coordinate inserite non rientrano nella geometria del comune selezionato");
                    }
                }

                switch ($evento_address) {
                    case 1:
                        if (empty($model->address) || empty($model->civico)) {
                            throw new \Exception("Inserisci indirizzo e seleziona il numero civico", 1);
                        }
                        // il cap è semi-virtuale, serve solo come check a posteriori per vedere se ha preso correttamente l'indirizzo
                        if (empty($model->cap)) {
                            throw new \Exception("L'indirizzo selezionato non è stato preso, riprova", 1);
                        }

                        $model->luogo = '';
                        $model->indirizzo = $model->address . ' ' . $model->civico . ' ' . $model->cap;

                        break;
                    case 2:
                        if (empty($model->google_address)) {
                            throw new Exception("Inserisci e seleziona l'indirizzo con l'autocomplete di google", 1);
                        }

                        $model->indirizzo = '';
                        $model->luogo = $model->google_address;

                        break;
                    case 3:
                        if (empty($model->manual_address)) {
                            throw new Exception("Inserisci l'indirizzo", 1);
                        }

                        $model->luogo = '';
                        $model->indirizzo = $model->manual_address;

                        break;
                    case 4:
                        if (empty($model->toponimo_address)) {
                            throw new Exception("Seleziona il toponimo dalla tendina", 1);
                        }

                        $model->luogo = $model->toponimo_address;
                        $model->indirizzo = '';

                        break;
                }



                if (!$model->save()) {
                    throw new Exception('Errore salvataggio Evento. Controllare i dati', 500);
                } else {
                    $segnalazioniDataProvider = $segnalazioniSearchModel->searchByEvento($model->id, Yii::$app->request->queryParams);
                }


                // Salvo gli eventuali extra evento
                if (isset(Yii::$app->request->post('UtlEvento')['extras'])) {
                    $extras = Yii::$app->request->post('UtlEvento')['extras'];
                    $extrasInfoArray = Yii::$app->request->post('UtlEventoExtraInfo');
                    if (!empty($extras)) {
                        $mdExtras = UtlExtraSegnalazione::find()->where(['id' => $extras])->all();
                        foreach ($mdExtras as $extra) {
                            $model->link('extras', $extra);

                            $extraInfo = ConEventoExtra::find()->where(['idevento' => $model->id, 'idextra' => $extra->id])->one();

                            $data = [];
                            if (isset($extrasInfoArray[$extra->id])) {
                                $data['ConEventoExtra'] = $extrasInfoArray[$extra->id];
                                $extraInfo->load($data);
                                $extraInfo->save();
                            }
                        }
                    }
                }

                \common\models\cap\CapExposedMessage::generateFromEvent($model, 'Create');
                if (!empty($model->idparent)) {
                    \common\models\cap\CapExposedMessage::generateFromEvent(UtlEvento::findOne($model->idparent), 'Added fronte');
                }

                $dbTrans->commit();
            } catch (Exception $e) {
                $dbTrans->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());

                return $this->render('create', [
                    'model' => $model,
                    'tipoItems' => $tipoItems,
                    'tasksSearchModel' => $tasksSearchModel,
                    'tasksDataProvider' => $tasksDataProvider,
                    'segnalazioniSearchModel' => $segnalazioniSearchModel,
                    'segnalazioniDataProvider' => $segnalazioniDataProvider,
                    'ingaggiSearchModel' => $ingaggiSearchModel,
                    'ingaggiDataProvider' => $ingaggiDataProvider,
                    'showLatLon' => $showLatLon
                ]);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'tipoItems' => $tipoItems,
                'tasksSearchModel' => $tasksSearchModel,
                'tasksDataProvider' => $tasksDataProvider,
                'segnalazioniSearchModel' => $segnalazioniSearchModel,
                'segnalazioniDataProvider' => $segnalazioniDataProvider,
                'ingaggiSearchModel' => $ingaggiSearchModel,
                'ingaggiDataProvider' => $ingaggiDataProvider,
                'showLatLon' => $showLatLon
            ]);
        }
    }


    /**
     * Assegna evento a sala esterna
     * If sala esterna exist send message CAP
     * @param integer $id
     * @return mixed
     */
    public function actionAssegnaSalaOperativaEsterna($id)
    {
        $model = $this->findModel($id);
        $postData = Yii::$app->request->post();

        if (!empty($postData)) {
            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();
            try {
                // Se postdata non è vuoto cancello le sale operative
                $model->unlinkAll('saleOperativeEsterne', true);

                // Associo sala operativa e chiamo il servizio per la creazione della seganalzione
                if (!empty($postData['UtlEvento']['saleOperativeEsterne'])) {
                    $salaEsterna = SalaOperativaEsterna::findOne($postData['UtlEvento']['saleOperativeEsterne']);
                    $model->link('saleOperativeEsterne', $salaEsterna);

                    // Recupero il messaggio CAP originale collegato all'evento
                    $capExposedMessage = CapExposedMessage::findOne(['id_evento' => $model->id, 'message_progr' => 0]);
                    if (!$capExposedMessage) {
                        throw new \Exception('Nessun messaggio cap trovato per questo evento');
                    }

                    // $capMessage = CapMessages::findOne(['identifier' => $capExposedMessage->identifier]);
                    // if (!$capMessage) throw new \Exception('Processo di sincronizzazione dei messaggi CAP in corso, riprovare tra poco');

                    $client = new Client();
                    $resToken = $client->request('POST', $salaEsterna->api_auth_url, [
                        'json' => ['username' => $salaEsterna->api_username, 'password' => $salaEsterna->api_password]
                    ]);

                    if ($resToken->getReasonPhrase() == 'OK') {
                        // Get response and parse token
                        $bodyToken = $resToken->getBody()->getContents();
                        $parsedBodyToken = json_decode($bodyToken, true);
                        $token = $parsedBodyToken['token'];
                        //$tokenDecoded = Yii::$app->jwt->getParser()->parse((string) $token);

                        $response = $client->request('POST', $salaEsterna->url_endpoint, [
                            'json' => [
                                'lat' => $model->lat,
                                'lon' => $model->lon,
                                'tipologia_evento' => $model->tipologia_evento,
                                'fromCapMessage' => true,
                                'cap_message_identifier' => $capExposedMessage->identifier
                            ],
                            'headers' => [
                                'Authorization' => "Bearer {$token}"
                            ]
                        ]);

                        if ($response->getReasonPhrase() == 'OK') {
                            Yii::$app->session->setFlash('success', 'Evento assegnato correttamente alla sala operativa selezionata');
                        } else {
                            throw new \Exception('Error api');
                        }
                    }
                }

                $dbTrans->commit();
                return $this->redirect('index');
            } catch (Exception $e) {
                $dbTrans->rollBack();
                //throw $e;

                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        //$model->unlinkAll('saleOperativeEsterne', true);
        return $this->render('assegna-sala-operativa-esterna', [
            'model' => $model,
        ]);
    }


    /**
     * Updates an existing UtlEvento model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = UtlEvento::SCENARIO_UPDATE;
        $old_stato = $model->stato;
        $old_parent = $model->idparent;
        $tipoItems = ArrayHelper::map(UtlTipologia::find()->where(['idparent' => null])
            ->andWhere(
                '(valido_dal is null OR valido_dal <= CURRENT_DATE) AND (valido_al is null OR valido_al >= CURRENT_DATE)'
            )
            ->all(), 'id', 'tipologia');


        $tasksSearchModel = new ConOperatoreTaskSearch();
        $tasksDataProvider = $tasksSearchModel->searchByEvento($id, Yii::$app->request->queryParams);

        $segnalazioniSearchModel = new UtlSegnalazioneSearch();
        $segnalazioniDataProvider = $segnalazioniSearchModel->searchByEvento($id, Yii::$app->request->queryParams);

        $ingaggiSearchModel = new UtlIngaggioSearch();
        $ingaggiDataProvider = $ingaggiSearchModel->searchByEvento($id, Yii::$app->request->queryParams);

        $posted = Yii::$app->request->post();

        //Variabile che mostra/nasconde i campi lat e lon nel form in caso di problemi di geolocalizzazione
        $showLatLon = false;

        $changed_position = false;
        if ((isset($posted['UtlEvento']['lat']) && $model->lat != $posted['UtlEvento']['lat']) ||
            (isset($posted['UtlEvento']['lon']) && $model->lon != $posted['UtlEvento']['lon'])
        ) {
            $changed_position = true;
            $old_lat = $model->lat;
            $old_lon = $model->lon;
        }

        if ($model->load(Yii::$app->request->post())) {
            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();
            try {
                /**
                 * Verifico permesso se sta chiudendo
                 */

                if (isset($posted['UtlEvento']['stato']) && $posted['UtlEvento']['stato'] == 'Chiuso' &&
                    $posted['UtlEvento']['stato'] != $old_stato
                ) :
                    if (!Yii::$app->user->can('closeEvento')) {
                        throw new Exception('Non puoi chiudere l\'evento', 401);
                    }

                    $fronti = UtlEvento::find()
                        ->where(['!=', 'stato', 'Chiuso'])
                        ->andWhere(['idparent' => $model->id])->all();

                    $n_f = count($fronti);
                    if ($n_f > 0) :
                        // sta chiudendo un evento con fronti aperti
                        $base_str = ($n_f == 1) ? "Stai cercando di chiudere l'evento con un fronte aperto: " : "Stai cercando di chiudere l'evento con " . $n_f . " fronti aperti: ";

                        if ($n_f > 1) :
                            $arr_fronti = [];
                            foreach ($fronti as $fronte) :
                                $arr_fronti[] = $fronte->num_protocollo;
                            endforeach;
                            throw new Exception($base_str . implode(", ", $arr_fronti), 403);
                        else :
                            throw new Exception($base_str . $fronti[0]->num_protocollo, 403);
                        endif;
                    endif;
                endif;

                if (isset($posted['UtlEvento']['stato']) && $old_stato == 'Chiuso' &&
                    $posted['UtlEvento']['stato'] != $old_stato
                ) :
                    if (!Yii::$app->user->can('openClosedEvento')) {
                        throw new Exception('Non puoi riaprire l\'evento', 401);
                    }
                endif;

                if ($changed_position) :
                    // modifica posizione evento
                    $diarioEvento = new ConOperatoreTask();
                    //$diarioEvento->idfunzione_supporto = 1; //DATI CABLATI NEL DB
                    $diarioEvento->idtask = 13; //DATI CABLATI NEL DB
                    $diarioEvento->idevento = $model->id;
                    $diarioEvento->note = "Modifica posizione evento da " . $old_lat . " - " . $old_lon . " a " . $posted['UtlEvento']['lat'] . " - " . $posted['UtlEvento']['lon'];
                    $diarioEvento->idoperatore = Yii::$app->user->identity->operatore->id;

                    if (!($diarioEvento->save())) {
                        throw new \Exception(json_encode($diarioEvento->getErrors()), 1);
                    }
                endif;

                if ($model->stato == 'Non gestito') :
                    $model->stato = 'In gestione';
                    $model->dataora_gestione = date('Y-m-d H:i:s');
                    $model->save();
                endif;

                $params = Yii::$app->request->post('UtlEvento');

                if (empty($model->idcomune)) {
                    throw new Exception("Seleziona il comune");
                }
                if (empty($model->lat) || empty($model->lon)) {
                    throw new Exception("Inserisci le coordinate selezionando un indirizzo/toponimo o manualmente");
                }


                $evento_address = Yii::$app->request->post('UtlEvento')['address_type'];
                $post_evento = Yii::$app->request->post('UtlEvento');


                if (!\common\utils\GeometryFunctions::verifyLatLonInComune($model->lat, $model->lon, $model->idcomune)) {
                    $comune_corretto = \common\utils\GeometryFunctions::getComuneByLatLon($model->lat, $model->lon);
                    if ($comune_corretto) {
                        throw new Exception("Le coordinate inserite rientrano nel comune di " . $comune_corretto['comune']);
                    } else {
                        throw new Exception("Le coordinate inserite non rientrano nella geometria del comune selezionato");
                    }
                }

                switch ($evento_address) {
                    case 1:
                        if (empty($model->address) || empty($model->civico)) {
                            throw new \Exception("Inserisci indirizzo e seleziona il numero civico", 1);
                        }
                        // il cap è semi-virtuale, serve solo come check a posteriori per vedere se ha preso correttamente l'indirizzo
                        if (empty($model->cap)) {
                            throw new \Exception("L'indirizzo selezionato non è stato preso, riprova", 1);
                        }

                        $model->luogo = '';
                        $model->indirizzo = $model->address . ' ' . $model->civico . ' ' . $model->cap;

                        break;
                    case 2:
                        if (empty($model->google_address)) {
                            throw new Exception("Inserisci e seleziona l'indirizzo con l'autocomplete di google", 1);
                        }

                        $model->indirizzo = '';
                        $model->luogo = $model->google_address;

                        break;
                    case 3:
                        if (empty($model->manual_address)) {
                            throw new Exception("Inserisci l'indirizzo", 1);
                        }

                        $model->luogo = '';
                        $model->indirizzo = $model->manual_address;

                        break;
                    case 4:
                        if (empty($model->toponimo_address)) {
                            throw new Exception("Seleziona il toponimo dalla tendina", 1);
                        }

                        $model->luogo = $model->toponimo_address;
                        $model->indirizzo = '';

                        break;
                }

                if ($model->stato == 'Chiuso') {
                    $unclosed_elicotteri = $model->getRichiesteElicotteroUndeleted()->where(
                        'engaged = true AND (dataora_atterraggio is null OR n_lanci is null)'
                    )->orWhere([
                        'edited' => 0
                    ])->count();

                    if ($unclosed_elicotteri > 0) {
                        throw new Exception('Non puoi chiudere l\'evento, ci sono richieste elicottero da completare', 500);
                    }
                }

                $model->is_public = $model->is_public ? $model->is_public : 0;
                if ($old_stato != 'Chiuso' && $model->stato == 'Chiuso') {
                    $model->closed_at = date('Y-m-d H:i:s');
                }


                if (!$model->save()) {
                    throw new Exception('Errore salvataggio Evento. Controllare i dati: ' . json_encode($model->getErrors()), 500);
                }

                // Salvo gli extra evento
                ConEventoExtra::deleteAll(['idevento' => $model->id]);
                if (!empty(Yii::$app->request->post('UtlEvento')['extras'])) {
                    $extras = Yii::$app->request->post('UtlEvento')['extras'];
                }
                $extrasInfoArray = Yii::$app->request->post('UtlEventoExtraInfo');
                if (!empty($extras)) {
                    $mdExtras = UtlExtraSegnalazione::find()->where(['id' => $extras])->all();
                    foreach ($mdExtras as $extra) {
                        $model->link('extras', $extra);

                        $extraInfo = ConEventoExtra::find()->where(['idevento' => $model->id, 'idextra' => $extra->id])->one();

                        $data = [];
                        if (isset($extrasInfoArray[$extra->id])) {
                            $data['ConEventoExtra'] = $extrasInfoArray[$extra->id];
                            $extraInfo->load($data);
                            $extraInfo->save();
                        }
                    }
                }

                \common\models\cap\CapExposedMessage::generateFromEvent($model, 'Update');

                // è cambiato l'evento principale
                // aggiorno sia il vecchio che il nuovo
                if (!empty($old_parent) && !empty($model->idparent) && $old_parent != $model->idparent
                ) {
                    \common\models\cap\CapExposedMessage::generateFromEvent(UtlEvento::findOne($model->idparent), 'Added fronte');
                    \common\models\cap\CapExposedMessage::generateFromEvent(UtlEvento::findOne($old_parent), 'Removed fronte');
                }

                // è stato associato a un evento
                if (empty($old_parent) && !empty($model->idparent)
                ) {
                    $fronti = UtlEvento::find()
                        ->where(['!=', 'stato', 'Chiuso'])
                        ->andWhere(['idparent' => $model->id])->count();

                    if ($fronti > 0) {
                        throw new \Exception("Non puoi trasformare questo evento in un fronte, ha dei fronti associati", 1);
                    }


                    \common\models\cap\CapExposedMessage::generateFromEvent(UtlEvento::findOne($model->idparent), 'Added fronte');
                }

                // è stato svincolato dall'evento
                if (!empty($old_parent) && empty($model->idparent)
                ) {
                    \common\models\cap\CapExposedMessage::generateFromEvent(UtlEvento::findOne($old_parent), 'Removed fronte');
                }

                $dbTrans->commit();
            } catch (Exception $e) {
                $dbTrans->rollBack();
                //throw $e;

                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->render('update', [
                    'model' => $model,
                    'tipoItems' => $tipoItems,
                    'tasksSearchModel' => $tasksSearchModel,
                    'tasksDataProvider' => $tasksDataProvider,
                    'segnalazioniSearchModel' => $segnalazioniSearchModel,
                    'segnalazioniDataProvider' => $segnalazioniDataProvider,
                    'ingaggiSearchModel' => $ingaggiSearchModel,
                    'ingaggiDataProvider' => $ingaggiDataProvider,
                    'showLatLon' => $showLatLon
                ]);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'tipoItems' => $tipoItems,
                'tasksSearchModel' => $tasksSearchModel,
                'tasksDataProvider' => $tasksDataProvider,
                'segnalazioniSearchModel' => $segnalazioniSearchModel,
                'segnalazioniDataProvider' => $segnalazioniDataProvider,
                'ingaggiSearchModel' => $ingaggiSearchModel,
                'ingaggiDataProvider' => $ingaggiDataProvider,
                'showLatLon' => $showLatLon
            ]);
        }
    }

    /**
     * List segnalazioni
     * @param integer $idEvento
     * @return mixed
     */
    public function actionListSegnalazioni($idEvento)
    {
        $evento = $this->findModel($idEvento);
        $operatore = null;
        $searchModel = new UtlSegnalazioneSearch();
        $dataProvider = $searchModel->searchByEvento($idEvento, Yii::$app->request->queryParams);

        return $this->render('list-segnalazioni', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'evento' => $evento
        ]);
    }



    /**
     * Get Sotto Tipologia Evento
     * @param integer $idEvento
     * @return mixed
     */
    public function actionGetSottotipologia()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $tipo_id = $parents[0];

                $out = array();
                if (!empty($tipo_id)) {
                    $list = UtlTipologia::find()->where(['idparent' => $tipo_id])
                        ->andWhere(
                            '(valido_dal is null OR valido_dal <= CURRENT_DATE) AND (valido_al is null OR valido_al >= CURRENT_DATE)'
                        )->orderBy(['tipologia' => SORT_ASC])->asArray()->all();
                    foreach ($list as $i => $item) {
                        $out[] = ['id' => $item['id'], 'name' => $item['tipologia']];
                        if ($i == 0) {
                            $selected = null;
                        }
                    }
                }
                return Json::encode(['output' => $out, 'selected' => '']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    /**
     * Autocomplete specific
     * @return [type] [description]
     */
    public function actionSearchIndirizzo()
    {
        $rows = (new \yii\db\Query())
            ->select(['full_address', 'via', 'civici'])
            ->from('_autocomplete_addresses')
            ->where('full_address % :q')
            ->andWhere(['comune' => Yii::$app->request->post('comune')])
            ->andWhere(['provincia' => Yii::$app->request->post('provincia')])
            ->orderBy(new \yii\db\Expression('full_address <-> :q'));

        $rows = $rows->addParams([
            'q' => Yii::$app->request->post('string')
        ])
            ->limit(20)
            ->all();

        return Json::encode($rows);
    }

    /**
     * Autocomplete specific
     * @return [type] [description]
     */
    public function actionSearchToponimo()
    {
        $rows = (new \yii\db\Query())
            ->select(['toponimo', 'ogc_fid', 'ST_X(geom_) as lon', 'ST_Y(geom_) as lat'])
            ->from('toponimi_igm_geom')
            ->where(['ilike', 'toponimo', Yii::$app->request->post('string')])
            ->andWhere(['ilike', 'comune', Yii::$app->request->post('comune')])
            ->andWhere(['cod_regione' => Yii::$app->params['region_filter_id']])
            ->limit(20)
            ->all();


        return Json::encode($rows);
    }

    /**
     * Torna comune
     * @return [type] [description]
     */
    public function actionGetComune()
    {
        if (empty(Yii::$app->request->get('id_comune'))) {
            return null;
        }
        return Json::encode(LocComune::find()->where(['loc_comune.id' => Yii::$app->request->get('id_comune')])->one());
    }

    /**
     * Get Indirizzi (sia segnalazione che evento)
     * @param integer $id_comune
     * @return mixed
     */
    public function actionGetIndirizzi()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $comune_id = $parents[0];

                $out = array();
                if (!empty($comune_id)) {
                    $list = LocIndirizzo::find()->where(['id_comune' => $comune_id])->asArray()->orderBy(['name' => SORT_ASC])->all();
                    foreach ($list as $i => $item) {
                        $out[] = ['id' => $item['id'], 'name' => $item['name']];
                        if ($i == 0) {
                            $selected = null;
                        }
                    }
                }
                return Json::encode(['output' => $out, 'selected' => '']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    /**
     * Get Civici (sia segnalazione che evento)
     * @param integer $id_comune
     * @return mixed
     */
    public function actionGetCivici()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $indirizzo_id = $parents[0];

                $out = array();
                if (!empty($indirizzo_id)) {
                    $list = LocCivico::find()->where(['id_indirizzo' => $indirizzo_id])->asArray()->orderBy(['civico' => SORT_ASC])->all();
                    foreach ($list as $i => $item) {
                        $out[] = ['id' => $item['id'], 'name' => $item['civico']];
                        if ($i == 0) {
                            $selected = null;
                        }
                    }
                }
                return Json::encode(['output' => $out, 'selected' => '']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    /**
     * Creates a new UtlSegnalazione model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @deprecated
     * @return mixed
     */
    public function actionAddSegnalazione($idEvento)
    {
        $model = new UtlSegnalazione();
        $utente = new UtlUtente();
        $utente->scenario = 'createSegnalatore';


        if ($model->load(Yii::$app->request->post())) {
            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();
            try {
                // Salvataggio utente
                if (!$utente->load(Yii::$app->request->post()) || !$utente->save(false)) {
                    throw new Exception('Errore salvataggio Segnalatore. Controllare i dati', 500);
                }

                // Aggiorno idutente e salvo il model
                $model->idutente = $utente->getPrimaryKey();

                // Calcola Lat e Lon
                $indirizzo = Yii::$app->request->post('UtlSegnalazione')['indirizzo'];
                $comune = LocComune::findOne(Yii::$app->request->post('UtlSegnalazione')['idcomune']);
                if (!empty($indirizzo) && !empty($comune)) {
                    $address = $indirizzo . ", " . $comune->comune;
                    $geoCoordinates = MyHelper::getLatLonFromAddress($address);
                    $model->lat = $geoCoordinates['lat'];
                    $model->lon = $geoCoordinates['lon'];
                } else {
                    $model->lat = 38.865671;
                    $model->lon = 16.602607;
                }

                // Salvo idsalaoperativa prendendelo dall'operatore che effettua la segnalazione
                $operatore = UtlOperatorePc::find()->where(['iduser' => Yii::$app->user->id])->one();
                if (isset($operatore)) {
                    $model->idsalaoperativa = $operatore->idsalaoperativa;
                }


                // Salvo il model segnalazione
                if (!$model->save()) {
                    throw new Exception('Errore salvataggio Segnalazione. Controllare i dati', 500);
                }


                // Salvo gli extra segnalazione
                $extras = Yii::$app->request->post('UtlSegnalazione')['extras'];
                if (!empty($extras)) {
                    $mdExtras = UtlExtraSegnalazione::find()->where(['id' => $extras])->all();
                    foreach ($mdExtras as $extra) {
                        $model->link('extras', $extra);
                    }
                }

                // Creo connessione con Segnalazione
                $conEventoSegnalazione = new ConEventoSegnalazione();
                $conEventoSegnalazione->idsegnalazione = $model->id;
                $conEventoSegnalazione->idevento = $idEvento;
                $conEventoSegnalazione->save();
                if (!$conEventoSegnalazione->save(false)) {
                    throw new Exception('Errore salvataggio Connessione Segnalazione', 500);
                }

                // Cambio stato alla segnalazione
                $model->stato = 'Verificata e trasformata in evento';
                if (!$model->save(false)) {
                    throw new Exception('Errore salvataggio stato Segnalazione', 500);
                }


                $dbTrans->commit();
            } catch (Exception $e) {
                $dbTrans->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
                return false;
            }

            return $this->redirect(['list-segnalazioni', 'idEvento' => $idEvento]);
        }
    }

    /**
     * List segnalazioni
     * @param integer $idEvento
     * @return mixed
     */
    public function actionViewSegnalazione($id)
    {
        $model = UtlSegnalazione::findOne($id);

        return $this->renderPartial('modal-segnalazione-view', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing UtlEvento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UtlEvento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UtlEvento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UtlEvento::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Elicotteri in volo
     * @return [type] [description]
     */
    public function actionElicotteriInVoloHtml()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $n = (isset(Yii::$app->params['helicopters_minutes'])) ? intval(Yii::$app->params['helicopters_minutes']) : 15;

        $elicotteri_in_volo = Yii::$app->db->createCommand("SELECT 
            device_name, 
            ore_di_volo, 
            stop_local_timestamp
            FROM (
                SELECT 
                    *,
                    row_number() over (partition by device_name order by stop_local_timestamp desc) as row_number
                FROM utl_arka_voli where start_local_timestamp > (NOW() - INTERVAL '1 day')) v WHERE 
                v.stop_local_timestamp > (NOW() - INTERVAL '" . $n . " minutes') AND v.row_number = 1")
            ->queryAll();

        $string = '
        <div>
            <p><b>ELICOTTERI IN VOLO</b></p>
            <div class="row">
        ';

        foreach ($elicotteri_in_volo as $elicottero) {
            $string .= '<div class="col-lg-4">
                    <b>' . $elicottero['device_name'] . '</b><br />
                    <small>(TEMPO DI VOLO ' . $elicottero['ore_di_volo'] . ')</small>
                    </div> ';
        }
        $string .= '</div></div>';

        return $string;
    }


    public function actionSchieramentoList($id)
    {
        $model = $this->findModel($id);

        $searchModel = new ViewRisorseSchieramenti();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->renderAjax('schieramento-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }


    public function actionActivateSchieramento($id_evento, $id_risorsa)
    {
        $model = $this->findModel($id_evento);

        $searchModel = new ViewRisorseSchieramenti();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $tr = Yii::$app->db->beginTransaction();

        try {
            $risorsa = ViewRisorseSchieramenti::find()->where(['uid'=>$id_risorsa])->one();
            if (!$risorsa) {
                throw new \Exception("Risorsa non trovata", 1);
            }

            $element = null;
            $ingaggio = null;
            if ($risorsa->tipo == 'mezzo') {
                $ingaggio = $risorsa->ingaggioMezzo;
                $element = UtlAutomezzo::findOne($risorsa->id);
            } else {
                $ingaggio = $risorsa->ingaggioAttrezzatura;
                $element = UtlAttrezzatura::findOne($risorsa->id);
            }

            $new_ingaggio = new UtlIngaggio();
            $new_ingaggio->idevento = $id_evento;
            if ($risorsa->tipo == 'mezzo') {
                $new_ingaggio->idautomezzo = $risorsa->id;
            } else {
                $new_ingaggio->idattrezzatura = $risorsa->id;
            }
            $new_ingaggio->idorganizzazione = $element->organizzazione ? $element->organizzazione->id : null;
            $new_ingaggio->idsede = $element->sede ? $element->sede->id : null;
            if (!$new_ingaggio->save()) {
                throw new \Exception(json_encode($new_ingaggio->getErrors()), 1);
            }

            $new_ingaggio->stato = 1;
            if (!$new_ingaggio->save()) {
                throw new \Exception(json_encode($new_ingaggio->getErrors()), 1);
            }

            if (!empty($ingaggio)) {
                $ingaggio->note = $ingaggio->note . " - Deviato su evento " . $model->num_protocollo . " per attivazione schieramento";
                $ingaggio->deviato = $new_ingaggio->id;
                $ingaggio->stato = 3;
                if (!$ingaggio->save()) {
                    throw new \Exception(json_encode($ingaggio->getErrors()), 1);
                }
            }

            $tr->commit();

            return $this->renderAjax('schieramento-list', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'model' => $model,
                'reload_pjax_main'=>true
            ]);
        } catch (\Exception $e) {
            $tr->rollBack();
            return $this->renderAjax('schieramento-list', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'model'=>$model,
                'error_message' => $e->getMessage()
            ]);
        }
    }

    private function replaceDashAndBar($txt)
    {
        return str_replace("||||", ",", str_replace("###", " ", $txt));
    }

    private function getCapMessageDate($v)
    {
        if (!$v) {
            return '';
        }
        $dt = \DateTime::createFromFormat("Y-m-d H:i:sP", str_replace("T", " ", $v));
        if (is_bool($dt)) {
            return '';
        }

        return $dt->format('d/m/Y H:i:s');
    }

    public function actionMonitoraggio()
    {
        $eventi = UtlEvento::find()->where(['stato'=>'In gestione'])->all();
        $id_eventi_correnti = array_map(function ($evento) {
            return $evento->id;
        }, $eventi);
        $richieste_elicottero = RichiestaElicottero::find()->where(['engaged'=>true])
            ->andWhere('id_elicottero is not null')
            ->andWhere(['idevento'=>$id_eventi_correnti])
            ->orderBy(['created_at'=>SORT_ASC])
            ->all();
        $richieste_canadair = RichiestaCanadair::find()->where(['engaged'=>true])
            ->andWhere('codice_canadair is not null')
            ->andWhere(['idevento'=>$id_eventi_correnti])
            ->orderBy(['created_at'=>SORT_ASC])
            ->all();
        $richieste_dos = RichiestaDos::find()->where(['engaged'=>true])
            ->andWhere('codicedos is not null')
            ->andWhere(['idevento'=>$id_eventi_correnti])
            ->orderBy(['created_at'=>SORT_ASC])
            ->all();

        $array_risultati = [];
        $codici_elicottero = [];
        $codici_canadair = [];
        $codici_dos = [];

        foreach ($richieste_elicottero as $richiesta) {
            $codici_elicottero[$richiesta->codice_elicottero] = [
                'codice' => $richiesta->codice_elicottero,
                'id_evento' => $richiesta->idevento,
                'elicottero' => $richiesta->elicottero->targa
            ];
        }

        foreach ($richieste_canadair as $richiesta) {
            $codici_canadair[$richiesta->codice_canadair] = [
                'codice' => $richiesta->codice_canadair,
                'id_evento' => $richiesta->idevento
            ];
        }

        foreach ($richieste_dos as $richiesta) {
            $codici_dos[$richiesta->codicedos] = [
                'codice' => $richiesta->codicedos,
                'id_evento' => $richiesta->idevento
            ];
        }

        foreach ($eventi as $evento) {
            $elemento = [
                'id' => $evento->id,
                'comune' => $evento->comune->comune,
                'provincia' => $evento->comune->provincia->sigla,
                'indirizzo' => !empty($evento->luogo) ? $evento->luogo : $evento->indirizzo,
                'elicotteri' => [],
                'canadair' => [],
                'dos' => [],
                'attivazioni' => [],
                'veicoli_cap' => []
            ];

            foreach ($codici_elicottero as $key => $value) {
                if ($value['id_evento'] == $evento->id) {
                    $elemento['elicotteri'][] = $value;
                }
            }

            foreach ($codici_canadair as $key => $value) {
                if ($value['id_evento'] == $evento->id) {
                    $elemento['canadair'][] = $value;
                }
            }

            foreach ($codici_dos as $key => $value) {
                if ($value['id_evento'] == $evento->id) {
                    $elemento['dos'][] = $value;
                }
            }

            $attivazioni = UtlIngaggio::find()
                ->where(['idevento'=>$evento->id, 'stato'=>1])
                ->all();

            foreach ($attivazioni as $attivazione) {
                $tipologia = '';
                $odv = '';
                $identificativo = '';
                if (empty($attivazione->organizzazione)) {
                    continue;
                } else {
                    $odv = $attivazione->organizzazione->ref_id;
                }
                if (!empty($attivazione->automezzo)) {
                    $tipologia = $attivazione->automezzo->tipo->descrizione;
                    $identificativo = $attivazione->automezzo->targa;
                }
                if (!empty($attivazione->attrezzatura)) {
                    $tipologia = $attivazione->attrezzatura->tipo->descrizione;
                    $identificativo = $attivazione->attrezzatura->modello;
                }



                $elemento['attivazioni'][] = [
                    'odv' => $odv,
                    'tipologia' => $tipologia,
                    'identificativo' => $identificativo
                ];
            }

            $vehicles = [];
            // veicoli da cap
            foreach ($evento->getLastCapMessages()->all() as $cap_message) {
                $json_data = $cap_message->json_content;
                $params = [];
                try {
                    $params = $json_data['info']['parameter'];
                } catch (\Exception $e) {
                }

                foreach ($params as $param) {
                    if ($param['valueName'] == 'VEHICLES') {
                        $txt = preg_replace_callback('~"[^"]*"~', function ($m) {
                            return preg_replace('~\s~', '###', $m[0]);
                        }, $param['value']);

                        $txt = preg_replace_callback('~"[^"]*"~', function ($m) {
                            return preg_replace('~\,~', '||||', $m[0]);
                        }, $txt);

                        $v = explode(" ", $txt);
                        foreach ($v as $row) {
                            $row_data = explode(",", $row);
                            $vehicles[] = [
                                'cap_id' => $cap_message->id,
                                'cap_identifier' => $cap_message->identifier,
                                'targa' => isset($row_data[0]) ? $this->replaceDashAndBar($row_data[0]) : '',
                                'modello' => isset($row_data[1]) ? $this->replaceDashAndBar($row_data[1]) : '',
                                'data_1' => isset($row_data[2]) ? $this->getCapMessageDate($row_data[2]) : '',
                                'data_2' => isset($row_data[3]) ? $this->getCapMessageDate($row_data[3]) : '',
                                'data_3' => isset($row_data[4]) ? $this->getCapMessageDate($row_data[4]) : '',
                                'data_4' => isset($row_data[5]) ? $this->getCapMessageDate($row_data[5]) : '',
                                'codice' => isset($row_data[6]) ? $this->replaceDashAndBar($row_data[6]) : '',
                            ];
                        }
                    }
                }
            }

            $elemento['veicoli_cap'] = $vehicles;
            $array_risultati[] = $elemento;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $array_risultati,
            'pagination' => false
        ]);

        return $this->render('monitoraggio_realtime', [
            'dataProvider' => $dataProvider
        ]);
    }



    private function getGeoQueries($evento)
    {
        $queries = GeoQuery::find()->where(['enabled'=>true])->orderBy(['group'=>SORT_ASC, 'id' => SORT_ASC])->all();
        $results = [];
        foreach ($queries as $q) {
            $results[] = $q->execQuery($evento);
        }
        return $results;
    }
}
