<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * 公用继承控制器
 */
class CommonController extends Controller
{
    /**
     * initalize会在所有方法前执行判断是否登录
     * @Author: Drizzle           2017-11-15
     * @E-mail: Drizzle88@163.com
     */
    public function _initialize()
    {   
        /*判断session的缓存是否存在,不存在表示未登录*/
        if(!session('?admin')){           
               $this->redirect('Login/login');
               exit;
        }else{
            //已登录判断登录时间是否与缓存时间符合,不符合表示已被他浏览器登录
            $name = session('admin');
            $user = M('admin_user');
            $logtime = $user->field('logtime')->where("username='{$name}'")->find();
            if(session('log')!=$logtime['logtime']){
                echo "<script>alert('管理账号过期,请重新登录')</script>";
                $this->success('1',U('Login/del'));
                exit;
        }

        //echo '当前控制器是：',CONTROLLER_NAME,'<br>';
        //echo '当前操作是：',ACTION_NAME,'<br>';
        $node = CONTROLLER_NAME.'/'.ACTION_NAME;
        //echo $node;
        //dump(session('nodeList'));
      if(session('admin')!='root'){
          if(!in_array_case($node, session('nodeList'))){
                //判断是否有权限
                $this->error('您没有权限，请联系骚龙☹',U('Index/home'),5);exit;
          }
      }

    }
           
    }


    /**
     * 执行文件上传操作
     * $a 为选择的目录
     * @return [error] [文件上传失败提示]
     * @return [$lujing] [文件上传成功返回路径]
     */
    public function file($a) {
        // 实例化
        $up = new \Think\Upload();
        
        // 设置大小
        $up->maxSize = 3145728;

        // 图片类型
        $up->exts = array('jpg', 'gif', 'png', 'jpeg');

        // 设置根目录
        $up->rootPath = './Public/';

        // 子目录
        $up->savePath = './Images/'.$a.'/';

        // 设置不自动创建目录时间
        $up->autoSub = false;

        // 执行上传
        $info = $up->upload();

        if (!$info) {
            // 获取报错信息
            return $this->error($up->getError());
            exit;
        } 

        // 获取数据 
        return $info;
    }

    /**
     * 空方法 访问不存在的方法时触发
     * @Author: Drizzle 2017-12-01
     * @return  [type]  [description]
     */
    public function _empty()
    {   
        $this->redirect('Login/login');
        exit;
    }
}