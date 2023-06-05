<?php 
use yii\helpers\Html;
use yii\helpers\Url;

use common\models\LocProvincia;
use common\models\LocComune;
use common\models\UtlTipologia;
use common\models\UtlAutomezzoTipo;
use common\models\UtlIngaggio;
use common\models\VolOrganizzazione;

function map_key_name($name) {
	switch($name){
		case 'year': 
			return "Anno";
		break;
		case 'month': 
			return "Mese";
		break;
		case 'pr': 
			return "Sigla provincia";
		break;
		case 'stato_ingaggio': 
			return "Stato attivazione";
		break;
		case 'tipo_mezzo': 
			return "Tipo di mezzo";
		break;
		case 'dataora': 
			return "Data e ora";
		break;
		default:
			return ucfirst($name);

	}
}

function map_key_value($name, $value) {
	switch($name){
		case 'stato_ingaggio':
			$s = UtlIngaggio::getStati();
			return $s[$value];
		break;
		case 'odv': 
			$org = VolOrganizzazione::find()->where(['ref_id'=>$value]);
			return $value . " - " . $org->denominazione;
		break;
		case 'pr':
			$pr =  LocProvincia::find()->where(['sigla'=>$value])->one();
			return $pr->sigla;
		break;
		case 'comune':
			$c =  LocComune::findOne($value);
			return $c->comune;
		break;
		case 'tipo_mezzo': 
			$t = UtlAutomezzoTipo::findOne($value);
			return $t->descrizione;
		break;
		case 'sottotipologia': case 'tipologia': 
			$t = UtlTipologia::findOne($value);
			return $t->tipologia;
		break;
		case 'dataora': 
			$dt = \DateTime::createFromFormat('Y-m-d H:i', $value);
			return $dt->format('d/m/Y H:i');
		break;
		default:
			return $value;

	}
}

$date_from = null;
$date_to = null;


$filters = [];

if(isset($filter_model)){
	foreach ($filter_model as $key => $value) {
		if($key === 'date_from' && !empty($value)) {
			$date_from = \DateTime::createFromFormat('Y-m-d', $value)->format('d/m/Y');
		} elseif($key === 'date_to' && !empty($value)) {
			$date_to = \DateTime::createFromFormat('Y-m-d', $value)->format('d/m/Y');
		} else {
			if(!empty($value)) {
				$filters[] = map_key_name($key) . ": " . map_key_value($key,$value);
			}
		}
	}
}

$filter_string = implode(", ", $filters);


?>
<htmlpagefooter name="def_footer">
	<div style="font-size: 7pt; width: 50%; float: left; text-align: left;">
		<?php echo date('d/m/Y H:i:s');?>
	</div>
	
    <div style="font-size: 7pt; width: 50%; float: right; text-align: right;">
        {PAGENO}/{nb}
    </div>
    
</htmlpagefooter>
<sethtmlpagefooter name="def_footer" value="on"/>
<div>
	<div style="width: 120px; float: left">
		<?php echo Html::img(Yii::getAlias('@backend/web/images/logo.png'), ['width' => 130]) ?>
	</div>
	<div style="width: 850px; float: left;">
		<p style="text-align: center; padding-top: 20px; margin-bottom: 20px">
			REPORT <?php echo $filter_string !== '' ? "(" . $filter_string . ") " : "";?><?php if($date_from) echo "dal " . $date_from;?> <?php if($date_to) echo "al " . $date_to;?>
		</p>
		<p >REGIONE LAZIO AGENZIA DI PROTEZIONE CIVILE</p>
	</div>
</div>