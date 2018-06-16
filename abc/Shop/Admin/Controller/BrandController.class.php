<?php

namespace Admin\Controller;
use Think\Controller;

class BrandController extends Controller 
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


	// 品牌列表页
	public function index() {
		
		// 查询品牌表
		$model = D('Pinpai');

		// 查询语句
		$arr = $model->field('id, name, content, logo, region, order, atime, xtime, state')->data();

		// 分配数据
		$this->assign('arr', $arr);

		// 显示模板
		$this->display();
	}

	// 添加品牌
	public function add() {

		/*
			实现图片预览效果
		 */
		// 实例化redis
		$redis = new \Redis();

		// 设置端口
		$redis->connect('127.0.0.1',6379);

		// 如果不是重定向回来的就删除文件
		$server = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		if ($_SERVER['HTTP_REFERER'] != $server) {
			
			// 删除缓存
			$re = $redis->del('image');
		}

		// 取图片路径
		$image = $redis->get('image');

		// 分配图片路径
		$this->assign('image', $image);

		/*
			查询分类表
		 */
		$type = M('Type');
		$typeAll = $type->order('concat(path, id)')->field('id, name, path')->select();


		// 查询商品表
		$model = M('Goods');
		$arr = $model->field('id, name')->select();

		if (IS_POST) {
			
			// 判断品牌名字
			$model = D('Pinpai');

			// 自动验证
			$arr = $model->create();
			
			if (!$arr) {
				$this->error($model->getError());
				exit;
			}

			// 追加图片
			$arr['logo'] = $this->get('image');
			
			// 删除缓存
			$redis->del('image');

			// 追加时间
			$arr['atime'] = time();

			// 查询排序，看下有没有重复的
			$array = array('order' => $arr['order']);
			
			// 查询排序的语句
			$sequel = $model->where($array)->field('id')->select();
			
			// 如果重复则不给创建品牌
			if ($sequel) {
				$this->error('请换一个排序');
				exit;
			}

			// 条件成立，添加
			if ($model->add($arr)) {

				$this->success('添加成功', U('Brand/index'), 1);
				exit;
			} else {

				$this->error('添加失败');
				exit;
			}
		}

		// 为添加进另一个select而需要ajax
		if (IS_AJAX) {

			// 接收id
			$array = array('id' => $_GET['id']);

			// 查询相应的商品
			$arr = $model->where($array)->find();

			// 返回数据
			$this->ajaxReturn($arr);
		}

		// 分配分类表的数据
		$this->assign('typeAll', $typeAll);

		// 分配数据 商品表
		$this->assign('arr', $arr);

		// 显示模板
		$this->display();

	}

	// 修改品牌
	public function edit() {

		$model = D('Pinpai');

		// 接收id
		$array = array('id' => $_GET['id']);

		// 查询语句
		$arr = $model->where($array)->find();

		if (IS_POST) {
			if (empty($_POST['id'])) {
				$this->error('你要修改的是那一条？');
				exit;
			}

			// 自动验证
			$yz = $model->create();			
			
			if (!$yz) {

				$this->error($model->getError());
				exit;

			}

			// 查询排序，看下有没有重复的
			$array = array('order' => $yz['order']);
			
			// 查询排序的语句
			$sequel = $model->where($array)->field('id, order')->select();
	
			// 判断是否上传图片
			if ($_FILES['logo']['size'] > 0) {
				
				// 获取路劲
				$lujing = $this->file('Pinpai');
				
				foreach ($lujing as $k => $v) {
					// 追加路劲
					$yz['logo'] = $lujing['logo']['savepath'].$lujing['logo']['savename'];
				}

			}

			// 获取修改时间
			$yz['xtime'] = time();
		
			// 执行修改
			$save = $model->save($yz);
			if ($save) {

				$this->success('修改成功', U('Brand/index'));
				exit;
			} else {
				$this->error('修改失败');
				exit;

			}
		}

		// 分配数据
		$this->assign('arr', $arr);

		// 显示模板
		$this->display();
	}

	/**
	 * 删除品牌
	 */
	public function del() {

		if (empty($_GET['id'])) {

			$this->error('你要删除的是那一条？');
			exit;
		}

		$model = M('Pinpai');

		// 接收id
		$array = array('id' => $_GET['id']);

		// 删除语句
		$del = $model->where($array)->delete();
		
		if ($del) {

			$this->success('删除成功', U('Brand/index'));
			exit;

		} else {

			$this->error('删除失败');
			exit;
		}

	}

	/**
	 * 处理图片预览
	 * 这个是添加的图片预览效果
	 */
	public function scan() {

		
		// 获取文件信息
		$file = $this->file('Pinpai');

		foreach ($file as $k => $v) {
			// 获取路径
			$data[$k]['logo'] = $v['savepath'].$v['savename'];
		}

		// 实例化redis
		$redis = new \Redis();

		// 设置端口
		$redis->connect('127.0.0.1',6379);

		// 存数据
		$redis->set('image',$data['logo']['logo']);

		// 重定向
		$this->redirect('Brand/add');
	}

}