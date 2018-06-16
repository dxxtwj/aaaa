<?php
/**
* 	配置账号信息
*/

class WxPayConf_pub
{
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	const APPID = 'wx38948dc19b403e29';
	//受理商ID，身份标识
	const MCHID = '1413085302';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = 'a25493adf6d7ba79612af52d7ecmeiya';
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	const APPSECRET = 'a25493adf6d7ba79612af52d7ec59751';
	
	//=======【JSAPI路径设置】===================================
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
	const JS_API_CALL_URL = 'http://yq.forhuman.cn/Application/Home/Mobile/ShopOrder/shop_order.php';
	
	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
	// /MASTER/Home/Mobile/WxPayPubHelper/
	const SSLCERT_PATH = './WxPayPubHelper/cacert/apiclient_cert.pem';
	const SSLKEY_PATH = './WxPayPubHelper/cacert/apiclient_key.pem';
	
	
	//=======【异步通知url设置】===================================
	//异步通知url，商户根据实际开发过程设定
	const NOTIFY_URL = 'http://yq.forhuman.cn/Application/Home/Mobile/ShopOrder/notify_url.php';

	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;
}
	
?>