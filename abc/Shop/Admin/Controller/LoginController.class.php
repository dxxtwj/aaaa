<?php
namespace Admin\Controller;
use Think\Controller;
 /**
 * 管理员控制器
 */
class LoginController extends Controller 
{

    //字段映射 
    protected $_map = array(
         'namejj' =>'username',
         'pwdjj'  =>'password');

/****************成员方法*********************/
     /**
     * 处理登录方法
     * @Author: Drizzle           2017-11-15
     * @E-mail: Drizzle88@163.com
     */
    public function login()
    {   
        //如果是POST提交数据则执行此判断
        if (IS_POST) { 
           
            //判断用户名是否为空
            if(empty(I('post.namejj'))){
                echo "<script>alert('用户名不能为空')</script>"; 
                $this->display('Login/login');
                exit;
            }
            //判断是否为纯数字
            if(preg_match('/^\d+$/',I('post.namejj'))){
                echo "<script>alert('用户名不合法')</script>"; 
                $this->display('Login/login');
                exit;
            }
            //判断密码是否为空
             if(empty(I('post.pwdjj'))){
                echo "<script>alert('密码不能为空')</script>"; 
                $this->display('Login/login');
                exit;
            }

            //判断验证码是否为空
            if(empty(I('post.yzm'))){
                echo "<script>alert('验证码不能为空')</script>"; 
                $this->display('Login/login');
                exit;
            }

            //判断验证码
            $verify = new \Think\Verify(); 
            if(!$verify->check(I('post.yzm'))){
                echo "<script>alert('验证码不正确')</script>"; 
                $this->display('Login/login');
                exit;
            }

            //实例数据库
            $user = M('admin_user');

            //获取表单中的用户名
            $name = I('post.namejj');

            //正则判断
            if(!preg_match("/^\w{4,10}+$/", $name)){
                echo "<script>alert('用户名不合法 o(╥﹏╥)o')</script>"; 
                $this->display('Login/login');
                exit;
            }

            //判断是否多次错误
            if(session('cs'.$name)=='4') {
                redirect('https://www.360.cn');
                exit;
            }

            //将表单中的用户作为where条件查询密码段
            $arr = $user->field(true)->where("username='{$name}'")->find();
            
            //如果没有查到数据则跳转登录页
            if(empty($arr)){
                echo "<script>alert('用户名或密码错误^_^')</script>"; 
                $this->display('Login/login');
                exit;
            }
            
            
            //将获取的密码与hash进行比较,如果为false则跳转登录页
            if( password_verify(I('post.pwdjj'),$arr['password'])){

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
                session('admin',$arr['username']);
                session('id',$arr['id']);
                //记录登录IP记录
                $userid = ['uid'=>$arr['id'],'ip'=>getip(),'time'=>time(),'adminname'=>$arr['username']];
                $adip = M('admin_ip');
                $adip->add($userid);
                
                //写入默认权限
                
                $user = D('AdminUser');
                $arr  = $user->user_authority();
               // dump($arr);exit;
                //获取权限写入缓存
                session('nodeList', $arr);
                $this->success('1', U('Index/index'));
                
                //如果要求记录密码,则写入缓存
                if(I('post.jizhu')=='1'){
                    session('adname',I('post.namejj'),7200);
                    session('adpwd',I('post.pwdjj'),7200);
                }
            }else{
                
                //密码错误进入此区域
                //密码错误限定次数,拼接用户名,限定单个用户的登录次数.限定4次错误机会
                if(session('cs'.$name)==false){
                    session('cs'.$name,'1',300);
                    echo "<script>alert('第一次错误')</script>";
                    $this->display();
                    exit;
                }elseif(session('cs'.$name) =='1'){
                    session('cs'.$name,'2',300);
                    echo "<script>alert('第二次错误')</script>"; 
                    $this->display();
                     exit;
                }elseif(session('cs'.$name)=='2'){
                    session('cs'.$name,'3',300);
                    echo "<script>alert('第三次错误')</script>"; 
                    $this->display();
                    exit;
                }elseif(session('cs'.$name)=='3'){
                     session('cs'.$name,'4',300);
                     echo "<script>alert(',登录用户{$name}已被暂时禁用,请5分钟后再尝试')</script>"; 
                     $this->display();
                     exit;
                }

                echo "<script>alert('用户名或密码错误')</script>"; 
                $this->error();
                exit;
            }

        }else{
            //不是post提交的进入此区域
            $this->assign('adname',session('adname'));
            $this->assign('adpwd',session('adpwd'));
            $this->display();

        }
        
    }

    /**
    * 验证码
    */
    public function yzm()
    {   
        //设置验证码样式为3位纯数字
        $Verify = new \Think\Verify();
        $Verify->fontSize=15;
        $Verify->useNoise=false;
        $Verify->useCurve=false;
        $Verify->length = 3; 
        $Verify->codeSet = '0123456789';
        $img = $Verify->entry();

    }


    /**
    * 退出登录
    */
    public function del()
    {
        session('admin',null);
         $this->display('Login/login');
    }

            

}
