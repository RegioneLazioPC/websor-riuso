<?php
namespace console\controllers;

use common\models\User;
use common\models\UtlEvento;
use Exception;
use Yii;
use yii\console\Controller;


use common\models\RichiestaCanadair;
use common\models\RichiestaElicottero;
use common\models\RichiestaDos;
use common\models\ConOperatoreTask;


use common\models\VolOrganizzazione;
use common\models\VolTipoOrganizzazione;
use common\models\VolVolontario;
use common\models\VolSede;
use common\models\utility\UtlContatto;
use common\models\utility\UtlIndirizzo;
use common\models\UtlAnagrafica;
use common\models\LocComune;
use common\models\UtlUtente;
use common\models\MasMessage;
use common\models\UplMedia;

use CodiceFiscale\Calculator;
use CodiceFiscale\Subject;

/**
 * Inserimento dati fake per test performance
 *
 * dopo ulteriori sviluppi dei model il metodo è
 * @deprecated
 */
class BatchController extends Controller
{
    public $faker;

    

    public $sample_events_data = [
        'UtlEvento' => [
            'tipologia_evento' => '49',
            'lat' => '41.9097334',
            'lon' => '12.4768165',
            'idcomune' => 4851,
            'indirizzo' => 'Via del Corso, 1, Roma, RM, Italia',
            'sottotipologia_evento' => 58,
            'stato'=>'In gestione'
        ],
        'ConOperatoreTask' => [
            'idoperatore' => 55,
            'idfunzione_supporto' => 5,
            'idtask' => 6
        ],
        'RichiestaCanadair' => [
            'idoperatore' => 55,
            'idcomunicazione' => 194
        ],
        'RichiestaDos' => [
            'idoperatore' => 55,
            'idcomunicazione' => 194
        ],
        'RichiestaElicottero' => [
            'idoperatore' => 55,
            'tipo_intervento' => 'Soppressione'
        ]
    ];

    public $sample_organizzazioni_faker = [];

    public $note = [
        'Richiesta DOS - Evento 225/2018',
        'Aggiornata scheda DOS - Codice DOS: 237891 - Nota',
        'Aggiornata scheda Canadair'
    ];

    public $vendors = ['ios','android'];

    /**
     * Inserimento di eventi per verifica problemi ram in lista eventi chiusi
     * @param  integer $n Numero di eventi da generare
     * @return void
     */
    public function actionAddEventi($n) {

        for($x = 0; $x < $n; $x++) {
            $evento = new UtlEvento;
            $evento->load($this->sample_events_data);
            $evento->save();

            for($nn = 0; $nn < 30; $nn++) {
                $tsk = new ConOperatoreTask;
                $tsk->load($this->sample_events_data);
                $tsk->idevento = $evento->id;
                $tsk->note = $this->note[rand(0,2)];
                $tsk->save();
            }

            $can = new RichiestaCanadair;
            $can->load($this->sample_events_data);
            $can->idevento = $evento->id;
            $can->save();
            $can = new RichiestaCanadair;
            $can->load($this->sample_events_data);
            $can->idevento = $evento->id;
            $can->save();
            $can = new RichiestaCanadair;
            $can->load($this->sample_events_data);
            $can->idevento = $evento->id;
            $can->save();

            $can = new RichiestaElicottero;
            $can->load($this->sample_events_data);
            $can->idevento = $evento->id;
            $can->save();
            $can = new RichiestaElicottero;
            $can->load($this->sample_events_data);
            $can->idevento = $evento->id;
            $can->save();
            $can = new RichiestaElicottero;
            $can->load($this->sample_events_data);
            $can->idevento = $evento->id;
            $can->save();

            $can = new RichiestaDos;
            $can->load($this->sample_events_data);
            $can->idevento = $evento->id;
            $can->save();
            $can = new RichiestaDos;
            $can->load($this->sample_events_data);
            $can->idevento = $evento->id;
            $can->save();
            $can = new RichiestaDos;
            $can->load($this->sample_events_data);
            $can->idevento = $evento->id;
            $can->save();

            $fronti_n = rand(0,10);
            for($f = 0; $f < $fronti_n; $f++) {
                $fronte = new UtlEvento;
                $fronte->load($this->sample_events_data);
                $fronte->idparent = $evento->id;
                $fronte->save();
            }

            $evento->stato = 'Chiuso';
            $evento->closed_at = date('Y-m-d H:m:s');
            $evento->save();

            echo $x."\n";
        }
    }


    /**
     * Aggiungi record da inserire per la rubrica
     * 
     * Inserimento organizzazioni
     * per ogni org 2 sedi
     *     per ogni sede 10 volontari
     * per ogni org 
     *     1 volontario vicepresidente
     *     1 volontario tesoriere
     *     1 volontario componente direttivo
     * per ogni org 1 anagrafica referente
     * per ogni org 1 anagrafica rappr. legale
     * per ogni volontario 1 anagrafica
     * per ogni anagrafica 
     *     1 tel
     *     1 tel h24
     *     1 email
     *     1 pec
     *     1 fax h24
     *     1 fax
     * per ogni rappr. legale device
     * per ogni referente device
     * per 3 volontari dell'org device
     * 
     * @param  integer $n Numero organizzazioni da inserire
     * @return void
     */
    public function actionPopulateRubrica($n = 1) {
        
        $this->faker = \Faker\Factory::create('it_IT');


        $tipi_contatto = [0,1,2,3,4,5];

        $conn = \Yii::$app->db;
        
        
        for ( $x = 0; $x < $n; $x++ ) {

            $dbTrans = $conn->beginTransaction();

            try {
                // metto org e sedi
                $org = new VolOrganizzazione;
                $org->load($this->getVolOrganizzazioneData());
                if(!$org->save()) throw new \Exception(json_encode($org->getErrors()));

                echo "Organizzazione\n";

                foreach ( $tipi_contatto as $tipo_contatto ) {
                    $contatto = new UtlContatto;
                    $contatto->load( $this->getContattoData($tipo_contatto) );
                    if(!$contatto->save()) throw new \Exception(json_encode($contatto->getErrors()));

                    echo "Contatto organizzazione\n";

                    $org->link('contatto', $contatto);
                }

                $sede_legale = new VolSede;
                $sede_legale->load($this->getVolSedeData($org->id,'Sede Legale'));
                if(!$sede_legale->save()) throw new \Exception(json_encode($sede_legale->getErrors()));

                echo "Sede legale\n";

                $sede_operativa = new VolSede;
                $sede_operativa->load($this->getVolSedeData($org->id,'Sede Operativa'));
                if(!$sede_operativa->save()) throw new \Exception(json_encode($sede_operativa->getErrors()));

                echo "Sede operativa\n";

                //metto volontari
                $vols = [];
                $vice = false;
                $teso = false;
                $comp = false;
                for ( $v = 0; $v < 10; $v++ ) {
                    $ruolo = false;
                    $ana = new UtlAnagrafica;
                    $ana->load($this->getVolontarioData($org->id, $sede_operativa->id, "", ""));
                    if(!$ana->save()) throw new \Exception(json_encode($ana->getErrors()));

                    echo "Anagrafica\n";

                    if(!$vice) : $ruolo = 'Vice Presidente'; $vice = true; endif;
                    if(!$teso && !$ruolo) : $ruolo = 'Tesoriere'; $teso = true; endif;
                    if(!$comp && !$ruolo) : $ruolo = 'Componente Direttivo'; $comp = true; endif;
                    if(!$ruolo) $ruolo = 'Volontario';


                    $vol = new VolVolontario;
                    $vol->load($this->getVolontarioData($org->id, $sede_operativa->id, $ana->id, $ruolo));
                    if(!$vol->save()) throw new \Exception(json_encode($vol->getErrors()));

                    $vols[] = $vol;
                    echo "Volontario\n";
                }

                // creo rappr. legale e referente
                $ana_rappr = new UtlAnagrafica;
                $ana_rappr->load($this->getVolontarioData($org->id, $sede_operativa->id, "", ""));
                if(!$ana_rappr->save()) throw new \Exception(json_encode($ana_rappr->getErrors()));

                echo "Anagrafica\n";

                $org->ref_id = $ana_rappr->id;
                $org->cf_rappresentante_legale = $ana_rappr->codfiscale;
                $org->nome_responsabile = $ana_rappr->nome . " " . $ana_rappr->cognome;
                if(!$org->save()) throw new \Exception(json_encode($org->getErrors()));

                echo "Aggiornata organizzazione\n";

                foreach ( $tipi_contatto as $tipo_contatto ) {
                    $contatto = new UtlContatto;
                    $contatto->load( $this->getContattoData($tipo_contatto) );
                    if(!$contatto->save()) throw new \Exception(json_encode($contatto->getErrors()));

                    echo "Contatto\n";

                    switch($tipo_contatto) {
                        case 0: $org->email_responsabile = $contatto->contatto; break;
                        case 1: $org->pec_responsabile = $contatto->contatto; break;
                        case 2: $org->tel_responsabile = $contatto->contatto; break;
                    }

                    if(!$org->save()) throw new \Exception(json_encode($org->getErrors()));

                    $ana_rappr->link('contatto', $contatto);
                }

                $ana_ref = new UtlAnagrafica;
                $ana_ref->load($this->getVolontarioData($org->id, $sede_operativa->id, "", ""));
                if(!$ana_ref->save()) throw new \Exception(json_encode($ana_ref->getErrors()));

                echo "Referente\n";

                $org->cf_referente = $ana_ref->codfiscale;
                $org->nome_referente = $ana_ref->nome . " " . $ana_ref->cognome;
                if(!$org->save()) throw new \Exception(json_encode($org->getErrors()));

                foreach ( $tipi_contatto as $tipo_contatto ) {
                    $contatto = new UtlContatto;
                    $contatto->load( $this->getContattoData($tipo_contatto) );
                    if(!$contatto->save()) throw new \Exception(json_encode($contatto->getErrors()));

                    echo "Contatto\n";

                    switch($tipo_contatto) {
                        case 0: $org->email_referente = $contatto->contatto; break;
                        case 3: $org->fax_referente = $contatto->contatto; break;
                        case 2: $org->tel_referente = $contatto->contatto; break;
                    }

                    if(!$org->save()) throw new \Exception(json_encode($org->getErrors()));

                    $ana_ref->link('contatto', $contatto);
                }

                // per ogni volontario metto contatti random
                foreach ($vols as $volontario) {
                    $indirizzo = new UtlIndirizzo;
                    $indirizzo->load($this->getIndirizzoData());
                    if(!$indirizzo->save()) throw new \Exception(json_encode($indirizzo->getErrors()));

                    $volontario->link('indirizzo', $indirizzo);
                    // inserisco contatti a volontario
                    echo "Contatto volontario\n";
                    
                    foreach ( $tipi_contatto as $tipo_contatto ) {
                        $contatto = new UtlContatto;
                        $contatto->load( $this->getContattoData($tipo_contatto) );
                        if(!$contatto->save()) throw new \Exception(json_encode($contatto->getErrors()));

                        $volontario->link('contatto', $contatto);
                    }
                }

                // aggiorno contatti organizzazione e cfs
                
                echo "Inserita ".$org->denominazione."\n";

                $dbTrans->commit();
            } catch (\Exception $e) {
                $dbTrans->rollBack();
                var_dump($e);
            }
        }

        echo "Terminato\n";
        return;
    }

    public function actionAddDevices($n = 1) {
        $this->faker = \Faker\Factory::create();

        $conn = \Yii::$app->db;
        $dbTrans = $conn->beginTransaction();

        try {

            $volontari = VolVolontario::find()->where(['operativo'=>TRUE])->all();
            for ( $x = 0; $x < $n; $x++ ) {
                $vol = $volontari[$x];

                $utente = UtlUtente::find()->where(['id_anagrafica'=>$vol->id_anagrafica])->one();
                if(!$utente) $utente = new UtlUtente;
                $utente->scenario = 'batchCreate';
                $utente->load( $this->getUtlUtenteData($vol->id_anagrafica) );
                if(!$utente->save()) throw new \Exception(json_encode($utente->getErrors()), 1);
                
                echo "Inserito ".$x." " . $vol->anagrafica->nome . " " . $vol->anagrafica->cognome . ": " . $utente->id . " \n";
            }

            $dbTrans->commit();

        } catch(\Exception $e) {
            $dbTrans->rollBack();
            var_dump($e);
        }
    }

    public function actionSyncUtlUtente( $commit = 0, $delete_non_operativi = 0 ) {
        $this->faker = \Faker\Factory::create();

        $conn = \Yii::$app->db;
        $dbTrans = $conn->beginTransaction();

        try {

            $volontari = VolVolontario::find()->where(['operativo'=>TRUE])->all();
            foreach ( $volontari as $volontario ) {
                
                $utente = UtlUtente::find()->where(['id_anagrafica'=>$volontario->id_anagrafica])->all();
                if(count($utente) > 1) {
                    throw new \Exception("Anagrafica " . $volontario->id_anagrafica . " multipla", 1);
                } elseif (count($utente) == 1) {
                    $utente = $utente[0];
                } else {
                    continue;
                }

                $utente->scenario = 'batchCreate';
                $utente->id_organizzazione = $volontario->id_organizzazione;
                if(!$utente->save()) throw new \Exception(json_encode($utente->getErrors()), 1);
                
                echo "Modificato ". $volontario->anagrafica->nome . " " . $volontario->anagrafica->cognome . ": " . $utente->id . " \n";
            }

            // ora query di update di quelli non più operativi, non hanno id_organizzazione valorizzato
            
            if($commit == 1) {
                $dbTrans->commit();
            } else {
                $dbTrans->rollBack();
            }

        } catch(\Exception $e) {
            $dbTrans->rollBack();
            echo $e."\n";
        }
    }

    /**
     * Sposta i media di messaggi e allerte caricati in precedenza dalle tabelle piatte alle connessioni
     * @return [type] [description]
     */
    public function actionMoveMediaConnection( $commit = 0 ) {
        $conn = Yii::$app->db;
        $dbTrans = $conn->beginTransaction();

        $mas_message = MasMessage::find()->all();
        foreach ($mas_message as $message) {
            if(!empty($message->id_allerta)) {
                if(!empty($message->allerta) && !empty($message->allerta->id_media)) {
                    $media = UplMedia::findOne($message->allerta->id_media);
                    if($media) {
                        $message->allerta->link('file', $media);
                    }
                }
            } else {
                if(!empty($message->id_media)) {
                    $media = UplMedia::findOne($message->id_media);
                    if($media) {
                        $message->link('file', $media);
                    }
                }
            }
        }

        if($commit == 1) {
            $dbTrans->commit();
        } else {
            $dbTrans->rollBack();
        }
    }

    /**
     * Verifica che esista il volontario associato a utl utente
     * @return [type] [description]
     */
    public function actionTestExistsVolontarioUtente() {
        $utl = UtlUtente::find()->where('device_token is not null')->offset(rand(0,300))->one();
        echo $utl->id . " -> " . $utl->volontario->organizzazione->denominazione . " " . $utl->volontario->anagrafica->nome . " " . $utl->volontario->anagrafica->cognome."\n";
    }

    private function getVolontarioData($id_organizzazione = "", $id_sede = "", $id_anagrafica = "", $ruolo = "") {

        $for_cf = [
            "name" => $this->faker->firstName,
            "surname" => $this->faker->lastName,
            "birthDate" => $this->faker->date('Y-m-d', 'now'),
            "gender" => $this->faker->randomElement(['F','M']),
            "belfioreCode" => "H501"
        ];

        $subject = new Subject($for_cf);
        $cod_f = new Calculator($subject);
        
        $comune = LocComune::find()->where(['comune'=>'Roma'])->one();
        return [
            'VolVolontario' => [
                'id_anagrafica' => $id_anagrafica,
                'valido_dal' => $this->faker->date('Y-m-d', 'now'),
                'operativo' => true,
                'id_organizzazione' => $id_organizzazione,
                'id_sede' => $id_sede,
                'vol_volontario_ruolo' => $ruolo
            ],
            'UtlAnagrafica' => [
                'nome' => $for_cf['name'],
                'cognome' => $for_cf['surname'],
                'data_nascita' => $for_cf['birthDate'],
                'luogo_nascita' => 'Roma',
                'codfiscale' => $cod_f->calculate(),
                'comune_residenza' => $comune->id,
                'indirizzo_residenza' => $this->faker->streetAddress,
                'cap_residenza' => substr($this->faker->postcode, 0, 5)
            ]
        ];
    }

    private function getVolOrganizzazioneData() {
        $nome = $this->faker->text(50);
        return [
            'VolOrganizzazione' => [
                'id_tipo_organizzazione' => VolTipoOrganizzazione::find()->where(['tipologia'=>'ASSOCIAZIONI DI VOLONTARIATO'])->one()->id,
                'denominazione' => $nome,
                'ragione_sociale' => $nome,
                'codice_fiscale' => $this->faker->regexify('[0-9]{11}'),
                'partita_iva' => $this->faker->regexify('[0-9]{11}'),
                'data_costituzione' => $this->faker->date('Y-m-d', 'now')
            ]
        ];
    }

    private function getVolSedeData($id_organizzazione = null, $tipo = 'Sede Legale') {
        return [
            'VolSede' => [
                'id_organizzazione' => $id_organizzazione,
                'indirizzo' => $this->faker->streetAddress,
                'comune' => LocComune::find()->where(['comune'=>'Roma'])->one()->id,
                'tipo' => $tipo,
                'lat' => $this->faker->latitude(),
                'lon' => $this->faker->longitude(),
                'cap' => substr($this->faker->postcode, 0, 5),
                'name' => $this->faker->text(10)
            ]
        ];
    }

    private function getIndirizzoData() {
        return [
            'UtlIndirizzo' => [
                'indirizzo' => $this->faker->streetAddress,
                'civico' => "1",
                'cap' => substr($this->faker->postcode, 0, 5),
                'id_comune' => LocComune::find()->where(['comune'=>'Roma'])->one()->id
            ]
        ];
    }

    private function getContattoData($tipo) {
        $cont = "";
        switch($tipo) {
            case 0: $cont = $this->faker->email; break;
            case 1: $cont = $this->faker->email; break;
            case 2: $cont = $this->faker->phoneNumber; break;
            case 3: $cont = $this->faker->phoneNumber; break;
            case 4: $cont = $this->faker->phoneNumber; break;
            case 5: $cont = $this->faker->phoneNumber; break;
        }
        
        $var = [
            'UtlContatto' => [
                'type' => "".$tipo,
                'contatto' => $cont
            ]
        ];

        
        return $var;
    }

    private function getUtlUtenteData($id_anagrafica) {
        $v = $this->vendors[rand(0,1)];
        return [
            'UtlUtente' => [
                'id_anagrafica' => $id_anagrafica,
                'telefono' => $this->faker->e164PhoneNumber,
                'device_token' => $this->faker->regexify('[0-9,A-Z]{20}'),
                'device_vendor' => $v
            ]
        ];
    }
    
}

