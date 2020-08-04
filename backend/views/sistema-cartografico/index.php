<?php
use Lcobucci\JWT\Signer\Hmac\Sha256;
use yii\helpers\Url;
use common\models\UtlEvento;
?>
<!DOCTYPE html>
<html dir="ltr" lang="it">
<head>
	<meta charset="utf-8"/>
	<link rel="shortcut icon" href="./cartografia/build/favicon.ico"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no"/>
	<meta name="theme-color" content="#000000"/>
	<link rel="manifest" href="./cartografia/build/manifest.json"/>
	<script src="https://maps.googleapis.com/maps/api/js?v=3&key=<?php echo Yii::$app->params['google_key'];?>&libraries=places"></script>
	<title>Sistema cartografico PC <?= Yii::$app->params['REGION_NAME'];?></title>
	<link href="./cartografia/build/static/css/2.d649c1e6.chunk.css" rel="stylesheet">
	<link href="./cartografia/build/static/css/main.716ad220.chunk.css" rel="stylesheet">
</head>
<body>
	<script type="text/javascript">
	<?php 

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



		$service_url = str_replace("backend/web/", "api/web/", Url::home(true));
	?>
	window.access_token = "<?php echo $token;?>"
	window.base_geoserver_url="<?php echo Yii::$app->params['geoserver_services_url'];?>" 
	window.base_api_url="<?php echo Yii::$app->urlManagerMap->getBaseUrl() ;?>/"
	window.google_key="<?php echo Yii::$app->params['google_key'];?>"
	window.base_foto_url="<?php echo Url::base(true);?>"
	window.base_lat="<?php echo Yii::$app->params['lat'];?>";
	window.base_lon="<?php echo Yii::$app->params['lng'];?>";
</script>
<noscript>Abilita javascript</noscript>
<div id="root"></div><script>!function(l){function e(e){for(var r,t,n=e[0],o=e[1],u=e[2],i=0,f=[];i<n.length;i++)t=n[i],c[t]&&f.push(c[t][0]),c[t]=0;for(r in o)Object.prototype.hasOwnProperty.call(o,r)&&(l[r]=o[r]);for(s&&s(e);f.length;)f.shift()();return p.push.apply(p,u||[]),a()}function a(){for(var e,r=0;r<p.length;r++){for(var t=p[r],n=!0,o=1;o<t.length;o++){var u=t[o];0!==c[u]&&(n=!1)}n&&(p.splice(r--,1),e=i(i.s=t[0]))}return e}var t={},c={1:0},p=[];function i(e){if(t[e])return t[e].exports;var r=t[e]={i:e,l:!1,exports:{}};return l[e].call(r.exports,r,r.exports,i),r.l=!0,r.exports}i.m=l,i.c=t,i.d=function(e,r,t){i.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:t})},i.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(r,e){if(1&e&&(r=i(r)),8&e)return r;if(4&e&&"object"==typeof r&&r&&r.__esModule)return r;var t=Object.create(null);if(i.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:r}),2&e&&"string"!=typeof r)for(var n in r)i.d(t,n,function(e){return r[e]}.bind(null,n));return t},i.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return i.d(r,"a",r),r},i.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},i.p="./cartografia/build/";var r=window.webpackJsonp=window.webpackJsonp||[],n=r.push.bind(r);r.push=e,r=r.slice();for(var o=0;o<r.length;o++)e(r[o]);var s=n;a()}([])</script><script src="./cartografia/build/static/js/2.a4061f72.chunk.js"></script><script src="./cartografia/build/static/js/main.7472bb32.chunk.js"></script>
</body></html>