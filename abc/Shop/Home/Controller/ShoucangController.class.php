<?php
namespace Home\Controller;
use Think\Controller;

Class ShoucangController extends Controller
{

	/**
	 * 添加收藏
	 * 只需要uid  gid
	 */
	public function add() {
		
		$model = M('Shoucang');
		$last = $model->add($_GET);

		if ($last) {

			$this->ajaxReturn(1);
			exit;
		} else {
			$this->ajaxReturn(2);
			exit;
		}

	}

	/**
	 * 取消收藏
	 * ajax的
	 */
	public function del() {

		$model = M('Shoucang');

		$shouCangTj['uid'] = $_GET['uid'];
		$shouCangTj['gid'] = $_GET['gid'];
		$del = $model->where($shouCangTj)->delete();

		
		if ($del) {

			$this->ajaxReturn(1);
			exit;
		} else {
			$this->ajaxReturn(2);
			exit;
		}
	}

	/**
	 * 这个是在个人中心那里取消收藏的
	 */
	public function delete() {
		$model = M('Shoucang');

		$shouCangTj['id'] = $_GET['id'];
		$del = $model->where($shouCangTj)->delete();

		if ($del) {

			$this->success('取消收藏成功', U('Usercenter/collection'));
			exit;

		} else {
			$this->error('取消收藏失败');
			exit;

		}
	}

}