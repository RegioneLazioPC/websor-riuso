<?php
/**
 * @deprecated
 */
use Lcobucci\JWT\Signer\Hmac\Sha256;
use yii\helpers\Url;
use common\models\UtlEvento;
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0">
  <title>Sistema cartografico</title>
	<script type="text/javascript" src="<?php echo Url::home(true);?>js/mapper.js?v1.5"></script>
</head>
<body>
<?php 
$service_url = str_replace("backend/web/", "", Url::home(true));

$evt = false;
$lat = (Yii::$app->request->get('lat')) ? Yii::$app->request->get('lat') : Yii::$app->params['lat'];
$lon = (Yii::$app->request->get('lon')) ? Yii::$app->request->get('lon') : Yii::$app->params['lng'];

if(Yii::$app->request->get('evt')) {
	$e = UtlEvento::findOne(Yii::$app->request->get('evt'));
	if($e){
		$evt = $e->id;
		$lat = $e->lat;
		$lon = $e->lon;
	}
}

// GENERO IL TOKEN
$request = new yii\web\Request;
$ip = $request->getUserIP();
$agent = $request->getUserAgent();
$signer = new Sha256();

$token = Yii::$app->jwt->getBuilder()
    ->setIssuer(Yii::$app->params['iss']) 
    ->setAudience(Yii::$app->params['aud']) 
    ->setId(Yii::$app->params['tid'], true) 
    ->setIssuedAt(time()) 
    ->setNotBefore(time()) 
    ->setExpiration(time() + (3600*24)) 
    ->set( 'uid', Yii::$app->user->identity->id ) 
    ->set( 'ip', $ip )
    ->set( 'agent', $agent )
    ->sign($signer, Yii::$app->params['secret-key'])
    ->getToken(); 

?>
	<mapper-component 
	base_assets_url="<?php echo Url::base(true).'/images/';?>" 
	base_url="<?php echo Url::base(true);?>" 
	base_map_url="<?php echo Yii::$app->params['geoserver_map_url'];?>" 
	base_geoserver_url="<?php echo Yii::$app->params['geoserver_services_url'];?>" 
	base_services_url="<?php echo $service_url;?>" 
	lat="<?php echo $lat;?>" 
	lng="<?php echo $lon;?>" 
	zoom="<?php echo Yii::$app->params['zoom'];?>"
	<?php
		if($evt){
			echo "evt_id='".$evt."'\n";
			echo "evt='true'\n";
		}
	?>
	google_key="<?php echo Yii::$app->params['google_key'];?>"
	<?php if(!Yii::$app->request->get('hide_events')){
		echo "visible_events='true'\n";
	}?>
	<?php if(Yii::$app->request->get('visible_reports') && Yii::$app->user->can('listSegnalazioni')){
		echo "visible_reports='true'\n";
	}?>
	<?php if(Yii::$app->request->get('visible_organizations')){
		echo "visible_organizations='true'\n";
	}?>
	<?php if(Yii::$app->request->get('can_add') && Yii::$app->user->can('createEvento')){
		echo "can_add_event='true'\n";
	}?>
	<?php if(Yii::$app->request->get('can_add') && Yii::$app->user->can('createSegnalazione')){
		echo "can_add_segnalazione='true'\n";
	}?>

	
	<?php 
	if(Yii::$app->user->can('listSegnalazioni')){
		echo "can_view_reports='true'\n";
	}?>
	<?php 
	if(Yii::$app->user->can('listEventi')){
		echo "can_view_events='true'\n";
	}?>
	<?php 
	if(Yii::$app->user->can('viewEvento')){
		echo "can_view_event='true'\n";
	}?>
	<?php 
	if(Yii::$app->user->can('viewSegnalazione')){
		echo "can_view_report='true'\n";
	}?>
	<?php 
	if(Yii::$app->user->can('viewSede')){
		echo "can_view_organization='true'\n";
	}?>
	<?php 
	if(Yii::$app->user->can('viewOrganizzazione')){
		echo "can_view_organizations='true'\n";
	}?>
	<?php 
	if(Yii::$app->user->can('createIngaggio')){
		echo "can_engage='true'\n";
	}?>
	can_view_shapes="true"
	can_view_amenities="true"
	token="<?php echo $token;?>"
	<?php 
	if(Yii::$app->params['exclude_geoserver_from_map']){
		echo "exclude_geoserver='true'\n";
	}	?>

	shapes='[{
      "name": "Aree percorse dal fuoco",
      "legend": [
        {"name":"Aree boschive", "url":"legends/aree_incendio/aree_incendio_boschivo.png"},
        {"name":"Aree non boschive", "url":"legends/aree_incendio/aree_incendio_non_boschiva.png"},
        {"name":"Aree pascolo", "url":"legends/aree_incendio/aree_incendio_pascolo.png"},
        {"name":"Aree non classificate", "url":"legends/aree_incendio/aree_incendio_non_classificata.png"}
      ],
      "layers": [
        {"layer": "postgis:lazio_2016", "name": "2016", "tile_identifier": "aree_fuoco_2016"},
        {"layer": "postgis:lazio_2015", "name": "2015", "tile_identifier": "aree_fuoco_2015"},
        {"layer": "postgis:lazio_2014", "name": "2014", "tile_identifier": "aree_fuoco_2014"},
        {"layer": "postgis:lazio_2013", "name": "2013", "tile_identifier": "aree_fuoco_2013"},
        {"layer": "postgis:lazio_2012", "name": "2012", "tile_identifier": "aree_fuoco_2012"},
        {"layer": "postgis:lazio_2011", "name": "2011", "tile_identifier": "aree_fuoco_2011"},
        {"layer": "postgis:lazio_2010", "name": "2010", "tile_identifier": "aree_fuoco_2010"}
      ]
    },
    {
      "name": "Boschi",
      "legend": [
      ],
      "layers": [
        {"layer": "postgis:Boschi_FR", "name": "Frosinone", "tile_identifier": "boschi_FR"},
        {"layer": "postgis:Boschi_LT", "name": "Latina", "tile_identifier": "boschi_LT"},
        {"layer": "postgis:Boschi_RI", "name": "Rieti", "tile_identifier": "boschi_RI"},
        {"layer": "postgis:Boschi_RM", "name": "Roma", "tile_identifier": "boschi_RM"},
        {"layer": "postgis:Boschi_VT", "name": "Viterbo", "tile_identifier": "boschi_VT"}
      ]
    },
    {
      "name": "Aree protette",
      "legend": [
      ],
      "layers": [
        {"layer": "postgis:Aree_Protette_Monumenti_Naturali_Lazio", "name": "Aree monumenti naturali", "tile_identifier": "aree_protette_monumenti_naturali_"}
      ]
    },
    {
      "name": "Parchi",
      "legend": [
      ],
      "layers": [
        {"layer": "postgis:PARCHI", "name": "Parchi", "tile_identifier": "parchi_"}
      ]
    },
    {
      "name": "Ville",
      "legend": [
      ],
      "layers": [
        {"layer": "postgis:Villestoriche", "name": "Ville storiche", "tile_identifier": "vllstr_"}
      ]
    },
    {
      "name": "CARTA FORESTALE",
      "legend": [
      ],
      "layers": [
        {"layer": "postgis:CARTA_FORESTALE_TIPO", "name": "Carta forestale", "tile_identifier": "carta_forestale"}
      ]
    }
  ]'
	>	
	
	</mapper-component>


</body>
</html>