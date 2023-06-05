<?php

use common\models\DbSession;
use common\models\UtlSalaOperativa;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlOperatorePcSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$roles = [];
foreach (Yii::$app->authManager->getRoles() as $role => $detail) {
    $roles[$role] = $role;
}

$this->title = 'Operatori WEB SOR';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-operatore-pc-index">


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive'=>true,
        'hover'=>true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'panel' => [
            'heading'=>'<h2 class="panel-title"><i class="fa fa-users"></i> '.Html::encode($this->title).'</h2>',
            'before'=> (Yii::$app->user->can('createOperatore')) ? Html::a('Nuovo utente', ['create'], ['class' => 'btn btn-success']) : "",
        ],
        'columns' => [
            [
                'attribute' => 'sessionOperatore',
                'label' => 'Stato',
                'format' => 'raw',
                'hAlign' => GridView::ALIGN_CENTER,
                'filter'=> Html::activeDropDownList($searchModel, 'sessionOperatore', [0=>'Non attivo', 1=>'Attivo'], ['class' => 'form-control','prompt' => 'Tutti gli stati']),
                'value' => function ($data) {
                    $dataLimite = date('Y-m-d H:i:s', strtotime("-1 hour"));
                    // considera l'ultima ora come limite per filtrare gli operatori online
                    if (!empty($data->sessionOperatore['id_user']) && ($data->sessionOperatore['last_write'] > $dataLimite)) {
                        return '<span class="fa fa-user" style="color:green"></span>';
                    } else {
                        return '<span class="fa fa-user" style="color:red"></span>';
                    }
                }
            ],
            /*

                SALA OPERATIVA RIMOSSA
                [
                    'label' => 'Sala Operativa',
                    'attribute' => 'idsalaoperativa',
                    'filter'=> Html::activeDropDownList($searchModel, 'idsalaoperativa', ArrayHelper::map(UtlSalaOperativa::find()->asArray()->all(), 'id', 'nome'), ['class' => 'form-control','prompt' => 'Tutte le sale']),
                    'value' => function($model){
                        if(isset($model->salaoperativa)){
                            return $model->salaoperativa->nome;
                        }
                    }
                ],
            */
            [
                'label' => 'Nome',
                'attribute' => 'nome',
                'value' => function ($data) {
                    if (!empty($data['anagrafica'])) {
                        return $data['anagrafica']['nome'];
                    }
                }
            ],
            [
                'label' => 'Cognome',
                'attribute' => 'cognome',
                'value' => function ($data) {
                    if (!empty($data['anagrafica'])) {
                        return $data['anagrafica']['cognome'];
                    }
                }
            ],
            [
                'label' => 'Matricola',
                'attribute' => 'matricola',
                'value' => function ($data) {
                    if (!empty($data['anagrafica'])) {
                        return $data['anagrafica']['matricola'];
                    }
                }
            ],
            [   'label' => 'Ruolo',
                'attribute' => 'ruolo',
                'filter'=> Html::activeDropDownList(
                    $searchModel,
                    'ruolo',
                    $roles,
                    ['class' => 'form-control','prompt' => 'Tutti']
                ),
                'value' => function ($data) {
                    return ($data->ruolo) ? $data->ruolo : "";
                }
            ],
            [   'label' => 'Status',
                'attribute' => 'status',
                'filter'=> Html::activeDropDownList(
                    $searchModel,
                    'status',
                    [10 => 'ATTIVO', -1=>'BLOCCATO'],
                    ['class' => 'form-control','prompt' => 'Tutti']
                ),
                'value' => function ($data) {
                    return (!empty($data->user)) ? \common\models\User::getStatusString($data->user->status) : "";
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => (Yii::$app->user->can('deleteOperatore')) ? '{view} {update} {delete} {lock-unlock}' : '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if (Yii::$app->user->can('viewOperatore')) {
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio operatore'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        } else {
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if (Yii::$app->user->can('updateOperatore')) {
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica operatore'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        } else {
                            return '';
                        }
                    },
                    'lock-unlock' => function ($url, $model) {
                        if (Yii::$app->user->can('deleteOperatore') || empty($model->user)) {
                            if ($model->user && $model->user->status == \common\models\User::STATUS_DELETED) {
                                return Html::a('<span style="margin-left: 6px; position: relative; top: 2px;" class="glyphicon glyphicon-upload"></span>&nbsp;&nbsp;', ['operatorepc/lock-unlock?id=' . $model->id ], [
                                    'title' => Yii::t('app', 'Attiva operatore'),
                                    'data-toggle' => 'tooltip'
                                ]);
                            } else {
                                return Html::a('<span style="margin-left: 6px; position: relative; top: 2px;" class="glyphicon glyphicon-minus-sign"></span>&nbsp;&nbsp;', ['operatorepc/lock-unlock?id=' . $model->id], [
                                    'title' => Yii::t('app', 'Blocca operatore'),
                                    'data-toggle' => 'tooltip'
                                ]);
                            }
                        } else {
                            return '';
                        }
                    }
                ]
            ],
        ],
    ]); ?>
</div>
