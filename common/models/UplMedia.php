<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\FileHelper;

use common\models\UplTipoMedia;


/**
 * This is the model class for table "upl_media".
 *
 * @property int $id
 * @property string $ext
 * @property string $mime_type
 * @property string $nome
 * @property int $uploaded_by
 * @property string $uploader_ip
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ConAnaDocumentoMedia[] $conAnaDocumentoMedia
 * @property ConOrganizzazioneMedia[] $conOrganizzazioneMedia
 * @property ConRisorsaMedia[] $conRisorsaMedia
 * @property ConVolontarioMedia[] $conVolontarioMedia
 */
class UplMedia extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'upl_media';
    }

    public function fields() {
        return array_merge(parent::fields(),[
            'tipo' => function($model) {
                return $model->type->descrizione;
            }
        ]);
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uploaded_by', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['uploaded_by', 'created_at', 'updated_at', 'id_tipo_media', 'orientation'], 'integer'],
            [['md5', 'exif', 'lat', 'lon'], 'safe'],
            [['id_tipo_media'], 'required'],
            [['ext', 'mime_type', 'nome', 'uploader_ip', 'date_upload'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ext' => 'Ext',
            'mime_type' => 'Mime Type',
            'nome' => 'Nome',
            'uploaded_by' => 'Uploaded By',
            'uploader_ip' => 'Uploader Ip',
            'date_upload' => 'Data',
            'id_tipo_media' => 'Tipo file',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getType() {
        return $this->hasOne(UplTipoMedia::className(), ['id' => 'id_tipo_media']);
    }

    private function normalizeName( $string )
    {
        return preg_replace( "/[^a-zA-Z0-9-]/", "_", $string);
    }
    
    public function uploadFile( $file, $type, $valid_files = [ /*'image/jpeg','image/png','image/jpg',*/'application/pdf'], $error_msg = "File non valido", $public = false )
    {

        $mimeType = FileHelper::getMimeType($file->tempName, null, false);
        if(!in_array($mimeType, $valid_files)) throw new \Exception($error_msg."; mime type file inviato: ".$mimeType, 1);

        $request = new yii\web\Request;
        // carico il file
        
        $this->uploaded_by = (Yii::$app instanceof \yii\console\Application) ? null : @Yii::$app->user->identity->id;
        $this->uploader_ip = $request->getUserIP();
        $this->nome = time().'_'.$this->normalizeName($file->name) . '.' . $file->extension;
        $this->ext = $file->extension;
        $this->mime_type = $mimeType;
        $this->date_upload = date("Y-m-d");
        $this->id_tipo_media = $type;


        $base_path = Yii::getAlias('@backend');
        if(!is_dir("{$base_path}/uploads")) mkdir("{$base_path}/uploads");
        if(!is_dir("{$base_path}/uploads/{$this->ext}")) mkdir("{$base_path}/uploads/{$this->ext}");     
        if(!is_dir("{$base_path}/uploads/{$this->ext}/{$this->date_upload}")) mkdir("{$base_path}/uploads/{$this->ext}/{$this->date_upload}");                
        $path = $base_path . '/uploads/' . $this->ext . '/' . $this->date_upload;

        if( !$file->saveAs($path . '/' . $this->nome ) ) throw new \Exception( json_encode ( ['Upload'=>['Errore caricamento file'  ]] ) );

        if( $public ) {
            if(!is_dir("{$base_path}/web")) mkdir("{$base_path}/web");
            if(!is_dir("{$base_path}/web/images")) mkdir("{$base_path}/web/images"); 
            if(!is_dir("{$base_path}/web/images/uploads")) mkdir("{$base_path}/web/images/uploads");  
            if(!is_dir("{$base_path}/web/images/uploads/{$this->ext}")) mkdir("{$base_path}/web/images/uploads/{$this->ext}");     
            if(!is_dir("{$base_path}/web/images/uploads/{$this->ext}/{$this->date_upload}")) mkdir("{$base_path}/web/images/uploads/{$this->ext}/{$this->date_upload}");                
            $p_path = $base_path . '/web/images/uploads/' . $this->ext . '/' . $this->date_upload;
            if( !copy($path . '/' . $this->nome, $p_path . '/' . $this->nome ) ) throw new \Exception( json_encode ( ['Upload'=>['Errore caricamento file' ]] ) );
        }

        
        if(!$this->save()) throw new \Exception( json_encode ( $this->getErrors() ) );
        
    }

    /**
     * Cancella file alla cancellazione del model
     * @return [type] [description]
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        try {
            $file_path = Yii::getAlias('@backend/uploads/'.$this->ext.'/'.$this->date_upload.'/'.$this->nome);
            if (file_exists($file_path)) unlink($file_path);
            
            if (file_exists($file_path.'.oriented')) unlink($file_path.'.oriented');
        } catch (Exception $e) {
            Yii::error("Errore cancellazione file ".$file_path);
        }

        return true;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uploaded_by']);
    }
}
