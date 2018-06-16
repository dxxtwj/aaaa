<?php
namespace Admin\Controller;
use Think\Controller;

/*管理员控制器*/
class AdminuserController extends CommonController
{
	/**
	 * 所有管理员的信息
	 * @Author: Drizzle           2017-11-17
	 * @E-mail: Drizzle88@163.com
	 */
	public function admin_list()
	{	
		//拿到用户列表数据
		//echo session('id');
		$user = D('AdminUser');
		$arr = $user->user_selectall();

		//拿到角色表的数据,用于添加
		$user2 = M('admin_role');
		$role = $user2->select();
		//dump($role);
		//分配数据
		$this->assign('arrall',$arr);
		$this->assign('roleall',$role);
		$this->display();

	}
	
	/**
	 * 管理员个人信息以及登录信息
	 * @Author: Drizzle           2017-11-16
	 * @E-mail: Drizzle88@163.com
	 */
	public function personal_details()
	{	
		//通过D函数找到个人数据
		$user = D('AdminUser');
		$arr = $user->user_select();
		//获取当前用户id
		$aid = $arr['id'];
		//找到登录记录
		$userip = M('admin_ip');
		//如果iplock为2则可以查看所有登录记录,否则只能看自己的记录
		if($arr['iplock']=='2'){
			$arrid = $userip->select();
		}else{
			$arrid = $userip->field(true)->where("uid='{$aid}'")->select();
		}
		//找到用户的身份字段
		$rid = $arr['role'];
		//找到当前用户的身份
		$role = M('admin_role');
		$rolename = $role->field('rolename')->where("id='{$rid}'")->find();
		//分配数据,登录信息,用户个人信息,个人信息
		$this->assign('aid',$arrid);
		$this->assign('admin',$arr);
		$this->assign('rolename',$rolename);
		$this->display();
	}

	/**
	 * 添加管理员
	 * @Author: Drizzle           2017-11-17
	 * @E-mail: Drizzle88@163.com
	 */
	public function admin_add()
	{	
		$user = M('admin_user');
		//AJAX用户名认证
		if(IS_AJAX){

			$name = $_POST['name'];
			//判断是否为纯数字
			if(preg_match('/^\d+$/',$name)){
               	echo 3;exit;
           	}
			$k = $user->field('username')->where("username='{$name}'")->find();
			if($k['username']==null){
				//可以添加 
				echo 1;exit;
			}else{
				//已经存在
				echo 2;exit;
				}
			}
		
		//添加认证
		if(IS_POST){
			//判断用户名是否合法
			//dump($_POST);
			//得到添加的角色
			$arr = array_slice($_POST,6);

			if(empty($_POST['username'])){
				$this->success('用户名不能为空哦...','admin_list','3');
				exit;
			}elseif(!preg_match('/^\w{4,10}$/',$_POST['username'])){
				$this->success('用户名不合法哦...','admin_list','3');
				exit;
			}
			//判断用户名是否为纯数字
			if(preg_match('/^\d+$/',I('post.namejj'))){
                $this->success('用户名不能为纯数字...','admin_list','3');
				exit;  
            }

			//判断密码是否合法
			if(empty($_POST['password'])){
				$this->success('密码不能为空哦...','admin_list','3');
				exit;
			}elseif(preg_match("/*{4}/",$_POST['password'])){
				$this->success('密码不能小于四位哦...','admin_list','3');
				exit;
			}
			//判断电话号码是否合法
			if(empty($_POST['phone'])){
				$this->success('电话号码空空哦...','admin_list','3');
			}elseif(!preg_match("/^1[34578]\d{9}$/",$_POST['phone'])){
				$this->success('请填写中国大陆号码哦...','admin_list','3');
			}
			//判断邮箱是否合法
			if(empty($_POST['mail'])){
					$this->success('邮箱码空空哦...','admin_list','3');
			}elseif(!preg_match("/^\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}$/",$_POST['mail'])){
					$this->success('邮箱不符合格式哦...','admin_list','3');
			}
			//判断用户是否已存在
			$name = $_POST['username'];
			$k = $user->field('username')->where("username='{$name}'")->find();
			if($k['username']==null){
				//通过可以添加	
				//密码验证
				if($_POST['password']!=$_POST['password2']){
					$this->success('两次密码不一致哦...','admin_list','3');
					exit;
				}
				//密码转换
				$_POST['password'] = password_hash($_POST['password'],PASSWORD_DEFAULT);
				//添加时间
				$_POST['atime'] = time();
				//加入数据库
				$lid = $user->add($_POST);
				//添加成功后再添加到角色表->获取刚添加用户的id->new出role角色表
				$aid = $user->field('id')->where("username='{$name}'")->find();
				$aid = $aid['id'];
				$role = M('admin_user_role');
				//遍历角色表->得到单个角色id->将角色id作为rid->用户id作为aid->执行添加
				foreach ($arr as $v) {
						//echo $v;
						$role->add(['aid'=>$aid,'rid'=>$v]);
				}
			
				if(!empty($lid)){
					$this->success('已添加成功,正在处理,请骚后哦...','admin_list','3');
					exit;
				}else{
					$this->success('年轻的樵夫,添加失败哦...','admin_list','3');
					exit;
				}
			}else{
				//已经存在
				$this->success('用户名已存在哦...','admin_list','3');
				exit;
				}
		}
	}

	/**
	 * AJAX删除管理员单条
	 * @Author: Drizzle           2017-11-20
	 * @E-mail: Drizzle88@163.com
	 * @return  int  删除的条数
	 */
	public function delete()
	{
		if(IS_AJAX){
			$id = $_POST['id'];
			
			//不能删除自己	
			if(session('id')==$id){
				echo 2;exit;
			}
			//判断是否等于1,等于1不能删除	
			if($id == 1){
				echo 2;exit;
			}

			$user = M('admin_user');
			
			$deid = $user->where("id='{$id}'")->delete();
			echo $deid;
		}
	}

	/**
	 * AJAX禁用管理员
	 * @Author: Drizzle           2017-11-20
	 * @E-mail: Drizzle88@163.com
	 * @return  int 禁止的条数
	 */
	public function forbidden()
	{
		if(IS_AJAX){
			$id = $_POST['id'];
			//不能禁用自己	
			if(session('id')==$id){
				echo 2;exit;
			}
			//判断是否等于1,等于1不能禁用
			if($id == 1){
				echo 2;exit;
			}
			$user = M('admin_user');
			$sid = $user-> where("id='{$id}'")->setField('status',2);
			echo $sid;
		}
	}

	/**
	 * AJAX解除禁用管理员 
	 * @Author: Drizzle           2017-11-20
	 * @E-mail: Drizzle88@163.com
	 * @return  int 解除的条数
	 */
	public function start()
	{
		if(IS_AJAX){
			$id = $_POST['id'];
			$user = M('admin_user');
			$sid = $user->where("id='{$id}'")->setField('status',1);
			echo $sid;
		}
	}

	/**
	 * 修改个人信息
	 * @Author: Drizzle           2017-11-21
	 * @E-mail: Drizzle88@163.com
	 * @return int 修改的条数
	 */
	public function revamp()
	{
		$name = $_POST['username'];
		//判断用户名是否合法
	    if(empty($_POST['username'])){
			$this->success('不合法...','personal_details','3');
			exit;
		}elseif(!preg_match('/^\w{4,10}$/',$_POST['username'])){
			$this->success('不合法...','personal_details','3');
			exit;
		}
		//判断用户名是否为纯数字
		if(preg_match('/^\d+$/',$name)){
            $this->success('不合法...','personal_details','3');
			exit;  
        }
		$user = M('admin_user');

		$k = $user->field('username')->where("username='{$name}'")->find();
		if( $k['username']==$name || $k['username']==null){
			$said = $user->where("username='{$name}'")->save($_POST); 
			if($said==1){	
				$this->success('修改成功...','personal_details','3');
			}else{
				$this->success('您什么都没改到...','personal_details','3');
			}
		}else{
			$this->success('修改的用户名已存在...','personal_details','3');
			exit; 

		}

	}

	/**
	 * 修改密码
	 * @Author: Drizzle           2017-11-27
	 * @E-mail: Drizzle88@163.com
	 * @return  bool
	 */
	public function pwd_amend()
	{	
		if(empty($_POST['pwd2'])){
			echo "<script>alert('密码不合法 o(╥﹏╥)o')</script>"; 
           	$this->display('Index/home');
			exit;
		}elseif(preg_match("/*{4}/",$_POST['pwd2'])){
			echo "<script>alert('密码不合法 o(╥﹏╥)o')</script>"; 
			$this->display('Index/home');
			exit;
		}

		if($_POST['pwd2']!=$_POST['pwd3']){
			echo "<script>alert('密码不一致 o(╥﹏╥)o')</script>"; 
            $this->display('Index/home');
            exit;
		}
		$name = session('admin');
		$user = M('admin_user');
		$said = $user->field('password')->where("username='{$name}'")->find();
		if(password_verify($_POST['pwd1'],$said['password'])){
		 	$pwd = password_hash($_POST['pwd2'],PASSWORD_DEFAULT);
		 	$idbool = $user->field('password')->where("username='{$name}'")->save(['password'=> $pwd]);
		 	if($idbool==1){
		 		//session()=null;
		 	    echo "<script>alert('修改成功 请重新登录o(╥﹏╥)o')</script>";
            	$this->display('Login/login');
            	exit;
		 	}else{
		 		echo "<script>alert('未知错误,未修改成功 o(╥﹏╥)o')</script>"; 
            	$this->display('Index/home');
            	exit;
		 	}
		}else{
		  	echo "<script>alert('原密码错误 o(╥﹏╥)o')</script>"; 
            $this->display('Index/home');
            exit;
		  }

		
	}

	/**
	 * 管理员权限列表
	 * @Author: Drizzle           2017-11-16
	 * @E-mail: Drizzle88@163.com
	 */
	public function authority_list()
	{	
		$role = M('admin_role')->select();
		$user = M('admin_user')->field('id,username')->select();
		$ule = M('admin_user_role')->field('aid,rid')->select();
		$this->assign('ule',$ule);
		$this->assign('user',$user);
		$this->assign('role',$role);
		$this->display();
	}

	/**
	 * 权限管理添加页面
	 * @Author: Drizzle           2017-11-21
	 * @E-mail: Drizzle88@163.com
	 */
	public function authority_add()
	{	
		if(IS_POST){
			$arr =$_POST;
			//获取角色名
			$name = $_POST['rolename'];
			//前连个字段加入role表
			$role = array_slice($arr,0,2);
			$user = M('admin_role')->add($role);
			//拿到添加角色id $rid 添加的角色id
			$rids = M('admin_role')->field('id')->where("rolename='{$name}'")->find();
			$rid = $rids['id'];
			
			//后面的字段的id加入到关联表
			$uarr = array_slice($arr,2);
			//将用户的id和角色id添加到关联表
			$user_role = M('admin_user_role');

			foreach ($uarr as $v) {
			//拿到用户id $v
				$user_role->add(['aid'=>$v,'rid'=>$rid]);
				
			}//添加完毕
			$this->success('添加成功...','authority_list','3');	
			exit;
		}
		$user = M('admin_user')->field('id,username')->select();
		$this->assign('user',$user);
		$this->display();
	}

	/**
	 * 删除权限
	 * @Author: Drizzle           2017-11-22
	 * @E-mail: Drizzle88@163.com
	 * @return  int 删除的条数
	 */
	public function authority_delete()
	{
		if(IS_AJAX){
			$id = $_POST['id'];
			$user = M('admin_role');
			$user2 = M('admin_user_role')->where("rid ='{$id}'")->delete(); 
			$deid = $user->where("id='{$id}'")->delete();
			if($deid==1){
				echo 1;exit;
			}
		}
	}

	/**
	 * 权限分配
	 * @Author: Drizzle           2017-11-24
	 * @E-mail: Drizzle88@163.com
	 * @return  [type]            [description]
	 */
	public function authority_distribution()
	{
		$rid = $_POST['rid'];
		$arr = $_POST;
		$arr = array_slice($arr,1);
		//dump($arr);exit;
		$user = M('admin_role_node');
		$user->where("rid='{$rid}'")->delete();
		foreach ($arr as $v) {
			$res = $user->add(['rid' =>$rid,'nid'=>$v]);
		}
		if($res){
			$this->success('分配成功...','authority_list','3');
		}else{
			$this->error('分配失败...',U('Node/compile_node'));
		}

	}

}