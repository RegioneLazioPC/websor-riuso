<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlUtente */

$this->title = $model->nome.' '.$model->cognome;
$this->params['breadcrumbs'][] = ['label' => 'Lista Utenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-utente-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if(Yii::$app->user->can('updateAppUser')) echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?php if(Yii::$app->user->can('deleteAppUser')) echo Html::a('Cancella', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler cancellare questo utente?',
                'method' => 'post',
            ],
        ]) ?>
        <?php echo Html::a('Annulla', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'user.username',
                'label' => 'Username'
            ],
            'anagrafica.nome',
            'anagrafica.cognome',
            'anagrafica.codfiscale',
            [
                'attribute' => 'anagrafica.data_nascita',
                'format' => 'date',
            ],
            'anagrafica.luogo_nascita',
            'anagrafica.telefono',
            'anagrafica.email:email',
            'anagrafica.comuneResidenza.comune',
            [
                'attribute' => 'tipo',
                'label' => 'Tipo utente',
                'value' => function($model) {
                    return $model->getTipo();
                }
            ],
            [
                'attribute' => 'organizzazione',
                'label' => 'Organizzazione',
                'value' => function($model) {
                    if(!empty($model->organizzazione)) {
                        return implode("; ", array_map(function($org){ 
                            return $org->ref_id . " - " . $org->denominazione; 
                        }, $model->organizzazione));
                    } else {
                        return "-";
                    }
                }
            ],
            [
                'attribute' => 'specializzazione',
                'label' => 'Specializzazione',
                'value' => function($model) {
                    if(!empty($model->organizzazione)) {
                        $sezioni = [];
                        
                        foreach ($model->organizzazione as $org) {
                            foreach ($org->sezioneSpecialistica as $sezione) {
                                $sezioni[] = $sezione->descrizione;
                            }
                        }

                        return implode("; ", array_unique($sezioni));
                        
                    } else {
                        return '-';
                    }
                }
            ],
            [
                'label' => 'Codice',
                'attribute' => 'codice_attivazione'
            ],
            [
                'attribute' => 'stato',
                'label' => 'Stato',
                'format' => 'raw',
                'value' => function($data) {
                    return ($data->enabled == 1) ? '<i class="fa fa-circle text-success"></i> Abilitato' : '<i class="fa fa-circle text-danger"></i> Disabilitato';
                }
            ],
            [
                'attribute' => 'user.created_at',
                'format' => 'datetime',
                'label' => 'Data registrazione'
            ],
            [
                'attribute' => 'user.updated_at',
                'format' => 'datetime',
                'label' => 'Data ultimo aggiornamento',
            ]

        ],
    ]) ?>

</div>
