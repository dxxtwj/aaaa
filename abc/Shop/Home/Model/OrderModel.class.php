<?php
namespace Home\Model;
use Think\Model;

class OrderModel extends Model 
{


		 /**
	 *处理显示广告管理-广告管理的数据
	 */
	public function getData() 
	{	
		$arr = $this->select();
		$state = ['1' => '待付款', '待发货', '待收货', '立即评价', '交易成功'];

		// 在返回之间处理数据
		foreach ($arr as $k=>$v) {
			$arr[$k]['state'] = $state[$v['state']];
			$arr[$k]['atime'] = date('Y-m-d H:i:s', $v['atime']);
		}
		return $arr;
	}
	
	/**
	 * 订单列表的处理
	 * 订单状态 1 待付款 2 待发货 3 发货 4立即评价 5 交易成功 
	 */
	public function order() {

		$state = ['1' => '待付款', '待发货', '待收货', '立即评价', '交易成功'];


		$arr = $this->select();

		foreach ($arr as $k => $v) {

			// 状态
			$arr[$k]['state'] = $state[$v['state']];

			// 添加时间
			$arr[$k]['atime'] = date('Y-m-d H:i:s', $v['atime']);

			// 付款时间
			$arr[$k]['ptime'] = date('Y-m-d H:i:s', $v['ptime']);

			// 发货时间
			$arr[$k]['stime'] = date('Y-m-d H:i:s', $v['stime']);

			// 订单ID -> 查询订单详情表
			$uid[] = $v['id'];
		}
		$joinId = join(',', $uid);
		$arr['id'] = $joinId;
		return $arr;

	}

	

}