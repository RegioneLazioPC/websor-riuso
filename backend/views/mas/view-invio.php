<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MasMessageTemplate */

$this->title = Yii::$app->formatter->asDate($model->data_invio);
$this->params['breadcrumbs'][] = ['label' => 'Messaggi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="mass-message-template-view">

    <h1><?= Html::encode($this->title) ?></h1>

    

    

    <?php 

    $stats = \common\models\MasSingleSend::calculateStats($model->id);
    echo '<br />';
    echo "<b>Totale contatti</b>: " . $stats['total'];
    echo '<br />'; 
    echo "<b>Destinatari contattati</b>: " . $stats['contacted'];
    echo '<br />'; 
    echo "<b>Invii a buon fine</b>: " . $stats['delivered'];
    echo '<br />';
    echo "<b>Destinatari non raggiunti</b>: " . $stats['not_delivered'];

    if(!empty(@$model->message->allerta)) {
        echo '<br />';
        echo "<b>Zone di allerta</b>: " . Html::encode($model->message->allerta->zone_allerta);
    }
    
    echo "<hr />";
    echo "<h5 style='margin-bottom: 5px;'>Report</h5><div class='row'>";
    $chs = ['mail'=>'Email','pec'=>'Pec','push'=>'Push','sms'=>'Sms','fax'=>'Fax'];
    foreach ($chs as $key => $value) {
        $kk = 'channel_' . $key;

        if(!empty($model->$kk) && $model->$kk == 1) {
            echo "<div class='col-md-2 col-sm-3 col-xs-4'>
            <p><b>". $value . "</b>";
            echo Html::a(
                'Excel', 
                ['/mas/export-invio', 
                    'id_invio'=>$model->id, 
                    'channel'=>$value, 
                    'result_type'=>'csv'
                ], 
                [
                    'target' => '_blank',
                    'class' => 'btn btn-success btn-xs',
                    'style' => 'margin-left: 10px; margin-right: 10px'
                ]
            );

            echo Html::a(
                'Tabella', 
                ['/mas/export-invio', 
                    'id_invio'=>$model->id, 
                    'channel'=>$value, 
                    'result_type'=>'pdf'
                ], 
                [
                    'target' => '_blank',
                    'class' => 'btn btn-warning btn-xs'
                ]
            );

            echo "</p></div>";
        }
    }


    echo "</div>";
    ?>

    

    <?php 
    echo $this->render('_partial_monitoraggio', [
        'model'=>$model        
    ]);
    ?>

</div>
