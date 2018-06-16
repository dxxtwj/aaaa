<?php
namespace Admin\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class SMSController extends CommonController {
    public function index(){
    	$sms = M('sms_rec');
    	if(IS_POST){
    		$data['SR_Status'] = (int)I('is_open');
		    $data['SR_Key'] = trim(I('sms_key'));
		    $data['SR_Secret'] = trim(I('sms_secret'));
		    $data['SR_TemplateID'] = trim(I('sms_id'));
		    $data['SR_UpdateTime'] = time();

		    $arr = array();
		    $arr[0]['type'] = 1;
		    $arr[0]['name'] = '用户注册验证码';
		    $arr[0]['code'] = trim(I('sms_register_user'));
		    $arr[0]['sign'] = trim(I('sms_register_user_signname'));


		    $arr[1]['type'] = 2;
		    $arr[1]['name'] = '修改密码验证码';
		    $arr[1]['code'] = trim(I('sms_change_pwd1'));
		    $arr[1]['sign'] = trim(I('sms_change_pwd1_signname'));


		    $arr[2]['type'] = 3;
		    $arr[2]['name'] = '忘记密码验证码';
		    $arr[2]['code'] = trim(I('sms_change_pwd2'));
		    $arr[2]['sign'] = trim(I('sms_change_pwd2_signname'));

		    $arr[3]['type'] = 4;
		    $arr[3]['name'] = '手机号变更验证码';
		    $arr[3]['code'] = trim(I('sms_change_mobile'));
		    $arr[3]['sign'] = trim(I('sms_change_mobile_signname'));

		    $arr_res = json_encode($arr);
		    $data['SR_CodeAndSign'] = $arr_res;

		    $res = $sms->where(array('SR_ID'=>1))->save($data);
		    if($res){
		    	$this->success('修改成功');
		    }else{
		    	$this->error('修改失败');
		    }

    	}else{
		    $res = $sms->where(array('SR_ID'=>1))->find();
		    if($res){
		    	$result = json_decode($res['SR_CodeAndSign'],true);
		    	$this->assign('res',$res);
		    	$this->assign('result',$result);
		    }
        	$this->display();
    	}
    }

}