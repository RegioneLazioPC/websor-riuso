<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

use \common\models\ConSegnalazioneAppEvento;
/* @var $this yii\web\View */
/* @var $model common\models\UtlSegnalazione */

$this->title = 'Dettaglio Segnalazione Emergenza N. Protocollo ' . $model->num_protocollo;
$this->params['breadcrumbs'][] = ['label' => 'Segnalazioni', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$fotos = $model->getMedia()->joinWith('type')->where(['upl_tipo_media.descrizione'=>'Immagine segnalazione'])->all();
?>
<div class="utl-segnalazione-view" ng-app="segnalazione" ng-controller="segnalazioneViewController as ctrl">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
    	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">


                <?php if($model->stato != 'Verificata e trasformata in evento'): ?>

                    <?php if(Yii::$app->user->can('updateSegnalazione')) echo Html::a('<i class="fa fa-pencil p5w"></i> Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

                    <?php 
                    // segnalazione da app con consiglio
                    if(empty($model->segnalazioneAppEvento) || $model->segnalazioneAppEvento->confirmed == ConSegnalazioneAppEvento::STATO_REFUSED) {

                        if(Yii::$app->user->can('transformSegnalazioneToEvento')) echo Html::a('<i class="fa fa-star p5w"></i> Trasforma in evento', ['change-evento', 'id' => $model->id], [
                            'class' => 'btn btn-success',
                            'data' => [
                                'confirm' => 'Trasforma la segnalazione in un evento. Sei sicuro di voler procedere?',
                                'method' => 'post',
                            ],
                        ]);

                        echo " ";

                        if(Yii::$app->user->can('transformSegnalazioneToEvento')) echo Html::a('<i class="fa fa-link p5w"></i> Associa ad evento (vedi lista)', '#', [
                            'class' => 'btn btn-warning',
                            'ng-click' => 'ctrl.listEventi = !ctrl.listEventi'
                        ]);
                        
                    }
                    ?>
                        
                    <?php 

                    if(Yii::$app->user->can('closeSegnalazione')) echo Html::a('<i class="fa fa-trash p5w"></i> Chiudi segnalazione', ['close', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Sei sicuro di voler chiudere questa segnalazione?',
                            'method' => 'post',
                        ],
                    ]);

                    
                    ?>
                    
                <?php endif; ?>



                <?php echo Html::a('<i class="fa fa-ban p5w"></i> Annulla', ['index'], ['class' => 'btn btn-default']) ?>
                
                <?php
                if(!empty($model->segnalazioneAppEvento) && in_array( $model->segnalazioneAppEvento->confirmed, [ConSegnalazioneAppEvento::STATO_APPROVED, ConSegnalazioneAppEvento::STATO_PENDING]) ) {

                        ?>
                        <br /><br />
                        <h4 style="margin-bottom: 6px;">Il segnalatore suggerisce che la segnalazione sia riguardante l'evento: <?php echo Html::a( $model->segnalazioneAppEvento->evento->num_protocollo , ['/evento/view', 'id' => $model->id], [
                            'target' => '_blank'
                        ]);?></h4>
                        <?php
                        if(Yii::$app->user->can('transformSegnalazioneToEvento') && $model->segnalazioneAppEvento->confirmed == ConSegnalazioneAppEvento::STATO_PENDING) {
                            
                            echo Html::a('<i class="fa fa-check p5w"></i> Conferma', ['approve-segnalazione-app', 'id' => $model->id], [
                                'class' => 'btn btn-success',
                                'data' => [
                                    'confirm' => 'La segnalazione verrà associata all\'evento consigliato dal segnalatore. Sei sicuro di voler procedere?',
                                    'method' => 'post',
                                ],
                            ]);

                            echo " ";

                            echo Html::a('<i class="fa fa-close p5w"></i> Ignora', ['refuse-segnalazione-app', 'id' => $model->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => 'Sicuro di non voler associare l\'evento a quanto suggerito dal segnalatore',
                                    'method' => 'post',
                                ],
                            ]);

                        }
                    }
                    ?>

    	</div>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" ng-show="ctrl.listEventi">

            <table class="table table-striped" summary="Eventi">
                <thead>
                    <tr>
                        <th scope="col">N.Protocollo</th>
                        <th scope="col">Tipologia</th>
                        <th scope="col">Data e ora</th>
                        <th scope="col">Latitudine</th>
                        <th scope="col">Longitudine</th>
                        <th scope="col">Comune</th>
                        <th scope="col">Indirizzo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listEventi as $evento): ?>
                        <tr>
                            <td><?php echo Html::encode($evento->num_protocollo); ?></td>
                            <td><?php echo Html::encode($evento->tipologia->tipologia); ?></td>
                            <td><?php echo Yii::$app->formatter->asDatetime($evento->dataora_evento); ?></td>
                            <td><?php echo $evento->lat; ?></td>
                            <td><?php echo $evento->lon; ?></td>
                            <td>
                                <?php echo $evento->comune['comune'] ? Html::encode($evento->comune['comune'] . " (" . $evento->comune['provincia_sigla'] . ")") : ""; ?>
                            </td>
                            <td><?php echo Html::encode(@$evento->indirizzo); ?></td>
                            <td>
                                <?php
                                    if(Yii::$app->user->can('transformSegnalazioneToEvento')) echo Html::a('<i class="fa fa-link p5w"></i> Associa', ['attach-evento', 'id' => $model->id, 'idEvento' => $evento->id], [
                                        'class' => 'btn btn-info',
                                        'data' => [
                                            'confirm' => 'Associa ad un evento esistente. Sei sicuro di voler procedere?',
                                            'method' => 'post',
                                        ],
                                    ]);
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>


    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-8">
            <?php if(Yii::$app->user->can('listSegnalazioni')) echo '<span class="carto-link">'.
                        Html::a('Cartografia', ['/sistema-cartografico?lat='.$model->lat.'&lon='.$model->lon.'&visible_reports=1'], ['class' => 'btn btn-warning','style'=>'margin-bottom: 10px; margin-top: 10px']).'</span>';?>
            <div id="segnalazioni-map-canvas" class="site-index m20h">

                <ui-gmap-google-map center='{latitude: <?php echo $model->lat; ?>, longitude: <?php echo $model->lon; ?>}' zoom='10'>

                    <ui-gmap-marker coords="{latitude: <?php echo $model->lat; ?>, longitude: <?php echo $model->lon; ?>}" idkey="<?php echo $model->id; ?>"> </ui-gmap-marker>

                </ui-gmap-google-map>

            </div>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'num_protocollo',
                    [
                        'label' => 'Segnalatore',
                        'attribute' =>'idutente',
                        'format' => 'raw',
                        'value' => function($data){
                            $ret = Html::encode(@$data->nome_segnalatore.' '.@$data->cognome_segnalatore);
                            if($data->organizzazione){
                                $ret .= "<br />".Html::a($data->organizzazione->denominazione, ['organizzazione-volontariato/view', 'id'=>$data->organizzazione->id], ['class' => '']);
                            }
                            return $ret;
                        }
                    ],
                    [
                        'label' => 'Telefono',
                        'attribute' => 'telefono_segnalatore'
                    ],
                    [
                        'label' => 'Email',
                        'attribute' => 'email_segnalatore'
                    ],
                    [
                        'label' => 'Tipo Segnalazione',
                        'attribute' =>'tipologia_evento',
                        'value' => ($model->tipologia) ? $model->tipologia->tipologia : ""
                    ],
                    [
                        'label' => 'Sottotipo Segnalazione',
                        'attribute' =>'sottotipologia_evento',
                        'value' => ($model->sottotipologia) ? $model->sottotipologia->tipologia : ""
                    ],
                    'fonte',
                    'comune.comune',
                    'indirizzo:ntext',
                    'luogo:ntext',
                    'lat',
                    'lon',
                    'note:ntext',
                    [
                        'attribute' => 'dataora_segnalazione',
                        'value' => function($data){
                            return Yii::$app->formatter->asDatetime($data->dataora_segnalazione);
                        }
                    ],
                    [
                        'label' => 'Dettagli',
                        'format' => 'raw',
                        'attribute' => 'extras',
                        'value' => function($data){
                            $extras = [];
                            foreach ($data->extras as $index => $extra){
                                $extras[] = Html::encode($extra->voce);
                            }
                            $extrasString = implode('<br>',$extras);
                            return $extrasString;
                        }
                    ],
                    [
                        'label' => 'Orientamento',
                        'format' => 'raw',
                        'attribute' => 'extras',
                        'value' => function($data) use ($fotos) {
                            if(!empty($fotos)) {
                                try {
                                    $img = $fotos[0];
                                    $o = $img->orientation;

                                    return '<p><span style="margin-right: 10px;">'.$o.' deg</span><img 
                                    src="'. Url::home(true) . 'images/arrow-up.png" 
                                    style="transform: rotate('.$o.'deg);"
                                    /></p>';
                                } catch(\Exception $e) {

                                }

                            } else {
                                return "-";
                            }

                        }
                    ]
                ],
            ]) ?>
             <div>
                <?php 
                $attachments = $model->getMedia()->joinWith('type')->where(['upl_tipo_media.descrizione'=>'Allegato segnalazione'])->all();
                foreach ($attachments as $attachment) {
                    ?>
                    <div>
                        <?php 
                        echo Html::a(
                            'Scarica allegato ' . $attachment->id . ' - ' . date("d-m-Y", strtotime($attachment->date_upload)) . ' <i class="fa fa-download p5w"></i>',
                            ['/media/view-media', 'id' => $attachment->id],
                            ['class' => 'btn btn-info btn-block m30h' ,'target' => '_blank']
                        );
                        ?>
                    </div>
                    <?php
                }
                ?>
                

                <?php
                if(Yii::$app->user->can('removeSegnalazione')){
                    echo Html::a('Elimina segnalazione', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'style' => 'margin-top: 10px;',
                        'data' => [
                            'confirm' => 'Sei sicuro di voler eliminare questa segnalazione? Questa azione è irreversibile',
                            'method' => 'post',
                        ],
                    ]);
                }
                ?>
            </div>
    	</div>

        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-4">
            <?php 
            foreach ($fotos as $foto) {
                ?>
                <div>
                    <img id="image" src="<?php echo Url::to(['/media/view-media', 'id' => $foto->id]); ?>" alt="" class="img-responsive">
                    <?php 
                    if(!empty($foto->exif)) {
                        $exif = json_decode($foto->exif, true);
                        \common\utils\Loopper::printArray( $exif );
                    }
                    ?>
                </div>
                <?php
            }
            ?>
            <?php if(!empty($model->foto)): ?>
                <div>
                    <img id="image" src="<?php echo $model->foto; ?>" alt="" class="img-responsive" width="100%">
                </div>
            <?php endif; ?>

            

        </div>
    </div>



</div>
