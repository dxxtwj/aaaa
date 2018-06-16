<?php  
namespace Admin\Model;
use Think\Model;

class AdModel extends Model
{
	// 自动验证
	// protected $_validate = [
	// 	// require表示必须输入
	// 	['length', 'require', '请输入长度'],
	// 	['width', 'require', '请输入宽度'],
	// ];

	// 自动完成:如何传递第2个参数?
	// protected $_auto = [
	// 	['pwd', 'md5', 1, 'function'],
	// 	['addtime', 'time', 1, 'function'],
	// ];

	/**
	 *处理显示广告管理-广告管理的数据
	 */
	public function getData() 
	{	
		$arr = $this->select();
		// $sort = ['首页大幻灯片', '单广告图', '秒杀'];
		$status = ['', '显示', '隐藏'];
		
		// 在返回之间处理数据
		foreach ($arr as $k=>$v) {
			$arr[$k]['status'] = $status[$v['status']];
			$arr[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
		}
		return $arr;
	}

}
?>