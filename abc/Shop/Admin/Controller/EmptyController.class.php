<?php
namespace Admin\Controller;
use Think\Controller;

/**
* 控制器描述: 空控制器 当访问不存在的控制时触发
* @Author: Drizzle 
* @E-mail: Drizzle88@163.com
* @blogs: www.ylphp.com
* @phone: 13838389438
*/
class EmptyController extends Controller
{
    public function index()
    {
      $this->redirect('Login/login');
      exit;
    }
}