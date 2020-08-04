<?php

use yii\helpers\Html;
use common\models\UtlEvento;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */

$this->title = 'Crea Evento';
$this->params['breadcrumbs'][] = ['label' => 'Lista eventi calamitosi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-evento-create">

    <h1><?php 
    if(!Yii::$app->request->get('idparent')) :
        echo Html::encode($this->title); 
    else:
        $parent = UtlEvento::findOne(Yii::$app->request->get('idparent'));
        if($parent) :

            $num_pr = $parent->num_protocollo;
            $type = $parent->tipologia->tipologia;

            echo "Crea nuovo fronte per evento n° protocollo ".Html::encode($num_pr)." - ".Html::encode($type)." <br />";

            $luogo = ($parent->indirizzo) ? $parent->indirizzo : $parent->luogo;

            if($parent->comune && $parent->comune->comune) : $luogo .= " ".$parent->comune->comune; endif;

            if($parent->comune && $parent->comune->provincia && $parent->comune->provincia->sigla) : $luogo .= " (".$parent->comune->provincia->sigla.")"; endif;

            echo Html::encode($luogo);

        else:
            echo Html::encode($this->title);
        endif;

    endif;

    ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'parent' => (isset($parent)) ? $parent : null,
        'tipoItems' => $tipoItems,
        'tasksSearchModel' => $tasksSearchModel,
        'tasksDataProvider' => $tasksDataProvider,
        'segnalazioniSearchModel' => $segnalazioniSearchModel,
        'segnalazioniDataProvider' => $segnalazioniDataProvider,
        'showLatLon' => $showLatLon
    ]) ?>

</div>
