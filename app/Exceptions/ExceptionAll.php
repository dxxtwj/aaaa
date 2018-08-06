<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/1/15
 * Time: 上午10:38
 */

namespace App\Exceptions;


class ExceptionAll extends \DdvPhp\DdvException\Error
{
    // 魔术方法
    public function __construct( $message = 'Unknown Error', $errorId = 'UNKNOWN_ERROR' , $code = '400', $errorData  = array() )
    {
        parent::__construct( $message , $errorId , $code, $errorData );
    }
}