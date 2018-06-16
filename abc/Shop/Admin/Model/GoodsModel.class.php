<?php
namespace Admin\Model;
use Think\Model;

class GoodsModel extends Model

{

	protected $_validate = [
		// ['name', '/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、下划线、中文组成的商品名名'],

		['ymoney', 'require', '请输入市场价'],

		['ymoney', '/^[0-9]{1,4}$/', '市场价范围不可以超过9999'],

		// ['money', 'require', '请输入现价'],

		// ['money', '/^[0-9]{1,3}$/', '现卖价范围不可以超过999'],

		['fenlei', 'require', '请选择分类'],

		['pinpai', 'require', '请选择品牌'],

		// ['num', 'require', '请输入库存,最多不能超过500'],

		// ['num', '/^[0-9]{1,3}$/', '库存范围是1~99'],

		['chandi', 'require', '请输入原料产地'],

		['baozhiqi', 'require', '保质期不能为空'],

		['baozhiqi', '/^\d/', '保质期只能是数字'],

		['material', 'require', '请输入原料'],

		['norm', 'require', '请输入生成许可证'],

		['weight', 'require', '请输入重量，千克'],

		// ['baozhuang', 'require', '请选择包装'],

		// ['baozhuang', '', '该包装已存在', 0, 'unique', 3],

		// ['baozhuang', '/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、下划线、中文组成的包装'],
		
		// ['kouwei', 'require', '请选择口味'],

		// ['kouwei', '/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、下划线、中文组成的口味'],
		// ['kouwei', '', '改口味已存在', 0, 'unique', 3],



	];

	/**
	 * 这个方法是为了处理时间和状态的
	 */
	public function data() {

		$arr = $this->select();
		
		// 定义数组
		$state = ['1' => '新上架,待销售', '在售中', '已下架'];

		foreach ($arr as $k => $v) {
			
			// 处理状态
			$arr[$k]['state'] = $state[$v['state']];

			// 处理时间
			$arr[$k]['atime'] = date('Y-m-d H:i:s', $v['atime']);
		}
	
		return $arr;
	}

}