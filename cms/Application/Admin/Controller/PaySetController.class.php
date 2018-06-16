<?php
namespace Admin\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class PaySetController extends CommonController {
	public function index(){
		$ali = M('ali_pay');
		$wx = M('wx_pay');
		$wx_res = $wx->field('WP_Status')->where(array('WP_ID'=>1))->find();
		$ali_res = $ali->field('AP_MobileStatus')->where(array('AP_ID'=>1))->find();
		$ali_res2 = $ali->field('AP_ScanStatus')->where(array('AP_ID'=>2))->find();

		$this->assign('status1',$wx_res['WP_Status']);
		$this->assign('status2',$ali_res['AP_MobileStatus']);
		$this->assign('status3',$ali_res2['AP_ScanStatus']);
		$this->display();
	}

	//是否关闭支付
	public function is_close(){
		$ali = M('ali_pay');
		$wx = M('wx_pay');
		$state = (int)I('state');
		switch($state){
			case 1://微信
				$where['WP_ID'] = 1;
				$data['WP_Status'] = 0;
				$data['WP_UpdateTime'] = time();
				$res = $wx->where($where)->save($data);
				break;
			case 2://支付宝手机
				$where['AP_ID'] = 1;
				$data['AP_MobileStatus'] = 0;
				$data['AP_UpdateTime'] = time();
				$res = $ali->where($where)->save($data);
				break;
			case 3://支付宝扫码
				$where['AP_ID'] = 2;
				$data['AP_ScanStatus'] = 0;
				$data['AP_UpdateTime'] = time();
				$res = $ali->where($where)->save($data);
				break;
		}
		if($res){
			$this->success('修改成功');
		}else{
			$this->error('修改失败');
		}

	}

	//是否开启支付
	public function is_open(){
		$ali = M('ali_pay');
		$wx = M('wx_pay');
		$state = (int)I('state');
		switch($state){
			case 1://微信
				$where['WP_ID'] = 1;
				$data['WP_Status'] = 1;
				$data['WP_UpdateTime'] = time();
				$res = $wx->where($where)->save($data);
				break;
			case 2://支付宝手机
				$where['AP_ID'] = 1;
				$data['AP_MobileStatus'] = 1;
				$data['AP_UpdateTime'] = time();
				$res = $ali->where($where)->save($data);
				break;
			case 3://支付宝扫码
				$where['AP_ID'] = 2;
				$data['AP_ScanStatus'] = 1;
				$data['AP_UpdateTime'] = time();
				$res = $ali->where($where)->save($data);
				break;
		}
		if($res){
			$this->success('修改成功');
		}else{
			$this->error('修改失败');
		}

	}

	public function wxedit(){
		$wx = M('wx_pay');
		if(IS_POST){
			$data['WP_APPID'] = I("appId");
			$data['WP_AppSecret'] = I("secret");
			$data['WP_WXMchId'] = I("mchId");
			$data['WP_WXSignKey'] = I("paySignKey");
			$data['WP_JSAPIURL'] = I("jsapi_url");
			$data['WP_NOTIFY_URL'] = I("notify_url");
			$data['WP_CURL_TIMEOUT'] = I("curl_timeout");
//			$data['WP_SSLCERT_PATH'] = I("sslcert");
//			$data['WP_SSLKEY_PATH'] = I("sslkey");
			$data['WP_Status'] = (int)I("is_pay");
			$data['WP_Type'] = 1;
			$data['WP_UpdateTime'] = time();
			if($_FILES['sslfile']['name'] && $_FILES['sslkeyfile']['name']){
				$type ='.';
				$path = $type.'/ThinkPHP/Library/Extend/wxpay/WxPayPubHelper/cacert/';
				if(!file_exists($path)){
					@mkdir($path);
				}
				$upload = new \Think\Upload();// 实例化上传类
				$upload->maxSize   =     5242880 ;// 设置附件上传大小
				$upload->exts      =     array('pem');// 设置附件上传类型
				$upload->rootPath  =     $type; // 设置附件上传根目录
				$upload->savePath  =     '/ThinkPHP/Library/Extend/wxpay/WxPayPubHelper/cacert/';
				$upload->uploadReplace  =     true;
				if($upload->maxSize<$_FILES['sslfile']['size']){
					$this->error('SSLCERT证书文件过大','/index.php/Admin/PaySet/wxedit');
				}
				if($upload->maxSize<$_FILES['sslkeyfile']['size']){
					$this->error('SSLKEY证书文件过大','/index.php/Admin/PaySet/wxedit');
				}
				$exts_arr = $upload->exts;
				if($exts_arr[0] != 'pem'){
					$this->error('请选择pem的证书文件','/index.php/Admin/PaySet/wxedit');
				}
				$info   =   $upload->upload();
				if($info){
					foreach($info as $file){
						if($file['key'] == 'sslfile'){
							$data['WP_SSLCERT_PATH'] = $file['savepath'].$file['savename'];
						}
						if($file['key'] == 'sslkeyfile'){
							$data['WP_SSLKEY_PATH'] = $file['savepath'].$file['savename'];
						}
					}

				}
			}
			$res = $wx->where(array('WP_Type'=>1))->save($data);
			if($res){
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}else{
			$res = $wx->where(array('WP_Type'=>1))->find();
			if($res['WP_JSAPIURL'] == '' || $res['WP_JSAPIURL'] == null){
				$res['WP_JSAPIURL'] = 'http://'.$_SERVER['HTTP_HOST'].'/index.php/Home/Pay/pay';
			}
			if($res['WP_NOTIFY_URL'] == '' || $res['WP_NOTIFY_URL'] == null){
				$res['WP_NOTIFY_URL'] = 'http://'.$_SERVER['HTTP_HOST'].'/index.php/Home/Pay/wx_notifyurl';
			}
			$this->assign('res',$res);
			$this->display();
		}
	}

	public function zfbedit(){
		$ali = M('ali_pay');
		if(IS_POST){
			$data['AP_Name'] = I('alipay_account');
			$data['AP_PartnerID'] = I('alipay_appid');
			$data['AP_PartnerPrivateKey'] = I('partner_dev_privatekey');
			$data['AP_PartnerPublicKey'] = I('partner_alipay_publickey');
			$data['AP_WirelessPrivateKey'] = I('wap_dev_privatekey');
			$data['AP_WirelessPublicKey'] = I('wap_alipay_publickey');
			$data['AP_ReturnUrl'] = I('return_url');
			$data['AP_NotifyUrl'] = I('notify_url');
			$data['AP_Type'] = 1;
			$data['AP_MobileStatus'] = (int)I('is_pay');
			$data['AP_UpdateTime'] = time();
			$res = $ali->where(array('AP_Type'=>1))->save($data);
			if($res){
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}else{
			$res = $ali->where(array('AP_Type'=>1))->find();
			if($res['AP_ReturnUrl'] == '' || $res['AP_ReturnUrl'] == null){
				$res['AP_ReturnUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/index.php/Home/Pay/ali_returnurl';
			}
			if($res['AP_NotifyUrl'] == '' || $res['AP_NotifyUrl'] == null){
				$res['AP_NotifyUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/index.php/Home/Pay/ali_notifyurl';
			}
			$this->assign('res',$res);
			$this->display();
		}
	}

	public function zfb_codepay(){
		$ali = M('ali_pay');
		if(IS_POST){
			$data['AP_Name'] = I('alipay_account');
			$data['AP_APPID'] = I('alipay_appid');
			$data['AP_MerchantPrivateKey'] = I('partner_dev_privatekey');
			$data['AP_ZFBPublicKey'] = I('partner_alipay_publickey');
			$data['AP_ScanReturnUrl'] = I('return_url');
			$data['AP_ScanNotifyUrl'] = I('notify_url');
			$data['AP_Type'] = 2;
			$data['AP_ScanStatus'] = (int)I('is_pay');
			$data['AP_UpdateTime'] = time();
			$res = $ali->where(array('AP_Type'=>2))->save($data);
			if($res){
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}else{
			$res = $ali->where(array('AP_Type'=>2))->find();
			if($res['AP_ScanReturnUrl'] == '' || $res['AP_ScanReturnUrl'] == null){
				$res['AP_ScanReturnUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/index.php/Home/Pay/ali_codepay_return';
			}
			if($res['AP_ScanNotifyUrl'] == '' || $res['AP_ScanNotifyUrl'] == null){
				$res['AP_ScanNotifyUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/index.php/Home/Pay/ali_codepay_notify';
			}
			$this->assign('res',$res);
			$this->display();
		}
	}



}