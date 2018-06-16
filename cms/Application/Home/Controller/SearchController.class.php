<?php
namespace Home\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class SearchController extends HomeController {
	
	/**
	 * 搜索显示页面
	 */
	public function search(){

		$this->display();
	}

}