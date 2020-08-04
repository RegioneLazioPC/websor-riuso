<?php
namespace common\models\reportistica;

use yii\base\Model;

/**
 * Classe per la gestione dei filtri nei report
 */
class FilterModel extends Model
{
    public $year, $date_from, $date_to, $month, $day, $pr, $tipologia, $sottotipologia, $comune, $tipo_mezzo, $stato_ingaggio, $dataora, $odv;

    public function rules()
    {
        return [
        	[['year'], 'date', 'format' => 'php:Y'],
        	[['month'], 'integer', 'min' => 1, 'max'=>12],
        	[['day'], 'integer', 'min' => 1, 'max'=>31],
        	[['date_from'], 'date', 'format' => 'php:Y-m-d'],
            [['dataora'], 'date', 'format' => 'php:Y-m-d H:i'],
        	[['date_to'], 'date', 'format' => 'php:Y-m-d'],
        	[['pr'], 'string', 'max'=>2],
            [['odv'], 'integer'],
        	[['tipologia', 'sottotipologia', 'comune', 'tipo_mezzo', 'stato_ingaggio'], 'integer']            
        ];
    }

    public function attributeLabels()
    {
        return [
            'year' => 'Anno',
            'month' => 'Mese',
            'day'  => 'Giorno',
            'pr' => 'Provincia',
            'date_from' => 'Data da',
            'date_to' => 'Data a',
            'tipo_mezzo' => 'Tipo mezzo',
            'stato_ingaggio' => 'Stato attivazione'
        ];
    }

    public static $months = [
    	null,
    	'Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'
    ];

    
}