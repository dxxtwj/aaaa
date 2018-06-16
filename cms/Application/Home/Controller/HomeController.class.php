<?php
namespace Home\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class HomeController extends Controller {
	public function _initialize(){
		//判断
        // if(is_mobile()){
        //     //设置默认默认主题为 Mobile
        //     // C('DEFAULT_THEME','Home');
        // }else{ 
        //     // C('DEFAULT_THEME','HomePC'); 
        //     C('DEFAULT_V_LAYER','PCView');    
        // }
		
	}

	/**
     * 检测登录(免登陆)，如果通过则把cookie里面的信息
     * 取出来存进session
     */
    public function SelectCookie(){
        $info = M('User_info');
        if(empty($_SESSION['UserInfo']['id']) || empty($_SESSION['Home']['userid'])){
            $Value = $_COOKIE['Login'];
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
                $info_res = $info->field('UI_ID, UI_ImgUrl, UI_Name')->where(array('UI_ID'=>$User['id']))->find();
                if($info_res){
                    $_SESSION['UserInfo'] = $User;
                    $_SESSION['Home']['userid'] = $User['id'];
                    $_SESSION['Home']['umobile'] = $User['umobile'];
                    $_SESSION['Home']['name'] = $info_res['UI_Name'];
                    $_SESSION['Home']['login'] = true;
                    $flag = 1;
                    return $flag;
                }else{
                    //重新登入
                    setcookie('Login','',time()-86400,'/');
                    $_SESSION['Home']['userid'] = null;
                    $_SESSION['Home']['umobile'] = null;
                    $_SESSION['Home']['login'] = false;
                    $_SESSION['Home']['name'] = null;
                    $_SESSION['UserInfo'] = null;
                    $flag = -1;
                    return $flag;
                }
            }else{
                //重新登入
                $flag = -1;
        		return $flag;
            }
        }else{
           
            $info_res = $info->field('UI_ID')->where(array('UI_ID'=>(int)$_SESSION['Home']['userid']))->find();
            if($info_res){
                $flag = 1;
                return $flag;
            }else{
                //重新登入
                setcookie('Login','',time()-86400,'/');
                $_SESSION['Home']['userid'] = null;
                $_SESSION['Home']['umobile'] = null;
                $_SESSION['Home']['login'] = false;
                $_SESSION['UserInfo'] = null;
                $flag = -1;
                return $flag;
            }
        }

    }

    public function is_mobile(){ 
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte"); 
        $is_mobile = false; 
        foreach ($mobile_agents as $device) {//这里把值遍历一遍，用于查找是否有上述字符串出现过 
            if (stristr($user_agent, $device)) { //stristr 查找访客端信息是否在上述数组中，不存在即为PC端。 
              $is_mobile = true; 
              break; 
            } 
        } 
        return $is_mobile; 
    }


    public function Common(){
        $info = M('User_info');

        if(IS_AJAX && IS_POST){
            $res = $this->SelectCookie();
            if((int)$res == 1){
                $uname =(int)$_SESSION['Home']['userid'];
                $us_info = $info->field('UI_Name')->where(array('UI_ID'=>$uname))->find();
                if($us_info){
                    $flag['state'] = 1;
                    $flag['uname'] = $us_info['UI_Name'];
                }else{
                    $flag['uname'] = '';
                }
            }else{
                $flag['state'] = -1;
            }
        }else{
            $flag['state'] = -2;
            $flag['mes'] = '非法的请求';
        }
        $this->ajaxReturn($flag);
    }

}