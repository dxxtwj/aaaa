<?php
namespace Home\Controller;
use Think\Controller;
use \Redis;

header("Content-type: text/html; charset=utf-8");
class CartController extends HomeController {

	 /**
     * 显示购物车
     * 两种情况： 1 登录  2未登录
     * 登录把cookie的购物车数据加进数据库再查询数据库取出购物车商品
     * 未登录直接查询cookie遍历
     */
    public function index() {
			
    	if ($_SESSION['Home']['userid']) {
    		
    		if (cookie('cart')) { // 如cookie存在并且用户是登录状态，添加cookie商品进数据库
	    		$car = M('CartRec');
				$cartList = unserialize(cookie('cart'));
	    		$cartWhere['CR_UID'] = $_SESSION['Home']['userid'];
				foreach ($cartList as $k => $v) {

					if ($v['oid']) {
		            	$cartWhere['CR_OID'] = $v['oid'];  
					}
		            $cartWhere['CR_GID'] = $v['goods_id'];
		            $cartData = $car->where($cartWhere)->find();
					if($cartData){ // 查到有相同则添加数量
						
	        			$data['CR_Number'] = $v['num'] + $cartData['CR_Number'];
		            	$saveCart = $car->where($cartWhere)->save($data);

		            	if ($saveCart) { // 添加完数量情况cookie
		            		cookie('cart', null);
		            	}

	        		} else {// 不相同为新商品，则添加新的商品数据进购物车表

	        			$cartArray['CR_UID'] = $_SESSION['Home']['userid']; // 用户id
	        			if ($cartList[$k]['oid']) {

			    			$cartArray['CR_OID'] = $cartList[$k]['oid']; // 规格id
	        			}
			    		$cartArray['CR_GID'] = $cartList[$k]['goods_id']; // 商品id
			    		$cartArray['CR_Number'] = $cartList[$k]['num']; // 购物该商品的数量
			    		$cartArray['CR_Price'] = $cartList[$k]['price']; // 购买的价格
			    		$cartArray['CR_Weight'] = $cartList[$k]['weight']; // 重量
			    		$cartArray['CR_Guige'] = $cartList[$k]['selected'] ? $cartList[$k]['selected'] : null; // 规格名
			    		$cartArray['CR_Name'] = $cartList[$k]['mes']; // 商品名
			    		$cartArray['CR_Img'] = $cartList[$k]['img']; // 图片
			    		$cartArray['CR_Addtime'] = time(); // 添加时间
			    		

			    		$cartAdd = M('CartRec')->add($cartArray);
			    		
			    		if ($cartAdd) { // 添加完后删除 cookie
			    			cookie('cart', null);
			    		}
	        		}
    			}exit;
			}
			
			$cartModel = M('CartRec'); // 购物车表
			$cartArray = $cartModel
				->where(array('CR_UID' => $_SESSION['Home']['userid']))
				->field('CR_GID')
				->select();

			$gr_where['GR_Is_Show'] = 1;
	    	$gr_where['GR_Is_Delete'] = 0;
	    	$gr_where['GR_ID'] = $cartArray['CR_GID'];

			$goodsRec = M('GoodsRec')
				->field('GR_ID')
				->where($gr_where)
				->find();

			if (empty($goodsRec)) { 

				$cartModel->where(array('CR_GID' => $goodsRec['GR_ID'], 'CR_UID' => $_SESSION['Home']['userid']))->delete(); //删除下架的商品购物车
			}

			$cartModel = M('CartRec'); // 购物车表
			$cartList = $cartModel
				->where(array('CR_UID' => $_SESSION['Home']['userid']))
				->field('CR_Number as num, CR_Price as price, CR_Guige as selected, CR_Name as mes, CR_Img as img, CR_GID as goods_id, CR_ID as id, CR_Weight as weight')
				->select();
			
			foreach ($cartList as $k => $v) { // 更改现价
				$cartList[$k]['selected'] = str_replace('@#', '+', $cartList[$k]['selected']); // 处理数据
				$cartList[$k]['price'] = $v['price'] * 0.01;
			}

			$login['login'] = true; // 标记为登录了

    	} else { // 没登录查询cookie

			$cartList = unserialize(cookie('cart'));
			foreach ($cartList as $k => $v) { // 更改现价
				$cartList[$k]['selected'] = str_replace('@#', '+', $cartList[$k]['selected']); // 处理数据
				$cartList[$k]['price'] = $v['price'] * 0.01;
			}

			$login['login'] = false; // 标记为登录了
    	}

		$this->assign('cart_json', json_encode($cartList));
		$this->assign('login_json', json_encode($login));
    	$this->display();
    }

    /**
     * 购物车加减
     */
    public function Add_Subtract() {

    	$subId = I('subId'); // 登录传购物车id 没登录传商品id
    	$oid = I('oid'); // 规格id
    	$number = I('number'); // 增加或减少数量

    	if ($_SESSION['Home']['userid']) { // 已登录 去购物车

    		$cartRec = M('CartRec');

			$symbol['CR_Number'] = (int)$number;
			$cartRec->where(array('CR_ID' => $subId))->save($symbol);
    	
    	} else { // 未登录 去cookie

    		if ($oid != 'null') { // 对规格区域,gid拼接规格id

    			$key = $subId.'@#'.$oid;

    		} else { // 不是多规格区域,拼接gid

    			$key = 'wuGuiGe@#gid'.$subId;

    		}
    		
    		$cookieCart = unserialize(cookie('cart'));

			$cookieCart[$key]['num'] = $number;
    		$cookieCartNum = serialize($cookieCart);
    		cookie('cart', $cookieCartNum, 604800); // 覆盖
    	}
    }

	/**
	 * 删除购物车
	 * 登录删除购物车表，未登录删除cookie
	 * 购物车需要传购物车id
	 * cookie会有两种情况
	 * 1： 有规格  2： 无规格
	 * 有规格键名要求 规格ID和商品ID拼接(前台拼接好传来)
	 * 无规格键名要求 只需要商品ID
	 */
	public function cart_delete() {
		
		if (IS_AJAX && IS_GET) {

			if ($_SESSION['Home']['userid']) { // 登录则删除购物车表

				foreach ($_GET['id'] as $k => $v) {
					$crIds[] = $v; // 购物车id 用in语法一条语句搞掂
				}

				$joinCrIds = join(',', $crIds);
				$deleteArray = array('CR_ID' => ['in', $joinCrIds]);
				$deleteCart = M('CartRec') // 购物车表
					->where($deleteArray)
					->delete();

				if (!empty($deleteCart)) { 

					$cartData['code'] = 1;
		    		$cartData['msg'] = '删除购物车成功';
		    		$this->ajaxReturn($cartData);
		    		exit;

				} else {

					$cartData['code'] = 0;
		    		$cartData['msg'] = '删除购物车失败';
		    		$this->ajaxReturn($cartData);
		    		exit;
				}

			} else { // 没登录删除cookie 需要规格id和商品id拼接
				
				$cookie = unserialize(cookie('cart'));
				
				foreach ($_GET['id'] as $k => $v) { // 遍历删除购物车
					
					if (strpos($v, '@#')) { // 多规格的键

						$key[] = $v;

					} else { // 无规格的键

						$key2[] = $v;
					}
				}
				foreach ($key as $v) { // 有规格删除区域

					unset($cookie[$v]);
				}

				foreach ($key2 as $val) {// 无规格删除区域

					unset($cookie['wuGuiGe@#gid'.$val]);

				}

				$cookieAlize = serialize($cookie); 
				cookie('cart', $cookieAlize, 345600); // 覆盖之前的，存储四天
			}
		}
	}
	
    /**
     * 结算方法
     */
    public function finish() {
    	
  	  	if (empty($_SESSION['Home']['userid']) || $_SESSION['Home']['login'] != true) {

    		$cartData['code'] = 0;
    		$cartData['url'] = '/index.php/Home/User/Login/class/Cart/method/index';
    		$cartData['msg'] = '请先登录再进行结算,2秒后自动跳转至登录页';
    		$this->ajaxReturn($cartData);
    		exit;
    	}
    	if (IS_AJAX && IS_POST) {

			$cartRec = M('CartRec'); // 购物车表
			$goodsRec = M('GoodsRec'); // 商品表
			$formatOptionRec = M('FormatOption'); // 规格表

	    	if ($_POST['state'] == 1) { // 购物车点击来的

		    	if (empty($_POST['crId'])) {
		    		$cartData['code'] = 0;
		    		$cartData['msg'] = '请选择商品';
		    		$this->ajaxReturn($cartData);
		    		exit;
		    	}

	    		foreach ($_POST['crId'] as $k => $v) {

		    		$cartList = $cartRec // 购物车表
		    			->where(array('CR_ID' => $v))
		    			->field('CR_ID, CR_Price, CR_OID, CR_GID, CR_Number, CR_Name')
		    			->find();

		    		if (empty($cartList)) {

		    			$cartData['code'] = 0;
		    			$cartData['crName'] = $cartList['CR_Name'];
			    		$cartData['msg'] = '数据丢失,请重新加入购物车';
			    		$this->ajaxReturn($cartData);
			    		exit;
		    		}
		    		
		    		$goodsWhere['GR_ID'] = $cartList['CR_GID']; // 商品id
		    		$goodsWhere['GR_Is_Show'] = 1;	// 开启的,上架的
					$goodsWhere['GR_Is_Delete'] = 0; // 未删除

		    		$goodsList = $goodsRec // 商品表
			    		->where($goodsWhere)
			    		->field('GR_ID, GR_Stock, GR_Price')
			    		->find();
			    	if (empty($goodsList)) { // 删除购物车下架商品

			    		$cartRec->where(array('CR_ID' => $v))->delete();

		    			$cartData['code'] = 0;
		    			$cartData['crName'] = $cartList['CR_Name'];
			    		$cartData['msg'] = '已下架或删除';
			    		$this->ajaxReturn($cartData);
			    		exit;
		    		}
		    		if (!empty($cartList['CR_OID'])) { // 存在多规格判断库存和价格

		    			$option = $formatOptionRec // 规格表
			    			->where(array('FO_ID' => $cartList['CR_OID']))
			    			->field('FO_Stock, FO_Price')
			    			->find();

			    		if ($option['FO_Stock'] < $cartList['CR_Number']) {

			    			$cartData['code'] = 0;
			    			$cartData['crName'] = $cartList['CR_Name'];
				    		$cartData['msg'] = '库存不足';
				    		$this->ajaxReturn($cartData);
				    		exit;
			    		}
			    		if ((int)$cartList['CR_Price'] != (int)$option['FO_Price']) {

			    			$cartRec->where(array('CR_ID' => $v))->delete();//删除购物车表

			    			$cartData['code'] = 0;
			    			$cartData['crName'] = $cartList['CR_Name'];
				    		$cartData['msg'] = '该商品价格有变,请重新加入购物车';
				    		$this->ajaxReturn($cartData);
				    		exit;

			    		} else {
			    			$price[] = $cartList['CR_Price'] * $cartList['CR_Number'] * 0.01; // 购物车存在多规格的所有商品的总价
			    			$_SESSION['Home']['priceTotal'] = $price;
			    		}

		    		} else { // 不存在多规格判断库存和价格

			    		if ((int)$cartList['CR_Price'] != (int)$goodsList['GR_Price']) {

				    			$cartRec->where(array('CR_ID' => $v))->delete();//删除购物车表

				    			$cartData['code'] = 0;
				    			$cartData['crName'] = $cartList['CR_Name'];
					    		$cartData['msg'] = '该商品价格有变,请重新加入购物车';
					    		$this->ajaxReturn($cartData);
					    		exit;
			    		} else {

			    			$price[] = $cartList['CR_Price'] * $cartList['CR_Number'] * 0.01; // 购物车不存在多规格的总价
			    			$_SESSION['Home']['priceTotal'] = $price;
			    		}

			    		if ($goodsList['GR_Stock'] < $cartList['CR_Number']) { 

		    				$cartData['code'] = 0;
		    				$cartData['crName'] = $cartList['CR_Name'];
				    		$cartData['msg'] = '库存不足';
				    		$this->ajaxReturn($cartData);
				    		exit;
			    		}
		    		}
	    		}

	    		// 如果都通过则存进session并跳转
	    		$cartData['code'] = 1;
	    		$cartData['url'] = '/index.php/Home/Order/confirm_order';
	    		$cartData['msg'] = '结算判断通过,即将跳转到提交订单页';
	    		$_SESSION['Home']['crids'] = $_POST['crId']; // 购物车id
				$_SESSION['Home']['JieSuanState'] = 1; // 购物车

	    		$this->ajaxReturn($cartData);
	    		exit;

	    	} elseif ($_POST['state'] == 2) { // 立即购买点击来的

	    		if ($_POST['gid'] <= 0) {

	    			$cartData['code'] = 0;
		    		$cartData['msg'] = '请确认您要买的商品';
		    		$this->ajaxReturn($cartData);
		    		exit;
	    		}

	    		if ($_POST['number'] <= 0) {

	    			$cartData['code'] = 0;
		    		$cartData['msg'] = '请选择数量';
		    		$this->ajaxReturn($cartData);
		    		exit;
	    		}

	    		if (!empty($_POST['fo_id'])) { // 多规格商品

	    			$formatOption = $formatOptionRec // 规格表
		    			->where(array('FO_ID' => $_POST['fo_id']))
		    			->field('FO_Price, FO_Stock, FO_ID')
		    			->find();
		    		$formatOption['FO_Price'] = $formatOption['FO_Price'] * 0.01; // 现价
		    		if ($formatOption['FO_Stock'] < $_POST['number']) {

	    				$cartData['code'] = 0;
			    		$cartData['msg'] = '库存不足';
			    		$this->ajaxReturn($cartData);
			    		exit;
		    		}
		    		if ((int)$formatOption['FO_Price'] != (int)$_POST['price']) {

		    			$cartData['code'] = 0;
		    			$cartData['url'] = '/index.php/Home/Goods/goods_detail/gid'.$goodsList['GR_ID'];
			    		$cartData['msg'] = '该商品的价格有变动,请重新选择';
			    		$this->ajaxReturn($cartData);
			    		exit;

		    		} else {
		    			$price[] = $_POST['number'] * $formatOption['FO_Price'];
		    			$_SESSION['Home']['priceTotal'] = $price; // 立即购买的多规格商品总价
		    		}

		    		// 如果判断都通过则返回
		    		$cartData['code'] = 1;
		    		$cartData['url'] = '/index.php/Home/Order/confirm_order';
		    		$cartData['msg'] = '立即购买判断通过,即将跳转到提交订单页';
		    		$_SESSION['Home']['liJiGouMaiList']['number'] = $_POST['number'];
					$_SESSION['Home']['liJiGouMaiList']['gid'] = $_POST['gid'];

					if ($formatOption['FO_ID']) { // 存在多规格则把id存进session
						$_SESSION['Home']['liJiGouMaiList']['fid'] = $formatOption['FO_ID'];
					}
					$_SESSION['Home']['JieSuanState'] = 2; // 立即购买

		    		$this->ajaxReturn($cartData);
		    		exit;

	    		} else { // 无多规格区域

		    		if ($_POST['gid'] <= 0) {

		    			$cartData['code'] = 0;
			    		$cartData['msg'] = '请确认您要买的商品';
			    		$this->ajaxReturn($cartData);
			    		exit;
		    		}
		    		if ($_POST['number'] <= 0) {

		    			$cartData['code'] = 0;
			    		$cartData['msg'] = '请选择数量';
			    		$this->ajaxReturn($cartData);
			    		exit;
	    			}

	    			$goodsList = M('GoodsRec') // 商品表
	    				->where(array('GR_ID' => $_POST['gid']))
	    				->field('GR_Price, GR_Stock')
	    				->find();

	    			$goodsList['GR_Price'] = $goodsList['GR_Price'] * 0.01;
	    			
	    			if ((int)$goodsList['GR_Price'] != (int)$_POST['price']) {

		    			$cartData['code'] = 0;
			    		$cartData['msg'] = '该商品价格有变,请重新购买';
			    		$this->ajaxReturn($cartData);
			    		exit;

	    			} else {

		    			$price[] = $goodsList['GR_Price'] * $_POST['number']; // 立即购买不存在多规格的总价
		    			$_SESSION['Home']['priceTotal'] = $price;

	    			}
	    			if ($goodsList['GR_Stock'] < $_POST['number']) {

	    				$cartData['code'] = 0;
			    		$cartData['msg'] = '库存不足';
			    		$this->ajaxReturn($cartData);
			    		exit;
	    			}
	    			
	    			// 如果判断都通过则返回
		    		$cartData['code'] = 1;
		    		$cartData['url'] = '/index.php/Home/Order/confirm_order';
		    		$cartData['msg'] = '立即购买判断通过,即将跳转到提交订单页';
					$_SESSION['Home']['liJiGouMaiList']['number'] = $_POST['number'];
					$_SESSION['Home']['liJiGouMaiList']['gid'] = $_POST['gid'];

					unset($_SESSION['Home']['liJiGouMaiList']['fid']); // 删除多规格id,如不删除后面执行不了

					$_SESSION['Home']['JieSuanState'] = 2; // 立即购买

					if ($cartList['CR_OID']) { // 存在多规格
						$_SESSION['Home']['liJiGouMaiList']['fid'] = $cartList['CR_OID'];
					}
		    		$this->ajaxReturn($cartData);
		    		exit;
	    		}
	    	}
    	}
    }

    /**
     * 添加购物车
     * 这个是多规格添加购物车的方法
     * 未登录存进cookie，登录存进数据库
     * 未登录的两种情况
     * 1、第一次来，存cookie
     * 2、第二次来判断cookie有没有数据，有：  数量加1  或  追加数据（不存在相同商品的情况下）
     * $cartNew[$gid.'@#'.$fid] = $newCarArray; 此为键名设计，再配合foreach即可
     * 有多规格键名 商品id拼接规格id
     * 无多规格键名 商品id
     */
    public function CartAdd1() {

    	$gid = I('gid'); //商品ID
    	$fid = I('fo_id'); //规格ID
    	$number = I('number'); //商品数量
	    		
    	if (IS_AJAX) {

	    	if ($gid < 0 || $number < 0) {

	    		$cartData['code'] = 0;
	    		$cartData['msg'] = '数量或商品有误';
	    		$this->ajaxReturn($cartData);
	    		exit;
	    	}

	    	$goodsWhere['GR_Is_Show'] = 1; // 上架
	    	$goodsWhere['GR_Is_Delete'] = 0; //未删除
	    	$goodsWhere['GR_ID'] = $gid; //商品id

	    	$goodsList = M('GoodsRec') // 商品表
	    		->where($goodsWhere)
	    		->field('GR_Name, GR_IMG, GR_Price, GR_Stock, GR_Weight')
	    		->find();

	    	if (empty($goodsList)) {

	    		$cartData['code'] = 0;
	    		$cartData['msg'] = '商品已下架或删除';
	    		$this->ajaxReturn($cartData);
	    		exit;
	    	}

	    	$formatOption = M('FormatOption') // 规格表
	    		->where(array('FO_ID' => $fid))
	    		->field('FO_Price, FO_IMG, FO_Stock, FO_ID, FO_Name, FO_Weight')
	    		->find();
	    	if ($formatOption['FO_Stock'] < $number || $goodsList['GR_Stock'] <= 0) {

	    		$cartData['code'] = 0;
	    		$cartData['msg'] = '库存不足';
	    		$this->ajaxReturn($cartData);
	    		exit;
	    	}

	    	if (!empty($formatOption)) { // 库存够并且有多规格

	    		$goodsList['GR_Price'] = $formatOption['FO_Price']; // 更换价格
	    		$goodsList['GR_Stock'] = $formatOption['FO_Stock']; // 更换库存
	    		$goodsList['GR_IMG'] = $formatOption['FO_IMG']; // 更换图片
	    		$goodsList['GR_Weight'] = $formatOption['FO_Weight']; // 更换图片
	    		$goodsList['formatOption'] = $formatOption; // 追加进商品数据

	    	}

	    	if ($_SESSION['Home']['userid']) { // 登录存数据库

	    		$cartModel = M('CartRec');
	    		$cartRec = $cartModel // 查询是否存在相同商品
	    			->where(array('CR_UID' => $_SESSION['Home']['userid'], 'CR_GID' => $gid, 'CR_OID' => $fid))
	    			->field('CR_ID, CR_Number, CR_Weight, CR_Addtime, CR_Price')
	    			->find();

	    		if (!empty($cartRec)) { // 数据库有该购物车商品区域，处理数量

		    		$saveCart['CR_Number'] = $cartRec['CR_Number'] + $number; // 购物该商品的数量
		    		$saveCart['CR_Addtime'] = time(); // 修改时间
		    		
	    			$bool = $cartModel // 购物车表
	    			   ->where(array('CR_ID' => $cartRec['CR_ID']))
	    			   ->save($saveCart);

	    			if (!empty($bool)) {

			    		$cartData['code'] = 1;
    					$cartData['data'] = $cartList;
			    		$cartData['msg'] = '添加购物车成功';
			    		$this->ajaxReturn($cartData);
			    		exit;

	    			} else {

    					$cartData['code'] = 0;
    					$cartData['data'] = null;
			    		$cartData['msg'] = '添加购物车失败';
			    		$this->ajaxReturn($cartData);
			    		exit;
	    			}

	    		}

	    		// 数据库没有该购物车商品
	    		$cartList['CR_UID'] = $_SESSION['Home']['userid']; // 用户id
	    		$cartList['CR_OID'] = $fid; // 规格id
	    		$cartList['CR_GID'] = $gid; // 商品id
	    		$cartList['CR_Number'] = $number; // 购物该商品的数量
	    		$cartList['CR_Price'] = $goodsList['GR_Price']; // 购买的价格
	    		$cartList['CR_Weight'] = $goodsList['GR_Weight']; // 重量
	    		$cartList['CR_Guige'] = $goodsList['formatOption']['FO_Name']; // 规格名
	    		$cartList['CR_Name'] = $goodsList['GR_Name']; // 商品名
	    		$cartList['CR_Img'] = $goodsList['GR_IMG']; // 图片
	    		$cartList['CR_Addtime'] = time(); // 添加时间
	    		$cart = $cartModel->add($cartList); // 添加
	    		if (!empty($cart)) {

	    			$cartData['code'] = 0;
	    			$cartData['data'] = $cartList;
		    		$cartData['msg'] = '添加购物车成功';
		    		$this->ajaxReturn($cartData);
		    		exit;

	    		} else {
					$cartData['code'] = 1;
	    			$cartData['data'] = null;
		    		$cartData['msg'] = '添加购物车失败';
		    		$this->ajaxReturn($cartData);
		    		exit;
	    		}

	    	} else { // 没登录存cookie

	    		if (cookie('cart')) { // cookie存在则处理数量
	    			$cartList = cookie('cart'); 
    				$cartList = unserialize($cartList);// 反串行化

	    			if ($formatOption['FO_Stock'] < $cartList[$gid.'@#'.$fid]['num'] || $goodsList['GR_Stock'] < $cartList[$gid.'@#'.$fid]['num']) {

			    		$cartData['code'] = 0;
			    		$cartData['msg'] = '库存不足';
			    		$this->ajaxReturn($cartData);
			    		exit;
	    			}

		    		if (array_key_exists($gid.'@#'.$fid, $cartList)) { // 存在这个键,则表示cookie中存在这个商品数据

	    				$cartList[$gid.'@#'.$fid]['num'] = $cartList[$gid.'@#'.$fid]['num'] + $number; // 原有基础上加数量

		    		} else { // 购物车中没有相同商品，则添加新的商品

		    			$newCarArray['goods_id'] = $gid; // 商品id
			    		$newCarArray['oid'] = $fid; // 规格id
			    		$newCarArray['num'] = $number; // 购物该商品的数量
			    		$newCarArray['price'] = $goodsList['GR_Price']; // 购买的价格
						$newCarArray['selected'] = $goodsList['formatOption']['FO_Name']; // 规格名
						$newCarArray['mes'] = $goodsList['GR_Name']; // 商品名
			    		$newCarArray['img'] = $goodsList['GR_IMG']; // 图片
			    		$newCarArray['weight'] = $goodsList['GR_Weight']; // 重量
						$newCarArray['addTime'] = time() + 604800; // 存储七天

						$cartNew[$gid.'@#'.$fid] = $newCarArray;
		    			// 新商品
		    			foreach ($cartNew as $k =>$v) {
		    				$cartList[$k] = $v;
		    			}
		    		}
	    			$cartAlize = serialize($cartList); // 覆盖原本的数据
	    			cookie('cart', $cartAlize, 604800); // 存储七天

	    			$cartData['code'] = 1;
	    			$cartData['data'] = $cartList;
		    		$cartData['msg'] = '添加购物车成功';
		    		$this->ajaxReturn($cartData);
		    		exit;
	    		}

	    		// 不存在则添加新的进cookie
	    		$cartOne['goods_id'] = $gid; // 商品id
	    		$cartOne['oid'] = $fid; // 规格id
	    		$cartOne['num'] = $number; // 购物该商品的数量
	    		$cartOne['price'] = $goodsList['GR_Price']; // 购买的价格
				$cartOne['selected'] = $goodsList['formatOption']['FO_Name']; // 规格名
				$cartOne['mes'] = $goodsList['GR_Name']; // 商品名
	    		$cartOne['img'] = $goodsList['GR_IMG']; // 图片
	    		$cartOne['weight'] = $goodsList['GR_Weight']; // 重量
				$cartOne['addTime'] = time() + 604800; // 存储七天

				$cartOneArray[$gid.'@#'.$fid] = $cartOne;

				foreach ($cartOneArray as $k => $v) {
					$cartList[$k] = $v;
				}
				$carAlize = serialize($cartList);
				cookie('cart', $carAlize, 604800); // 存储七天

				$cartData['code'] = 1;
				$cartData['data'] = $cartList;
	    		$cartData['msg'] = '添加购物车成功';
	    		$this->ajaxReturn($cartData);
	    		exit;
	    	}
    	}
    }

    /**
     * 添加购物车
     * 这个是没有规格添加购物车处理的方法
     */
    public function CartAdd2() {

    	$gid = I('gid'); //商品ID
    	$number = I('number'); //商品数量
    	
    	if (IS_AJAX) {

    		if ($gid < 0 || $number < 0) {

	    		$cartData['code'] = 0;
	    		$cartData['msg'] = '数量或商品有误';

	    		$this->ajaxReturn($cartData);
	    		exit;
	    	}

	    	$goodsWhere['GR_Is_Show'] = 1; // 上架
	    	$goodsWhere['GR_Is_Delete'] = 0; //未删除
	    	$goodsWhere['GR_ID'] = $gid; //商品id

	    	$goodsList = M('GoodsRec') // 商品表
	    		->where($goodsWhere)
	    		->field('GR_Name, GR_IMG, GR_Price, GR_Stock, GR_Weight')
	    		->find();

	    	if (empty($goodsList)) {

	    		$cartData['code'] = 0;
	    		$cartData['msg'] = '商品已下架或删除';
	    		$this->ajaxReturn($cartData);
	    		exit;
	    	}

    		if ($goodsList['GR_Stock'] <= 0) {

	    		$cartData['code'] = 0;
	    		$cartData['msg'] = '库存不足';
	    		$this->ajaxReturn($cartData);
	    		exit;
	    	}
	    	if ($_SESSION['Home']['userid']) { // 登录存数据库

	    		$cartModel = M('CartRec');
	    		$cartRec = $cartModel // 查询是否存在相同商品
	    			->where(array('CR_UID' => $_SESSION['Home']['userid'], 'CR_GID' => $gid))
	    			->field('CR_ID, CR_Number, CR_Weight, CR_Addtime, CR_Price')
	    			->find();

	    		if (!empty($cartRec)) { // 数据库有该购物车商品区域，处理数量

		    		$saveCart['CR_Number'] = $cartRec['CR_Number'] + $number; // 购物该商品的数量
		    		$saveCart['CR_Addtime'] = time(); // 修改时间
		    		
	    			$bool = $cartModel // 购物车表
	    			   ->where(array('CR_ID' => $cartRec['CR_ID']))
	    			   ->save($saveCart);

	    			if (!empty($bool)) {

			    		$cartData['code'] = 1;
    					$cartData['data'] = $cartList;
			    		$cartData['msg'] = '添加购物车成功';
			    		$this->ajaxReturn($cartData);
			    		exit;

	    			} else {

    					$cartData['code'] = 0;
    					$cartData['data'] = null;
			    		$cartData['msg'] = '添加购物车失败';
			    		$this->ajaxReturn($cartData);
			    		exit;
	    			}

	    		}

	    		// 数据库没有该购物车商品
	    		$cartList['CR_UID'] = $_SESSION['Home']['userid']; // 用户id
	    		$cartList['CR_GID'] = $gid; // 商品id
	    		$cartList['CR_Number'] = $number; // 购物该商品的数量
	    		$cartList['CR_Price'] = $goodsList['GR_Price']; // 购买的价格
	    		$cartList['CR_Weight'] = $goodsList['GR_Weight']; // 重量
	    		$cartList['CR_Guige'] = $goodsList['formatOption']['FO_Name']; // 规格名
	    		$cartList['CR_Name'] = $goodsList['GR_Name']; // 商品名
	    		$cartList['CR_Img'] = $goodsList['GR_IMG']; // 图片
	    		$cartList['CR_Addtime'] = time(); // 添加时间

	    		$cart = $cartModel->add($cartList); // 添加
	    		if (!empty($cart)) {

	    			$cartData['code'] = 0;
	    			$cartData['data'] = $cartList;
		    		$cartData['msg'] = '添加购物车成功';
		    		$this->ajaxReturn($cartData);
		    		exit;

	    		} else {
					$cartData['code'] = 1;
	    			$cartData['data'] = null;
		    		$cartData['msg'] = '添加购物车失败';
		    		$this->ajaxReturn($cartData);
		    		exit;
	    		}

	    	} else { // 没登录存cookie

	    		if (cookie('cart')) { // cookie存在则处理数量
	    			$cartList = cookie('cart'); 
    				$cartList = unserialize($cartList);// 反串行化

	    			if ($goodsList['GR_Stock'] < $cartList['wuGuiGe@#gid'.$gid]['num']) {

			    		$cartData['code'] = 0;
			    		$cartData['msg'] = '库存不足';
			    		$this->ajaxReturn($cartData);
			    		exit;
	    			}
		    		if (array_key_exists('wuGuiGe@#gid'.$gid, $cartList)) { // 存在这个键,则表示cookie中存在这个商品数据

	    				$cartList['wuGuiGe@#gid'.$gid]['num'] = $cartList['wuGuiGe@#gid'.$gid]['num'] + $number; // 原有基础上加数量

		    		} else { // 购物车中没有相同商品，则添加新的商品

		    			$cartArray['goods_id'] = $gid; // 商品id
			    		$cartArray['num'] = $number; // 购物该商品的数量
			    		$cartArray['price'] = $goodsList['GR_Price']; // 购买的价格
						$cartArray['selected'] = $goodsList['formatOption']['FO_Name']; // 规格名
						$cartArray['mes'] = $goodsList['GR_Name']; // 商品名
			    		$cartArray['img'] = $goodsList['GR_IMG']; // 图片
			    		$cartArray['weight'] = $goodsList['GR_Weight']; // 重量
						$cartArray['addTime'] = time() + 604800; // 存储七天

						$cartOneArray['wuGuiGe@#gid'.$gid] = $cartArray;
		    			// 新商品
		    			foreach ($cartOneArray as $k =>$v) {
		    				$cartList[$k] = $v;
		    			}
		    		}

	    			$cartAlize = serialize($cartList); // 覆盖原本的数据
	    			cookie('cart', $cartAlize, 604800); // 存储七天
	    			$cartData['code'] = 1;
	    			$cartData['data'] = $cartList;
		    		$cartData['msg'] = '添加购物车成功';

		    		$this->ajaxReturn($cartData);
		    		exit;
	    		}

	    		// 不存在则添加新的进cookie
	    		$cartOne['goods_id'] = $gid; // 商品id
	    		$cartOne['num'] = $number; // 购物该商品的数量
	    		$cartOne['price'] = $goodsList['GR_Price']; // 购买的价格
				$cartOne['selected'] = $goodsList['formatOption']['FO_Name']; // 规格名
				$cartOne['mes'] = $goodsList['GR_Name']; // 商品名
	    		$cartOne['img'] = $goodsList['GR_IMG']; // 图片
	    		$cartOne['weight'] = $goodsList['GR_Weight']; // 重量
				$cartOne['addTime'] = time() + 604800; // 存储七天

				$cartOneArray['wuGuiGe@#gid'.$gid] = $cartOne;

				foreach ($cartOneArray as $k => $v) {
					$cartList[$k] = $v;
				}

				$carAlize = serialize($cartList);
				cookie('cart', $carAlize, 604800); // 存储七天

				$cartData['code'] = 1;
				$cartData['data'] = $cartList;
	    		$cartData['msg'] = '添加购物车成功';
	    		$this->ajaxReturn($cartData);
	    		exit;
	    	}
    	}
    }

}