<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use common\models\TblSezioneSpecialistica;
use common\models\ViewRubrica;
/* @var $this yii\web\View */
/* @var $model common\models\RubricaGroup */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Gruppi rubrica', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$tp = ViewRubrica::getTipiRiferimento();
$tps = [];
$str = "['";
foreach ($tp as $key => $value) {
    $tps[] = str_replace("'","\\'",$value);
}
$str .= implode("','", $tps) . "']";

?>
<div class="rubrica-group-view" ng-app="AppRubrica" ng-controller="GruppoRubricaController">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if(Yii::$app->user->can('updateRubricaGroup')) echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?php if(Yii::$app->user->can('deleteRubricaGroup')) echo Html::a('Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler eliminare questo gruppo?',
                'method' => 'post',
            ],
        ]); ?>
    </p>
    
    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute'=>'name',
                'label'=>'Nome'
            ]
        ],
    ]) ?>
    
    <div ng-class="{'show': block_form, 'hidden': !block_form}" 
    style="display:none; width: 100vw;height: 100vh;position: fixed;top: 0;left: 0;background-color: rgba(0,0,0,.4);z-index: 9991;">
        <em class="fa fa-spinner fa-spin" style="margin-left: -15px; margin-top: -15px;color: #fff; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); font-size: 30px;"></em>
        <p style="position: absolute; top: 50%; margin-top: 25px; width: 100vw;text-align: center; color: #fff">
            {{spin_message}} <br />
            <span style="display: block" ng-repeat="error in errors" class="text-danger">
              {{error}}
            </span>
            <input ng-if="can_reset" style="margin-top: 20px;" type="button" ng-click="resetAll()" class="btn btn-danger btn-sm" value="Resetta tutto" />
        </p>

    </div>

    <div ng-init="loadContacts(<?php echo $model->id;?>,'<?php echo Yii::$app->request->csrfToken;?>', <?= $str;?>)">
        <div ui-i18n="{{lang}}">
           <div>
                <input style="margin: 10px 5px 10px 0;" type="button" ng-click="exportRubrica()" class="btn btn-success btn-sm" value="esporta csv" />
                <br />
                <div style="margin: 10px 0;">
                    <span>Se selezionati:</span><br />
                    <button ng-class="{'disabled':calling}" style="margin-left: 10px; margin-top: 5px;" class="btn btn-default btn-sm" type="button" ng-click="addMultiple()">Inserisci</button>
                    <button ng-class="{'disabled':calling}" style="margin-left: 10px; margin-top: 5px;" class="btn btn-danger btn-sm" type="button" ng-click="removeMultiple()">Rimuovi</button>
                </div>
                <div 
                id="ui_grid1" 
                ui-grid="uiContactsGrid" 
                class="grid" 
                ui-grid-resize-columns ui-grid-move-columns ui-grid-selection ui-grid-exporter></div>
            </div>
        </div>
    </div>


    <script type="text/ng-template" id="uiSelectTemplate.html">
        <ui-select class="ui-grid-filter-select ui-grid-filter-input-0 ng-empty ng-touched" ng-model="colFilter.term" ng-show="colFilter.selectOptions.length > 0" ng-attr-placeholder="{{colFilter.placeholder || aria.defaultFilterLabel}}" aria-label="" ng-options="option.value as option.label for option in colFilter.selectOptions" placeholder="Filter for column">
        </ui-select>
      </script>



    <script type="text/ng-template" id="presentTemplate.html">
        <i class="fa" style="margin-top: 10px; margin-left: 25px;" ng-class="{'fa-check text-success' : row.entity.inserted, 'fa-close text-danger' : !row.entity.inserted}"></i>
    </script>

    <script type="text/ng-template" id="actionsTemplate.html">
        <button ng-class="{'disabled':calling}" style="margin-left: 10px; margin-top: 5px;" ng-if="!row.entity.inserted" title="Aggiungi" class="btn btn-default btn-xs" type="button" ng-class="{'disabled': grid.appScope.calling}" ng-click="grid.appScope.addContact(row.entity, COL_FIELD)"><i class="fa fa-plus"></i></button>
        <button ng-class="{'disabled':calling}" style="margin-left: 10px; margin-top: 5px;" ng-if="row.entity.inserted" title="Rimuovi" class="btn btn-danger btn-xs" type="button" ng-class="{'disabled': grid.appScope.calling}" ng-click="grid.appScope.removeContact(row.entity, COL_FIELD)"><i class="fa fa-minus"></i></button>        
    </script>


</div>
