<?php
namespace Admin\Model;
use Think\Model;

class OrderModel extends Model
{
	protected $_validate = array(
		array('wlid','number','请输入数字'),
		array('userphone','number','请输入数字'),
		array('username','/[\x{4e00}-\x{9fa5}]/u','收件人填写错误'),
		);
	//订单遍历数据处理
	public function Single() {
		$arr = $this->select();
		$state = ['1'=>'等待付款', '2'=>'待发货', '3'=>'已发货', '4'=>'订单完成'];
		$wlname = ['1'=>'天天快递', '2'=>'圆通快递', '3'=>'中通快递', '4'=>'顺丰快递', '5'=>'申通快递', '6'=>'邮政EMS', '7'=>'韵达快递'];
		foreach($arr as $k=>$v) {
			$arr[$k]['atime'] = date('Y-m-d H:i:s', $v['atime']);
			$arr[$k]['state'] = $state[$v['state']];
			$arr[$k]['stime'] = date('Y-m-d H:i:s', $v['stime']);
			$arr[$k]['wlname'] = $wlname[$v['wlname']];
		}
		return $arr;
	}

	public function wlname($wlname) {
		$this->select();
		switch ($wlname) {
			case '1':
				$arr = '天天快递';
				break;
			case '2':
				$arr = '圆通快递';
				break;
			case '3':
				$arr = '中通快递';
				break;
			case '4':
				$arr = '顺丰快递';
				break;
			case '5':
				$arr = '申通快递';
				break;
			case '6':
				$arr = '邮政EMS';
				break;
			case '7':
				$arr = '韵达快递';
				break;
		}
		return $arr;
	}

	//用来处理订单显示数量
	public function wait($p, $id='') {
		$user = D('Order');

		if ($id == '') {
			$arr = $user->order('id desc')->limit($p->firstRow, $p->listRows)->field('id,atime,username,userphone,uaddress,money,state,wlname,cuidan')->Single();
		} else {
			$arr = $user->order('id desc')->limit($p->firstRow, $p->listRows)->where(['state'=>$id])->field('id,atime,username,userphone,uaddress,money,state,wlname,cuidan')->Single();	
		}
		return $arr;
	}
}