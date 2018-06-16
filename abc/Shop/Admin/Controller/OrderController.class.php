<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;

class OrderController extends Controller
{
	public function index() {
		
		$user = D('Order');
		$id = I('get.id');

		$total['1'] = $user->count();
        $total[] = $user->where(['state'=>'1'])->count();
        $total[] = $user->where(['state'=>'2'])->count();
        $total[] = $user->where(['state'=>'3'])->count();
        $total[] = $user->where(['state'=>'4'])->count();

        //判断要显示的订单
        if ($id == '1') {
        	$p = new Page($total['2'], 5);
        } else {
        	if ($id == '2') {
        		$p = new Page($total['3'], 5);
        	} else {
        		if ($id == '3') {
        			$p = new Page($total['4'], 5);
        		} else {
        			if ($id == '4') {
	        			$p = new Page($total['5'], 5);
        			} else {
        				$p = new Page($total['1'], 5);
        			}
        		}
        	}	
        }
		$arr = $user->wait($p, $id);
        $brr = $p->show();
    	$this->assign('total', $total);
		$this->assign('brr', $brr);
		$this->assign('arr', $arr);
		$this->display();
	}

	//发货处理
	public function save() {
		$order = D('order');
		$id = I('get.');
		$wlid = $order->create($id);
		if (!$wlid) {
			$this->ajaxReturn(2);
		}
		$data = $order->where(['id'=>$id['id']])->select();
		if ($data[0]['state'] == '2') {
			$arr['state'] = '3';
			$arr['wlname'] = $id['wlname'];
			$arr['wlid'] = $id['wlid'];
			$wlname = $order->wlname($arr['wlname']);
			$brr = $order->where(['id'=>$id['id']])->save($arr);
			$this->ajaxReturn($wlname);
		} else {
			$this->ajaxReturn(1);
		}
	}

	//显示商品详情页
	public function details() {
		$order = D('order');
		$arr = $order->where(['id'=>I('get.id')])->field('id,username,userphone,wlid,uaddress,state,atime,wlname,stime')->single();
		$data = M('orderdata');
		$brr = $data->where(['oid'=>I('get.id')])->field('pname,specifications,num,pmoney,plogo')->select();
		foreach($brr as $v) {
			$crr['num'] += $v['num'];
		}
		foreach($brr as $v) {
			$crr['pmoney'] += $v['pmoney'];
		}
		$this->assign('crr', $crr);
		$this->assign('brr', $brr);
		$this->assign('arr', $arr);
		$this->display();
	}

	//修改收件人信息
	public function edit() {
		if (IS_AJAX) {
			if (IS_GET) {
				$order = M('order');
				$id = I('get.id');
				$res = $order->field('username,userphone,uaddress')->where(['id'=>$id])->find();
				$res['id'] = $id;
				$this->ajaxReturn($res);			
			} else {
				$order = D('order');
				$arr = $order->create(I('post'));
				if (!$arr) {
					$res = $order->getError();
					if ($res == '收件人填写错误') {
						$this->ajaxReturn(3);
					} else {
						//手机号填写错误返回2
						$this->ajaxReturn(2);
					}
					
				} else {
					$id = I('post.id');
					$post = I('post.');
					$res = $order->field("id,username,userphone,uaddress")->where(['id'=>$id])->save($post);
					$this->ajaxReturn($res);
				}	
			} 
		}
	}

	//用于秒杀页面
	public function miao() {
		$this->display();
	}

	public function handle() {

		$seckill = M('seckill');
		
		$res = I('post.');
		
		$post = $res['year'].'-'.$res['month'].'-'.$res['day'].' '.$res['time'].':'.$res['branch'].':0';
		$ti = strtotime($post);
		// dump($ti);exit;

		$time = time();
		// dump(date('Y-m-d H:i:s',$time));
		//活动结束时间
		$times = $ti + 120;
		$y['year'] = date('Y', $times);
		$y['month'] = date('m', $times);
		$y['day'] = date('d', $times);
		$y['time'] = date('H', $times);
		$y['branch'] = date('i', $times);
		$y['second'] = 0;
		
		//秒杀商品
		$goods = M('goods');
		$ord = $goods->where(['state'=>2])->field('id,name,money,sellnum,image1,content')->limit(4)->select();
		// dump($ord);exit;
		$deta = M('detailed');
		//将秒杀商品进行遍历
		foreach($ord as $k=>$v) {
			$cs = $deta->where(['gid'=>$v['id']])->field('id,price')->select();

			//遍历某件商品对应的属性id和价格
			foreach($cs as $ke=>$va) {
				$jk[$va['id']] = $va['price'];
			}
		}

		foreach($jk as $key=>$val) {
			// $deta->where(['id'=>$key])->save(['prices'=>$val]);
			$deta->where(['id'=>$key])->save(['price'=>9.9]);
		}

		// dump($y);exit;

		// dump($times);exit;
		$asd = $ti-$time;
		if ($asd > 86400 || $asd < 0) {
			$this->error('只能开启24小时内的秒杀活动', '', 2);
		}

		// dump($res);exit;

		$arr = $seckill->find();
		// $arr[]
		// $sb = $arr['year'].'-'.$arr['month'].'-'.$arr['day'].' '.$arr['time'].':'.$arr['branch'].':'.$arr['second'];
		// $cha = strtotime($sb);
		// dump($cha);exit;

		if ($arr['data'] != 4) {
			$this->error('上一次秒杀还未结束！', '', 2);
		} else {
			// $arr[] = '1';
			
			$res['data'] = '2';
			$res['id'] = '1';
			// dump($res);exit;
			//修改活动结束事件
			$saves = $seckill->where(['id'=>2])->save($y);
			$save = $seckill->where(['id'=>1])->save($res);
			if ($save === false) {
				$this->error('开启失败', '', 1);
			} else {
				$this->success('开启秒杀成功', U('Order/miao'), 1);
			}
		}
	}
}