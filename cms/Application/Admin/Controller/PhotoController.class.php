<?php
namespace Admin\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class PhotoController extends CommonController {
	//广告管理
    public function advertising(){
        $this->display();
    }
    //分类管理
    public function Sort_ads(){
        $this->display();
    }

    
}