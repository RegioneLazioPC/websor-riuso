<?php

use yii\db\Migration;

use common\models\UtlSegnalazioneAttachments;
use common\models\UplMedia;
use common\models\UplTipoMedia;
use common\models\AuditTrail;
use yii\helpers\FileHelper;
/**
 * Class m190205_100817_modify_segnalazione_attachment_move_to_new_upl_media
 */
class m190205_100817_modify_segnalazione_attachment_move_to_new_upl_media extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $attachments = UtlSegnalazioneAttachments::find()->all();

        $tipo = UplTipoMedia::find()->where(['descrizione'=>'Allegato segnalazione'])->one();
        if(!$tipo) {
            $tipo = new UplTipoMedia();
            $tipo->descrizione = 'Allegato segnalazione';
            $tipo->save();
        }

        /**
         * Sposto tutti gli attachments nelle cartelle upload
         */
        foreach ($attachments as $attachment) {

            $segnalazione = $attachment->getSegnalazione()->one();

            // prendo utente che ha caricato il file da AuditTrail
            $trail = AuditTrail::find()
            ->where(['model'=>'common\models\UtlSegnalazione'])
            ->andWhere(['action'=>'CREATE'])
            ->andWhere(['model_id'=>$segnalazione->id])
            ->one();

            $id_uploader = null;
            if(!$trail || !$trail->user_id) {
                echo "Non presente dato su creazione segnalazione\n";
            } else {
                $id_uploader = $trail->user_id;
            }

            $file_path = Yii::getAlias('@backend').'/uploads/' . $attachment->filename;
            if(file_exists($file_path)) {
                $filename = explode(".", $attachment->filename);
                

                $ext = end($filename);
                $mimeType = FileHelper::getMimeType($file_path, null, false);

                $media = new UplMedia;
                $media->uploaded_by = $id_uploader;
                $media->uploader_ip = null;
                $media->nome = time().'_'.$attachment->filename;
                $media->ext = $ext;
                $media->mime_type = $mimeType;
                $media->date_upload = date("Y-m-d", strtotime( $segnalazione->dataora_segnalazione ) );
                $media->id_tipo_media = $tipo->id;
                $media->save();

                $media->created_at = strtotime( $segnalazione->dataora_segnalazione );
                $media->save();

                $base_path = Yii::getAlias('@backend');
                if(!is_dir("{$base_path}/uploads")) mkdir("{$base_path}/uploads");
                if(!is_dir("{$base_path}/uploads/{$media->ext}")) mkdir("{$base_path}/uploads/{$media->ext}");     
                if(!is_dir("{$base_path}/uploads/{$media->ext}/{$media->date_upload}")) mkdir("{$base_path}/uploads/{$media->ext}/{$media->date_upload}");                
                $file_dest = $base_path . '/uploads/' . $media->ext . '/' . $media->date_upload . '/' . $media->nome;

                copy ( $file_path, $file_dest );

                $segnalazione->link('media', $media);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $tipo = UplTipoMedia::find()->where(['descrizione'=>'Allegato segnalazione'])->one();
        if($tipo) {

            $medias = UplMedia::find()->where(['id_tipo_media' => $tipo->id])->all();
            foreach ($medias as $media) {
                $media->delete();
            }
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190205_100817_modify_segnalazione_attachment_move_to_new_upl_media cannot be reverted.\n";

        return false;
    }
    */
}
