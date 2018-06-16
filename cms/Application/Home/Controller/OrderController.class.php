<?php
namespace Home\Controller;
use Think\Controller; 
use Think\Model;
header("Content-type: text/html; charset=utf-8");
class OrderController extends HomeController {
	public function initialize_first(){
		$uname = $_SESSION['Home']['userid'];
		$res = $this->SelectCookie();
		if((int)$res == -1){
			header('Location:/index.php/Home/User/Login/class/Order/method/confirm_order');
			// header('Location:/index.php/Home/User/Login?loc=confirm_order');

		}
	}

    /**
     * 订单显示页面
     * 两种情况：购物车进来的  立即购买点击进来的
     * 通过以上两种情况，都要分别对多规格多规格和无规格进行处理
     * 本次商品总计（不包含邮费和满减）会在cart控制器处理完传输过来，还有本次提交订单的商品数量
     * 查询该单的邮费需要传本次订单的总重量
     * 先后顺序，满减完后再判断是否有可用优惠券
     */
	public function confirm_order() {
 		$this->initialize_first(); // 调用检测登录
 		
 		$priceTotalArray = array_sum($_SESSION['Home']['priceTotal']); // 所有商品总价，不包括邮费,立即购买和购物车进来都有值
		$uid = $_SESSION['Home']['userid']; // 用户id
		$crId = $_SESSION['Home']['crids']; // 购物车id
		$state = $_SESSION['Home']['JieSuanState']; // 1代表是购物车来  2代表是立即购买
		$bool = true; // 判断标记
		
		$cartRec = M('CartRec');// 购物车表
		$formatOptionRec = M('FormatOption'); // 规格表
		$goodsRec = M('GoodsRec'); // 商品表
		$addressRec = M('AddressRec');// 地址表
		
		if (empty($state)) {
			header('location:/index.php/Home/Index/index');
		}

		if ($_SESSION['Home']['addressId']){// 为真，则是选择地址

			$addressWhere['AR_ID'] = $_SESSION['Home']['addressId']; 
			unset($_SESSION['Home']['addressId']);// 清空地址id

		} else {//为假则显示默认地址

			$addressWhere['AR_Is_Default'] = 1; 
		}

		$addressWhere['AR_UID'] = $uid; // 用户id
		$address = $addressRec
			->where($addressWhere)
			->field('AR_Phone as phone, AR_Province as province, AR_City as city, AR_County as county, AR_Detail as detail, AR_Link as link, AR_ID as id')
			->find();

		if (empty($address)) { // 为空则查所有
			$where['AR_UID'] = $uid;
			$address = $addressRec
			->where($where)
			->field('AR_Phone as phone, AR_Province as province, AR_City as city, AR_County as county, AR_Detail as detail, AR_Link as link,  AR_ID as id')
			->find();
		}

////////////////////////////////////////////////购物车点击进来的///////////////////////////////////////////

		if ($state == 1) { // 购物车点击进来的

			if (empty($crId)) {

	    		$cartData['code'] = 0;
	    		$cartData['msg'] = '购物车不能为空';
	    		$cartData['url'] = '/index.php/Home/Cart/index';
	    		$bool = false;
			}

			foreach ($crId as $k => $v) {
				$crIds[] = $v;
			}
			$crIdsJoin = join(',', $crIds);
			$cartWhere['CR_ID'] = ['in', $crIdsJoin]; // 购物车id
			$cartWhere['CR_UID'] = $uid; // 用户id

			$cartList = $cartRec
				->where($cartWhere)
				->field('CR_OID as oid, CR_GID as gid, CR_Name as name, CR_Price as price, CR_Number as number, CR_Guige as guiGe, CR_Img as img, CR_Weight as weight, CR_ID as id')
				->select();

			foreach ($cartList as $k => $v) {
			
				$goodsWhere['GR_ID'] = $v['gid']; // 商品id
	    		$goodsWhere['GR_Is_Show'] = 1;	// 开启的,上架的
				$goodsWhere['GR_Is_Delete'] = 0; // 未删除

	    		$goodsList = $goodsRec // 商品表
		    		->where($goodsWhere)
		    		->field('GR_ID, GR_Stock, GR_Price, GR_Weight')
		    		->find();

		    	if (empty($goodsList)) { // 删除购物车下架商品

		    		$cartRec->where(array('CR_ID' => $v['CR_ID']))->delete();
		    		$cartData['code'] = 0;
		    		$cartData['msg'] = '购买的商品'.$v['name'].'已下架或删除';
		    		$cartData['url'] = '/index.php/Home/Cart/index';
	    			$bool = false;
	    		}

				if ($v['oid']) { // 存在多规格

					$option = $formatOptionRec // 规格表
		    			->where(array('FO_ID' => $v['oid']))
		    			->field('FO_Stock, FO_Price, FO_Weight')
		    			->find();

		    		if ($option['FO_Stock'] < $v['number']) {

		    			$cartData['code'] = 0;
			    		$cartData['url'] = '/index.php/Home/Cart/index';
			    		$cartData['msg'] = '购买的商品'.$v['name'].'库存不足,请重新添加购物车';
	    				$bool = false;
			    		
		    		}
		    		
			    	if ((int)$v['price'] != (int)$option['FO_Price']) {
		    			$cartRec->where(array('CR_ID' => $v['id']))->delete();//删除购物车表
		    			$cartData['code'] = 0;
			    		$cartData['url'] = '/index.php/Home/Cart/index';
			    		$cartData['msg'] = '购买的商品'.$v['name'].'价格有变,请重新提交订单';
	    				$bool = false;
	    			
		    		} else {

		    			$cartList[$k]['price'] = $option['FO_Price'] * 0.01; // 多规格现价价格
		    		}

		    		$cartList[$k]['guiGe'] = str_replace('@#', '+', $cartList[$k]['guiGe']);

				} else { // 不存在多规格
					
					if ((int)$v['price'] != (int)$goodsList['GR_Price']) {

		    			$cartRec->where(array('CR_ID' => $v['id']))->delete();//删除购物车表

		    			$cartData['code'] = 0;
			    		$cartData['url'] = '/index.php/Home/Cart/index';
			    		$cartData['msg'] = '购买的商品'.$v['name'].'价格有变,请重新加入购物车';
	    				$bool = false;

		    		} else {

		    			$cartList[$k]['price'] = $goodsList['GR_Price'] * 0.01; // 无规格现价价格
		    		}

		    		if ($goodsList['GR_Stock'] < $v['number']) { 

	    				$cartData['code'] = 0;
			    		$cartData['url'] = '/index.php/Home/Cart/index';
			    		$cartData['msg'] =  '购买的商品'.$v['name'].'库存不足,请重新添加购物车';
	    				$bool = false;
		    		}
				}
				$sum += $v['number'] * $v['weight']; // 获取单笔每个商品的总规格，数组形式
			}

////////////////////////////////////////////////////购物车进来的满减和邮费///////////////////////////////////////////////////////////

			$priceArray = $this->GetFullCut($priceTotalArray);// 检测本次交易可否包邮和满减

			if ($address) { // 如果地址存在则找邮费
		
				if ($priceArray['fullcut'] == -1) { // 表示不包邮，则去得邮费

					$youFei = $this->GetPostage($address['city'], $sum); // 得到邮费
				
					if ($youFei['state'] == -1) {  // 表示有错误

	    				$cartData['code'] =  0;
			    		$cartData['weight_price'] = $youFei['weight_price'];
			    		$cartData['msg'] = $youFei['msg'];
	    				$bool = false;
		    		}
		    		
				} else { // 包邮, 则运费价格清空

					$youFei['weight_price'] = '0';
				}
			} else { // 未设置地址

				$address['code'] = 0;
				$youFei['weight_price'] = '0'; // 运费为0，因为没设置地址
			}

			if ($priceArray['full_recude'] != -1) { // 可以参加满减
				
				if ($priceTotalArray >= $priceArray['full_money']) { // 满足添加则把总价减去满足条件的价钱

					$manJianPrice['priceTotalArray'] = $priceTotalArray; // 小计
					$manJianPrice['price'] = $priceTotalArray - $priceArray['full_recude']; // 满减后的价钱
					$manJianPrice['full_money'] = $priceArray['full_money']; // 满多少
					$manJianPrice['full_recude'] = $priceArray['full_recude']; // 减了多少
		
				}
			} else {

				$manJianPrice['price'] = $priceTotalArray;// 没满减,显示原来的小计
				$manJianPrice['full_money'] = 0; // 满多少
				$manJianPrice['full_recude'] = 0; // 减了多少
			}

////////////////////////////////////////////////////购物车进来的优惠券//////////////////////////////////////////////////////////////////
			
			$couponPrice = $priceTotalArray - $manJianPrice['full_recude'];//满减完的价格
			$couponArray = $this->coupon($couponPrice);// 查看是否有优惠券
		
			if ($couponArray != -1) { // 有优惠券可以使用
				
				$this->assign('couponArrayJson', json_encode($couponArray));

			} else {
			    $wuCoupon = '0';
				$this->assign('couponArrayJson', json_encode($wuCoupon));

			}

////////////////////////////////////////////////////立即点击进来的//////////////////////////////////////////////////////

		} elseif ($state == 2) { // 立即购买点击进来的

			$fid = $_SESSION['Home']['liJiGouMaiList']['fid']; // 规格id
			$number = $_SESSION['Home']['liJiGouMaiList']['number']; //购买数量
			$gid = $_SESSION['Home']['liJiGouMaiList']['gid'];// 商品id

			if ($gid <= 0 || $number <= 0) {

	    		$cartData['code'] = 0;
	    		$cartData['msg'] = '数据丢失或未选择数量';
	    		$cartData['url'] = '/index.php/Home/Goods/goods_detail/gid/'.$goodsList['GR_ID'];
	    		$bool = false;
			}
			
			$goodsWhere['GR_ID'] = $gid; // 商品id
    		$goodsWhere['GR_Is_Show'] = 1;	// 开启的,上架的
			$goodsWhere['GR_Is_Delete'] = 0; // 未删除
		
    		$goodsList = $goodsRec // 商品表
	    		->where($goodsWhere)
	    		->field('GR_ID, GR_Stock, GR_Price, GR_Weight, GR_Name as name, GR_IMG')
	    		->find();

	    	if (empty($goodsList)) {

	    		$cartData['code'] = 0;
	    		$cartData['msg'] = '购买的商品'.$goodsList['name'].'已下架或删除';
	    		$cartData['url'] = '/index.php/Home/Goods/goods_detail/gid/'.$goodsList['GR_ID'];
    			$bool = false;
    		}
    		if ($fid) { // 立即购买的多规格区间
    			$option = $formatOptionRec // 规格表
	    			->where(array('FO_ID' => $fid))
	    			->field('FO_Stock, FO_Price, FO_Weight, FO_IMG, FO_Name, FO_ID')
	    			->find();

	    		if ($option['FO_Stock'] < $number) {

	    			$cartData['code'] = 0;
		    		$cartData['url'] = '/index.php/Home/Goods/goods_detail/gid/'.$goodsList['GR_ID'];
		    		$cartData['msg'] = '购买的商品'.$goodsList['GR_Name'].'库存不足,请重新添加购物车';
    				$bool = false;
    				
	    		}
	    		
	    		if ((float)$priceTotalArray != (float)$option['FO_Price'] * $number * 0.01) {

	    			$cartData['code'] = 0;
		    		$cartData['url'] = '/index.php/Home/Goods/goods_detail/gid/'.$goodsList['GR_ID'];
		    		$cartData['msg'] = '购买的商品'.$v['name'].'价格有变,请重新提交订单';
    				$bool = false;

	    		}

				$guiGeGoods['gid'] = $goodsList['GR_ID']; // 商品id
				$guiGeGoods['name'] = $goodsList['name']; // 立即购买多规格商品名称
				$guiGeGoods['price'] = $option['FO_Price'] * 0.01; // 立即购买多规格商品价格
				$guiGeGoods['stock'] = $option['FO_Stock']; // 立即购买多规格商品库存
				$guiGeGoods['guiGe'] = str_replace('@#', '+', $option['FO_Name']); // 立即购买多规格组合名
				$guiGeGoods['number'] = $number; // 购买数量
				$guiGeGoods['img'] = $option['FO_IMG']; // 立即购买多规格商品图片
				$guiGeGoods['oid'] = $option['FO_ID']; // 规格ID
    			$cartList[] = $guiGeGoods;

    		} else { // 立即购买的无多规格区间
    			
	    		if ($goodsList['GR_Stock'] < $number) {

	    			$cartData['code'] = 0;
		    		$cartData['url'] = '/index.php/Home/Goods/goods_detail/gid/'.$goodsList['GR_ID'];
		    		$cartData['msg'] = '购买的商品'.$goodsList['name'].'库存不足,请重新添加购物车';
    				$bool = false;
    				
	    		}

    			if ((float)$priceTotalArray != (float)$goodsList['GR_Price'] * $number * 0.01) { // 总价对比

	    			$cartData['code'] = 0;
		    		$cartData['url'] = '/index.php/Home/Goods/goods_detail/gid/'.$goodsList['GR_ID'];
		    		$cartData['msg'] = '购买的商品'.$goodsList['name'].'价格有变,请重新加入购物车';
    				$bool = false;

	    		}
	    		
    			$wuGuiGeGoods['gid'] = $goodsList['GR_ID']; // 商品id
    			$wuGuiGeGoods['name'] = $goodsList['name']; // 立即购买无多规格名字
    			$wuGuiGeGoods['img'] = $goodsList['GR_IMG']; // 立即购买无多规格图片
    			$wuGuiGeGoods['number'] = $number; // 立即购买无多规格购买数量
    			$wuGuiGeGoods['price'] = $goodsList['GR_Price'] * 0.01; // 立即购买无多规格价钱

    			$cartList[] = $wuGuiGeGoods;
    		}
/////////////////////////////////////////立即点击进来的满减和邮费/////////////////////////////////////////////////////////
			$priceArray = $this->GetFullCut($priceTotalArray);// 检测本次交易可否包邮和满减
			
			if ($address) { // 如果地址存在则找邮费

				if ($priceArray['fullcut'] == -1) { // 表示不包邮，则去得邮费

					if ($fid) { // 有多规格则用多规格的weight

						$weight = $option['FO_Weight'] * $number;
					} else { // 没有则用goods表的weight

						$weight = $goodsList['GR_Weight'] * $number;
					}
		
			
					$youFei = $this->GetPostage($address['city'], $weight); // 得到邮费

					if ($youFei['state'] == -1) {  // 表示有错误

	    				$cartData['code'] =  0;
			    		$cartData['weight_price'] = $youFei['weight_price'];
			    		$cartData['msg'] = $youFei['msg'];
	    				$bool = false;
		    		}
 		
				} else { // 包邮, 则运费价格清空

					$youFei['weight_price'] = '0';
				}
			} else { // 未设置地址

				$address['code'] = 0;
				$youFei['weight_price'] = '0'; // 运费为0，因为没设置地址
			}

			if ($priceArray['full_recude'] != -1) { // 可以参加满减
				
				if ($priceTotalArray >= $priceArray['full_money']) { // 满足添加则把总价减去满足条件的价钱
					
					$manJianPrice['priceTotalArray'] = $priceTotalArray; // 小计
					$manJianPrice['price'] = $priceTotalArray - $priceArray['full_recude']; // 满减后的价钱
					$manJianPrice['full_money'] = $priceArray['full_money']; // 满多少
					$manJianPrice['full_recude'] = $priceArray['full_recude']; // 减了多少
		
				}
			} else {
				$manJianPrice['priceTotalArray'] = $priceTotalArray; // 小计
				$manJianPrice['price'] = $priceTotalArray;// 没满减,显示原来的小计
				$manJianPrice['full_money'] = 0; // 满多少
				$manJianPrice['full_recude'] = 0; // 减了多少
			}
		
			
///////////////////////////////////////////////立即点击进来的优惠券///////////////////////////////////////////////

			$couponPrice = $priceTotalArray - $manJianPrice['full_recude'];//满减完的价格
			$couponArray = $this->coupon($couponPrice);// 查看是否有优惠券
			
			if ($couponArray != -1) { // 有优惠券可以使用
				
				$this->assign('couponArrayJson', json_encode($couponArray));

			} else {
			    $wuCoupon = '0';
				$this->assign('couponArrayJson', json_encode($wuCoupon));

			}

		} else { // 如果不是从购物车或者立即购进来 区间

			$bool = false;
		}

		if ($bool) {

			$cartData['code'] = 1;
    		$cartData['msg'] = $cartData['msg'];
    		$cartData['url'] = $cartData['url'];
		} else {

			$cartData['code'] = 0;
    		$cartData['msg'] = $cartData['msg'];
    		$cartData['url'] = $cartData['url'];
		}
	
		$this->assign('youFeiJson', json_encode($youFei)); // 邮费
		$this->assign('manJianJson', json_encode($manJianPrice)); // 满减
		$this->assign('addressJson', json_encode($address)); // 默认地址
		$this->assign('cartDataJson', json_encode($cartData)); // 提示数据
		$this->assign('cartListJson', json_encode($cartList)); // 购物车或立即购买的数据

		$this->display();
	}
	/**
	 * 用户点击选择地址的时候触发 ajax请求
	 * 把地址表的id存进session
	 */
	public function putAddressToSession() {

		$this->initialize_first();
		if (IS_AJAX && IS_POST) {
			if ($_POST['addressId'] <= 0 || empty($_POST['addressId'])) {

				$data['code'] = 0;
				$data['msg'] = '更换地址失败';
				$this->ajaxReturn($data);
				exit;
			} else {

				$_SESSION['Home']['addressId'] = $_POST['addressId']; 

				$data['code'] = 1;
				$data['url'] = '/index.php/Home/Order/confirm_order';
				$data['msg'] = '更换地址成功';
				$this->ajaxReturn($data);
				exit;
			}

		}
	}
	/**
	 * 提交订单
	 */
	public function SureOrder() {

		if (IS_AJAX && IS_POST) {
			$userId = $_SESSION['Home']['userid'];
			$oid = I('oid'); // 规格id
			$gid = I('gid'); // 商品id
			$state = I('state'); // 1 为购物车  2为立即购买
			$cartIds = I('cartIds');//购物车id
			$addressId = I('addressId');//地址id
			$total = sprintf("%.2f",substr(sprintf("%.4f", I('price')), 0, -2)); // 实付价格,保留小数点后面两位小数，不四舍五入
			$message = I('message'); // 买家留言
			$id = I('id'); // 优惠券id
			$youHuiNum = I('youHuiNum');//优惠了多少钱
			
			if (empty($userId)) {// 判断登录

				$data['code'] = 0;
				$data['msg'] = '请先登录';
				$data['url'] = '/index.php/Home/User/Login/class/Order/method/confirm_order';
				$this->ajaxReturn($data);
				exit;
			}

			if (empty($addressId)) {

				$data['code'] = 0;
				$data['msg'] = '请选择地址';
				$this->ajaxReturn($data);
				exit;
			}

			$couponPushRec = M('CouponRec') // 优惠券表
				->where(array('CR_ID' => $id))
				->field('CR_Condition, CR_Minus')
				->find();

			if ((float)$couponPushRec['CR_Minus'] != (float)$youHuiNum) {

				$data['code'] = 0;
				$data['msg'] = '优惠券有变动，请重新选择';
				$data['url'] = '/index.php/Home/Order/confirm_order';
				$this->ajaxReturn($data);
				exit;
			}
			if ($id) { // 使用了优惠券

				$saveCouponPush['CP_State'] = 1; // 使用了
				$saveCouponPush['CP_Status'] = 0;// 失效了

				$couponPush = M('CouponPush')
					->where(array('CP_CID' => $id))
					->save($saveCouponPush);
			}
			$addressRec = M('AddressRec');// 地址表
			$addressList = $addressRec 
				->where(array('AR_ID' => $addressId))
				->field('AR_Link, AR_Phone, AR_Province, AR_City, AR_County, AR_Detail')
				->find();

///////////////////////////////////////////////////购物车点击提交订单按钮区间//////////////////////////////////////////////////

			if ($state == 1) { // 购物车
				if (empty($_SESSION['Home']['JieSuanState'])) {

					$data['code'] = 0;
					$data['msg'] = '请不要重复提交订单';
					$this->ajaxReturn($data);
					exit;
				}

				if (empty($cartIds)) {

					$data['code'] = 0;
					$data['msg'] = '提交的数据有误，请重新提交';
					$this->ajaxReturn($data);
					exit;
				}
				$joinCartId = join(',', $cartIds);
				$cartWhere['CR_ID'] = ['in', $joinCartId];

				$cartRec = M('CartRec');//购物车表
				$cartList = $cartRec
					->where($cartWhere)
					->field('CR_ID, CR_OID, CR_GID, CR_Number, CR_Weight, CR_Price')
					->select();

				$optionRec = M('FormatOption'); // 规格表
				$goodsRec = M('GoodsRec'); // 商品表

				foreach ($cartList as $K => $v) {
				
					$goodsWhere['GR_ID'] = $v['CR_GID']; // 商品id
		    		$goodsWhere['GR_Is_Show'] = 1;	// 开启的,上架的
					$goodsWhere['GR_Is_Delete'] = 0; // 未删除

		    		$goodsList = $goodsRec // 商品表
			    		->where($goodsWhere)
			    		->field('GR_ID, GR_Stock, GR_Price, GR_Weight, GR_Cost_Price')
			    		->find();

			    	if (empty($goodsList)) { // 删除购物车下架商品

			    		$cartRec->where(array('CR_ID' => $v))->delete();
			    		$data['code'] = 0;
			    		$data['msg'] = '购买的商品'.$v['name'].'已下架或删除';
			    		$data['url'] = '/index.php/Home/Order/confirm_order';
			    		$this->ajaxReturn($data);
			    		exit;
		    		}

					if ($v['CR_OID']) { // 购物车多规格
						$optionList = $optionRec
							->where(array('FO_ID' => $v['CR_OID']))
							->field('FO_Price, FO_Stock, FO_Weight, FO_Cost_Price')
							->find();

						$goodsList['GR_Cost_Price'] = $optionList['FO_Cost_Price'];// 存在多规格的替换原本的成本价

						if ($optionList['FO_Stock'] < $v['CR_Number']) {

			    			$data['code'] = 0;
				    		$data['url'] = '/index.php/Home/Cart/index';
				    		$data['msg'] = '购买的商品'.$v['name'].'库存不足,请重新添加购物车';
				    		$this->ajaxReturn($data);
				    		exit;
			    		}
			    		
				    	if ((int)$v['CR_Price'] != (int)$optionList['FO_Price']) {

			    			$cartRec->where(array('CR_ID' => $v['CR_ID']))->delete();//删除购物车表
			    			$data['code'] = 0;
				    		$data['url'] = '/index.php/Home/Cart/index';
				    		$data['msg'] = '购买的商品'.$v['name'].'价格有变,请重新提交订单';
				    		$this->ajaxReturn($data);
				    		exit;
			    		}


					} else { // 购物车无多规格

						if ((int)$v['CR_Price'] != (int)$goodsList['GR_Price']) {

			    			$cartRec->where(array('CR_ID' => $v['CR_ID']))->delete();//删除购物车表

			    			$data['code'] = 0;
				    		$data['url'] = '/index.php/Home/Cart/index';
				    		$data['msg'] = '购买的商品'.$v['name'].'价格有变,请重新加入购物车';
				    		$this->ajaxReturn($data);
				    		exit;
			    		}

			    		if ($goodsList['GR_Stock'] < $v['CR_Number']) { 

		    				$data['code'] = 0;
				    		$data['url'] = '/index.php/Home/Cart/index';
				    		$data['msg'] = '购买的商品'.$v['name'].'库存不足,请重新添加购物车';
				    		$this->ajaxReturn($data);
				    		exit;
			    		}
					}

					$weightTotal += $v['CR_Number'] * $v['CR_Weight']; // 获取单笔每个商品的总规格
					$priceTotal += $v['CR_Number'] * $v['CR_Price'] * 0.01; // 获取单笔每个商品的总价钱，不包含邮费和满减等优惠
					$Cost += $goodsList['GR_Cost_Price']; // 总成本价
					$profit += ($v['CR_Number'] * $v['CR_Price'] * 0.01) - ($v['CR_Number'] * $goodsList['GR_Cost_Price'] * 0.01); // 本次订单利润
				}

				
			
				$priceArray = $this->GetFullCut($priceTotal);// 检测本次交易可否包邮和满减

				if ($addressList) { // 如果地址存在则找邮费
			
					if ($priceArray['fullcut'] == -1) { // 表示不包邮，则去得邮费

						$youFei = $this->GetPostage($addressList['AR_City'], $weightTotal); // 得到邮费
					
						if ($youFei['state'] == -1) {  // 表示有错误

		    				$data['code'] =  0;
				    		$data['weight_price'] = $youFei['weight_price'];
				    		$data['msg'] = $youFei['msg'];

				    		$this->ajaxReturn($data);
				    		exit;
			    		}


			    		
					} else { // 包邮, 则运费价格清空

						$youFei['weight_price'] = '0';
						$baoyou = 3;
					}
				} else { // 未设置地址

					$address['code'] = 0;
					$youFei['weight_price'] = 0; // 运费为0，因为没设置地址
				}
				

				if ($priceArray['full_recude'] != -1) { // 可以参加满减
					
					if ($priceTotal >= $priceArray['full_money']) { // 满足添加则把总价减去满足条件的价钱

						$manJianPrice['price'] = $priceTotal - $priceArray['full_recude']; // 满减后的价钱
						$manJianPrice['full_recude'] = $priceArray['full_recude']; // 减了多少
						
					}
				} else {

					$manJianPrice['price'] = 0; // 没满减
					$manJianPrice['full_recude'] = 0; // 减了多少
				}

				/**
					OrderRec表的数据
				 */
				$orderRec = M('OrderRec');
				$orderGoodsRec = M('OrderGoods');
				$formatOptionRec = M('FormatOption');
				$goodsRec = M('GoodsRec');
				$cartRec2 = M('CartRec');
				$orderRec->startTrans(); // 开启事务

				$priceOrder = $priceTotal + $youFei['weight_price'] - $manJianPrice['full_recude'] - $youHuiNum;// 订单总价(商品总价 + 邮费 - 满减 - 优惠券的钱)

				$addOrder['OR_OrderTotal'] = sprintf("%.2f", substr(sprintf("%.4f", $priceOrder), 0, -2)); // 保留小数点后面两位小数，不四舍五入
				
				if ((float)$addOrder['OR_OrderTotal'] != (float)$total) {

					$data['code'] =  0;
		    		$data['msg'] = '价格有变,请重新提交订单';
		    		$data['url'] = '/index.php/Home/Order/confirm_order';
		    		$this->ajaxReturn($data);
		    		exit;
				}

				$addOrder['OR_UID'] = $userId; // 用户id
				$addOrder['OR_Umobile'] = $_SESSION['Home']['umobile']; //  下单人手机号
				$addOrder['OR_Key'] = 'YK'.time().mt_rand(10000000, 99999999); // 订单号
				$addOrder['OR_GoodsPrice'] = $priceTotal;// 商品总价（不包含邮费,优惠)
				$addOrder['OR_ShiJiPay'] = $total;// 实付价格
				$addOrder['OR_YouFei'] =  $youFei['weight_price'];// 邮费
				$addOrder['OR_GoodsCost'] = $Cost;// 商品成本价
				$addOrder['OR_Profit'] = $profit;// 本次订单利润 售价 - 成本价 = 利润
				$addOrder['OR_State'] = 0; // 订单状态:待支付
				$addOrder['OR_DeleteState'] = 0;//0 表示没有放进回收站
				$addOrder['OR_CreateTime'] = time();// 订单创建时间
				$addOrder['OR_Province'] = $addressList['AR_Province'];// 省
				$addOrder['OR_City'] = $addressList['AR_City'];//  市 
				$addOrder['OR_County'] = $addressList['AR_County'];// 区县 
				$addOrder['OR_Detail'] = $addressList['AR_Detail'];// 详细地址
				$addOrder['OR_Link'] = $addressList['AR_Link'];// 联系人
				$addOrder['OR_Phone'] = $addressList['AR_Phone'];// 联系电话
				$addOrder['OR_OrderType'] = 1;// 1 购物车下单 2立即购买下单
				$addOrder['OR_Administrators'] = 1;// 是否被后台管理员阅读过
				$addOrder['OR_Message'] = $message; // 买家留言

				$addOrderLastId = $orderRec // 订单表
				    ->add($addOrder);

				$bool = true; // 标记
				if (!$addOrderLastId) {
					$bool = false;
				}
				/*
					OrderGoods表的数据
				*/
				$cartRec2 = M('CartRec');//购物车表
				$cartList2 = $cartRec2
					->where($cartWhere)
					->field('CR_ID, CR_OID, CR_GID, CR_Number, CR_Weight, CR_Price, CR_UID, CR_Guige, CR_Name, CR_Img')
					->select();

				foreach ($cartList2 as $k => $v) { // 购物车里的数据

					$addOrderGoods['OG_OID'] = $addOrderLastId;//关联订单表id
					$addOrderGoods['OG_UID'] = $v['CR_UID'];//用户id
					$addOrderGoods['OG_GID'] = $v['CR_GID'];//商品id
					$addOrderGoods['OG_Guige'] = $v['CR_Guige'];//组合规格名字
					$addOrderGoods['OG_Name'] = $v['CR_Name'];//商品名
					$addOrderGoods['OG_Img'] = $v['CR_Img'];//商品图片
					$addOrderGoods['OG_Price'] = $v['CR_Price'];//商品单价
					$addOrderGoods['OG_Number'] = $v['CR_Number'];//商品数量
					$addOrderGoods['OG_GuigeID'] = $v['CR_OID'];//规格id
					$addOrderGoods['OG_Addtime'] = time();//添加时间

					$addOrderGoodsLastId = $orderGoodsRec// 订单商品表
					    ->add($addOrderGoods);

					if (!$addOrderGoodsLastId) {
						$bool = false;
					}
				}

				// 减库存
				foreach ($cartList2 as $k => $v) {
					$delete[] = $v['CR_ID'];
					$orderGoodsList = $goodsRec// 商品表
					    ->where(array('GR_ID' => $v['CR_GID']))
					    ->field('GR_Less, GR_Stock')
					    ->find();

					if ($orderGoodsList['GR_Less'] == 1) { // 等于1表示下单减库存

						if ((int)$v['CR_OID'] < 0 || empty($v['CR_OID'])) { // 无多规格

							$saveGoods['GR_Stock'] = $orderGoodsList['GR_Stock'] - $v['CR_Number'];
							$saveBool[] = $goodsRec
							    ->where(array('GR_ID' => $v['CR_GID']))
							    ->save($saveGoods); // 购物车无多规格修改库存

						} else { // 有多规格

							$OrderoptionList = $formatOptionRec //规格表
								->where(array('FO_ID' => $v['CR_OID']))
								->field('FO_Stock, FO_ID')
								->find();
							if ($OrderoptionList) {

								$saveOptionList['FO_Stock'] = $OrderoptionList['FO_Stock'] - $v['CR_Number']; // 购物车多规格减库存
								$saveBool[] = $formatOptionRec
									->where(array('FO_ID' => $v['CR_OID']))
									->save($saveOptionList); // 购物车有多规格修改库存
							}
						}
					}
				}

				$inArraySave = in_array('0', $saveBool); // 查找数组中是否有修改库存失败的

				$deleteWhere['CR_ID'] = ['in', join(',', $delete)];
				$deleteBool = $cartRec2
				    ->where($deleteWhere)
				    ->delete();// 购物车无多规格删除购物车
			    
				if (!$deleteBool && $inArraySave == true || $saveBool == null) { //特别注意saveBool=null的情况是写错参数
					$bool = false;
				}
				
				if ($bool) { // 提交事务
					
					$orderRec->commit();
					// 删除session
					unset($_SESSION['Home']['crids']);// 删除购物车id
					unset($_SESSION['Home']['JieSuanState']); // 删除结算状态
					unset($_SESSION['Home']['priceTotal']); // 删除结算状态

					if (!empty($id)) {// 表示使用优惠券
					
						$addYouHuiQuan['DR_OID'] = $addOrderLastId;//订单id
						$addYouHuiQuan['DR_Money'] = $couponPushRec['CR_Condition'];//满多少
						$addYouHuiQuan['DR_Reduce'] = $couponPushRec['CR_Minus'];//减多少
						$addYouHuiQuan['DR_Type'] = 1;//1优惠券，2满减，3满额包邮
						$addDiscount = M('DiscountRec')->add($addYouHuiQuan);
					}

					if ($manJianPrice['price'] != 0) { // 表示使用满减

						$addManJianPrice['DR_OID'] = $addOrderLastId;//订单id
						$addManJianPrice['DR_Money'] = $priceArray['full_money'];//满多少
						$addManJianPrice['DR_Reduce'] = $priceArray['full_recude'];//减多少
						$addManJianPrice['DR_Type'] = 2;//1优惠券，2满减，3满额包邮
						$addDiscount = M('DiscountRec')->add($addManJianPrice);
					}


					if ($baoyou) { // 表示满额包邮

						$addManJianBaoYou['DR_OID'] = $addOrderLastId;//订单id
						$addManJianBaoYou['DR_Money'] = 0;//满多少
						$addManJianBaoYou['DR_Reduce'] = 0;//减多少
						$addManJianBaoYou['DR_Type'] = 3;//1优惠券，2满减，3满额包邮
						$addDiscount = M('DiscountRec')->add($addManJianBaoYou);
					} 

					$data['code'] = 1;
		    		$data['oid'] = $addOrderLastId;
		    		$data['type'] = 1;
		    		$data['msg'] = '提交订单成功';
		    		$this->ajaxReturn($data);
		    		exit;

				} else { // 回滚事务

					$orderRec->rollback();
				}

//////////////////////////////////////////////////////立即购买点击的提交订单区间///////////////////////////////////////////////////

			} elseif ($state == 2) { // 立即购买

				$priceTotal = $_SESSION['Home']['priceTotal'][0]; // 商品总价，不包含优惠和邮费
				$gid = $_POST['liJiGouMai'][0]['gid']; // 商品id
				$oid = $_POST['liJiGouMai'][0]['oid']; // 规格id
				$number = $_POST['liJiGouMai'][0]['number']; // 商品数量
				$message = $_POST['message'];
				// $oid = $_SESSION['Home']['liJiGouMaiList']['fid']; // 规格id
				// $number = $_SESSION['Home']['liJiGouMaiList']['number']; //购买数量
				// $gid = $_SESSION['Home']['liJiGouMaiList']['gid'];// 商品id

				if (empty($_SESSION['Home']['JieSuanState'])) {

					$data['code'] = 0;
					$data['msg'] = '请不要重复提交订单';
					$this->ajaxReturn($data);
					exit;
				}

				// total 实付价格
				$orderRec = M('OrderRec');
				$orderGoodsRec = M('OrderGoods');
				$formatOptionRec = M('FormatOption');
				$goodsRec = M('GoodsRec');
				$cartRec2 = M('CartRec');

				if (empty($gid)) {

					$data['code'] = 0;
					$data['msg'] = '提交数据有误，请重新提交';
					$this->ajaxReturn($data);
					exit;
				}

				$goodsWhere['GR_ID'] = $gid; // 商品id
	    		$goodsWhere['GR_Is_Show'] = 1;	// 开启的,上架的
				$goodsWhere['GR_Is_Delete'] = 0; // 未删除

	    		$goodsList = $goodsRec // 商品表
		    		->where($goodsWhere)
		    		->field('GR_ID, GR_Stock, GR_Price, GR_Weight, GR_Cost_Price, GR_Name, GR_IMG')
		    		->find();

		    	if (empty($goodsList)) { // 删除购物车下架商品

		    		$data['code'] = 0;
		    		$data['msg'] = '购买的商品'.$goodsList['GR_Name'].'已下架或删除';
		    		$data['url'] = '/index.php/Home/Order/confirm_order';
		    		$this->ajaxReturn($data);
		    		exit;
	    		}
				if ($oid) { // 多规格

					$optionList = $formatOptionRec
						->where(array('FO_ID' => $oid))
						->field('FO_Price, FO_Stock, FO_Weight, FO_Cost_Price, FO_Name')
						->find();

					$goodsOptionName = $optionList['FO_Name']; // 规格名

					$goodsList['GR_Cost_Price'] = $optionList['FO_Cost_Price'];// 存在多规格的替换原本的成本价

					if ($optionList['FO_Stock'] < $number) {

		    			$data['code'] = 0;
			    		$data['url'] = '/index.php/Home/Cart/index';
			    		$data['msg'] = '购买的商品'.$goodsList['GR_Name'].'库存不足,请重新添加购物车';
			    		$this->ajaxReturn($data);
			    		exit;
		    		}
		    		if ((float)$priceTotal != (float)$optionList['FO_Price'] * $number * 0.01) {

		    			$data['code'] = 0;
			    		$data['url'] = '/index.php/Home/Cart/index';
			    		$data['msg'] = '购买的商品'.$goodsList['GR_Name'].'价格有变,请重新提交订单';
			    		$this->ajaxReturn($data);
			    		exit;
		    		}

		    		$weightTotal = $optionList['FO_Weight'] * $number; // 立即购买 多规格的总重量
		    		$costPrice = $optionList['FO_Cost_Price'] * $number; // 立即购买 多规格的总成本价
		    		$profit = ($optionList['FO_Price'] * $number) - ($optionList['FO_Cost_Price'] * $number); // 利润
					$goodsPrice = $optionList['FO_Price'] * 0.01; // 商品单价，如有多规格则用多规格的单价
				} else { // 无多规格

					if ((float)$priceTotal != (float)$goodsList['GR_Price'] * $number * 0.01) {

		    			$data['code'] = 0;
			    		$data['url'] = '/index.php/Home/Cart/index';
			    		$data['msg'] = '购买的商品'.$goodsList['GR_Name'].'价格有变,请重新提交订单';
			    		$this->ajaxReturn($data);
			    		exit;
		    		}

		    		if ($goodsList['GR_Stock'] < $number) { 

	    				$data['code'] = 0;
			    		$data['url'] = '/index.php/Home/Cart/index';
			    		$data['msg'] = '购买的商品'.$v['name'].'库存不足,请重新添加购物车';
			    		$this->ajaxReturn($data);
			    		exit;
		    		}

		    		$weightTotal = $goodsList['GR_Weight'] * $number; // 立即购买 多规格的总重量
		    		$Cost = $goodsList['GR_Cost_Price'] * $number; // 立即购买 多规格的总成本价
		    		$profit = ($goodsList['GR_Price'] * $number) - ($goodsList['GR_Cost_Price'] * $number); // 利润
		    		$goodsPrice = $goodsList['GR_Price'] * 0.01; //商品单价，无多规格
				}

////////////////////////////////////从立即购买点击进来的检测包邮和满减区间//////////////////////////////////////////////////////

				$priceArray = $this->GetFullCut($priceTotal);// 检测本次交易可否包邮和满减
			   

				if ($addressList) { // 如果地址存在则找邮费
			 
					if ($priceArray['fullcut'] == -1) { // 表示不包邮，则去得邮费

						$youFei = $this->GetPostage($addressList['AR_City'], $weightTotal); // 得到邮费
					
						if ($youFei['state'] == -1) {  // 表示有错误

		    				$data['code'] =  0;
				    		$data['weight_price'] = $youFei['weight_price'];
				    		$data['msg'] = $youFei['msg'];


				    		$this->ajaxReturn($data);
				    		exit;
			    		}
						

					} else { // 包邮, 则运费价格清空

						$youFei['weight_price'] = '0';
						$baoyou = 3;
					}
				} else { // 未设置地址

					$address['code'] = 0;
					$youFei['weight_price'] = 0; // 运费为0，因为没设置地址
				}
				

				if ($priceArray['full_recude'] != -1) { // 可以参加满减
					
					if ($priceTotal >= $priceArray['full_money']) { // 满足添加则把总价减去满足条件的价钱

						$manJianPrice['price'] = $priceTotal - $priceArray['full_recude']; // 满减后的价钱
						$manJianPrice['full_recude'] = $priceArray['full_recude']; // 减了多少
			
					}
				} else {

					$manJianPrice['price'] = 0; // 没满减
					$manJianPrice['full_recude'] = 0; // 减了多少
				}

				/**
					OrderRec表的数据  立即购买
				 */
				$orderRec->startTrans(); // 开启事务
				$priceOrder = $priceTotal + $youFei['weight_price'] - $manJianPrice['full_recude'] - $youHuiNum;// 订单总价(商品总价 + 邮费 - 满减 - 优惠券)
				$addOrder['OR_OrderTotal'] = sprintf("%.2f", substr(sprintf("%.4f", $priceOrder), 0, -2)); // 保留小数点后面两位小数，不四舍五入
				
				if ((float)$addOrder['OR_OrderTotal'] != (float)$total) {

					$data['code'] =  0;
		    		$data['msg'] = '价格有变,请重新提交订单';
		    		$data['url'] = '/index.php/Home/Order/confirm_order';
		    		$this->ajaxReturn($data);
		    		exit;
				}
				$addOrder['OR_UID'] = $userId; // 用户id
				$addOrder['OR_Umobile'] = $_SESSION['Home']['umobile']; //  下单人手机号
				$addOrder['OR_Key'] = 'YK'.time().mt_rand(10000000, 99999999); // 订单号
				$addOrder['OR_GoodsPrice'] = $priceTotal;// 商品总价（不包含邮费,优惠)
				$addOrder['OR_ShiJiPay'] = $total;// 实付价格
				$addOrder['OR_YouFei'] =  $youFei['weight_price'];// 邮费
				$addOrder['OR_GoodsCost'] = $Cost;// 商品成本价
				$addOrder['OR_Profit'] = $profit;// 本次订单利润 售价 - 成本价 = 利润
				$addOrder['OR_State'] = 0; // 订单状态:待支付
				$addOrder['OR_DeleteState'] = 0;//0 表示没有放进回收站
				$addOrder['OR_CreateTime'] = time();// 订单创建时间
				$addOrder['OR_Province'] = $addressList['AR_Province'];// 省
				$addOrder['OR_City'] = $addressList['AR_City'];//  市 
				$addOrder['OR_County'] = $addressList['AR_County'];// 区县 
				$addOrder['OR_Detail'] = $addressList['AR_Detail'];// 详细地址
				$addOrder['OR_Link'] = $addressList['AR_Link'];// 联系人
				$addOrder['OR_Phone'] = $addressList['AR_Phone'];// 联系电话
				$addOrder['OR_OrderType'] = 1;// 1 购物车下单 2立即购买下单
				$addOrder['OR_Administrators'] = 1;// 是否被后台管理员阅读过
				$addOrder['OR_Message'] = $message; // 买家留言

				$addOrderLastId = $orderRec // 订单表
				    ->add($addOrder);

				$bool = true; // 标记

				if (!$addOrderLastId) {
					$bool = false;
				}
				/*
					OrderGoods表的数据
				*/
				$addOrderGoods['OG_OID'] = $addOrderLastId;//关联订单表id
				$addOrderGoods['OG_UID'] = $userId;//用户id
				$addOrderGoods['OG_GID'] = $gid;//商品id
				$addOrderGoods['OG_Guige'] = $goodsOptionName;//组合规格名字
				$addOrderGoods['OG_Name'] = $goodsList['GR_Name'];//商品名
				$addOrderGoods['OG_Img'] = $goodsList['GR_IMG'];//商品图片
				$addOrderGoods['OG_Price'] = $goodsPrice * 100;//商品单价
				$addOrderGoods['OG_Number'] = $number;//商品数量
				$addOrderGoods['OG_GuigeID'] = $oid;//规格id
				$addOrderGoods['OG_Addtime'] = time();//添加时间

				$addOrderGoodsLastId = $orderGoodsRec// 订单商品表
				    ->add($addOrderGoods);

				if (!$addOrderGoodsLastId) {
					$bool = false;
				}

				// 减库存
				$orderGoodsList = $goodsRec// 商品表
				    ->where(array('GR_ID' => $gid))
				    ->field('GR_Less, GR_Stock')
				    ->find();

				if ($orderGoodsList['GR_Less'] == 1) { // 等于1表示下单减库存

					if ((int)$oid < 0 || empty($oid)) { // 无多规格

						$saveGoods['GR_Stock'] = $orderGoodsList['GR_Stock'] - $number;
						$saveBool[] = $goodsRec
						    ->where(array('GR_ID' => $gid))
						    ->save($saveGoods); // 购物车无多规格修改库存

					} else { // 有多规格

						$OrderoptionList = $formatOptionRec //规格表
							->where(array('FO_ID' => $oid))
							->field('FO_Stock, FO_ID')
							->find();

						if ($OrderoptionList) {
							$saveOptionList['FO_Stock'] = $OrderoptionList['FO_Stock'] - $number; // 购物车多规格减库存
							$saveBool[] = $formatOptionRec
								->where(array('FO_ID' => $oid))
								->save($saveOptionList); // 购物车有多规格修改库存
						}
					}
					$inArraySave = in_array('0', $saveBool); // 查找数组中是否有修改库存失败的

					
					if ($inArraySave == true || $saveBool == null) { //特别注意saveBool=null的情况是写错参数
						$bool = false;
					}

					if ($bool) { // 提交事务
						
						$orderRec->commit();
						// 删除session
						unset($_SESSION['Home']['crids']);// 删除购物车id
						unset($_SESSION['Home']['JieSuanState']); // 删除结算状态
						unset($_SESSION['Home']['priceTotal']); // 删除结算状态
						unset($_SESSION['Home']['liJiGouMaiList']); // 删除立即购买的数据


					if (!empty($id)) {// 表示使用优惠券
					
						$addYouHuiQuan['DR_OID'] = $addOrderLastId;//订单id
						$addYouHuiQuan['DR_Money'] = $couponPushRec['CR_Condition'];//满多少
						$addYouHuiQuan['DR_Reduce'] = $couponPushRec['CR_Minus'];//减多少
						$addYouHuiQuan['DR_Type'] = 1;//1优惠券，2满减，3满额包邮
						$addDiscount = M('DiscountRec')->add($addYouHuiQuan);
					}
						
					if ($manJianPrice['price'] != 0) { // 表示使用满减
						
						$addManJianPrice['DR_OID'] = $addOrderLastId;//订单id
						$addManJianPrice['DR_Money'] = $priceArray['full_money'];//满多少
						$addManJianPrice['DR_Reduce'] = $priceArray['full_recude'];//减多少
						$addManJianPrice['DR_Type'] = 2;//1优惠券，2满减，3满额包邮
						$addDiscount = M('DiscountRec')->add($addManJianPrice);
					}

					if ($baoyou) { // 表示满额包邮

						$addManJianBaoYou['DR_OID'] = $addOrderLastId;//订单id
						$addManJianBaoYou['DR_Money'] = 0;//满多少
						$addManJianBaoYou['DR_Reduce'] = 0;//减多少
						$addManJianBaoYou['DR_Type'] = 3;//1优惠券，2满减，3满额包邮
						$addDiscount = M('DiscountRec')->add($addManJianBaoYou);
					} 

						$data['code'] = 1;
			    		$data['oid'] = $addOrderLastId;
			    		$data['type'] = 1;
			    		$data['msg'] = '提交订单成功';
			    		$this->ajaxReturn($data);
			    		exit;

					} else { // 回滚事务
						$orderRec->rollback();
					}
				}
			}
		}
	}

	/**
	 * Notes:计算邮费
	 * Author: AndyZhang
	 * Date: 2018/1/23 上午10:21
	 * @param $city城市
	 * @param int $weight重量
	 * @return mixed若flag['state'] =-1;则提示flag['msg'];flag['state']=1时，得到邮费为flag['weight_price']
	 */
	public function GetPostage($city,$weight=0){
		// dump($weight);
        $postage = $this->show($city);
        $go_weight = $weight;
        // dump($postage);
        if($postage){
        	if($postage == -2){
            	$flag['state'] = -1;
            	$flag['msg'] = '该地址不在配送范围内,请重新选择';
                $flag['weight_price'] = 0.00;
            }else{
            	if($go_weight == 0){
	            	$flag['state'] = 1;
	                $flag['weight_price'] = 0.00;
	            }else{
	                $go_weight = floor($go_weight*100)/100;
	                $first_weight = floor($postage['PR_Firstweight']*100)/100; //首重
	                if($go_weight <= $first_weight){
	                	$flag['state'] = 1;
	                    $flag['weight_price'] = floor($postage['PR_Firstprice']*100)/100;
	                }else{
	                    $first_price = floor($postage['PR_Firstprice']*100)/100; //首重价格
	                    $second_weight = $go_weight - $first_weight; //超出重量
	                    $system_second_weight = floor($postage['PR_Secondweight']*100)/100;
	                    $second_price = (ceil($second_weight/$system_second_weight))*floor($postage['PR_Secondprice']*100)/100;//超出价格
	                   	$flag['state'] = 1;
	                    $flag['weight_price'] = $first_price + $second_price; //邮费 
	                }
	            } 
            }
        }else{
        	$flag['state'] = -1;
        	$flag['weight_price'] = 0.00;
        	$flag['msg'] = '地址查询错误,请稍后重试';
        }
        return $flag;
	}

	/**
	 * Notes:计算邮费
	 * Author: AndyZhang
	 * Date: 2018/1/23 上午10:20
	 * @param $city
	 * @return int|mixed
	 */
    public function show($city){
        $postage = M('PostageRec');
        // 查不配送
        $no_where['PR_Random'] = array('exp', 'is NULL');
        $no_where['PR_Sort'] = 0;
        $no_where['PR_Dispatchareas'] = 1;
        $no_res = $postage->where($no_where)->find();
        // 查默认区域
        $default_where['PR_Random'] = array('exp', 'is NULL');
        $default_where['PR_Sort'] = 1;
        $default_where['PR_Dispatchareas'] = 0;
        $default_res = $postage->where($default_where)->find();
        // 配送区域
        $where['PR_Random'] = array('exp', 'is not NULL');
        $where['PR_Dispatchareas'] = 0;
        $res = $postage->where($where)->order('PR_Sort desc')->select();
        
        //判断是否在不配送区域内 
        if($no_res['PR_City']){
            $no_res['PR_City'] = explode(';',$no_res['PR_City']);
            if(in_array($city, $no_res['PR_City'])){
                // 不在配送区域
               return -2;
            }else{
                // 查配送
                if($res){
                    foreach($res as $key=>$val){
                        $val['PR_City'] = explode(';',$val['PR_City']);
                        if(in_array($city, $val['PR_City'])){
                            return $res[$key];
                        }
                    }
                    // 选择区域不在配送区域内
                    return $default_res;
                }else{
                    return $default_res;
                }
            }
        }else{
            if($res){
                foreach($res as $key=>$val){
                    $val['PR_City'] = explode(';',$val['PR_City']);
                    if(in_array($city, $val['PR_City'])){
                        return $res[$key];
                    }
                }
                // 选择区域不在配送区域内
                return $default_res;
            }else{
               return $default_res;
            }
        }
    }  

	/**
	 * Notes:获取系统中可以领取的优惠券，并且自己未曾得到
	 * Author: AndyZhang
	 * Date: 2018/1/23 上午10:18
	 * @return int 返回-1表示没有可领取的优惠券
	 */
    public function GetCouponList($price){
    	$co = M('coupon_rec');
    	$push = M('coupon_push');

    	//系统中符合要求的优惠券
    	$where['CR_State'] = 1;
    	$where['CR_ISDelete'] = 1;
    	$where['CR_ISget'] = 1;
    	$where['CR_StartTime'] = array('elt',time());
    	$where['CR_EndTime'] = array('egt',time());
    	$where['CR_Condition'] = array('elt',$price);
    	$sys_res = $co->field('CR_ID as id')->where($where)->select();
    	// $sys_ids = array_column($sys_res, 'id');
    	foreach($sys_res as $k => $v){
			$sys_ids[] = $v['id'];
		}
    	//自己得到的并且是未使用的,&是未失效的
    	$us_where['CP_UID']  = (int)$_SESSION['Home']['userid'];
    	$us_where['CP_State'] = 0;//未使用
    	$us_where['CP_Status'] = 1;//未失效

    	$user_res = $push->field('CP_CID as cpid')->where($us_where)->select();
    	// $get_ids = array_column($user_res, 'cpid');
    	foreach($user_res as $k => $v){
			$get_ids[] = $v['cpid'];
		}

    	if($sys_ids && $get_ids){
    		foreach ($sys_ids as $key => $value) {
    			if(in_array($value, $get_ids)){
    				unset($sys_ids[$key]);
    			}
    		}
    	}
    	if($sys_ids){
    		$co_where['CR_ID'] = array('in',$sys_ids);
	    	$co_res = $co->field('CR_ID as id, CR_Name as name, CR_Sort as sort, CR_Minus as minus')->where($co_where)->order('CR_Sort desc')->select();
	    	if(!$co_res){
	    		$co_res = -1;
	    	}
    	}else{
    		$co_res = -1;
    	}
    	
    	return $co_res;
    }

    /**
     * Notes:可以使用的优惠券
     * Author: AndyZhang
     * Date: 2018/1/23 下午5:28
     */
	public function coupon(){
		$co = M('coupon_rec');
		$push = M('coupon_push');
		//个人所有可用优惠券
		$where['CP_UID'] = (int)$_SESSION['Home']['userid'];
		$where['CP_State'] = 0;//未使用
		$where['CP_Status'] = 1;//未失效
		$push_res = $push->field('CP_CID as cpid')->where($where)->select();
		foreach ($push_res as $k=>$v) {
			$push_ids[] = $v['cpid'];
		}
//		dump($push_ids);
		//系统不可用优惠券
		$co_where['CR_State'] = 0;
		$co_where['CR_ISDelete'] = 0;
		$co_where['_logic'] = 'OR';
		$co_res = $co->field('CR_ID as crid')->where($co_where)->select();
		foreach ($co_res as $k=>$v) {
			$co_ids[] = $v['crid'];
		}
//		dump($co_ids);
		if($push_ids && $co_ids){
			foreach ($push_ids as $key => $value) {
				if(in_array($value, $co_ids)){
					unset($push_ids[$key]);
				}
			}
		}
		//得到可用的优惠券ID
		$arr = array();
		if($push_ids){
			$get_where['CR_ID'] = array('in',$push_ids);
			$get_res = $co->field('CR_ID as id, CR_Name as name, CR_Sort as sort, CR_Minus as minus, CR_Condition as man')->where($get_where)->order('CR_Sort desc')->select();
			if(!$get_res){
                $get_res = -1;
			}else{
                foreach ($get_res as $k=>$v) {
                    $get_res[$k]['status'] = 1;
                }
            }
		}else{
            $get_res = -1;
		}
        $arr = $get_res;
        return $arr;
	}
	/**
	 * Notes:检查是否有包邮设置和满额设置
	 * Author: AndyZhang
	 * Date: 2018/1/23 上午11:03
	 * @param $goodstotal参数商品总价（不计邮费)
	 * @return array,arr['fullcut']=1表示可包邮，-1为不包邮，arr['fuu_recude']表示满减金额，为-1时表示不可满减,arr['full_money']表示满足多少钱
	 */
    public function GetFullCut($goodstotal){
		$sys = M('system_rec');
		$full = M('full_reduce');
		$sys_res = $sys->field('SR_FreeShipping,SR_FullToPostage')->where(array('SR_ID'=>1))->find();
		$arr = array();
		if((int)$sys_res['SR_FreeShipping'] == 1){
			//包邮
			if((float)$sys_res['SR_FullToPostage'] <= 0){
				//全场包邮，不限制
				$arr['fullcut'] = 1;
			}else{
				if((float)$goodstotal >= (float)$sys_res['SR_FullToPostage']){//包邮
					$arr['fullcut'] = 1;
				}else{
					$arr['fullcut'] = -1;
				}
			}
		}else{
			$arr['fullcut'] = -1;
		}

		//满额减
		$full_res = $full->order('FR_FullMoney desc')->select();
		foreach ($full_res as $k=>$v) {
			if((float)$goodstotal >= (float)$v['FR_FullMoney']){
				$reduce = (float)$v['FR_ReduceMoney'];
				$money = (float)$v['FR_FullMoney'];
				$flag = 1;
				break;
			}else{
				$flag = -1;
			}
		}
		if($flag != -1){
			$arr['full_recude'] = $reduce;
			$arr['full_money'] = $money;
		}else{
			$arr['full_recude'] = -1;
		}
		return $arr;
    }




}