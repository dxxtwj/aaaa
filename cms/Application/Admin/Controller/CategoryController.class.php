<?php
namespace Admin\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class CategoryController extends CommonController {
	//分类列表
    public function index(){
    	$cr_info = $this->getNextSon();
    	$cr_info = $this->getSortCatetory($cr_info);
    	$res = json_encode($cr_info);
    	// dump($cr_info);
    	// dump($res);

    	$this->assign('res',$res);
        $this->display();
    }
	
    // 得到该分类的子类
    private function getNextSon($id = 0){
    	$cr = M('category_rec');
    	$where['CR_PID'] = $id;
        $where['CR_Type'] = 1;
    	$res = $cr->order('CR_Sort desc')->where($where)->select();
    	if(!$res){
    		return false;
    	}
    	
		foreach ($res as $key => $value) {
			$res[$key]['son'] = $this->getNextSon($value['CR_ID']);
		}
		return $res;
    }

    private function getSortCatetory($res){
    	foreach ($res as $key => $value) {
    		if($value['son']){
    			$son_has_sons = $this->getSortCatetory($value['son']);
    			unset($res[$key]['son']);
    			$arr = $res[$key];
    			$new_res[] = $arr;
    			unset($arr);

    			if($son_has_sons && $new_res){
    				$new_res = array_merge($new_res,$son_has_sons);
    			}

    		}else{
    			$new_res[] = $value;
    		}
    	}
    	if($new_res){
    		return $new_res;
    	}
    	return $res;
    }

    //添加、修改分类
    public function add(){
    	$cr = M('category_rec');
        
    	if(IS_POST){
    		if((int)I('cid')>0){
    			$where['CR_ID'] = I('cid');
	    		$map['CR_Name'] = I('name');
	    		$map['CR_Sort'] = I('sort');
                $map['CR_Is_Index'] = I('isIndex');
                $map['CR_IMG'] = I('path');
                $map['CR_Banner'] = I('path_banner');
                $map['CR_BannerURL'] = I('bannerURL');
                $map['CR_Is_Index_PC'] = I('isIndex_PC');
                $map['CR_Banner_PCtop'] = I('path_category_PCtop');
                $map['CR_Banner_PCleft'] = I('path_category_PCleft');
                $map['CR_BannerURL_PCtop'] = I('bannerURL_PCleft');
                $map['CR_BannerURL_PCleft'] = I('bannerURL_PCleft');
	    		$map['CR_UpdateTime'] = time();

				$info = $cr->where($where)->save($map);
				if($info){
					echo "<script>alert('修改成功');parent.location.reload();</script>";
				}else{
					$this->error("修改失败");
				}
    		}else{
    			if((int)I('pid')>0){
					$add['CR_PID'] = I('pid');
    			}else{
					$add['CR_PID'] = 0;
                    $add['CR_Banner'] = I('path_banner');
                    $add['CR_BannerURL'] = I('bannerURL');
    			}
	    		$add['CR_Name'] = I('name');
	    		$add['CR_Sort'] = I('sort');
	    		$add['CR_AddTime'] = time();
                $add['CR_Is_Index'] = I('isIndex');
                $add['CR_IMG'] = I('path');
                $add['CR_Is_Show'] = 1;
                $add['CR_Type'] = 1;


				$info = $cr->add($add);
				if($info){
					echo "<script>alert('添加成功');parent.location.reload();</script>";
				}else{
					$this->error("添加失败");
				}

    		}

    	}else{
    		if((int)I('id')>0){
    			$where['CR_ID'] = I('id');
    			$cr_info = $cr->where($where)->find();
                $cr_info['url'] = DOMAIN_NAME."/index.php/Home/Goods/hot_sale?cate=".$where['CR_ID'];
                $cr_info['PCurl'] = DOMAIN_NAME."/index.php/Home/goods/goods/cid/".$where['CR_ID'];
                // dump($cr_info);
    			$this->assign('res',$cr_info);
    		}
    		if((int)I('pid')>0){
				$pwhere['CR_ID'] = I('pid');
    			$pcr_info = $cr->where($pwhere)->find();
    			$this->assign('pres',$pcr_info);
    		}

    		$this->display('category_add');
    	}
    }


    //禁用、启用分类
    public function setCategoryShow(){
    	$cid = I('cid');
    	if((int)$cid>0){
    		$cr = M('category_rec');
    		$isShow = $cr->where(array('CR_ID' => $cid))->getField('CR_Is_Show');
    		if((int)$isShow === 0){
    			//不显示改为显示
    			$save['CR_Is_Show'] = 1;
    		}elseif((int)$isShow === 1){
    			//不显示改为显示
    			$save['CR_Is_Show'] = 0;
    		}
	    	$cr_info = $this->getNextSon($cid);
	    	$cr_info = $this->getSortCatetory($cr_info);
	    	$id_arr[] = $cid;
	    	foreach ($cr_info as $key => $value) {
	    		$id_arr[] = $value['CR_ID'];
	    	}
	    	$ids = implode(",", $id_arr);
	    	if($ids){
	    		$where['CR_ID'] = array('in',$ids);
	    		$info = $cr->where($where)->save($save);
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

    public function delCategory(){
    	$cid = I('cid');
    	if((int)$cid>0){
    		$cr = M('category_rec');
	    	$cr_info = $this->getNextSon($cid);
	    	$cr_info = $this->getSortCatetory($cr_info);
	    	$id_arr[] = $cid;
	    	foreach ($cr_info as $key => $value) {
	    		$id_arr[] = $value['CR_ID'];
	    	}
	    	$ids = implode(",", $id_arr);
	    	if($ids){
	    		$where['CR_ID'] = array('in',$ids);
	    		$info = $cr->where($where)->delete();
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
}