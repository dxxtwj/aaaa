<?php
namespace Admin\Model;
use Think\Model;
class PinpaiModel extends Model {

	protected $_validate = [
		// 判断品牌名是否为空
		['name', 'require', '请检查品牌名是否为空'],

		// 判断特殊字符
		['name', '/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、下划线、中文组成的品牌名'],

		// 判断商品价格
		// ['price', '/^[0-9]+(.[0-9]{1,2})$/u', '请输入100.00这样格式的商品价格'],

		// 判断品牌是否存在
		['name', '', '该品牌已存在', 0, 'unique', 3],
		
		// 判断排序
		// ['order', 'require', '请选择排序'],

		// ['order', '', '该排序已存在', 0, 'unique', 3],

		// 判断地区
		['region', '/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', '请输入由英文或中文组成的地区名'],

		// 判断品牌排序(失效的)
		// ['order', '/^[0-9]{1,16}$/', '请输入1~16的排序格式'],

		// ['discount', '/^(100|[1-9][0-9]|[0-9])$/u', '输入10的,折扣以10%形式打折'],
	];

	// 字段映射
	// protected $_map = [
	// 	// 品牌名
	// 	'pinpainame' => 'name',

	// 	// 品牌描述
	// 	'miaoshu' => 'content',

	// 	// 品牌图片
	// 	'pic' => 'logo',

	// 	// 品牌地区
	// 	'diqu' => 'region',

	// 	// 品牌状态
	// 	'or' => 'order',

	// ];

	// 处理品牌列表数据
	public function data() {


		$list = $this->select();
		
		$arr = [
			1 => '第一位显示',
			2 => '第二位显示',
			3 => '第三位显示',
			4 => '第四位显示',
			5 => '第五位显示',
			6 => '第六位显示',
			7 => '第七位显示',
			8 => '第八位显示',
			9 => '第九位显示',
			10 => '第十位显示',
			11 => '暂不展示品牌',
		];

		$str = [1 => '国内', 2 => '国外'];
		$state = [1 => '展示', 2 => '暂时不展示'];

		foreach ($list as $k => $v) {
			// 排序
			$list[$k]['order'] = $arr[$v['order']];

			// 产地
			$list[$k]['region'] = $str[$v['region']];
			
			// 更改状态
			$list[$k]['state'] = $state[$v['state']];

			// 加入时间
			$list[$k]['atime'] = date('Y-m-d H:i:s', $v['atime']);
			
			// 修改时间
			if ($list[$k]['xtime']) {
				$list[$k]['xtime'] = date('Y-m-d H:i:s', $v['xtime']);
			}
		}

		// 返回数据
		return $list;

	} 



}