<?php
namespace api\utils;

use Yii;
use yii\web\HttpException;

class ResponseError
{

    /**
     * Vedere organizzazione/addPolizza per modifiche ai try {} catch {}
     * @param  [type] $status [description]
     * @param  [type] $string [description]
     * @return [type]         [description]
     */
    public static function returnSingleError($status, $string)
    {
        throw new HttpException($status, $string);
    }

    public static function returnMultipleErrors($status, $array)
    {
        throw new HttpException($status, json_encode( $array ) );
    }
    
}