<?php

namespace api\modules\v1\controllers;

use common\models\UtlEvento;
use common\models\UtlTipologia;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\db\Expression;

/**
 * Evento controller
 * 
 */
class EventoController extends ActiveController
{
    public $modelClass = 'common\models\UtlEvento';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \api\utils\Authenticator::class,
            'except' => ['login','options']
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * Lista eventi
     *
     * @return mixed
     */
    public function actionIndex()
    {

        return new ActiveDataProvider([
            'query' => UtlEvento::find()
            ->where(['!=', 'stato', 'Chiuso'])
            ->andWhere(['is_public' => 1])
            ->joinWith('tipologia as tipo')
            ->andWhere('tipo.check_app = 1'),
            'pagination'=> false
        ]);
    }


    /**
     * Lista eventi per distanza
     *
     * @return mixed
     */
    public function actionListByGeo()
    {

        $params = Yii::$app->request->get();
        $params['lat'] = !empty($params['lat']) ? $params['lat'] : Yii::$app->params['lat'];
        $params['lon'] = !empty($params['lon']) ? $params['lon'] : Yii::$app->params['lng'];
        $params['distance'] = !empty($params['distance']) ? $params['distance'] : 100;


        
        return new ActiveDataProvider([

            'query' => UtlEvento::find()
                        ->select( array_merge( [
                            "utl_evento.id",
                            "utl_evento.tipologia_evento",
                            "utl_evento.lat",
                            "utl_evento.lon",
                            "utl_evento.idcomune",
                            new Expression("CASE WHEN utl_evento.indirizzo is null OR utl_evento.indirizzo = '' THEN utl_evento.luogo
                                ELSE utl_evento.indirizzo 
                                END as indirizzo
                            "),
                            "utl_evento.direzione",
                            "utl_evento.distanza",
                            "utl_evento.dataora_evento",
                            "utl_evento.dataora_modifica",
                            "utl_evento.num_protocollo",
                            "utl_evento.sottotipologia_evento",
                            "utl_evento.pericolo",
                            "utl_evento.feriti",
                            "utl_evento.vittime",
                            "utl_evento.interruzione_viabilita",
                            "utl_evento.aiuto_segnalatore",
                            "utl_evento.is_public",
                            "utl_evento.geom",
                            "utl_evento.idparent",
                            "utl_evento.stato",
                            "utl_evento.closed_at",
                            "utl_evento.address_type",
                            "utl_evento.id_indirizzo",
                            "utl_evento.id_civico",
                            "utl_evento.id_gestore_evento",
                            "utl_evento.has_coc",
                            "utl_evento.id_sottostato_evento",
                            "utl_evento.archived",
                            "concat( 'Comune: ', c.comune, '\n', 'Sottotipologia: ', COALESCE(st.tipologia, ' - '), '\n', 'Stato: ', COALESCE(ss.descrizione, ' - ')  ) as note"
                        ], ['ST_Distance_Sphere(geom, ST_MakePoint(:lon, :lat)) as distance'] ) )
                        ->where(['!=', 'stato', 'Chiuso'])
                        ->andWhere('ST_Distance_Sphere(geom, ST_MakePoint(:lon, :lat)) <= :distance')
                        ->addParams([
                            ':lat' => floatval($params['lat']),
                            ':lon' => floatval($params['lon']),
                            ':distance' => intval($params['distance']*1000)
                        ])                        
                        ->andWhere(['utl_evento.is_public' => 1])
                        ->joinWith(['tipologia as t','sottotipologia as st', 'sottostato as ss', 'comune as c'])
                        ->andWhere(['t.check_app' => 1]),
            'pagination'=> false,
        ]);
    }


    /**
     * Lista tipoligia eventi
     *
     * @return mixed
     */
    public function actionTipologia()
    {

        return new ActiveDataProvider([
            'query' => UtlTipologia::find()
            ->where(['idparent' => null])
            ->andWhere(['check_app'=>1]),
            'pagination'=> false,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC
                ]
            ],
        ]);
    }

}


