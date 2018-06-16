<?php
namespace Home\Controller;
use Think\Controller;
use \Redis;

class CommonController extends Controller 
{

	/**
	 * 实例化redis
	 */
	public function _initialize() {

		// 实例化redis
		$this->redis = new Redis();

		// 设置端口
		$this->redis->connect('localhost', 6379);


	}
	
	/**
	 * 循环返回库存
	 * $a 为要修改的详情数据ID
	 * $b 为要修改的详情数据库存
	 */
	public function saveorder($a, $b) {
			
		$detailed = M('Detailed');

		// 查询出现在商品的库存
		$detailedNum = $detailed->where(['id' => $a])->field('num')->find();
	
		// 返回库存
		$num['num'] = $b + $detailedNum['num'];
		$arr = $detailed->where(['id' => $a])->save($num);
	}


}