<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/1/24
 * Time: 下午2:52
 */

namespace App\Logic\Shopping\Common\Exception;

use App\Exceptions\ExceptionAll;

class PaymentWaiting extends ExceptionAll
{
    // 魔术方法
    public function __construct( $message = '等待付款结果', $errorId = 'WAIT_EXCEPTION' , $code = '400', $errorData  = array() )
    {
        parent::__construct( $message , $errorId , $code, $errorData );
    }

}