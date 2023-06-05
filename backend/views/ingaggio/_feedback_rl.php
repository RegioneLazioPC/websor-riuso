<?php 

use yii\helpers\Html;
use yii\widgets\DetailView;

$volontari_inseriti = $model->getVolontari()->all();
$can_view_feedback_attivazioni = Yii::$app->user->can('listAttivazioniToCheck');
$can_edit_feedback_attivazioni = Yii::$app->user->can('editAttivazioniToCheck');

$col_w = $can_view_feedback_attivazioni ? '6' : '12';

function is_volontario_inserito($id,$volontari_inseriti) {
    foreach ($volontari_inseriti as $v) {
        if($v->id == $id) return true;
    }
    return false;
}

if($model->rl_feedback_to_check == 1 && $can_edit_feedback_attivazioni) {
	$model->rl_feedback_to_check = 0;
	if (!$model->save()) {
		echo json_encode($model->getErrors());
	}
}

?>
<div class="col-xs-12 col-sm-12 col-md-<?php echo $col_w;?> col-lg-<?php echo $col_w;?> p10w p10h">
    <h5 class="m10h text-uppercase color-gray">Feedback LEGALE RAPPRESENTANTE</h5>
    <?php if (Yii::$app->session->hasFlash('error_feedback_attivazione_'.$model->id)): ?>
	    <div class="alert alert-danger alert-dismissable">
	         <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
	         <?= Yii::$app->session->getFlash('error_feedback_attivazione') ?>
	    </div>
	<?php endif; ?>
    <?php if(!empty($model->feedbackRl)) {
    echo DetailView::widget([
            'model' => $model->feedbackRl,
            'attributes' => [
                [
                    'label'  => 'Codice fiscale RL',
                    'attribute' => 'rl_codfiscale'
                ],
                [
                    'label'  => 'Risorsa',
                    'format'=>'raw',
                    'value' => function($data) use ($can_edit_feedback_attivazioni, $from, $model) {
                        if(!empty($data->risorsa)) {

                        	$add = '';

                        	$model_id = null;
	                        $risorsa = null;
                        	if(!empty($model->idautomezzo)) {
                        		$risorsa = \common\models\UtlAutomezzo::findOne($data->risorsa['id']);
                        		$model_id = $model->idautomezzo;
                        	} else {
                        		$risorsa = \common\models\UtlAttrezzatura::findOne($data->risorsa['id']);
                        		$model_id = $model->idattrezzatura;
                        	}
                        	if(!$risorsa) return '-';

                        	if($can_edit_feedback_attivazioni /*&& $from == 'update'*/ && $risorsa->id != $model_id){
	                            $add .= Html::a('Sostituisci la risorsa', [
	                                        'ingaggio/use-feedback-resource',
	                                        'from' => $from,
	                                        'id' => $data->id_ingaggio
	                                    ], [
	                                            'class' => 'btn btn-xs btn-success',
	                                            'data' => [
	                                                'confirm' => 'Sicuro di voler sostituire la risorsa con quella segnalata dal legale rappresentante?',
	                                                'method' => 'post'
	                                            ],
	                                        ]) . '<br /><br />';
	                        }

                            return "ID: " . $risorsa->id . " " . (!empty($risorsa->tipo) ? $risorsa->tipo->descrizione : '') . " " . $add;
                        }

                        return '-';
                    }
                ],
                [
                    'label'  => 'Volontari',
                    'format' => 'raw',
                    'value' => function($data) use ($volontari_inseriti, $can_edit_feedback_attivazioni, $from) {
                        $add = '';

                        if($can_edit_feedback_attivazioni /*&& $from == 'update'*/ && !empty($data->volontari) && count($data->volontari) > 1){
                            $add .= Html::a('Aggiungi tutti i volontari', [
                                        'ingaggio/add-all-feedback-volontari',
                                        'from' => $from,
                                        'id' => $data->id_ingaggio
                                    ], [
                                            'class' => 'btn btn-xs btn-success',
                                            'data' => [
                                                'confirm' => 'Sicuro di voler aggiungere i volontari alla lista?',
                                                'method' => 'post'
                                            ],
                                        ]) . '<br /><br />';
                        }

                        $list = '';
                        if(!empty($data->volontari)) {
                           
                            foreach ($data->volontari as $volontario) {

                                $can_add = !is_volontario_inserito($volontario['id'], $volontari_inseriti) && $can_edit_feedback_attivazioni /*&& $from == 'update'*/;
                                try {
                                    $list .= '<li>'.$volontario['anagrafica']['nome'].'
                                        '.$volontario['anagrafica']['cognome'].'<br />
                                        <b>'.$volontario['anagrafica']['codfiscale'].'</b><br />
                                        <b>Rimborso:</b> '.(@$volontario['refund'] ? 'Si' : 'No').'
                                        '.($can_add ? Html::a('Aggiungi', [
                                            'ingaggio/add-feedback-volontario',
                                            'from' => $from,
                                            'id_volontario' => $volontario['id'],
                                            'id' => $data->id_ingaggio
                                        ], [
                                                'class' => 'btn btn-xs btn-danger',
                                                'data' => [
                                                    'confirm' => 'Sicuro di voler aggiungere questo volontario?',
                                                    'method' => 'post'
                                                ],
                                            ]) : '').'
                                        <br /><br />
                                    </li>';
                                } catch(\Exception $e) {
                                    Yii::error($e->getMessage());
                                }
                            }
                        }

                        return '<div>'.$add.'<ul class="list-unstyled">'.$list.'</ul></div>';
                    }
                ],
                'note:ntext',
                [
                    'label' => 'Stato',
                    'attribute' => 'stato',
                    'value' => function($model) {
                        if($model->stato != 2) return $model->getStato();

                        $ret = $model->getStato();
                        $ret .= " - " . $model->getMotivazioneRifiuto();
                        
                        return $ret;
                    }
                ],
                [
                    'label'  => 'Data',
                    'attribute' => 'created_at',
                    'format' => 'datetime'
                ]
            ],
        ]);
    } else {
        ?>
        <div class="alert alert-danger" role="alert">
        	<?php 
        	echo ($model->rl_to_check == 1) ? "Il Legale Rappresentante non ha visualizzato l'attivazione" : "Il Legale Rappresentante non ha fornito feedback";
        	?>
        </div>
        <?php
    }
    ?>
</div>