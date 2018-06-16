<?php
namespace Home\Controller;
use Think\Controller;
use Com\Queue;


class IndexController extends Controller {
	/**
	 * 首页数据处理
	 */
	public function index() {
		// dump(session());exit;
		$res = D('Seckill');
		$arr = $res->Seckill();
		$brr = $res->where(['id'=>2])->find();

		// dump($brr);exit;
		//热门商品
		$goods = M('goods');
		$order = $goods->order('sellnum desc')->field('id,name,money,sellnum,image0,content')->limit(4)->select();
		//秒杀商品
		$ord = $goods->order('id desc')->where(['state'=>2])->field('id,name,money,sellnum,image0,content')->limit(4)->select();

		

		// dump($zy);exit;
// 		$deta = M('detailed');
// 		//将秒杀商品进行遍历
// 		foreach($ord as $k=>$v) {
// 			$cs = $deta->where(['gid'=>$v['id']])->field('id,price')->select();

// 			//遍历某件商品对应的属性id和价格
// 			foreach($cs as $ke=>$va) {
// 				$jk[$va['id']] = $va['price'];
// 			}
// 		}
// // 		dump($jk);
// // dump($cs);exit;
// 		foreach($jk as $key=>$val) {
// 			$deta->where(['id'=>$key])->save(['prices'=>$val]);
// 			// $deta->where(['id'=>$key])->save(['price'=>$val]);
// 		}
		// dump($key);exit;
		// dump($jk);exit;
		// dump($cs);exit;
		// dump($cs);exit;

		// dump($id);exit;
		// dump($ord);exit;
		// dump($ord);exit;
		//如果当前时间小于秒杀，修改状态
		$time = $arr['year'].'-'.$arr['month'].'-'.$arr['day'].' '.$arr['time'].':'.$arr['branch'].':0';
		$po = strtotime($time);
		$so = time();
		$no = $po-$so;
		if ($no < 0 && $arr['data'] == '2') {
			$true['data'] = '3';
			$mn = $res->where(['id'=>'1'])->save($true);
		}

		$type = M('type');
		$data = $type->where(['pid'=>'0'])->select();

		if (IS_AJAX) {
			//选项卡
			if (IS_GET) {
				$tab = M('tab');
				$tab = $tab->where(['uid'=>I('get.id')])->select();
				$this->ajaxReturn($tab);
			} else {
				//遍历左侧菜单
				$id = I('post.id');
				$zxc = S($id);
				// 如果缓存存在，直接获取缓存的数据
				if ($zxc) {
					
					$this->ajaxReturn($zxc);	
				} else {
					//遍历拼接首页二三级菜单
					$post = $type->where(['pid'=>$id])->field('id,name')->select();
				

					$count = count($post);


					for ($i=0;$i<$count;$i++) {
						$post=$type->limit("$i,1")->where(['pid'=>$id])->select();

						foreach($post as $k=>$v) {
							$er = $type->where(['pid'=>$v['id']])->select();
						
							$asd = "<dl class='dl-sort'><dt><span title=".$post[$k]['name'].">".$post[$k]['name']."</span></dt>";
						}
								

						foreach ($er as $val) {

							$asd .= '<dd><a title='.$val['name'].' href="'.U('Goods/index', ['id'=>$val['id']]).'"><span>'.$val['name'].'</span></a></dd>';
						}	
							$scc .= $asd.'</dl>';
					}
					//首次查询写入缓存
					S($id, $scc);
					$this->ajaxReturn($scc);
				}
			}		
		}
		
		// 连接广告表
		$ad = D('Ad');
		// 查询首页大轮播图的广告
		$Carousel = $ad->where(['aid'=>17])->select();
		// 查询立即抢购的广告
		$Immediate = $ad->where(['aid'=>18])->select();

		// 连接广告分类表
		$adSort = D('adSort');
		// 查询广告分类表的所有id,分类名
		$sort = $adSort->field('id,sort')->select();
		// 拿出sort作为值, id作为键
		$res = array_column($sort, 'sort', 'id');
		
		
		/*
			QQ互连登录
		 */
		// 导入文件
		Vendor('Connect.qqConnectAPI');

		if (!empty($_COOKIE['qq_openid'])) {
			
			// 传值去获取QQ的详细信息
			$qc = new \QC($_COOKIE['qq_accesstoken'], $_COOKIE['qq_openid']);
			$userinfo = $qc->get_user_info();

			// 名字
			$qqData['username'] = $userinfo['nickname'];

			// 性别
			$qqData['gender'] = $userinfo['gender'];

			// 头像
			$qqData['figureurl_1'] = $userinfo['figureurl_1'];

			// 把数据存进session
			session('user', $qqData);
		}
	
		
		$this->assign('zy', $zy);
		$this->assign('res', $res);
		$this->assign('Carousel', $Carousel);		
		$this->assign('Immediate', $Immediate);
		$this->assign('ord', $ord);
		$this->assign('brr', $brr);
		$this->assign('order', $order);
		$this->assign('data', $data);
		$this->assign('arr', $arr);
		$this->display();
	}


	//首页秒杀功能
	public function miaosha() {
		$redis = new \Redis();
		$redis->connect('127.0.0.1', 6379);
		// $arr = $redis->lPop($redis_name);
		// $arr = $redis->lLen('ms');
		// dump($arr);exit;
		
		$redis_name = 'ms';
		// // $uid = '1';
		// // $uid = $_GET['uid'];
		if ($redis->lLen($redis_name) < 10) {
			$redis->rPush($redis_name, $uid.'%'.microtime(true));
			$this->ajaxReturn(2);
			// echo $uid.'秒杀成功';
		} else {
			// echo 321;exit;
			// echo '秒杀失败';
			$arr = $this->end();
			$this->ajaxReturn('1');
		}
			$redis->close();	
	}

	//当秒杀开始，修改数据库,前台改为进行中
	public function hand() {
		$user = M('seckill');
		$stat['data'] = '3';
		$stat['id'] = '1';
		$arr = $user->where(['id'=>'1'])->save($stat);
		// $this->ajaxReturn();
		// dump($user->_sql());
	}

	//修改抢购完成时的状态
	public function end() {
		$user = M('seckill');
		$stat['data'] = '4';
		$stat['id'] = '1';
		$arr = $user->where(['id'=>'1'])->save($stat);
	}

	public function gif() {
		$goods = M('goods');
		//推荐
		$zy = $goods->order('id asc')->where(['state'=>2])->field('image0')->limit(7)->select();
		// $arr = $ceshi->field('id,name1,name2,name3,name4,name5,name6,name7')->find();
		// dump($zy);exit; 
		// sleep(1);
    	$this->ajaxReturn($zy);
		// dump($arr);exit;
	}

}

// vendor('wwj.memcached', '', '.class.php');
// S(array(
// 'type'=>'memcache',
// 'host'=>'192.168.32.127',
// 'port'=>'11211',
// // 'prefix'=>'think',
// 'expire'=>60)
// );
// S('key', '金珂', 1000);
// $arr = S('key');
		// $m = new \Mem();
		// var_dump($m);exit;
		// $server = array(
		// 		array('127.0.0.1', 11211),
		// 	);
		// // echo $m->getError();
		// $m->addServer($server);
		// $arr = $m->s('key', 'value', 120);
		// $arr = 1;
		// echo $arr;