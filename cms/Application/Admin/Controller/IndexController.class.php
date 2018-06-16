<?php
namespace Admin\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class IndexController extends CommonController {
    public function index(){
    	$map['id'] = array('in', $_SESSION['Admin']['info']['SA_Role_ID']);
        $roleinfo = M('auth_group')->where($map)->find();

        $where['pid'] = 0;
        $where['isMenu'] = 1;
        if($roleinfo['id'] == 1){
            $data = M('auth_rule')->where($where)->order('sort desc')->select();
            if($data){
                // 查出控制器的方法
                foreach($data as $key=>$val){
                    $where['pid'] = $val['id'];
                    $data[$key]['action'] = M('auth_rule')->where($where)->order('sort desc,id asc')->select();
                }
            }
           
        }else{
            $where['id'] = array('in',$roleinfo['rules']);
            $data = M('auth_rule')->where($where)->order('sort desc')->select();
            if($data){
                // 查出控制器的方法
                foreach($data as $key=>$val){
                    $where['pid'] = $val['id'];
                    $data[$key]['action'] = M('auth_rule')->where($where)->order('sort desc,id asc')->select();
                }
            }
        }
        if(!$data){
            $data = '';
        }  
        $this->assign('data', $data);
        $this->assign('username', $_SESSION['Admin']['info']['SA_Name']);
        $this->assign('roleinfo', $roleinfo);
        $this->assign('path', $_SESSION['Admin']['info']['SA_Path']);
        $this->assign('img', $_SESSION['Admin']['info']['SA_Img']);
        $this->display();
    }

    public function home(){
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


        //  商品相关
        $gr = M('goods_rec');
        $fo = M('format_option');

        $gr_where1['GR_Is_Delete'] = $gr_where2['GR_Is_Delete'] = $gr_where3['GR_Is_Delete'] = 0;
        //上架的商品
        $gr_where1['GR_Is_Show'] = 1;
        $goods_sale_count = $gr->where($gr_where1)->count();

        //下架的商品
        $gr_where2['GR_Is_Show'] = 0;
        $goods_notsale_count = $gr->where($gr_where2)->count();

        //缺少库存的商品
        $goods_emptyStcok_count = 0;
        $gr_info = $gr->field('GR_ID,GR_Is_Options,GR_Stock')->where($gr_where3)->select();
        foreach ($gr_info as $gr_key => $gr_value) {
            if((int)$gr_value['GR_Is_Options'] == 0 && (int)$gr_value['GR_Stock'] == 0){
                $goods_emptyStcok_count++;
            }elseif((int)$gr_value['GR_Is_Options'] == 1){
                $fo_where['FO_GID'] = $gr_value['GR_ID'];
                $fo_where['FO_Stock'] = 0;
                $fo_info = $fo->where($fo_where)->find();
                if($fo_info){
                    $goods_emptyStcok_count++;
                }
            }
        }

        $this->assign('goods_sale_count',$goods_sale_count);
        $this->assign('goods_notsale_count',$goods_notsale_count);
        $this->assign('goods_emptyStcok_count',$goods_emptyStcok_count);
        
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

    //数据统计
    public function getGoodsCount(){
        

    }
}