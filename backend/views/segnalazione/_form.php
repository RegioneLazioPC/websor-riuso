<?php

use common\models\ConSegnalazioneExtra;
use common\models\UtlExtraSegnalazione;
use common\models\UtlExtraUtente;
use common\models\UtlRuoloSegnalatore;
use common\models\UtlTipologia;
use common\models\UtlUtente;
use common\models\LocComune;
use common\models\LocIndirizzo;
use common\models\LocCivico;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\UtlAnagrafica;
use common\models\VolOrganizzazione;

use kartik\widgets\DepDrop;
use yii\helpers\Url;


$anagrafica = ($utente->anagrafica) ? $utente->anagrafica : new UtlAnagrafica();
if (!$utente->tipo) $utente->tipo = 1;

if (Yii::$app->request->post('UtlAnagrafica')) :
    $anagrafica->attributes = Yii::$app->request->post('UtlAnagrafica');
endif;
/* @var $this yii\web\View */
/* @var $model common\models\UtlSegnalazione */
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

    if (Yii::$app->request->post('UtlSegnalazione') && isset(Yii::$app->request->post('UtlSegnalazione')['extras'])) :
        foreach (Yii::$app->request->post('UtlSegnalazione')['extras'] as $extra) {
            if ($extra == $checkbox->id) {
                $checkedTag = 'checked';
                break;
            }
        }
    endif;

    //ShowNumero
    $detail = "";
    $detailExtra = ConSegnalazioneExtra::find()->where(['idsegnalazione' => $model->id, 'idextra' => $checkbox->id])->one();
    if ($checkbox->show_numero) {
        $val = @$detailExtra['numero'];
        $detail .= "N. <input style='width:40px' type='text' name='UtlSegnalazioneExtraInfo[{$checkbox->id}][numero]' value='{$val}' />";
    }

    //ShowNote
    if ($checkbox->show_note) {
        $val = @$detailExtra['note'];
        $detail .= "&nbsp;&nbsp;&nbsp;Note <input style='width:350px' type='text' name='UtlSegnalazioneExtraInfo[{$checkbox->id}][note]' value='{$val}' />";
    }

    //ShowNumNucleiFamiliari
    if ($checkbox->show_num_nuclei_familiari) {
        $val = @$detailExtra['numero_nuclei_familiari'];
        $detail .= "&nbsp;&nbsp; di cui N. nuclei familiari <input style='width:40px' type='text' name='UtlSegnalazioneExtraInfo[{$checkbox->id}][numero_nuclei_familiari]' value='{$val}' />";
    }

    //show_num_disabili
    if ($checkbox->show_num_disabili) {
        $val = @$detailExtra['numero_disabili'];
        $detail .= "&nbsp;&nbsp; N. disabili/persone non autosufficienti<input style='width:40px' type='text' name='UtlSegnalazioneExtraInfo[{$checkbox->id}][numero_disabili]' value='{$val}' />";
    }

    //show_num_sistemazione_parenti_amici
    if ($checkbox->show_num_sistemazione_parenti_amici) {
        $val = @$detailExtra['numero_sistemazione_parenti_amici'];
        $detail .= "N. presso parenti/amici<input style='width:40px' type='text' name='UtlSegnalazioneExtraInfo[{$checkbox->id}][numero_sistemazione_parenti_amici]' value='{$val}' />";
    }

    //show_num_sistemazione_strutture_ricettive
    if ($checkbox->show_num_sistemazione_strutture_ricettive) {
        $val = @$detailExtra['numero_sistemazione_strutture_ricettive'];
        $detail .= "&nbsp;&nbsp; N. presso str.ricettive<input style='width:40px' type='text' name='UtlSegnalazioneExtraInfo[{$checkbox->id}][numero_sistemazione_strutture_ricettive]' value='{$val}' />";
    }

    //show_num_sistemazione_area_ricovero
    if ($checkbox->show_num_sistemazione_area_ricovero) {
        $val = @$detailExtra['numero_sistemazione_area_ricovero'];
        $detail .= "&nbsp;&nbsp; N. in aree di ricovero <input style='width:40px' type='text' name='UtlSegnalazioneExtraInfo[{$checkbox->id}][numero_sistemazione_area_ricovero]' value='{$val}' />";
    }

    //show_num_persone_isolate
    if ($checkbox->show_num_persone_isolate) {
        $val = @$detailExtra['numero_persone_isolate'];
        $detail .= "&nbsp;&nbsp; N. persone isolate <input style='width:40px' type='text' name='UtlSegnalazioneExtraInfo[{$checkbox->id}][numero_persone_isolate]' value='{$val}' />";
    }

    //show_num_utenze
    if ($checkbox->show_num_utenze) {
        $val = @$detailExtra['numero_utenze'];
        $detail .= "&nbsp;&nbsp; n. utenze<input style='width:40px' type='text' name='UtlSegnalazioneExtraInfo[{$checkbox->id}][numero_utenze]' value='{$val}' />";
    }


    $ngInit = $checkedTag ? 'true' : 'false';



    if (!empty($checkbox->parent_id)) {

        echo  "<div ng-if='segCtrl.checkBoxes[{$checkbox->parent_id}]' class='checkbox'>------ <label class='text-uppercase'><input ng-init='segCtrl.checkBoxes[{$checkbox->id}]={$ngInit}' ng-model='segCtrl.checkBoxes[{$checkbox->id}]' {$checkedTag} type='checkbox' name='UtlSegnalazione[extras][]' value='{$checkbox->id}'>{$checkbox->voce}</label></div>";
        echo "<div ng-if='segCtrl.checkBoxes[{$checkbox->id}]'>" . $detail . "</div>";
    } else {

        echo  "<div class='checkbox'><label class='text-uppercase'><input ng-init='segCtrl.checkBoxes[{$checkbox->id}]={$ngInit}' ng-model='segCtrl.checkBoxes[{$checkbox->id}]' {$checkedTag} type='checkbox' name='UtlSegnalazione[extras][]' value='{$checkbox->id}'>{$checkbox->voce}</label></div>";
        if (!empty($detail)) {
            echo "<div ng-if='segCtrl.checkBoxes[{$checkbox->id}]'><div class='color-gray'>" . $detail . "</div></div>";
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

    $('[name=\"UtlSegnalazione[address_type]\"]').change(function() {
        
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
<hr>
<div class="utl-segnalazione-form" ng-app="segnalazione" ng-controller="segnalazioneFormController as segCtrl">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'] // important
    ]); ?>

    <?php echo $form->field($model, 'idutente')->hiddenInput(['value' => $utente->id])->label(false); ?>

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

            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
                    <h5 class="m10h text-uppercase color-gray">Dati segnalatore</h5>

                    <?= $form->field($model, 'nome_segnalatore', ['options' => ['class' => 'col-lg-6 no-pl']])->textInput(['autocomplete' => 'off']); ?>
                    <?= $form->field($model, 'cognome_segnalatore', ['options' => ['class' => 'col-lg-6 no-pr']])->textInput(['autocomplete' => 'off']); ?>
                    <?= $form->field($model, 'telefono_segnalatore', ['options' => ['class' => 'col-lg-6 no-pl']])->textInput(['autocomplete' => 'off'])->label('Telefono *'); ?>
                    <?= $form->field($model, 'email_segnalatore', ['options' => ['class' => 'col-lg-6 no-pr']])->textInput(['autocomplete' => 'off']); ?>

                    <?= $form->field($utente, 'tipo', ['options' => ['class' => 'col-lg-12 no-pr no-pl']])->dropDownList(
                        [1 => 'Cittadino Privato', 2 => 'Ente Pubblico', 3 => 'Organizzazione di Volontariato'],
                        [
                            'ng-model' => 'segCtrl.tipoSegnalatore',
                            'ng-change' => 'segCtrl.id_tipo_ente_pubblico = null; segCtrl.ruoloSegnalatore = null',
                            'ng-init' => "segCtrl.tipoSegnalatore = '" . $utente->tipo . "'"
                        ]
                    )->label('Tipologia segnalatore *'); ?>

                    <?= $form->field($utente, 'id_ruolo_segnalatore', ['options' => ['class' => 'col-lg-12 no-pr no-pl', 'ng-show' => 'segCtrl.tipoSegnalatore == 2']])->dropDownList(
                        ArrayHelper::map(UtlRuoloSegnalatore::find()->all(), 'id', 'descrizione'),
                        [
                            'prompt' => 'Seleziona un ruolo...',
                            'ng-model' => 'segCtrl.ruoloSegnalatore',
                            'ng-init' => "segCtrl.ruoloSegnalatore = '" . $utente->id_ruolo_segnalatore . "'"
                        ]
                    )->label('Ruolo del segnalatore (in caso di ente pubblico)'); ?>



                    <?php


                    if (Yii::$app->FilteredActions->type == 'comunale') {
                        $comune = LocComune::findOne(['codistat' => Yii::$app->params['websorCitiesIstat']]);
                        $orgs = VolOrganizzazione::find()
                            ->joinWith(['convenzione', 'volSedes'])
                            ->where(['vol_sede.comune' => $comune->id])
                            ->orWhere(['not', ['vol_convenzione.id' => null]])
                            ->orderBy(['ref_id' => SORT_ASC])
                            ->all();
                    } else {
                        $orgs = VolOrganizzazione::find()->orderBy(['ref_id' => SORT_ASC])->all();
                    }


                    $data = array();
                    foreach ($orgs as $org) {
                        $data[$org->id] = ($org->ref_id) ? $org->ref_id . " - " . $org->denominazione : $org->denominazione;
                    }
                    echo $form->field($model, 'id_organizzazione', ['options' => ['class' => 'col-lg-12 no-pr no-pl', 'ng-show' => 'segCtrl.tipoSegnalatore == 3']])->widget(Select2::classname(), [
                        'data' => $data,
                        'options' => [
                            'multiple' => false,
                            'tabindex' => false,
                            'placeholder' => 'Organizzazione...',
                            'ng-model' => 'segCtrl.id_organizzazione',
                            'ng-init' => "segCtrl.id_organizzazione = '" . $model->id_organizzazione . "'"
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);

                    ?>


                </div>
            </div>

            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h" ng-controller="AutocompleteController as ctrl" ng-init="init('<?php echo Yii::$app->request->csrfToken; ?>', segCtrl)">
                    <h5 class="m10h text-uppercase color-gray">Dati Segnalazione</h5>

                    <?php
                    $model->fonte = array_search($model->fonte, $model->getFonteArray());
                    echo $form->field($model, 'fonte', ['options' => ['class' => 'col-lg-12 no-pl no-pr']])->dropDownList(
                        $model->getFonteArray()
                    );
                    ?>


                    <?= $form->field($model, 'tipologia_evento')->dropDownList(
                        ArrayHelper::map(
                            UtlTipologia::find()->where(['idparent' => null])->orderBy(['tipologia' => SORT_ASC])->all(),
                            'id',
                            'tipologia'
                        ),
                        [
                            'id' => 'tipo-id',
                            'prompt' => 'Seleziona tipologia...'
                        ]
                    )->label('Tipologia segnalazione'); ?>

                    <?php
                    // Sotto tipo
                    if ($model->tipologia) :
                        $data = [null => 'Seleziona sottotipo...'];
                        $sottotipologie = UtlTipologia::find()->where(['idparent' => $model->tipologia->id])->orderBy(['tipologia' => SORT_ASC])->asArray()->all();
                        foreach ($sottotipologie as $sottotipo) {
                            $data[$sottotipo['id']] = $sottotipo['tipologia'];
                        }
                    else :
                        $data = [];
                    endif;

                    echo $form->field($model, 'sottotipologia_evento')->widget(DepDrop::classname(), [
                        'data' => $data,
                        'pluginOptions' => [
                            'depends' => ['tipo-id'],
                            'placeholder' => 'Seleziona sottotipo...',
                            'url' => Url::to(['evento/get-sottotipologia'])
                        ]
                    ])->label('Sottotipologia segnalazione');
                    ?>

                    <?php
                    if (Yii::$app->FilteredActions->showFieldComune) {

                        echo $form->field($model, 'idcomune', ['options' => ['class' => 'col-lg-12 no-pl']])->widget(Select2::classname(), [
                            'data' => ArrayHelper::map(LocComune::find()->where([
                                Yii::$app->params['region_filter_operator'],
                                'id_regione',
                                Yii::$app->params['region_filter_id']
                            ])->andWhere(['soppresso' => false])->orderBy(['comune' => SORT_ASC])->all(), 'id', 'comune'),
                            'options' => [
                                'id' => 'id_comune',
                                'placeholder' => 'Seleziona un comune...',
                                'ng-model' => 'ctrl.comune',
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
                    // Sotto tipo
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
                            <input ng-disabled="comune_name == ''" type="text" id="_via" ng-model="ctrl.address" ng-change="loadResults()" class="form-control" name="UtlSegnalazione[address]">
                            <table class="auto_table" role="none">
                                <tr ng-repeat="result in results">
                                    <td ng-click="selectAddress(result)">{{result.via}}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-4 no-pl field-id_civico">
                            <label class="control-label" for="utlanagrafica-telefono">Civico *</label>
                            <select ng-disabled="comune_name == '' || avaible_civici.length === 0" id="_civico" ng-model="ctrl.civico" ng-change="selectCivico()" class="form-control" name="UtlSegnalazione[civico]">
                                <option ng-repeat="civico in avaible_civici track by $index" value="{{civico.civico}}">
                                    {{civico.civico}}
                                </option>
                            </select>
                        </div>


                        <input type="hidden" name="UtlSegnalazione[lat]" ng-model="ctrl.lat" />
                        <input type="hidden" name="UtlSegnalazione[lon]" ng-model="ctrl.lon" />
                        <input type="text" style="visibility: hidden; width: 1px; height: 1px;" name="UtlSegnalazione[cap]" ng-model="ctrl.cap" />



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

                        <?php
                        echo $form->field($model, 'manual_address', ['options' => ['class' => 'col-lg-12 no-pr no-pl']])
                            ->textInput([
                                'ng-model' => 'ctrl.manual_address',
                                'ng-init' => " ctrl.manual_address = '" . $manual_address . "'"
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
                                    ?> type="text" id="toponimo_" ng-model="ctrl.toponimo" ng-change="loadToponimoResults()" ng-disabled="comune_name == ''" class="form-control" name="UtlSegnalazione[toponimo_address]">
                            <table class="auto_table" role="none">
                                <tr ng-repeat="result in toponimo_results">
                                    <td ng-click="selectToponimo(result)">{{result.toponimo}}</td>
                                </tr>
                            </table>
                        </div>

                    </div>



                    <?php echo $form->field($model, 'attachment', ['options' => ['class' => 'col-lg-12 no-pl no-pr']])->widget(FileInput::classname(), [])->label('Allega file (audio telefonata, email)'); ?>

                    <div class="col-lg-12 no-pl no-pr">
                        <?php
                        $attachments = $model->getMedia()->joinWith('type')->where(['upl_tipo_media.descrizione' => 'Allegato segnalazione'])->all();
                        foreach ($attachments as $attachment) {
                        ?>
                            <div>
                                <?php
                                echo Html::a(
                                    'Scarica allegato ' . $attachment->id . ' - ' . date("d-m-Y", strtotime($attachment->date_upload)) . ' <i class="fa fa-download p5w"></i>',
                                    ['/media/view-media', 'id' => $attachment->id],
                                    ['class' => 'btn btn-info btn-block m30h', 'target' => '_blank']
                                );
                                ?>
                            </div>
                        <?php
                        }
                        ?>

                    </div>



                    <?php if (!($model->isNewRecord) && ($model->fonte == 1)) : ?>

                        <?php echo $form->field($model, 'direzione')->textInput(['maxlength' => true]) ?>
                        <?php echo $form->field($model, 'distanza')->textInput() ?>

                    <?php endif; ?>
                </div>
            </div>

        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">

            <?php if (!$model->isNewRecord) : ?>
                <div ng-init="segCtrl.setLatLon(<?php echo $model->lat; ?>, <?php echo $model->lon; ?>)" id="segnalazioni-map-canvas" class="site-index m20h">

                    <ui-gmap-google-map events="map.events" center='{latitude: <?php echo $model->lat; ?>, longitude: <?php echo $model->lon; ?>}' zoom='13'>

                        <ui-gmap-marker coords="{latitude: segCtrl.lat, longitude: segCtrl.lon}" idkey="<?php echo $model->id; ?>"> </ui-gmap-marker>

                    </ui-gmap-google-map>

                </div>
            <?php else : ?>
                <?php

                $center_lat = !empty($model->lat) ? $model->lat : Yii::$app->params['lat'];
                $center_lon = !empty($model->lon) ? $model->lon : Yii::$app->params['lng'];

                ?>
                <div id="map-canvas-mod" class="site-index" ng-init="segCtrl.setLatLon(<?php echo $center_lat; ?>, <?php echo $center_lon; ?>)">

                    <ui-gmap-google-map events="map.events" center='{latitude: <?php echo $center_lat; ?>, longitude: <?php echo $center_lon; ?>}' zoom='10'>
                        <ui-gmap-marker coords="{latitude: segCtrl.lat, longitude: segCtrl.lon}" idkey="{evt_1}"></ui-gmap-marker>
                    </ui-gmap-google-map>

                </div>

            <?php endif ?>

            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
                    <h5 class="m10h text-uppercase color-gray">Informazioni sulla segnalazione</h5>

                    <?php

                    $extraArray = UtlExtraSegnalazione::find()
                        ->where('parent_id is null')
                        ->with(['children'])
                        ->all();
                    foreach ($extraArray as $index => $checkbox) {
                        generate_checkbox_code($checkbox, $model);
                    }
                    ?>


                </div>
            </div>

            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
                    <h5 class="m10h text-uppercase color-gray">Ulteriori informazioni sulla segnalazione</h5>

                    <?= $form->field($model, 'note')->textarea(['rows' => 4]) ?>

                </div>
            </div>

        </div>

    </div>

    <div class="form-group p5w">
        <?php echo Html::a('Annulla', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Crea' : 'Aggiorna', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>