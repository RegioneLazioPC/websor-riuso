<?php

namespace api\modules\v1\controllers;

use api\utils\ResponseError;
use common\models\ConEventoSegnalazione;
use common\models\LocComune;
use common\models\LocRegione;
use common\models\MyHelper;
use common\models\User;
use common\models\UtlExtraSegnalazione;
use common\models\UtlSalaOperativa;
use common\models\UtlSegnalazione;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;

use common\models\UplMedia;
use common\models\UplTipoMedia;
use yii\web\UploadedFile;

use common\models\ConSegnalazioneAppEvento;
use common\models\VolVolontario;

/**
 * Segnalazione Controller API
 *
 */
class SegnalazioneController extends ActiveController
{
    public $modelClass = 'common\models\UtlSegnalazione';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \api\utils\Authenticator::class,
            'except' => ['login', 'options']
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }


    /**
     * Lista segnalazioni
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => UtlSegnalazione::find()
                ->with('tipologia', 'utente.user', 'comune')
                ->where(['stato' => 'Nuova in lavorazione'])
                ->orderBy('dataora_segnalazione DESC'),
        ]);
    }

    /**
     * Nuova segnalazione
     *
     * @return mixed
     */
    public function actionCreate()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Creo segnalazione
        $model = new UtlSegnalazione();
        $data = Yii::$app->request->post();
        $data['fonte'] = 'App';

        $data['stato'] = "Nuova in lavorazione";

        Yii::info('Segnalazione -- Dati in arrivo da app', 'api');
        Yii::error(array(
            'status' => 200,
            'message' => Yii::$app->request->post()
        ), 'api');

        // Check user/utente
        $user = Yii::$app->user->identity;
        if (empty($user->utente->id) || $user->utente->enabled != 1) {
            Yii::error(array(
                'status' => 401,
                'message' => 'Utente non abilitato per inviare segnalazioni'
            ), 'api');

            ResponseError::returnSingleError(401, 'Utente non abilitato per inviare segnalazioni');
        }
        Yii::error($data['lat'], $data['lon']);

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("select pro_com
        from
        loc_comune_geom
        where
         ST_Contains(geom,  ST_Transform(ST_SetSRID(ST_Point(:lon, :lat),4326), 32632 ) )
         LIMIT 1", [':lon' => $data['lon'], ':lat' => $data['lat']]);

        $result_ = $command->queryAll();

        // Controllo se la segnalazione arriva dalla regione
        $geoData = $this->actionCheckIsCorrectRegion($data['lat'], $data['lon']);
        if (count($result_) > 0) {
            Yii::error(json_encode($geoData));
            Yii::info('Segnalazione -- Geolocalizzazione OK', 'api');

            if (isset($geoData['route']) && isset($geoData['street_number'])) {
                $indirizzo = @$geoData['route'] . ', ' . @$geoData['street_number'];
            } else {
                $connection = Yii::$app->getDb();
                $command = $connection->createCommand("select ST_Distance_Sphere(the_geom,ST_SetSRID(ST_Point(:lon, :lat),4326)) as distance,
                    *
                    from
                    routing.osm_ways
                    order by
                    the_geom <-> ST_SetSRID(ST_Point(:lon, :lat),4326)
                    limit 1", [':lon' => $data['lon'], ':lat' => $data['lat']]);
                $result = $command->queryAll();

                if (count($result) > 0) {
                    $indirizzo = $result[0]['name'];
                } else {
                    $indirizzo = '';
                }
            }

            $comune = LocComune::find()->select('id')
                ->where(['codistat' => $result_[0]['pro_com']])->one();
            if (!$comune) {
                ResponseError::returnSingleError(422, 'Comune non trovato');
            }

            $data['idcomune'] = $comune->id;
        } else {
            Yii::error(array(
                'status' => 422,
                'message' => 'Errore invio dati, la segnalazione deve pervenire dal territorio della Regione'
            ), 'api');

            ResponseError::returnSingleError(422, 'Errore invio dati, la segnalazione deve pervenire dal territorio della Regione');
        }

        // Salvo utente, indirizzo e comune
        $data['indirizzo'] = $indirizzo;
        $data['idcomune'] = $comune->id;
        $data['idutente'] = $user->utente->id;
        // se volontario associo l'organizzazione
        if ($user->utente->tipo == 3) {
            $vol = VolVolontario::find()->where(['id_anagrafica' => $user->utente->id_anagrafica])->andWhere(['operativo' => true])->one();
            if ($vol && !empty($vol->organizzazione)) {
                $data['id_organizzazione'] = $vol->organizzazione->id;
            }
        }

        $data['nome_segnalatore'] = @$user->utente->anagrafica->nome;
        $data['cognome_segnalatore'] = @$user->utente->anagrafica->cognome;
        $data['telefono_segnalatore'] = @$user->utente->telefono;
        $data['email_segnalatore'] = @$user->email;

        if (!empty($data['sos'])) {
            $model->scenario = "sos";
            $data['tipologia_evento'] = null;
        }

        if (!empty($data['imageUrl'])) {
            $data['foto'] = $data['imageUrl'];
        }

        if ($model->load(['UtlSegnalazione' => $data]) && $model->save()) {
            Yii::info('Segnalazione -- Entro nel salvataggio su db', 'api');

            // Salvo gli extra
            if (!empty($data["pericolo"])) {
                $data['extras'][] = 1;
            }
            if (!empty($data["feriti"])) {
                $data['extras'][] = 2;
            }
            if (!empty($data["vittime"])) {
                $data['extras'][] = 3;
            }

            if (!empty($data['extras'])) {
                $extras = $data['extras'];
                $mdExtras = UtlExtraSegnalazione::find()->where(['id' => $extras])->all();

                foreach ($mdExtras as $extra) {
                    $model->link('extras', $extra);
                }
            }

            // Se esiste idevento collego la segnalazione all'evento esistente
            if (!empty($data["idevento"])) {
                // Creo connessione con Segnalazione
                $conEventoSegnalazione = new ConSegnalazioneAppEvento();
                $conEventoSegnalazione->id_segnalazione = $model->id;
                $conEventoSegnalazione->id_evento = $data["idevento"];
                $conEventoSegnalazione->save();
                if (!$conEventoSegnalazione->save(false)) {
                    ResponseError::returnSingleError(422, 'Errore salvataggio connessione Evento Segnalazione');
                }
            }

            if ((!isset($data['sos']) || !$data['sos']) && empty($data['fromCapMessage'])) {
                $file = UploadedFile::getInstanceByName('image');
                if (empty($file)) {
                    ResponseError::returnSingleError(422, 'Carica una immagine');
                }
                $this->uploadSegnalazioneFile($file, $model);
            }

            // fix per segnalazione da sala regionale
            $created_at = (isset($data['created_at'])) ? $data['created_at'] : time();
            $model->dataora_segnalazione = date('Y-m-d H:i:s', $created_at);
            $model->save();

            Yii::info('Segnalazione -- Extra salvati correttamente su db', 'api');

            Yii::info('Segnalazione -- Salvataggio db OK', 'api');
            Yii::error(array(
                'status' => 200,
                'message' => $model
            ), 'api');

            return $model;
        } else {
            Yii::error($model->getErrors(), 'api');

            ResponseError::returnMultipleErrors(422, $model->getErrors());
        }
    }

    /**
     * Crea una segnalazione da un evento
     * @return mixed
     */
    public function actionCreateByEvent()
    {
        ResponseError::returnSingleError(403, 'Non sei abilitato, crea una nuova segnalazione');
        // Creo segnalazione
        $model = new UtlSegnalazione();
        $data = Yii::$app->request->post();
        $data['fonte'] = 'App';
        $data['stato'] = empty($data["idevento"]) ? "Nuova in lavorazione" : 'Verificata e trasformata in evento';

        Yii::info('Segnalazione By Evento -- Dati in arrivo da app', 'api');
        Yii::error(array(
            'status' => 200,
            'message' => Yii::$app->request->post()
        ), 'api');

        // Check user/utente
        $user = Yii::$app->user->identity;
        if (empty($user->utente->id)) {
            Yii::error(array(
                'status' => 401,
                'message' => 'Utente non abilitato per inviare segnalazioni'
            ), 'api');

            ResponseError::returnSingleError(401, 'Utente non abilitato per inviare segnalazioni');
        }

        // Controllo se la segnalazione arriva dalla regione
        $geoData = $this->actionCheckIsCorrectRegion($data['lat'], $data['lon']);
        if (!empty($geoData)) {
            Yii::info('Segnalazione -- Geolocalizzazione OK', 'api');
            $provincia = addslashes($geoData['administrative_area_level_2']);
            //$salaOperativa = UtlSalaOperativa::find()->where("'{$provincia}' LIKE CONCAT('%', comune, '%')")->one();
            $indirizzo = @$geoData['route'] . ', ' . @$geoData['street_number'];
            $comune = LocComune::find()->select('id')->where(['comune' => $geoData['administrative_area_level_3']])->one();
            $data['idcomune'] = $comune->id;
        } else {
            Yii::error(array(
                'status' => 422,
                'message' => 'Errore invio dati, la segnalazione deve pervenire dal territorio della Regione'
            ), 'api');

            ResponseError::returnSingleError(422, 'Errore invio dati, la segnalazione deve pervenire dal territorio della Regione');
        }

        // Salvo utente, indirizzo e comune
        $data['indirizzo'] = $indirizzo;
        $data['idcomune'] = $comune->id;
        $data['idutente'] = $user->utente->id;

        if ($model->load(['UtlSegnalazione' => $data]) && $model->save()) {
            Yii::info('Segnalazione -- Entro nel salvataggio su db', 'api');

            // Salvo gli extra
            if (!empty($data['extras'])) {
                $extras = $data['extras'];
                $mdExtras = UtlExtraSegnalazione::find()->where(['id' => $extras])->all();
                //error_log(print_r($extras,true));
                foreach ($mdExtras as $extra) {
                    $model->link('extras', $extra);
                }
            }

            if (!empty($data["idevento"])) {
                // Creo connessione con Segnalazione
                $conEventoSegnalazione = new ConEventoSegnalazione();
                $conEventoSegnalazione->idsegnalazione = $model->id;
                $conEventoSegnalazione->idevento = $data["idevento"];
                $conEventoSegnalazione->save();
                if (!$conEventoSegnalazione->save(false)) {
                    ResponseError::returnSingleError(422, 'Errore salvataggio connessione Evento Segnalazione');
                }
            }

            $file = UploadedFile::getInstanceByName('image');
            if (!empty($file)) {
                $this->uploadSegnalazioneFile($file, $model);
            }

            $model->dataora_segnalazione = date('Y-m-d H:i:s', $data['created_at']);
            $model->save();

            Yii::info('Segnalazione -- Extra salvati correttamente su db', 'api');

            Yii::info('Segnalazione -- Salvataggio db OK', 'api');
            Yii::error(array(
                'status' => 200,
                'message' => $model
            ), 'api');

            return  $model;
        } else {
            Yii::error($model->getErrors(), 'api');

            ResponseError::returnMultipleErrors(422, $model->getErrors());
        }
    }

    /**
     * Mostra segnalazioni visibili all'utente
     *
     * @return mixed
     */
    public function actionListByUser()
    {
        $postData = Yii::$app->request->post();

        $user = Yii::$app->user->identity;
        if (!empty($user)) {
            if (isset($user->utente->id)) {
                $query = UtlSegnalazione::find()->where(['idutente' => $user->utente->id])->orderBy('dataora_segnalazione DESC');
            }
        }

        if (!empty($query)) {
            return $query = new ActiveDataProvider([
                'query' => $query,
            ]);
        } else {
            ResponseError::returnSingleError(422, 'Nessuna segnalazione presente');
        }
    }

    /**
     * Verifica che un punto sia nella regione configurata
     *
     * @return mixed
     */
    public function actionCheckIsCorrectRegion($lat, $lon)
    {

        $region_name = LocRegione::findOne(Yii::$app->params['region_filter_id']);
        if (!$region_name) {
            return false;
        }


        $address = MyHelper::getAddressFromLatLon($lat, $lon);

        // get the important data
        if (!empty($address['results']['0']['address_components'])) {
            $data = array();
            foreach ($address['results']['0']['address_components'] as $element) {
                $data[$element['types']['0']] = $element['long_name'];
            }

            if (isset($data['administrative_area_level_1']) && strtoupper($data['administrative_area_level_1']) == strtoupper($region_name->regione)) {
                return $data;
            }
        }

        Yii::error('Check is Regione KO', 'api');
        return false;
    }

    /**
     * Upload immagini segnalazione
     * @return [type] [description]
     */
    protected function uploadSegnalazioneFile($file, $segnalazione)
    {

        $tipo = UplTipoMedia::find()->where(['descrizione' => 'Immagine segnalazione'])->one();
        if (empty($tipo)) {
            $tipo = new UplTipoMedia;
            $tipo->descrizione = 'Immagine segnalazione';
            $tipo->save();
        }

        $valid_files = ['image/jpeg', 'image/png', 'image/jpg'];

        $media = new UplMedia;
        $media->uploadFile($file, $tipo->id, $valid_files, "Immagine non valida", true);
        $media->refresh();

        $postdata = Yii::$app->request->post();

        if (empty($postdata['lat']) || empty($postdata['lon'])) {
            $media->delete();
            ResponseError::returnSingleError(422, "Inserisci latitudine e longitudine");
        }

        $media->lat = $postdata['lat'];
        $media->lon = $postdata['lon'];
        if (!empty($postdata['orientation'])) {
            $media->orientation = $postdata['orientation'];
        }

        $file_path = Yii::getAlias('@backend') . '/uploads/' . $media->ext . '/' . $media->date_upload . '/' . $media->nome;


        if (extension_loaded('imagick')) {
            $im = new \imagick($file_path);
            $exif = $im->getImageProperties();
        } else {
            $exif = @exif_read_data($file_path);
        }

        if ($exif) {
            $media->exif = json_encode($exif);
        }

        $md5 = md5_file($file_path);


        if (!empty($postdata['md5'])) {
            if ($postdata['md5'] != $md5) {
                $media->delete();
                ResponseError::returnSingleError(422, "Md5 non corrispondente");
            }
        }



        $media->md5 = $md5;
        $media->created_at = $postdata['created_at'];
        $media->save();

        $segnalazione->link('media', $media);

        return $media;
    }

    /**
     * Crea marker orientato
     * @param  [type] $filepath    [description]
     * @param  [type] $orientation [description]
     * @return [type]              [description]
     */
    protected function makeOrientedMarker($filepath, $orientation)
    {

        $circle = new \Imagick();
        $circle->newImage(36, 36, 'none');
        $circle->setimageformat('png');
        $circle->setimagematte(true);

        $draw = new \ImagickDraw();
        $draw->circle(36 / 2, 36 / 2, 36 / 2, 32);
        $circle->drawimage($draw);

        $imagick = new \Imagick(realpath($filepath));

        $imagick->cropThumbnailImage(36, 36);
        //$imagick->resizeImage(36, 36,\Imagick::FILTER_LANCZOS, 1, TRUE);
        $imagick->setImageFormat("png");
        $imagick->setimagematte(true);
        //$imagick->cropimage(36, 36, 0, 0);
        $imagick->compositeimage($circle, \Imagick::COMPOSITE_DSTIN, 0, 0);

        if ($orientation != -1) {
            $draw = new \ImagickDraw();

            $draw->setStrokeOpacity(1);
            $draw->setStrokeColor("#000");
            $draw->setStrokeWidth(1);

            $draw->setFillColor("#fff");

            $draw->line(0, 4, 4, 0);
            $draw->line(4, 0, 8, 4);
            //$draw->line(350, 170, 100, 150);


            $line = new \Imagick();
            $line->newImage(8, 8, "none");
            $line->setImageFormat("png");
            $line->drawImage($draw);

            $output = new \Imagick();

            $output->newimage(50, 50, "none"); //"none"
            $output->setImageFormat("png");


            $output->compositeimage($line, \Imagick::COMPOSITE_COPY, 14, 0);
            $output->compositeimage($imagick, \Imagick::COMPOSITE_COPY, 0, 4);
            $output->rotateImage('none', $orientation);

            $output->writeImage($filepath . '.oriented');
        } else {
            $imagick->writeImage($filepath . '.oriented');
        }
    }
}
