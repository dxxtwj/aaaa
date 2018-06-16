<?php
namespace Home\Controller;
use Think\Controller;
use \Extend\wxpay\WxPayPubHelper as lib;
use \Extend\Alipay as Alipays;

use \Extend\wx_codepay\lib as wx_code_lib;

require_once "ThinkPHP/Library/Extend/wx_codepay/lib/WxPay.Api.php";
require_once "ThinkPHP/Library/Extend/wx_codepay/example/WxPay.NativePay.php";
require_once 'ThinkPHP/Library/Extend/wx_codepay/example/log.php';
header("Content-type: text/html; charset=utf-8");
class PayController extends HomeController {
    private $wx;
    function __construct(){ //初始化对象，将初始化值放在括号内
    	parent::__construct();
		// if(!is_mobile()){
		// 	// $notify = new wx_code_lib\NativePay();
		// }else{
			$wx = new lib\WxPayPubHelper();
		// }
	}

	public function pay(){
		if(!is_mobile()){
			C('DEFAULT_V_LAYER','PCView'); 
            $this->PCpay();
        }else{
        	$pay = M('wx_pay');
			$res = $pay->where(array('WP_ID'=>1))->find();
			if((int)$res['WP_Status'] == 1){
				$wx_status = 1;

	        	if(($_POST['isWeiXin'] && $_SESSION['Pay']['isWeiXin']) || ($_POST['isWeiXin'] && !$_SESSION['Pay']['isWeiXin'])|| (!$_POST['isWeiXin'] && $_SESSION['Pay']['isWeiXin'])){
					header('Content-Type:text/html; charset=utf-8');
			        $baseurl = 'http://'.$_SERVER['HTTP_HOST'];//域名

				    $jsApi = new lib\JsApi_pub();
				    if (!isset($_GET['code'])) {
				        $_SESSION['Pay']['oid'] = $_POST['oid'];
				        $_SESSION['Pay']['type'] = $_POST['type'];
				        $_SESSION['Pay']['isWeiXin'] = $_POST['isWeiXin'];
				        $_SESSION['Pay']['openid'] = null;
				        $pub = new lib\WxPayConf_pub();
				        $pub->getSome();
				        $url = $jsApi->createOauthUrlForCode($GLOBALS['a1']['WP_JSAPIURL']);
				        Header("Location:$url");
				    }else{
				    	$oid = $_SESSION['Pay']['oid'];
				    	$order_type = $_SESSION['Pay']['type'];
				        $code = $_GET['code'];
				        $jsApi->setCode($code);
				        $openid = $jsApi->getOpenId();
				        if($openid){
				        	$_SESSION['Pay']['openid'] = $openid;
				        }
				    }
				}
				
				if(I('type')==1 ||$order_type == 1){
					if(I('oid')){
						$where['OR_ID'] = I('oid');
					}else{
						$where['OR_ID'] = $oid;
					}
					if(!((int)$where['OR_ID']>0)){
						exit;
					}
					$where['OR_UID'] = (int)$_SESSION['Home']['userid'];
					// $where['OR_UID'] =1;

					$or = M('order_rec');

					$or_info = $or->field('OR_ID,OR_Key,OR_OrderTotal')->where($where)->find();
					if(I('type')){
						$or_info['type'] = I('type');
					}elseif($order_type){
						$or_info['type'] = $order_type;
					}
					if($openid){
						$this->assign('openid_json',json_encode($openid));
					}else{
						$this->assign('openid_json',json_encode($_SESSION['Pay']['openid']));
					}
					$this->assign('or_info_json',json_encode($or_info));
				}
			}else{
				$wx_status = -1;
			}
			$this->assign('wxstatus',json_encode($wx_status));
			$this->display();

        }
			
				
	}


	//公众号支付
	public function wxpay(){
		// header('Content-Type:text/html; charset=utf-8');
        $baseurl = 'http://'.$_SERVER['HTTP_HOST'];//域名
	    $jsApi = new lib\JsApi_pub();

    	$out_trade_no = I('WIDout_trade_no');
    	$order_type = I('type');
    	$openid = I('openid');
    	$paytype = 2;

	    if(!$out_trade_no){
	    	exit;
	    }

	    if($order_type == 1){
	    	$or = M('order_rec');
	    	$og = M('Order_goods');
	    	$pay_data['OR_PayType'] = $paytype;
	    	$pay_res = $or->where(array('OR_Key'=>$out_trade_no))->save($pay_data);

		    $post['WIDout_trade_no'] = $where['OR_Key'] = $out_trade_no;
			$or_info = $or->field('OR_ID,OR_Key,OR_OrderTotal')->where($where)->find();
			$og_info = $og->field('OG_Name')->where(array('OG_OID'=>(int)$or_info['OR_ID']))->find();

			if(!$or_info || !$og_info){
				exit;
			}

		    $post['WIDtotal_fee'] = $money = $or_info['OR_OrderTotal'];
		    $post['WIDsubject'] = $good_info = $out_trade_no;
	    }else{
			exit;
	    }
	    $pub = new lib\WxPayConf_pub();
		$pub->getSome();

	    $unifiedOrder = new lib\UnifiedOrder_pub();
	    $unifiedOrder->setParameter("openid","$openid");//商品描述
	    $unifiedOrder->setParameter("body",$good_info);//商品描述
	    $timeStamp = time();
	    $unifiedOrder->setParameter("out_trade_no",$out_trade_no);//商户订单号 
	    $unifiedOrder->setParameter("total_fee",$money*100);//总金额
	    $unifiedOrder->setParameter("notify_url",$GLOBALS['a1']['WP_NOTIFY_URL']);//通知地址 
	    $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型

	    $prepay_id = $unifiedOrder->getPrepayId();
	    $jsApi->setPrepayId($prepay_id);

	    $jsApiParameters = $jsApi->getParameters();
	    $this->ajaxReturn(json_decode($jsApiParameters,true));
	 
    }


    // 异步跳转
    public function wx_notifyurl(){

	    //使用通用通知接口
		$notify = new lib\Notify_pub();

		//存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];	
		$notify->saveData($xml);
		
		if($notify->checkSign() == FALSE){
			$notify->setReturnParameter("return_code","FAIL");//返回状态码
			$notify->setReturnParameter("return_msg","签名失败");//返回信息
		}else{
			$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
		}
		$returnXml = $notify->returnXml();
		// echo $returnXml;
		
		//以log文件形式记录回调信息
		$log_ = new \Extend\wxpay\Log_();
		$log_name="./Log/wx_notifyurl.log";//log文件路径
		$log_->log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");
		// $log_->log_result($log_name,"【接收到的notify通知】:\n".$returnXml."\n");

		if($notify->checkSign() == TRUE)
		{
			if ($notify->data["return_code"] == "FAIL") {
				//此处应该更新一下订单状态，商户自行增删操作
				$log_->log_result($log_name,"【通信出错】:\n".$xml."\n");
			}
			elseif($notify->data["result_code"] == "FAIL"){
				//此处应该更新一下订单状态，商户自行增删操作
				$log_->log_result($log_name,"【业务出错】:\n".$xml."\n");
			}
			else{
				//此处应该更新一下订单状态，商户自行增删操作
			
				//将xml转成数组
				$new_arr = xmlToArray($xml);
				$key = substr($new_arr['out_trade_no'],0,2);
				// $this->WriteLog($key,'./Log/1.txt');
				switch($key){
					case 'YK':
						$res = $this->UpdateOrder($new_arr['out_trade_no'],$new_arr['total_fee'],2);
						if($res){
							echo "SUCCESS";
						}
						break;
				}

			}
			//商户自行增加处理流程,
			//例如：更新订单状态
			//例如：数据库操作
			//例如：推送支付完成信息
		}
    }

    /***
    *
    *支付宝支付
    *
    */
    public function ali_buy(){
		header('Content-Type:text/html; charset=utf-8');
        $baseurl = 'http://'.$_SERVER['HTTP_HOST'];//域名
        $out_trade_no = $where['OR_Key'] = $_POST['WIDout_trade_no'];
        $ali_type = $_POST['type'];
        $paytype = 1;
        if((int)$ali_type == 1){
        	$pay_data['OR_PayType'] = $paytype;
	    	$pay_res = M('Order_rec')->where(array('OR_Key'=>$out_trade_no))->save($pay_data);

        	$or_info = M('Order_rec')->field('OR_ID,OR_UID,OR_Key,OR_OrderTotal')->where($where)->find();
	        if($or_info && $or_info['OR_ID']){
				$og_info = M('Order_goods')->where('OG_OID='.$or_info['OR_ID'])->find();
				// foreach ($og_info as $key => $value) {
					// $subject = mb_substr($og_info['OG_Name'],0,20,'utf-8');
					$subject = $out_trade_no;

				// }
	        	$args = array(
		            'WIDout_trade_no'=>$out_trade_no,
		            'WIDsubject'=> $subject,
		            'WIDtotal_fee'=> $or_info['OR_OrderTotal'],
		            'WIDshow_url'=> $baseurl,
		            'WIDbody'=> '',
		            );
	        	$ali_data = M('ali_pay');
	        	$ali_res = $ali_data->where(array('AP_ID'=>1))->find();
	        	if($ali_res){
	        		$alipay_arr = array(
						'service'=> "alipay.wap.create.direct.pay.by.user",
						'partner'	=>$ali_res['AP_PartnerID'],
						'seller_id'	=> $ali_res['AP_PartnerID'],
						'private_key'	=> trim($ali_res['AP_PartnerPrivateKey']),
						'alipay_public_key' => trim($ali_res['AP_PartnerPublicKey']),
						'notify_url' => $ali_res['AP_NotifyUrl'],
						'return_url' => $ali_res['AP_ReturnUrl'],
						'sign_type'  => strtoupper('RSA'),
						'input_charset' => strtolower('utf-8'),
						'cacert'=> getcwd().'\\cacert.pem',
						'transport'    =>'http',
						'payment_type'=> "1",
						'service'=> "alipay.wap.create.direct.pay.by.user",
					);
			        $s = new Alipays\Alipay();
			        $s->pay($alipay_arr,$args);
	        	}
	        	
	        }
	      
        }
          
    }
    // 同步跳转
    public function ali_returnurl(){
        $ali_data = M('ali_pay');
    	$ali_res = $ali_data->where(array('AP_ID'=>1))->find();
    	if($ali_res){
    		$alipay_config = array(
				'service'=> "alipay.wap.create.direct.pay.by.user",
				'partner'	=>$ali_res['AP_PartnerID'],
				'seller_id'	=> $ali_res['AP_PartnerID'],
				'private_key'	=> trim($ali_res['AP_PartnerPrivateKey']),
				'alipay_public_key' => trim($ali_res['AP_PartnerPublicKey']),
				'notify_url' => $ali_res['AP_NotifyUrl'],
				'return_url' => $ali_res['AP_ReturnUrl'],
				'sign_type'  => strtoupper('RSA'),
				'input_charset' => strtolower('utf-8'),
				'cacert'=> getcwd().'\\cacert.pem',
				'transport'    =>'http',
				'payment_type'=> "1",
				'service'=> "alipay.wap.create.direct.pay.by.user",
			);
		}
        //计算得出通知验证结果
        $alipayNotify = new Alipays\lib\AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        if($verify_result) {//验证成功
            //商户订单号
            $out_trade_no = $_GET['out_trade_no'];
            //支付宝交易号
            $trade_no = $_GET['trade_no'];
            //交易状态
            $trade_status = $_GET['trade_status'];
            if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                //交易成功                
            }else {
            	echo "trade_status=".$_GET['trade_status'];
            }  

            /*
            *同步（跳转页面）处理
            */
            if(substr($_GET['out_trade_no'], 0,2) == 'YK'){
				echo "<script>window.location.href='/index.php/Home/MyOrder/index';</script>";
			}else{
				echo "<script>window.location.href='/index.php/Home/index/index';</script>";
			}
            // echo "验证成功<br />";
        }else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            echo "验证失败";
        }
    }
    // 异步跳转
    public function ali_notifyurl(){
    	$ali_data = M('ali_pay');
    	$ali_res = $ali_data->where(array('AP_ID'=>1))->find();
    	if($ali_res){
    		$alipay_config = array(
				'service'=> "alipay.wap.create.direct.pay.by.user",
				'partner'	=>$ali_res['AP_PartnerID'],
				'seller_id'	=> $ali_res['AP_PartnerID'],
				'private_key'	=> trim($ali_res['AP_PartnerPrivateKey']),
				'alipay_public_key' => trim($ali_res['AP_PartnerPublicKey']),
				'notify_url' => $ali_res['AP_NotifyUrl'],
				'return_url' => $ali_res['AP_ReturnUrl'],
				'sign_type'  => strtoupper('RSA'),
				'input_charset' => strtolower('utf-8'),
				'cacert'=> getcwd().'\\cacert.pem',
				'transport'    =>'http',
				'payment_type'=> "1",
				'service'=> "alipay.wap.create.direct.pay.by.user",
			);
		}
        //计算得出通知验证结果
        $alipayNotify = new Alipays\lib\AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {//验证成功
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];
            if($_POST['trade_status'] == 'TRADE_FINISHED'||$_POST['trade_status'] == 'TRADE_SUCCESS') {
              //交易成功
            }
            //---修改订单开始
            $key = substr($out_trade_no, 0,2);
			$this->WriteLog(json_encode($_POST),'./Log/273.txt');
			switch($key){
				case 'YK':
					$res = $this->UpdateOrder($out_trade_no,$_POST['total_fee'],1);
					if($res){
						echo "SUCCESS";
					}
					break;
			}
            //---修改订单结束
            echo "success";     //请不要修改或删除
            
        }else {
            //验证失败
            echo "fail";
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }

    /*
    *支付宝结束
    */
    public function log_result($file,$word){
	    $fp = fopen($file,"a");
	    flock($fp, LOCK_EX) ;
	    fwrite($fp,"执行日期：".strftime("%Y-%m-%d-%H：%M：%S",time())."\n".$word."\n\n");
	    flock($fp, LOCK_UN);
	    fclose($fp);
	}


	public function getIP(){
		$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
		$user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];
		return $user_IP;

	}

	public function wxpayH5(){

		$pub = new lib\WxPayConf_pub();
		$pub->getSome();
		// $appid = lib\WxPayConf_pub::APPID;//appid  
		// $mch_id = lib\WxPayConf_pub::MCHID;//商户id 
		// $key = lib\WxPayConf_pub::KEY;//商户密钥 
		// $notify_url = lib\WxPayConf_pub::NOTIFY_URL; //回调地址  


		$appid = $GLOBALS['a1']['WP_APPID'];//appid  
		$mch_id = $GLOBALS['a1']['WP_WXMchId'];//商户id 
		$key = $GLOBALS['a1']['WP_WXSignKey'];//商户密钥 
		$notify_url = $GLOBALS['a1']['WP_NOTIFY_URL']; //回调地址  
		$trade_type = 'MWEB';//交易类型 具体看API 里面有详细介绍  
		// $scene_info ='{"h5_info":{"type":"Wap","wap_url":'.DOMAIN_NAME.',"wap_name":"爱人类CMS"}}';
		$scene_info ='{"h5_info":{"type":"Wap","wap_url":http://'.$_SERVER['HTTP_HOST'].',"wap_name":"爱人类CMS"}}';

		$nonce_str = $this->createNoncestr();//随机字符串 
		$spbill_create_ip = $this->getIP(); //获得用户设备IP  


		/*
		*根据POST订单号查询金额
		*
		*/

		$out_trade_no = $_POST['WIDout_trade_no'];//平台内部订单号
		$order_type = $_POST['type'];
		$paytype = 3;
		// dump($out_trade_no);
		if(!$out_trade_no){
	    	exit;
	    }
	    // $uid=$_GET['u']
	    if($order_type == 1){
	    	$or = M('order_rec');
		    $where['OR_Key'] = $out_trade_no;
		    $pay_data['OR_PayType'] = $paytype;
	    	$pay_res = $or->where(array('OR_Key'=>$out_trade_no))->save($pay_data);

			$or_info = $or->field('OR_ID,OR_Key,OR_OrderTotal')->where($where)->find();
			if(!$or_info){
				exit;
			}
			$total_fee = $or_info['OR_OrderTotal'] * 100; //金额*100  $or_info['OR_OrderTotal'];

			$og_info = M('Order_goods')->where('OG_OID='.$or_info['OR_ID'])->find();
			// $body = mb_substr($og_info['OG_Name'],0,20,'utf-8');//内容  
			$body = $out_trade_no;//内容  
	    }else{
			exit;
	    }


		$signA ="appid=$appid&body=$body&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";  
		$strSignTmp = $signA."&key=$key"; //拼接字符串  注意顺序微信有个测试网址 顺序按照他的来 直接点下面的校正测试 包括下面XML  是否正确  
		$sign = strtoupper(MD5($strSignTmp)); // MD5 后转换成大写  
		$post_data = "<xml>  
                       <appid>$appid</appid>  
                       <body>$body</body>  
                       <mch_id>$mch_id</mch_id>  
                       <nonce_str>$nonce_str</nonce_str>  
                       <notify_url>$notify_url</notify_url>  
                       <out_trade_no>$out_trade_no</out_trade_no>  
                       <scene_info>$scene_info</scene_info>  
                       <spbill_create_ip>$spbill_create_ip</spbill_create_ip>  
                       <total_fee>$total_fee</total_fee>  
                       <trade_type>$trade_type</trade_type>  
                       <sign>$sign</sign>  
                   </xml>";//拼接成XML 格式  
		$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";//微信传参地址  
		$dataxml = $this->http_post($url,$post_data);
		$objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA);
		if($order_type == 1){
			// $redirect_url = DOMAIN_NAME.'/index.php/Home/MyOrder/index';
			$redirect_url ='http://'.$_SERVER['HTTP_HOST'].'/index.php/Home/MyOrder/index';
		}
		$redirect_url = '&redirect_url='.urlencode($redirect_url);
		// echo '<a href="'.$objectxml['mweb_url'].$redirect_url.'">点击跳转微信支付</a>';
		// echo '<a href="'.$objectxml['mweb_url'].'">点击跳转微信支付</a>';
		$payurl = $objectxml['mweb_url'].$redirect_url;

        Header("Location:$payurl");
	}
	
	public function http_post($url, $data) {  
      $ch = curl_init();  
      curl_setopt($ch, CURLOPT_URL,$url);  
      curl_setopt($ch, CURLOPT_HEADER,0);  
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
      curl_setopt($ch, CURLOPT_POST, 1);  
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
      $res = curl_exec($ch);  
      curl_close($ch);  
      return $res;  
	}  
	public function createNoncestr( $length = 32 ){
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		}  
		return $str;
	}

	//修改订单状态,并判断是否减库存
	private function UpdateOrder($or_key,$total,$paytype){
		$or = M('Order_rec');
		$og = M('Order_goods');
		$goods = M('Goods_rec');
		$format_option = M('Format_option');
		$or_info = $or->field('OR_ID')->where(array('OR_Key'=>$or_key))->find();
		$og_info = $og->field('OG_GID,OG_Number,OG_GuigeID')->where(array('OG_OID'=>$or_info['OR_ID']))->select();

		if($or_info && $og_info){
			//改状态
			$data['OR_State'] = 1;
			// $data['OR_PayType'] = (int)$paytype;
			if($paytype == 4){
				$data['OR_PayType'] = 4;
			}elseif($paytype == 5){
				$data['OR_PayType'] = 5;
			}
			$data['OR_PayTime'] = time();
			if((int)$paytype == 1){
				$data['OR_ShiJiPay'] = $total;
			}elseif((int)$paytype == 2){
				$data['OR_ShiJiPay'] = $total/100;
			}elseif((int)$paytype == 4){
				$data['OR_ShiJiPay'] = $total/100;
			}elseif((int)$paytype == 5){
				$data['OR_ShiJiPay'] = $total;
			}else{
				$data['OR_ShiJiPay'] = $total/100;
			}
			$or_result = $or->where(array('OR_Key'=>$or_key))->save($data);

			//减库存
            foreach($og_info as $v){
            	$ku_goods = $goods->field('GR_Less,GR_Stock')->where(array('GR_ID'=>$v['OG_GID']))->find();
            	if($ku_goods){
            		if((int)$ku_goods['GR_Less'] == 2){
                		//判断是否有规格
                		if((int)$v['OG_GuigeID'] == 0){
                			$now_ku = (int)$ku_goods['GR_Stock'] - (int)$v['OG_Number'];
                			if($now_ku <= 0){
                				$ku_data['GR_Stock'] = 0;
                			}else{
                				$ku_data['GR_Stock'] = $now_ku;
                			}
                			$ku_res = $goods->where(array('GR_ID'=>$v['OG_GID']))->save($ku_data);
                		}else{
                			$ku_format = $format_option->field('FO_Stock')->where(array('FO_ID'=>$v['OG_GuigeID']))->find();
                			$now_ku = (int)$ku_format['FO_Stock'] - (int)$v['OG_Number'];
                			if($now_ku <= 0){
                				$ku_data['FO_Stock'] = 0;
                			}else{
                				$ku_data['FO_Stock'] = $now_ku;
                			}
                			$ku_res = $format_option->where(array('FO_ID'=>$v['OG_GuigeID']))->save($ku_data);
                		}
                	}else{
                		$ku_res = true;
                	}
            	}else{
            		$ku_res = false;
            	}
            	
            }

            if($or_result && $ku_res){
            	$now_result = true;
            }else{
            	$now_result = false;
            }
		}else{
        	$now_result = false;
        }
        return $now_result;
	}

	// 写日志
	function WriteLog($log,$text_name){
	   //这里是你记录调试信息的地方请自行完善以便中间调试
	   error_log($log."\r\n", 3,$text_name);
	}

	//检查是否调用过支付
	public function SelectPayType(){
			$key = I('key');
			$wx_type = (int)I('wx_type');
			$or = M('Order_rec');
			if(substr($key,0,2)=='YK'){
				$res = $or->field('OR_PayType')->where(array('OR_Key'=>$key))->find();
	   		if($res){
	   			if((int)$res['OR_PayType'] == 2 && $wx_type == 2){

	   				$flag['state'] = 2;

	   			}elseif((int)$res['OR_PayType'] == 2 && $wx_type == 3){

	   				$flag['state'] = 1;
	   				$flag['msg'] = '请在微信公众号内完成支付';

	   			}elseif((int)$res['OR_PayType'] == 3 && $wx_type == 2){
	   				$flag['state'] = 1;
	   				$flag['msg'] = '请使用手机浏览器完成支付';

	   			}elseif((int)$res['OR_PayType'] == 3 && $wx_type == 3){
	   				$flag['state'] = 2;
	   			}else{
	   				$flag['state'] = 2;
	   			}
	   			
	   		}else{
	   			$flag['state'] = -1;
	   		}
	   		$this->ajaxReturn($flag);
			}elseif(substr($key,0,2)=='YZ'){
				$oy = M('order_yi');
				$res = $oy->field('OY_Pay_Type')->where(array('OY_Key'=>$key))->find();
	   		if($res){
	   			if((int)$res['OY_Pay_Type'] == 2 && $wx_type == 2){

	   				$flag['state'] = 2;

	   			}elseif((int)$res['OY_Pay_Type'] == 2 && $wx_type == 3){

	   				$flag['state'] = 1;
	   				$flag['msg'] = '请在微信公众号内完成支付';

	   			}elseif((int)$res['OY_Pay_Type'] == 3 && $wx_type == 2){
	   				$flag['state'] = 1;
	   				$flag['msg'] = '请使用手机浏览器完成支付';

	   			}elseif((int)$res['OY_Pay_Type'] == 3 && $wx_type == 3){
	   				$flag['state'] = 2;
	   			}else{
	   				$flag['state'] = 2;
	   			}
	   			
	   		}else{
	   			$flag['state'] = -1;
	   		}
	   		$this->ajaxReturn($flag);
			}elseif(substr($key,0,2)=='JZ'){
				$oy = M('order_yi');
				$res = $oy->field('OY_Paynine_Type')->where(array('OY_Key'=>$key))->find();
	   		if($res){
	   			if((int)$res['OY_Paynine_Type'] == 2 && $wx_type == 2){

	   				$flag['state'] = 2;

	   			}elseif((int)$res['OY_Paynine_Type'] == 2 && $wx_type == 3){

	   				$flag['state'] = 1;
	   				$flag['msg'] = '请在微信公众号内完成支付';

	   			}elseif((int)$res['OY_Paynine_Type'] == 3 && $wx_type == 2){
	   				$flag['state'] = 1;
	   				$flag['msg'] = '请使用手机浏览器完成支付';

	   			}elseif((int)$res['OY_Paynine_Type'] == 3 && $wx_type == 3){
	   				$flag['state'] = 2;
	   			}else{
	   				$flag['state'] = 2;
	   			}
	   			
	   		}else{
	   			$flag['state'] = -1;
	   		}
	   		$this->ajaxReturn($flag);
			}
			
	}


    private function orderone_buy($key,$is_nine,$fee,$PCpaytype = 0){

		$oy = M('order_yi');
		$cr = M('current_rec');
		$oy_info = $oy->where(array('OY_Key'=>$key))->find();

		if(!$is_nine){
			if((int)$PCpaytype!=0){
				$savePaytype = $oy->where(array('OY_Key'=>$key))->save(array('OY_Pay_Type'=>(int)$PCpaytype));
			}
			if($oy_info['OY_Total'] != $fee){
				return false;
			}
			$cr_info = $cr->where(array('CR_GID'=>$oy_info['OY_GID']))->order('CR_Qishu desc')->find();//期数
	   		if($cr_info['CR_UIDS']){
				$people_num = count(explode(",",$cr_info['CR_UIDS']));
				if($people_num>=28){
					$this->ajaxReturn(-4);//参与人数已满
				}
				$nums_choosed = explode(",",$cr_info['CR_Nums']);
				for ($i=1; $i <= 28; $i++) { 
					if(!in_array($i, $nums_choosed)){
						$remain[] = $i;
					}
				}
				$n = $remain[mt_rand(0,count($remain)-1)];
				// $n = $people_num+1;//参加用户的数量
			}else{
				$n = mt_rand(1,28);
			}

			$uids = $cr_info['CR_UIDS'];
			if(!empty($uids)){
				// $unum=explode(",",$uids);
				$newuids = $uids.",".$oy_info['OY_UID'];
			}else{
				$newuids = $oy_info['OY_UID'];
			}
			$nums = $cr_info['CR_Nums'];
			if(!empty($nums)){
				$newnums=$nums.",".$n;
			}else{
				$newnums=$n;
			}

			$go_data['CR_UIDS']=$newuids;
			$go_data['CR_Nums']=$newnums;
			$newrecord = $cr->where(array('CR_GID'=>$oy_info['OY_GID']))->save($go_data);


			$order_save['OY_Number'] = $n;
			$order_save['OY_PayTime'] = time();
			$order_save['OY_State'] = 1;
			$order_save['OY_OrderState'] = 1;

			$oy_info = $oy->where(array('OY_Key'=>$key))->save($order_save);
			if($newrecord && $oy_info){
				return 1;
			}else{
				return 0;
			}
		}else{
			if((int)$PCpaytype!=0){
				$savePaytype = $oy->where(array('OY_Key'=>$key))->save(array('OY_Paynine_Type'=>(int)$PCpaytype));
			}
			if($oy_info['OY_Total']*9 != $fee){
				return false;
			}
			$order_save['OY_PaynineTime'] = time();
			$order_save['OY_Buy_Nine'] = 1;
			$oy_info = $oy->where(array('OY_Key'=>$key))->save($order_save);
		}
    }


	public function PCpay(){
		if((int)I('type')==1){
			if(I('oid')){
				$where['OR_ID'] = I('oid');
			}
			if(!((int)$where['OR_ID']>0)){
				exit;
			}
			$where['OR_UID'] = (int)$_SESSION['Home']['userid'];

			$or = M('order_rec');

			$info = $or->field('OR_ID as id,OR_Key as num,OR_OrderTotal as price,concat(OR_Province,OR_City,OR_County,OR_Detail) as loc,OR_Link as user,OR_Phone as phone')->where($where)->find();
			// $info['phone'] = substr($info['phone'],0,3)."****".substr($info['phone'],7,4);
			if(I('type')){
				$info['type'] = I('type');
			}

			$this->assign('info_json',json_encode($info));
			$this->display();
		}elseif(I('type')==2){

			if(I('oid')){
				$where['OY_ID'] = I('oid');
			}
			if(!((int)$where['OY_ID']>0)){
				exit;
			}
			$where['OY_UID'] = (int)$_SESSION['Home']['userid'];

			$oy = M('order_yi');

			$info = $oy->field('OY_ID as id,OY_Key as num,OY_Total as price,concat(OY_Province,OY_City,OY_County,OY_Detail) as loc,OY_Link as user,OY_Phone as phone')->where($where)->find();
			if(I('type')){
				$info['type'] = I('type');
			}
			if(substr($info['num'], 0,2)=='YZ'){
				$info['price'] = $info['price']*0.01;
			}elseif(substr($info['num'], 0,2)=='JZ'){
				$info['price'] = $info['price']*9*0.01;
			}
			$this->assign('info_json',json_encode($info));
			$this->display();
		}
	}

	public function wx_codepay(){
		$input = new \WxPayUnifiedOrder();
		$notify = new \NativePay();
		$num = trim(I('num'));
		$uid = (int)$_SESSION['Home']['userid'];
		// echo $num;
		//订单信息
		if(substr($num, 0,2) == 'YK'){
			$or = M('order_rec');
			$or_where['OR_Key'] = $num;
			$or_where['OR_UID'] = $uid;
			$info = $or->field('OR_ID as id,OR_Key as num,OR_OrderTotal as price,concat(OR_Province,OR_City,OR_County,OR_Detail) as loc,OR_Link as user,OR_Phone as phone')->where($or_where)->find();
			// $info['phone'] = substr($info['phone'],0,3)."****".substr($info['phone'],7,4);
			$this->assign('info_json',json_encode($info));
		}elseif(substr($num, 0,2) == 'JZ' || substr($num, 0,2) == 'YZ'){
			$oy = M('order_yi');
			$oy_where['OY_Key'] = $num;
			$oy_where['OY_UID'] = $uid;
			$info1 = $oy->field('OY_ID as id,OY_Key as num,OY_Total as price,concat(OY_Province,OY_City,OY_County,OY_Detail) as loc,OY_Link as user,OY_Phone as phone')->where($oy_where)->find();
			
			if(substr($info1['num'], 0,2)=='YZ'){
				$info1['price'] = $info1['price']*0.01;
			}elseif(substr($info1['num'], 0,2)=='JZ'){
				$info1['price'] = $info1['price']*9*0.01;
			}
			// echo $info1['num'];
			$this->assign('info_json',json_encode($info1));
		}

		//调用接口
		if(substr($num, 0,2) == 'YZ'){
			$oy = M('Order_yi');
			$info = $oy->field('OY_Name as name,OY_Key as orderkey,OY_Total as price,OY_OrderState as state,OY_EndTime as endtime')->where(array('OY_Key'=>$num))->find();
			if(!$info || (int)$info['state']!=0){
				exit;
			}
		}elseif(substr($num, 0,2) == 'JZ'){
			$oy = M('Order_yi');
			$info = $oy->field('OY_Name as name,OY_Key as orderkey,OY_Total as price,OY_Buy_Nine as state,OY_AddTime as addtime')->where(array('OY_Key'=>$num))->find();
			if(!$info || (int)$info['state']!=0){
				exit;
			}
			$info['price'] = $info['price']*9;
			$info['endtime'] = $info['addtime']+24*3600;
		}elseif(substr($num, 0,2) == 'YK'){
			$or = M('order_rec');

			$info = $or->field('OR_Key as name,OR_Key as orderkey,OR_OrderTotal as price,OR_State as state')->where(array('OR_Key'=>$num))->find();
			$info['price'] = $info['price']*100;
			if(!$info || (int)$info['state'] != 0){
				exit;
			}
		}
		// dump($info['orderkey']);
		$input->SetBody($info['name']);
		// $input->SetAttach("test");
		$input->SetOut_trade_no($info['orderkey']);
		$input->SetTotal_fee((int)$info['price']);
		if(substr($num, 0,2) == 'YZ'){
			$input->SetTime_start(date("YmdHis",$info['endtime']-600));
			$input->SetTime_expire(date("YmdHis",$info['endtime']));
		}elseif(substr($num, 0,2) == 'JZ'){
			$input->SetTime_start(date("YmdHis",$info['addtime']));
			$input->SetTime_expire(date("YmdHis",$info['addtime']+24*3600));
		}
		// $input->SetGoods_tag("test");
		$input->SetNotify_url(DOMAIN_NAME."/index.php/Home/Pay/wx_codepay_notify");
		$input->SetTrade_type("NATIVE");
		$input->SetProduct_id($info['orderkey']);
		$result = $notify->GetPayUrl($input);
		$url2 = $result["code_url"];
		// dump($url2);
		$this->assign('url',$url2);
		// $this->assign('end',$info['endtime']);
		$this->display();
		

	}
	//生成二维码
	public function getWXQrcode(){
		error_reporting(E_ERROR);
		require_once 'ThinkPHP/Library/Extend/wx_codepay/example/phpqrcode/phpqrcode.php';
		$url = urldecode($_GET["data"]);
		\QRcode::png($url);
	}

	public function wx_codepay_notify(){
		require_once 'ThinkPHP/Library/Extend/wx_codepay/lib/WxPay.Notify.php';
		require_once 'ThinkPHP/Library/Extend/wx_codepay/example/notify.php';
		//初始化日志
		$logHandler= new \CLogFileHandler("./ThinkPHP/Library/Extend/wx_codepay/logs/".date('Y-m-d').'.log');
		$log = \Log::Init($logHandler, 15);
		\Log::DEBUG("begin notify");
		$notify = new \PayNotifyCallBack();
		$notify->Handle(false);
		$postXml = $GLOBALS["HTTP_RAW_POST_DATA"];
		$postArr = xmlToArray($postXml);
		$r = $notify->Queryorder($postArr['transaction_id']);
		if($r){
			$key = $postArr['out_trade_no'];
			if(substr($key, 0,2) == 'YK'){
				$res = $this->UpdateOrder($key,$postArr['total_fee'],4);
			}
		}
	}
	//查询订单支付状态
	public function findPaystatus(){
		$key = trim(I('key'));
		if(substr($key,0,2) == 'YZ'){
			$oy = M('Order_yi');
			$info = $oy->field('OY_PayTime,OY_OrderState')->where(array('OY_Key'=>$key))->find();
			if((int)$info['OY_OrderState']!=0 && (int)$info['OY_PayTime']>0){
				$this->ajaxReturn(1);//支付成功
			}else{
				$this->ajaxReturn(0);
			}
		}elseif(substr($key,0,2) == 'JZ'){
			$oy = M('Order_yi');
			$info = $oy->field('OY_PaynineTime,OY_Buy_Nine')->where(array('OY_Key'=>$key))->find();
			if((int)$info['OY_Buy_Nine']!=0 && (int)$info['OY_PaynineTime']>0){
				$this->ajaxReturn(1);//支付成功
			}else{
				$this->ajaxReturn(0);
			}
		}elseif(substr($key,0,2) == 'YK'){
			$or = M('Order_rec');
			$info = $or->field('OR_PayTime,OR_State')->where(array('OR_Key'=>$key))->find();
			if((int)$info['OR_State']!=0 && (int)$info['OR_PayTime']>0){
				$this->ajaxReturn(1);//支付成功
			}else{
				$this->ajaxReturn(0);
			}
		}
		$this->ajaxReturn(0);
	}


	public function ali_codepay(){
		require_once 'ThinkPHP/Library/Extend/ali_codepay/config.php';
		require_once 'ThinkPHP/Library/Extend/ali_codepay/pagepay/service/AlipayTradeService.php';
		require_once 'ThinkPHP/Library/Extend/ali_codepay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';
		// $out_trade_no = trim($_POST['num']);
		$out_trade_no = trim($_POST['num']);
	    // echo $out_trade_no;
	    // exit;
		if(substr($out_trade_no, 0,2) == 'YZ'){
			$oy = M('Order_yi');
			$info = $oy->field('OY_Name as name,OY_Key as orderkey,OY_Total as price,OY_OrderState as state,OY_EndTime as endtime')->where(array('OY_Key'=>$out_trade_no))->find();
			if(!$info || (int)$info['state']!=0){
				exit;
			}
			$now = time();
			if($now<$info['endtime']){
				$minutes = floor(($info['endtime']-$now)/60);
				if($minutes<=0){
					exit;
				}
				$timeout_express = $minutes.'m';
			}else{
				echo '订单超过付款时限，请等下期';
				exit;
			}
		}elseif(substr($out_trade_no, 0,2)=='JZ'){
			$oy = M('Order_yi');
			$info = $oy->field('OY_Name as name,OY_Key as orderkey,OY_Total as price,OY_OrderState as orderstate,OY_Buy_Nine as state,OY_AddTime as addtime')->where(array('OY_Key'=>$out_trade_no))->find();
			if(!$info || (int)$info['orderstate']==0 || (int)$info['state']!=0){
				exit;
			}
			$info['price'] = $info['price']*9;
			$now = time();
			if($now<$info['addtime']+24*3600){
				$minutes = floor(($info['addtime']+24*3600-$now)/60);
				if($minutes<=0){
					echo '订单超过付款时限';
					exit;
				}
				$timeout_express = $minutes.'m';
			}else{
				exit;
			}
		}elseif(substr($out_trade_no, 0,2)=='YK'){
			$or = M('Order_rec');
			$info = $or->field('OR_Key as name,OR_Key as orderkey,OR_OrderTotal as price,OR_State as state')->where(array('OR_Key'=>$out_trade_no))->find();
			$info['price'] = $info['price']*100;
			if(!$info || (int)$info['state'] != 0){
				exit;
			}
		}

	    $total_amount = $info['price']*0.01;//付款金额，必填
	    //订单名称，必填
	    $subject = trim($info['name']);

	    // //商品描述，可空
	    // $body = trim($_POST['WIDbody']);

		//构造参数
		$payRequestBuilder = new \AlipayTradePagePayContentBuilder();
		$payRequestBuilder->setSubject($subject);
		$payRequestBuilder->setTotalAmount($total_amount);
		$payRequestBuilder->setOutTradeNo($out_trade_no);
		if($timeout_express){
			$payRequestBuilder->setTimeExpress($timeout_express);
			// echo $timeout_express;
		}

		$aop = new \AlipayTradeService($config);

		/**
		 * pagePay 电脑网站支付请求
		 * @param $builder 业务参数，使用buildmodel中的对象生成。
		 * @param $return_url 同步跳转地址，公网可以访问
		 * @param $notify_url 异步通知地址，公网可以访问
		 * @return $response 支付宝返回的信息
	 	*/
		$response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

		//输出表单
		var_dump($response);
	}

	public function ali_codepay_notify(){
		require_once 'ThinkPHP/Library/Extend/ali_codepay/config.php';
		require_once 'ThinkPHP/Library/Extend/ali_codepay/pagepay/service/AlipayTradeService.php';

		$arr=$_POST;
		$alipaySevice = new \AlipayTradeService($config); 
		$alipaySevice->writeLog(var_export($_POST,true));
		$result = $alipaySevice->check($arr);

		/* 实际验证过程建议商户添加以下校验。
		1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
		2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
		3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
		4、验证app_id是否为该商户本身。
		*/
		if($result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			
		    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			
			//商户订单号

			$out_trade_no = $_POST['out_trade_no'];

			//支付宝交易号

			$trade_no = $_POST['trade_no'];

			//交易状态
			$trade_status = $_POST['trade_status'];


		    if($_POST['trade_status'] == 'TRADE_FINISHED') {

				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//请务必判断请求时的total_amount与通知时获取的total_fee为一致的
					//如果有做过处理，不执行商户的业务程序
						
				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
		    }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//请务必判断请求时的total_amount与通知时获取的total_fee为一致的
					//如果有做过处理，不执行商户的业务程序			
				//注意：
				//付款完成后，支付宝系统发送该交易状态通知
				if(substr($out_trade_no, 0,2)=='YK'){
					$res = $this->UpdateOrder($out_trade_no,$_POST['total_amount'],5);
				}
		    }
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			echo "success";	//请不要修改或删除
		}else {
		    //验证失败
		    echo "fail";

		}
	}



	public function ali_codepay_return(){
		require_once 'ThinkPHP/Library/Extend/ali_codepay/config.php';
		require_once 'ThinkPHP/Library/Extend/ali_codepay/pagepay/service/AlipayTradeService.php';


		$arr=$_GET;
		$alipaySevice = new \AlipayTradeService($config); 
		$result = $alipaySevice->check($arr);

		/* 实际验证过程建议商户添加以下校验。
		1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
		2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
		3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
		4、验证app_id是否为该商户本身。
		*/
		if($result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代码
			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

			//商户订单号
			$out_trade_no = htmlspecialchars($_GET['out_trade_no']);

			//支付宝交易号
			$trade_no = htmlspecialchars($_GET['trade_no']);
				
			// echo "验证成功<br />支付宝交易号：".$trade_no;
			header("Location: /index.php/Home/Pay/pay_success/key/".$out_trade_no);   
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}else {
		    //验证失败
		    echo "验证失败";
		}
	}

	public function pay_success(){
		$num = trim(I('key'));
		if(substr($num, 0,2)=='YZ'){
			$oy = M('order_yi');
			$uid = (int)$_SESSION['Home']['userid'];
			$oy_where['OY_Key'] = $num;
			$oy_where['OY_UID'] = $uid;
			$info = $oy->field('OY_ID as id,OY_Key as num,OY_Total as price,concat(OY_Province,OY_City,OY_County,OY_Detail) as loc,OY_Link as user,OY_Phone as phone,OY_Pay_Type as type')->where($oy_where)->find();
			
			$info['price'] = $info['price']*0.01;
			if($info['type'] == 4){
				$info['pay'] = '微信支付';
			}elseif($info['type'] == 5){
				$info['pay'] = '支付宝支付';
			}
			$info['href'] = '/index.php/Home/Person/details_winning/oid/'.$info['id'];
		}if(substr($num, 0,2)=='JZ'){
			$oy = M('order_yi');
			$uid = (int)$_SESSION['Home']['userid'];
			$oy_where['OY_Key'] = $num;
			$oy_where['OY_UID'] = $uid;
			$info = $oy->field('OY_ID as id,OY_Key as num,OY_Total as price,concat(OY_Province,OY_City,OY_County,OY_Detail) as loc,OY_Link as user,OY_Phone as phone,OY_Paynine_Type as type')->where($oy_where)->find();
			if($info['type'] == 4){
				$info['pay'] = '微信支付';
			}elseif($info['type'] == 5){
				$info['pay'] = '支付宝支付';
			}
			$info['price'] = $info['price']*9*0.01;
			$info['href'] = '/index.php/Home/Person/details_winning/oid/'.$info['id'];
		}elseif(substr($num, 0,2)=='YK'){
			$or = M('order_rec');
			$uid = (int)$_SESSION['Home']['userid'];
			$or_where['OR_Key'] = $num;
			$or_where['OR_UID'] = $uid;
			$info = $or->field('OR_ID as id,OR_Key as num,OR_OrderTotal as price,concat(OR_Province,OR_City,OR_County,OR_Detail) as loc,OR_PayType as type,OR_Link as user,OR_Phone as phone')->where($or_where)->find();
			if($info['type'] == 4){
				$info['pay'] = '微信支付';
			}elseif($info['type'] == 5){
				$info['pay'] = '支付宝支付';
			}
			$info['href'] = '/index.php/Home/MyOrder/order_details.html?oid='.$info['id'];
			// $info['phone'] = substr($info['phone'],0,3)."****".substr($info['phone'],7,4);
		}else{

			
		}
		$this->assign('info_json',json_encode($info));
		$this->display();
	}

}