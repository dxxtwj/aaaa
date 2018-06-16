<?php
namespace Admin\Model;
use Think\Model;

class UserModel extends Model
{

	//自动验证
	protected $_validate = array(
		array('username','require','用户名不能为空'),
		array('tname','require','真实姓名不能为空'),
		// array('password','require','密码不能为空'),
		array('password', 'password1', '两次密码不一致', 2, 'confirm'),
		array('phone','require','手机不能为空'),
		array('integral','require','积分不能空'),
		);  

	//自动完成密码加密
	// if (!empty($add['password'])) {
	// 	protected $_auto = [
	// 		['password', 'password_hash', 2, 'function', [PASSWORD_DEFAULT]],
	// 	];
	// } else {
	// 	pullall($add['password']);
	// }
		 

	//用户首页数据处理
	public function User() 
	{
		$arr = $this->select();

		$sex = ['1'=>'男', '2'=>'女', '3'=>'保密'];
		$status = ['1'=>'正常', '2'=>'禁用'];
		$grade = ['1'=>'普通用户', '2'=>'VIP用户', '3'=>'钻石用户'];
		$coupon = ['0'=>'无优惠劵', '10'=>'满88-10', '20'=>'满88-20', '30'=>'满88-30'];
		foreach($arr as $k=>$v) {
			$arr[$k]['sex'] = $sex[$v['sex']];
			$arr[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
			$arr[$k]['status'] = $status[$v['status']];
			$arr[$k]['grade'] = $grade[$v['grade']];
			$arr[$k]['coupon'] = $coupon[$v['coupon']];
		}
		return $arr;
	}

	//修改用户数据处理
	public function Edit() {
		$arr = $this->select();
		return $arr;
	}


	//处理普通用户显示的数据
	public function ordinary($p) {
		$user = D('User');
		$arr = $user->order('id desc')->limit($p->firstRow, $p->listRows)->where(['grade'=>'1'])->field('id,username,sex,phone,email,addtime,grade,status,integral,coupon')->user();
		return $arr;
	}

	//处理VIP用户显示的数据
	public function vip($p) {
		$user = D('User');
		$arr = $user->order('id desc')->limit($p->firstRow, $p->listRows)->where(['grade'=>'2'])->field('id,username,sex,phone,email,addtime,grade,status,integral,coupon')->user();
		return $arr;
	}

	//处理钻石用户显示的数据
	public function dirmond($p) {
		$user = D('User');
		$arr = $user->order('id desc')->limit($p->firstRow, $p->listRows)->where(['grade'=>'3'])->field('id,username,sex,phone,email,addtime,grade,status,integral,coupon')->user();
		return $arr;
	}

	//处理全部用户显示的数据
	public function whole($p) {
		$user = D('User');
		$arr = $user->order('id desc')->limit($p->firstRow, $p->listRows)->field('id,username,sex,phone,email,addtime,grade,status,integral,coupon')->user();
		return $arr;
	}

	//处理优惠用户显示的数据
	public function nocoupon($p) {
		$user = D('User');
		$arr = $user->order('id desc')->limit($p->firstRow, $p->listRows)->where(['coupon'=>['neq',0]])->field('id,username,sex,phone,email,addtime,grade,status,integral,coupon')->user();
		return $arr;
	}

	//处理无优惠用户显示的数据
	public function coupon($p) {
		$user = D('User');
		$arr = $user->order('id desc')->limit($p->firstRow, $p->listRows)->where(['coupon'=>['eq',0]])->field('id,username,sex,phone,email,addtime,grade,status,integral,coupon')->user();
		return $arr;
	}
}