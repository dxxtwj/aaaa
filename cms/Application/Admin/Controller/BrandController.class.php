<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Model;

header("Content-type: text/html; charset=utf-8");
class BrandController extends CommonController {
	public function index(){
        $br = M('Brand_rec');
        // $ir = M('images_rec');
        $page_num = 6;

        if($_GET['brand_name']){
            $where['BR_Name'] = array('like','%'.$_GET['brand_name'].'%');
        }
        if($_GET['starttime'] && !$_GET['endtime']){

            $where['BR_AddTime'] = array('egt',strtotime($_GET['starttime']));

        }elseif(!$_GET['endtime'] && $_GET['endtime']){

            $where['BR_AddTime'] = array('elt',strtotime($_GET['endtime']));

        }elseif($_GET['starttime'] && $_GET['endtime']){

            $where['BR_AddTime'] = array('between',array(strtotime($_GET['starttime']),strtotime($_GET['endtime'])));
        }
        // dump(strtotime($_GET['starttime']));
        // dump(strtotime($_GET['endtime']));
        // dump($where['BR_AddTime']);
        $count = $br->where($where)->count();
        $page = new \Think\Page($count,$page_num);
        $page->setConfig('first','首页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $show = $page->show();
        $limit = $page->firstRow.','.$page->listRows;

        $br_info = $br->where($where)->limit($limit)->order('BR_Sort desc,BR_AddTime desc')->select();
        foreach ($br_info as $key => $value) {
            if($value['BR_AddTime']){
                $br_info[$key]['BR_AddTime'] = date('Y-m-d H:i:s',$value['BR_AddTime']);
            }
            // $ir_info = $ir->field('IR_Path,IR_Img')->where(array('IR_ID'=>$value['BR_IID']))->find();
            // $br_info[$key]['IR_Path'] = $ir_info['IR_Path'];
            // $br_info[$key]['IR_Img'] = $ir_info['IR_Img'];
        }
        $this->assign('getInfo',$_GET);
        $this->assign('count',$count);
        $this->assign('res',$br_info);
        $this->assign('show',$show);
        $this->display();
    }

    //禁用、启用品牌
    public function setBrandShow(){
        $bid = I('bid');
        if((int)$bid>0){
            $br = M('brand_rec');
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


    public function add(){
        $br = M('brand_rec');

        if(IS_POST){
            
            $map['BR_Name'] = I('name');
            $map['BR_Sort'] = I('sort');
            $map['BR_Is_Show'] = I('isShow');
            $map['BR_IMG'] = I('path');

            if((int)I('bid')>0){
                $where['BR_ID'] = I('bid');
                $map['BR_UpdateTime'] = time();
                $info = $br->where($where)->save($map);
                if($info){
                    $this->success('修改成功');
                }else{
                    $this->error('修改失败');
                }
            }else{
                $map['BR_AddTime'] = time();
                $info = $br->add($map);
                if($info){
                    $this->success('添加成功','index');
                }else{
                    $this->error('添加失败');
                }
            }

        }else{
            if((int)I('bid')>0){
                $where['BR_ID'] = I('bid');
                $br_info = $br->where($where)->find();
                if($br_info){
                    // $ir = M('images_rec');
                    // $ir_info = $ir->field('IR_Path,IR_Img')->where(array('IR_ID'=>$br_info['BR_IID']))->find();
                    // if($ir_info){
                    //     $br_info = array_merge($br_info,$ir_info);
                    // }
                    // dump($br_info);
                    $this->assign('res',$br_info);
                }
            }
            $this->display();
        }
    }


    public function del(){
        $bids = I('bid');
        if($bids!=null){
            $br = M('brand_rec');
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

    public function detail(){

        $this->display();
    }
    public function setSort(){
        $bid = (int)I('bid');
        $sort = (int)I('sort');
        if($bid>0){
            $br = M('brand_rec');
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
}