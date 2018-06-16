<?php
namespace Admin\Controller;
use Think\Controller;

class TuiguangController extends Controller 
{	

	 /**
     * 执行文件上传操作
     * $a 为选择的目录
     * @return [error] [文件上传失败提示]
     * @return [$lujing] [文件上传成功返回路径]
     */
    public function file($a) {
        // 实例化
        $up = new \Think\Upload();
        
        // 设置大小
        $up->maxSize = 3145728;

        // 图片类型
        $up->exts = array('jpg', 'gif', 'png', 'jpeg');

        // 设置根目录
        $up->rootPath = './Public/';

        // 子目录
        $up->savePath = './Images/'.$a.'/';

        // 设置不自动创建目录时间
        $up->autoSub = false;

        // 执行上传
        $info = $up->upload();

        if (!$info) {
            // 获取报错信息
            return $this->error($up->getError());
            exit;
        } 

        // 获取数据 
        return $info;
    }

	/**
	 * 商品推广列表
	 */
	public function index() {

		$model = M('Tuijiangoods');
		$tuiGuang = $model->field('id, name, money, sellnum, dtime, image, state, gid')->select();
		
		$this->assign('tuiGuang', $tuiGuang);
		$this->display();

	}
	/**
	 * 商品推广表单
	 */
	public function add() {

		$model = M('Goods');

		// 等于2 也就是未推广的商品才查询出来
		$str['tuiguang'] = ['eq', '2'];
		$goodsList = $model->where($str)->field('id, name, money')->select();

		$this->assign('goodsList', $goodsList);

		$this->display();

	}

	/**
	 * 处理添加商品推广
	 */
	public function addchuli(){
		
		// 推荐表获取gid 和name 
		$arr = explode(',', $_POST['name']);
		$_POST['name'] = $arr[0];
		$_POST['gid'] = $arr[1];
		
		if (empty($_POST['gid'])) {

			$this->error('您要为哪一个商品添加促销？');
			exit;
		}

		// 判断价格输入合法是否
		$match = preg_match('/^\d{1,3}$/', $_POST['money']);
		
		if (empty($match)) {

			$this->error('价格输入不合法');
			exit;
		}

		// 执行文件上传
		if ($_FILES['logo']['size'] > 0) {

			$file = $this->file('TuiGuang');
			foreach($file as $k => $v) {

				$_POST['image'] = $v['savepath'].$v['savename'];
			}
		}

		// 添加数据
		$goods = M('Goods');
		$_POST['sellnum'] = $goods->where(['id' => $_POST['gid']])->field('sellnum')->find();
		$_POST['atime'] = time();

		// 1： 在推广   2： 未推广
		$_POST['state'] = 1;
		$model = M('Tuijiangoods');
		$add = $model->add($_POST);

		if (empty($add)) {
			$this->error('添加失败');
			exit;

		} else {
			// 同时修改商品表价格
			$dataGoods['id'] = $_POST['gid'];
			$dataGoods['money'] = $_POST['money'];
			$dataGoods['tuiguang'] = 1;
			$goodMoney = $goods->save($dataGoods);
			if ($goodMoney) {

				$this->success('添加推广成功', U('Tuiguang/index'));
				exit;
			} else {

				$this->error('添加推广失败');
				exit;
			}
		}
	}

	/**
	 * 取消推广
	 */
	public function del() {
	
		$tuiguang = M('Tuijiangoods');
		$goods = M('Goods');

		// 开启事务
		$tuiguang->startTrans();
		$bool = true;

		// 删除推广表商品
		$delete = $tuiguang->where(['gid' => $_GET['gid']])->delete();

		// 更改商品表状态
		$goodsState = $goods->where(['id' => $_GET['gid']])->save(['tuiguang' => '2']);
		
		if (!$delete) {
			// 删除不成功
			$bool = false;
		} 

		if (!$goodsState) {
			$bool = false;
		}

		if ($bool) {
			// 提交事务
			$tuiguang->commit();
			$this->success('取消成功', U('Tuiguang/index'));
			exit;
		} else {
			// 回滚事务
			$tuiguang->rollback();
			$this->error('取消失败');
			exit;
		}
	}
}

