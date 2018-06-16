<?php
namespace Home\Controller;
use Think\Controller;
use Think\Page;
use \Redis;

/**
* 个人中心控制器
* @Author: Drizzle 
* @E-mail: Drizzle88@163.com
* @blogs: www.ylphp.com
* @phone: 13838389438
*/
class UsercenterController  extends CommonController
{	
	/**
	 * 个人中心首页->个人资料
	 * @Author: Drizzle           2017-11-28
	 * @return  arr
	 */
	public function index()
	{	
		if(empty(session('user'))){
			echo "<script>alert('请先登录')</script>";
			$this->display('Login/login');
			exit;
		}

		// 连接订单表
		$order = D('order');
		$orders = $order->where(['uid'=>38])->limit(2)->getData();
						
		$this->assign('orders', $orders);
		$this->display();
	}

	/**
	 * 个人信息页面
	 * @Author: Drizzle           2017-11-29
	 * @return  arr
	 */
	public function information()
	{	
		$user = D('User');
		$data = $user->where(['id'=>session('user')['id']])->find();
		// dump($data);
		if (IS_POST) {
			// 连接用户表

	        // 查询用户表的数据
	        // 接收用户填写的昵称
	        $data['nickname'] = I('post.nickname');

	        // 如果用户还没有填写真实姓名,就接收用户填写的姓名,
	        // 如果用户已经填写真实姓名了,就不给用户填写姓名。
	        if(empty($data['tname'])) {
	        	$data['tname'] = I('post.tname');
	        }

	        // 接收性别
	        $data['sex'] = I('post.sex');

	        // 接收用户生日的年/月/日
	        $data['year']  = I('post.year');
	        $data['month'] = I('post.month');
	        $data['day']   = I('post.day');
	        // dump($data);exit;

	        $save = $user->save($data);
	        if ($save) {
	        				// echo "<pre>";
	        				// 		var_dump($data);
	        				// 	echo "</pre>";
	        				// 		exit;
	        	$this->success('修改个人资料成功', U('Usercenter/information'));
	        	exit;
	        } else {
	        	$this->error('修改个人资料失败,请重新修改');
	        	exit;
	        }
		}
		$this->assign('data', $data);
		$this->display();
	}

	/**
	 * 地址管理 
	 * @Author: Drizzle           2017-11-29
	 * @E-mail: Drizzle88@163.com
	 * @return  [type]            [description]
	 */
	public function address()
	{
		// 全国地址三级联动处理
		if (IS_AJAX) {

			// 查询下一级的省份或城市
			$areas = M('areas');
			// id不等于1
			$array['id'] = ['neq', 1];
			$array['parent_id'] = $_GET['id'];
			$areasList = $areas->where($array)->field('id, parent_id, area_name, area_type')->select();

			$this->ajaxReturn($areasList);
			exit;
		}

		if (IS_POST) {
			// 连接地址表
			$address = M('address');
			$data = $address->create($_POST);
			$data['createtime'] = time();
			if (empty(I('post.username'))) {
				$this->error('请输入收货人姓名');
			} 

			if (empty(I('post.userphone'))) {
				$this->error('请输入收货人姓名');
			}

			if (empty(I('post.address'))) {
				$this->error('请输入收货人姓名');
			}
			
			$data['area1'] = I('post.province');
			$data['area2'] = I('post.city');
			$data['area3'] = I('post.area');
			$data['uid'] = session('user')['id'];
			$model = M('address');
			$data = $model->add($data);
			if ($data) {
				$this->success('新增地址成功', 'address');
				exit;
			} else {
				$this->error('新增地址失败,请重新添加');
				exit;
			}
		}
		// 连接地址表
		$address = D('address');
		$address = $address->where(['uid'=>session('user')['id']])->PayModel();
						
		// 连接地区表
		$area = M('areas');
	    // 查找所有省份
	    $province = $area->where('`parent_id` = 1')->select();
	    
	    $this->assign('address', $address);		
	    $this->assign('province', $province);
	    		// echo "<pre>";
	    		// 	var_dump($address);
	    		// echo "</pre>";
	    		
		$this->display();
	}

	/**
	 * 默认地址
	 */
	public function defaultAddr()
	{
		$address = M('Address');
		$id = I('post.id');
		$uid = I('post.uid'); 
		$add['isdefault'] = '1';
		$del['isdefault'] = '0';
		$addressA = $address->where("id = $id")->save($add);
		if($addressA){
			$addressB = $address->where("uid = $uid and id != $id")->save($del);
			if($addressB){
				$this->ajaxReturn('1');
			}
		}


	}


	/**
	 *[addressajaxDel ajax删除地址]
     * @param int $id 要删除的数据
	 */
	public function addressajaxDel($id) {
		if (IS_AJAX) {
			if (M('address')->delete($id)) {
				$this->success('删除成功');
			} else {
				$this->error('删除失败');
			}
		}
	}

	/**
	 *编辑收货地址
	 */
	public function editaddress() {
		if (IS_POST) {
			// 实例化
			$model = M('address');

			// 判断id是否为空
			if (empty($_POST['id'])) {

				$this->error('你要修改那一条？');
				exit;
			}

			// 修改语句
			if ($model->where(['id'=>$_POST['id']])->save($_POST)) {
				// 成功区域
				$this->success('修改成功', U('Usercenter/address'));
				exit;
			} else {
				// 不传参数默认跳转原来地方（表单）
				$this->error('你没有修改地址数据');
				exit;
			}
		} 
		// 连接地址表
		$address = D('address');
		// 查询单条地址表的数据
		$data = $address->where(['id'=>$_GET['id']])->find();
		// 连接地区表
		$area = M('areas');
	    // 查找所有省份
	    $province = $area->where('`parent_id` = 1')->select();

	 //    // 连接地区表
	 //    $areas = D('areas');
	 //    // 查询地区表的所有parent_id,area_name
	 //    $area_name = $areas->field('parent_id, area_name')->select();
	 //    // 拿出sort作为值, id作为键
		// $res = array_column($area_name, 'area_name', 'parent_id');

		// // 分配数据
		// $this->assign('res', $res);
		$this->assign('data', $data);
		$this->assign('province', $province);
		$this->display();
	}


	/**
	 * 个人订单管理
	 * @Author: Drizzle           2017-11-29
	 * @return  arr
	 */
	public function order()
	{
	
		// 查询所有的订单表
		$order = D('Order');
		$orderStr['uid'] = ['eq', $_SESSION['user']['id']];
		$orderStr['del'] = ['eq', 2];
		$orderArr = $order->where($orderStr)->order();

		if (!empty($orderArr['id'])) {
			// 查询所有的订单详情表
			$orderData = M('Orderdata');
			$orderDerdataStr['pid'] = ['in', $orderArr['id']];
			$orderDataList = $orderData->where($orderDerdataStr)->select();
		}

		// 查询待付款的订单表和详情表
		// 追加条件
		$orderStr['state'] = ['eq', 1];
		$orderFk = $order->where($orderStr)->order();
		if (!empty($orderFk['id'])) {
			$orderDerdataStr['pid'] = ['in', $orderFk['id']];
			$orderFkList = $orderData->where($orderDerdataStr)->select();
		}


		// 查询待发货订单
		$orderStr['state'] = ['eq', 2];
		$orderFh = $order->where($orderStr)->order();

		if (!empty($orderFh['id'])) {

			$orderDerdataStr['pid'] = ['in', $orderArr['id']];
			$orderFhList = $orderData->where($orderDerdataStr)->select();

		}

		// 查询待收货订单
		$orderStr['state'] = ['eq', 3];
		$orderSh = $order->where($orderStr)->order();
		if (!empty($orderSh['id'])) {

			$orderDerdataStr['pid'] = ['in', $orderSh['id']];
			$orderShList = $orderData->where($orderDerdataStr)->select();
		}

		// 查询待评价订单
		$orderStr['state'] = ['eq', 4];
		$orderPj = $order->where($orderStr)->order();
		if (!empty($orderPj['id'])) {

			$orderDerdataStr['pid'] = ['in', $orderPj['id']];
			$orderPjList = $orderData->where($orderDerdataStr)->select();
		}

		// 查询已完成订单
		$orderStr['state'] = ['eq', 5];
		$orderNew = $order->where($orderStr)->order();
		if (!empty($orderNew['id'])) {
			$orderDerdataStr['pid'] = ['in', $orderNew['id']];
			$orderNewList = $orderData->where($orderDerdataStr)->select();
		}

		// 删除多余的订单表pid
		unset($orderArr['id']);
		unset($orderFk['id']);
		unset($orderFh['id']);
		unset($orderSh['id']);
		unset($orderPj['id']);
		unset($orderNewList['id']);
	
		
		
		// 待付款等等的订单
		$this->assign('orderFkList', $orderFkList);
		$this->assign('orderFhList', $orderFhList);
		$this->assign('orderShList', $orderShList);
		$this->assign('orderPjList', $orderPjList);
		$this->assign('orderNewList', $orderNewList);

		$this->assign('orderFk', $orderFk);
		$this->assign('orderFh', $orderFh);
		$this->assign('orderSh', $orderSh);
		$this->assign('orderPj', $orderPj);
		$this->assign('orderNewList', $orderNewList);


		// 所有订单表
		$this->assign('orderArr', $orderArr);

		// 所有订单详情表
		$this->assign('orderDataList', $orderDataList);
		$this->display();
	}

	/**
	 * 用户点击催单
	 */
	public function cuidan() {

		if (empty($_GET['id'])) {

			$this->error('你要提醒那一条订单发货？');
			exit;
		}

		$model = M('Order');
		$cuiDanTj['id'] = $_GET['id'];
		$cuiDanTj['cuidan'] = ['eq', '1'];
		$cuiDan = $model->where($cuiDanTj)->field('cuidan')->find();

		if (!empty($cuiDan)) {
			$this->error('亲，您已经催过单了，请过段时间再来，返回订单首页中', U('Usercenter/order'), 3);
			exit;
		}

		// 2 为正常
		$_GET['cuidan'] = '1';
		$bool = $model->save($_GET);

		if ($bool) {

			$this->success('催单成功，返回订单首页中', U('Usercenter/order'), 3);
			exit;
		} else {
			$this->error('催单失败，返回订单首页中',  U('Usercenter/order'), 3);
			exit;
		}
		
	}

	/**
	 * 用户点击确认收货
	 * @return [type] [description]
	 */
	public function confirm() {

		$model = M('Order');
		// 4 为立即评价
		$_GET['state'] = '4';
		$bool = $model->save($_GET);

		if ($bool) {
			$this->success('确认收货成功，返回订单首页中', U('Usercenter/order'), 3);
			exit;
		} else {
			$this->error('确认收货失败，返回订单首页中',  U('Usercenter/order'), 3);
			exit;
		}
	}

	/**
	 * 用户点击删除订单
	 * 这里是更改状态del的子弹，
	 */
	public function del() {
		
		if (empty($_GET['id'])) {

			$this->error('亲，你要删除哪一个订单收货？？',  U('Usercenter/order'));
			exit;
		}

		$model = M('Order');

		// 开启事务
		$model->startTrans();
		$bool = true;
		$del['del'] = 1;

		// 删除订单表
		$del = $model->where(['id' => $_GET['id']])->save($del);

		if (!$del) {
			$bool = false;
		}

		if ($bool) {

			$model->commit();
			$this->success('删除订单成功，返回订单首页中', U('Usercenter/order'), 3);
			exit;
		} else {

			$model->rollback();
			$this->error('删除订单失败，返回订单首页中',  U('Usercenter/order'), 3);
			exit;
		}
	}


	/**
	 * 用户点击取消订单
	 * 回滚，把库存还原
	 */
	public function exit() {

		if (empty($_GET['id'])) {

			$this->error('亲，你要取消哪一个订单？',  U('Usercenter/order'));
			exit;
		}
		// 用户ID
		$id = $_SESSION['user']['id'];

		$order = M('Order');
		$orderData = M('Orderdata');
		$goods = M('Goods');
		$detailed = M('Detailed');


		// 开启事务
		$order->startTrans();
		$bool = true;

		// 找到该用户选中的订单
		$orderDataPid = $orderData->where(['pid' => $_GET['id']])->select();
		foreach ($orderDataPid as $v ) {
			$goodsIds[] = $v['fid'];
			$goodsIds[] = $v['num'];
		}
		
		
		// 区分开每一组的库存和商品详情id
		$j = 0;
		for ($i = 0; $i < count($goodsIds); $i++) {

			$data[$j][] = $goodsIds[$i];
			if ($i % 2 ==1) {
				$j++;
			}
		}

		// 回滚库存
		foreach ($data as $k => $v) {
			
		
			// 修改库存的方法
			$save = $this->saveorder($v[0], $v[1]);
		}

		// 删除订单表
		$del = $order->where(['id' => $_GET['id']])->delete();

		if (!$del) {
			$bool = false;
		}

		// 删除详情表
		$delXq = $orderData->where(['pid' => $_GET['id']])->delete();

		if (!$delXq) {
			$bool = false;
		}

		if ($bool) {
			$order->commit();
			$this->success('取消订单成功，返回订单首页中', U('Usercenter/order'), 3);
			exit;
		} else {
			$order->rollback();
			$this->error('取消订单失败，返回订单首页中',  U('Usercenter/order'), 3);
			exit;
		}
	}

	/**
	 * 用户点击确认收货
	 */
	public function ok() {

		if (empty($_GET['id'])) {

			$this->error('亲，你要确定哪一个订单呢？',  U('Usercenter/order'));
			exit;
		}

		$order = M('Order');

		// 4的状态为立即评价
		$state['state'] = 4;

		// 开启事务
		$order->startTrans();
		$bool = true;

		$orderSave = $order->where(['id' => $_GET['id']])->save($state);

		if (!$orderSave) {
			$bool = false;
		}

		if ($bool) {

			$order->commit();
			$this->success('亲，谢谢您的光顾，确认收货成功', U('Usercenter/order'), 3);
			exit;

		} else {

			$order->rollback();
			$this->error('亲，您确认收货失败哦~',  U('Usercenter/order'));
			exit;
		}
	}

	/**
	 * 个人评价管理
	 * @Author: Drizzle           2017-11-29
	 * @return  arr
	 */
	public function commentlist()
	{
		// 模拟用户id
		$id = $_SESSION['user']['id'];

		// 查询出要评论的列表，第一查询订单表
		$order = M('Order');
		$orderTj['uid'] = $id;
		$orderTj['id'] = $_GET['id'];
		$orderTj['state'] = ['eq', 4];
		$orderArr = $order->where($orderTj)->field('id')->select();

		foreach ($orderArr as $v) {
			$ids[] = $v['id'];
		}
		
		if ($ids) {
			// 为了严谨，不直接查询详情表，所以通过评论表ID查询
			// 第二查询出订单表的详情数据    要评价的商品 PID相同的 
			$join = join(',', $ids);
			$orderDataTj = array('a.pid' => ['in', $join]);
			$orderData = M('Orderdata as a');
			$orderDataArr = $orderData->join('__DETAILED__ as b on a.fid = b.id', 'LEFT')->where($orderDataTj)->select();
		}
				
		// 商品ID和商品图片
		$this->assign('goodsList', $goodsList);
		
		// 订单表数据
		$this->assign('orderArr', $orderArr);

		// 订单详情表数据
		$this->assign('orderDataArr', $orderDataArr);

		$this->display();
	}

	/**
	 * 用户发表评论处理的方法
	 */
	public function pinlun(){


		// 区分开每一个组
		$count = count($_POST);
		for ($i = 0; $i < $count; $i++) {
			$fuck[$i] = array_column($_POST, $i);
		}

		// 删除多余的数组,删除后下标会顶上去，所以加2
		for ($i = 0; $i < count($fuck)+2; $i++) {
			if (empty($fuck[$i])) {
				unset($fuck[$i]);
			}
		}

		foreach ($fuck as $k => $v) {

			$gid[] = $v[0];
		}
		
		// 查找出商品详情 并通过他找出对应的商品
		$joinGid = join(',', $gid);
		$detailTj = array('id' => ['in', $joinGid]);
		$goodsDetail = M('Detailed')->where($detailTj)->select();
		
		// 追加detail的GID
		foreach ($goodsDetail as $v) {
			$goodsIds[] = $v['gid'];
		}
		$joinDetailGid = join(',', $goodsIds);
		$goodsTj = array('id' => ['in', $joinDetailGid]);
		$goodsId = M('Goods')->where($goodsTj)->field('id')->select();

		// 追加商品ID
		foreach ($goodsId as $v ) {
			$data[$k]['gid'] = $v['id'];
		}

		// 追加所有数据
		foreach ($fuck as $k => $v) {
			$data[$k]['pid'] = $v[1];
			$data[$k]['content'] = $v[2];
			$data[$k]['dafen'] = $v[3];
			$data[$k]['username'] = $_SESSION['user']['username'];
			$data[$k]['uid'] = $_SESSION['user']['id'];
			$data[$k]['atime'] = time();
		}

		// 添加进评论表
		$pinLun = M('Pinlun');
		$pinLun->startTrans();
		$bool = true;
		foreach ($data as $v) {

			$pinLunLast = $pinLun->add($v);
		}

		if (!$pinLunLast) {
			$bool = false;
		}
		
		// 更改订单状态
		$order = M('Order');
		$orderTj['state'] = 5;
		$orderSave = $order->where(['id' => $_POST['pid'][0]])->save($orderTj);

		if (!$orderSave) {
			$bool = false;
		}

		if ($bool) {

			$order->commit();
			$this->success('亲，评论成功，返回订单首页中', U('Usercenter/order'), 3);
			exit;

		} else {

			$order->rollback();
			$this->error('亲，评论失败，返回订单首页中~',  U('Usercenter/order'), 3);
			exit;

		}
	}

	/**
	 * 个人积分管理
	 * @Author: Drizzle           2017-11-29
	 * @return  arr
	 */
	public function points()
	{
		// 连接用户表
		$user = M('user');
		$data = $user->where(['id'=>session('user')['id']])->find();
		
		$order = M('order');
		$orders = $order->where(['uid'=>session('user')['id']])->find();
		
		// 获取当前时间的年月日时分秒
        $year 	= date('Y', $data['addtime']);
        $month 	= date('m', $data['addtime']);
        $day 	= date('d', $data['addtime']);
        $hour   = date('H', $data['addtime']);
        $minute = date('i', $data['addtime']);
        $second = date('s', $data['addtime']);

		$this->assign('data', $data);
		$this->assign('year', $year);
		$this->assign('month', $month);
		$this->assign('day', $day);
		$this->assign('hour', $hour);
		$this->assign('minute', $minute);
		$this->assign('second', $second);
		$this->assign('orders', $orders);
		$this->display();
	}

	/**
	 * 个人代币券查看
	 * @Author: Drizzle           2017-11-29
	 * @return  arr
	 */
	public function coupon()
	{
		$this->display();
	}

	/**
	 * 个人余额查看
	 * @Author: Drizzle           2017-11-29
	 * @return  arr
	 */
	public function walletlist()
	{
		$this->display();
	}

	/**
	 * 账单明细
	 * @Author: Drizzle           2017-11-29
	 * @return  [type]           
	 */
	public function bill()
	{
		$this->display();
	}

	/**
	 * 个人收藏
	 * @Author: Drizzle           2017-11-29
	 * @E-mail: Drizzle88@163.com
	 * @return  arr
	 */
	public function collection()
	{

		// 查询收藏表
		$uid = $_SESSION['user']['id'];
		
		$model = M('Goods as a');
		$arr = $model->join("__SHOUCANG__ as b on a.id = b.gid")->where('b.uid = '.$uid)->field('ymoney, image0, name, money, b.id')->select();

		// 分配收藏数据
		$this->assign('arr', $arr);

		$this->display();
	}

	/**
	 * 个人安全设置
	 * @Author: Drizzle 2017-11-29
	 * @return 
	 */
	public function safety()
	{
		$this->display();
	}

	/**
	 * 显示个人中心-安全设置-登录密码的页面
	 */
	public function password() 
	{
		$this->display();
	}

	/**
     *前台个人中心-个人资料-安全设置-修改密码(判断用户在原密码输入框输入的密码是否与原密码一致)
     */
    public function passwordJudge() {
        // 连接用户表
        $user = D('user');

        // 获取用户在原密码输入框输入的密码
        $oldpassword = I('post.oldpassword');

        // 获取修改密码用户的数据
        $data = $user->where(['id'=>session('user')['id']])->find();

        // 如果用户在原密码输入框输入的密码与用户在数据库的密码一致就返回1,否则就返回-1
        if(password_verify($oldpassword, $data['password'])) {
            $this->ajaxReturn('1');
        } else {
            $this->ajaxReturn('-1');
        }

    }

    /**
     * 修改密码
     */
    public function savePassword() {
        
        // 连接用户表
        $user = D('user');

        // 查询用户表的数据
        $data = $user->where(['id'=>session('user')['id']])->find();
        				
        if (empty(I('post.password'))) {
        	$this->error('请输入原密码');
        } else {

	        // 如果用户输入的新密码与确认密码一致 
	        if (I('post.newpassword') == I('post.confirmpassword')) {

	            $data['password'] = password_hash(I('post.newpassword'), PASSWORD_DEFAULT);

	            $save = $user->save($data);

	            if ($save) {
	                $this->success('修改成功,请您登录', U('Login/login'));
	            } else {
	                $this->error('修改失败,请重新修改');
	            }

	        } else {
	            $this->error('两次密码不一致,请重新输入');
	            exit;
	        }
        }				
        		
    }


    /**
	 * 显示个人中心-安全设置-手机验证的页面
	 */
	public function bindphone() 
	{
		$this->display();
	}


    /**
	 * 显示个人中心-安全设置-邮箱验证的页面
	 */
	public function email() 
	{

		$this->display();
	}

	/**
	 * 显示发送更换邮箱的页面
	 */
	public function replaceemail()
	{
		if (IS_POST) {
			 // 连接用户表
            $user = D('user');

            // 查询用户表的数据
            $data = $user->where(['id'=>session('user')['id']])->find();
            		
           	if (empty(I('post.emailcode'))) {
           		$this->error('请输入验证码');
           	} else {
           		
                // 获取要更改邮箱地址的用户名
                $username = $data['username'];

                // 获取要更换邮箱账户的用户的id
                $id = session('user')['id'];

                // 获取当前时间的年月日时分秒
                $year 	= date('Y', time());
                $month 	= date('m', time());
                $day 	= date('d', time());
                $hour   = date('H', time());
                $minute = date('i', time());
                $second = date('s', time());

                // 用vendor函数导入Vendor目录下的类库
                // vendor/PHPMailer/class.pop3.php
                vendor('PHPMailer.class', '', '.pop3.php');

                // vendor/PHPMailer/classphpmailer.php
                vendor('PHPMailer.classphpmailer');
               
                // 发送邮件,邮件上的url地址指向Login/activateAccount(激活账户)
                echo sendMail($data['email'], '零食商城邮箱验证提醒',"尊敬的".$username." 您好：,您于 ".$year."年".$month."月".$day."日 ".$hour."时".$minute."分".$second."秒 申请验证邮箱，点击以下链接，即可完成验证：<br><a href=http://192.168.32.108/www/abc/Shop/Home/Usercenter/updateMail?id=".$id.">http://www.lowewe.com/validate/verify/validIdentityMail.action?v=e13956ea8cd09b45ba41785b5c17c26c&type=updateEmail</a>");
                                        
                $this->success("已发送验证邮件至:{$_SESSION['user']['email']},验证邮件24小时内有效，请尽快登录您的邮箱点击验证链接完成验证。", 'sendIdentityValidMailSuccess');
                exit;    
           	}
            		
		}
		$this->display();
	}


	/**
	 * 显示成功发送验证邮件的页面
	 */
	public function sendIdentityValidMailSuccess()
	{
		$this->display();
	}

	/**
	 * 显示修改邮箱的界面
	 */
	public function updateMail()
	{
			// 连接用户表
            $user = D('user');

            // 查询用户表的数据
            $data = $user->where(['id'=>$_GET['id']])->find();
     
        if (IS_POST) {
           	if (empty(I('post.emailcode'))) {
           		$this->error('请输入验证码');
           	} else {
           		  
                // 获取要更改邮箱地址的用户名
                $username = $data['username'];
	            // 连接用户表
	            $user = D('user');

	            // 查询用户表的数据
	            $data = $user->where(['id'=>$_POST['id']])->find();
                // 获取要更换邮箱账户的用户的id
                $id = $data['id'];
                // 获取当前时间的年月日时分秒
                $year 	= date('Y', time());
                $month 	= date('m', time());
                $day 	= date('d', time());
                $hour   = date('H', time());
                $minute = date('i', time());
                $second = date('s', time());

                // 用vendor函数导入Vendor目录下的类库
                // vendor/PHPMailer/class.pop3.php
                vendor('PHPMailer.class', '', '.pop3.php');

                // vendor/PHPMailer/classphpmailer.php
                vendor('PHPMailer.classphpmailer');
               
                // 发送邮件,邮件上的url地址指向Usercenter/verificationmail(验证邮箱)
                echo sendMail(I('post.email'), '零食商城邮箱验证提醒',"尊敬的".$username." 您好：,您于 ".$year."年".$month."月".$day."日 ".$hour."时".$minute."分".$second."秒 申请验证邮箱，点击以下链接，即可完成验证：<br><a href=http://192.168.32.108/www/abc/Shop/Home/Usercenter/verificationmail?id=".$id."&email=".I('post.email').">http://www.lowewe.com/validate/verify/validIdentityMail.action?v=e13956ea8cd09b45ba41785b5c17c26c&type=updateEmail</a>");

	            $emailname = I('post.email');

                $this->success("已发送验证邮件至:".$emailname.",验证邮件24小时内有效，请尽快登录您的邮箱点击验证链接完成验证。", U('Usercenter/sendBindMailSuccess', ['emailname' => $emailname]));
                exit;    
           	}
        }         	
           		
		
				
		$this->display();
	}

	/**
	 * 显示已发送验证邮件的页面
	 */
	public function sendBindMailSuccess()
	{
		$this->display();
	}

	/**
	 * 验证邮箱
	 */
	public function verificationmail()
	{
		// 连接用户表
        $user = D('user');

        // 查询用户表的数据
        $data = $user->where(['id'=>$_GET['id']])->select();
        // 接收get传递过来的邮箱地址
        $data['email'] = $_GET['email'];
		
        // 获取当前时间的年月日时分秒
        $year 	= date('Y', time());
        $month 	= date('m', time());
        $day 	= date('d', time());
        $hour   = date('H', time());
        $minute = date('i', time());
        $second = date('s', time());

        // 用vendor函数导入Vendor目录下的类库
        // vendor/PHPMailer/class.pop3.php
        vendor('PHPMailer.class', '', '.pop3.php');

        // vendor/PHPMailer/classphpmailer.php
        vendor('PHPMailer.classphpmailer');

        $data = $user->where(['id'=>$_GET['id']])->save($data);

        if ($data) {
        	session('user')['email'] = $data['email'];

    	    echo sendMail($_GET['email'], '零食商城账户绑定邮箱变动提醒',"尊敬的".$username." 您好：,您于 ".$year."年".$month."月".$day."日 ".$hour."时".$minute."分".$second."秒 将账户邮箱 <a href='mailto:".session('user')['email']." target='_blank'>".session('user')['email']."</a> 变更为 <a href='mailto:".$_GET['email']."' target='_blank'>".$_GET['email']."</a> 。<br>若您未申请过更改邮箱，可能面临账户被盗风险，请尽快致电 400-123-4567 转 8 ，由客服协助您取回帐号。");
        	$this->success('更改邮箱成功', 'validBindMail');
        } else {
        	$this->error('抱歉，验证失败', 'validBindMailfail');
        }

	}

	/**
	 *显示邮箱验证成功的页面!
	 */
	public function validBindMail()
	{
		$this->display();
	}

	/**
	 *显示邮箱验证失败的页面!
	 */
	public function validBindMailfail()
	{
		$this->display();
	}

	/**
	 * 显示个人中心-安全设置-实名认证的页面
	 */
	public function idcard()
	{
		// 连接用户表
        $user = D('user');

        // 查询用户表的数据
        $data = $user->where(['id'=>session('user')['id']])->find();

		if (IS_POST) {
			// 连接用户表
	        $user = D('user');

	        // 查询用户表的数据
	        $data = $user->where(['id'=>session('user')['id']])->find();

	        // 如果用户还没有填写真实姓名,就接收用户填写的姓名,
	        // 如果用户已经填写真实姓名了,就不给用户填写姓名。
	        if(empty($data['tname'])) {
	        	$data['tname'] = I('post.tname');
	        }

	        if(empty($data['idcard'])) {
	        	$data['idcard'] = I('post.idcard');
	        }

	        $save = $user->save($data);
	        if ($save) {
	        	$this->success('修改个人资料成功', U('Usercenter/idcard'));
	        	exit;
	        } else {
	        	$this->error('修改个人资料失败,请重新修改');
	        	exit;
	        }

	        $this->assign('data', $data);
		}
        $this->assign('data', $data);
		$this->display();
	}

}	
