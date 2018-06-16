<?php
namespace Home\Controller;
use Think\Controller;

 /**
 * 前台登录控制器
 */
class LoginController extends Controller 
{
    
    //字段映射 
    // protected $_map = array(
    //      'namejj' =>'username',
    //      'pwdjj'  =>'password');

/****************成员方法*********************/
     /**
     * 处理登录方法
     * @Author: Roway           2017-11-21
     * @E-mail: 704525680@qq.com
     */
    public function login()
    {   


        // 如果是POST提交数据则执行此判断
        if (IS_POST) { 
           
            // //判断用户名是否为空
            // if(empty(I('post.username'))){
            //     echo "<script>alert('用户名不能为空')</script>"; 
            //     $this->display('Login/login');
            //     exit;
            // }
            
            // //判断密码是否为空
            //  if(empty(I('post.password'))){
            //     echo "<script>alert('密码不能为空')</script>"; 
            //     $this->display('Login/login');
            //     exit;
            // }

            // //判断验证码是否为空
            // if(empty(I('post.yzm'))){
            //     echo "<script>alert('验证码不能为空')</script>"; 
            //     $this->display('Login/login');
            //     exit;
            // }

            // //判断验证码
            // $verify = new \Think\Verify(); 
            // if(!$verify->check(I('post.yzm'))){
            //     echo "<script>alert('验证码不正确')</script>"; 
            //     $this->display('Login/login');
            //     exit;
            // }

            //正则判断
            // if(!preg_match("/^\w{4,20}+$/", $name)){
            //     echo "<script>alert('用户名不合法 o(╥﹏╥)o')</script>"; 
            //     $this->display('Login/login');
            //     exit;
            // }

            // 连接用户表
            $user = D('user');

            //获取表单中的用户名
            $name = I('post.username');

            //判断是否多次错误
            if(session('cs'.$name)=='4') {
                redirect('https://www.360.cn');
                exit;
            }

            //将表单中的用户作为where条件查询密码段
            $arr = $user->field(true)->where(['username'=>$name])->find();
            
            //如果没有查到数据则跳转登录页
            if(empty($arr)){
                echo "<script>alert('用户名或密码错误^_^')</script>"; 
                $this->display('Login/login');
                exit;
            }           
               
            //判断账户是否激活
            if($arr['activation']=='0'){
                echo "<script>alert('继续操作前请先到注册邮箱点击激活邮件激活您的帐号，若没有收到邮件请点击下方重发激活邮件')</script>";
                $this->display('Login/login');
                exit;
            } 

            //将获取的密码与hash进行比较,如果为false则跳转登录页
            if( password_verify(I('post.password'),$arr['password']) ){

                //判断是否被禁用
                if($arr['status']=='2'){
                    echo "<script>alert('管理账号过期')</script>";
                    $this->display('Login/login');
                    exit;
                }

                //记录登录时间用于比对是否被占领,比对缓存的登录时间与数据库的时间是否一致?
                $arr['logtime'] = md5(time().mt_rand(1,999999999));

                session('log',$arr['logtime']);
                $user->save($arr);
                //登录成功,显示页面并写入缓存
                session('user',$arr);
                        
                //记录登录IP记录
                // $userid = ['uid'=>$arr['id'],'ip'=>getip(),'time'=>time(),'Homename'=>$arr['username']];
                //$adip = M('Home_ip');
                //$adip->add($userid);

                // 跳转到商城前台首页
                $this->success('登录成功', U('Index/index'));

                //如果用户要记录密码,则写入缓存
                if(I('post.jizhu')=='1'){
                    session('adname',I('post.username'),7200);
                    session('adpwd',I('post.password'),7200);
                }
                
            }else{
                
                //密码错误进入此区域
                //密码错误限定次数,拼接用户名,限定单个用户的登录次数.限定4次错误机会
                if(session('cs'.$name)==false){
                    session('cs'.$name,'1',300);
                    echo "<script>alert('密码第一次输入错误')</script>";
                    $this->display();
                    exit;
                }elseif(session('cs'.$name) =='1'){
                    session('cs'.$name,'2',300);
                    echo "<script>alert('密码第二次输入错误')</script>"; 
                    $this->display();
                     exit;
                }elseif(session('cs'.$name)=='2'){
                    session('cs'.$name,'3',300);
                    echo "<script>alert('密码第三次输入错误')</script>"; 
                    $this->display();
                    exit;
                }elseif(session('cs'.$name)=='3'){
                     session('cs'.$name,'4',300);
                     echo "<script>alert('登录用户{$name}已被暂时禁用,请5分钟后再尝试')</script>"; 
                     $this->display();
                     exit;
                }

                echo "<script>alert('用户名或密码错误')</script>"; 
                $this->error();
                exit;
            }

            // $this->display();
        }else{
            //不是post提交的进入此区域
            $this->assign('adname',session('adname'));
            $this->assign('adpwd',session('adpwd'));
            $this->display();

        }
        
    }


    /**
     * 忘记密码
     */
    public function forgetPassword() {
        if (IS_POST) {
            // 连接用户表
            $user = D('user');

            $email = I('post.email');

            // 查询用户表账号的信息
            $email = $user->where(['email'=>$email])->find();

            // 如果数据库存在该邮箱,即用户有通过该邮箱注册账户,则可以重发激活邮件到该邮箱
            if ($email) {

                // 如果账户的状态为1(已激活)
                if($email['activation'] == '0') {
                    
                    $this->error('该邮箱尚未激活,请先激活账户');
                } else {
                    // 获取要重置密码的账户的id
                    $id = $email['id'];
                    // 获取要重置密码的账户的用户名
                    $username = $email['username'];

                    // 用vendor函数导入Vendor目录下的类库
                    // vendor/PHPMailer/class.pop3.php
                    vendor('PHPMailer.class', '', '.pop3.php');

                    // vendor/PHPMailer/classphpmailer.php
                    vendor('PHPMailer.classphpmailer');
                   
                    // 发送邮件,邮件上的url地址指向Login/resetPassword(重置密码的账户)
                    echo sendMail(I('post.email'), '零食商城重置密码信息', "您好".$username.",您已经请求了重置密码，可以点击下面的链接来重置密码<br><a href=http://192.168.32.251/2qipro/ErQiXiangMu-03/TP-shop/Home/Login/resetPassword?id=".$id.">http://www.lowewe.com/password/edit?reset_password_token=LBLpB2qTDLJhGNosAzow</a><p>如果你没有请求重置密码，请忽略这封邮件.</p><p>在你点击上面链接修改密码之前，你的密码将会保持不变</p>");
                                            
                    $this->success('重置密码邮件发送成功,请按照邮件提示重置密码', 'login');
                    exit;    
                }
            } else {
                $this->error('该邮箱尚未注册,请先注册');
            }       
                
        } else {
            
            $this->display();
        }
    }

    /**
     * 重置密码
     */
    public function resetPassword() {
        if (IS_POST) {
            // 连接用户表
            $user = D('user');

            // 查询用户表的数据
            $data = $user->where(['id'=>$_POST['id']])->find();

            // 如果用户输入的新密码与确认密码一致 
            if (I('post.password') == I('post.passwordRepeat')) {

                $data['password'] = password_hash(I('post.password'), PASSWORD_DEFAULT);

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
                    
        } else {
            // 接收重置密码邮件链接通过get方式传过来的id 
            $id = $_GET['id'];  
                       
            // 分配接收的id到前台
            $this->assign('id', $id);       
            $this->display();
        }
    }

    /**
     * 激活新注册的账号
     */
    public function activateAccount() {
        // 连接用户表
        $user = D('user');

        // 查询用户表的数据
        $data = $user->where(['id'=>$_GET['id']])->select();

        // 用户成功注册账户的时间(数据新添加的时间)
        $createtime = $data[0]['addtime'];

        // 当前时间(用户点击邮箱链接的时刻)
        $presenttime = time();

        // 链接的有效期,当前时间-账户的创建时间
        $agotime = ($presenttime-$createtime);

        // 激活状态
        $activation = $data[0]['activation'];

        // 如果账号未激活(为0),则改为激活(为1)
        if ($activation == '0') {

            // 如果激活邮件已经超过10分钟,则链接失效
            if ($agotime > 600) {
                $this->error("注册已超过10分钟,链接已失效,请重新获取激活邮件", 'login');

            // 如果激活邮件在有效期内
            } else if ($agotime < 600){

                $arr['activation'] = '1';

                // 开始更改账户状态
                $act = $user->where(['id'=>$_GET['id']])->save($arr);

                if ($act == '1') {
                    $this->success('恭喜您，账号激活成功，请您登录', 'login');
                    exit;
                } else {
                    $this->error('很抱歉,账户激活失败,网络繁忙,请重新获取激活邮件', 'login');
                    exit;
                }
            }

        // 如果账户已经激活,则提示用户直接登录
        } else if ($activation == '1') {
            $this->error('账号已激活,请直接登录', 'login');
            exit;
        }

        $this->display('Login/login');
    }

    /**
     * 重发激活邮件
     */
    public function activationMail() 
    {
        if (IS_POST) {
            // 连接用户表
            $user = D('user');

            $email = I('post.email');

            // 查询用户表账号的信息
            $email = $user->where(['email'=>$email])->find();

            // 如果数据库存在该邮箱,即用户有通过该邮箱注册账户,则可以重发激活邮件到该邮箱
            if ($email) {

                // 如果账户的状态为1(已激活)
                if($email['activation'] == '1') {
                    
                    $this->error('该邮箱已经激活,请直接登录');
                } else {
                    // 获取要激活账户的id
                    $id = $email['id'];
                    // 获取要激活账户的用户名
                    $username = $email['username'];

                    // 用vendor函数导入Vendor目录下的类库
                    // vendor/PHPMailer/class.pop3.php
                    vendor('PHPMailer.class', '', '.pop3.php');

                    // vendor/PHPMailer/classphpmailer.php
                    vendor('PHPMailer.classphpmailer');
                   
                    // 发送邮件,邮件上的url地址指向Login/activateAccount(激活账户)
                    echo sendMail(I('post.email'), '您在零食商城上的账号已创建，请激活',"您好".$username.",你需要点击以下链接来激活你的零食商城账户:<br><a href=http://192.168.32.251/2qipro/ErQiXiangMu-03/TP-shop/Home/Login/activateAccount?id=".$id.">http://www.lowewe.com/verification?confirmation_token=oAcjkF84cgSxGmY8oMNZ</a>");
                                            
                    $this->success('重发激活邮件成功请前往注册邮箱激活登录', 'login');
                    exit;    
                }
            } else {
                $this->error('该邮箱尚未注册,请先注册');
            }       
                
        } else {
            
            $this->display();
        }
    }

    /**
     * 账户激活状态判断
     */
    public function activationMailJudge() {
        if(IS_POST) {

            // 连接用户表
            $user = D('user');

            $email = I('post.email');

            // 查询用户表是否存在该邮箱
            $email = $user->where(['email'=>$email])->select();
                   
            if($email) {
                $this->ajaxReturn('1');
            } else {
                $this->ajaxReturn('-1');
            }

        } else {

            // 连接用户表
            $user = D('user');

            $email = I('get.email');

            // 查询用户表账号的状态
            $email = $user->where(['email'=>$email])->find();
            
            // 如果账户的状态为1(已激活)
            if($email['activation'] == '1') {
                $this->ajaxReturn('1');
            } else {
                $this->ajaxReturn('-1');
            }
        }
    }


    /**
     * 登录判断
     */
    public function loginJudge() {
        // 连接用户表
        $user = D('user');

        if (IS_POST) {
            
            $username = I('post.username');

            // 查询用户表是否已经存在该用户
            $username = $user->where(['username'=>$username])->select();
                   
            if($username) {
                $this->ajaxReturn('1');
            } else {
                $this->ajaxReturn('-1');
            }
        }
    }

    /**
    * 前台注册验证码
    */
    public function code()
    {   
        //设置验证码样式为4位纯数字
        $Verify = new \Think\Verify();
        // 验证码有效期
        $Verify->expire=10;
        $Verify->fontSize=15;
        $Verify->imageH=42;
        $Verify->useNoise=false;
        $Verify->useCurve=false;
        $Verify->length = 4; 
        $Verify->codeSet = '0123456789';
        $img = $Verify->entry();

    }


    /**
     * 用户注册-通过邮箱注册
     */
    public function emailRegister() {
        if (IS_POST) {

            // 连接用户表
            $user = D('user');
            $data = $user->create();

            $data['username'] = I('post.username');
            // 哈希加密密码
            $data['password'] = password_hash(I('post.password'), PASSWORD_DEFAULT);
            $data['email'] = I('post.email');
            // 添加时间
            $data['addtime'] = time();

      
            // 添加数据
            $id = $user->add($data);
            if ($data) {
                if ($id != false) {
                    // 用vendor函数导入Vendor目录下的类库
                    // vendor/PHPMailer/class.pop3.php
                    vendor('PHPMailer.class', '', '.pop3.php');

                    // vendor/PHPMailer/classphpmailer.php
                    vendor('PHPMailer.classphpmailer');
                    // dump()
                    // 发送邮件,邮件上的url地址指向Login/activateAccount(激活账户)
                    echo sendMail(I('post.email'), '您在零食商城上的账号已创建，请激活',"您好".I('post.username').",你需要点击以下链接来激活你的零食商城账户:<br><a href=http://192.168.32.251/2qipro/ErQiXiangMu-03/TP-shop/Home/Login/activateAccount?id=".$id.">http://www.lowewe.com/verification?confirmation_token=oAcjkF84cgSxGmY8oMNZ</a>");
                                            
                    $this->success('注册成功请前往注册邮箱激活登录', 'login');
                    exit;

                } else {
                    $this->error('注册失败');
                    exit;
                }
            } else {
                // 获取错误提示
                $this->error($user->getError());
                exit;
            }

            // 判断用户名是否为空
            // if(empty(I('post.username'))){
            //     echo "<script>alert('用户名不能为空')</script>"; 
            //     $this->display('Login/register');
            //     exit;
            // }

            //  //判断验证码是否为空
            // if(empty(I('post.emailcode'))){
            //     // echo "<script>alert('验证码不能为空')</script>"; 
            //     // $this->display('Login/register');
            //     $this->error('验证码不能为空','javascript:history.back(-1);',3);
            //     exit;
            // }
          
            // //判断验证码
            // $verify = new \Think\Verify(); 
            // if($verify->check(I('post.emailcode'))){

            //     $this->error('验证码不正确','javascript:history.back(-1);',3);
            //     exit;
            // }
            
            

            $this->display('Login/login');
            
        } else {
            $this->display();
        }
    }

    /**
     * 用户注册-通过手机号注册
     */
    public function phoneregister() {
        if (IS_POST) {
            // 判断用户名是否为空
            // if(empty(I('post.username'))){
            //     echo "<script>alert('用户名不能为空')</script>"; 
            //     $this->display('Login/register');
            //     exit;
            // }

            //  //判断验证码是否为空
            // if(empty(I('post.emailcode'))){
            //     // echo "<script>alert('验证码不能为空')</script>"; 
            //     // $this->display('Login/register');
            //     $this->error('验证码不能为空','javascript:history.back(-1);',3);
            //     exit;
            // }
          
            // //判断验证码
            // $verify = new \Think\Verify(); 
            // if($verify->check(I('post.emailcode'))){

            //     $this->error('验证码不正确','javascript:history.back(-1);',3);
            //     exit;
            // }
            
            $this->display('Login/login');
            
        } else {
            $this->display();
        }
    }

    /**
     *邮箱注册连接数据库判断用户名、邮箱是否已经被注册
     */
    public function emailRegisterJudge() {
        // 连接用户表
        $user = D('user');

        if (IS_POST) {
            
            $username = I('post.username');

            // 查询用户表是否已经存在该用户
            $username = $user->where(['username'=>$username])->select();
                   
            if($username) {
                $this->ajaxReturn('1');
            } else {
                $this->ajaxReturn('-1');
            }
        } else {

            $email = I('get.email');
            // 查询用户表是否已经存在该邮箱
            $email = $user->where(['email'=>$email])->select();
            if($email) {
                $this->ajaxReturn('1');
            } else {
                $this->ajaxReturn('-1');
            }
        }

    }

    /**
     * 验证用户在邮箱注册-验证码输入框，输入的验证码是否正确
     */
    public function emailcodeJudge() {

        $verify = new \Think\Verify(); 

        // 验证码有效期为10秒
        $Verify->expire = 10;

        if ( $verify->check(I('get.emailcode')) ) {
            $this->ajaxReturn('1');
        } else {
            $this->ajaxReturn('-1');
        }
    }

    /**
     *手机号码注册连接数据库判断用户名、手机号码是否已经被注册
     */
    public function phoneRegisterJudge() {
        // 连接用户表
        $user = D('user');

        if (IS_POST) {
            
            $username = I('post.username');

            // 查询用户表是否已经存在该用户
            $username = $user->where(['username'=>$username])->select();
                   
            if($username) {
                $this->ajaxReturn('1');
            } else {
                $this->ajaxReturn('-1');
            }
        } else {

            $phone = I('get.phone');

            // 查询用户表是否已经存在该手机号码
            $phone = $user->where(['phone'=>$phone])->select();

            if($phone) {
                $this->ajaxReturn('1');
            } else {
                $this->ajaxReturn('-1');
            }
        }

    }

     /**
     * 验证用户在手机号注册-验证码输入框，输入的验证码是否正确
     */
    public function phonecodeJudge() {

        $verify = new \Think\Verify(); 

        // 验证码有效期为10秒
        $Verify->expire=10;

        if ( $verify->check(I('get.phonecode')) ) {
            $this->ajaxReturn('1');
        } else {
            $this->ajaxReturn('-1');
        }
    }

    /**
    * 退出登录
    */
    public function logout()
    {
    
        session('user',null);

        // 注销QQ登录
        cookie('qq_accesstoken', null);
        cookie('qq_openid', null);
        
        $this->display('Login/login');
    }
          

}
