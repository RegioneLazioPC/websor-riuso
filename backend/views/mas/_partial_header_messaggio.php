<?php 
use yii\helpers\Html;
             
    if(!empty($model->id_template)) {
        ?>
        <p><strong>Template:</strong> <?= Html::a($model->template->nome, 
                ['mas/view-template', 'id' => $model->template->id], 
                ['class' => '', 'target'=>'_blank', 'data-pjax'=>0]
            );?></p>
        <?php
    }
    ?>
    <?php 
    if(!empty($model->id_allerta)) {
        ?>
        <p><strong>Allerta:</strong> <?= Html::a("#" . $model->allerta->id . " del " . Yii::$app->formatter->asDate($model->allerta->data_allerta), 
                ['allerta-meteo/view', 'id' => $model->allerta->id], 
                ['class' => '', 'target'=>'_blank', 'data-pjax'=>0]
            );?>
            <?php 
            if(!empty($model->allerta->file)) {
                foreach ($model->allerta->file as $media) {

                    ?>
                    <p><?= Html::encode($media->nome);?>
                        <?php echo Html::a('Vedi allegato', ['media/view-media', 'id' => $media->id], ['class' => 'btn btn-primary btn-xs', 'target'=>'_blank']);?>
                    </p>
                    <?php
                }
                ?>
                
                <?php
            } 
        ?>
        </p>
        <?php
    }

    if(!empty($model->file)) {
        foreach ($model->file as $media) {

            ?>
            <p><?= Html::encode($media->nome);?>
                <?php echo Html::a('Vedi allegato', ['media/view-media', 'id' => $media->id], ['class' => 'btn btn-primary btn-xs', 'target'=>'_blank']);?>
            </p>
            <?php
        }
        ?>
        
        <?php
    }