<?php

namespace Admin\Controller;
use Think\Controller;

class ShopController extends CommonController 
{
	
	/**
	 * 显示商品分类导航和商品
	 */
	public function index() {
	
		/*
			查询商品数据表
		 */
		$model = D('Goods');

		// 得到总数
		$counts = $model->count();

		// 实例化分页
		$pages = new \Think\Page($counts, 10);

		// 实例化分页按钮
		$shows = $pages->show();
	
		// 不等于2 
		$array['del'] = ['neq', 2];

		// 查询语句，查询所有的商品
		$list = $model->field('id, image0, name, content, atime, num, money, state')->where($array)->order('state asc')->limit($pages->firstRow.','.$pages->listRows)->data();

		// 分配数据
		$this->assign('list', $list);

		// 分配分页按钮数据
		$this->assign('shows', $shows);

		// 渲染模板
		$this->display();
	}

	/**
	 * 查询出二级分类
	 * 这个是index产品类型列表的
	 * @return [json] [返回二级分类的数据]
	 */
	public function erji() {

		if (IS_AJAX) {
			
			
			// 通过顶级分类id查询出分类
			$model = D('Type');

			// 获取二级分类PID
			$array = array('pid' => $_POST['id']);

			// 查询语句（二级、三级的分类）
			$list = $model->field('name, id')->where($array)->typeJb();

			// 返回二级分类数据
			$this->ajaxReturn($list);
			exit;
		}
	}

	/**
	 * 查看商品详情
	 */
	public function look() {

		$goods = M('Goods');
		$goodsName = $goods->where(['id' => $_GET['id']])->field('name, id')->find();

		// 分类商品名,和ID
		$this->assign('goodsName', $goodsName);

		// 实例化商品表
		$model = M('Detailed');

		// 接收参数
		$array = array('gid' => $_GET['id']);

		// 查询商品详情
		$list = $model->where($array)->select();

		// 分配商品数据
		$this->assign('list', $list);

		// 显示模板
		$this->display();

	}

	/**
	 * 这是开启商品销售的方法
	 * 2为开始销售状态
	 */
	public function xiaoshou() {

		$goods = M('Goods');
		$_GET['state'] = 2;
		$goodsState = $goods->save($_GET);

		if ($goodsState) {

			$this->success('已经为您更改为销售状态', U('Shop/index'));
			exit;
		} else {

			$this->error('真是不好意思，更改销售模状态失败,检查是否已经在售了', 3);
			exit;
		}

	}

	/**
	 * 下架商品的方法
	 * 3为下架的状态
	 */
	public function xiajia() {

		// 判断商品是否在促销
		$tuiGuang = M('Tuijiangoods');
		$tuiGuangList = $tuiGuang->where(['gid' => $_GET['id']])->field('id')->find();
		if ($tuiGuangList) {
			$this->error('该商品在火热促销中，不可以下架');
			exit;
		}
		
		$goods = M('Goods');
		$_GET['state'] = 3;
		$goodsSave = $goods->save($_GET);

		if ($goodsSave) {

			$this->success('商品下架成功', U('Shop/index'));
			exit;
		} else {

			$this->error('商品下架失败');
			exit;
		}

	}

	/**
	 * 添加表单
	 */
	public function add() {

		// 查询分类表
		$type = M('Type');
		$arr = $type->field('id, name, pid, path')->order('concat(path, id)')->select();


		// 查询品牌表
		$pinpai = M('Pinpai');
		$list = $pinpai->field('id, name')->select();

		// 分配数据（分类表）
		$this->assign('arr', $arr);

		// 分配数据（品牌表）
		$this->assign('list', $list);

		// 分配数据（属性表）
		$this->assign('shuxing', $shuxing);

		// 模板渲染
		$this->display();
	}

	/**
	 * 处理添加商品
	 * 内部用了事务管理，$bool为事务管理的一个判断
	 */
	public function chuliadd() {
	
		// 实例化商品表
		$model = D('Goods');
		
		// 自动验证
		$arr = $model->create();

		if (!$arr) {

			$this->error($model->getError());
			exit;
		}

		// 开启事务
		$model->startTrans();
		$bool = true;
	
		/*
			goods表的处理
		 */
		$goodsList['name'] = $_POST['name'];
		$goodsList['keyman'] = $_POST['keyman'];
		$goodsList['ymoney'] = $_POST['ymoney'];
		$goodsList['money'] = $_POST['money'];
		$goodsList['cid'] = $_POST['cid'];
		$goodsList['pid'] = $_POST['pid'];
		$goodsList['chandi'] = $_POST['chandi'];
		$goodsList['material'] = $_POST['material'];
		$goodsList['norm'] = $_POST['norm'];
		$goodsList['weight'] = $_POST['weight'];
		$goodsList['atime'] = time();

		// 执行文件上传 
		$file = $this->file('Goods');

		// 获取路径
		foreach ($file as $k => $v) {

			// 路径
			 $data[$k]['image'] = $v['savepath'].$v['savename'];
		}
	
	
		if (count($data) > 5) {
			$this->error('商品图片不要超过五张');
			exit;
		}


		// 追加数据进相应的数据库字段
		for ($i = 0; $i < count($data); $i++) {
			$goodsList['image'.$i] = $data[$i]['image'];
		}
		
		// 添加 goods 表
		$lastInertId = $model->add($goodsList);
		
		if (!$lastInertId) {

			$bool = false;
		}

		if (!empty($lastInertId)) {

			// 导入讯搜文件
			vendor('xunsearch.php.lib.XS');
			
			// 创建索引
			$suoyin['id'] = $lastInertId;
			$suoyin['name'] = $goodsList['name'];
			
			// 实例化讯搜
			$xunsearch = new \XS('goods');

			// 获取管理索引的对象
			$index = $xunsearch->index;

			// 创建对象
			$doc = new \XSDocument($suoyin);
			
			if ($doc) {

				// 添加索引
				$index->update($doc);
				// 索引同步
				$index->flushIndex();
			}

		}

		// 判断事务
		if ($bool) {

			// 提交事务
			$model->commit();
			$this->success('添加成功', U('Shop/index'));
			exit;
		} else {
	
			// 回滚事务
			$model->rollback();
			$this->error('添加失败');
			exit;
		}
	}

	/**
	 * 添加详情图表单
	 */
	public function xiangqing() {
		
		// 分配商品id
		$this->assign('gid', $_GET['id']);

		$this->display();
	}

	/**
	 * 处理详情图片添加
	 */
	public function chulixiangqing() {
	
		if (empty($_GET['id'])) {

			$this->error('您要为哪一件商品添加详情图片？');
			exit;
		}


		// 文件上传
		if ($_FILES['logo']['size'] > 0) {

			$file = $this->file('GoodsXiangQing');
			
		}


		// 获取路径
		foreach ($file as $k => $v) {

			// 路径
			 $data[$k]['image'] = $v['savepath'].$v['savename'];//这里以获取在本地的保存路径为例
		}
	
		if (count($data) > 20) {
			$this->error('商品图片不要超过20张');
			exit;
		}

		// 获取下标
		for ($i = 1; $i < count($data); $i++) {
		
			$xiangQing['image'.$i] = $data[$i]['image'];
		}
		
		// 追加gid
		$xiangQing['gid'] = $_GET['id'];

		// 添加 详情表 表
		$model = M('GoodsImages');
		
		$lastInertId = $model->add($xiangQing);

		if ($lastInertId) {

			$this->success('成功上传详情图片', U('Shop/index'), 5);
			exit;
		} else {
			
			$this->error('上传详情图片失败');
			exit;
		}
	} 

	/**
	 * 修改商品
	 */
	public function editgoods() {
		/*
			处理修改数据
		 */
		if (IS_POST) {
			// 处理修改的ID
			if (empty($_POST['id'])) {

				$this->error('修改那一条!?');
				exit;
			}

			// 更新讯搜
			// 导入讯搜文件
			vendor('xunsearch.php.lib.XS');

			// 实例化讯搜
			$xunsearch = new \XS('Goods');

			// 获取管理索引的对象
			$index = $xunsearch->index;

			// 删除索引
			$del = $index->del($_POST['id']);
			
			// 更新索引
			$index = $xunsearch->index;
			$suoyin['id'] = $_POST['id'];
			$suoyin['name'] = $_POST['name'];
			$suoyin['keyman'] = $_POST['keyman'];

			// 创建对象
			$doc = new \XSDocument($suoyin);
			
			if ($doc) {

				// 添加索引
				$index->update($doc);

				// 索引同步
				$index->flushIndex();
				
			}

			$goods = M('Goods');

			$arr = $_POST;
			// 通过验证上传图片
			if ($_FILES['pic']['size'][0] > 0) {

				$files = $this->file('Goods');

				foreach ($files as $k => $v) {
					// 取路径
					$data[$k]['image'] = $v['savepath'].$v['savename'];
				}	


				// 判断图片数量
				if (count($data) > 5) {
					$this->error('图片最多不可以超过五张');
					exit;
				}

				for ($i = 0; $i < count($data); $i++) {

					$arr['image'.$i] = $data[$i]['image'];
				}
			}

			// 接收参数
			$array = array('id' => $_POST['id']);

			// 执行修改
			$bool = $goods->where($array)->save($arr);

			if ($bool) {

				$this->success('修改成功', U('Shop/index'), 3);
				exit;
			} else {

				$this->error('修改失败，你什么都没有修到');
				exit;
			}

		}
		// 进入修改表单
		if (empty($_GET['id'])) {

			$this->error('修改那一条???');
			exit;
		}

		/*
			查询分类表
		 */
		$type = M('Type');
		$arr = $type->field('id, path, name, pid')->order('concat(path, id)')->select();

		/*
			查询商品表
		 */
		$goods = M('Goods');
		$gid = array(['id' => $_GET['id']]);
		$list = $goods->where($gid)->find();

		/*
			查询品牌表
		 */
		$pinpai = M('Pinpai');
		$pinpaiAll = $pinpai->field('id, name')->select();

	
		
		// 分配大属性表
		$this->assign('daShuXingList', $daShuXingList);
			
		// 分配小属性表
		$this->assign('xShuXingList', $xShuXingList);

		// 分配要修改的数据
		$this->assign('list', $list);

		// 分配类型表数据
		$this->assign('arr', $arr);

		// 分配品牌表数据
		$this->assign('pinpaiAll', $pinpaiAll);


		// 显示模板
		$this->display();
	}


	/**
	 * 删除商品
	 * 把商品的删除状态改为2假删除，要去回收站彻底删除
	 */
	public function del() {

		if (empty($_GET['id'])) {
			$this->error('你要删除的是那条？');
			exit;
		}

		// 查询该商品是否在促销
		$tuiGuang = M('tuijiangoods');
		$tuiGuangList = $tuiGuang->where(['gid' => $_GET['id']])->field('id')->find();
		if ($tuiGuangList) {
			$this->error('该商品正在搞促销，不能删除哦');
			exit;
		}
		

		// 更改del状态 1 为 未删除  2 为假删除
		$_GET['del'] = 2;
		$model = M('Goods');
		$del = $model->save($_GET);

		if ($del) {



			$this->success('商品已经添加进回收站，请在回收站彻底删除');
			exit;
		} else {

			$this->error('商品添加回收站失败');
			exit;
		}
	}

	/**
	 * 显示添加属性表单
	 */
	public function addshuxing() {

		$goods = M('Goods');
		$goodsList = $goods->where(['id' => $_GET['id']])->find();

		$this->assign('goodsList', $goodsList);
		$this->display();
	}

	/**
	 * 处理添加属性
	 */
	public function chuliaddshuxing() {
		
		// 添加进遍历属性表
		$attr = M('Detailed');

		$array['gid'] = ['eq', $_POST['gid']]; 
		$array['baozhuang'] = ['eq', $_POST['baozhuang']];
		$array['kouwei'] = ['eq', $_POST['kouwei']];
		$detailed = $attr->where($array)->find();
		
		if (!empty($detailed)) {

			$this->error('已经存在该组合');
			exit;
		}

		
		if ($_FILES['image']['size'] > 0) {

			$file = $this->file('GoodsShuXingTu');
		}

		if (empty($file)) {

			$this->error('没有图片上传');
			exit;
		}

		// 取路径
		foreach ($file as $v) {

			$image = $v['savepath'].$v['savename'];
		}
		
		$_POST['prices'] = $_POST['price'];
		
		// 追加图片
		$_POST['image'] = $image;
		
		$arr = $attr->add($_POST);
		
		if ($arr) {

			$this->success('添加属性组合成功', U('Shop/index'));
			exit;
		}
		
		if (empty($lastList)) {
			$this->error('添加属性失败');
			exit;
		} else {
			$this->assign('添加成功', U('Shop/addshuxing'));
			exit;
		}
	}

	/**
	 * 修改商品属性表单
	 */
	public function editshuxing() {
		$model = M('Detailed');
		
		$arr = $model->where(['id' => $_GET['id']])->find();
		

		$this->assign('arr', $arr);
		$this->display();
	}

	/**
	 * 处理修改属性
	 */
	public function chulieditshuxing() {

		$model = M('Detailed');

		$array['gid'] = ['eq', $_POST['gid']]; 
		$array['baozhuang'] = ['eq', $_POST['baozhuang']];
		$array['kouwei'] = ['eq', $_POST['kouwei']];
		$detailed = $model->where($array)->find();
		
		if (!empty($detailed)) {

			$this->error('已经存在该组合');
			exit;
		}


		if ($_FILES['image']['size'] > 0) {
			$file = $this->file('GoodsShuXingTu');
		}
		
		if (empty($file)) {

			$this->error('没有图片上传');
			exit;
		}

		// 取路径
		foreach ($file as $v) {

			$image = $v['savepath'].$v['savename'];
		}
		
		// 追加图片
		$_POST['image'] = $image;

		$bool = $model->where()->save($_POST);

		if (empty($bool)) {
			$this->error('修改属性失败');
			exit;
		} else {
			$this->assign('修改成功', U('Shop/addshuxing'));
			exit;
		}
		
	}

	/**
	 * 删除商品属性 
	 */
	public function delshuxing() {
		
		if (empty($_GET['id'])) {
			$this->error('你要删除的是那条？');
			exit;
		}

		// 查询该商品是否在促销
		$tuiGuang = M('tuijiangoods');
		$tuiGuangList = $tuiGuang->where(['gid' => $_GET['gid']])->field('id')->find();
		
		if ($tuiGuangList) {
			$this->error('该商品正在搞促销，不能删除哦');
			exit;
		}

		$model = M('Detailed');
		$del = $model->where(['id' => $_GET['id']])->delete();

		if ($del) {

			$this->success('已经删除属性');
			exit;
		} else {

			$this->error('商品删除属性失败');
			exit;
		}
	}

	/**
	 * 设置属性表单
	 */
	// public function shezhishuxing() {

	// 	// 查询大属性表
	// 	$dattr = M('Dattrbute');
	// 	$dAttrList = $dattr->where(['gid' => $_GET['id']])->getField('id, dattrbute');
	// 	$this->assign('dAttrList', $dAttrList);
		
	// 	foreach ($dAttrList as $k => $v) {
	// 		$xAttrList[$k] = $dattr->table('__XATTRBUTE__')->where(['dtypeid' => $k])->select();
	// 	}

	// 	foreach ($dAttrList as $dk => $dv) {
	// 		echo '大属性名字：'.$dv;
	// 		foreach ($xAttrList[$dk] as $xk => $xv) {
	// 			// echo '小属性名字：'.$xv['xattrbute'].',id：'.$xv['id']."<br>";
	// 			// var_dump($xk);
	// 			var_dump($xv['xattrbute']);
	// 			echo '<br>';
	// 		}
	// 	}
	// 	exit;
	// 	$this->assign('xAttrList', $xAttrList);
	// 	$this->display();
	// }
}

	