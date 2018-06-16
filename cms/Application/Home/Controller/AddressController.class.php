<?php
namespace Home\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class AddressController extends HomeController {
	public function initialize_first(){
		$uname =(int)$_SESSION['Home']['userid'];
		$res = $this->SelectCookie();
		if((int)$res == -1){
			header('Location:/index.php/Home/User/Login?loc=person');
		}
	}

	/**
	 * 显示地址页面
	 */
	public function address(){
		$this->initialize_first();
		$uid = $_SESSION['Home']['userid'];
		if((int)$uid > 0){
		    $addressInfo = M('Address_rec')
		        ->where(array("AR_UID" =>$uid))
		        ->order('AR_Is_Default desc')
		        ->field('AR_ID as id, AR_Link as name, AR_Phone as phone, AR_Province as province, AR_City as city, AR_County as county, AR_Detail as detail, AR_Is_Default as isDefault')
		        ->select();
		}
		$this->assign('addressInfoJson',json_encode($addressInfo));
		$this->display();
	}

	/**
	 * 管理地址页面
	 */
	public function address_management(){
		$this->initialize_first();
		$uid = $_SESSION['Home']['userid'];
		if((int)$uid > 0){
		    $addressInfo = M('Address_rec')
		        ->where(array("AR_UID" =>$uid))
		        ->order('AR_Is_Default desc')
		        ->field('AR_ID as id, AR_Link as name, AR_Phone as phone, AR_Province as province, AR_City as city, AR_County as county, AR_Detail as detail, AR_Is_Default as isDefault')
		        ->select();
		}
		
		$this->assign('addressInfoJson',json_encode($addressInfo));
		$this->display();
		
	}

	//查询某条地址信息
    public function address_info(){
		$this->initialize_first();
        $aid = I('aid');
        $address=M('Address_rec')->where("AR_ID=".$aid)->find();
        
        if($address){
            $this->ajaxReturn($address);
            
       }else{
            $this->ajaxReturn(0);
            
       }
    }
    //添加地址
  //   public function new_address(){
		// $this->initialize_first();
        
  //   	if(IS_POST){
  //   		$map['AR_UID'] = $where['AR_UID'] = $_SESSION['Home']['userid'];
  //       	// $where['AR_UID'] = $map['AR_UID'] = 1;
  //   		if(!((int)$map['AR_UID']>0)){
	 //            exit;
  //   		}
	 //    	$map['AR_Link']=I('link');//联系人
	 //    	$map['AR_Phone']=I('phone');//手机
	 //    	$map['AR_Province']=I('province');//省
	 //    	$map['AR_City']=I('city');//市
	 //    	$map['AR_County']=I('county');//县
	 //    	$map['AR_Detail']=I('detail');//详细地址
		// 	$map['AR_CreateTime']=time();//创建时间
	 //        $map['AR_Is_Default'] = (int)I('isDefault');//默认地址为0

	 //        //其他地址设为非默认地址
	 //        // dump($map);

	 //        $is_first = M('Address_rec')->where($where)->find();
	 //        // if($map['AR_IsDefault']==1&&$is_first){
	 //        //     $setNoDefault=M('Address_rec')->where('AR_UID ='.$map['AR_UID'])->setField('AR_IsDefault','0');
	 //        // }
	 //        //若无地址，首个创建的地址为默认地址
	 //        if(!$is_first){
		// 		$map['AR_Is_Default']=1;
	 //        }elseif($is_first && $map['AR_Is_Default'] == 1){
	 //        	$save['AR_Is_Default'] = 0;
	 //        	$saveNodefault = M('Address_rec')->where($where)->save($save);
	 //        }
	 //        $address=M('Address_rec')->add($map);
	 //    	if($address){
	 //    		if((int)$_SESSION['Home']['isarid'] == 99){
	 //    			$_SESSION['Home']['arid'] = $address;
	 //    			$_SESSION['Home']['isarid'] = null;
	 //    			echo "<script>var url = '/index.php/Home/Order/confirm_order';var state = {url:url};history.replaceState(state,'',url);location.reload();</script>";
	 //    		}
	 //    		if($t == "new" || $t == "person"){
  //   				echo "<script>var url = '/index.php/Home/Address/address_management/t/person';var state = {url:url};history.replaceState(state,'',url);location.reload();</script>";
	 //    		}elseif($t == "confirm"){
  //   				echo "<script>var url = '/index.php/Home/Address/address';var state = {url:url};history.replaceState(state,'',url);location.reload();</script>";
	 //    		}elseif($t == "c"){
  //   				echo "<script>var url = '/index.php/Home/Address/address_management/t/c';var state = {url:url};history.replaceState(state,'',url);location.reload();</script>";
	 //    		}
  //   			echo "<script>var url = '/index.php/Home/Address/address_management';var state = {url:url};history.replaceState(state,'',url);location.reload();</script>";
	    		
	 //        }else{
	        		
	 //        }
  //   	}else{
		// 	$this->assign('t',$t);
  //   		$this->display();
  //   	}
	    	
  //   }
    /**
     * 添加新地址
     */
    public function new_address() {
    	$this->initialize_first();
    	if (IS_POST && IS_AJAX) {

    		$addressList['AR_Link'] = I('name'); // 联系人
    		$addressList['AR_UID'] = $_SESSION['Home']['userid']; // 用户id
    		$addressList['AR_Detail'] = I('addressDetail'); // 详细地址
    		$addressList['AR_Phone'] = I('phone'); // 联系方式
    		$addressList['AR_CreateTime'] = time(); // 添加时间
    		$shengShiXian = I('address'); // 省市县
    		$moRen = I('moRen'); // 是否默认
    		$bool = true; // 标记

    		$addressArray = explode(' ', $shengShiXian);
    		if (empty($addressArray[0]) || empty($addressArray[1]) || empty($addressArray[2])) {

    			$address['code'] = 0;
    			$address['msg'] = '所在地址不能为空';
    			$bool = false;
    			$this->ajaxReturn($address);
    			exit;

    		} else {

	    		$addressList['AR_Province'] = $addressArray[0]; // 省
	    		$addressList['AR_City'] = $addressArray[1]; // 市
	    		$addressList['AR_County'] = $addressArray[2]; // 县

    		}

    		if (empty($addressList['AR_Link'])) {

    			$address['code'] = 0;
    			$address['msg'] = '收货人不能为空';
    			$bool = false;
    			$this->ajaxReturn($address);
    			exit;
    		}

    		if (empty($addressList['AR_Detail'])) {

    			$address['code'] = 0;
    			$address['msg'] = '详细地址不能为空';
    			$bool = false;
    			$this->ajaxReturn($address);
    			exit;
    		}

    		if (empty($addressList['AR_Phone'])) {

    			$address['code'] = 0;
    			$address['msg'] = '联系方式不能为空';
    			$bool = false;
    			$this->ajaxReturn($address);
    			exit;
    		}
    		
    		$addressRec = M('AddressRec'); // 地址表
    		
    		if ($moRen) { // ture表示设置默认

    			$addressUser = $addressRec // 查询旧地址
	    			->where(array('AR_UID' => $addressList['AR_UID'], 'AR_Is_Default' => 1))
	    			->field('AR_Is_Default, AR_ID')
	    			->find();

	    		if ($addressUser['AR_Is_Default'] == 1) { // 把久的默认地址取消

	    			$save['AR_Is_Default'] = 0;
    				$saveDefault = $addressRec
    				    ->where(array('AR_ID' => $addressUser['AR_ID']))
    				    ->save($save);
	    		}

				$addressList['AR_Is_Default'] = 1;
    		} else {

    			$addressList['AR_Is_Default'] = 0;
    		}

    		if ($bool) { // 判断通过->添加进数据库
    			
    			$addressLast = $addressRec->add($addressList);
    			$_SESSION['Home']['addressId'] = $addressLast; // 设置地址id
    			if ($addressLast) {

    				$address['code'] = 1;
	    			$address['url'] = '/index.php/Home/Order/confirm_order';
	    			$address['msg'] = '保存成功';
	    			$this->ajaxReturn($address);
	    			exit;

    			} else {

    				$address['code'] = 0;
	    			$address['msg'] = '保存失败';
	    			$this->ajaxReturn($address);
	    			exit;
    			}
    		}
    	}
    	$this->display();

    }
    /**
     * 修改地址页面和处理修改的地址
     */
    public function change_address() {
    	$this->initialize_first();
    	$id = I('id'); // 地址表id
    	if ($id <= 0) {

    		   dump(404);exit;
    	}
    	$addressRec = M('AddressRec');
    	$addressList = $addressRec
    		->where(array('AR_ID' => $id))
    		->field('AR_ID as id, AR_Link as name, AR_Phone as phone, AR_Province as province, AR_City as city, AR_County as county, AR_Detail as detail')
    		->find();

    	if (IS_POST && IS_AJAX) {

    		$addressNew['AR_UID'] = $_SESSION['Home']['userid'];
    		$addressNew['AR_Link'] = I('name');
    		$addressNew['AR_Phone'] = I('phone');
    		$loc = I('loc');
    		$loc = explode(' ', $loc);
    		$addressNew['AR_Detail'] = I('detail');
    		$addressNew['AR_Is_Default'] = I('default') == true ? 1 : 0; // true为默认 false 为不默认
    		$addressNew['AR_UpdateTime'] = time();

			$addressNew['AR_Province'] = $loc[0]; // 省
			$addressNew['AR_City'] = $loc[1]; // 市
			$addressNew['AR_County'] = $loc[2]; // 区县
    		
    		if ($addressNew['AR_Is_Default'] == 1) { // 为1则要把旧的默认地址修改

    			$save['AR_Is_Default'] = 0;
				$saveDefault = $addressRec
				    ->where(array('AR_UID' => $_SESSION['Home']['userid']))
				    ->save($save);
    		}

			$addressLast = $addressRec->where(array('AR_ID' => $id))->save($addressNew);

			if ($addressLast) {

				$addressData['code'] = 1;
				$addressData['lastId'] = $addressLast;
				$addressData['url'] = '/index.php/Home/Address/address_management';
				$addressData['msg'] = '修改地址成功,两秒后跳转';
				$this->ajaxReturn($addressData);
				exit;

			} else {

				$addressData['code'] = 0;
				$addressData['msg'] = '修改地址失败';
				$this->ajaxReturn($addressData);
				exit;
			}
    	}

    	$this->assign('addressListJson', json_encode($addressList));
    	$this->display();

    }
    /**
     * 删除地址
     */
   	public function addressDel() {
    	$this->initialize_first();
   		$id = I('id');

   		if ($id <= 0 ) {
   			dump(404);exit;
   		}

   		$addressRec = M('AddressRec')->where(array('AR_ID' => $id))->delete();

   		if ($addressRec) {

			$addressData['code'] = 1;
			$addressData['msg'] = '删除地址成功';
			$this->ajaxReturn($addressData);
			exit;

		} else {

			$addressData['code'] = 0;
			$addressData['msg'] = '删除地址失败';
			$this->ajaxReturn($addressData);
			exit;
		}

   	}
    //设为默认地址
    public function setDefault(){
		$this->initialize_first();
        $aid = I('aid');
		$uid = $_SESSION['Home']['userid'];
    	// $uid = 1;
        $set0=M('address_rec')->where(array('AR_UID'=>$uid))->setField('AR_Is_Default','0');
        $set1=M('address_rec')->where(array('AR_ID'=>$aid))->setField('AR_Is_Default','1');
        if($set1){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(-1);
        }
    }


    //PC端
    private function PCaddress_management(){
		$uid = $_SESSION['Home']['userid'];
		if((int)$uid>0){
		    $address = M('Address_rec')->where(array("AR_UID"=>$uid))->order('AR_Is_Default desc')->select();
		}
		foreach ($address as $key => $value) {
			$address_info[$key]['id'] = $address[$key]['AR_ID'];
			$address_info[$key]['name'] = $address[$key]['AR_Link'];
			$address_info[$key]['phone'] = $address[$key]['AR_Phone'];
			$address_info[$key]['loc'] = $address[$key]['AR_Province'].' '.$address[$key]['AR_City'].' '.$address[$key]['AR_County'];
			$address_info[$key]['address'] = $address[$key]['AR_Detail'];
			$address_info[$key]['isDefaultLoc'] = $address[$key]['AR_Is_Default'] == 1?true:false;
		}
			
		$this->assign('address_info_json',json_encode($address_info));
		$this->display();
    }

    //添加地址
    public function add(){
		$this->initialize_first();
		$map['AR_UID'] = $where['AR_UID'] = $where1['AR_UID'] = $_SESSION['Home']['userid'];
    	// $where['AR_UID'] = $map['AR_UID'] = 1;

		if(!((int)$map['AR_UID']>0)){
            $this->ajaxReturn(-1);
		}
    	$data = I('post_data');
    	if(!$data){
            $this->ajaxReturn(-1);
    	}
    	$aid = (int)$data['id'];
    	$map['AR_Link'] = trim($data['name']);//联系人
        $map['AR_Phone']= trim($data['phone']);//手机
        $map['AR_Province']= trim($data['province']);//省
        $map['AR_City'] = trim($data['city']);//市
        $map['AR_County'] = trim($data['county']);//县
        $map['AR_Detail'] = trim($data['detail']);//详细地址
        $map['AR_Is_Default'] = (int)$data['isDefault'];//默认地址为0
		if($aid){
			$where['AR_ID'] = $aid;
	        
	        $map['AR_UpdateTime']=time();
	        // $map['AR_IsDefault']=0;//默认地址
	        //其他地址设为非默认地址
	        if($map['AR_Is_Default']==1 && $where1['AR_UID']){
	            $setNoDefault=M('Address_rec')->where($where1)->setField('AR_Is_Default',0);
	        }
	        $address=M('Address_rec')->where($where)->save($map);
	        if($address){
	    		$this->ajaxReturn(1);
	        }else{
	    		$this->ajaxReturn(0);
	        }
		}else{
			$map['AR_CreateTime']=time();//创建时间

	        //其他地址设为非默认地址
	        // dump($map);

	        $is_first = M('Address_rec')->where($where)->find();
	        // if($map['AR_IsDefault']==1&&$is_first){
	        //     $setNoDefault=M('Address_rec')->where('AR_UID ='.$map['AR_UID'])->setField('AR_IsDefault','0');
	        // }
	        //若无地址，首个创建的地址为默认地址
	        if(!$is_first){
				$map['AR_Is_Default']=1;
	        }elseif($is_first && $map['AR_Is_Default'] == 1){
	        	$save['AR_Is_Default'] = 0;
	        	$saveNodefault = M('Address_rec')->where($where)->save($save);
	        }
	        $address=M('Address_rec')->add($map);
	    	if($address){
	    		$this->ajaxReturn($address);
	        }else{
	    		$this->ajaxReturn(0);
	        }
		}
    }
}