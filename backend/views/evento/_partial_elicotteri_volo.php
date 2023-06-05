<?php
/*use yii\helpers\Url;

?>
<div id="list-elicotteri-in-volo" style="margin-bottom: 12px;"></div>
<?php
// secondi di refresh
$seconds = (isset(Yii::$app->params['helicopters_load_list_seconds_interval'])) ? Yii::$app->params['helicopters_load_list_seconds_interval']*1000 : 60*1000;

$js = '$(document).ready(function() {
	
	$( "#list-elicotteri-in-volo" ).load( "'.Url::base(true).'/evento/elicotteri-in-volo-html" );

	setInterval( function refresh() {
		$( "#list-elicotteri-in-volo" ).load( "'.Url::base(true).'/evento/elicotteri-in-volo-html" );
	},'.$seconds.');
});
';

$this->registerJs($js, $this::POS_READY);
*/