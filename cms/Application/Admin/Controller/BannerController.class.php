<?php
namespace Admin\Controller;
use Think\Controller;

class BannerController extends CommonController {

	// 列表
	public function index(){
		$banner = M('banner_rec');

        $page_num = 10;
        $where['BR_Type'] = 1;
        $count = $banner->where($where)->count();
        //分页
        $page = new \Think\Page($count,$page_num);
        $page->setConfig('first','首页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $show = $page->show();
        $limit = $page->firstRow.','.$page->listRows;

		$res = $banner->order('BR_Sort desc')->where($where)->limit($limit)->select();
		foreach($res as $key=>$val)
		{
			$res[$key]['BR_AddTime'] = date('Y-m-d H:i:s',$val['BR_AddTime']);
		}
		
		$this->assign('res', $res);
		$this->assign('show', $show);
        $this->assign('count', $count);
        // dump($count);
		$this->display();
	}


    // 添加
	public function add(){
		$br = M('banner_rec');
		// dump($_POST);
		// exit;
		if(IS_POST){
			// dump($_POST);
			// exit;
        	$br_id = (int)I('bid');
			if($br_id>0){
				$name = I('name');
				$url = I('url');
				$sort = I('sort');
				$show = I('isShow');
				$path = I('path');
				// 处理数据
				$map['BR_Name'] = $name;
				$map['BR_Url'] = $url;
				$map['BR_Sort'] = $sort;
				$map['BR_Is_Show'] = $show;
				$map['BR_UpdateTime'] = time();
				$map['BR_IMG'] = $path;
				
				$res = $br->where(array('BR_ID'=>$br_id))->save($map);
				if($res){
					$this->success('修改轮播图成功',U('Banner/index'));
				}else{
					$this->error('修改轮播图失败',U('Banner/index'));
				}
			}else{
				$name = I('name');
				$url = I('url');
				$sort = I('sort');
				$show = I('isShow');
				$path = I('path');
				// 处理数据
				$map['BR_Name'] = $name;
				$map['BR_Url'] = $url;
				$map['BR_Sort'] = $sort;
				$map['BR_Is_Show'] = $show;
				$map['BR_AddTime'] = time();
				$map['BR_IMG'] = $path;
        		$map['BR_Type'] = 1;
				
				$res = $br->add($map);
				if($res){
					$this->success('添加轮播图成功',U('Banner/index'));
				}else{
					$this->error('添加轮播图失败',U('Banner/index'));
				}
			}
				
    	}
	}
	//禁用、启用
    public function setBannerShow(){
        $bid = I('bid');
        if((int)$bid>0){
            $br = M('banner_rec');
            $isShow = $br->where(array('BR_ID' => $bid))->getField('BR_Is_Show');
            if((int)$isShow === 0){
                //不显示改为显示
                $save['BR_Is_Show'] = 1;
            }elseif((int)$isShow === 1){
                //不显示改为显示
                $save['BR_Is_Show'] = 0;
            }

            $where['BR_ID'] = $bid;
            $info = $br->where($where)->save($save);

            if($info){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(0);
            }
        }else{
            $this->ajaxReturn(-1);
        }
    }
    

   	// 删除
	public function del(){
        $bids = I('bid');
    	if($bids!=null){
            $br = M('banner_rec');
            $ids = trim($bids,',');
            if($ids){
                $where['BR_ID'] = array('in',$ids);
                $info = $br->where($where)->delete();
            }
            if($info){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(0);
            }
        }else{
            $this->ajaxReturn(-1);
        }
	}

    
	public function setSort(){
        $bid = (int)I('bid');
        $sort = (int)I('sort');
        if($bid>0){
            $br = M('banner_rec');
            $map['BR_Sort'] = $sort;
            $info = $br->where(array('BR_ID'=>$bid))->save($map);
            if($info){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(0);
            }
        }else{
            $this->ajaxReturn(-1);
        }
    }
    
    // 广告列表
	public function advertising(){
		$banner = M('banner_rec');

        $page_num = 10;
        $where['BR_Type'] = 2;
        $count = $banner->where($where)->count();
        //分页
        $page = new \Think\Page($count,$page_num);
        $page->setConfig('first','首页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $show = $page->show();
        $limit = $page->firstRow.','.$page->listRows;

		$res = $banner->order('BR_Sort desc')->where($where)->limit($limit)->select();
		foreach($res as $key=>$val)
		{
			$res[$key]['BR_AddTime'] = date('Y-m-d H:i:s',$val['BR_AddTime']);
		}
		
		$this->assign('res', $res);
		$this->assign('show', $show);
        $this->assign('count', $count);
        // dump($count);
		$this->display();
	}


    // 添加
	public function addAdvertising(){
		$br = M('banner_rec');
		// dump($_POST);
		// exit;
		if(IS_POST){
        	$br_id = (int)I('bid');
			if($br_id>0){
				$name = I('name');
				$url = I('url');
				$sort = I('sort');
				$show = I('isShow');
				$path = I('path');
				// 处理数据
				$map['BR_Name'] = $name;
				$map['BR_Url'] = $url;
				$map['BR_Sort'] = $sort;
				$map['BR_Is_Show'] = $show;
				$map['BR_UpdateTime'] = time();
				$map['BR_IMG'] = $path;
				
				$res = $br->where(array('BR_ID'=>$br_id))->save($map);
				if($res){
					$this->success('修改广告成功',U('Banner/advertising'));
				}else{
					$this->error('修改广告失败',U('Banner/advertising'));
				}
			}else{
				$name = I('name');
				$url = I('url');
				$sort = I('sort');
				$show = I('isShow');
				$path = I('path');
				// 处理数据
				$map['BR_Name'] = $name;
				$map['BR_Url'] = $url;
				$map['BR_Sort'] = $sort;
				$map['BR_Is_Show'] = $show;
				$map['BR_AddTime'] = time();
				$map['BR_IMG'] = $path;
        		$map['BR_Type'] = 2;
				
				$res = $br->add($map);
				if($res){
					$this->success('添加广告成功',U('Banner/advertising'));
				}else{
					$this->error('添加广告失败',U('Banner/advertising'));
				}
			}
				
    	}
	}




	//PC端
	// 列表
	public function PCindex(){
		$banner = M('banner_rec');

        $page_num = 10;
        $where['BR_Type'] = 3;
        $count = $banner->where($where)->count();
        //分页
        $page = new \Think\Page($count,$page_num);
        $page->setConfig('first','首页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $show = $page->show();
        $limit = $page->firstRow.','.$page->listRows;

		$res = $banner->order('BR_Sort desc')->where($where)->limit($limit)->select();
		foreach($res as $key=>$val)
		{
			$res[$key]['BR_AddTime'] = date('Y-m-d H:i:s',$val['BR_AddTime']);
		}
		
		$this->assign('res', $res);
		$this->assign('show', $show);
        $this->assign('count', $count);
        // dump($count);
		$this->display();
	}


    // 添加
	public function PCadd(){
		$br = M('banner_rec');
		// dump($_POST);
		// exit;
		if(IS_POST){
			// dump($_POST);
			// exit;
        	$br_id = (int)I('bid');
			if($br_id>0){
				$name = I('name');
				$url = I('url');
				$sort = I('sort');
				$show = I('isShow');
				$path = I('path');
				// 处理数据
				$map['BR_Name'] = $name;
				$map['BR_Url'] = $url;
				$map['BR_Sort'] = $sort;
				$map['BR_Is_Show'] = $show;
				$map['BR_UpdateTime'] = time();
				$map['BR_IMG'] = $path;
				
				$res = $br->where(array('BR_ID'=>$br_id))->save($map);
				if($res){
					$this->success('修改轮播图成功',U('Banner/PCindex'));
				}else{
					$this->error('修改轮播图失败',U('Banner/PCindex'));
				}
			}else{
				$name = I('name');
				$url = I('url');
				$sort = I('sort');
				$show = I('isShow');
				$path = I('path');
				// 处理数据
				$map['BR_Name'] = $name;
				$map['BR_Url'] = $url;
				$map['BR_Sort'] = $sort;
				$map['BR_Is_Show'] = $show;
				$map['BR_AddTime'] = time();
				$map['BR_IMG'] = $path;
        		$map['BR_Type'] = 3;
				
				$res = $br->add($map);
				if($res){
					$this->success('添加轮播图成功',U('Banner/PCindex'));
				}else{
					$this->error('添加轮播图失败',U('Banner/PCindex'));
				}
			}
				
    	}
	}
}