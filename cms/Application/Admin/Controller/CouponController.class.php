<?php
namespace Admin\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class CouponController extends CommonController {
    public function index(){
        // dump($_GET);
        $start = I('start_timea');
        $end = I('end_timea');
        $name = trim(I('couname'));
        if($name){
            $where['CR_Name'] = array('like','%'.$name.'%');
        }
        if($start && $end){
            $start_time = strtotime($start.' 00:00:00');
            $end_time = strtotime($end.' 23:59:59');
            $where['CR_StartTime'] = array('egt',$start_time);
            $where['CR_EndTime'] = array('elt',$end_time);
        }
        $where['CR_ISDelete'] = 1;
        $coupon = M('coupon_rec');
        $num = $coupon->where($where)->count();

        $page = new \Think\Page($num,10);
        $page->setConfig('frist','首页');
        $page->setConfig('last','尾页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->lastSuffix==false;
        $page -> setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $show = $page->show();
        // dump($where);
        $res = $coupon->where($where)->limit($page->firstRow.','.$page->listRows)->order('CR_Sort desc')->select();
        foreach($res as $k=>$v){
            switch($v['CR_State']){
                case 1:
                    $res[$k]['state'] = '开启';
                    $res[$k]['lose'] = '有效';
                break;
                case 0:
                    $res[$k]['state'] = '关闭';
                    $res[$k]['lose'] = '失效';
                break;
            }
            $res[$k]['time'] = date('Y-m-d H:i:s',$v['CR_StartTime']).'---'.date('Y-m-d H:i:s',$v['CR_EndTime']);
            if(time() < $v['CR_StartTime']){
                $res[$k]['lose'] = '失效';
            }
            if(time() > $v['CR_EndTime']){
                $res[$k]['lose'] = '失效';
                $dat['CR_State'] = 0;//关闭
                $coupon->where(array('CR_ID'=>$v['CR_ID']))->save($dat);
            }
        }
        $this->assign('res',$res);
        $this->assign('show',$show);
        $this->display();
    }

    public function Add(){
        if(IS_POST){
            $coupon = M('coupon_rec');
            
            $save['CR_Sort'] = I("displayorder");
            $save['CR_Name'] = I("couponname");
            $save['CR_Condition'] = I("enough");
            if((int)I('timelimit') == 1){
                $save['CR_TimeType'] = 2;
                $start = strtotime(I('start_timea').' 00:00:00');
                $end = strtotime(I('end_timea').' 23:59:59');
                $save['CR_StartTime'] = $start;
                $save['CR_EndTime'] = $end;
            }else{
                $save['CR_TimeType'] = 1;
                $save['CR_StartTime'] = time();
                $timedays = date('Y-m-d H:i:s',strtotime('+'.(int)I('timedays').' day'));
                $save['CR_EndTime'] = strtotime($timedays);
            }
            $save['CR_Minus'] = I("deduct");
            $save['CR_State'] = I("gettype");
            $save['CR_ISget'] = (int)I('isget');//领券中心是否可得
            $save['CR_UserGet'] = (int)I('getmax');//每人获得张数
            $save['CR_UpdateTime'] = time();

            $res = $coupon->add($save);
            if($res){
                $this->success('添加成功','/index.php/Admin/Coupon/index');
            }else{
                $this->error('添加失败','/index.php/Admin/Coupon/index');
            }
        }else{
            $this->display();
        }
    }

    public function del(){
        $coupon = M('coupon_rec');
        if(IS_POST && IS_AJAX){
            $crid = (int)I('crid');
            $data['CR_ISDelete'] = 0;
            $res = $coupon->where(array('CR_ID'=>$crid))->save($data);
            if($res){
                $flag['msg'] = '删除成功';
            }else{
                $flag['msg'] = '删除失败';
            }
        }else{
            $flag['msg'] = '非法的请求方式';
        }
        
        $this->ajaxReturn($flag);
        // $this->display();
    }

    public function update(){
        $coupon = M('coupon_rec');
        if(IS_POST){
            $save['CR_Sort'] = I("displayorder");
            $save['CR_Name'] = I("couponname");
            $save['CR_Condition'] = I("enough");
            if((int)I('timelimit') == 1){
                $save['CR_TimeType'] = 2;
                $start = strtotime(I('start_timea').' 00:00:00');
                $end = strtotime(I('end_timea').' 23:59:59');
                $save['CR_StartTime'] = $start;
                $save['CR_EndTime'] = $end;
            }else{
                $save['CR_TimeType'] = 1;
                $save['CR_StartTime'] = time();
                $timedays = date('Y-m-d H:i:s',strtotime('+'.(int)I('timedays').' day'));
                $save['CR_EndTime'] = strtotime($timedays);
            }
            $save['CR_Minus'] = I("deduct");
            $save['CR_State'] = I("gettype");
            $save['CR_ISget'] = (int)I('isget');//领券中心是否可得
            $save['CR_UserGet'] = (int)I('getmax');//每人获得张数
            $save['CR_UpdateTime'] = time();
            $crid = (int)I('crid');
            $res = $coupon->where(array('CR_ID'=>$crid))->save($save);
            if($res){
                $this->success('修改成功','/index.php/Admin/Coupon/index');
            }else{
                $this->error('修改失败','/index.php/Admin/Coupon/index');
            }
        }else{
            $crid = (int)I('crid');
            $res = $coupon->where(array('CR_ID'=>$crid))->find();
            if($res){
                if((int)$res['CR_TimeType'] == 1){
                    $time_day = $this->timediff($res['CR_StartTime'],$res['CR_EndTime']);
                    $res['timedays'] = $time_day['day'];
                }else{
                    $res['CR_StartTime'] = date('Y-m-d',$res['CR_StartTime']);
                    $res['CR_EndTime'] = date('Y-m-d',$res['CR_EndTime']);
                }
                $this->assign('res',$res);
            }else{
                $this->error('数据查询失败，请稍后重试','/index.php/Admin/Coupon/index');
            }
            $this->display();
        }
    }

    public function restartorclose(){
        $coupon = M('coupon_rec');
        if(IS_POST && IS_AJAX){
            $crid = (int)I('crid');
            $state = (int)I('state');
            if($state == 1){//关闭
                $save['CR_State'] = 0;
            }elseif($state == 2){
                $save['CR_State'] = 1;
            }
            $save['CR_UpdateTime'] = time();
            $res = $coupon->where(array('CR_ID'=>$crid))->save($save);
            if($res){
                $flag['msg'] = '修改成功';
            }else{
                $flag['msg'] = '修改失败';
            }
        }else{
            $flag['msg'] = '非法的请求方式';
        }
        $this->ajaxReturn($flag);

    }

    public function send(){
        $cou = M('coupon_rec');
        $info = M('user_info');
        $cr = M('coupon_push');

        if(IS_POST){
            // dump($_POST);
            $crid = (int)I('couid');
            $send_type = (int)I('send1');
            $res = $cou->field('CR_StartTime,CR_EndTime,CR_State,CR_ISDelete')->where(array('CR_ID'=>$crid))->find();
            if(time() > $res['CR_EndTime'] || (int)$res['CR_State'] == 0 || (int)$res['CR_ISDelete'] == 0){
                $lose = 0;//失效
            }else{
                $lose = 1;
            }

            switch($send_type) {
                case 1:
                    $phone = trim(I('send_phone'),',');
                    $phones = trim($phone,'，');
                    $str = str_replace("，",",",$phones);
                    $str_arr = explode(',',$str);
                    
                    foreach ($str_arr as $key => $value) {
                        $where['UI_Mobile'] = $value;
                        $uids = $info->field('UI_ID')->where($where)->find();

                        //检查是否有领取过
                        $push_where['CP_UID'] = (int)$uids['UI_ID'];
                        $push_where['CP_CID'] = $crid;
                        $push_res = $cr->where($push_where)->find();

                        if($uids && !$push_res){
                            $data['CP_UID'] = (int)$uids['UI_ID'];
                            $data['CP_CID'] = $crid;
                            $data['CP_StartTime'] = $res['CR_StartTime'];
                            $data['CP_EndTime'] = $res['CR_EndTime'];
                            $data['CP_State'] = 0;
                            $data['CP_Status'] = $lose;
                            $result = $cr->add($data);
                        }
                    }
                    $this->success('推送完成');
                    break;
                case 2:
                    $level = (int)I('send_level');
                    $user = $info->field('UI_ID as id')->where(array('UI_Status'=>$level))->select();
//                    $ids = array_column($user,'id');
                    foreach($user as $k => $v){
                        $ids[] = $v['id'];
                    }
                    foreach ($ids as $key => $value) {
                        //检查是否有领取过
                        $push_where['CP_UID'] = (int)$value;
                        $push_where['CP_CID'] = $crid;
                        $push_res = $cr->where($push_where)->find();

                        if(!$push_res){
                            $data['CP_UID'] = $value;
                            $data['CP_CID'] = $crid;
                            $data['CP_StartTime'] = $res['CR_StartTime'];
                            $data['CP_EndTime'] = $res['CR_EndTime'];
                            $data['CP_State'] = 0;
                            $data['CP_Status'] = $lose;
                            $result = $cr->add($data);
                        }
                    }
                    $this->success('推送完成');
                    break;

                case 3:
                    $user = $info->field('UI_ID as id')->select();
//                    $ids = array_column($user,'id');
                    foreach($user as $k => $v){
                        $ids[] = $v['id'];
                    }
                    foreach ($ids as $key => $value) {
                        //检查是否有领取过
                        $push_where['CP_UID'] = (int)$value;
                        $push_where['CP_CID'] = $crid;
                        $push_res = $cr->where($push_where)->find();

                        if(!$push_res){
                            $data['CP_UID'] = $value;
                            $data['CP_CID'] = $crid;
                            $data['CP_StartTime'] = $res['CR_StartTime'];
                            $data['CP_EndTime'] = $res['CR_EndTime'];
                            $data['CP_State'] = 0;
                            $data['CP_Status'] = $lose;
                            $result = $cr->add($data);
                        }
                    }
                    $this->success('推送完成');
                    break;
            }
        }else{
            $crid = (int)I('crid');
            $res = $cou->field('CR_ID,CR_Name')->where(array('CR_ID'=>$crid))->find();
            if($res){
                $this->assign('res',$res);
            }else{
                $this->error('数据查询失败，请稍后重试');
            }
            $this->display();
        }
    }

    public function FullSet(){
        $sys = M('system_rec');
        $full = M('full_reduce');

        if(IS_POST){

            $ret = $full->where('1')->delete(); 
            $s_data['SR_FreeShipping'] = (int)I('enoughfree');
            $s_data['SR_FullToPostage'] = (float)I('enoughorder');
            $sys_rec = $sys->where(array('SR_ID'=>1))->save($s_data);
            $enough = I('enough');
            $give = I('give');
            
            $arr = array();
            //前一个数组的值为新数组的索引，后一个参数的值为新数组的值
            $merge = array_combine($enough,$give);
            $i = 0;
            foreach($merge as $key => $value){
                if($value != '' && $key != ''){
                    $arr[$i]['full'] = (float)$key;
                    $arr[$i]['reduce'] = (float)$value;
                    $data['FR_FullMoney'] = (float)$key;
                    $data['FR_ReduceMoney'] = (float)$value;
                    $full_res = $full->add($data);
                    $i ++;
                }
            }
            $this->success('修改成功');
            
        }else{
            $full_res = $full->select();
            $sys_res = $sys->where(array('SR_ID'=>1))->find();
            $this->assign('res',$full_res);
            $this->assign('sys_res',$sys_res);
            $this->display();
        }
    }

    public function timediff($begin_time,$end_time){
        if($begin_time < $end_time){
            $starttime = $begin_time;
            $endtime = $end_time;
        }else{
            $starttime = $end_time;
            $endtime = $begin_time;
        }


        //计算天数
        $timediff = $endtime-$starttime;
        $days = intval($timediff/86400);
        //计算小时数
        $remain = $timediff%86400;
        $hours = intval($remain/3600);
        //计算分钟数
        $remain = $remain%3600;
        $mins = intval($remain/60);
        //计算秒数
        $secs = $remain%60;
        $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);
        return $res;
    }
}