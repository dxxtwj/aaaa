<?php
namespace Admin\Controller;
use Think\Controller;

/*后台默认控制器*/
class IndexController extends CommonController{
	
	/*后台首页*/
	public function index()
	{
		//获取session数据并分配
		$this->assign('admin',session('admin'));
		$this->display();
	}
	public function home() 
	{
		$this->assign('ip',getip());
		$this->display();
	}

}