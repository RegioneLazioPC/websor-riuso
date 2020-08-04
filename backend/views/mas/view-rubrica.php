<?php 


use common\models\MasRubrica;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MasMessageRubrica */

$this->title = "Elemento rubrica";
$this->params['breadcrumbs'][] = ['label' => 'Rubrica', 'url' => ['index-rubrica']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mass-message-template-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= (Yii::$app->user->can('updateMasRubrica') && $model->tipo_riferimento == 'id_mas_rubrica') ? Html::a('Aggiorna', ['update-rubrica', 
        'id' => $model->id_riferimento
    ], ['class' => 'btn btn-primary']) : "" ?>
        
    </p>

    
	    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-8 m10 panel" style="padding: 0;">
		    <?=  DetailView::widget([
		        'model' => $model,
		        'attributes' => [
		            [
		                'label'=>'Riferimento',
		                'attribute'=>'valore_riferimento'
		            ],
		            [
		                'label'=>'Tipo riferimento',
		                'attribute'=>'tipologia_riferimento'
		            ],
		            [
		            	'label' => 'Extra',
		            	'attribute' => 'valore_riferimento',
		            	'value' => function ( $model ) {
		            		return 'ok';
		            	}
		            ],
		            [
		            	'label' => 'Indirizzo',
		            	'attribute' => 'valore_riferimento',
		            	'value' => function ( $model ) {
		            		return $model->indirizzo . ", " . $model->comune . " (" . $model->provincia . ")";
		            	}
		            ],
		            [
		                'label'=>'Latitudine',
		                'attribute'=>'lat'
		            ],
		            [
		                'label'=>'Longitudine',
		                'attribute'=>'lon'
		            ]          
		        ],
		    ]) ?>
		</div>
	<div class="clearfix"></div>
		
	    <div class="row">
	    	<div class="col-sm-12"><h3>Contatti</h3></div>
	    <?php 
	    	echo $this->render('_list-contatto', ['dataProvider' => $dataProvider, 'model'=>$model]);    	
	    ?>
		</div>


	<?php 
	if($model->tipo_riferimento == 'id_mas_rubrica') {
    	echo $this->render('/everbridge/index', [ 'model'=> MasRubrica::findOne($model->id_riferimento) ]);
    }

    ?>
</div>
