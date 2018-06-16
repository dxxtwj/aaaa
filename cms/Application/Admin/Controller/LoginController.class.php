<?php
namespace Admin\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class LoginController extends Controller {
    public function index(){
    	if(IS_POST){
            // dump($_SESSION);
            // exit;
    		$map['SA_Name'] = trim(I('username'));
            $password = trim(I('password'));
            $is_savePwd = (int)I('is_savePwd');
            $admin=D('Sys_admin')->where($map)->find();

            if(empty($admin)){
                
                $this->error('用户不存在');
                
            }elseif(!empty($admin)&&$admin['SA_State']==1){

                $pwd = sha1($password.$admin['SA_Salt']);

                if($pwd == $admin['SA_Pwd']){
                    //将登录信息，存放在Cookie中
                    $Admin['username'] = $map['SA_Name'];
                    $Admin['password'] = $pwd;
                    unset($admin['SA_Salt']);
                    $_SESSION['AdminInfo']  = $Admin;
                	if($is_savePwd == 1){
                        $Key="phpsafe";
                        $Value = serialize($Admin);            
                        $Str   = md5($Value.$Key);
                        setcookie('AdminLogin',$Str.$Value,time()+86400,'/');
                	}else{
                        setcookie('AdminLogin','',time()-86400,'/');
                    }
                    $_SESSION['Admin']['islogin']=true;
                    $_SESSION['Admin']['username']=$admin['SA_Name'];
                    $_SESSION['Admin']['info']=$admin;
                    $_SESSION['Admin']['password']=$admin['SA_Pwd'];
                    write_adminLog();
                    redirect(U('Index/index'));
                   
                }else{
                	$this->error('密码错误');
	                
                }
            }elseif($admin['SA_State']==0){
                    
                $this->error('用户被禁用');
            }

    	}else{
            // dump($_SESSION['Admin']);
        	$this->display();
    	}
    }

    public function logout(){
        setcookie('AdminLogin','',time()-86400,'/');
        $_SESSION['Admin']['username'] = null;
        $_SESSION['Admin']['info'] = null;
        $_SESSION['Admin']['password'] = null;
        $_SESSION['Admin']['islogin'] = false;
        $_SESSION['AdminInfo'] = null;
        $this->redirect('Login/index');
    }

}