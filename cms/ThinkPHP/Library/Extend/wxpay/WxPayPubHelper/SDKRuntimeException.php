<?php

namespace Extend\wxpay\WxPayPubHelper;
use Exception;
class  SDKRuntimeException extends Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}

}

?>