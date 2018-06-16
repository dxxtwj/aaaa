<?php
namespace Home\Controller;
use Think\Controller;
use Think\Page;
// use \Redis;

/**
 * 购物车控制页面
 */
class ShopcartController  extends CommonController
{	
	/**
	 * 购物车列表
	 */
	public function index() {
		// S(array(   
		//  	'type'=>'redis',    
		//  	'host'=>'127.0.0.1',    
		// 	'port'=>'6379',
		// ));
		// dump(cookie());exit;
		// dump(session('user')['id']);exit;
		// setcookie("id","",time()-10);
		// dump(unserialize(cookie('id')));exit;
		// dump(cookie());exit;
		//用户登录之后点击购物车页面，合并购物车
		if (session('user')['id']) {
			// echo 888;
			$lize = unserialize(cookie('id'));
			// dump($lize);
			$redis = $this->redis->get(session('user')['id']);
			$redis = unserialize($redis);
			// dump($redis);exit;
			// dump(cookie('id'));
			// exit;
			foreach($lize as $k=>$v) {
					$cha = $lize[$k];
					// dump($cha);
					foreach($redis as $k=>$v) {
						$po = $redis[$k];
						// dump($po);
						if ($cha['id'] == $po['id']) {
							$redis[$k]['num'] = $cha['num'] + $v['num'];
						} else {
							$length++;
						}
					}
					if ($length == count($redis)) {
						$redis[] = $cha;
					}
				}
				$ce = M('detailed');
				// dump($redis);

				foreach ($redis as $key => $val) {
					$redis[$key]['jin'] = 0;
					$mh = $ce->where(['id'=>$val['id']])->field('num')->find();
					// dump($mh);exit;
					if ($mh['num'] <= $val['num']) {
						$redis[$key]['jin'] = 1;
						if ($mh['num'] < $val['num']) {
							$redis[$key]['ku'] = 1;
						} else {
							$redis[$key]['ku'] = 2;
						}
					} else {
						$redis[$key]['ku'] = 2;
					}
					// dump($ce->_sql());
				}

					// dump($mh);exit;
				
					

				$asds = serialize($redis);
			// $true = $this->redis->set(session('user')['id'], $redis);
				$true = $this->redis->set(session('user')['id'], $asds);
			if ($true == 'true') {
				cookie('id',null);
			}
		} else {
			$co = cookie('id');
			$redis = unserialize($co);
			// dump($redis);exit;
		}
		

		// $get = I('get.');
		// $get = ['id1'=>'690', 'id2'=>'693'];
		// $xa = M('xattrbute');
		// $deta = M('dattrbute');
		// foreach ($get as $v) {
		// 	$arr = $xa->where(['id'=>$v])->field('dtypeid,xattrbute')->find();
		// 	dump($arr['dtypeid']);
		// 	$info = $deta->where(['id'=>$arr['dtypeid']])->field('id,gid,dattrbute')->find();
		// 	dump($info);
		// }
		// exit;
		
			// setcookie(session_id(), $cs, time()+3600*24*7, '/');
		// }
		// $this->assign('ass', $ass);
		$this->assign('redis', $redis);
		$this->display();
	}

	/**
	 * 处理输入框内容
	 */
	public function zy() {
		// S(array(   
		//  	'type'=>'redis',    
		//  	'host'=>'127.0.0.1',    
		// 	'port'=>'6379',
		// ));
		$id = I('get.id');
		$num = I('get.num');
		$ce = M('detailed');
		$arr = $ce->where(['id'=>$id])->field('num')->find();
		// $this->ajaxReturn($arr);exit;
		if ($num > $arr['num']) {
			$data = $this->redis->get(session('user')['id']);

			$data = unserialize($data);
			// $this->ajaxReturn($data);
			foreach ($data as $k => $v) {
				if ($data[$k]['id'] == $id) {
					$data[$k]['num'] = $arr['num'];
				}
			}
			$data = serialize($data);
			$this->redis->set(session('user')['id'], $data);
			$this->ajaxReturn($arr['num']);
		} else {
			$data = $this->redis->get(session('user')['id']);
			$data = unserialize($data);
			// $this->ajaxReturn($data);
			foreach ($data as $k => $v) {
				if ($data[$k]['id'] == $id) {
					$data[$k]['num'] = $num;
				}
			}
			$data = serialize($data);
			$this->redis->set(session('user')['id'], $data);
				// $this->ajaxReturn($num);
			if ($arr['num'] == $num) {
				$this->ajaxReturn('-1');
			} else {
				$this->ajaxReturn('-2');
			}
		}
		// $this->ajaxReturn($arr['num']);
		// dump($id);dump($num);exit;
	}


	/**
	 * 加入购物车处理
	 */
	public function handle() {
		// $id1 = '824';
		// $id2 = '825';
		// $num = '5';
		// $xa = M('xattrbute');
		// $arr = $xa->where(['dtypeid'=>$id1])->find();
		// $brr = $xa->where(['dtypeid'=>$id2])->find();
		// // dump($arr);exit;
		// $this->ajaxReturn(I('get.id'));exit;
		// dump(cookie());exit;
		$data = explode('.', I('get.id'));
		// dump($data);
		$de = M('detailed');
		// dump($data[0]);exit;
		$all = $de->where(['id'=>$data[0]])->field('id,baozhuang,kouwei,price,gid')->find();
		$all['num'] = $data[1];
		$goods = M('goods');
		$good = $goods->where(['id'=>$all['gid']])->field('name')->find();
		$all['name'] = $good['name'];
		// dump($all);exit;
		// $this->ajaxReturn($arr);
		
		// //拼接口味包装的数据
		// $data1 = $arr['dtypeid'].'_'.$arr['id'];
		// $data2 = $brr['dtypeid'].'_'.$brr['id'];
		// $data = $data1.','.$data2;

		// //查询具体的口味包装对应的那条数据
		// $deta = M('detailed');
		// $info = $deta->where(['typeids'=>$data])->field('id,gid,price')->find();

		// //查询对应的商品名
		// $goods = M('goods');
		// $good = $goods->where(['id'=>$info['gid']])->field('name,image0')->find();
		// // dump($good);exit;

		// $da = M('dattrbute');
		// $bute1 = $da->where(['id'=>$id1])->field(['dattrbute'])->find();
		// $bute2 = $da->where(['id'=>$id2])->field(['dattrbute'])->find();
		// // dump($bute);exit;
		
		// //创建一条数据
 	// 	$all['id'] = $info['id'];
 	// 	$all['xa1'] = $arr['xattrbute'];
 	// 	$all['xa2'] = $brr['xattrbute'];
 	// 	$all['bute1'] = $bute1['dattrbute'];
 	// 	$all['bute2'] = $bute2['dattrbute'];
		// $all['name'] = $good['name'];
		// $all['src'] = $good['image0'];
		// $all['price'] = $info['price'];
		// $all['num'] = $num;
		// $all['qian'] = $num * $info['price'];
		// $all['gid'] = $info['gid'];
		// S(array(   
		//  	'type'=>'redis',    
		//  	'host'=>'127.0.0.1',    
		// 	'port'=>'6379',
		// ));
// session('id', 'jk');
		// $all['id'] = '10';
		// $all['name'] = '芒果';
		// $all['bute1'] = '产地';
		// $all['bute2'] = '重量';
		// $all['xa1'] = '湖北';
		// $all['xa2'] = '2千克';
		// $all['price'] = '3';
		// $all['num'] = '5';

// $sb = S(session('id'));dump($sb);exit;
		// $jk[] = $all;
		// $lize = unserialize(cookie('id'));dump($lize);exit;
		// dump(session());exit;
		// dump(cookie('id', null));exit;
		// $fg = unserialize(cookie('id'));dump($fg);exit;
		// dump(session());exit;
		
		

		// 如果用户已登录就把添加进购物车的商品存入redis
		if (session('user')['id']) {
			// echo 789;
			if ($this->redis->get(session('user')['id'])) {
				// echo 111;
				$red = $this->redis->get(session('user')['id']);
				$red = unserialize($red);
				foreach($red as $k=>$v) {
					$mh[] = $v['id'];
					if ($v['id'] == $all['id']) {
						// echo 999;
						// dump($v['num'] + $all['num']);
						$red[$k]['num'] = $v['num'] + $all['num'];
						// dump($zy);
					}
				}
					// dump($red);exit;
				$so = in_array($all['id'], $mh);

					if (!$so) {
						// echo 128;
						// $zy[] = $all;
						$red[] = $all;
					}
				// dump($red);exit;
				// if ()
				
				// dump($red);
				$red = serialize($red);
				// dump($res);exit;
				$this->redis->set(session('user')['id'], $red);
				// dump($res); 				
			} else {
				// echo 222;
				$cun[] = $all;
				$cun = serialize($cun);
				$this->redis->set(session('user')['id'], $cun);
			}
		} else {
			//当用户没有登录第一次加入购物车,cookie里面的session_id还未设置,直接从存入cookie_id中，第二次来的时候取出之前的数据，在合并，在存入cookie
			if (cookie('id') === NULL) {
				// echo 123;
				// $cs = array_merge($jk);
				$zy[] = $all;
				$cs = serialize($zy);
				// dump($cs);exit;
				cookie('id', $cs, 604800);
			} else {
				// echo 456;
				$arr_str = cookie('id');
				$zy = unserialize($arr_str);
				// dump($zy);
				foreach($zy as $k=>$v) {
					$cha[] = $v['id'];
					// dump($in);
					if ($v['id'] == $all['id']) {
						// echo 789;
						// dump($v['num'] + $all['num']);
						$zy[$k]['num'] = $v['num'] + $all['num'];
						// dump($zy);
					}
				}

				$in = in_array($all['id'], $cha);
					if (!$in) {
						echo 120;
						$zy[] = $all;
					}	
					// if ($in == 'true') {
					// 	dump(count($zy));
					// 	// dump($zy[count($zy)-1]['num']);exit;
					// 	$zy[count($zy)-1]['num'] = $zy[count($zy)-1]['num'] + $all['num'];
					// } else {
					// 	$zy[] = $all;
					// }
					// dump($zy);exit;
					// dump($in);exit;
				
				// dump($in);
				// exit;
				// dump($cha);exit;
				// dump($zy);exit;
				$cs = serialize($zy);
				cookie('id', $cs, 604800);
			}
		}
	}

	/**
	 * 删除购物车物品
	 */
	public function del() {
		// dump(unserialize(cookie('id')));exit;

		if (session('user')['id']) {
		// S(array(   
		//  	'type'=>'redis',    
		//  	'host'=>'127.0.0.1',    
		// 	'port'=>'6379',
		// ));
			$id = I('get.id');
			$as = $this->redis->get(session('user')['id']);
			$as = unserialize($as);
			foreach ($as as $k => $v) {
				if ($v['id'] == $id) {
					unset($as[$k]);
					// dump($un);exit;
					$as = serialize($as);
					// dump($arr);exit;
					$this->redis->set(session('user')['id'], $as);
				}
			}
		} else {
			$id = I('get.id');
			$un = unserialize(cookie('id'));
			// dump($un);
			foreach ($un as $k => $v) {
				if ($v['id'] == $id) {
					unset($un[$k]);
					// dump($un);exit;
					$arr = serialize($un);
					// dump($arr);exit;
					cookie('id',$arr);
				}
			}
		}
		// dump($un);
		// dump(unserialize(cookie('id')));
		$this->ajaxReturn(1);
	}

	/**
	 * 减少商品
	 */
	public function jian() {
		// dump(session());exit;
		// dump(cookie());exit;
		// S(array(   
		//  	'type'=>'redis',    
		//  	'host'=>'127.0.0.1',    
		// 	'port'=>'6379',
		// 	));
		// dump(S(session('id')));exit;
		// session('id', 'jk');
		if (session('user')['id']) {
			// echo 123;
			// S(array(   
		 // 	'type'=>'redis',    
		 // 	'host'=>'127.0.0.1',    
			// 'port'=>'6379',
			// ));
			$as = $this->redis->get(session('user')['id']);
			$as = unserialize($as);
			// dump($as);
			$id = substr(I('get.id'), 1);
			$ce = M('detailed');
			$se = $ce->where(['id'=>$id])->field('num')->find();
			foreach ($as as $k => $v) {
				if ($v['id'] == $id) {
					$as[$k]['num'] = $v['num']-1;
					$as = serialize($as);
					$this->redis->set(session('user')['id'], $as);
					if ($se['num'] == $as[$k]['num']) {
						$this->ajaxReturn(-1);
					} else {
						if ($se['num'] < $as[$k]['num']) {
							$this->ajaxReturn(2);
						} else {
							$this->ajaxReturn(1);
						}
					}
				}
			}
		} else {
			$id = substr(I('get.id'), 1);
			$un = unserialize(cookie('id'));
			foreach ($un as $k => $v) {
				if ($v['id'] == $id) {
					$ce = M('detailed');
					$se = $ce->where(['id'=>$id])->field('num')->find();
					if ($un[$k]['num'] > 1) {
						$un[$k]['num'] = $v['num']-1;
						$ze = serialize($un);
						cookie('id', $ze);
						if ($un[$k]['num'] == '1') {
							$this->ajaxReturn('-1');
						}
					}
				}
			}
			// dump($un);
			// dump(unserialize(cookie('id')));exit;
		}
		// $this->ajaxReturn();
	}


	public function jia() {
		if (session('user')['id']) {
			// echo 123;
			// S(array(   
		 // 	'type'=>'redis',    
		 // 	'host'=>'127.0.0.1',    
			// 'port'=>'6379',
			// ));
			$as = $this->redis->get(session('user')['id']);
			$as = unserialize($as);
			// dump($as);
			$id = substr(I('get.id'), 1);
			foreach ($as as $k => $v) {
				if ($v['id'] == $id) {
					$ce = M('detailed');
					$se = $ce->where(['id'=>$id])->field('num')->find();
					// dump($se);exit;
					if ($se['num'] > $as[$k]['num']) {
						$as[$k]['num'] = $v['num']+1;
						$as = serialize($as);
						$this->redis->set(session('user')['id'], $as);
						if ($se['num'] == $as[$k]['num']) {
							$this->ajaxReturn('-1');
						}
					} else {
						$this->ajaxReturn($as[$k]['num']);
					}
				}
			}
			
		} else {
			// echo 213;
			$id = substr(I('get.id'), 1);
			dump($id);
			$un = unserialize(cookie('id'));
			// dump($un);exit;
			foreach ($un as $k => $v) {
				if ($v['id'] == $id) {
					$ce = M('detailed');
					$se = $ce->where(['id'=>$id])->field('num')->find();
					if ($un[$k]['num'] < 100) {
						$un[$k]['num'] = $v['num']+1;
						$ze = serialize($un);
						cookie('id', $ze);
						if ($un[$k]['num'] == $se['num']) {
							$this->ajaxReturn('-1');
						}
					} 
				}
			}
			// dump(unserialize(cookie('id')));exit;
		}
		// $this->ajaxReturn();
	}

	/**
	 * 提交购物车数据判断是否登录
	 */
	public function go() {
		// $this->ajaxReturn(I('post.id'));
		// dump(session('user'));exit;
		if (session('user')['id']) {
			$id = I('post.id');
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
				// $this->ajaxReturn($jk);
				if ($jk[0]['num'] < $va) {
					$zy[] = $ke;
				}
			}
			// dump($zy);exit;
			// $this->ajaxReturn($zy);
			if (empty($zy)) {
				$this->ajaxReturn(2);
			} else {
				$this->ajaxReturn($zy);
			}
		} else {
			$this->ajaxReturn(1);
		}
	}
}