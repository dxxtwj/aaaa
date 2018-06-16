<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;

class AdsController extends CommonController{
	/**
	 * 显示广告管理-广告管理
	 * @return [type] [description]
	 */
	public function Ads_list() {
		// 连接广告表
		$ad = D('Ad');
		// 统计广告的个数
		$adCount = $ad->count('id');

		// 连接广告分类表
		$adSort = D('adSort');
		// 查询广告分类表的所有id,分类名
		$sort = $adSort->field('id,sort')->select();
		// 拿出sort作为值, id作为键
		$res = array_column($sort, 'sort', 'id');
		
		// 分页类
		$total = $ad->count();

		$page = new Page($total, 3);
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$arr = $ad->limit($page->firstRow, $page->listRows)->order('addtime desc')->getData();
		
		$btn = $page->show();
		// if (IS_AJAX) {
		// 	$data = $arr;
		// 	$data['page'] = $btn;
		// 	$this->ajaxReturn($data);
		// }
		// $btn = 123;
		// 分配数据
		$this->assign('res', $res);
		$this->assign('adCount', $adCount);
		$this->assign('sort', $sort);
		$this->assign('btn', $btn);
		$this->assign('list', $arr);
		$this->display();
		exit;
	}

	/**
	 * 添加广告
	 */
	public function add() {
		if (IS_POST) {
			$ad = D('ad');
			$data = $ad->create($_POST);
			$data['addtime'] = time();

			// if ($data) {
			// if ($ad->add($data)) {
			// 	$this->success('添加成功', 'Ads_list');
			// } else {
			// 	$this->error('添加失败');
			// }
			// } else {
			// 	// 获取错误提示
			// 	$this->error($ad->getError());
			// }

			$upload = new \Think\Upload();
			$upload->maxSize	=	3145728;
			$upload->exts 		=	array('jpg', 'gif', 'png', 'jpeg');
			$upload->rootPath	=	'./Public/';//设置根目录
			$upload->savePath 	=	'./Images/Ads/';//存到子目录
			$upload->autoSub = false;
			$info = $upload->upload();

			if(!$info) {
				$this->error($upload->getError());
			} else {
				$data['pic'] = $info['pic']['savepath'].$info['pic']['savename'];
			
			 	$model = M('Ad');
				$data = $model->add($data); 
				$this->success('添加成功', 'Ads_list');
			}

		} else {
			$this->display();
		}
	}

	/**
	 * 修改广告
	 */
	public function edit() {
		if (IS_POST) {
			// 实例化
			$model = M('Ad');

			// 判断id是否为空
			if (empty($_POST['id'])) {

				$this->error('你要修改那一条？');
				exit;
			}

			// 判断是否选择了上传文件
			if ($_FILES['pic']['size'] > 0) {

				// 实例化文件上传类
				$upload = new \Think\Upload();

				// 文件最大的大小
				$upload->maxSize	=	3145728;

				// 类型
				$upload->exts 		=	array('jpg', 'gif', 'png', 'jpeg');

				//设置根目录
				$upload->rootPath	=	'./Public/';

				//存到子目录
				$upload->savePath 	=	'./Images/Ads/';

				// 这个是不自动创建时间目录
				$upload->autoSub = false;

			
				// 成功则返回数据，失败则返回false
				$info = $upload->upload();

				if ($info) {

					// 取得路径
					$_POST['pic'] = $info['pic']['savepath'].$info['pic']['savename'];

				} else {

					// 上传图片失败提示错误信息
					$this->error($model->getError());
				}
			}		

			// 修改语句
			if ($model->where(['id'=>$_POST['id']])->save($_POST)) {
				// 成功区域
				$this->success('修改成功', U('Ads/Ads_list'));
				exit;
			} else {
				// 不传参数默认跳转原来地方（表单）
				$this->error('你没有修改广告数据');
				exit;
			}


		}
		// 连接广告表
		$ad = D('ad');
		// 查询单条广告表的数据
		$data = $ad->where(['id'=>$_GET['id']])->find();
		// 连接广告分类表
		$adSort = D('adSort');
		// 查询广告分类表的所有id,分类名
		$sort = $adSort->field('id,sort')->select();
		// 分配数据
		$this->assign('data', $data);
		$this->assign('sort', $sort);
		$this->display();
	}


	/**
	 *[ajaxDel ajax删除广告]
     * @param int $id 要删除的数据
	 */
	public function ajaxDel($id) {
		if (IS_AJAX) {
			if (M('ad')->delete($id)) {
				$this->success('删除成功');
			} else {
				$this->error('删除失败');
			}
		}
	}

	/**
	 * 批量删除
	 */
	public function checkdel() {
		// 连接广告表
		$ad = M('ad');
		$id = I('get.id');
		if (empty($id)) {
			$this->error('请选择您要删除的广告');
			exit;
		}
		// explode -使用,分割$id
		$id = explode(',',$id);
		// 将数组最后一个单元(,)弹出 （出栈）
		$id_pop = array_pop($id);

		// 判断id是数组还是一个数值
		if(is_array($id)) {
			// implode -将一个一维数组的值转换为字符串
			$where = 'id in('.implode(',',$id).')';
		} else {
			$where = 'id='.$id;
		}

		$list = $ad->where($where)->delete();
		if($list!=false) {
			$this->success("成功删除{$list}条!",U('Ads/Ads_list'));
		} else {
			$this->error('删除失败!');
		}
	}

	/**
	 * 修改广告状态
	 */
    public function doStatus($id) {
        $ad = D('Ad');
        $crr = $ad->where(["id"=>$id])->select();
      
        $drr = $crr[0]['id'];
        $crr = $crr[0]['status'];

        if ($crr == '1') {
           $arr['status'] = '2'; 
        } else {
            $arr['status'] = '1';
        }

        $brr = $ad->where(["id"=>$id])->save($arr);

        if ($brr == '1') {
        	$this->ajaxReturn($drr);
       } else {
            $this->ajaxReturn('-1');
       }
    }
	
}