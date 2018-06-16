<?php

namespace Admin\Controller;

use Think\Controller;

class CrontabController extends Controller {

	private $each_time = 600;//每一期时间

    // 自动收货

    public function Auto_Deliver(){

        $or=M('Order_rec');

        $where['OR_State']= 2;//待收货

        $sr=M('system_rec');



        $sr_info=$sr->where('SR_ID=1')->find();

        if(!$sr_info||!$sr_info['SR_AutoReceipt']){

            exit;

        }



        $or_info=$or->where($where)->select();



        foreach ($or_info as $key => $value) {

            $closeTime = $value['OR_FahuoTime']+$sr_info['SR_AutoReceipt']*24*3600;

            $now=time();

            if($closeTime<$now){
                $save['OR_State']=3; //确认收货
                $save['OR_QianShouTime'] = time();
                $or->where('OR_ID='.$value['OR_ID'])->save($save);

            }

        }

    }



    //自动关闭订单

    public function close_order(){

        $or=M('Order_rec');

        $where['OR_State']= 0;

        $sr=M('system_rec');
        $sr_info=$sr->where('SR_ID=1')->find();

        if(!$sr_info||!$sr_info['SR_OrderClose']){
            exit;
        }

        $or_info=$or->where($where)->select();
        foreach ($or_info as $key => $value) {

            $closeTime = $value['OR_CreateTime']+$sr_info['SR_OrderClose']*24*3600;

            $now=time();

            if($closeTime<$now){
                $save['OR_State']=6; //交易关闭

                $or->where('OR_ID='.$value['OR_ID'])->save($save);

            }

        }



    }









    public function taskBatch(){

        $ip = $_SERVER['REMOTE_ADDR'];//120.77.76.178

        if($ip=="120.77.76.178"){
        // if($ip=="119.147.218.112"){

            $this->end();

            $this->start();

        }else{

        }

    }





    //新一轮

    private function start(){

        $cr = M('current_rec');//当期表

        $pr = M('past_rec');//往期表

        $oy = M('order_yi');//订单表



        $gr = M('goods_rec');//用来减库存

        $fo = M('format_option');

        



        //公共记录

        $setFirst = $cr->where('CR_Is_Common=1')->find();

        if(!$setFirst){//首次激活定时器

            $time = $this->nextTenMinutes();

            // $this->setCommonTime($time['start'],$time['end']);

            $time['CR_Is_Common'] = 1;

            $com_add = $cr->add($time);

            // $this->writeLog('定时器初始化---状态：'.$com_add);

        }



        // 新一期

        $sr=D('system_rec')->find();

        if(!($sr['SR_Switch']==1)){//后台定时器是否开启

            exit();

        }

        

        $com=$cr->where('CR_Is_Common=1')->find();//查询

        if(!empty($com['CR_Luck_Num'])){

            $new_record['CR_Prev_Num'] = $com['CR_Luck_Num'];

            $new_record['CR_Luck_Num'] = 0;

            $new_record['CR_Prev_EndTime'] = $com['CR_EndTime'];



            //判断定时器情况在设置时间

            $time=$this->nextTenMinutes();

            // $new_record['CR_StartTime'] = $com['CR_StartTime'] + $this->each_time;

            // $new_record['CR_EndTime'] = $new_record['CR_StartTime'] + $this->each_time;

            $new_record['CR_StartTime'] = $time['CR_StartTime'];

            $new_record['CR_EndTime'] = $time['CR_EndTime'];

            $flag = $cr->where('CR_Is_Common=1')->save($new_record);

            // $this->writeLog('新一期--更新公共信息---状态：'.$flag);

            $com=$cr->where('CR_Is_Common=1')->find();//查询

        }

        $map['GR_Is_Show'] = 1;//是否上架

        $map['GR_Is_Delete'] = 0;//是否删除

        $map['GR_Type'] = 2;//一折购类型

        $gr_info = $gr->where($map)->select();//一折购商品信息

        // dump($gr_info);

        //循环当前参与一折购的商品

        $goods_log = '';

        foreach ($gr_info as $k => $v) {

            if(!$v['GR_ID']){

                continue;

            }

            $map1['CR_GID'] = $map2['CR_GID'] = $v['GR_ID'];

            //确立当前商品的期数

            $info = $cr->where($map1)->field('CR_Qishu')->order('CR_Qishu desc')->find();

            if($info){

                $map2['CR_Qishu']=(int)$info['CR_Qishu']+1;//当前最大期数+1

            }else{

                $map2['CR_Qishu']=1;//第一期

            }



            // $t=((int)$tc['TC_Kaijiang'])*60;//一期的时间

            $t = $this->each_time;

            $map2['CR_StartTime'] = $com['CR_StartTime'];

            $map2['CR_EndTime'] = ((int)$map2['CR_StartTime'])+$t;

            if($map2['CR_Qishu'] == 1){

                $new = $cr->add($map2);

            }else{

                $map2['CR_UIDS'] = '';

                $map2['CR_Nums'] = '';

                $save = $cr->where(array('CR_GID'=>$v['GR_ID']))->save($map2);

            }

            // $goods_log .= '商品id：'.$v['GD_GID'].'--期数:'.$map2['CR_Qishu'].'--时间：'.date('Y-m-d H:i:s',$map2['CR_StartTime']).'~~~'.date('Y-m-d H:i:s',$map2['CR_EndTime'].'---状态：'.$new."\r\n");

            // $this->writeLog($goods_log);

        }





    }

    //生成开奖号码

	private function set_num(){

		$a=mt_rand(100,999);//生成一个随机的3位数

		$b=floor($a/28);

		$number=$a-$b*28+1;

		return $number;

		// return 1;

	}



    private function end(){

        $number = $this->set_num();//中奖号码

        $n = (int)$number-1;//中奖用户索引

        $cr = M('current_rec');//当期表

        $pr = M('past_rec');//往期表

        $oy = M('order_yi');//订单表



        $gr = M('goods_rec');//用来减库存

        $fo = M('format_option');



        $com = $cr->where('CR_Is_Common=1')->find();//查询

        

        if($com && !$com['CR_Luck_Num'] && $com['CR_StartTime'] && $com['CR_EndTime']){

            // dump('开奖1');

            // $this->writeLog('开奖号码：'.$number);

            $cr_info = $cr->where('CR_EndTime='.$com['CR_EndTime'])->select();

            // $cr_change_where['CR_EndTime'] = $com['CR_EndTime'];

            $cr_change_where['CR_Is_Common'] = 1;

            $cr_change_save['CR_Luck_Num'] = $number;

            $cr_change_save['CR_LastKjtime'] = $com['CR_EndTime'];



            $cr_change = $cr->where($cr_change_where)->save($cr_change_save);

            foreach ($cr_info as $k => $v) {
                // dump($cr_change_where);
                if(!$v['CR_GID']){
                    continue;
                }
                if(!empty($v['CR_UIDS'])){
                    $pr_gid_arr[] = $v['CR_GID'];
                    // dump('开奖3');
                    $uids = explode(",",$v['CR_UIDS']);

                    $nums = explode(",",$v['CR_Nums']);

                    $map['OY_Qishu'] = $cr_info[$k]['CR_Qishu'];//期数

                    $map['OY_GID']=$cr_info[$k]['CR_GID'];//商品id


                    $pr_info = array();
                    $pr_info['qishu'] = $v['CR_Qishu'];

                    for($i = 0;$i<count($uids);$i++){//循环参与的用户id，修改中奖状态
                        // dump('开奖4');

                        $map['OY_UID'] = $uids[$i];//用户id

                        $map['OY_Number']=$nums[$i];

                        $user['uid'] = $uids[$i];
                        $user['num'] = $nums[$i];
                        $oyid = $oy->field('OY_ID')->where($map)->find();
                        $user['oid'] = $oyid['OY_ID'];

                        $pr_info['information'][] = $user;
                        

                        $map_win['OY_State'] = 3;

                        $map_not['OY_State'] = 2;

                        if($nums[$i]==$number){
                            $map_win['OY_LuckNum'] = $number;
                            $oy_save = $oy->where($map)->save($map_win);//中奖
                            // dump($oy_save);
                            

                            //若有人中奖，将该用户下单的商品库存-1

                            $info = $oy->field('OY_FormatOptionID')->where($map)->find();
 
                            $guigeID = $info['OY_FormatOptionID'];

                            $gr_info = $gr->where('GR_ID='.$map['OY_GID'])->find();

                            if($gr_info['GR_Less'] == 3){



                                if(!$guigeID){

                                    //若无多规格

                                    $kucun=((int)$gr_info['GR_Stock'])-1;

                                    $setkucun=$gr->where('GR_ID='.$map['OY_GID'])->setField('GR_Stock',$kucun);

                                }elseif($guigeID){

                                    //若有多规格

                                    $setkucun = $fo->where(array('FO_ID'=>$guigeID))->setDec('FO_Stock',1);

                                }

                            }



                        }else{
                            $map_not['OY_LuckNum'] = $number;
                            $oy_save = $oy->where($map)->save($map_not);//未中奖
                            // dump($oy_save);

                        }


                    }
                    $pr_info['gid'] = $v['CR_GID'];
                    $pr_info1[] = $pr_info;

                    // dump($map);

                }

            }



            if($pr_info1){
                $pr_add['PR_AddTime'] = time();
                $pr_add['PR_StartTime'] = $com['CR_StartTime'];
                $pr_add['PR_EndTime'] = $com['CR_EndTime'];
                $pr_add['PR_LuckNum'] = $number;
                $pr_add['PR_Information'] = json_encode($pr_info1);
                $pr_id = $pr->add($pr_add);
                if($pr_id && $pr_gid_arr){
                    // $pr_gids = implode(",", $pr_gid_arr);
                    foreach ($pr_gid_arr as $k_pr => $v_pr) {
                        $gr_pr_where['GR_ID'] = $v_pr;
                        $oldid = $gr->where(array('GR_ID'=>$v_pr))->field('GR_PastID')->find();
                        if($oldid['GR_PastID']){
                            $gr_pr_save['GR_PastID'] = $oldid['GR_PastID'].','.$pr_id;
                    dump($gr_pr_save);
                            $gr->where($gr_pr_where)->save($gr_pr_save);
                        }elseif((int)$oldid['GR_ID'] == 0){
                            $gr_pr_save['GR_PastID'] = $pr_id;
                            $gr->where($gr_pr_where)->save($gr_pr_save);
                        }
                    }
                }
            }
            // dump($pr_info1);

        }

    }

    //得出下一个整10分 时间

    private function nextTenMinutes(){

        $now=time();

        $time['CR_StartTime']=(int)(floor($now/$this->each_time))*$this->each_time;

        // $time['CR_StartTime']=((int)($now/$this->each_time)+1)*$this->each_time;

        $time['CR_EndTime']=((int)$time['CR_StartTime'])+$this->each_time;

        return $time;

    }



    public function text(){

        $now = time();

        $now1 = (int)(floor($now/$this->each_time))*$this->each_time;

        echo $now.','.$now1;

    }



    private function writeLog($msg){

        $logFile = './Uploads/timerLog/'.date('Y-m-d').'.txt';

        $msg = date('Y-m-d H:i:s').' >>> '.$msg."\r\n";

        file_put_contents($logFile,$msg,FILE_APPEND);

    }

    private function setCommonTime($start,$end){

        $cr = D('current_rec');

        $time['CR_StartTime']=$start;

        $time['CR_EndTime']=$end;



        $com = $cr->where('CR_Is_Common = 1')->find();

        if($com){

            $com_save = $cr->where('CR_Is_Common=1')->save($time);

        }else{

            $time['CR_Is_Common']=1;

            $com_add = $cr->add($time);

        }

    }



    //删除无用记录

    // private function delete(){

    //     $cr=D('goods_one_discount');



    //     $map['CR_Is_Common']  = 0;

    //     $goods_id=$cr->field('GR_ID')->where($map)->order('CR_Qishu desc')->group('GR_ID')->select();

        



    //     foreach ($goods_id as $k => $v) {

    //         $map1['CR_Is_Common']  = 0;

    //         $map1['GR_ID']=$v['GR_ID'];

    //         $count=$cr->where($map1)->count();

    //         if($count>3){

    //             $record=$cr->where($map1)->select();

    //             foreach($record as $kk => $vv){

                    

    //                 if(!$vv['GO_UIDS']){

    //                     $letete=$cr->where('GO_ID='.$vv['GO_ID'])->delete();

    //                 }



    //                 if($kk>=$count-3){

    //                     break;

    //                 }//保留最近2条记录

                    

    //             }

    //             // $info[]=$record;

    //         }

    //     }



    //     // $this->ajaxReturn($info);

    // }



    

}