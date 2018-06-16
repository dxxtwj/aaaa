<?php
namespace Admin\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class ProductController extends CommonController {
	//产品列表
    public function Products_List(){
        $this->display();
    }

    //品牌管理
    public function Brand_Manage(){
        $this->display();
    }
    //分类管理
    public function Category_Manage(){
        $this->display();
    }
}