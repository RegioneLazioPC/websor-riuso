<?php
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;

use common\utils\EverbridgeUtility;
use yii\helpers\Html;
use yii\helpers\Url; 


if(Yii::$app->user->can('ManageEverbridge')) {

    $js = "
        $(document).on('click', '.openGridContact', function(e) { 
            e.preventDefault();
            $('#everbridge_loader').load($(this).attr('href'));
            $(this).hide();
        });

        $(document).on('click', '.forceSync', function(e) { 
            e.preventDefault();
            $.get( $(this).attr('href'), function( data ) {
              
              alert(data);
              if(data == 'Sincronizzato') {
                $('#everbridge_loader').load($('.openGridContact').attr('href'));
              }
            });
        });
    ";

    $this->registerJs($js, $this::POS_READY);

        ?>
        
        <p>
            <?php echo Yii::$app->params['sync_everbridge'] ? 'Everbridge sincronizzato' : 'Sincronizzazione con everbridge non attiva';?>
        </p>

        <?php
        echo Html::button(
            'Vedi stato su everbridge',
            [
                'title' => Yii::t('app', 'Carica dati everbridge'),
                'class' => 'openGridContact btn btn-success m10t',
                'href' => Url::to(['everbridge/paths', 'ext_ids' => implode(",", $model->getExtIds()) ])
            ]
        );  

        echo Html::button(
            'Sincronizza con everbridge',
            [
                'title' => Yii::t('app', 'Sincronizza'),
                'class' => 'forceSync btn btn-danger m10t',
                'style' => 'margin-left: 8px',
                'href' => Url::to(['everbridge/force-sync', 'model' => $model->className(), 'id' => $model->id ])
            ]
        );    
                
    ?>

    <div id="everbridge_loader" style="margin-top: 12px"></div>

<?php } ?>