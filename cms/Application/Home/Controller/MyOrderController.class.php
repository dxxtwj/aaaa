<?php
namespace Home\Controller;
use Think\Controller;
 
header("Content-type: text/html; charset=utf-8");
class MyOrderController extends HomeController {
	public function initialize_first(){
		$uname =(int)$_SESSION['Home']['userid'];
		$res = $this->SelectCookie();
		if((int)$res == -1){
			// header('Location:/index.php/Home/User/Login?loc=myorder');
			header('Location:/index.php/Home/User/Login/class/MyOrder/method/index');
		}
	}
	/**
	 * 我的订单显示页面
	 * 默认显示全部
	 */
	public function index(){
		$this->initialize_first();
    	$state = I('state');
		if($state == -1){
			$status = -1;//全部
		}elseif($state == 0){
			$status = 0;//待付款
		}elseif($state == 1){
			$status = 1;//待发货
		}elseif($state == 2){
			$status = 2;//待签收
		}elseif($state == 3){
			$status = 3;//已完成
		}
		$orderList = $this->getOrder($status, 0, 0);

		if ($orderList) {

			$orderData = $orderList;

		} else {

			$orderData = 0;
		}
		
		$this->assign('orderDataJson',json_encode($orderData));
		$this->display();
		
	}
	/**
	 * ajax请求我的订单
	 */
	public function getOrder_ajax(){

		$this->initialize_first();
		$state = I('state');
		$page = I('page');
		if($state == -1){
			$status = -1;//全部
		}elseif($state == 0){
			$status = 0;//待付款
		}elseif($state == 1){
			$status = 1;//待发货
		}elseif($state == 2){
			$status = 2;//待签收
		}elseif($state == 3){
			$status = 3;//已完成
		}
		$or_info = $this->getOrder($status, $page);
		if ($or_info) {

			$orderData = $or_info;

		} else {

			$orderData = 0;
		}

		$this->ajaxReturn($orderData);
	}

	/**
	 * 获取订单数据的一个方法
	 * 一个订单数组里面可能存在两个以上的商品数据
	 */
	public function getOrder($status=-1, $page) {
		$this->initialize_first();
		$uid = $_SESSION['Home']['userid']; // 用户id

		$orderRec = M('OrderRec'); // 订单表
		$orderGoods = M('OrderGoods');// 订单商品表

		if ($status != -1) { // 全表查

			$orderWhere['OR_State'] = $status;
		}
		$num = 5; // 查询5条
		$orderWhere['OR_UID'] = $uid;//用户id
		$orderWhere['OR_DeleteState'] = 0;
		$orderList = $orderRec
			->where($orderWhere)
			->field('OR_State as state, OR_OrderTotal as total_price, OR_Key as danhao, OR_ID as id')
			->order('id desc')
			->limit($page * $num, $num)
			->select();
		
		foreach ($orderList as $k => $v) {
			$orderGoodsWhere['OG_OID'] = $v['id'];
			$orderGoodsList = $orderGoods
			    ->where($orderGoodsWhere)
			    ->field('OG_Name as mes, OG_Img as img, OG_Number as num, OG_GID as gid, OG_Price as price, OG_OID as oid')
			    ->select();

			foreach ($orderGoodsList as $key => $val) {

					$orderList[$k]['array'][$key]= $val;
					$orderList[$k]['array'][$key]['price']= $val['price'] * 0.01;
			}
			if($orderList[$k]['state'] == 0){
				$orderList[$k]['state'] = '待付款';
				$orderList[$k]['status'] = 0;
				$orderList[$k]['move'] = '付款';
			}elseif($orderList[$k]['state'] == 1){
				$orderList[$k]['state'] = '待发货';
				$orderList[$k]['status'] = 1;
			}elseif($orderList[$k]['state'] == 2){
				$orderList[$k]['state'] = '待签收';
				$orderList[$k]['move'] = '确认收货';
				$orderList[$k]['status'] = 2;
			}elseif($orderList[$k]['state'] == 3){
				$orderList[$k]['state'] = '已完成';
				$orderList[$k]['move'] = '申请售后';
				$orderList[$k]['status'] = 3;
			}elseif($orderList[$k]['state'] == 4){
				$orderList[$k]['state'] = '申请售后';
				$orderList[$k]['status'] = 4;
			}elseif($orderList[$k]['state'] == 5){
				$orderList[$k]['state'] = '售后';
				$orderList[$k]['status'] = 5;
			}elseif($or_info[$k1]['state'] == 6){
				$orderList[$k]['state'] = '无效订单';
				$orderList[$k]['status'] = 6;
			}elseif($orderList[$k]['state'] == 7){
				$orderList[$k]['state'] = '用户删除';
				$orderList[$k]['status'] = 7;
			}elseif($or_info[$k1]['state'] == 8){
				$orderList[$k]['state'] = '售后审核失败';
				$orderList[$k]['status'] = 8;
			}
		}
		return $orderList;		
	}

	//我的订单猜你喜欢
	public function getOrderlike(){
		$gr = M('goods_rec');
		$fo = M('format_option');
		$cr = M('category_rec');

		$gr_where['GR_Is_Recommend'] = 1;//是否热销
		$gr_where['GR_Is_Show'] = 1;//是否上架
		$gr_where['GR_Is_Delete'] = 0;//是否删除
		$gr_where['GR_Type'] = array('in','1,3');//商品类型

		$page_num = 25;

			// return $page_num*$page;

		$gr_info = $gr->where($gr_where)->field('GR_ID as id,GR_IMG as img,GR_Name as mes,GR_Price as price,GR_Is_Options as is_Options')->limit($page_num)->order('GR_Sort desc')->select();

		$fo = M('format_option');

		foreach ($gr_info as $key => $value) {

			if($value['is_Options'] == 1){
				$fo_info = $fo->field('FO_ID as id,FO_Price as price')->where(array('FO_GID'=>$value['id']))->order('FO_ID DESC')->find();
				if($fo_info){
					//商品默认显示的信息
					$gr_info[$key]['price'] = $fo_info['price'];
				}

			}
			$gr_info[$key]['href'] = '/index.php/Home/Goods/goods_detail/gid/'.$gr_info[$key]['id'];
			$gr_info[$key]['price'] = $gr_info[$key]['price']*0.01;

		}

		return $gr_info;

	}

	/**
	 * 订单详情处理 ajax请求
	 */
	public function orderAjax() {
		$this->initialize_first();

		if (IS_AJAX && IS_POST) {

			$oid = I('oid');
			if ((int)$oid <= 0) {
				dump(404);exit;
			}
			if ($oid) {

				$data['code'] = 1;
				$data['url'] = '/index.php/Home/MyOrder/order_details/oid/'.$oid;

			} else {

				$data['code'] = 0;
			}

			$this->ajaxReturn($data);
		}

	}
	/**
	 * 订单详情页显示页面
	 */
	public function order_details() {
		$this->initialize_first();

		$oid = I('oid');//订单id

		if ((int)$oid <= 0 || empty($oid)) {
			dump(404);exit;
		}
		//OR_PayTime 订单付款时间 OR_FahuoTime 发货时间
		$orderRec = M('OrderRec');
		$orderGoods = M('OrderGoods');
		$orderData = $orderRec
		    ->where(array('OR_ID' => $oid, 'OR_DeleteState' => 0))
		    ->field('OR_State as state, OR_OrderTotal as goodsPrice, OR_YouFei as takeMoney, OR_Key as danhao, OR_Province as province, OR_City as city, OR_County as county, OR_Detail as detail, OR_Link as name, OR_Phone as phone, OR_CreateTime as orderTime, OR_PayTime as payTime, OR_FahuoTime as goodTime, OR_ShiJiPay as goodPrice, OR_ExpressKey as wuliu, OR_Company as company')
		    ->find();
		    
		if ($orderData['payTime']) {
			$orderData['payTime'] = date('Y-m-d H:i:s', $orderData['payTime']);//订单付款时间
		}
		if ($orderData['orderTime']) {
			$orderData['orderTime'] = date('Y-m-d H:i:s', $orderData['orderTime']);//订单创建时间
		}
		if ($orderData['goodTime']) {
			$orderData['goodTime'] = date('Y-m-d H:i:s', $orderData['goodTime']);// 发货时间 
		}

		$discount = M('DiscountRec');//优惠表
			$discountList = $discount
				->where(array('DR_OID' => $oid))
				->field('DR_Money, DR_Reduce, DR_Type')
				->select();
			foreach ($discountList as $k => $v) {

				if ($v['DR_Type'] == 1) { // 1优惠券，2满减，3满额包邮
					$orderData['youHuiQuan'] = $v['DR_Money'].'减'.$v['DR_Reduce'];
				} elseif ($v['DR_Type'] == 2) {
					$orderData['manJian'] = $v['DR_Money'].'减'.$v['DR_Reduce'];
				}
			}

		    if($orderData['state'] == 0){
				$orderData['state'] = '待付款';
			}elseif($orderData['state'] == 1){
				$orderData['state'] = '待发货';
			}elseif($orderData['state'] == 2){
				$orderData['state'] = '待签收';
			}elseif($orderData['state'] == 3){
				$orderData['state'] = '已完成';
			}elseif($orderData['state'] == 4){
				$orderData['state'] = '申请售后';
			}elseif($orderData['state'] == 5){
				$orderData['state'] = '售后';
			}elseif($orderData['state'] == 6){
				$orderData['state'] = '无效订单';
			}elseif($orderData['state'] == 7){
				$orderData['state'] = '用户删除';
			}elseif($orderData['state'] == 8){
				$orderData['state'] = '售后审核失败';
			}

	    // 拼接地址信息
		$address['name'] = $orderData['name'];
		$address['phone'] = $orderData['phone'];
		$address['loc'] = $orderData['province'].$orderData['city'].$orderData['county'].$orderData['detail'];

		// 获取订单的各种时间
		$orderTime['orderTime'] = $orderData['orderTime'];// 创建时间
		$orderTime['payTime'] = $orderData['payTime'];// 付款时间
		$orderTime['goodTime'] = $orderData['goodTime'];// 发货时间

		$orderGoodsList = $orderGoods
			->where(array('OG_OID' => $oid))
			->field('OG_Name as mes, OG_Img as img, OG_Number as num, OG_Price as price, OG_Guige as selected')
			->select();
		foreach ($orderGoodsList as $k => $v) { // 更改价格

			$orderGoodsList[$k]['price'] = $v['price'] * 0.01;
		}

		if ($orderData && $orderGoodsList) {

			$orderData['mes'] = $orderGoodsList; // 追加商品数据
			$orderData['goodNum'] = count($orderGoodsList); // 追加商品数据
			$orderRecList = $orderData;

		} else {

			$orderRecList = 0;
		}
		$exp = $orderData['OR_ExpressKey'];
		// $exp = 888334243257963810; //模拟数据
        if($exp != ''){ 
            $express = new \Org\Util\Express;
            $result  = $express->getorder($exp);
            if($result['message'] == 'ok'){

                $orderWuLiu['wuliu'] = $result;
            }else{

            	$orderWuLiu['wuliu'] = 0;
            }
        }else{
        	$orderWuLiu['wuliu'] = 0;
        }
		
		$this->assign('orderTimeJson', json_encode($orderTime)); //订单的各种时间
		$this->assign('addressJson', json_encode($address));//地址的各种时间
		$this->assign('orderRecListJson', json_encode($orderRecList));//订单的信息
		$this->assign('orderWuLiuJson', json_encode($orderWuLiu));//物流信息
		$this->assign('youHuiJson', json_encode($youHui));//优惠的
		$this->display();

	}

	/**
	 * 确认收货
	 */
	public function affirmOrder() {

		if (IS_AJAX && IS_POST) {

			$oid = I('oid');//订单id
			if ((int)$oid <= 0 || empty($oid)) {

				$data['code'] = 0;
				$data['msg'] = '确认失败 404';
				$this->ajaxReturn($data);
				exit;
			}

			$saveOrder['OR_State'] = 3; // 已完成
			$orderRecSave = M('OrderRec')//订单表
				->where(array('OR_ID' => $oid))
				->save($saveOrder);
			
			if ($orderRecSave) {

				$data['code'] = 1;
				$data['msg'] = '确认收货成功';
			} else {

				$data['code'] = 0;
				$data['msg'] = '确认收货失败';
			}
				$this->ajaxReturn($data);
				exit;

		}
	}

	/**
	 * 取消订单  ajax请求 
	 */
	public function exitOrder() {
		$this->initialize_first();

		if (IS_AJAX && IS_POST) {

			$oid = I('oid');//订单id
			if ((int)$oid <= 0 || empty($oid)) {

				$data['code'] = 0;
				$data['msg'] = '取消订单失败';
				$this->ajaxReturn($data);
				exit;
			}

			$orderDeleteSave['OR_DeleteState'] = 1; // 1是放进回收站
			$orderDelete = M('OrderRec')->where(array('OR_ID' => $oid))->save($orderDeleteSave);

			if ($orderDelete) {

				$data['code'] = 1;
				$data['msg'] = '取消订单成功';
			} else {

				$data['code'] = 0;
				$data['msg'] = '取消订单失败';
			}
				$this->ajaxReturn($data);
				exit;

		}

	}

	//确认收货
	public function confirm_order(){
		$this->initialize_first();
		$or = M('order_rec');

		$where['OR_ID'] = (int)I('oid');
		$where['OR_UID'] = (int)$_SESSION['Home']['userid'];
		// $where['OR_UID'] = 1;
		if(!($where['OR_ID']>0) || !($where['OR_UID']>0)){
			$this->ajaxReturn(-1);
		}

		$or_info = $or->where($where)->field('OR_State')->find();
		if($or_info && $or_info['OR_State'] == 2){
			$save['OR_State'] = 3;
			$save['OR_QianShouTime'] = time();
			$info = $or->where($where)->save($save);
			if($info){
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
		}else{
			$this->ajaxReturn(-1);
		}
	}

	public function cancel_order(){
		$this->initialize_first();
		$or = M('order_rec');

		$where['OR_ID'] = (int)I('oid');
		$where['OR_UID'] = (int)$_SESSION['Home']['userid'];
		// $where['OR_UID'] = 1;
		if(!($where['OR_ID']>0) || !($where['OR_UID']>0)){
			$this->ajaxReturn(-1);
		}

		$or_info = $or->where($where)->field('OR_State')->find();
		if($or_info && $or_info['OR_State'] == 0){
			$save['OR_State'] = 6; 
			$info = $or->where($where)->save($save);
			if($info){
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
		}else{
			$this->ajaxReturn(-1);
		}
	}
	//申请售后
	public function service_order(){
		$this->initialize_first();
		$or = M('order_rec');

		$where['OR_ID'] = (int)I('oid');
		$where['OR_UID'] = (int)$_SESSION['Home']['userid'];
		// $where['OR_UID'] = 1;
		if(!((int)$where['OR_ID']>0) || !((int)$where['OR_UID']>0)){
			$this->ajaxReturn(-1);
		}

		$sr = M('system_rec');
		$sr_info = $sr->field('SR_ISCustomerService,SR_OrderExchangeGoods')->find();

		$or_info = $or->where($where)->field('OR_State,OR_QianShouTime')->find();
		if($or_info && $or_info['OR_State'] == 3 && $sr_info && $sr_info['SR_ISCustomerService'] == 1 && $sr_info['SR_OrderExchangeGoods']*24*3600 + $or_info['OR_QianShouTime'] >= time() ){
			$save['OR_State'] = 4; 
			$save['OR_ShenQingTuiHuoTime'] = time();
			$info = $or->where($where)->save($save);
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