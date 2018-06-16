<?php
namespace Admin\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class AdministratorController extends CommonController {
	//权限管理
    public function admin_Competence(){
        $this->display();
    }
    //管理员列表
    public function administrator(){
        $this->display();
    }
    //个人信息
    public function admin_info(){
        $this->display();
    }
    

    
}