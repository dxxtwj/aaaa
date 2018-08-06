<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/1/24
 * Time: 下午2:52
 */

namespace App\Logic\Common\Exception;

use App\Exceptions\ExceptionAll;

class PaymentNext extends ExceptionAll
{
    // 魔术方法
    public function __construct( $message = '尝试下一种方式', $errorId = 'NEXT_EXCEPTION' , $code = '400', $errorData  = array() )
    {
        parent::__construct( $message , $errorId , $code, $errorData );
    }

}