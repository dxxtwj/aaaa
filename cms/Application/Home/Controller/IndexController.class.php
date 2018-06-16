<?php

namespace Home\Controller;

use Think\Controller;



header("Content-type: text/html; charset=utf-8");

class IndexController extends HomeController {

	/**
	 * 手机端首页
	 */
	public function index(){
		
        //轮播图
		$br = M("BannerRec");
		$br_where['BR_Is_Show'] = 1;
		$br_where['BR_Type'] = 1;
		$br_data = $br
		    ->where($br_where)
		    ->field('BR_IMG as img, BR_Url as url')
		    ->order('BR_Sort desc')
		    ->select();
		$this->assign('br_data_json', json_encode($br_data));

		//导航
		$nr = M("NavigationRec");
		$nr_limit = 10;
		$nr_where['NR_Is_Show'] = 1;
		$nr_data = $nr
			->field('NR_Name as name, NR_IMG as img, NR_Url as url')
			->where($nr_where)
			->limit($nr_limit)
			->order('NR_Sort desc')
			->select();
		
		$this->assign('nr_data_json', json_encode($nr_data));
	
		// 商品表
		$goods = M("GoodsRec");
		$goodsWhere['GR_Is_Show'] = 1; // 是否上架 
		$goodsWhere['GR_Is_Recommend'] = 1; // 是否推荐到首页展示
		$goodsWhere['GR_Is_Delete'] = 0;
		$num = 4; // 每页显示条数
		if (IS_AJAX && !empty($_GET['page'])) { // 前台懒加载ajax分页区间

			$page = $_GET['page'];
			$goodsRes = $goods->field('GR_ID as id, GR_Name as name, GR_IMG as img, GR_Price as price, GR_Old_Price as oldPrice')
				->where($goodsWhere)
				->order('GR_Sort desc')
				->limit($page * $num, $num)
				->select();

			foreach ($goodsRes as $k => $v) {
				$goodsRes[$k]['price'] = $goodsRes[$k]['price'] * 0.01;//现价
				$goodsRes[$k]['oldPrice'] = $goodsRes[$k]['oldPrice'] * 0.01;// 原价
			}
			if (!empty($goodsRes)) { // 查到有数据区间

				$goods_rec['data'] = $goodsRes; // 调用处理数据的一个方法
				$goods_rec['code'] = 1;
				$goods_rec['msg'] = '返回数据 data';
				$goods_rec['success'] = true;

			} else { // 没有数据区间

				$goods_rec['data'] = null; 
				$goods_rec['code'] = 0;
				$goods_rec['msg'] = '暂无相关数据';
				$goods_rec['success'] = true;

			}

			$this->ajaxReturn($goods_rec);

		}
		
		// 用户初次到来，即默认显示3条商品数据区间
		$goodsRes = $goods->field('GR_ID as id, GR_Name as name, GR_IMG as img, GR_Price as price, GR_Old_Price as oldPrice')
			->where($goodsWhere)
			->order('GR_Sort desc')
			->limit($num)
			->select();

		foreach ($goodsRes as $k => $v) {
			$goodsRes[$k]['price'] = $goodsRes[$k]['price'] * 0.01;//现价
			$goodsRes[$k]['oldPrice'] = $goodsRes[$k]['oldPrice'] * 0.01;// 原价

		}
		
		$this->assign('gr_data_json', json_encode($goodsRes));
		$this->display();

		
	}
	
}