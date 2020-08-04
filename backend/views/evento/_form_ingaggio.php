<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

use common\models\UtlAttrezzaturaTipo;
use common\models\UtlAutomezzoTipo;
use common\models\LocProvincia;
use common\models\LocComune;
use common\models\UtlCategoriaAutomezzoAttrezzatura;
use common\models\TblSezioneSpecialistica;
use common\models\VolOrganizzazione;
use yii\widgets\Pjax;

use common\models\UtlEvento;
use common\models\UtlAggregatoreTipologie;
use common\models\tabelle\TblTipoRisorsaMeta;

$meta_keys = "[]";
try {
    // creo un array di chiavi da passare al javascript
    // tanto le chiavi sono tutte alfanumeriche
    $meta_keys = "[{'". implode("'},{'", array_map( function($meta){
        return Html::encode( $meta->key ) . "':'" . str_replace("'", "\\'", Html::encode( $meta->label ) );
    }, TblTipoRisorsaMeta::find()->where(['show_in_column'=>1])->all() ) ) . "'}]";
} catch(\Exception $e) {
    Yii::error($e->getMessage() . " in UtlIngaggio::23");
}

/* @var $this yii\web\View */
/* @var $model common\models\UtlIngaggio */
/* @var $form yii\widgets\ActiveForm */

$mappatura_categorie_aggregatori = UtlAggregatoreTipologie::find()->with(['categoria'])->all();
$temp_agg = [];
foreach ($mappatura_categorie_aggregatori as $aggregatore) {
    $temp_agg[$aggregatore->id] = $aggregatore->categoria->id;
}

$mappatura_categorie_aggregatori = $temp_agg;

$js = "

window.refresh_select2_cat_options = function() {
        
    var select2Instance = $(\"#utlingaggiosearchform-id_tipologia\").data('select2');
    var resetOptions = select2Instance.options.options;
    $(\"#utlingaggiosearchform-id_tipologia\").select2('destroy').select2(resetOptions);

    var select2Instance = $(\"#utlingaggiosearchform-id_utl_automezzo_tipo\").data('select2');
    var resetOptions = select2Instance.options.options;
    $(\"#utlingaggiosearchform-id_utl_automezzo_tipo\").select2('destroy').select2(resetOptions);

    var select2Instance = $(\"#utlingaggiosearchform-id_utl_attrezzatura_tipo\").data('select2');
    var resetOptions = select2Instance.options.options;
    $(\"#utlingaggiosearchform-id_utl_attrezzatura_tipo\").select2('destroy').select2(resetOptions);

}

window.refresh_select2_type_options = function() {
        
    var select2Instance = $(\"#utlingaggiosearchform-id_utl_automezzo_tipo\").data('select2');
    var resetOptions = select2Instance.options.options;
    $(\"#utlingaggiosearchform-id_utl_automezzo_tipo\").select2('destroy').select2(resetOptions);

    var select2Instance = $(\"#utlingaggiosearchform-id_utl_attrezzatura_tipo\").data('select2');
    var resetOptions = select2Instance.options.options;
    $(\"#utlingaggiosearchform-id_utl_attrezzatura_tipo\").select2('destroy').select2(resetOptions);

}

";
$this->registerJs($js, $this::POS_READY);
?>
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=<?php echo Yii::$app->params['google_key'];?>" type="text/javascript"></script>
<div ng-app="ingaggi" ng-controller="ingaggioSearchController as ctrl" ng-init="inizializza(<?php echo $model->lat;?>,<?php echo $model->lon;?>,<?php echo $meta_keys;?>)">
    <div class="row m5w m20h bg-grayLighter box_shadow">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
            <div class="utl-ingaggio-form">

                <?php $form = ActiveForm::begin(); ?>

                <div class="row">
                    <div class="col-md-6">

                    <?php
                        $evento = UtlEvento::findOne($model->id_evento);
                        $categorie = UtlCategoriaAutomezzoAttrezzatura::find()->all();
                        $cat_array = [];
                        $selected_categorie = [];
                        $cat_array["0"] = "Tutti";
                        foreach ($categorie as $cat) {
                            if($evento->tipologia && $cat->id_tipo_evento == $evento->tipologia->id) $selected_categorie[] = $cat->id;

                            $cat_array[$cat->id] = $cat->descrizione;
                        }
                        echo $form->field($model, 'id_categoria', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                            'data' => $cat_array,
                            'showToggleAll'=>false,
                            'options' => [
                                'multiple'=>true,
                                'placeholder' => '',
                                'ng-model' => 'ctrl.id_categoria',
                                'ng-disabled' => 'ctrl.ctgr',
                                'ng-change' => 'changedCategory()',
                                'ng-init' => "setCategoriaDefault([".implode(",", $selected_categorie)."])"
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label('Categoria di mezzi/attrezzature');
                        
                    ?>

                   
                    </div>
                    <div class="col-md-6">
                    <?php
                        
                        $options = UtlAggregatoreTipologie::find()
                                    ->all();
                        $ang_opt = [];
                        $list = [];
                        foreach ($options as $opt) {
                            if(!isset($list[$opt->categoria->descrizione])) $list[$opt->categoria->descrizione] = [];

                            $list[$opt->categoria->descrizione][$opt->id] = $opt->descrizione;
                            $ang_opt[$opt->id] = [
                                'ng-disabled'=>'isDisabledTipologiaOption('.$opt->id.', '.$opt->categoria->id.')',
                                'id' => 'opt_type_mezzo_'.$opt->id
                            ];
                        }

                        echo $form->field($model, 'id_tipologia', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                            'data' => $list,
                            'showToggleAll'=>false,
                            'options' => [
                                'multiple'=>true,
                                'placeholder' => '',
                                'ng-model' => 'ctrl.id_tipologia',
                                'ng-change' => 'changedType()',
                                'ng-init' => "",
                                'options' => $ang_opt
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label('Sottocategoria di mezzi/attrezzature');
                        
                    ?>
                    
                    </div>
                </div>
                
                
                <div class="row">
                    <div class="col-md-6">
                        <?php

                        $ang_opt = [];
                        $list = [];
                        

                        $automezzo = UtlAutomezzoTipo::find()->with(['aggregatori','aggregatori.categoria'])->asArray()->all();
                        foreach ($automezzo as $a) {
                            $aggregatori = [];
                            $categorie = [];
                            
                            if($a['aggregatori']):

                                foreach ($a['aggregatori'] as $agg) {
                                    $aggregatori[] = $agg['id'];
                                    $categorie[] = $agg['categoria']['id'];
                                    if(!isset($cats[$agg['categoria']['id']])) $cats[$agg['categoria']['id']] = [];
                                }
                            endif;          

                            $categorie = array_unique($categorie);                   
                            
                            $list[$a['id']] = $a['descrizione'];
                            $ang_opt[$a['id']] = [
                                'ng-disabled'=>'isDisabledTipoMezzoAttrezzatura('.$a['id'].', ['.implode(",",$categorie).'], ['.implode(",",$aggregatori).'], \''.str_replace("\"","\\\"",json_encode($mappatura_categorie_aggregatori)).'\')',
                                'id' => 'opt_type_'.$a['id']
                            ];
                        }


                        echo $form->field($model, 'id_utl_automezzo_tipo', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                            'data' => $list,
                            'showToggleAll'=>false,
                            'options' => [
                                'multiple'=>true,
                                //'tabindex' => false,
                                'placeholder' => '',
                                'ng-model' => 'ctrl.id_utl_automezzo_tipo',
                                'ng-init' => "ctrl.id_utl_automezzo_tipo = '".$model->id_utl_automezzo_tipo."'",
                                'options' => $ang_opt
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label('Tipo di mezzo');
                        
                    ?>

                    </div>
                    <div class="col-md-6">
                        <?php
                        $ang_opt = [];
                        $list = [];
                        

                        $attrezzatura = UtlAttrezzaturaTipo::find()->with(['aggregatori','aggregatori.categoria'])->asArray()->all();
                        foreach ($attrezzatura as $a) {
                            $aggregatori = [];
                            $categorie = [];
                            
                            if($a['aggregatori']):

                                foreach ($a['aggregatori'] as $agg) {
                                    $aggregatori[] = $agg['id'];
                                    $categorie[] = $agg['categoria']['id'];
                                    if(!isset($cats[$agg['categoria']['id']])) $cats[$agg['categoria']['id']] = [];
                                }
                            endif;          

                            $categorie = array_unique($categorie);                   
                            
                            $list[$a['id']] = $a['descrizione'];
                            $ang_opt[$a['id']] = [
                                'ng-disabled'=>'isDisabledTipoMezzoAttrezzatura('.$a['id'].', ['.implode(",",$categorie).'], ['.implode(",",$aggregatori).'], \''.str_replace("\"","\\\"",json_encode($mappatura_categorie_aggregatori)).'\')',
                                'id' => 'opt_type_'.$a['id']
                            ];
                        }

                        echo $form->field($model, 'id_utl_attrezzatura_tipo', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                            'data' => $list,
                            'showToggleAll'=>false,
                            'options' => [
                                'multiple'=>true,
                                'placeholder' => '',
                                'ng-model' => 'ctrl.id_utl_attrezzatura_tipo',
                                'ng-init' => "ctrl.id_utl_attrezzatura_tipo = '".$model->id_utl_attrezzatura_tipo."'",
                                'options' => $ang_opt
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label('Tipo di attrezzatura');
                    ?>
                    </div>
                    
                </div>

                <div class="row">
                    <div class="col-md-4">
                    <?php 
                        echo $form->field($model, 'id_provincia', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                            'data' => ArrayHelper::map( LocProvincia::find()->where([
                                Yii::$app->params['region_filter_operator'], 
                                'id_regione', 
                                Yii::$app->params['region_filter_id']
                            ])->all(), 'id', 'provincia'),
                            'options' => [
                                'multiple'=>true,
                                'placeholder' => '',
                                'ng-model' => 'ctrl.id_provincia',
                                'ng-disabled' => 'ctrl.pr',
                                'ng-init' => "ctrl.id_provincia = '".$model->id_provincia."'"
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label('Limita la ricerca alla provincia di');

                    ?>
                    </div>
                    <div class="col-md-4">
                    <?php 
                        echo $form->field($model, 'id_comune', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                            'data' => ArrayHelper::map( LocComune::find()->where([
                                Yii::$app->params['region_filter_operator'], 
                                'id_regione', 
                                Yii::$app->params['region_filter_id']
                            ])->all(), 'id', 'comune'),
                            'options' => [
                                'multiple'=>true,
                                'placeholder' => '',
                                'ng-model' => 'ctrl.id_comune',
                                'ng-disabled' => 'ctrl.cm',
                                'ng-init' => "ctrl.id_comune = '".$model->id_comune."'"
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label('Limita la ricerca al comune di');

                    ?>
                    </div>
                    <div class="col-md-4">
                        <div class=" field-utlingaggiosearchform-place">
                            <label class="control-label" for="utlingaggiosearchform-place">Indirizzo</label>
                            <input type="text" id="utlingaggiosearchform-place" class="form-control" options="autocompleteOptions" g-places-autocomplete 
                                ng-model="place">

                            <div class="help-block"></div>
                        </div>
                        
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                    <?php 
                        echo $form->field($model, 'specializzazioni', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                            'data' => ArrayHelper::map( TblSezioneSpecialistica::find()->all(), 'id', 'descrizione'),
                            'showToggleAll'=>false,
                            'options' => [
                                'multiple'=>true,
                                'placeholder' => '',
                                'ng-model' => 'ctrl.specializzazioni',
                                'ng-disabled' => 'ctrl.ctgr',
                                'ng-init' => "ctrl.specializzazioni = '".$model->specializzazioni."'"
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                    ?>
                    </div>
                    <div class="col-md-6">
                    <?php 
                        echo $form->field($model, 'num_comunale', ['options' => []])->textInput(['ng-model' => 'ctrl.num_comunale',]);
                    ?>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-md-6">
                    <?php
                    $orgs = VolOrganizzazione::find()->all();
                    $data = array();
                    foreach ($orgs as $org) {
                        $data[$org->id] = ($org->ref_id) ? $org->ref_id . " - " . $org->denominazione : $org->denominazione;
                    }
                    echo $form->field($model, 'id_organizzazione', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                            'data' => $data,
                            'showToggleAll'=>false,
                            'options' => [
                                'multiple'=>true,
                                'tabindex' => false,
                                'placeholder' => '',
                                'ng-model' => 'ctrl.id_organizzazione',
                                'ng-init' => "ctrl.id_organizzazione = '".$model->id_organizzazione."'"
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label('Nome organizzazione');

                    ?>
                    </div>
                    <div class="col-md-6">
                        <?php 

                        echo $form->field($model, 'distance', ['options'=>[
                            'class'=>'no-p',
                            'ng-model' => 'ctrl.distance',
                            'ng-disabled' => 'ctrl.no_d',
                            'ng-init' => "ctrl.distance = '".$model->distance."'"
                            ]])->dropDownList([ 
                            '2' => '2', 
                            '5' => '5', 
                            '10' => '10', 
                            '25' => '25', 
                            '50' => '50', 
                            '100' => '100', 
                            '200' => '200', 
                            ], ['prompt' => '', 'ng-model' => 'ctrl.distance',
                            'ng-disabled' => 'ctrl.no_d',
                            'ng-init' => "ctrl.distance = '".$model->distance."'"])->label('Limita distanza max a:') ?>
                    </div>
                </div>

               
                <div class="row">
                    <div class="col-md-6">
                        <?php  
                        echo $form->field($model, 'sort', ['options'=>[
                            'class'=>'no-p',
                            'ng-model' => 'ctrl.sort',
                            'ng-disabled' => 'ctrl.no_d',
                            'ng-init' => "ctrl.sort = '".$model->sort."'"
                            ]])->dropDownList([ 
                            'disponibilita_oraria_sede' => 'Disponibilità oraria sede',
                            'tipo_mezzo' => 'Tipo automezzo',
                            'tipo_attrezzatura' => 'Tipo attrezzatura',
                            'disponibile' => 'Disponibilità',
                            'organizzazione' => 'Organizzazione',
                            'specializzazione' => 'Specializzazione',
                            'id_sede' => 'Identificativo sede'
                            ], ['prompt' => '', 'ng-model' => 'ctrl.sort',
                            'ng-disabled' => 'ctrl.no_d',
                            'ng-init' => "ctrl.sort = '".$model->sort."'"])->label('Ordina per') ?>
                    </div>
                    <div class="col-md-6">
                        <?php 

                        echo $form->field($model, 'sort_order', ['options'=>[
                            'class'=>'no-p',
                            'ng-model' => 'ctrl.sort_order',
                            'ng-disabled' => 'ctrl.no_d',
                            'ng-init' => "ctrl.sort_order = '".$model->sort_order."'"
                            ]])->dropDownList([ 
                            'asc'=>'ascendente',
                            'desc' => 'discendente'
                            ], ['prompt' => '', 'ng-model' => 'ctrl.sort_order',
                            'ng-disabled' => 'ctrl.no_d',
                            'ng-init' => "ctrl.sort_order = '".$model->sort_order."'"])->label('Direzione ordinamento') ?>
                    </div>
                </div>
                

                <div class="form-group">
                    <?php echo Html::button('Annulla', ['class' => 'btn']) ?>
                    <?php echo Html::button('Cerca', ['class' => 'btn btn-success', 'ng-click' => 'submitForm()']) ?>
                </div>

                <?php ActiveForm::end(); ?>
                
            </div>
        </div>        
    </div>
    <div ng-if="initialized">
        <ui-gmap-google-map 
            center='{latitude: <?php echo $model->lat;?>, longitude: <?php echo $model->lon;?>}' 
            zoom='map.zoom' 
            ng-cloak>
            <ui-gmap-marker ng-repeat="marker in markers" coords="{latitude: marker.lat, longitude: marker.lon}" options="marker.options" events="marker.events" idkey="marker.id">      
                <ui-gmap-window>
                    <div class="popup">
                        <h2>{{marker.organizzazione}}</a></h2>                        
                    </div>
                </ui-gmap-window>                  
            </ui-gmap-marker>
        </ui-gmap-google-map>
    </div>
    <div  ng-if="initialized" class="grid-view hide-resize" style="margin-top: 30px;">
        <div class="panel panel-default">
            <div class="panel-heading">  
                    <div class="pull-right"></div>
                <h3 class="panel-title"><em class="glyphicon glyphicon-bell"></em> Organizzazioni di volontariato</h3>
            </div>
            
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="table-responsive">
                    <table class="table table-bordered" summary="Risultati">
                        <thead>
                            <tr>
                                <th scope="col">#Ter</th>
                                <th scope="col">Denominazione</th>
                                <th scope="col">Telefono</th>
                                <th scope="col">Orari</th>
                                <th scope="col">Risorsa</th>
                                <th scope="col">Meta dati</th>
                                <th scope="col">Specializzazione</th>
                                <th scope="col">Calcola tempo</th>
                                <th scope="col">Distanza stradale</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-class="{'bg-success': !result.ref_engaged && !hasEngagedInSession(result)}" ng-repeat="result in results" >
                                <td>{{result.codice_associazione}}</td>
                                <td style="max-width: 150px; white-space: normal;">{{result.denominazione_organizzazione}}</td>
                                <td style="max-width: 200px; white-space: normal;">
                                    {{result.ambito}}
                                    <p ng-repeat="value in result.contattiAttivazioni track by $index">{{getContact(value)}}</p>
                                </td>
                                <td >{{result.disponibilita_oraria_sede}}</td>
                                <?php 
                                // la specializzazione è corretto che non ci sia
                                ?>
                                <td style="max-width: 150px; white-space: normal;">{{result.ref_identifier}} {{result.ref_tipo_descrizione}}</td>
                                <td style="max-width: 200px; white-space: normal;">
                                    <ul style="list-style: none; padding: 0">
                                        <li ng-repeat="meta in getMeta(result.ref_meta)">{{
                                            meta
                                        }}</li>
                                    </ul>
                                </td>
                                <td style="max-width: 150px; white-space: normal;">{{getSpecializzazioni( result.sezioneSpecialistica ) }}</td>
                                <td ><a style="cursor:pointer" ng-click="calculate([result.lon, result.lat], result)" ng-if="!result.time"><span class="fa fa-calendar"></span></a>
                                    <span ng-if="result.time">{{result.time}}</span>
                                </td>
                                <td >{{distanceFormat(result.distance)}}</td>
                                <td >
                                    <?php 
                                    if(Yii::$app->user->can('createIngaggio')) :
                                    ?><a style="cursor:pointer" 
                                    ng-click="engage(result, <?php echo $model->id_evento;?>)">
                                        <span ng-if="!result.ref_engaged && !engaging && !hasEngagedInSession(result)" class="fa fa-plus"></span>
                                    </a>
                                    <?php 
                                    endif;                                       
                                    ?> 
                                </td>
                            </tr>
                        </tbody>

                    </table>
                    <p>{{"Pagina " + ctrl.page + " di " + total_pages}}<br />
                        Vai a pagina <input style="text-align: center;" type="number" min="1" ng-model="gotopage" max="{{total_pages}}" /> <em style="cursor: pointer; padding-left: 12px; padding-right: 12px;" class="fa fa-angle-right" ng-click="loadPage(gotopage)"></em>
                    </p>

                    <ul class="pagination">
                        <li class="prev"><span ng-click="prevPage()">«</span></li>
                        <li ng-repeat="n in show_pages" ng-class="{active: n == ctrl.page}" >
                            <a href="#" ng-click="loadPage(n)">{{n}}</a>
                        </li>
                        <li class="next"><a ng-click="nextPage()">»</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>        
</div>

