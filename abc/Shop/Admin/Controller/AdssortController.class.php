<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;

class AdssortController extends CommonController{
	/**
	 * 显示广告管理-分类管理
	 */
	public function Sort_ads()
	{
		// 连接广告分类表
		$adSort = D('adSort');
		// 统计广告分类个数
		$adSortCount = $adSort->count('sort');

		// 连接广告表
		$ad = D('ad');
								
		// 分页类
		// 统计广告分类的个数
		$total = $adSort->count();
		$page = new Page($total, 2);
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$arr = $adSort->limit($page->firstRow, $page->listRows)->order('addtime desc')->sort();
			
		for($i=0;$i<($page->listRows);$i++){
			// 追加num字段给$arr,广告表的aid等于广告分类表的id
			if ($arr[$i]['id'] != '') {
				$arr[$i]['num'] = $ad->where(['aid'=>$arr[$i]['id']])->group('aid')->count('aid');
		 	}
		}

		$btn = $page->show();
		// if (IS_AJAX) {
		// 	$data = $arr;
		// 	$data['page'] = $btn;
		// 	$this->ajaxReturn($data);
		// }
		// 分配数据
		// 分配分类个数数据
		$this->assign('count', $adSortCount);
		$this->assign('btn', $btn);
		$this->assign('list', $arr);
		$this->display();
		exit;
	}


	/**
	 * 添加广告分类
	 */
	public function add()
	{
		if (IS_POST) {
			$adSort = D('adSort');
			$data = $adSort->create();

			if (!$data) {

				$this->error($adSort->getError());
			}

					// echo "<pre>";
					// 	var_dump($adSort->getError());
					// echo "</pre>";
					// exit;

			$data['addtime'] = time();

			if ($data) {
				if ($adSort->add($data)) {
					$this->success('添加成功', 'Sort_ads');
				} else {
					$this->error('添加失败');
				}
			} else {
				// 获取错误提示
				$this->error($adSort->getError());
			}
		} else {
			$this->display();
		}
	}
	
	/**
	 *[ajaxDel ajax删除广告分类]
     * @param int $id 要删除的数据
	 */
	public function ajaxDel($id) {
		if (IS_AJAX) {
			if (M('adSort')->delete($id)) {
				$this->success('删除成功');
			} else {
				$this->error('删除失败');
			}
		}
	}

	/**
	 * 批量删除(选中删除)
	 */
	public function checkdel(){ 
       	// 连接广告分类表 
        $adSort = M('adSort');
        $id = I('get.id');
        if (empty($id)) {
        	$this->error('请选择您要删除的广告分类');
        	exit;
        }
        // explode — 使用,分割$id 
        $id = explode(',',$id);
        // 将数组最后一个单元(,)弹出（出栈） 
        $id_pop = array_pop($id);
        				
        if(is_array($id)){
        	// implode — 将一个一维数组的值转化为字符串 
            $where = 'id in('.implode(',',$id).')';
        }else{
            $where = 'id='.$id;
        }
       
        $list=$adSort->where($where)->delete();
        if($list!==false) {
            $this->success("成功删除{$list}条！",U('Adssort/Sort_ads'));
        }else{
            $this->error('删除失败！');
        }
    }



	/**
	 * 修改广告分类状态
	 */
    public function doStatus($id) {
        $ad = D('AdSort');
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

    /**
	 * 修改广告分类
	 */
	public function edit() {

		if (IS_POST) {

			if (empty($_POST['id'])) {
				echo '请问你要修改哪条广告分类';
				exit;
			}

			$adSort = D('adSort');

			if ($adSort->where(['id'=>$_POST['id']])->save($_POST)) {

				$this->success('修改成功', 'Sort_ads');
				exit;

			} else {

				$this->error('你没有修改广告分类数据');
				exit;
			}
		} else {
			// 连接广告分类表
			$adSort = D('adSort');
			// 查询单条广告分类表的数据
			$data = $adSort->where(['id'=>$_GET['id']])->find();
			// 分类数据
			// $this->assign('adSort', $adSort);
			$this->assign('data', $data);
			$this->display();
		}
	}
}