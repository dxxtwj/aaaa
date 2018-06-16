<?php
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('PRC');
require_once 'aop/AopClient.php';
require_once 'aop/request/AlipayFundTransToaccountTransferRequest.php';

$aop = new AopClient ();
$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
$aop->appId = '2088821594710896';
$aop->rsaPrivateKey = 'MIICXAIBAAKBgQDYpgU1eL8hokcjTeo3zOf50lpY+MF6sCYnUceMtmmOXwhbXl6zO5QPYoQCqVLoVfMUe4ZkfR4TLF2W56ULfxUbuf435+iC+nPsJetyEkqZ/WbXikMDXSyKHTWU81rVzoMiwtt/aGhs9vs802Y3HHsV9B1hBQPiG4L2cl1fj2V30QIDAQABAoGAeZarg7gRpOG7hQ/cbJa+dpHdDOQHSkEEGLsoAEv77+BcA1NyWKsOTJgguJGpKyXZt9wfr9Qchew3VlMJaOtYv1912E1DPW4ZakABFy20+zxxGAtOKeOBpiOiAewdqEBxvZOefORZoFry+Yc4oeWjJXetN1arKHjldVTDTjSS7dUCQQDxUJD6owwdoIxcDyqWPqNQbsCUd5an+zR1YWBC/HVek4e4U4/zJwjFh2gPmSODok3tuPi13tJgw8W/yMksOEIvAkEA5dUuIh9olcVcbIJPv2RGH1emEHUXC4PpcBA/Mqapcrod85GyZeefMnBB2ziDFto4qzBmwDqxR8QmtVF6bfFl/wJAA+NocqFt8IxFtrYH2aPovcMLF1lV9B74GWwYQPwQaBW4eh/ekexvF7+2zYmKKPTUjKAOYd/VQ/njldOGak/9wQJBAOGTSXM+RoTxL1Rk1eawgU6T1S7D7Xlk4AIYQB7zis5Ks8jy0BjKq+pyWYDTR35vcj07BS5YrNUWRTOjI7myu3sCQDxT+wS9z+PsBAASvjJ8iZYTFcxGDTHAR3PocOElHu9YNUGgct5i4iWCQCWxgf16jomaNIKvNXy5mD4iL1XIT3g=';
$aop->alipayrsaPublicKey='MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB';
$aop->apiVersion = '1.0';
$aop->signType = 'RSA';
$aop->postCharset='UTF-8';
$aop->format='json';
$request = new AlipayFundTransToaccountTransferRequest ();
$request->setBizContent("{" .
    "    \"out_biz_no\":\"3142321423432\"," .
    "    \"payee_type\":\"ALIPAY_LOGONID\"," .
    "    \"payee_account\":\"799632155@qq.com\"," .
    "    \"amount\":\"1.00\"," .
    "    \"remark\":\"转账备注\"," .
    "    \"ext_param\":\"{\\\"order_title\\\":\\\"会员提现\\\"}\"" .
    "  }");
$result = $aop->execute ( $request);
var_dump($result);
$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
$resultCode = $result->$responseNode->code;
echo $responseNode;
echo $resultCode;
if(!empty($resultCode)&&$resultCode == 10000){
    echo "成功";
} else {
    echo "失败";
}

