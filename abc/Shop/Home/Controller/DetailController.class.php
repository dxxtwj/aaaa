<?php
namespace Home\Controller;
use Think\Controller;

Class DetailController extends CommonController
{
	/**
	 *详情表显示
	 */
	public function index() {

		if (empty($_GET['id'])) {

			$this->error('404');
			exit;
		}

		//查询秒杀开始时间
		// $res = M('Seckill');
		// $arr = $res->where(['id'=>1])->find();
		// $brr = $res->where(['id'=>2])->find();
		// $crr = $res->where(['id'=>1])->field('data')->find();
		// $deta = M('detailed');


		//当活动结束之后修改价格
		// if ($crr['data'] == '4') {
		// 	$go = M('goods');
		// 	$ae = $go->order('id desc')->limit(4)->field('id')->select();
		// 	foreach($ae as $k=>$va) {
		// 	$fi = $deta->where(['gid'=>$va['id']])->field('id,prices')->select();
		// 		foreach($fi as $key=>$val) {
		// 			if($val['prices'] == '9.9') {

		// 			} else {
		// 				$arrs['price'] = $val['prices'];
		// 				$sa = $deta->where(['id'=>$val['id']])->save($arrs);
		// 				$as['prices'] = '9.9';
		// 				$sb = $deta->where(['id'=>$val['id']])->save($as);
		// 			}
		// 		}
		// 	}	
		// }


		
		// 查询收藏表
		$shouCang = M('Shoucang');
		$shouCangTj['uid'] = $_SESSION['user']['id'];
		$shouCangTj['gid'] = $_GET['id'];
		$ShoucangFind = $shouCang->where($shouCangTj)->find();
		
		
		if ($ShoucangFind) {
			// 已收藏
			$shouCangs = 1;

		} else {
			// 未收藏
			$shouCangs = 2;
		}

		// 分配收藏
		$this->assign('shouCangs', $shouCangs);
		/*
			查询评论表,分页
		 */
		$pinlun = D('Pinlun');
		$count = $pinlun->where(['gid' => $_GET['id']])->count();
		
		$page = new \Think\Page($count, 5);
		$show = $page->show();

		$pinLunList = $pinlun->limit($page->firstRow.','.$page->listRows)->where(['gid' => $_GET['id']])->pinlun();
		
		// 分配好评  中评  差评  然后删除多余的
		$this->assign('haoPing', $pinLunList['haoPing']);
		$this->assign('zhongPing', $pinLunList['zhongPing']);
		$this->assign('chaPing', $pinLunList['chaPing']);
		$this->assign('zong', $pinLunList['zong']);
		unset($pinLunList['haoPing']);
		unset($pinLunList['zhongPing']);
		unset($pinLunList['chaPing']);
		unset($pinLunList['zong']);


		if (IS_AJAX) {
			$data['pinLunList'] = $pinLunList;
			$data['show'] = $show;

			$this->ajaxReturn($data);
			exit;
		}

		/*
			查询省份表
		 */
		// 去掉中国
		$array['id'] = ['neq', 1];
		$areas = M('Areas');
		$areasList = $areas->field('id, parent_id, area_name, area_type')->where($array)->select();

		$detailedData = $this->redis->get('Detail'.$_GET['id']);

		if (empty($detailedData)) {
			/*
				查询商品表
			*/
		
			// 缓存没数据
			$goods = M('Goods');

			$goodsList = $goods->where(['id' => $_GET['id']])->field('id, image0, image1, image2, image3, image4, image5, chandi, name, sellnum, ymoney ,clicknum, collectnum, baozhiqi, commentnum, weight, norm, material, stockpile, cid')->select();

			// 查询商品所属分类,并分配名字
			$type = M('Type');
			$typeName = $type->where(['id' => $goodsList[0]['cid']])->field('name')->find();
		

			// 获取图片，有就获取
			if ($goodsList[0]['image0']) {

				$goodsImage[] = $goodsList[0]['image0'];
			}
			if ($goodsList[0]['image1']) {

				$goodsImage[] = $goodsList[0]['image1'];
			}
			if ($goodsList[0]['image2']) {

				$goodsImage[] = $goodsList[0]['image2'];
			}
			if ($goodsList[0]['image3']) {

				$goodsImage[] = $goodsList[0]['image3'];
			}
			if ($goodsList[0]['image4']) {

				$goodsImage[] = $goodsList[0]['image4'];
			}
			if ($goodsList[0]['image5']) {

				$goodsImage[] = $goodsList[0]['image5'];
			}

			
			// 查询详情图片，口味，包装
			$images = M('GoodsImages');
			$detailedList = $images->where(['gid' => $_GET['id']])->field('image1, image2, image3, image4, image5, image6, image7, image8, image9')->select();

			// 处理图片
			foreach ($detailedList as $v ) {

				if ($v['image1']) {

					$image[] = $v['image1'];
				}
				if ($v['image2']) {

					$image[] = $v['image2'];
				}
					if ($v['image3']) {

					$image[] = $v['image3'];
				}
					if ($v['image4']) {

					$image[] = $v['image4'];
				}
					if ($v['image5']) {

					$image[] = $v['image5'];
				}
					if ($v['image6']) {

					$image[] = $v['image6'];
				}
					if ($v['image7']) {

					$image[] = $v['image7'];
				}
					if ($v['image8']) {

					$image[] = $v['image8'];
				}

			}

			// 去掉重复的图片
			$image = array_unique($image);
			
			// 追加图片
			for ($i = 1; $i < count($image); $i++) {

				$pic[$i] = $image[$i];
			}

			// 查询
			$detailed = M('Detailed');
			$attr = $detailed->where(['gid' => $_GET['id']])->field('id, kouwei, baozhuang, num, price')->select();
		
			
			// 获取最高价钱和最低价钱
			foreach ($attr as $v) {

				$price[] = $v['price'];
			}
			
			// 价钱最大值
			$max[] = max($price);

			// 价钱最小值
			$min[] = min($price);
			


			// 获取包装
			foreach ($attr as $val) {

				$baozhuang[] = $val['baozhuang'];
			}

			// 去重，返回一维数组
			$a = array_unique($baozhuang);
			
			// 重新从0开始排序数组,返回一维数组，重新排序为了方便遍历
			$baozhuangs = array_values($a);

			// 获取口味
			foreach ($attr as $val) {

				$kouwei[] = $val['kouwei'];
			}

			// 去重，返回一维数组
			$b = array_unique($kouwei);
			
			// 重新从0开始排序数组,返回一维数组，重新排序为了方便遍历
			$kouweis = array_values($b);
			
			// 存缓存
			// 商品goods
			$detailDatas['goodsList'] = $goodsList;

			// 商品基本图片
			$detailDatas['goodsImage'] = $goodsImage;

			// 商品详情图片
			$detailDatas['pic'] = $pic;

			// 商品价格最小值
			$detailDatas['min'] = $min;

			// 商品价格最大值
			$detailDatas['max'] = $max;

			// 商品组合的价格
			$detailDatas['price'] = $price;

			// 商品的包装
			$detailDatas['baozhuangs'] = $baozhuangs;

			// 商品口味
			$detailDatas['kouweis'] = $kouweis;
			
			$redisDetailData['detail'.$_GET['id']] = $detailDatas;
			$detailedArr = $redisDetailData;
			$DetailchuanXingHua = serialize($detailedArr);
			$this->redis->set('Detail'.$_GET['id'], $DetailchuanXingHua, 30);

			// 存进浏览记录
			// 存储ID
			$bool = $this->redis->get('liuLangLiShiId');
			$bool = strrchr($_GET['id'], $bool);
			
			if (!empty($bool)) {
				// 追加
				$a = $this->redis->append('liuLangLiShiId', $_GET['id'].',');
				
			} else {

				$this->redis->set('liuLangLiShiId', $_GET['id'].',', 30);

			}
			
			// 存储数据
			$liulangLiShi[] = $goodsList[0]['id'];
			$liulangLiShi[] = $goodsList[0]['image0'];
		    $liulangLiShi[] = $goodsList[0]['name'];
		    $liulangLiShi[] = $min;

			// 存储数据
			$chuanxinghua = serialize($liulangLiShi);
			$this->redis->set('liuLangLiShi_'.$_GET['id'], $chuanxinghua, 30);

		} else {

			$a = unserialize($detailedData);
			$detailedData = $a['detail'.$_GET['id']];
		
			// 商品goods
			$goodsList = $detailedData['goodsList'];

			// 商品基本图片
			$goodsImage = $detailedData['goodsImage'];

			// 商品详情图片
			$pic = $detailedData['pic'];

			// 商品价格最小值
			$min = $detailedData['min'];

			// 商品价格最大值
			$max = $detailedData['max'];

			// 商品组合的价格
			$price = $detailedData['price'];

			// 商品的包装
			$baozhuangs = $detailedData['baozhuangs'];

			// 商品口味
			$kouweis = $detailedData['kouweis'];

			$a = $this->redis->get('liuLangLiShi');
			
			
			$s = unserialize($a);
		}
		
		/*
			浏览历史
		 */
		$bool = $this->redis->get('liuLangLiShiId');
		$bool = trim($bool, ',');
		
		// 分配为数组
		$arrLiuLang = explode(',', $bool);
		$arrLiuLang = array_unique($arrLiuLang);

		for ($i = 0; $i < count($arrLiuLang); $i++) {

			$arrLiuLangId[] = $arrLiuLang[$i];
		}

		// 数组降序
		rsort($arrLiuLangId);

		// 固定显示3个浏览历史
		if ($arrLiuLangId[0]) {

			$ids[] = $arrLiuLangId[0];
		}		
		if ($arrLiuLangId[1]) {

			$ids[] = $arrLiuLangId[1];
		}		
		if ($arrLiuLangId[2]) {

			$ids[] = $arrLiuLangId[2];
		}	
		

		// 循环串行化
		for ($i = 0; $i < count($ids); $i++) {
				
			$lishi = $this->redis->get('liuLangLiShi_'.$ids[$i]);
			$liuLangJilu[] = unserialize($lishi);
		}
		
		// 分配浏览记录
		$this->assign('liuLangJilu', $liuLangJilu);
		
		
		// 评论表数据，分页
		$this->assign('pinLunList', $pinLunList);
		$this->assign('show', $show);
		$this->assign('areasList', $areasList);

		// 分配商品表数据,和分类名字
		$this->assign('typeName', $typeName);
		$this->assign('goodsList', $goodsList);


		// 分配商品基本图片
		$this->assign('goodsImage', $goodsImage);

		// 分配详情图片
		$this->assign('pic', $pic);

		// 分配最高价钱和最低价钱
		$this->assign('min', $min);
		$this->assign('max', $max);

		// 分配口味包装
		$this->assign('baozhuangs', $baozhuangs);
		$this->assign('kouweis', $kouweis);

		//分配定时器时间
		$this->assign('arr', $arr);
		$this->assign('brr', $brr);
		$this->assign('crr', $crr);

		$this->display();
	}

	/**
	 * 这个方法是详情表里面的三级联动的处理
	 */
	public function areas() {

		if (IS_AJAX) {
			
			// 查询下一级的省份或城市
			$areas = M('Areas');
			$array['id'] = ['neq', 1];
			$array['parent_id'] = $_GET['id'];
			$areasList = $areas->where($array)->field('id, parent_id, area_name, area_type')->select();

			$this->ajaxReturn($areasList);
			exit;
		}
	}

	/**
	 * ajax的猜你喜欢
	 * 通过商品ID查询出该商品的分类
	 * 拿到该分类下的ID查询所有属于他的商品
	 */
	public function cainixihuan() {

		if (IS_AJAX) {

			$goods = M('Goods');
			$goodsCid = $goods->where(['id' => $_GET['id']])->field('cid')->find();

			// 分类
			$type = M('Type');
			$typeList = $type->where(['id' => $goodsCid['cid']])->field('id')->find();
			
			// 该分类下的所有商品
			$goodsList = $goods->where(['cid' => $typeList['id']])->limit(8)->field('id, image0, money, name')->select();
			
			$this->ajaxReturn($goodsList);
			exit;
		}
		
	}

	/**
	 * 这个方法发是用户点击包装然后查询口味的
	 * 然后检测是否存在该口味，存在则返回库存和价钱
	 * 还有信息
	 */
	public function attr() {
		$model = M('Detailed');

		$array['gid'] = $_GET['gid'];
		$array['baozhuang'] = $_GET['baozhuang'];

		$arr = $model->where($array)->field('id, kouwei, num, price, gid')->select();
		
		$this->ajaxReturn($arr);
		exit;
		
	}

	/**
	 * 这个方法是用户点击口味后显示库存和口味的
	 */
	public function kp() {

		$model = M('Detailed');
		$array['gid'] = $_GET['gid'];
		$array['baozhuang'] = $_GET['baozhuang'];
		$array['kouwei'] = $_GET['kouwei'];
		$arr = $model->where($array)->field('price, image, num')->select();
		
		$this->ajaxReturn($arr);
		exit;
	}

	/**
	 * 处理详情表id和用户点击的数量拼接
	 */
	public function chuLi() {
		$model = M('Detailed');

		// 接收数据并查询
		$array['gid'] = $_GET['gid'];
		$array['kouwei'] = $_GET['kouwei'];
		$array['baozhuang'] = $_GET['baozhuang'];
		$array['price'] = $_GET['price'];
		$arr = $model->where($array)->field('id')->find();

		// 拼接
		$shuliang['shuliang'] = $_GET['shuliang'];
		$pinjie = $arr['id'].'.'.$shuliang['shuliang'];
	
		
		$this->ajaxReturn($pinjie);
		exit;
	}

	
}
