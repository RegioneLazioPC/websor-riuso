<?php

use common\models\ConOperatoreEvento;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */

$this->title = 'Assegna operatori';
$this->params['breadcrumbs'][] = ['label' => 'Eventi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$countOperatoriAssegnati = ConOperatoreEvento::find()->where(['idevento' => $model->id])->count();
?>
<div class="utl-evento-view">

    <h1><?= Html::encode($this->title) ?> all'evento calamitoso N. Protocollo <?php echo $model->num_protocollo ?></h1>
    <h2 class="m20h">Operatori totali <?php echo count($operatori) ?> assegnati a questo evento <?php echo $countOperatoriAssegnati; ?> </h2>

    <p>
        <?= Html::a('Annulla', ['index'], [
            'class' => 'btn btn-default'
        ]) ?>
    </p>

    <div class="row">
    	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-12">

            <table class="table table-striped" summary="Operatori">
                <thead>
                <tr>
                    <th scope="col">Matricola</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Cognome</th>
                    <th scope="col">Sala operativa</th>
                    <th scope="col">Eventi assegnati</th>
                    <th scope="col">Azione</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($operatori as $operatore): ?>
                        <tr>
                            <td><?php echo Html::encode(@$operatore->anagrafica->matricola); ?></td>
                            <td><?php echo Html::encode(@$operatore->anagrafica->nome); ?></td>
                            <td><?php echo Html::encode(@$operatore->anagrafica->cognome); ?></td>
                            <td><?php echo Html::encode(@$operatore->salaoperativa->nome); ?></td>
                            <td>
                                <?php
                                    $operatoreEventiCount = ConOperatoreEvento::find()
                                                ->where(['idoperatore' => $operatore->id])
                                                ->count();
                                    echo $operatoreEventiCount;
                                ?>
                            </td>
                            <td>
                                <?php

                                    $operatoreCheck = ConOperatoreEvento::find()->where(['idoperatore' => $operatore->id, 'idevento' => $model->id])->all();

                                    if(!empty($operatoreCheck)){

                                        echo Html::a('<i class="fa fa-unlink"></i> Rimuovi', ['remove-event'], [
                                            'class' => 'btn btn-danger',
                                            'data' => [
                                                'confirm' => "Rimuovi operatore {$operatore->anagrafica->nome} {$operatore->anagrafica->cognome} dall'evento. Sei sicuro di voler procedere?",
                                                'method' => 'post',
                                                'params' => [
                                                    'idoperatore' => $operatore->id,
                                                    'idevento'    => $model->id
                                                ],
                                            ],
                                        ]);
                                    } else {

                                        echo Html::a('<i class="fa fa-link"></i> Assegna', ['assign-event'], [
                                            'class' => 'btn btn-success',
                                            'data' => [
                                                'confirm' => "Assegna evento a {$operatore->anagrafica->nome} {$operatore->anagrafica->cognome}. Sei sicuro di voler procedere?",
                                                'method' => 'post',
                                                'params' => [
                                                    'idoperatore' => $operatore->id,
                                                    'idevento'    => $model->id
                                                ],
                                            ],
                                        ]);
                                    }

                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

    	</div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">

    	</div>
    </div>



</div>
