<?php
namespace Home\Controller;
use Think\Controller;

class GoodsController extends Controller {
	public function index()
	{

		echo "<pre>";
		  echo "<hr>";
		    var_dump(1);
		  echo "<hr>";
		echo "</pre>";exit;
		
		// // 图片上传
		// $upload = new \Think\Upload();

		// // 设置根目录
		// $upload->rootPath = './Image/';

		// // 根目录里面的子目录
		// $upload->savePath = './Goods/';

		// // 取消自动使用子目录功能
		// $upload->autoSub = false;
		
		// // 执行上传
		// $zhixing = $upload->upload();
		// // 判断错误
		// if (!$zhixing) {
		// 	echo $upload->getError();
		// 	exit;
		// }

		// 成功插入数据库
		$model = M('upload');

		// 获取图片数据
		$arr = $model->select();
		
		if (IS_AJAX) {
			// 返回数据
			$this->ajaxReturn($arr);

		} 

		// 分配数据
		$this->assign('arr', $arr);

		// 渲染模板
		$this->display();

	}
	public function aa() {

		// if (IS_AJAX) {

		// 	$this->ajaxReturn(I('post.'));
		// 	// dump(I('post.'));
		// }

		// $this->display();


		$this->redis = new \Redis();

		$this->redis->connect('localhost', 6379);

		$this->redis->set('abc', 5465456);

		$xiaoShou = $this->redis->get('abc');//记录销售量
		

		$limit = 3;

		if ($xiaoShou >= $limit) {

			echo "<pre>";
			  echo "<hr>";
			    var_dump('活动结束了');
			  echo "<hr>";
			echo "</pre>";exit;
			
		}

		$redis->multi();//现在进入顺序存放，当某一个操作被打断的时候，返回空值  这个也是事务的开始
		$redis->set('abc', $xiaoShou+1);
		$bool = $redis->exec();//返回成功或者失败   0,1  这个也是事务的结尾

		if ($bool) {

			$asd = M('Ceshi')->where('id = 1')->find();

			$a['ints'] = $asd['ints'] -1;

			$hah = M('Ceshi')->where('id = 1')->save($a);
			
		}

exit;


		

	}
}

