<?php
namespace Home\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class PersonController extends HomeController {
	public function initialize_s(){
		$uname =(int)$_SESSION['Home']['userid'];
		$res = $this->SelectCookie();
		if((int)$res == -1){
			// header('Location:/index.php/Home/User/Login?loc=person');
			header('Location:/index.php/Home/User/Login/class/person/method/person');
		}
	}
	/**
	 * 我的个人中心
	 */
	public function person(){
		if ($_SESSION['Home']['userid']) { // 登录了

			$userList['userInfo'] = M('UserInfo')
				->where(array('UI_ID' => $_SESSION['Home']['userid']))
				->field('UI_ID as id, UI_Name as perName, UI_ImgUrl as perImg, UI_Mobile as uId')
				->find();

			// 订单表
			$order = M('OrderRec');
			$orderWhere['OR_UID'] = $_SESSION['Home']['userid']; // 用户id

			$orderWhere['OR_State'] = 0;
			$userList['unpaid_num'] = $order
				->where($orderWhere)
				->count(); // 待支付

			$orderWhere['OR_State'] = 1;
			$userList['wfh_num'] = $order
				->where($orderWhere)
				->count(); // 待发货（已付）

			$orderWhere['OR_State'] = 2;
			$userList['dsh_num'] = $order
				->where($orderWhere)
				->count(); // 待签收（已发货）
				
			// 订单商品表
			$orderWhere['OR_State'] = 3;
			$userList['comment_num'] = M('OrderRec')
				->where($orderWhere)
				->count(); // 待评价
			
			// 购物车统计
			$userList['cart_num'] = M('CartRec')
				->where(array('CR_UID' => $userList['userInfo']['id']))
				->count(); 
			
			// 优惠券

		} else { // 没登录

			$userList = 0;

		}
		
		$this->assign('userList_json', json_encode($userList));

		$this->display();
		
	} 
	 
	//我的购物车
	public function getCartInfo(){
		$goods = M('Goods_rec');
    	$format_option = M('Format_option');
		$uname = $_SESSION['Home']['userid'];
		// $uname = 1;
		$car = M('Cart_rec');

        //如果未登录 ，提示进行登录，并且跳转至登录页面   
        $shop_cart = $_SESSION['Home']['cart'];  //从session中读取购物车二维数组  
        // $shop_cart = $arrPro; 

		if(isset($uname)){
			// dump($_SESSION['Home']['cart']);
			//已经登录,从session中取出数据来写入数据库  

			$res = $car->where(array('CR_UID'=>$uname))->order('CR_Addtime desc')->select();
			if($res){
				$result = array();
				foreach($res as $k=>$v){
					//是否有多规格，查库存(将已删除的商品从购物车中删除)
					$goods_res = $goods->field('GR_Is_Show,GR_Is_Delete,GR_Stock')->where(array('GR_ID'=>$v['CR_GID']))->find();
					
					if($v['CR_OID'] == 0){
						//没有多规格
						if($goods_res && (int)$goods_res['GR_Is_Show'] == 1 && (int)$goods_res['GR_Is_Delete'] == 0){
							//商品显示,判断库存
							if((int)$goods_res['GR_Stock'] < (int)$v['CR_Number']){
								$result[$k]['is_stock'] = 0;  //库存不足
								$result[$k]['stock'] = (int)$goods_res['GR_Stock'];  //库存数量
							}else{
								$result[$k]['is_stock'] = (int)$goods_res['GR_Stock'];  //库存足够
								$result[$k]['stock'] = (int)$goods_res['GR_Stock'];  //库存数量
							}
						}else{
							//从购物车中删除
							$cart_delete = $car->where(array('CR_ID'=>$v['CR_ID']))->delete();
						}
						
					}else{
						//有规格
						if($goods_res && (int)$goods_res['GR_Is_Show'] == 1 && (int)$goods_res['GR_Is_Delete'] == 0){
							//商品显示,判断库存
							$format_option_res = $format_option->field('FO_Stock')->where(array('FO_ID'=>$v['CR_OID']))->find();
							if($format_option_res && (int)$format_option_res['FO_Stock'] >= (int)$v['CR_Number']){
								$result[$k]['is_stock'] = 1;  //库存足够
								$result[$k]['stock'] = (int)$format_option_res['FO_Stock'];  //库存数量
							}else{
								$result[$k]['is_stock'] = 0;  //库存足够
								$result[$k]['stock'] = (int)$format_option_res['FO_Stock'];  //库存数量
							}
						}else{
							//从购物车中删除
							$cart_delete = $car->where(array('CR_ID'=>$v['CR_ID']))->delete();
						}
						
					}

					$result[$k]['img'] = $v['CR_Img'];
					// $result[$k]['mes'] = $v['CR_Name'];
					
					// $result[$k]['selected'] = $v['CR_Guige'];
					// $result[$k]['num'] = $v['CR_Number'];
					$result[$k]['price'] = $v['CR_Price']/100;
					// $result[$k]['gid'] = $v['CR_ID'];//
					$result[$k]['href'] = "/index.php/Home/Goods/goods_detail/gid/".$v['CR_GID'];//没有登入时存过来的是goods_id.'@'.$gid,登入时传过来的是crid
					// $result[$k]['max_num'] = $result[$k]['stock'];
				}
			}else{
				$result = 0;  //没有数据
			}
		}else{
			//查session
			if(!empty($_SESSION['Home']['cart'])){
				$res = $_SESSION['Home']['cart'];
				$result = array();
				foreach($res as $k=>$v){
					
					$result[$k]['img'] = $v['goods_img'];
					// $result[$k]['mes'] = $v['goods_name'];
					// $result[$k]['selected'] = $v['guige'];
					// $result[$k]['num'] = $v['goods_num'];
					$result[$k]['price'] = $v['goods_price']/100;
					// $result[$k]['gid'] = $v['goods_id'].'@'.$v['foid'];//没有登入时存过来的是goods_id.'@'.$gid,登入时传过来的是crid
					$result[$k]['href'] = "/index.php/Home/Goods/goods_detail/gid/".$v['CR_GID'];
					// $result[$k]['max_num'] = $v['max_num'];
					// $result[$k]['goods_id'] = $v['goods_id'];//
				}
			}else{
				$result = 0;
			}
		}
		return $result;
	}

	/**
	 * 修改密码
	 */
	public function editPwd() {
		$this->initialize_s();

		if (IS_AJAX && IS_POST) {

			$jiuPwd = $_POST['jiuPwd'];
			$pwd1 = $_POST['pwd1'];
			$pwd2 = $_POST['pwd2'];

			if (empty($jiuPwd) || empty($pwd1) || empty($pwd2)) {

				$data['code'] = 0;
				$data['msg'] = '新旧密码不能为空';
				$this->ajaxReturn($data);
				exit;
			}

			if ($pwd1 != $pwd2) {
				$data['code'] = 0;
				$data['msg'] = '两次密码不一致';
				$this->ajaxReturn($data);
				exit;
			}

			$userInfo = M('UserInfo'); // 用户表
			$userList = $userInfo
				->where(array('UI_ID' => $_SESSION['Home']['userid']))
				->field('UI_Pwd, UI_Salt')
				->find();

			if (empty($userList)) {
				$data['code'] = 0;
				$data['msg'] = '用户数据没找到';
				$this->ajaxReturn($data);
				exit;
			}

			$jiuPwd1 = $userList['UI_Pwd']; // 数据库的
			$isPwd = sha1($jiuPwd.$userList['UI_Salt']);// 输入的

			if ($jiuPwd1 != $isPwd) { // 对比
				$data['code'] = 0;
				$data['msg'] = '旧密码不正确';
				$this->ajaxReturn($data);
				exit;
			}

			$salt = salt();
 			$password = sha1($pwd1.$salt);
 			$saveData['UI_Salt'] = $salt;
			$saveData['UI_Pwd'] = $password;
	        $saveData['UI_LastUpdateTime'] = time();

			$save = $userInfo
			    ->where(array('UI_ID' => $_SESSION['Home']['userid']))
			    ->save($saveData);

		   if ($save) {

		   		//修改密码成功，清除session
	            $_SESSION['Home']['userid'] = null;
	            $_SESSION['Home']['umobile'] = null;
	            $_SESSION['Home']['login'] = false;
	            $_SESSION['UserInfo'] = null;
	            setcookie('Login', '', time()-86400, '/');

	            $data['code'] = 1;
				$data['url'] = '/index.php/Home/User/Login/class/Person/method/person';
				$data['msg'] = '密码修改成功';

		   } else {

		   	   $data['code'] = 0;
			   $data['msg'] = '密码修改失败';
		   }

			$this->ajaxReturn($data);
			exit;
		}

		$this->display();
	}
	
	//个人资料及修改资料
	public function info(){
		$this->initialize_s();
		$where['UI_ID'] = (int)$_SESSION['Home']['userid'];
		if(!($where['UI_ID']>0)){

		}
		$ui = M('user_info');
		if(IS_POST){
//			 dump($_FILES);
//			 dump($_POST);
//			 exit;
			$ui_info = $ui->field('UI_ImgUrl')->where($where)->find();
			if($_FILES['photo']){
				if(IS_WIN){
	            	$type = "C:/wamp64/www/yuki";
		        }else{
		            $type ='.';
		        }
		        $path = $type.'/Uploads/user_img/';
		        if(!file_exists($path)){
		            @mkdir($path);
		        }
		        $upload = new \Think\Upload();// 实例化上传类
		        $upload->maxSize   =     5242880 ;// 设置附件上传大小
		        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		        $upload->rootPath  =     $type; // 设置附件上传根目录
		        $upload->savePath  =     '/Uploads/user_img/'; // 设置附件上传（子）目录
		        // 上传文件

		        if($upload->maxSize<$_FILES['size']){
		            $this->ajaxReturn(-2);
		        }
		        $info   =   $upload->upload();
		        if($info){
		        	foreach($info as $file){
		        		// $this->ajaxReturn($file);
				        $map['UI_ImgUrl'] = $file['savepath'].$file['savename'];
				        if($ui_info['UI_ImgUrl'] != "/Uploads/defaultimg/defaultimg.png"){
		        			unlink($type.$ui_info['UI_ImgUrl']);
				        }
				        
				    }
		        	
		        }
			}

			$map['UI_Name'] = trim($_POST['name']);
			$map['UI_Birthday'] = trim($_POST['birthday']);
			$map['UI_Address'] = trim($_POST['address']);
            if(trim($_POST['sex']) == '男'){
                $map['UI_Sex'] = 1;
            }else{
                $map['UI_Sex'] = 0;
            }
			if($map['UI_Name']){
				$save = $ui->where($where)->save($map);
			}
			if($save){
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
		}else{
			$ui_info = $ui->field('UI_ImgUrl,UI_Name,UI_Sex,UI_Address,UI_Birthday')->where($where)->find();
            if(!$ui_info['UI_ImgUrl']){
                !$ui_info['UI_ImgUrl'] = -1;
            }
            if(!$ui_info['UI_Address']){
                $ui_info['UI_Address'] = -1;
            }
            if(!$ui_info['UI_Birthday']){
                $ui_info['UI_Birthday'] = -1;
            }
			$this->assign('ui_info_json', json_encode($ui_info));
			$this->display();
		}
			
	}

	
	//确认收货
	public function confirm_order(){
		$oy = M('order_yi');

		$where['OY_ID'] = (int)I('oid');
		$where['OY_UID'] = (int)$_SESSION['Home']['userid'];
		// $where['OR_UID'] = 1;
		if(!($where['OY_ID']>0) || !($where['OY_UID']>0)){
			$this->ajaxReturn(-1);
		}

		$oy_info = $oy->where($where)->field('OY_OrderState')->find();
		if($oy_info && $oy_info['OY_OrderState'] == 2){
			$save['OY_OrderState'] = 3;
			$save['OY_ConfirmTime'] = time();
			$info = $oy->where($where)->save($save);
			if($info){
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
		}else{
			$this->ajaxReturn(-1);
		}
	}
	public function service(){
		$sr = M('System_rec');	
		$sr_info = $sr->field('SR_KeFuPhone,SR_KeFuWeChat,SR_QrcodeImg')->find();
		
		$this->assign('sr_info',$sr_info);
		$this->display();	
	}
    /**
     * Notes:我的优惠券
     * Author: AndyZhang
     * Date: 2018/1/23 下午5:28
     */
	public function my_coupon(){
       $this->initialize_s();
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
			$get_res = $co->field("CR_Minus as money,CR_Condition as full_money,DATE_FORMAT(FROM_UNIXTIME(CR_StartTime),'%Y.%m.%d') as date_begin,DATE_FORMAT(FROM_UNIXTIME(CR_EndTime),'%Y.%m.%d') as date_end")->where($get_where)->order('CR_Sort desc')->select();
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

        $this->assign('res',json_encode($arr));
		$this->display();
	}

    /**
     * Notes:我的优惠券ajax请求
     * Author: AndyZhang
     * Date: 2018/1/23 下午7:48
     */
    public function MyCouponAjax(){
        $co = M('coupon_rec');
        $push = M('coupon_push');

        if(IS_POST && IS_AJAX){
            //系统不可用优惠券
            $co_where['CR_State'] = 0;
            $co_where['CR_ISDelete'] = 0;
            $co_where['_logic'] = 'OR';
            $co_res = $co->field('CR_ID as crid')->where($co_where)->select();
			foreach ($co_res as $k=>$v) {
				$co_ids[] = $v['crid'];
			}
            $state = (int)I('state');
            switch($state){
                case 0://未使用
                    //个人所有可用优惠券
                    $where['CP_UID'] = (int)$_SESSION['Home']['userid'];
                    $where['CP_State'] = 0;//未使用
                    $where['CP_Status'] = 1;//未失效
                    $push_res = $push->field('CP_CID as cpid')->where($where)->select();
                    foreach ($push_res as $k=>$v) {
						$push_ids[] = $v['cpid'];
					}
                    if($push_ids){
                        foreach ($push_ids as $key => $value) {
                            if(in_array($value, $co_ids)){
                                unset($push_ids[$key]);
                            }
                        }
                        if($push_ids){
                            $get_where['CR_ID'] = array('in',$push_ids);
                            $get_res = $co->field("CR_Minus as money,CR_Condition as full_money,DATE_FORMAT(FROM_UNIXTIME(CR_StartTime),'%Y.%m.%d') as date_begin,DATE_FORMAT(FROM_UNIXTIME(CR_EndTime),'%Y.%m.%d') as date_end")->where($get_where)->order('CR_Sort desc')->select();
                            if(!$get_res){
                                $flag['state'] = -2;
                            }else{
                                foreach ($get_res as $k=>$v) {
                                    $get_res[$k]['status'] = 1;
                                }
                                $flag['state'] = 1;
                                $flag['data'] = $get_res;
                            }
                        }else{
                            $flag['state'] = -2;
                        }
                    }else{
                        $flag['state'] = -2;
                    }
                    break;
                case 1://已使用
                    //个人已使用优惠券
                    $map['CP_UID'] = (int)$_SESSION['Home']['userid'];
                    $map['CP_State'] = 1;
                    $lose_res = $push->field('CP_CID as cpid')->where($map)->select();
 					foreach ($lose_res as $k=>$v) {
						$lose_ids[] = $v['cpid'];
					}
                    if($lose_ids){
                        $yi_where['CR_ID'] = array('in',$lose_ids);
                        $yi_res = $co->field("CR_Minus as money,CR_Condition as full_money,DATE_FORMAT(FROM_UNIXTIME(CR_StartTime),'%Y.%m.%d') as date_begin,DATE_FORMAT(FROM_UNIXTIME(CR_EndTime),'%Y.%m.%d') as date_end")->where($yi_where)->order('CR_Sort desc')->select();
                        if(!$yi_res){
                            $flag['state'] = -2;
                        }else{
                            foreach ($yi_res as $k=>$v) {
                                $yi_res[$k]['status'] = 2;
                            }
                            $flag['state'] = 1;
                            $flag['data'] = $yi_res;
                        }
                    }else{
                        $flag['state'] = -2;
                    }
                    break;
                case 2://已失效
                    //个人已失效优惠券
                    $where['CP_UID'] = (int)$_SESSION['Home']['userid'];
                    $where['CP_Status'] = 0;
                    $push_res = $push->field('CP_CID as cpid')->where($where)->select();
                    foreach ($push_res as $k=>$v) {
						$push_ids[] = $v['cpid'];
					}
                    if($push_ids){
                        $yi_where['CR_ID'] = array('in',$push_ids);
                        $yi_res = $co->field("CR_Minus as money,CR_Condition as full_money,DATE_FORMAT(FROM_UNIXTIME(CR_StartTime),'%Y.%m.%d') as date_begin,DATE_FORMAT(FROM_UNIXTIME(CR_EndTime),'%Y.%m.%d') as date_end")->where($yi_where)->order('CR_Sort desc')->select();
                        foreach ($yi_res as $k=>$v) {
                            $yi_res[$k]['status'] = 3;
                        }
                        $flag['state'] = 1;
                        $flag['data'] = $yi_res;
                    }else{
                        $flag['state'] = -2;
                    }
                    break;
            }
        }else{
            $flag['state'] = -1;
            $flag['msg'] = '非法的请求方式';
        }
        $this->ajaxReturn($flag);
    }

	/**
	 * Notes:商品详情领取优惠券
	 * Author: AndyZhang
	 * Date: 2018/1/24 下午4:33
	 */
	public function GetCoupon(){
		if(isset($_SESSION['Home']['userid']) && !empty($_SESSION['Home']['userid'])){
			$push = M('coupon_push');
			$co = M('coupon_rec');
			if(IS_POST && IS_AJAX){
				$crid = (int)I('crid');
				$co_res = $co->where(array('CR_ID'=>$crid))->find();
				$data['CP_CID'] = $crid;
				$data['CP_UID'] = (int)$_SESSION['Home']['userid'];
				$data['CP_StartTime'] = $co_res['CR_StartTime'];
				$data['CP_EndTime'] = $co_res['CR_EndTime'];
				$result = $push->add($data);
				if($result){
					$flag['state'] = 1;
					$flag['msg'] = '您已成功领取优惠券！';
				}else{
					$flag['state'] = -1;
					$flag['msg'] = '领取失败，请稍后重试';
				}
			}else{
				$flag['state'] = -1;
				$flag['msg'] = '非法的请求方式';
			}
		}else{
			$flag['state'] = -2;
			$flag['msg'] = '请先登入';
		}

		$this->ajaxReturn($flag);
	}


}