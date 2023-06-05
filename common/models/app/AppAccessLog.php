<?php

namespace common\models\app;

use Yii;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_login_attempt".
 *
 *
 */
class AppAccessLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'app_access_log';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_user' => Yii::t('app', 'Id user'),
            'ip' => Yii::t('app', 'Indirizzo IP'),
            'username' => Yii::t('app', 'Stringa utente'),
            'email' => Yii::t('app', 'Email'),
            'action' => Yii::t('app', 'Azione'),
            'meta' => Yii::t('app', 'Meta campi'),
            'created_at' => Yii::t('app', 'Creazione'),
            'updated_at' => Yii::t('app', 'Aggiornamento'),
        ];
    }

    public static function addLogElement($action, $meta = [])
    {
        $element = new self();
        $element->id_user = Yii::$app->user->identity->id;
        $element->ip = Yii::$app->request->getUserIP();
        $element->username = Yii::$app->user->identity->username;
        $element->email = Yii::$app->user->identity->email;
        $element->action = $action;
        $element->meta = $meta;
        if (!$element->save()) {
            throw new \Exception(json_encode($element->getErrors()), 1);
        }
    }
}
