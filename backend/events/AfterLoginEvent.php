<?php 
namespace backend\events;
use Yii;


class AfterLoginEvent{
    // public AND static
    public static function handleNewUser($event)
    {
    	$request = new \yii\web\Request;
    	
    	// genero un hash
    	$str = $event->identity->auth_key.time();
    	$token = Yii::$app->getSecurity()->generatePasswordHash($str);

    	$event->identity->access_token = $token;
    	$event->identity->ip_address = $request->getUserIP();
    	$event->identity->user_agent = $request->getUserAgent();
    	$event->identity->save();
    }
}