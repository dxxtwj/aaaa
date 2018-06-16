<?php
namespace Admin\Controller;
use Think\Controller;

class CommonController extends Controller {
    
    public function _initialize()
    {
    	// if(!$_SESSION['Admin']['islogin']){
    	// 	// $this->redirect('Login/index');
    	// 	// $this->error('没有权限访问', U('Index/index'));
    	// }
        $resu = $this->SelectCookie();
        // dump($resu);
        if((int)$resu == -1){
           $this->redirect('Login/index');
        }
    	import('Think.Auth');//加载类
    	$auth = new \Think\Auth();
    	$name = CONTROLLER_NAME."/".ACTION_NAME;
    	//  || ($name!='Index/home' && $name!='Index/index')
        // dump($_SESSION);
    	if($_SESSION['Admin']['info']['SA_ID']!=1){
	    	if(!$auth->check($name,$_SESSION['Admin']['info']['SA_ID'])){

	    		if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){ 
	    			$this->ajaxReturn(-99);
				}else{ 
	    			$this->error('你没有权限');
	    			// echo MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME.'.....你没有权限';
				};
	    		exit;
	    	}
    	}

    }

    //检测是否登陆
    public function SelectCookie(){
        // dump($_SESSION);
        if(empty($_SESSION['AdminInfo']['username']) || empty($_SESSION['Admin']['username'])){
            $Value = $_COOKIE['AdminLogin'];
            // 去掉魔术引号
            if(get_magic_quotes_gpc()){
                $Value = stripslashes($Value);
            }
            $Key="phpsafe";
            $Str = substr($Value,0,32);
            $Value = substr($Value,32);
            //校验
            if(md5($Value.$Key) == $Str){
                $User  = unserialize($Value);
                $admin = D('Sys_admin')->where(array('SA_Name'=>$User['username']))->find();
                if($admin){
                    if($User['password'] == $admin['SA_Pwd']){
                        unset($admin['SA_Salt']);
                        $_SESSION['AdminInfo'] = $User;
                        $_SESSION['Admin']['username'] = $User['username'];
                        $_SESSION['Admin']['info'] = $admin;
                        $_SESSION['Admin']['password'] = $User['password'];
                        $_SESSION['Admin']['islogin'] = true;
                        $flag = 1;
                        return $flag;
                    }else{
                        setcookie('AdminLogin','',time()-86400,'/');
                        $_SESSION['Admin']['username'] = null;
                        $_SESSION['Admin']['info'] = null;
                        $_SESSION['Admin']['password'] = null;
                        $_SESSION['Admin']['islogin'] = false;
                        $_SESSION['AdminInfo'] = null;
                        $flag = -1;
                        return $flag;
                    }
                    
                }else{
                    //重新登入
                    setcookie('AdminLogin','',time()-86400,'/');
                    $_SESSION['Admin']['username'] = null;
                    $_SESSION['Admin']['info'] = null;
                    $_SESSION['Admin']['password'] = null;
                    $_SESSION['Admin']['islogin'] = false;
                    $_SESSION['AdminInfo'] = null;
                    $flag = -1;
                    return $flag;
                }
            }else{
                //重新登入
                $flag = -1;
                return $flag;
            }
        }else{
            $admin = D('Sys_admin')->where(array('SA_Name'=>$_SESSION['Admin']['username']))->find();
            if($admin){
                if($_SESSION['Admin']['password'] == $admin['SA_Pwd']){
                    $flag = 1;
                    return $flag;
                }else{
                    setcookie('AdminLogin','',time()-86400,'/');
                    $_SESSION['Admin']['username'] = null;
                    $_SESSION['Admin']['info'] = null;
                    $_SESSION['Admin']['password'] = null;
                    $_SESSION['Admin']['islogin'] = false;
                    $_SESSION['AdminInfo'] = null;
                    $flag = -1;
                    return $flag;
                }
                
            }else{
                //重新登入
                setcookie('AdminLogin','',time()-86400,'/');
                $_SESSION['Admin']['username'] = null;
                $_SESSION['Admin']['info'] = null;
                $_SESSION['Admin']['password'] = null;
                $_SESSION['Admin']['islogin'] = false;
                $_SESSION['AdminInfo'] = null;
                $flag = -1;
                return $flag;
            }
        }

    }

    

}
