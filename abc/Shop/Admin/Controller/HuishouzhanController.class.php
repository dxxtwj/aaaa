<?php

namespace Admin\Controller;
use Think\Controller;

class HuishouzhanController extends Controller {

	/**
	 * 回收站列表
	 */
	public function index() {
		
		$model = M('Goods');

		// 显示回收站商品
		$array['del'] = ['eq', 2]; 
		$goodsListDle = $model->where($array)->select();
	
		// 分配数据
		$this->assign('goodsListDle', $goodsListDle);

		$this->display();
	}

	/**
	 * 彻底删除商品
	 */
	public function del() {

		if (IS_AJAX) {

			if (empty($_GET['id'])) {
				$this->ajaxReturn(2);
				exit;
			}

			$model = M('Goods');

			// 删除相应的商品
			$array = ['id' => $_GET['id']];
			$delete = $model->where($array)->delete();
			
			if (!empty($delete)) {

				$this->ajaxReturn(1);
				exit;

			} else {

				$this->ajaxReturn(2);
				exit;

			}

		}

	}


	/**
	 * 还原商品
	 * @return [type] [description]
	 */
	public function huanyuan() {


		if (IS_GET) {
			if (empty($_GET['id'])) {

				$this->error('你要还原的是那一条数据？');
				exit;
			}

			$model = M('goods');
			
			$_GET['del'] = 1;

			$save = $model->save($_GET);

			if (!empty($save)) {

				$this->success('还原成功', U('Huishouzhan/index'));
				exit;
			} else {
				$this->error('还原失败');
				exit;
			}
			
		}

	}
}