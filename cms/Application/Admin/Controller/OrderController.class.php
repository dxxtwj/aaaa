<?php
namespace Admin\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class OrderController extends CommonController {

	//交易信息
    public function transaction(){
        $order = M('Order_rec');
        $orderinfo = array();
        //要求：计算出当前年份中各月份，各状态的订单数
        //交易金额
        $where['OR_DeleteState'] = 0;
        $where['OR_State'] = array('in','1,2,3,5');
        $recyle_res = $order->field('OR_OrderTotal')->where($where)->select();
        $recycle = 0;
        foreach($recyle_res as $v){
            $recycle += $v['OR_OrderTotal'];
        } 
        array_push($orderinfo,number_format($recycle,2));
        //订单数量
        $all_order = $order->count();
        array_push($orderinfo,$all_order);
        //关闭交易
        $close_order = $order->where(array('OR_State'=>6))->count();
        array_push($orderinfo,$close_order);
        //订单回收站
        $recycle_order = $order->where(array('OR_DeleteState'=>1))->count();
        array_push($orderinfo,$recycle_order);
        //售后订单
        $s_where['OR_State'] = array('in','4,5,8');
        $custom_order = $order->where($s_where)->count();
        array_push($orderinfo,$custom_order);


        $order = array();
        $start_month = 1;//开始月份
        //所有订单
        $all = array();
        $all_order = $this->SelectOrderAll($start_month,$all);
        $all_order_s = implode($all_order,',');

        //待支付
        $not_pay = array();
        $notpay_order = $this->SelectOrderNotPay($start_month,$not_pay=array());
        $notpay_order_s = implode($notpay_order,',');

        //已付款
        $pay = array();
        $pay_order = $this->SelectOrderPay($start_month,$pay=array());
        $pay_order_s = implode($pay_order,',');

        //待签收
        $send = array();
        $send_order = $this->SelectOrderSend($start_month,$send=array());
        $send_order_s = implode($send_order,',');
        // dump(json_encode($all_order_s));
        // dump(json_encode($all_order));
        // dump($all_order);
        $this->assign('order_all',$all_order_s);
        $this->assign('notpay_order',$notpay_order_s);
        $this->assign('pay_order',$pay_order_s);
        $this->assign('send_order',$send_order_s);
        $this->assign('order',$orderinfo);

        $this->display();
    }
    //递归查询每个月的 所有 订单数量
    public function SelectOrderAll($start_month,$all=array()){
        $order = M('Order_rec');

        $current_month = date('m');//当前月份
        $dt = date('Y').'-'.$start_month.'-'.'01'.' '.'00:00:00';//1月份的起始与结束日期
        $rrt = $this->getthemonth($dt);
        $where['OR_CreateTime'] = array('between',array($rrt[0],$rrt[1]));
        $all[$start_month] = $order->where($where)->count();
        //当前月份开始时间
        $now_month = trim(date('0m'),'0');
        if($start_month < 12){
            $start_month ++;
            return $this->SelectOrderAll($start_month,$all);
            
        }else{
            // $all = -1;
        }
        return $all;  
    }

     //递归查询每个月的 待支付 订单数量
    public function SelectOrderNotPay($start_month,$all=array()){
        $order = M('Order_rec');

        $current_month = date('m');//当前月份
        $dt = date('Y').'-'.$start_month.'-'.'01'.' '.'00:00:00';//1月份的起始与结束日期
        $rrt = $this->getthemonth($dt);
        $where['OR_CreateTime'] = array('between',array($rrt[0],$rrt[1]));
        $where['OR_State'] = 0;
        $all[$start_month] = $order->where($where)->count();
        //当前月份开始时间
        $now_month = trim(date('0m'),'0');
        if($start_month < 12){
            $start_month ++;
            return $this->SelectOrderNotPay($start_month,$all);
            
        }else{
            // $all = -1;
        }
        return $all;  
    }

     //递归查询每个月的 已付款 订单数量
    public function SelectOrderPay($start_month,$all=array()){
        $order = M('Order_rec');

        $current_month = date('m');//当前月份
        $dt = date('Y').'-'.$start_month.'-'.'01'.' '.'00:00:00';//1月份的起始与结束日期
        $rrt = $this->getthemonth($dt);
        $where['OR_CreateTime'] = array('between',array($rrt[0],$rrt[1]));
        $where['OR_State'] = 1;
        $all[$start_month] = $order->where($where)->count();
        //当前月份开始时间
        $now_month = trim(date('0m'),'0');
        if($start_month < 12){
            $start_month ++;
            return $this->SelectOrderPay($start_month,$all);
            
        }else{
            // $all = -1;
        }
        return $all;  
    }

     //递归查询每个月的 待签收 订单数量
    public function SelectOrderSend($start_month,$all=array()){
        $order = M('Order_rec');

        $current_month = date('m');//当前月份
        $dt = date('Y').'-'.$start_month.'-'.'01'.' '.'00:00:00';//1月份的起始与结束日期
        $rrt = $this->getthemonth($dt);
        $where['OR_CreateTime'] = array('between',array($rrt[0],$rrt[1]));
        $where['OR_State'] = 2;
        $all[$start_month] = $order->where($where)->count();
        //当前月份开始时间
        $now_month = trim(date('0m'),'0');
        if($start_month < 12){
            $start_month ++;
            return $this->SelectOrderSend($start_month,$all);
            
        }else{
            // $all = -1;
        }
        return $all;  
    }
   

    //获取某个月的起始日期与结束时间，$date为那个月的时间
    function getthemonth($date){
       $firstday = date('Y-m-01 00:00:00', strtotime($date));
       $lastday = date('Y-m-d 23:59:59', strtotime("$firstday +1 month -1 day"));
       return array(strtotime($firstday), strtotime($lastday));
    } 
// /////////////////////////////////////////////////////////////////////////////////
    //订单管理
    public function Orderform(){
        // dump($_GET);
        $or = M('Order_rec');
        //根据订单号搜索
        if($_GET['orderKey']){
            // $where['OR_Key']='(OR_Key like "%'.$_GET['orderKey'].'%")';
            $where['OR_Key']=array('like','%'.$_GET['orderKey'].'%');
        }
        if($_GET['userInfo']){
            if(is_numeric($_GET['userInfo'])){
                $where['OR_Umobile'] = $_GET['userInfo'];
            }else{
                $user_where['UI_Name'] = $_GET['userInfo'];
                $ui_info = M('user_info')->where($user_where)->find();
                $where['OR_UID'] = $ui_info['UI_ID'];
            }
        }
        

        if($_GET['orderPress']){
            // $where['OR_ExpressKey']='(OR_ExpressKey like "%'.$_GET['orderPress'].'%")';
            $where['OR_ExpressKey']=array('like','%'.$_GET['orderPress'].'%');
        }
        $time_status = I('time_status'); //时间类型
        $addtime = $_GET['timea'];
        if($time_status && $addtime){
            $startTime = $addtime.' 00:00:00';
            $start = strtotime($startTime);
            $end = $start + 24 * 3600 - 1;
            $endTime = date('Y-m-d H:i:s',$end);
            switch($time_status){
                case 1:
                    $where['OR_CreateTime'] = array('between',array($start,$end));
                    break;
                case 2:
                    $where['OR_PayTime'] = array('between',array($start,$end));
                    break;
                case 3:
                    $where['OR_FahuoTime'] = array('between',array($start,$end));
                    break;
                case 4:
                    $where['OR_QianShouTime'] = array('between',array($start,$end));
                    break;

            }
        }else{//没有时间类型，有时间则默认下单时间
            if($addtime){
                $startTime = $addtime.' 00:00:00';
                $start = strtotime($startTime);
                $end = $start + 24 * 3600 - 1;
                $endTime = date('Y-m-d H:i:s',$end);
                $where['OR_CreateTime'] = array('between',array($start,$end));
            }
        }

        $order_status = I('order_status');
        if($order_status){
            switch((int)$order_status){
                case 8:
                    $where['OR_State'] = 0;
                    break;
                case 1:
                    $where['OR_State'] = 1;
                    break;
                case 2:
                    $where['OR_State'] = 2;
                    break;
                case 3:
                    $where['OR_State'] = 3;
                    break;
                case 6:
                    $where['OR_State'] = array('in','6,7');
                    break;
                case 7:
                    $where['OR_State'] = array('in','4,5,8');
                    break;
            }
        }
        
        //这段，左侧边栏单独传递过来的
        $orderType = I('orderType');
        if($orderType){
            switch($orderType){
                case 1://全部订单

                    break;

                case 2://代付款
                    $where['OR_State'] = 0;
                    break;

                case 3://待发货
                    $where['OR_State'] = 1;
                    break;

                case 4://待收货
                    $where['OR_State'] = 2;
                    break;

                case 5://已完成
                    $where['OR_State'] = 3;
                    break;

                case 6://已关闭
                    $where['OR_State'] = array('in','6,7');
                    break;

                case 7://售后申请
                    $where['OR_State'] = array('in','4,5,8');
                    break;
            }
        }

     
        
        $where['OR_DeleteState'] = 0;

        $info['num'] = $or->where($where)->count();

        $page = new \Think\Page($info['num'],10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $page->setConfig('frist','首页');
        $page->setConfig('last','尾页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->lastSuffix==false;
        $page -> setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $show       = $page->show();// 分页显示输出
        // dump($show);
        $orderList = $or->where($where)->order('OR_PayTime desc,OR_ID desc')->limit($page->firstRow.','.$page->listRows)->select();
        $info['total'] =0.00;
        // dump($or->getLastSql());

        $og = M('Order_goods');
        foreach ($orderList as $key => $val) {
            $og_where['OG_OID'] = $val['OR_ID'];
            $og_info = $og->where($og_where)->select();
            $small_total = 0.00;
            foreach ($og_info as $key1 => $value1) {
                $og_info[$key1]['OG_Price'] = $value1['OG_Price']/100;
                $og_info[$key1]['OG_Img'] = ltrim($og_info[$key1]['OG_Img'],".");
                if(!$value1['OG_Guige']){
                    $og_info[$key1]['OG_Guige'] = '';
                }else{
                    $og_info[$key1]['OG_Guige'] = $value1['OG_Guige'];
                }
                if($key1==0){
                    $og_info[$key1]['rowspan']=count($og_info);
                }else{
                    $og_info[$key1]['rowspan']=0;
                }
            }

            $orderList[$key]['discountList'] = M('DiscountRec')
                ->where(array('DR_OID' => $val['OR_ID']))
                ->field()
                ->select();//优惠表

            $info['total'] += $val['OR_OrderTotal'];  //订单总金额
            $yingshou = $val['OR_OrderTotal'];
            
            $orderList[$key]['small_total'] = number_format($val['OR_GoodsPrice'],2);  //商品价格
            $orderList[$key]['OR_YouFei'] = number_format($val['OR_YouFei'],2); //邮费
            $orderList[$key]['yingshou'] = number_format($yingshou,2); 

            $orderList[$key]['og_info'] = $og_info;
            $orderList[$key]['OR_CreateTime'] = date('Y-m-d H:i:s',$val['OR_CreateTime']);
            if($orderList[$key]['OR_PayTime']){
                $orderList[$key]['OR_PayTime'] = date('Y-m-d H:i:s',$val['OR_PayTime']);
            }else{
                $orderList[$key]['OR_PayTime'] = '';
            }
        }

        $info['total'] = number_format($info['total'],2);

        // dump(time());
        // dump($info);
        // dump($orderList);
        if($orderType){
            $o_type = $orderType;
        }else{
            $o_type = 1;
        }
        $alln['OR_DeleteState'] = 0;
        $all_num = $or->where($alln)->count();//全部订单

        $weiw['OR_DeleteState'] = 0;
        $weiw['OR_State'] = 0;
        $wei_num = $or->where($weiw)->count();//代付款

        $daiw['OR_DeleteState'] = 0;
        $daiw['OR_State'] = 1;
        $dai_num = $or->where($daiw)->count();//待发货

        $shouw['OR_DeleteState'] = 0;
        $shouw['OR_State'] = 2;
        $shou_num = $or->where($shouw)->count();//待收货

        $wanw['OR_DeleteState'] = 0;
        $wanw['OR_State'] = 3;
        $wan_num = $or->where($wanw)->count();//已完成

        $guanw['OR_DeleteState'] = 0;
        $guanw['OR_State'] = array('in','6,7');
        $guan_num = $or->where($guanw)->count();//已关闭

        $houw['OR_DeleteState'] = 0;
        $houw['OR_State'] = array('in','4,5,8');//售后
        $hou_num = $or->where($houw)->count();

        $this->assign('all_num',$all_num);
        $this->assign('wei_num',$wei_num);
        $this->assign('dai_num',$dai_num);
        $this->assign('shou_num',$shou_num);
        $this->assign('wan_num',$wan_num);
        $this->assign('guan_num',$guan_num);
        $this->assign('hou_num',$hou_num);
        $this->assign('orderType',$o_type);
        $this->assign("orderList",$orderList);


        if($_GET['orderKey']){
            $this->assign('key',$_GET['orderKey']);
        }
        if($_GET['userInfo']){
            $this->assign('uinfo',$_GET['userInfo']);
        }
        if($_GET['orderPress']){
            $this->assign('press',$_GET['orderPress']);
        }
        if($addtime){
            $this->assign('atime',$addtime);
        }
        $this->assign("info",$info);
        $this->assign("show",$show);
        $this->display();
    }

    // 编辑（填写快递信息）
    public function kuaidi_company(){
        $oid = I('oid');
        $exp["OR_ExpressKey"] = I('expressKey');
        $exp["OR_Company"] = I('company');

        $res = M('Order_rec')->where(array('OR_ID'=>$oid))->save($exp);

        if($res){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(-1);
        }


    }

    // 订单列表放入回收站
    public function listDel(){
        $id = I('oid');

        $data['OR_DeleteState'] = 1;   //放入回收站
        $res = M('Order_rec')->where(array('OR_ID'=>$id))->save($data);
        if($res){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(-1);
        }

    }

    // 发货处理
    public function send(){
        $where['OR_ID'] = I('oid');
        $where['OR_DeleteState'] = 0;
        $or=M('Order_rec');

        $orderinfo = $or->where($where)->field('OR_ID,OR_UID,OR_ExpressKey,OR_Company,OR_State,OR_Link,OR_Province,OR_City,OR_County,OR_Detail')->find();
        if(!$orderinfo){
            $flag['state'] = -1;
            $flag['mes'] = '该订单不存在';
        }elseif($orderinfo['OR_State']==0){
            $flag['state'] = -1;
            $flag['mes'] = '该订单还未付款';
        }elseif($orderinfo['OR_State']==2){
            $flag['state'] = -1;
            $flag['mes'] = '该订单商品已发货';
        }elseif($orderinfo['OR_State']==3){
            $$flag['state'] = -1;
            $flag['mes'] = '该订单已完成';
        }elseif($orderinfo['OR_State']==1){
            if(!$orderinfo['OR_ExpressKey']){
                $flag['state'] = -1;
                $flag['mes'] = '该订单未填写运单号';
            }elseif(!$orderinfo['OR_Company']){
                $flag['state'] = -1;
                $flag['mes'] = '该订单未填写快递公司';
            }else{
                $save['OR_State']=2;
                $save['OR_Tixing']=0;
                $save['OR_FahuoTime']=time();
                $info=$or->where($where)->save($save);

                if($info){
                    $flag['state'] = 1;
                    $flag['mes'] = '订单发货成功';
                }else{
                    $flag['state'] = -1;
                    $flag['mes'] = '订单发货失败';
                }
            }

        }

        $this->ajaxReturn($flag);
    }

    // 确认收货
    public function shouhuo(){
        $id = I('oid');
        $order = M('Order_rec');

        $data['OR_State'] = 3;
        $data['OR_QianShouTime'] = time();

        $state = $order->where(array('OR_ID'=>$id))->getField('OR_State');
        if($state == 2){
            $res = $order->where(array('OR_ID'=>$id))->save($data);
            if($res){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(-1);
            }
        }else{
            $this->ajaxReturn(2);
        }
    }

    // 查看物流
    public function wuliu(){
        $oid = I('oid');
        header('Content-Type:text/html; charset=utf-8');
        $exp = M('Order_rec')->where(array('OR_ID'=>$oid))->getField('OR_ExpressKey');   //获取快递单号

        if($exp != ''){
            $express = new \Org\Util\Express;
            $result  = $express->getorder($exp);
            if($result){
                $this->assign('res',$result);
            }else{
                $this->error('运单号错误',U('Order/Orderform'));
            }
        }else{
            $this->error('未添加运单号',U('Order/Orderform'));
        }
        $this->display();
    }

    //售后审核
    public function Customer(){
        // dump($_POST);
        // exit;
        $id = I('orid');
        $state = I('state');
        $order = M('Order_rec');

        if($state){
            if($state == 1){//通过
                $data['OR_State'] = 5;
                $data['OR_TuiHuoTime'] = time();
            }elseif($state == 2){
                $data['OR_State'] = 8;  //订单状态,售后审核不通过
            }
            $res = $order->where(array('OR_ID'=>$id))->save($data);

            if($res){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(-1);
            }
        }else{
            $this->ajaxReturn(-2); //缺少必要参数state
        }  
        
    }

    // 导出excel表格
    public function orderExcel(){
        header('Content-Type:text/html;charset=gb2312');
        if(I('loc')==='0'||I('loc')===0||I('loc')){
            $where['OR_DeleteState'] = I('loc');
        }

        $or = M('Order_rec');
        //根据订单号搜索
        if($_GET['orderKey']){
            $where['OR_Key']=array('like','%'.$_GET['orderKey'].'%');
        }
        if($_GET['userInfo']){
            if(is_numeric($_GET['userInfo'])){
                $where['OR_Umobile'] = $_GET['userInfo'];
            }else{
                $user_where['UI_Name'] = $_GET['userInfo'];
                $ui_info = M('user_info')->where($user_where)->find();
                $where['OR_UID'] = $ui_info['UI_ID'];
            }
        }
        

        if($_GET['orderPress']){
            $where['OR_ExpressKey']=array('like','%'.$_GET['orderPress'].'%');
        }
        $time_status = I('time_status'); //时间类型
        $addtime = $_GET['timea'];
        if($time_status && $addtime){
            $startTime = $addtime.' 00:00:00';
            $start = strtotime($startTime);
            $end = $start + 24 * 3600 - 1;
            $endTime = date('Y-m-d H:i:s',$end);
            switch($time_status){
                case 1:
                    $where['OR_CreateTime'] = array('between',array($start,$end));
                    break;
                case 2:
                    $where['OR_PayTime'] = array('between',array($start,$end));
                    break;
                case 3:
                    $where['OR_FahuoTime'] = array('between',array($start,$end));
                    break;
                case 4:
                    $where['OR_QianShouTime'] = array('between',array($start,$end));
                    break;

            }
        }else{//没有时间类型，有时间则默认下单时间
            if($addtime){
                $startTime = $addtime.' 00:00:00';
                $start = strtotime($startTime);
                $end = $start + 24 * 3600 - 1;
                $endTime = date('Y-m-d H:i:s',$end);
                $where['OR_CreateTime'] = array('between',array($start,$end));
            }
        }

        $order_status = I('order_status');
        if($order_status){
            switch((int)$order_status){
                case 8:
                    $where['OR_State'] = 0;
                    break;
                case 1:
                    $where['OR_State'] = 1;
                    break;
                case 2:
                    $where['OR_State'] = 2;
                    break;
                case 3:
                    $where['OR_State'] = 3;
                    break;
                case 6:
                    $where['OR_State'] = array('in','6,7');
                    break;
                case 7:
                    $where['OR_State'] = array('in','4,5,8');
                    break;
            }
        }
        
        //这段，左侧边栏单独传递过来的
        $orderType = I('orderType');
        if($orderType){
            switch($orderType){
                case 1://全部订单

                    break;

                case 2://代付款
                    $where['OR_State'] = 0;
                    break;

                case 3://待发货
                    $where['OR_State'] = 1;
                    break;

                case 4://待收货
                    $where['OR_State'] = 2;
                    break;

                case 5://已完成
                    $where['OR_State'] = 3;
                    break;

                case 6://已关闭
                    $where['OR_State'] = array('in','6,7');
                    break;

                case 7://售后申请
                    $where['OR_State'] = array('in','4,5,8');
                    break;
            }
        }


        $res=M('Order_rec')->where($where)->select();

        // dump($res);exit;
        $data=array(); 
        foreach ($res as $key=>$val){
            // 付款时间
            if(!$val['OR_PayTime']){
                $res[$key]['OR_PayTime'] = '';
            }else{
                $res[$key]['OR_PayTime'] = date('Y-m-d H:i:s',$val['OR_PayTime']);

            }
            // 发货时间
            if(!$val['OR_FahuoTime']){
                $res[$key]['OR_FahuoTime'] = '';
            }else{
                $res[$key]['OR_FahuoTime'] = date('Y-m-d H:i:s',$val['OR_FahuoTime']);
            }
            // 签收时间
            if(!$val['OR_QianShouTime']){
                $res[$key]['OR_QianShouTime'] = '';
            }else{
                $res[$key]['OR_QianShouTime'] = date('Y-m-d H:i:s',$val['OR_QianShouTime']);
            }
            // 申请售后时间
            if(!$val['OR_ShenQingTuiHuoTime']){
                $res[$key]['OR_ShenQingTuiHuoTime'] = '';
            }else{
                $res[$key]['OR_ShenQingTuiHuoTime'] = date('Y-m-d H:i:s',$val['OR_ShenQingTuiHuoTime']);
            }
            // 退货时间
            if(!$val['OR_TuiHuoTime']){
                $res[$key]['OR_TuiHuoTime'] = '';
            }else{
                $res[$key]['OR_TuiHuoTime'] = date('Y-m-d H:i:s',$val['OR_TuiHuoTime']);
            }
            switch($val['OR_State']){
                case 0:
                    $res[$key]['OR_State'] = '待支付';
                    break;
                case 1:
                    $res[$key]['OR_State'] = '待发货';
                    break;
                case 2:
                    $res[$key]['OR_State'] = '待签收';
                    break;
                case 3:
                    $res[$key]['OR_State'] = '已完成';
                    break;
                case 4:
                    $res[$key]['OR_State'] = '申请售后';
                    break;
                case 5:
                    $res[$key]['OR_State'] = '售后中';
                    break;
                case 6:
                    $res[$key]['OR_State'] = '订单关闭';
                    break;
                case 7:
                    $res[$key]['OR_State'] = '用户删除';
                    break;
                 case 8:
                    $res[$key]['OR_State'] = '售后审核不通过';
                    break;
            }
            switch($val['OR_PayType']){
                case 2:
                    $res[$key]['OR_PayType'] = '微信';
                    break;
                case 3:
                    $res[$key]['OR_PayType'] = '微信';
                    break;
                case 1:
                    $res[$key]['OR_PayType'] = '支付宝';
                    break;
                case 4:
                    $res[$key]['OR_PayType'] = '微信扫码支付';
                    break;
                case 5:
                    $res[$key]['OR_PayType'] = '支付宝扫码支付';
                    break;
                
            }
            switch($val['OR_ISPackage']){
                case 2:
                    $res[$key]['OR_ISPackage'] = '大礼包';
                    break;
                case 1:
                    $res[$key]['OR_ISPackage'] = '普通商品';
                    break;
                
            }
            $us = M('user_info');
            $us_info = $us->field('UI_Name')->where(array("UI_ID"=>$val['OR_UID']))->find();
            $og=M('Order_goods');
            $og_info=$og->where('OG_OID='.$val['OR_ID'])->select();
            $goods_name="";
            $goods_price="";
            $goods_guige="";
            $goods_number="";
            unset($name_arr);
            unset($price_arr);
            unset($guige_arr);
            unset($number_arr);
            // $barcode_arr = array();
            // foreach ($og_info as $k => $value) {
            //     $name_arr[]=$value['OG_Name'];

            //     $price_arr[]=$value['OG_Price']/100;
            //     $barcode = M('Goods_rec')->field('GR_Barcode')->where(array('GR_ID'=>$value['OG_GID']))->find();
            //     $barcode_arr[] = $barcode['GR_Barcode'];
            //     if($value['OG_Guige']&&$value['OG_Guige']!='null'){
            //         $guige_arr[]=$value['OG_Guige'];
            //     }else{
            //         $guige_arr[]='无规格';
            //     }
                
            //     $number_arr[]='x'.$value['OG_Number'];

            // }
            $goods_name=implode(" , ", $name_arr);
            $goods_price=implode(" , ", $price_arr);
            $goods_guige=implode(" , ", $guige_arr);
            $goods_number=implode(" , ", $number_arr);
            $goods_barcode = implode(" , ",$barcode_arr);
            array_push($data, array(
                //这里的需要导出的内容，要注意键名跟上面的字段键名要一致
                'OR_Key'=>" ".$val['OR_Key'],
                'OR_ISPackage'=>$res[$key]['OR_ISPackage'],
                'username'=>$us_info['UI_Name'].'/'." ".$val['OR_Umobile'],
                'OR_ExpressKey'=>" ".$val['OR_ExpressKey'],
                'OR_Company'=>$val['OR_Company'],
                'OR_OrderTotal'=>$val['OR_OrderTotal'],
                'OR_GoodsPrice'=>$val['OR_GoodsPrice'],
                'OR_GoodsCost'=>$val['OR_GoodsCost'],
                'OR_Profit'=>$val['OR_Profit'],
                'OR_YouFei'=>$val['OR_YouFei'],
                'goods_name'=>$goods_name,
                'goods_guige'=>$goods_guige,
                'goods_price'=>$goods_price,
                'goods_number'=>$goods_number,
                'goods_barcode'=>$goods_barcode,
                'OR_State'=>$res[$key]['OR_State'],
                'OR_CreateTime'=>date('Y-m-d H:i:s',$val['OR_CreateTime']),
                'OR_PayTime'=>$res[$key]['OR_PayTime'],
                'OR_FahuoTime'=>$res[$key]['OR_FahuoTime'],
                'OR_QianShouTime'=>$res[$key]['OR_QianShouTime'],
                'OR_ShenQingTuiHuoTime'=>$res[$key]['OR_ShenQingTuiHuoTime'],
                'OR_TuiHuoTime'=>$res[$key]['OR_TuiHuoTime'],
                'OR_PayType'=>$res[$key]['OR_PayType'],
                'OR_Link'=>'收货人：'.$val['OR_Link'].'；联系手机号：'.$val['OR_Phone'],
                'OR_Province'=>$val['OR_Province'].' '.$val['OR_City'].' '.$val['OR_County'].' '.$val['OR_Detail'],
                'OR_Message'=>$val['OR_Message'],
                ));
        } 
          //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能import导入
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.Writer.Excel2007");
        import("Org.Util.PHPExcel.IOFactory.php");

        $filename="YUKI全球优品订单列表";
        $headArr=array("订单号","商品类型","用户信息","快递单号","快递公司","订单总价","商品总价","订单成本","订单利润","邮费(单位：元)","商品名","商品规格","商品单价(单位：元)","商品数量","条形码","订单状态","下单时间","支付时间","发货时间","签收时间","申请售后时间","售后时间","支付方式","收货人、联系电话","收货地址","用户留言");
        // dump($where);
        $this->getExcel($filename,$headArr,$data);
    }

    private function getExcel($fileName,$headArr,$data){
        header('Content-Type:text/html;charset=utf-8');
      //对数据进行检验
        if(empty($data) || !is_array($data)){
            // die("data must be a array");
            // die("数据必须是一个数组");
            die("暂时还没有数据");
        }
        //检查文件名
        if(empty($fileName)){
            exit;
        }

        $date = date("Y_m_d",time());
        $fileName .= "_{$date}.xls";

      //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();
      
        //设置表头
        $key = ord("A");
        foreach($headArr as $v){
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        
        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        foreach($data as $key => $rows){ //行写入
            $span = ord("A");
            foreach($rows as $keyName=>$value){// 列写入
                $j = chr($span);
                $objActSheet->setCellValue($j.$column, $value);
                $span++;
            }
            $column++;
      }

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //重命名表
        // $objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
      header("Content-Disposition: attachment;filename=\"$fileName\"");
      header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }

    //导入Excel表格
    public function Stu_Excel(){      
        // dump($_FILES);
        //接收前台文件  
        $ex = $_FILES['file_stu']; 
        if (!empty($_FILES['file_stu']['name'])){
            $file_types = explode(".", $_FILES['file_stu']['name'] );
            $file_type = $file_types[count($file_types) - 1];
            /*判别是不是.xls文件，判别是不是excel文件*/
            if (strtolower ( $file_type ) != "xls"){    
                // if(strtolower ( $file_type ) != "xlsx"){
                    $this->error ( '不是Excel文件，重新上传' );
                // }          
            }
            /*以时间来命名上传的文件*/
            $str = date('Ymdhis'); 
            $file_name = $str . "." . $file_type;
            $path = './Uploads/excel/'.$file_name;//设置移动路径  

            $result = move_uploaded_file($ex['tmp_name'],$path);  
            dump($result);
            //表用函数方法 返回数组  
            $exfn = $this->_readExcel($path);  
            //重定向
            // $this->redirect('input'); 
            // dump($exfn); 
        }else{
            $this->error('请选择文件');
        } 
        
    }  
  
    //创建一个读取excel数据，可用于入库  
    public function _readExcel($filename){      
        // 引用PHPexcel 类  
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.Writer.Excel2007");
        import("Org.Util.PHPExcel.IOFactory.php");
        //创建PHPExcel对象，注意，不能少了\
        $PHPExcel=new \PHPExcel();
        $PHPReader=new \PHPExcel_Reader_Excel5();
        //载入文件
        $PHPExcel=$PHPReader->load($filename);
        // $PHPExcel=\PHPExcel_IOFactory::load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet=$PHPExcel->getSheet(0);
        //获取总列数
        $allColumn=$currentSheet->getHighestColumn();
        //获取总行数
        $allRow=$currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for($currentRow=2;$currentRow<=$allRow;$currentRow++){
            //从哪列开始，A表示第一列
            for($currentColumn='A';$currentColumn!='AJ';$currentColumn++){
                //数据坐标
                $address=$currentColumn.$currentRow;
                //读取到的数据，保存到数组$arr中
                $arr[$currentRow][$currentColumn]=$currentSheet->getCell($address)->getValue();
            }
        
        }
        // return $arr;
       
        $order = M('Order_rec');
        $flag = 0;
        $flag_error = 0;
        foreach($arr as $key => $val){
            // dump($val);
            $data_res['OR_ExpressKey'] = trim($val[D]);
            $data_res['OR_Company'] = trim($val[E]);
            $data_res['OR_State'] = 2;
            $data_res['OR_FahuoTime'] = time();

            $where['OR_Key'] = trim($val[A]);
            // if($val[P] != '待发货'){
                // if($val[B] != null && $val[C] != null){
                     $res = $order->where($where)->save($data_res);
                     if($res){
                        $flag++;
                     }else{
                        $flag_error++;
                     }
                // }else{
                //     $flag = -1;
                //     continue;
                // }
            // }else{
            //     $flag = -1;
            //     continue;
            // }
        }
        //重定向
        $this->success('一键发货成功条数：'.$flag.',发送失败条数：'.$flag_error,'/index.php/Admin/Order/Orderform',3);
    } 
// /////////////////////////////////////////////////////////////////////////////////
    //订单处理
    public function Order_handling(){
        $or = M('Order_rec');
        //根据订单号搜索
        if($_GET['orderKey']){
            // $where['OR_Key']='(OR_Key like "%'.$_GET['orderKey'].'%")';
            $where['OR_Key']=array('like','%'.$_GET['orderKey'].'%');
        }
        if($_GET['userInfo']){
            if(is_numeric($_GET['userInfo'])){
                $where['OR_Umobile'] = $_GET['userInfo'];
            }else{
                $user_where['UI_Name'] = $_GET['userInfo'];
                $ui_info = M('user_info')->where($user_where)->find();
                $where['OR_UID'] = $ui_info['UI_ID'];
            }
        }
        

        if($_GET['orderPress']){
            // $where['OR_ExpressKey']='(OR_ExpressKey like "%'.$_GET['orderPress'].'%")';
            $where['OR_ExpressKey']=array('like','%'.$_GET['orderPress'].'%');
        }
        $time_status = I('time_status'); //时间类型
        $addtime = $_GET['timea'];
        if($time_status && $addtime){
            $startTime = $addtime.' 00:00:00';
            $start = strtotime($startTime);
            $end = $start + 24 * 3600 - 1;
            $endTime = date('Y-m-d H:i:s',$end);
            switch($time_status){
                case 1:
                    $where['OR_CreateTime'] = array('between',array($start,$end));
                    break;
                case 2:
                    $where['OR_PayTime'] = array('between',array($start,$end));
                    break;
                case 3:
                    $where['OR_FahuoTime'] = array('between',array($start,$end));
                    break;
                case 4:
                    $where['OR_QianShouTime'] = array('between',array($start,$end));
                    break;

            }
        }else{//没有时间类型，有时间则默认下单时间
            if($addtime){
                $startTime = $addtime.' 00:00:00';
                $start = strtotime($startTime);
                $end = $start + 24 * 3600 - 1;
                $endTime = date('Y-m-d H:i:s',$end);
                $where['OR_CreateTime'] = array('between',array($start,$end));
            }
        }

        $order_status = I('order_status');
        if($order_status){
            switch((int)$order_status){
                case 8:
                    $where['OR_State'] = 0;
                    break;
                case 1:
                    $where['OR_State'] = 1;
                    break;
                case 2:
                    $where['OR_State'] = 2;
                    break;
                case 3:
                    $where['OR_State'] = 3;
                    break;
                case 6:
                    $where['OR_State'] = array('in','6,7');
                    break;
                case 7:
                    $where['OR_State'] = array('in','4,5,8');
                    break;
            }
        }
        
        //这段，左侧边栏单独传递过来的
        $orderType = I('orderType');
        if($orderType){
            switch($orderType){
                case 1://全部订单

                    break;

                case 2://代付款
                    $where['OR_State'] = 0;
                    break;

                case 3://待发货
                    $where['OR_State'] = 1;
                    break;

                case 4://待收货
                    $where['OR_State'] = 2;
                    break;

                case 5://已完成
                    $where['OR_State'] = 3;
                    break;

                case 6://已关闭
                    $where['OR_State'] = array('in','6,7');
                    break;

                case 7://售后申请
                    $where['OR_State'] = array('in','4,5,8');
                    break;
            }
        }

        
        $where['OR_DeleteState'] = 1;

        $info['num'] = $or->where($where)->count();

        $page = new \Think\Page($info['num'],10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $page->setConfig('frist','首页');
        $page->setConfig('last','尾页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->lastSuffix==false;
        $page -> setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $show       = $page->show();// 分页显示输出
        // dump($show);
        $orderList = $or->where($where)->order('OR_PayTime desc,OR_ID desc')->limit($page->firstRow.','.$page->listRows)->select();
        $info['total'] =0.00;
        // dump($or->getLastSql());

        $og = M('Order_goods');
        foreach ($orderList as $key => $val) {
            $og_where['OG_OID'] = $val['OR_ID'];
            $og_info = $og->where($og_where)->select();
            $small_total = 0.00;
            foreach ($og_info as $key1 => $value1) {
                $og_info[$key1]['OG_Price'] = $value1['OG_Price']/100;
                $og_info[$key1]['OG_Img'] = ltrim($og_info[$key1]['OG_Img'],".");
                if(!$value1['OG_Guige']){
                    $og_info[$key1]['OG_Guige'] = '';
                }else{
                    $og_info[$key1]['OG_Guige'] = $value1['OG_Guige'];
                }
                if($key1==0){
                    $og_info[$key1]['rowspan']=count($og_info);
                }else{
                    $og_info[$key1]['rowspan']=0;
                }
            }
            $info['total'] += $val['OR_OrderTotal'];  //订单总金额
            $yingshou = $val['OR_OrderTotal'];
            
            $orderList[$key]['small_total'] = number_format($val['OR_GoodsPrice'],2);  //商品价格
            $orderList[$key]['OR_YouFei'] = number_format($val['OR_YouFei'],2); //邮费
            $orderList[$key]['yingshou'] = number_format($yingshou,2); 

            $orderList[$key]['og_info'] = $og_info;
            $orderList[$key]['OR_CreateTime'] = date('Y-m-d H:i:s',$val['OR_CreateTime']);
            if($orderList[$key]['OR_PayTime']){
                $orderList[$key]['OR_PayTime'] = date('Y-m-d H:i:s',$val['OR_PayTime']);
            }else{
                $orderList[$key]['OR_PayTime'] = '';
            }
        }
        $info['total'] = number_format($info['total'],2);

        // dump(time());
        // dump($info);
        // dump($orderList);
        if($orderType){
            $o_type = $orderType;
        }else{
            $o_type = 1;
        }
        $alln['OR_DeleteState'] = 1;
        $all_num = $or->where($alln)->count();//全部订单

        $weiw['OR_DeleteState'] = 1;
        $weiw['OR_State'] = 0;
        $wei_num = $or->where($weiw)->count();//代付款

        $daiw['OR_DeleteState'] = 1;
        $daiw['OR_State'] = 1;
        $dai_num = $or->where($daiw)->count();//待发货

        $shouw['OR_DeleteState'] = 1;
        $shouw['OR_State'] = 2;
        $shou_num = $or->where($shouw)->count();//待收货

        $wanw['OR_DeleteState'] = 1;
        $wanw['OR_State'] = 3;
        $wan_num = $or->where($wanw)->count();//已完成

        $guanw['OR_DeleteState'] = 1;
        $guanw['OR_State'] = array('in','6,7');
        $guan_num = $or->where($guanw)->count();//已关闭

        $houw['OR_DeleteState'] = 1;
        $houw['OR_State'] = array('in','4,5,8');//售后
        $hou_num = $or->where($houw)->count();

        $this->assign('all_num',$all_num);
        $this->assign('wei_num',$wei_num);
        $this->assign('dai_num',$dai_num);
        $this->assign('shou_num',$shou_num);
        $this->assign('wan_num',$wan_num);
        $this->assign('guan_num',$guan_num);
        $this->assign('hou_num',$hou_num);
        $this->assign('orderType',$o_type);
        $this->assign("orderList",$orderList);


        if($_GET['orderKey']){
            $this->assign('key',$_GET['orderKey']);
        }
        if($_GET['userInfo']){
            $this->assign('uinfo',$_GET['userInfo']);
        }
        if($_GET['orderPress']){
            $this->assign('press',$_GET['orderPress']);
        }
        if($addtime){
            $this->assign('atime',$addtime);
        }
        $this->assign("info",$info);
        $this->assign("show",$show);
        $this->display();
    }

    // 订单回收站列表还原订单
    public function recycleHuan(){
        $id = I('oid');
        $data['OR_DeleteState'] = 0;
        $res = M('Order_rec')->where(array('OR_ID'=>$id))->save($data);

        if($res){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(-1);
        }

    }
    // 订单回收站列表删除订单
    public function recycleDel(){
        $id = I('oid');

        $res = M('Order_rec')->where(array('OR_ID'=>$id))->delete();
        $ret = M('Order_goods')->where(array('OG_OID'=>$id))->delete();

        if($res && $ret){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(-1);
        }

    }
// /////////////////////////////////////////////////////////////////////////////////
    //支付管理
    public function Cover_management(){
        $this->display();
    }
// /////////////////////////////////////////////////////////////////////////////////
    //订单详情
    public function order_detailed(){
        $id = I('oid');
        $or = M('Order_rec');
        $og = M('Order_goods');

        $discount = M('DiscountRec')// 查询使用的满减和优惠券
            ->where(array('DR_OID' => $id))
            ->field('DR_Money, DR_Reduce, DR_Type')//满多少减多少
            ->find();

        if ($discount['DR_Type'] == 1) {//优惠券
            $data['youHuiQuan'] =  $discount['DR_Money'].'减'.$discount['DR_Reduce'];

        } elseif ($discount['DR_Type'] == 2) {//满额减

            $data['manEJian'] = $discount['DR_Money'].'减'.$discount['DR_Reduce'];
        }
            $this->assign('discount', $data);

        $orderinfo = $or->where('OR_ID='.$id)->find();
        $orderinfo['OR_CreateTime'] = date('Y-m-d H:i:s',$orderinfo['OR_CreateTime']);
        switch($orderinfo['OR_State']){
            case 0:
                $orderinfo['OR_State'] = '待支付';
                break;
            case 1:
                $orderinfo['OR_State'] = '待发货';
                break;
            case 2:
                $orderinfo['OR_State'] = '待签收';
                break;
            case 3:
                $orderinfo['OR_State'] = '已完成';
                break;
            case 4:
                $orderinfo['OR_State'] = '申请售后';
                break;
            case 5:
                $orderinfo['OR_State'] = '售后中';
                break;
            case 6:
                $orderinfo['OR_State'] = '订单关闭';
                break;
            case 7:
                $orderinfo['OR_State'] = '用户删除';
                break;
            case 8:
                $orderinfo['OR_State'] = '售后审核不通过';
                break;
        }

        switch($orderinfo['OR_PayType']){
            case 2:
                $orderinfo['OR_PayType'] = '微信';
                break;
            case 3:
                $orderinfo['OR_PayType'] = '微信';
                break;
            case 1:
                $orderinfo['OR_PayType'] = '支付宝';
                break;
            case 4:
                $orderinfo['OR_PayType'] = '微信扫码支付';
                    break;
            case 5:
                $orderinfo['OR_PayType'] = '支付宝扫码支付';
                break;
        }

        $res=$og->where('OG_OID='.$id)->select();
        $all_num = 0;
        foreach($res as $k=>$v){
            $all_num += $v['OG_Number'];
            $res[$k]['OG_Price'] = $v['OG_Price']/100;
            if(!$v['OG_Guige']){
                $res[$k]['OG_Guige'] = '';
            }
        }
        $res[0]['all_num'] = $all_num;
        // dump($orderinfo);
        // dump($res);

        $this->assign('orderinfo',$orderinfo);
        $this->assign('res',$res);
        $this->display();
    }

    //订单详情修改地址
    public function ChangeOrderAddress(){
        header('Content-Type:text/html;charset=utf-8');
        $order = M('Order_rec');
        $oid = I('orid');
        $save_data['OR_Link']  = I('link');
        $save_data['OR_Phone']  = I('phone');
        $save_data['OR_Province']  = I('pro');
        $save_data['OR_City']  = I('city');
        $save_data['OR_County']  = I('county');
        $save_data['OR_Detail']  = I('detail');

        if($oid){
            $where['OR_ID'] = $oid;
            $where['OR_State'] = array('in','0,1');
            $res = $order->field('OR_State')->where($where)->find();
            if($res){
               $res = $order->where(array('OR_ID'=>$oid))->save($save_data);
                if($res){
                    $flag['state'] = 1;
                    $flag['mes'] = '更新订单成功';
                }else{
                    $flag['state'] = -2;
                    $flag['mes'] = '更新订单失败';
                }
            }else{
                $flag['state'] = -3;
                $flag['mes'] = '该订单不可修改地址（只有待付款或者待发货订单可以修改地址信息）';
            }
            
        }else{
            $flag['state'] = -1;
            $flag['mes'] = '查询订单失败';
        }

        $this->ajaxReturn($flag);
    } 

    /**
    * 下载文件
    * header函数
    *
    */
    public function ExcelDownload(){

        $file_xls = './Uploads/excel/send.xls';
        
        $example_name=basename($file_xls);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        // header('Content-Disposition: attachment; filename='.basename($filepath));
        header('Content-Disposition: attachment; filename='.mb_convert_encoding($example_name,"gb2312","utf-8"));  //转换文件名的编码 
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_xls));
        ob_clean();
        flush();
        readfile($file_xls);
    }
    
}