<?php
namespace Admin\Model;
use Think\Model;

class TypeModel extends Model {

	protected $_validate = [
		// 判断分类名输入了没 
		['name', 'require', '请输入你要添加的分类名字!'],
	];


	/**
	 * 判断顶级分类名输入是否存在特殊字符
	 * @param  [表单传过来的分类名] $a 
	 * @return [0]    [输入有误，返回空]
	 * @return [$a]    [处理过后的数据，返回去再进行添加的操作]
	 */
	public function verification($a) {

		// 追加只有顶级才会有的属性
		$a['pid'] = 0;
		$a['path'] = '0,';
		$a['atime'] = time();

		// 判断是否有特殊字符
		if (!preg_match('/^[\w\x{4e00}-\x{9fa5}]{1,18}$/u', $a['name'])) {
			return "0";
			exit;
		}
		// 没问题就返回
		return $a;
	}

	/**
	 * 判断添加和修改子分类的方法
	 * @param  [array] $a [表单传过来的数据]
	 * @return [0]    [判断不通过的]
	 * @return [$a]    [判断通过则返回数据]
	 */
	public function taypeass2verification($a) {
		
		// 判断是否为空
		if (empty($a['name'])) {
			return "0";
			exit;
		}

		// 判断是否有特殊字符
			if (!preg_match('/^[\w\x{4e00}-\x{9fa5}]{1,18}$/u', $a['name'])) {
			return "0";
			exit;
		}
		// 没问题就返回
		return $a;
	}

	/**
	 * 格式化添加时间
	 */
	public function data() {

		$arr = $this->select();

		foreach ($arr as $k=>$v) {
			// 格式化添加时间
			$arr[$k]['atime'] = date('Y-m-d H:i:s', $v['atime']);
			
			if($arr[$k]['xtime']) {
				
				// 格式化修改时间
				$arr[$k]['xtime'] = date('Y-m-d H:i:s', $v['xtime']);
			}
		}

		return $arr;

	}


	/**
	 * 处理分类表的二级分类和三级分类
	 * @return [array] [$list] [返回的是二级和三级的数据]
	 */
	public function typeJb() {

		$type = $this->select();


		foreach ($type as $k => $v) {

			$erjiPid[] = $v['id'];
		}

		$joinErjiPid =  join(',', $erjiPid);

		// 查询三级分类
		$array = array('pid' => ['in', $joinErjiPid]);

		// 查询语句
		$list = $this->where($array)->select();

		// 追加二级的数据		
		// $list['erji'] = $type;

		// 返回数据
		return $list;
		
	}
}
