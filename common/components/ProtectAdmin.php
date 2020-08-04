<?php 
namespace common\components;

use Yii;

/**
 * Impedisci l'apertura di admin a chi non ha il permesso
 */
class ProtectAdmin extends \yii\base\Component {
    public function init() {
    	if(preg_match("/admin\//", Yii::$app->urlManager->parseRequest(Yii::$app->request)[0]) && !Yii::$app->user->can('manageRbac')) :
    		Yii::$app->user->logout();       
    		header( "location: ".Yii::$app->urlManager->createUrl('site/login') );
    		exit(0);
    	endif;
        
        parent::init();
    }
}