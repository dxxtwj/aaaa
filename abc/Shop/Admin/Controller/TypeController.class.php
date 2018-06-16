<?php
namespace Admin\Controller;
use Think\Controller;

class TypeController extends Controller
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
	 * 添加顶级分类
	 * verification 为model层的一个判断方法
	 */
	public function typeadd() {

		// 顶级分类   post传过来的
		if (IS_POST) {

			// 添加到数据库
			$model = D('Type');

			// 自动验证(判断分类名是否为空)
			$bool = $model->create();
		
			if (!$bool) {
				$this->error($model->getError());
				exit;
			}

			// 处理添加条件
			$typeadd = $model->verification($_POST);

			if (!$typeadd) {

				$this->error('请检查分类名存不存在特殊字符,或为空');
				exit;
				
			}
			
			// 执行添加
			if ($model->add($typeadd)) {

				$this->success('添加顶级分类成功');
				exit;
			} else {

				$this->error('添加顶级分类失败');
				exit;

			}

		}
		// 渲染模板
		$this->display();
	}

	/**
	 * 商品分类列表
	 */
	public function shopnews() {

		// 查询分类表
		$model = D('Type');
		
		// 统计总条数
		$count = $model->count();

		// 实例化分页类
		$page = new \Think\Page($count, 15);

		// 实例化分页按钮
		$show = $page->show();

		// 查询语句
		$arr = $model->limit($page->firstRow.','.$page->listRows)->field('id, pid, path, name, atime, xtime')->order('concat(path, id)')->data();
		
		// 分配数据
		$this->assign('arr', $arr);

		// 分配分页按钮
		$this->assign('show', $show);

		// 显示模板
		$this->display();
	}

	/**
	 * 显示添加子分类模板，和处理添加子分类
	 * taypeass2verification 是model层里面的判断方法
	 */
	public function typeadd2() {

		if (IS_POST) {
			
			$model = D('Type');

			// 执行判断的方法
			$str = $model->taypeass2verification($_POST);

			if (!$str) {
				$this->error('请检查分类名存不存在特殊字符');
				exit;
			}

			// 添加时间
			$str['atime'] = time();

			// 如果判断通过则添加
			$bool = $model->add($str);
			if ($bool) {
				$this->success('添加成功', U('Type/shopnews'));
				exit;
			}
		}
		// 分配父类id
		$this->assign('get', $_GET);

		// 显示模板
		$this->display();
	}


	/**
	 * 修改子分类表单和处理修改
	 */
	public function typeedit() {
		
		if (empty($_GET['id'])) {
			$this->error('请传ID');
			exit;
		}

		$model = M('Type');
		
		// 查出该id所属的分类
		$type = $model->field('id, name, content, logo')->where($_GET)->find();
	
		// 分配数据
		$this->assign('type', $type);
		
		if (IS_POST) {
			// 判断
			$model = D('Type');

			// 判断结果 成功返回数据  失败返回0
			$type = $model->taypeass2verification($_POST);
			
			if (!$type) {
				$this->error('输入有特殊字符');
				exit;
			}
			// 获取修改时间
			$type['xtime'] = time();

			// 修改
			$edit = $model->save($type);

			if ($edit) {
				$this->success('修改成功', U('Type/typeadd'));
				exit;
			} else {
				$this->error('修改失败');
				exit;
			}
		}
		// 修改模板
		$this->display();
	}

	// 删除商品分类
	public function delType() {

		if (empty($_GET['id'])) {
			$this->error('请传ID');
			exit;
		}


		// 实例化
		$model = M('Type');

		// 接收参数
		$array = array('pid' => $_GET['id']);

		// 查询要删除的分类有没有子类
		$son = $model->where($array)->field('pid')->select();
		
		if (!empty($son)) {
			$this->error('请先删除此分类下的子类');
			exit;
		}
	
		$goods = M('Goods')->where(['cid' => $_GET['id']])->field('id')->find();

		if ($goods) {
			$this->error('小亲亲， 该分类下面还有商品哦~');
			exit;
		}


		// 删除
		$del = $model->where($_GET)->delete();

		if ($del) {

			$this->success('删除成功', U('Type/typeadd'));
			exit;
		} else {

			$this->error('删除失败');
			exit;
		}
		
	}

}

