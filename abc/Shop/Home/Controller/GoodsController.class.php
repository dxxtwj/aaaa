<?php
namespace Home\Controller;
use Think\Controller;
use Think\Page;
use \Redis;

class GoodsController extends CommonController 
{

	/**
	 * 显示商品列表页
	 */
	public function index() {
		
		if ($_GET['search']) {
			
			// 讯搜
			vendor('xunsearch.php.lib.XS');

			// 实例化讯搜
			$xunsearch = new \XS('goods');

	
			// 管理索引
			$search = $xunsearch->search;
	
			// 取出数据id
			$res = $search->setQuery($_GET['search'])->search();
		
			if (!empty($res)) {
				foreach ($res as $k => $v) {
					$goodsId[] = $v['id'];
				}
				// 通过id找goods对应的数据
				$goods = M('Goods');
				$joinGoodsId = join(',', $goodsId);
				$goodsIds = array('id' => ['in', $joinGoodsId]);
				$goodsList = $goods->where($goodsIds)->select();

				// 通过商品表的pid找品牌
				foreach ($goodsList as $v) {

					$goodsPid[] = $v['pid'];
					$goodsCid[] = $v['cid'];
				}

				// 去掉重复的品牌id
				$goodsPid = array_unique($goodsPid);
				
				// 品牌区域
				$joinGoodsPid = join(',', $goodsPid);
				$goodsPids = array('id' => ['in', $joinGoodsPid]);
				$pinPai = M('Pinpai');
				$pinPaiList = $pinPai->where($goodsPids)->select();
				
				// 分类区域
				$joinGoodsCid = join(',', $goodsCid);
				$goodsCids = array('id' => ['in', $joinGoodsCid]);
				$type = M('Type');
				$typeList = $type->where($goodsCids)->select();

			} else {

				// 没有对应的商品区域
				$pinPaiList = M('Pinpai')->select();
				$typeList = M('Type')->select();
			}
		} 

		if (!empty($_GET['pid'])) {

			if ($_GET['pid'] == 'a') {

				$type = M('Type');
				$typeFind = $type->where(['id' => $_GET['id']])->find();

				
				$num = substr_count($typeFind['path'], ',');

				if ($typeFind['pid'] === '0' || $num === 2) {
					// 顶级或者二级 
					unset($_GET['pid']);
					
					
				} elseif ($num >= 3){
					// 三级
					unset($_GET['pid']);
				}
			}
		}

		// 排序
		if (!empty($_GET['order'])) {

			// 替换字符串
			$str = str_replace('+', ' ', $_GET['order'] );

		} else {

			$str = 'state desc';
		}

		// 点击品牌时候
		if (!empty($_GET['pid'])) {

			session('pid', $_GET['pid']);
		}
		
		$typeId = I('get.id');

		// 查自己
		$typeInfo = D('Type')->field('path, name, id, pid')->find($typeId);

		if (!empty($typeInfo)) {

			// 取自己的path
			$path = $typeInfo['path'];

			// 查找有没有同级
			$data['path'] = ['like', $path.$typeId.',%'];
			$type = D('Type');
			$res = $type->where($data)->select();

			if (!empty($res)) {
				//顶级或者二级的
				
				foreach ($res as $v) {
					// 所有分类的id
					$typeIds[] = $v['id']; 
				}
					
				// 转换所有分类ID为字符串
				$joinTypeIds = join(',', $typeIds);

				// 查询所有分类
				$array = array('id' => ['in', $joinTypeIds]);
				$typeList = $type->where($array)->select();
				
				// 通过所有分类的id去找商品
				$goodsIds['cid'] = ['in', $joinTypeIds];

				// 状态为2 即在售中的商品
				$goodsIds['state'] = ['eq', 2];

				if (!empty($_GET['pid'])) {
					$goodsIds['pid'] = session('pid');

				}
				$goods = M('Goods');
				
				// 分页
				$count = $goods->where($goodsIds)->count();
				$page = new Page($count, 12);
				$show = $page->show();
				$goodsList = $goods->where($goodsIds)->order($str)->field('id, name, pid, money, sellnum, image0')->limit($page->firstRow, $page->listRows)->select();


				// 通过所有商品里面的PID找品牌
				foreach ($goodsList as $v) {
					$goodsListPid[] = $v['pid'];
				}
				
				if (!empty($goodsListPid)) {

					// 去掉重复的Pid			
					$goodsListPid = array_unique($goodsListPid);
					$joinGoodsListPid = join(',', $goodsListPid);
					
					// 通过商品表的pid查询相应的品牌
					$array = array('id' => ['in', $joinGoodsListPid]);
					$pinPai = M('Pinpai');
					$pinPaiList = $pinPai->where($array)->select();
				}

			} else {
				//三级的

				// 查询分类
				$type = M('Type');
				$array = array('pid' => $typeInfo['pid']);
				$typeList = $type->where($array)->select();
				
				// 查询点击的分类下的所有商品
				$goods = M('Goods');
				$goodsIds['cid'] = $_GET['id'];

				// 状态为2 即在售中的商品
				$goodsIds['state'] = ['eq', 2];

				if (!empty($_GET['pid'])) {
					$goodsIds['pid'] = session('pid');

				}
			
				// 分页
				$count = $goods->where($goodsIds)->count();
				$page = new Page($count, 12);
				$show = $page->show();
				$goodsList = $goods->where($goodsIds)->order($str)->field('id, name, pid, money, sellnum, image0')->limit($page->firstRow, $page->listRows)->select();	

				if (!empty($goodsList)) {
					// 通过商品查询品牌
					foreach ($goodsList as $v) {
						$goodsListPid[] = $v['pid'];
					}
				}

				if (!empty($goodsListPid)) {
					// 去掉重复的pid 然后查询品牌表
					$goodsListPid = array_unique($goodsListPid);
					$joingoodsListPid = join(',', $goodsListPid);
					$pinpai = M('Pinpai');
					$array = array('id' => ['in', $joingoodsListPid]);

					$pinPaiList = $pinpai->where($array)->select();
				}
				
			}

		} 


		/*
			商品推广
		 */

		// 判断缓存有没有
		$chuanXingHua = $this->redis->get('tuiGuangList');

		// 反串行化
		$redisTuiGuangList = unserialize($chuanXingHua);

		if (empty($redisTuiGuangList)) {

			// 查询数据库
			$tuiGuang = M('Tuijiangoods');
			$state['state'] = ['eq', 1];
			$tuiGuangList = $tuiGuang->limit(3)->field('id, gid, name, money, image, shouliang')->where($state)->select();

			if (!empty($tuiGuangList)) {

				// 设置键名
				foreach ($tuiGuangList as $v) {
					
					$redisJian['TuiGuang_'.$v['id']] = $v;
					
				}

				// 串行化 60秒时间
				$chuanXingHua = serialize($redisJian);
				$this->redis->set('tuiGuangList', $chuanXingHua, 60);
			
				
			}
		
		} else {
			
			// 取缓存数据
			$tuiGuangList = $redisTuiGuangList;

		}

			if (IS_AJAX) {

				for ($i = 0; $i < count($goodsList); $i++) {

					// 去除路径多余的点
					$lujing = ltrim($goodsList[$i]['image0'], ".");
					
					// 拼接商品数据
					$goodsAllList .= '<li><div class="i-pic limit"><a href="'.U('Detail/index', ['id' => $goodsList[$i]['id']]).'"><img class="lazy" src="/Public/Images/LanJiaZai/timg.gif" data-original="/Public'.$lujing.'"/></a><p class="title fl"><a href="'.U('Detail/index', ['id' => $goodsList[$i]['id']]).'">'.$goodsList[$i]['name'].'</a></p><p  class="price fl"><b>¥</b><strong><a href="'.U('Detail/index', ['id' => $goodsList[$i]['id']]).'"><span style="color: #e4393c;">'.$goodsList[$i]['money'].'</a></span></strong></p><p class="number fl">销量<a href="'.U('Detail/index', ['id' => $goodsList[$i]['id']]).'"><span style="color: #e4393c;">'.$goodsList[$i]['sellnum'].'</span></a></p></div></li>';
				}	
					
					// 追加商品数据
					$data['goodsAllList'] = $goodsAllList;				

					// 追加分页按钮
					$data['show'] = $show;

					// $data['typeList'] = $typeList;

					// $data['pinPaiList'] = $pinPaiList;
					$this->ajaxReturn($data);			
			}
			if (!empty($typeList)) {

				// 分配类型表数据
				$this->assign('sanjiType', $typeList);
			}
			if (!empty($pinPaiList)) {

				// 分类品牌表数据
				$this->assign('pinpaiList', $pinPaiList);
			}
			
			if (!empty($goodsList)) {

				// 分类商品表数据
				$this->assign('goodsList', $goodsList);
			}
			if (!empty($show)) {

				// 分页
				$this->assign('show', $show);
			}

			if (!empty($tuiGuangList)) {

				// 推广表
				$this->assign('tuiGuangList', $tuiGuangList);
				
			}

			// 模板渲染
			$this->display();
	}

	/**
	 * xunsearch纠错
	 * 返回的是商品
	 */
	public function search() {
		// 讯搜
		vendor('xunsearch.php.lib.XS');

		// 实例化讯搜
		$xunsearch = new \XS('Goods');

		// 管理索引
		$search = $xunsearch->search;

		// 取出数据id
		$res = $search->setQuery($_GET['jiucuo'])->search();
		
		// 英文区域
		$jiuCuo = $search->getCorrectedQuery();
	
		if ($jiuCuo) {
			$res = $search->setQuery($jiuCuo[0])->search();
		
			foreach ($res as $v) {
				
				$iDs[] = $v['id'];
			}
			
			$joiniDs = join(',', $iDs);
			$array = array('id' => ['in', $joiniDs]);
			$a = M('Goods')->where($array)->field('id, name')->select();
		}
		$this->ajaxReturn($a);
	}


}