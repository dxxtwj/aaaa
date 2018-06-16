<?php
namespace Admin\Controller;
use Think\Controller;

class NavigationController extends CommonController {

	// 列表
	public function index(){
		$nr = M('navigation_rec');

        $page_num = 20;
        $count = $nr->count();
        //分页
        $page = new \Think\Page($count,$page_num);
        $page->setConfig('first','首页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $show = $page->show();
        $limit = $page->firstRow.','.$page->listRows;

		$res = $nr->order('NR_Sort desc')->limit($limit)->select();
		foreach($res as $key=>$val)
		{
			$res[$key]['NR_AddTime'] = date('Y-m-d H:i:s',$val['NR_AddTime']);
		}
		
		$this->assign('res', $res);
		$this->assign('show', $show);
        $this->assign('count', $count);
        // dump($count);
		$this->display();
	}


    // 添加
	public function add(){
		$nr = M('navigation_rec');
		// dump($_POST);
		// exit;
		if(IS_POST){
			// dump($_POST);
			// exit;
        	$nr_id = (int)I('nid');
			if($nr_id>0){
				$name = I('name');
				$url = I('url');
				$sort = I('sort');
				$show = I('isShow');
				$path = I('path');
				// 处理数据
				$map['NR_Name'] = $name;
				$map['NR_Url'] = $url;
				$map['NR_Sort'] = $sort;
				$map['NR_Is_Show'] = $show;
				$map['NR_AddTime'] = time();
				$map['NR_IMG'] = $path;
				
				$res = $nr->where(array('NR_ID'=>$nr_id))->save($map);
				if($res){
					$this->success('修改导航成功',U('navigation/index'));
				}else{
					$this->error('修改导航失败',U('navigation/index'));
				}
			}else{
				$name = I('name');
				$url = I('url');
				$sort = I('sort');
				$show = I('isShow');
				$path = I('path');
				// 处理数据
				$map['NR_Name'] = $name;
				$map['NR_Url'] = $url;
				$map['NR_Sort'] = $sort;
				$map['NR_Is_Show'] = $show;
				$map['NR_AddTime'] = time();
				$map['NR_IMG'] = $path;
				
				$res = $nr->add($map);
				if($res){
					$this->success('添加导航成功',U('navigation/index'));
				}else{
					$this->error('添加导航失败',U('navigation/index'));
				}
				
			}
				
    	}
	}
	//禁用、启用
    public function setNavigationShow(){
        $nid = I('nid');
        if((int)$nid>0){
            $nr = M('navigation_rec');
            $isShow = $nr->where(array('NR_ID' => $nid))->getField('NR_Is_Show');
            if((int)$isShow === 0){
                //不显示改为显示
                $save['NR_Is_Show'] = 1;
            }elseif((int)$isShow === 1){
                //不显示改为显示
                $save['NR_Is_Show'] = 0;
            }

            $where['NR_ID'] = $nid;
            $info = $nr->where($where)->save($save);

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
        $nids = I('nid');
    	if($nids!=null){
            $nr = M('navigation_rec');
            $ids = trim($nids,',');
            if($ids){
                $where['NR_ID'] = array('in',$ids);
                $info = $nr->where($where)->delete();
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
        $nid = (int)I('nid');
        $sort = (int)I('sort');
        if($nid>0){
            $nr = M('navigation_rec');
            $map['NR_Sort'] = $sort;
            $info = $nr->where(array('NR_ID'=>$nid))->save($map);
            if($info){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(0);
            }
        }else{
            $this->ajaxReturn(-1);
        }
    }
    
    
}