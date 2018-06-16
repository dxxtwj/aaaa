<?php
class Mem
{
	public $type = 'Memcached';
	public $m;
	public $time = 0;
	public $error;
	// private $array = array(
	// 		array('192.168.32.81',11211),
	// 	);
	public function __construct() 
	{
		if(!class_exists($this->type)) {
			$this->error = 'NO'.$this->type;
			return false;
		} else {
			$this->m = new $this->type;
		}
	}

	public function addServer($arr) 
	{
		$this->m->addServers($arr);
	}

	public function s($key,$value='',$time = NULL)
	{
		$number = func_num_args();
		if ($number == 1) {
			$this->get($key);
		} else if($number >= 2){
			if ($value === NUll){
				$this->delete($key);
			} else {
				$this->set($key,$value,$time);
			}
		}
	}

	public function set($key,$value,$time = NULL)
	{
		if ($time === NULL) {
			$time = $this->time;
		}
		$this->m->set($key,$value,$time);
		//返回Memcached::RES_*系列常量中的一个来表明最后一次执行Memcached方法的结果。 不等于0时,表示程序出问题了
		if ($this->m->getResultCode() != 0) {
			return false;
		}
		// echo 1;
	}

	public function get($key) 
	{
		$return = $this->m->get($key);
		if ($this->m->getResultCode() != 0) {
			return false;
		}
	}

	public function getError() {
		if ($this->error) {
			return $this->error;
		} else {
			//返回一个字符串来描述最后一次Memcached方法执行的结果。 
			return $this->m->getResultMessage();
		}
	}

	// public function ss() {
	// 	retutn
	// }
}