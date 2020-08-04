<?php 
use yii\widgets\ListView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\Pjax;

$js = "

$(document).on('click', '.contattoModal', function(e) { 
    e.preventDefault();
    $('#modal-contatto').modal('show');
});

";

$this->registerJs($js, $this::POS_READY);
?>
<div style="margin-top: 18px;">
	<div class="col-lg-6 col-md-8 col-sm-12">
	<?php
	
		echo Html::button(
	                '<i class="glyphicon glyphicon-plus"></i> Nuovo contatto',
	                [
	                    'title' => 'Nuovo contatto',
	                    'class' => 'contattoModal btn btn-success',
	                    'style' => 'margin-bottom: 18px'
	                ]
	            );

	    Pjax::begin();

		echo ListView::widget([
		    'dataProvider' => $dataProvider,
		    'itemView' => '_single-contatto',
		    'viewParams' => ['elemento'=>$model]
		]);

		Pjax::end();
    ?>
 	</div>
</div>

<?php


    Modal::begin([
        'id' => 'modal-contatto',
        'header' => '<h2 class="p10w">Inserisci un contatto</h2>',
        'size' => 'modal-md'
    ]);

    echo Yii::$app->controller->renderPartial('_form-rubrica_contatto', [
    	'id_model'=>$model->id
    ]);
    
    Modal::end();

?>
