<?php
namespace Home\Controller;
use Think\Controller;
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
class UserController extends HomeController {

    /**
     * 用户登录 ajax请求
     * 用户登录成功后，把信息加密后存进cookie
     * 以后判断是加密后对比，如果通过则把cookie的数据存session 这样就可以免登陆
     * switch要求格式: 例class=User  方法: method=index
     */
    public function Login() {
        
        if (IS_AJAX && IS_POST) {
           
            if (!empty($_SESSION['Home']['userid'])) { // 说明已经登录了

                $userLogin['code'] = 0;
                $userLogin['msg'] = '请先退出当前账号';
                $this->ajaxReturn($userLogin);
                exit;
                
            }

            $phone = I('phone');
            $password = I('password');

            if (empty($phone)) {

                $userLogin['code'] = 0;
                $userLogin['msg'] = '手机不能为空';

                $this->ajaxReturn($userLogin);
                exit;

            }

            if (empty($password)) {

                $userLogin['code'] = 0;
                $userLogin['msg'] = '密码不能为空';

                $this->ajaxReturn($userLogin);
                exit;
            }

            $userInfo = M('UserInfo')
                ->where(array('UI_Mobile' => $phone))
                ->field('UI_ID as id, UI_Pwd as pwd, UI_Name as name, UI_ImgUrl as img, UI_Mobile as umobile, UI_Salt as salt')
                ->find();
            // $hash = password_verify($password, $userInfo['pwd']); // 密码解密
            $pwd = $userInfo['pwd'];
			$salt = $userInfo['salt'];

			$s_pwd = sha1($password.$salt);   //输入的密码+盐,加密对比

			if($s_pwd != $pwd){
			    
                $userLogin['code'] = 0;
                $userLogin['msg'] = '账号或密码错误';
                $this->ajaxReturn($userLogin);
                exit;
			} else {
                
                if ($_POST['url']['class'] && $_POST['url']['method']) {
                    
                    $url = '/index.php/Home/'.$_POST['url']['class'].'/'.$_POST['url']['method'];
                } else {

                    $url = '/index.php/Home/Index/index';
                }
                // if (I('class') && I('method')) {
                    
                //     $url = '/index.php/Home/'.I('class').'/'.I('method');
                // } else {

                //     $url = '/index.php/Home/Index/index';
                // }

                // 存进session
                $_SESSION['Home']['userid'] = $userInfo['id'];
                $_SESSION['Home']['name'] = $userInfo['name'];
                $_SESSION['Home']['img'] = $userInfo['img'];
                $_SESSION['Home']['umobile'] = $userInfo['umobile'];
                $_SESSION['Home']['login'] = true;



                //将登录信息，存放在Cookie中
                $User['id'] = $userInfo['id'];
                $User['name'] = $userInfo['name'];
                $User['umobile'] = $userInfo['umobile'];
                $_SESSION['UserInfo']  = $User;

                $Key = "phpsafe";
                $Value = serialize($User);
                $Str = md5($Value.$Key);
                setcookie('Login', $Str.$Value, time() + 86400, '/');

                $userLogin['code'] = 1;
                $userLogin['url'] = $url;
                $userLogin['msg'] = '登录成功';

                $this->ajaxReturn($userLogin);
                exit;
            }
        }

            $this->assign('json_url', json_encode($_GET)); // url地址
            $this->display();
    }

    /**
     * 用户注册 ajax请求
     * @param  $userName 用户名
     * @param  $phone 手机号
     * @param  $note 验证码
     * @param  $password1 第一次密码
     * @param  $password2 第二次密码
     */
    public function Register() {
        
        if (IS_AJAX && IS_POST) {
            
            // 用户名
            $userName = I('name');
            $phone = I('phone');
            $note = I('note');// 验证码
            $password1 = I('pwd1');
            $password2 = I('pwd2');

            if (!empty($_SESSION['Home']['userid'])) {

                $userData['code'] = 0;
                $userData['data'] = null;
                $userData['msg'] = '请退出当前账号再注册';

                $this->ajaxReturn($userData);
                exit;

            }

            if (empty($userName) || empty($password1) || empty($password2)) {

                $userData['code'] = 0;
                $userData['data'] = null;
                $userData['msg'] = '用户名或密码不能为空';

                $this->ajaxReturn($userData);
                exit;
            }

            if (empty($phone) || empty($note)) {

                $userData['code'] = 0;
                $userData['data'] = null;
                $userData['msg'] = '手机号或验证码不能为空';

                $this->ajaxReturn($userData);
                exit;
            }

            if ($password1 != $password2) {

                $userData['code'] = 0;
                $userData['data'] = null;
                $userData['msg'] = '两次密码不正确';

                $this->ajaxReturn($userData);
                exit;
            }

            $userModel = M('UserInfo');
            $yzName = $userModel->where(array('UI_Name' => $userName))->field('UI_Name')->find();
            if (!empty($yzName)) {

                $userData['code'] = 0;
                $userData['data'] = null;
                $userData['msg'] = '该用户名已经存在了,请换一个吧';
                $userData['succes'] = false;

                $this->ajaxReturn($userData);
                exit;
            }

            $yzPhone = $userModel->where(array('UI_Mobile' => $phone))->field('UI_Mobile')->find();
            if (!empty($yzPhone)) {
                $userData['code'] = 0;
                $userData['data'] = null;
                $userData['msg'] = '该手机号已经注册了,请换一个吧';

                $this->ajaxReturn($userData);
                exit;
            }

            $yzm = M('SmsTemp');
            $yzmWhere['ST_Message'] = $note;
            $yzmWhere['ST_Phone'] = $phone;
            $yzmSuccess = $yzm->where($yzmWhere)->field('ST_ID, ST_LoseTime, ST_Message')->find();
         
            if (time() > $yzmSuccess['ST_LoseTime']) {

                $userData['code'] = 0;
                $userData['data'] = null;
                $userData['msg'] = '验证码时间过期';

                $this->ajaxReturn($userData);
                exit;

            }
            

            // $password1 = password_hash($password1, PASSWORD_DEFAULT);   // 密码加密
           //盐
            $salt = salt();
            //把得出的盐拼接到密码的后面，再对其使用sha1进行哈希
            $password = sha1($password1.$salt);
            if (!empty($password)) { // 加密成功后才可以加数据库

                $data['UI_Name'] = $userName; // 用户名
                $data['UI_Status'] = 1; // 逛客注册
                $data['UI_Mobile'] = $phone; // 用户注册的手机号
                $data['UI_Pwd'] = $password; // 用户的登录密码
                $data['UI_Addtime'] = time(); // 用户注册的时间
                $data['UI_Salt'] = $salt;


                // 添加进用户表
                $userLastId = $userModel->add($data);
            }

            if (empty($userLastId)) {

                $userData['code'] = 0;
                $userData['data'] = null;
                $userData['msg'] = '注册失败';

            } else {

                $userData['code'] = 1;
                $userData['data'] = null;
                $userData['id'] = $userLastId;
                $userData['msg'] = '注册成功';

                // 存进session
                $_SESSION['Home']['userid'] = $userLastId;
                $_SESSION['Home']['name'] = $userName;
                $_SESSION['Home']['mobile'] = $phone;
                $_SESSION['Home']['login'] = true;

                
                //将登录信息，存放在Cookie中
                $User['id'] = $userLastId;
                $User['name'] = $userName;
                $User['umobile'] = $phone;
                $_SESSION['UserInfo']  = $User;

                $Key = "phpsafe";
                $Value = serialize($User);
                $Str = md5($Value.$Key);
                setcookie('Login', $Str.$Value, time() + 86400, '/');
                
            }

            // 协议（暂时不做)
            $this->ajaxReturn($userData);
            exit;
        }
        
        $this->display();
        
    }

    /**
     * 下一步，判断验证码和手机号是否正确的一个方法 ajax请求
     * @return $phone 手机号
     * @param  $code   验证码
     */
    public function forget() {
        if (IS_AJAX && IS_POST) {

            $phone = I('phone');
            $note = I('note');
            if (empty($phone) || empty($note)) {

                $userData['code'] = 0;
                $userData['data'] = null;
                $userData['msg'] = '手机号或验证码不能为空';

                $this->ajaxReturn($userData);
                exit;
            }

            $yzm = M('SmsTemp');
            $yzmWhere['ST_Message'] = $note;
            $yzmWhere['ST_Phone'] = $phone;
            $yzmSuccess = $yzm
                ->where($yzmWhere)
                ->field('ST_ID, ST_LoseTime, ST_Message, ST_Phone')
                ->find();

            if (empty($yzmSuccess)) {

                $userData['code'] = 0;
                $userData['data'] = null;
                $userData['msg'] = '验证码或手机号输入不正确';

                $this->ajaxReturn($userData);
                exit;

            }
           
            if (time() > $yzmSuccess['ST_LoseTime']) {

                $userData['code'] = 0;
                $userData['data'] = null;
                $userData['msg'] = '验证码时间过期';

                $this->ajaxReturn($userData);
                exit;

            }

            // 所有判断都通过了
            $userData['code'] = 1;
            $userData['data'] = null;
            $userData['msg'] = '验证通过';

            $this->ajaxReturn($userData);
            exit;
        }
        $this->display();
    }



    /**
     * 退出登录
     */
    public function logout(){

        $_SESSION['Home']['userid'] = null;
        $_SESSION['Home']['name'] = null;
        $_SESSION['Home']['img'] = null;
        $_SESSION['Home']['mobile'] = null;
        $_SESSION['Home']['login'] = true;
        $_SESSION['UserInfo'] = null;
        setcookie('Login','',time()-86400,'/');


        $this->ajaxReturn(1);  //退出成功
    } 

    /**
     * 忘记密码 -> 短信验证通过 -> 输入两次密码的页面
     * @param $phone 手机号
     * @param $password1 第一次密码
     * @param $password2 第二次密码
     */
    public function finish() {

        if (IS_AJAX && IS_POST) {

            $password1 = I('password1');
            $password2 = I('password2');
            $phone = I('phone');

            if (empty($password1) || empty($password2)) {

                $userData['code'] = 0;
                $userData['data'] = null;
                $userData['msg'] = '检测密码是否为空';

                $this->ajaxReturn($userData);
                exit;
            }

            if ($password1 != $password2) {

                $userData['code'] = 0;
                $userData['data'] = null;
                $userData['msg'] = '两次密码不相符';

                $this->ajaxReturn($userData);
                exit;
            }

            // $save['UI_Pwd'] = password_hash($password1, PASSWORD_DEFAULT); // 哈希加密
            // 
            //盐
            $salt = salt();
            //把得出的盐拼接到密码的后面，再对其使用sha1进行哈希
            $password = sha1($password1.$salt);

            $save['UI_LastUpdateTime'] = time(); // 当前时间
            $save['UI_Pwd'] = $password; // 当前时间
            $save['UI_Salt'] = $salt; // 当前时间

            $saveWhere['UI_Mobile'] = $phone; // 条件
            $saveUserPassword = M('UserInfo')
                ->where($saveWhere)
                ->save($save);

                if (empty($saveUserPassword)) {

                    $userData['code'] = 0;
                    $userData['data'] = null;
                    $userData['msg'] = '修改密码失败';

                } else {
                   
                    $userData['code'] = 1;
                    $userData['data'] = null;
                    $userData['msg'] = '修改密码成功';

                }
                $this->ajaxReturn($userData);
                exit;
        }
    }
 


     /**
     * 生成短信验证码
     * @param  integer $length [验证码长度]
     */
    public function createSMSCode($length = 4){
        $min = pow(10 , ($length - 1));
        $max = pow(10, $length) - 1;
        return rand($min, $max);
    }

     /**
     * 发送验证码
     * @param  [integer] $phone [手机号]
     */
    public function send_phone(){
        $phone = I('phone');
        $state = (int)I('state');
        $sms = M('sms_rec');
        $res = $sms->where(array('SR_ID'=>1))->find();
        if($res){
            $result = json_decode($res['SR_CodeAndSign'],true);
        }else{
            $flag = -1;
            $this->ajaxReturn($flag);
        }
        if($state){
            if($state == 1){//注册
                $sms_code = $result[0]['code']; 
                $sign = $result[0]['sign'];
            }elseif($state == 2){//忘记密码
                $sms_code = $result[2]['code'];
                $sign = $result[2]['sign'];
            }
        }else{
            $flag = -1;
            $this->ajaxReturn($flag);
        }

        $code=$this->createSMSCode($length = 4);

        require_once  './Dysms/vendor/autoload.php';    //此处为你放置API的路径
        // require_once  './Api/dysms/vendor/autoload.php';    //此处为你放置API的路径
        Config::load();             //加载区域结点配置

        $accessKeyId = $res['SR_Key']; // 短信key
        $accessKeySecret = $res['SR_Secret'];//短信secret
        $templateCode = $res['SR_TemplateID'];   //短信模板ID

        //短信API产品名（短信产品名固定，无需修改）
        $product = "Dysmsapi";

        //短信API产品域名（接口地址固定，无需修改）
        $domain = "dysmsapi.aliyuncs.com";

        //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
        $region = "cn-hangzhou";

        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

        // 增加服务结点
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);

        // 初始化AcsClient用于发起请求
        $acsClient = new DefaultAcsClient($profile);

        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        // 必填，设置短信接收号码
        $request->setPhoneNumbers($phone);

        // 必填，设置签名名称
        $request->setSignName($sign);

        // 必填，设置模板CODE
        $request->setTemplateCode($sms_code);

        $smsData = array('code'=>$code);    //所使用的模板若有变量 在这里填入变量的值  我的变量名为username此处也为username

        //选填-假如模板中存在变量需要替换则为必填(JSON格式),友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
        $request->setTemplateParam(json_encode($smsData));

        //发起访问请求
        $acsResponse = $acsClient -> getAcsResponse($request);
        //返回请求结果
        $result = json_decode(json_encode($acsResponse), true);
        $resp = $result['Code'];
        $resu = $this->sendMsgResult($resp,$phone,$code);
        $this->ajaxReturn($resu);
        // dump($resu);
    }

    /**
     * 验证手机号是否发送成功  前端用ajax，发送成功则提示倒计时，如50秒后可以重新发送
     * @param  [json] $resp  [发送结果]
     * @param  [type] $phone [手机号]
     * @param  [type] $code  [验证码]
     * @return [type]        [description]
     */
    private function sendMsgResult($resp,$phone,$code){
        $sms = M('Sms_temp');
        if ($resp == "OK") {
            $ret = $sms->where(array('ST_Phone'=>$phone))->find();
            if($ret){
                // 存在,则更新验证码跟时间
                $dat['ST_Message'] = $code;
                $dat['ST_LoseTime'] = time()+1800;
                $result = $sms->where(array('ST_Phone'=>$phone))->save($dat);
            }else{
                // 不存在，则添加
                $date['ST_Phone'] = $phone;
                $date['ST_Message'] = $code;
                $date['ST_LoseTime'] = time()+1800;
                $result = $sms->add($date);
            }

            if($result){
                $flag = 1;
                // $data="发送成功";
            }else{
                $flag = -1;
                // $data="发送失败";
            }
        } else{
            $flag = -1;
            // $data="发送失败";
        }
        return $flag;
    }


}