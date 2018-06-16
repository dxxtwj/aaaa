<?php  
namespace Admin\Model;
use Think\Model;

class AdSortModel extends Model
{
	// 自动验证
	protected $_validate = [
		['sort','require','请填写广告分类名称！'], //默认情况下用正则进行验证
		['sort','','此广告分类已经存在！',0,'unique',3], // 验证sort广告分类字段是否唯一
		['describe','require','请填写广告分类说明！'], //默认情况下用正则进行验证
		['describe','','此广告分类说明已经存在,不能重复填写！',0,'unique',3], // 验证sort广告分类字段是否唯一
	];

	// 自动完成:如何传递第2个参数?
	// protected $_auto = [
	// 	['pwd', 'md5', 1, 'function'],
	// 	['addtime', 'time', 1, 'function'],
	// ];

	/**
	 *处理显示广告管理-分类管理的数据
	 */
	public function sort() 
	{
		$arr = $this->select();
		$status = ['', '显示', '隐藏'];
		foreach ($arr as $k=>$v) {
			$arr[$k]['status'] = $status[$v['status']];
			$arr[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
		}
		return $arr;
	}

	
}
?>