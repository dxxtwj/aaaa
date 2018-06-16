<?php
namespace Home\Controller;
use Think\Controller;

class PayController extends Controller {
	/**
	 * 结算页数据处理
	 */
	public function index() {
		// dump(I('post.'));exit;
		// $this->redirect('Pay/index');
		$data = I('post.id');
		// dump(explode(',', I('get.id')));
		//多条数据根据,切割
		$data = explode(',', I('post.id'));
		// dump($data);
		// exit;
		//查询用户提交的数据
		foreach($data as $k=>$v) {
			$arr = $data[$k];
			//单条数据根据.切割
			$ex = explode('.', $arr);
			$jk[$ex[0]] = $ex[1];
		}
			// dump($arr);exit;
		// dump($jk);
		//获取相应的数据拼成数组
		$ce = M('detailed');
		$goods = M('goods');
		foreach($jk as $ke=>$va) {
			$brr = $ce->where(['id'=> $ke])->field('id,baozhuang,kouwei,price,gid,image')->find();
			$brr['num'] = $va;
			// dump($brr);
			$all = $goods->where(['id'=>$brr['gid']])->field('name')->find();
			$brr['name'] = $all['name'];
			// dump($info);exit;
			$res[] = $brr;
		}
		// dump($res);
		// dump($ex);
		// dump($arr);exit;

		//查询三级联动的顶级
		$areas = M('areas');
		$arr = $areas->where(['area_type'=>1])->field('id,area_name')->select();

		//多地址遍历
		$ress = D('address');
		$sel = $ress->where(['uid'=>session('user')['id']])->field('id,isdefault,username,userphone,area1,area2,area3,address')->PayModel();
		// dump($sel);
		// $arrs = session('user')['id'];
		$so = $ress->where(['isdefault'=>'1' , 'uid'=>session('user')['id']])->PayModel();
		// dump($ress->_sql());
		// dump($so);exit;

		//查询用户
		$user = M('user');
		$info = $user->where(['id'=>session('user')['id']])->field('coupon')->find();

		//三级联动查询
		if (IS_AJAX) {
			if (IS_GET) {
				$id = I('get.id');
				$sel = $areas->where(['parent_id'=>$id])->field('id,area_name')->select();
				$this->ajaxReturn($sel);
			} else {
				$ids = I('post.id');
				$sels = $areas->where(['parent_id'=>$ids])->field('id,area_name')->select();
				$this->ajaxReturn($sels);
			}	
		}

		
		$this->assign('res', $res);
		$this->assign('info', $info);
		$this->assign('so', $so);
		$this->assign('sel', $sel);
		$this->assign('arr', $arr);
		$this->display();
	}

	/**
	 * 默认地址设置
	 */
	public function address() {
		if (IS_POST) {
			$address = M('address');
			$post = I('post.');
			$post['uid'] = session('user')['id'];
			$arr = $address->add($post);
			$this->redirect('Pay/index');
		} else {
			$id = I('get.id');
			$add = M('address');
			$arr = $add->where(['isdefault'=>'1' , 'uid'=>session('user')['id']])->field('id,isdefault')->find();
			if ($arr) {
				$default['isdefault'] = '0';
				$res = $add->where(['id'=>$arr['id']])->save($default);
			}

			$default['isdefault'] = '1';
			$res = $add->where(['id'=>$id])->save($default);
			$this->ajaxReturn($res);
		}
	}

	/**
	 * 删除地址
	 */
	public function del() {
		$ress = M('address');
		$id = I('get.id');
		$res = $ress->delete($id);
		// dump($res);exit;
		$this->ajaxReturn($res);
	}

	/**
	 * 修改地址
	 */
	public function edit() {
		if (IS_AJAX) {
			if (IS_GET) {
				$ress = M('address');
				$id = I('get.id');
				$arr = $ress->where(['id'=>$id])->find();
				$this->ajaxReturn($arr);
			}
		}
		
		if (IS_POST) {
			// dump(123);
			$address = M('address');
			$post = I('post.');
			$arr = $address->save($post);
			// $post['uid'] = 1;
			// $arr = $address->add($post);
			$this->redirect('Pay/index');
		}
	}

	//查询商品数量是否足够
	public function query() {
		$id = I('get.id');
			$ce = M('detailed');
			//拆分ajax传送过来的数据
			foreach ($id as $k => $v) {
				$arr = $id[$k];
				$ex = explode('.', $arr);
				// $ex[1]
				$data[$ex[0]] = $ex[1];
			}
			//拆分成id作为键,数量作为值
			$zy = [];
			foreach ($data as $ke => $va) {
				$jk = $ce->where(['id'=> $ke])->field('num')->select();
				// $this->ajaxReturn($va);
				if ($jk[0]['num'] < $va) {
					$zy[] = $ke;
				}
			}
			if (empty($zy)) {
				$this->ajaxReturn(2);
			} else {
				$this->ajaxReturn($zy);
			}
	}

	/**
	 * 添加订单
	 */
	public function join() {
		$data = I('post.data');
		//收件人地址
		$all['uaddress'] = $data[0].$data[1].$data[2].$data[3];
		$all['username'] = $data[4];
		$all['userphone'] = $data[5];

		//订单金额
		$all['money'] = $data[6];
		
		//用户留言
		$all['content'] = $data[7];

		//商品总额
		$all['productmoney'] = $data[8];
		//订单状态
		$all['state'] = 1;
		//订单创建时间
		$all['atime'] = time();
		//快递公司
		$all['wlname'] = $data[9];
		//用户编号
		$all['uid'] = session('user')['id'];
		//商品数量
		$all['number'] = $data[10];

		// $this->ajaxReturn(555);
		$order = M('order');
		$ord = M('orderdata');
		$detailed = M('detailed');
		$goods = M('goods');
		$user = M('user');

		//开启事物
		$order->startTrans();
		
		if ($all['money'] != $all['productmoney']) {
			// $sion['id'] = session('user')['id'];
			// $this->ajaxReturn(444);
			$sion['coupon'] = 0;
			$use = $user->where(['id'=>$all['uid']])->save($sion);	
		}

		//添加订单
		$info = $order->add($all);

		//订单编号
		// $orderdata['oid'] = $info;
		//订单ID
		$id = I('post.id');

			//拆分ajax传送过来的数据
			foreach ($id as $k => $v) {
				$arr = $id[$k];
				$ex = explode('.', $arr);
				// $ex[1]
				$res[$ex[0]] = $ex[1];
			}

			// $this->ajaxReturn($res);
			//拆分成id作为键,数量作为值
			$zy = [];
			foreach ($res as $ke => $va) {
				// $this->ajaxReturn($ke);
				//查询商品信息
				$jk = $detailed->where(['id'=> $ke])->field('gid,image,price,baozhuang,kouwei')->find();

				//查询商品名
				$so = $goods->where(['id'=>$jk['gid']])->field('name')->find();

				$lsx['oid'] = $jk['gid'];
				$lsx['pid'] = $info;
				$lsx['num'] = $va;
				$lsx['pname'] = $so['name'];
				$lsx['pmoney'] = $jk['price'];
				$lsx['fid'] = $ke;
				$lsx['fbao'] = $jk['baozhuang'];
				$lsx['fkou'] = $jk['kouwei'];
				//添加订单详情信息
				$add[$ke] = $ord->add($lsx);
				// $this->ajaxReturn($add);
				if ($add) {
					S(array(   
		 				'type'=>'redis',    
		 				'host'=>'127.0.0.1',    
						'port'=>'6379',
					));
					$sess = S(session('user')['id']);

					//清空购物车
					foreach ($sess as $key => $val) {
						// $this->ajaxReturn(333);
						if ($ke == $val['id']) {
							unset($sess[$key]);
							// $this->ajaxReturn($sess);
						}
					}

				}
				S(session('user')['id'], $sess);
				//减少库存
				$set = $detailed->where(['id'=>$ke])->setInc('num', -$va);
				// $zs = $detailed->_sql();
			}

				if ($info && $add && $set) {
					
					// session('user')['id'];
					$sx = $order->commit();
				} else {
					$sx = $order->rollback();
				}
				$last = $order->order('id desc')->limit(1)->select();
			
			$this->ajaxReturn($last);
				// $this->ajaxReturn($sx);
			// $this->ajaxReturn($zy);
	}
}