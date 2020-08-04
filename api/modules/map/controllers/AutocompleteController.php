<?php

namespace api\modules\map\controllers;

use Exception;
use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;


use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;

use api\utils\ResponseError;

/**
 * Autocomplete Controller
 *
 * 
 */
class AutocompleteController extends ActiveController
{
    public $modelClass = 'common\models\UtlEvento';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['authenticator'] =[
                'class' => \api\utils\Authenticator::class,
                'except' => ['options','search']
        ];

        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'denyCallback' => function ($rule, $action) {
                throw new Exception('Non sei autorizzato', 401);
            },
            'except' => ['options','search'],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    /**
     * Di default per il metodo options torniamo ok in modo da non avere errori not found dalle chiamate automatiche del browser
     * @return [type] [description]
     */
    public function actionOptions() {
        return ['message'=>'ok'];
    }

    
    public $replace_useless = [
        'via', 'vi', 'pzza', 'piazza', 'pza', 'strada', 'strda', 'stada', 'strd', 'strad',
        'via ', 'vi ', 'pzza ', 'piazza ', 'pza ', 'strada ', 'strda ', 'stada ', 'strd ', 'strad '
    ];

    /**
     * Ritorna lista di indirizzi corrispondenti alla ricerca
     * @return [type] [description]
     */
    public function actionSearch (  ) 
    {

        $result_number = 15;
          

        $string = Yii::$app->request->get('address');
        if(strlen($string) < 3) return [];



        $return_logs = [];
        $parsed = $this->removeUseless($string, $return_logs);
        // rimuovi i primi se è nello useless
        if(strlen($string) < 3) return [];
        $string = $parsed[0];
        $return_logs = $parsed[1];

        
        $rows = (new \yii\db\Query())
            ->select(['*'])
            ->from('_autocomplete_addresses')
            ->where('search_field @@ plainto_tsquery(:q)');

        if( Yii::$app->request->get('c') ) $rows->andWhere(['ilike','comune',Yii::$app->request->get('c').'%', false]);
        if( Yii::$app->request->get('pr') ) $rows->andWhere(['ilike','provincia',Yii::$app->request->get('c').'%', false]);

        $rows = $rows->addParams([
            'q' => Yii::$app->request->get('address')
        ])
        ->limit($result_number)
        ->all();

        return $rows;
        
        
    }

    private function removeUseless($address, $logs) {
        
        $str_length = strlen( $address );
        $replacements = [];
        foreach($this->replace_useless AS $value)
        {            
            if( preg_match( 
                '/^'.$value.'/', 
                substr($address, 0, 8) 
            ) > 0 ) {
                $string = str_replace($value, "", $address );
                $replacements[] = $string;
                $logs[] = "valore per " . $value . ": " .$string;
            }
            
        }
        
        if ( count( $replacements ) == 0 ) return [$address, $logs];

        $return_string = $replacements[0];
        // teoricamente il valore più lungo rimosso è quello che maggiormente fa il match con la stringa
        foreach ($replacements as $string_replaced) {
            if ( strlen( $string_replaced ) < strlen( $return_string ) ) $return_string = $string_replaced;
        }
        return [ trim ( $return_string ),$logs];
    }

    

}
