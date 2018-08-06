<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/1/24
 * Time: 下午2:52
 */

namespace App\Logic\Shopping\Common\Exception;

use App\Exceptions\ExceptionAll;

class PaymentWaitReTry extends ExceptionAll
{
    // 魔术方法
    public function __construct( $message = '等待重试', $errorId = 'WAIT_RE_TRY_EXCEPTION' , $code = '500', $errorData  = array() )
    {
        parent::__construct( $message , $errorId , $code, $errorData );
    }

}