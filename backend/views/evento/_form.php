<?php

use common\models\ConEventoExtra;
use common\models\LocComune;
use common\models\UtlEvento;
use common\models\UtlExtraSegnalazione;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\bootstrap\Tabs;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\LocCivico;
use common\models\LocIndirizzo;
use common\models\UtlTipologia;
//use Yii;
/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */
/* @var $form yii\widgets\ActiveForm */

function generate_checkbox_code($checkbox, $model)
{
    //Checked
    $checkedTag = '';
    foreach ($model->extras as $extra) {
        if (($extra->id == $checkbox->id)) {
            $checkedTag = 'checked';
            break;
        }
    }

    if (Yii::$app->request->post('UtlEvento') && isset(Yii::$app->request->post('UtlEvento')['extras'])) :
        foreach (Yii::$app->request->post('UtlEvento')['extras'] as $extra) {
            if ($extra == $checkbox->id) {
                $checkedTag = 'checked';
                break;
            }
        }
    endif;


    $detail = "";
    $detailExtra = ConEventoExtra::find()->where(['idevento' => $model->id, 'idextra' => $checkbox->id])->one();
    if ($checkbox->show_numero) {
        $val = @$detailExtra['numero'];
        $detail .= "N. <input style='width:40px' type='text' name='UtlEventoExtraInfo[{$checkbox->id}][numero]' value='{$val}' />";
    }

    //ShowNote
    if ($checkbox->show_note) {
        $val = @$detailExtra['note'];
        $detail .= "&nbsp;&nbsp;&nbsp;Note <input style='width:350px' type='text' name='UtlEventoExtraInfo[{$checkbox->id}][note]' value='{$val}' />";
    }

    //ShowNumNucleiFamiliari
    if ($checkbox->show_num_nuclei_familiari) {
        $val = @$detailExtra['numero_nuclei_familiari'];
        $detail .= "&nbsp;&nbsp; di cui N. nuclei familiari <input style='width:40px' type='text' name='UtlEventoExtraInfo[{$checkbox->id}][numero_nuclei_familiari]' value='{$val}' />";
    }

    //show_num_disabili
    if ($checkbox->show_num_disabili) {
        $val = @$detailExtra['numero_disabili'];
        $detail .= "&nbsp;&nbsp; N. disabili/persone non autosufficienti<input style='width:40px' type='text' name='UtlEventoExtraInfo[{$checkbox->id}][numero_disabili]' value='{$val}' />";
    }

    //show_num_sistemazione_parenti_amici
    if ($checkbox->show_num_sistemazione_parenti_amici) {
        $val = @$detailExtra['numero_sistemazione_parenti_amici'];
        $detail .= "N. presso parenti/amici<input style='width:40px' type='text' name='UtlEventoExtraInfo[{$checkbox->id}][numero_sistemazione_parenti_amici]' value='{$val}' />";
    }

    //show_num_sistemazione_strutture_ricettive
    if ($checkbox->show_num_sistemazione_strutture_ricettive) {
        $val = @$detailExtra['numero_sistemazione_strutture_ricettive'];
        $detail .= "&nbsp;&nbsp; N. presso str.ricettive<input style='width:40px' type='text' name='UtlEventoExtraInfo[{$checkbox->id}][numero_sistemazione_strutture_ricettive]' value='{$val}' />";
    }

    //show_num_sistemazione_area_ricovero
    if ($checkbox->show_num_sistemazione_area_ricovero) {
        $val = @$detailExtra['numero_sistemazione_area_ricovero'];
        $detail .= "&nbsp;&nbsp; N. in aree di ricovero <input style='width:40px' type='text' name='UtlEventoExtraInfo[{$checkbox->id}][numero_sistemazione_area_ricovero]' value='{$val}' />";
    }

    //show_num_persone_isolate
    if ($checkbox->show_num_persone_isolate) {
        $val = @$detailExtra['numero_persone_isolate'];
        $detail .= "&nbsp;&nbsp; N. persone isolate <input style='width:40px' type='text' name='UtlEventoExtraInfo[{$checkbox->id}][numero_persone_isolate]' value='{$val}' />";
    }

    //show_num_utenze
    if ($checkbox->show_num_utenze) {
        $val = @$detailExtra['numero_utenze'];
        $detail .= "&nbsp;&nbsp; n. utenze<input style='width:40px' type='text' name='UtlEventoExtraInfo[{$checkbox->id}][numero_utenze]' value='{$val}' />";
    }

    //$detail .= "</div>";

    //Template
    $ngInit = $checkedTag ? 'true' : 'false';



    if (!empty($checkbox->parent_id)) {

        echo  "<div ng-if='ctrl.checkBoxes[{$checkbox->parent_id}]' class='checkbox'>------ <label class='text-uppercase'><input ng-init='ctrl.checkBoxes[{$checkbox->id}]={$ngInit}' ng-model='ctrl.checkBoxes[{$checkbox->id}]' {$checkedTag} type='checkbox' name='UtlEvento[extras][]' value='{$checkbox->id}'>{$checkbox->voce}</label></div>";
        echo "<div ng-if='ctrl.checkBoxes[{$checkbox->id}]'>" . $detail . "</div>";
    } else {

        echo  "<div class='checkbox'><label class='text-uppercase'><input ng-init='ctrl.checkBoxes[{$checkbox->id}]={$ngInit}' ng-model='ctrl.checkBoxes[{$checkbox->id}]' {$checkedTag} type='checkbox' name='UtlEvento[extras][]' value='{$checkbox->id}'>{$checkbox->voce}</label></div>";
        if (!empty($detail)) {
            echo "<div ng-if='ctrl.checkBoxes[{$checkbox->id}]'><div class='color-gray'>" . $detail . "</div></div>";
        }
    }

    if ($checkbox->children && count($checkbox->children) > 0) {
        foreach ($checkbox->children as $ch) {
            generate_checkbox_code($ch, $model);
        }
    }
}

// @todo
// la bounding box andrebbe ricalcolata in automatico per essere configurabile
$js = "

$(document).ready(function(){

    if(google) {

        var options = {
          bounds: new google.maps.LatLngBounds(new google.maps.LatLng(" . Yii::$app->params['gmap_sw_latlng']['lat'] . "," . Yii::$app->params['gmap_sw_latlng']['lng'] . "), new google.maps.LatLng(" . Yii::$app->params['gmap_ne_latlng']['lat'] . "," . Yii::$app->params['gmap_ne_latlng']['lng'] . ")),
          strictBounds: true,
          /*types: ['address'],*/
          componentRestrictions: {country: 'it'}
        };

        var input = document.getElementById('indirizzo-google-api');
        var autocomplete = new google.maps.places.Autocomplete(input, options);
        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            var changeLatLngGoogleEvent = new CustomEvent('changedLatLng', {detail: { lat: place.geometry.location.lat(), lon: place.geometry.location.lng()} });
            
            window.dispatchEvent(changeLatLngGoogleEvent);
        })
    }

    $('[name=\"UtlEvento[address_type]\"]').change(function() {
        
        var val = $(this).val();
        if(val == 1 ) {
          $('._google_add').hide();
          $('.manual_add').hide();
          $('.toponimo_add').hide();
          $('.address_add').show();
        }
        if(val == 2 ) {
          $('.address_add').hide();
          $('._google_add').show();
          $('.toponimo_add').hide();
          $('.manual_add').hide();
        }
        if(val == 3 ) {
          $('.address_add').hide();
          $('._google_add').hide();
          $('.toponimo_add').hide();
          $('.manual_add').show();
        }
        if(val == 4 ) {
          $('._google_add').hide();
          $('.manual_add').hide();
          $('.address_add').hide();
          $('.toponimo_add').show();
        }
    })
})

";
$this->registerJs($js, $this::POS_READY);
?>
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=<?php echo Yii::$app->params['google_key']; ?>" type="text/javascript"></script>
<div class="utl-evento-form" ng-app="evento" ng-controller="eventoController as evtCtrl">

    <?php

    $unclosed_elicotteri = $model->getRichiesteElicotteroUndeleted()->where(
        'engaged = true AND (dataora_atterraggio is null OR n_lanci is null)'
    )
        ->orWhere([
            'edited' => 0
        ])->count();

    ?>


    <?php $form = ActiveForm::begin(); ?>
    <?php $stato = $model->stato ? $model->stato : 'Non gestito' ?>
    <div class="row" ng-init="evtCtrl.stato = '<?php echo $stato; ?>'">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h3 class="m10h">Stato: {{evtCtrl.stato}}</h3>
            <p>Cambia stato:</p>
            <a href="#" class="btn blue-td" ng-click="evtCtrl.stato = 'Non gestito'">Non gestito</a>
            <a href="#" class="btn green-td" ng-click="evtCtrl.stato = 'In gestione'">In gestione</a>
            <?php if (Yii::$app->user->can('closeEvento') && $unclosed_elicotteri == 0) : ?><a href="#" class="btn gray-td" ng-click="evtCtrl.stato = 'Chiuso'">Chiuso</a><?php endif; ?>
            <?php
            if (Yii::$app->user->can('closeEvento') && $unclosed_elicotteri > 0) {
            ?>
                <a href="#" class="btn gray-td">Chiuso</a> <span class="text-danger" style="display: block; font-size: 22px; font-weight: bold;">Non puoi chiudere l'evento, ci sono richieste elicottero da completare</span>
            <?php
            }
            ?>

            <?php echo $form->field($model, 'stato')
                ->hiddenInput(['ng-value' => 'evtCtrl.stato'])
                ->label(false) ?>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php

            /**
             * In base a tipologia vedo quali sottostati posso prendere
             */
            if (!empty($model->tipologia)) {

                $sottostati = $model->tipologia->getSottostati()->all();

                if (count($sottostati) > 0) {
                    echo $form->field($model, 'id_sottostato_evento', ['options' => ['class' => 'col-lg-12 no-pl']])->widget(Select2::classname(), [
                        'data' => ArrayHelper::map($sottostati, 'id', 'descrizione'),
                        'options' => [
                            'placeholder' => 'Seleziona lo stato interno...',
                            //'ng-disabled' => 'evtCtrl.stato != "In gestione"'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label('Stato interno');
                }
            }
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">

            <?php if (!empty($showLatLon)) : ?>

                <div class="row m5w m20h bg-grayLighter box_shadow">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
                        <h5 class="m10h text-uppercase color-red">Inserimento manuale latitudine e longitudine</h5>

                        <?php echo $form->field($model, 'lat', ['options' => ['class' => 'col-lg-6 no-pl']])->textInput([]); ?>
                        <?php echo $form->field($model, 'lon', ['options' => ['class' => 'col-lg-6 no-pr']])->textInput([]); ?>

                    </div>
                </div>

            <?php endif; ?>

            <?php if (!Yii::$app->request->get('idparent')) { ?>

                <div class="row m5w m20h bg-grayLighter box_shadow">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
                        <h5 class="m10h text-uppercase color-gray">Associa ad un evento esistente</h5>

                        <?php
                        $query = UtlEvento::find()->where(['!=', 'stato', 'Chiuso'])
                            ->andWhere('idparent IS NULL');
                        if ($model->id)  $query->andWhere(['!=', 'id', $model->id]);

                        echo $form->field($model, 'idparent', ['options' => ['class' => 'col-lg-8 no-pl']])->widget(Select2::classname(), [
                            'data' => ArrayHelper::map($query->all(), 'id', function ($model) {
                                $txt = "";
                                if (isset($model['indirizzo']) && $model['indirizzo'] != '') :
                                    $_comune = (isset($model['comune'])) ? @$model['comune']['comune'] : "";
                                    $txt = " - " . $model['indirizzo'] . ' - ' . $_comune;
                                else :
                                    $txt = isset($model['luogo']) ? " - " . $model['luogo'] : "";
                                endif;
                                $_tipo = (isset($model['tipologia'])) ? @$model['tipologia']['tipologia'] : "";
                                return 'Evento ' . $model['num_protocollo'] . ' - ' . $_tipo . $txt;
                            }),
                            'options' => [
                                'placeholder' => 'Seleziona un evento principale...',
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label('Eventi in corso');
                        ?>

                    </div>
                </div>

            <?php } ?>


            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h" ng-controller="AutocompleteController as ctrl" ng-init="init('<?php echo Yii::$app->request->csrfToken; ?>', evtCtrl)">
                    <h5 class="m10h text-uppercase color-gray">Dati evento</h5>
                    <?= $form->field($model, 'tipologia_evento')->dropDownList(
                        $tipoItems,
                        [
                            'id' => 'tipo-id',
                            'prompt' => 'Seleziona tipologia...'
                        ]
                    ); ?>

                    <?php
                    // Sotto tipo
                    if ($model->tipologia) :
                        $data = [];
                        $sottotipologie = UtlTipologia::find()
                            ->where(['idparent' => $model->tipologia->id])
                            ->andWhere(
                                '(valido_dal is null OR valido_dal <= CURRENT_DATE) AND (valido_al is null OR valido_al >= CURRENT_DATE)'
                            )->orderBy(['tipologia' => SORT_ASC])
                            ->asArray()
                            ->all();
                        foreach ($sottotipologie as $sottotipo) {
                            $data[$sottotipo['id']] = $sottotipo['tipologia'];
                        }
                    else :
                        $data = [];
                    endif;


                    $sottotipologia_colonna_di_fumo = UtlTipologia::find()->where(['UPPER(tipologia)' => 'COLONNA DI FUMO'])->one();
                    $id_sotto_colonna = ($sottotipologia_colonna_di_fumo) ? $sottotipologia_colonna_di_fumo->id : null;

                    $n_elicotteri = $model->getRichiesteElicotteroUndeleted()->count();

                    echo $form->field($model, 'sottotipologia_evento')->widget(
                        DepDrop::classname(),
                        [
                            'data' => $data,
                            'options' => [
                                'ng-model' => 'evtCtrl.sottotipologia_evento',
                                'ng-change' => 'changedSottotipo()',
                                'ng-init' => "loadInitSottotipologia(
                                    '" . $model->sottotipologia_evento . "', '" . $id_sotto_colonna . "', '" . $n_elicotteri . "')"
                            ],
                            'pluginOptions' => [
                                'depends' => ['tipo-id'],
                                'placeholder' => 'Seleziona sottotipo...',
                                'url' => Url::to(['get-sottotipologia'])
                            ]
                        ]
                    )->label('Sotto Tipologia Evento *');

                    if (empty($model->id_gestore_evento)) $model->id_gestore_evento = 0;
                    if (Yii::$app->FilteredActions->type == 'comunale' && empty($model->id_gestore_evento)) {
                        $model->id_gestore_evento = 1;
                    }
                    echo $form->field($model, 'id_gestore_evento', ['options' => [
                        'class' => 'col-lg-12 no-pl'
                    ]])->dropDownList(
                        ArrayHelper::map(
                            \common\models\EvtGestoreEvento::find()->all(),
                            'id',
                            'descrizione'
                        )
                    )->label('Gestore');
                    ?>

                    <?php

                    echo $form->field($model, 'has_coc')->radioList([
                        0 => 'No',
                        1 => 'Si'
                    ], [
                        'item' => function ($index, $label, $name, $checked, $value) use ($model) {
                            $selected = ($model->has_coc == $value) ? 'checked' : '';
                            $return = '<label class="modal-radio" style="margin-right: 12px">';
                            $return .= '<input name="' . $name . '" type="radio" value="' . $value . '" ' . $selected . ' style="margin-right: 8px;">';
                            $return .= '<span>' . ucwords($label) . '</span>';
                            $return .= '</label>';

                            return $return;
                        }
                    ])->label('Apertura COC');

                    ?>



                    <?php
                    if (Yii::$app->FilteredActions->showFieldComune) {
                        echo $form->field($model, 'idcomune', ['options' => ['class' => 'col-lg-12 no-pl no-pr']])->widget(Select2::classname(), [
                            'data' => ArrayHelper::map(LocComune::find()->where(
                                [
                                    Yii::$app->params['region_filter_operator'],
                                    'id_regione',
                                    Yii::$app->params['region_filter_id']
                                ]
                            )->andWhere(['soppresso' => false])->orderBy(['comune' => SORT_ASC])->all(), 'id', 'comune'),
                            'options' => [
                                'id' => 'id_comune',
                                'placeholder' => 'Seleziona un comune...',
                                'ng-model' => 'ctrl.comune',
                                //'ng-required' => 'ctrl.indirizzo',
                                //'ng-disabled' => 'ctrl.luogo',
                                'ng-change' => 'loadComune()',
                                'ng-init' => "loadInitComune('" . $model->idcomune . "')"
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                    } else {
                        echo $form->field($model, 'idcomune', ['options' => ['class' => 'col-lg-12 no-pr', 'ng-init' => "loadInitComune('" . Yii::$app->FilteredActions->comune->id . "')"]])->hiddenInput(['value' => Yii::$app->FilteredActions->comune->id])->label(false);
                    }

                    ?>




                    <?php

                    if ($model->idcomune) :
                        $data_i = [];
                        $indirizzi = LocIndirizzo::find()->where(['id_comune' => $model->idcomune])->asArray()->orderBy(['name' => SORT_ASC])->all();
                        foreach ($indirizzi as $indirizzo) {
                            $data_i[$indirizzo['id']] = $indirizzo['name'];
                        }

                        if ($model->id_civico) {
                            $data_c = [];
                            $civici = LocCivico::find()->where(['id_indirizzo' => $model->locCivico->id_indirizzo])->asArray()->orderBy(['civico' => SORT_ASC])->all();
                            foreach ($civici as $civico) {
                                $data_c[$civico['id']] = $civico['civico'];
                            }
                        } else {
                            $data_c = [];
                        }
                    else :
                        $data_i = [];
                        $data_c = [];
                    endif;

                    ?>

                    <?php

                    if (empty($model->address_type)) $model->address_type = 1;

                    echo $form->field($model, 'address_type')->radioList([
                        1 => 'Inserisci indirizzo',
                        2 => 'Usa google',
                        3 => 'Inserisci coordinate manualmente',
                        4 => 'Toponimi igm'
                    ], [
                        'item' => function ($index, $label, $name, $checked, $value) use ($model) {
                            $selected = ($model->address_type == $value) ? 'checked' : '';
                            $return = '<label class="modal-radio" style="margin-right: 12px">';
                            $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" ' . $selected . ' style="margin-right: 8px;">';
                            $return .= '<span>' . ucwords($label) . '</span>';
                            $return .= '</label>';

                            return $return;
                        }
                    ])->label('Tipo inserimento');
                    ?>

                    <div class="address_add" <?php if ($model->address_type != 1) echo 'style="display: none;"'; ?> <?php
                                                                                                                    if (!empty($model->indirizzo) && $model->address_type == 1) {
                                                                                                                        echo 'ng-init="loadInitAddress(\'' . addslashes($model->indirizzo) . '\')"';
                                                                                                                    }
                                                                                                                    ?>>

                        <div class="col-lg-8 no-pl field-id_address">
                            <label class="control-label" for="utlanagrafica-telefono">Via *</label>
                            <input ng-disabled="comune_name == ''" type="text" id="_via" ng-model="ctrl.address" ng-change="loadResults()" class="form-control" name="UtlEvento[address]">
                            <table class="auto_table" role="none">
                                <tr ng-repeat="result in results">
                                    <td ng-click="selectAddress(result)">{{result.via}}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-4 no-pl field-id_civico">
                            <label class="control-label" for="utlanagrafica-telefono">Civico *</label>
                            <select ng-disabled="comune_name == '' || avaible_civici.length === 0" id="_civico" ng-model="ctrl.civico" ng-change="selectCivico()" class="form-control" name="UtlEvento[civico]">
                                <option ng-repeat="civico in avaible_civici track by $index" value="{{civico.civico}}">
                                    {{civico.civico}}
                                </option>
                            </select>
                        </div>



                        <input type="hidden" name="UtlEvento[lat]" ng-model="ctrl.lat" />
                        <input type="hidden" name="UtlEvento[lon]" ng-model="ctrl.lon" />
                        <input type="text" style="visibility: hidden; width: 1px; height: 1px;" name="UtlEvento[cap]" ng-model="ctrl.cap" />


                    </div>
                    <div class="_google_add" <?php if ($model->address_type != 2) echo 'style="display: none;"'; ?>>
                        <?php $google_address = (!empty($model->luogo) && $model->address_type == 2) ? $model->luogo : '';
                        echo $form->field($model, 'google_address', ['options' => ['class' => 'col-lg-12 no-pr no-pl']])
                            ->textInput([
                                'id' => 'indirizzo-google-api',
                                'ng-model' => 'ctrl.google_address',
                                'ng-init' => "ctrl.google_address = '" . addslashes($google_address) . "'"
                            ])->label('Luogo orientativo/località'); ?>



                    </div>


                    <div class="manual_add" <?php if ($model->address_type != 3) echo 'style="display: none;"'; ?>>
                        <?php
                        $manual_address = ($model->address_type == 3) ? addslashes($model->indirizzo) : '';
                        ?>

                        <?php echo $form->field($model, 'manual_address', ['options' => ['class' => 'col-lg-12 no-pr no-pl']])
                            ->textInput([
                                'ng-model' => 'ctrl.manual_address',
                                'ng-init' => "ctrl.manual_address = '" . $manual_address . "'"
                            ]); ?>

                        <?php
                        echo $form->field($model, 'lat', ['options' => ['class' => 'col-lg-4 no-pl']])
                            ->textInput([
                                'ng-model' => 'ctrl.lat',
                                'ng-change' => 'updateManualMarker(ctrl.lat, ctrl.lon);',
                                'ng-init' => "ctrl.lat = '{$model->lat}'"
                            ])->label('Latitudine');
                        ?>

                        <?php
                        echo $form->field($model, 'lon', ['options' => ['class' => 'col-lg-4 no-pl']])
                            ->textInput([
                                'ng-model' => 'ctrl.lon',
                                'ng-change' => 'updateManualMarker(ctrl.lat, ctrl.lon);',
                                'ng-init' => "ctrl.lon = '{$model->lon}'"
                            ])->label('Longitudine');
                        ?>

                    </div>

                    <div class="toponimo_add" <?php if ($model->address_type != 4) echo 'style="display: none;"'; ?>>

                        <div class="col-lg-12 no-pl field-id_address">
                            <label class="control-label" for="toponimo_address">Nome toponimo *</label>
                            <input <?php
                                    if (!empty($model->luogo) && $model->address_type == 4) {
                                        echo 'ng-init="ctrl.toponimo = \'' . addslashes($model->luogo) . '\'"';
                                    }
                                    ?> type="text" id="toponimo_" ng-model="ctrl.toponimo" ng-change="loadToponimoResults()" ng-disabled="comune_name == ''" class="form-control" name="UtlEvento[toponimo_address]">
                            <table class="auto_table" role="none">
                                <tr ng-repeat="result in toponimo_results">
                                    <td ng-click="selectToponimo(result)">{{result.toponimo}}</td>
                                </tr>
                            </table>
                        </div>

                    </div>



                </div>
            </div>

            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
                    <h5 class="m10h text-uppercase color-gray">Informazioni aggiuntive</h5>


                    <?php
                    $extraArray = UtlExtraSegnalazione::find()
                        ->where('parent_id is null')
                        ->all();
                    foreach ($extraArray as $index => $checkbox) {
                        generate_checkbox_code($checkbox, $model);
                    }
                    ?>




                </div>
            </div>

        </div>

        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">

            <?php if (!$model->isNewRecord) : ?>

                <div ng-init="evtCtrl.setLatLon(<?php echo $model->lat; ?>, <?php echo $model->lon; ?>)" id="map-canvas-mod" class="site-index">
                    <ui-gmap-google-map events="map.events" center='{latitude: <?php echo $model->lat; ?>, longitude: <?php echo $model->lon; ?>}' zoom='10'>
                        <ui-gmap-marker coords="{latitude: evtCtrl.lat, longitude: evtCtrl.lon}" idkey="{evt_1}"> </ui-gmap-marker>
                    </ui-gmap-google-map>
                </div>

            <?php else : ?>
                <?php

                $center_lat = Yii::$app->params['lat'];
                $center_lon = Yii::$app->params['lng'];

                if (Yii::$app->request->get('idparent') && $parent) :
                    $center_lat = $parent->lat;
                    $center_lon = $parent->lon;
                endif;
                ?>
                <div id="map-canvas-mod" class="site-index" ng-init="evtCtrl.setLatLon(<?php echo $center_lat; ?>, <?php echo $center_lon; ?>)">

                    <ui-gmap-google-map events="map.events" center='{latitude: <?php echo $center_lat; ?>, longitude: <?php echo $center_lon; ?>}' zoom='10'>
                        <ui-gmap-marker coords="{latitude: evtCtrl.lat, longitude: evtCtrl.lon}" idkey="{evt_1}"></ui-gmap-marker>
                        <?php if (Yii::$app->request->get('idparent') && $parent) :
                        ?>
                            <ui-gmap-marker coords="{latitude: <?php echo $parent->lat; ?>, longitude: <?php echo $parent->lon; ?>}" idkey="{<?php echo $parent->id; ?>}"> </ui-gmap-marker>
                        <?php
                        endif;

                        ?>
                    </ui-gmap-google-map>

                </div>

            <?php endif ?>

            <?= $form->field($model, 'note')->textarea(['rows' => 7]) ?>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="form-group pull-right">

                <?php echo Html::a('Annulla', ['index'], ['class' => 'btn btn-default']) ?>
                <span ng-hide="needPopupEvt()">
                    <?= Html::submitButton($model->isNewRecord ? 'Salva' : 'Aggiorna', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </span>
                <span ng-show="needPopupEvt()">
                    <?= Html::submitButton($model->isNewRecord ? 'Salva' : 'Aggiorna', [
                        'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                        'data' => [
                            'confirm' => "Sicuro di voler chiudere questo evento? Risultano presenti richieste elicottero per una colonna di fumo",
                            'method' => 'post',
                        ]
                    ]) ?>
                </span>

            </div>
        </div>

    </div>




    <?php ActiveForm::end(); ?>

    <hr>

    <?php
    if (!$model->isNewRecord) :

        $widget_els = [];

        $widget_els[] = [
            'label' => 'Diario dell\'evento',
            'content' => $this->render('_partial_mattinale', ['model' => $model, 'tasksSearchModel' => $tasksSearchModel, 'tasksDataProvider' => $tasksDataProvider]),
            'active' => true
        ];

        if (!$model->idparent) :
            $widget_els[] = [
                'label' => 'Fronti',
                'content' => $this->render('_view_list_fronti', ['model' => $model]),
            ];
        endif;

        $widget_els[] = [
            'label' => 'Segnalazioni',
            'content' => $this->render('_partial_segnalazioni', ['segnalazioniSearchModel' => $segnalazioniSearchModel, 'segnalazioniDataProvider' => $segnalazioniDataProvider]),
        ];

        $widget_els[] = [
            'label' => 'Attivazioni',
            'content' => $this->render('_partial_ingaggi', ['ingaggiSearchModel' => $ingaggiSearchModel, 'ingaggiDataProvider' => $ingaggiDataProvider, 'model' => $model, 'hide_btn' => true])
        ];

        echo Tabs::widget([
            'items' => $widget_els
        ]);
    endif;
    ?>

</div>