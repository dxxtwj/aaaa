<?php

namespace Home\Controller;
use Think\Controller;
class CeshiController extends Controller {

	/**
	 * 测试 append 和 appendTo的区别
	 * append 添加新的节点进某一个标签，用于追加到目标中
	 * appendTo 把所有匹配的元素追加到另一个指定的元素元素集合中。
	 */
	public function index() {
		
		// $this->redis = new \Redis();

		// $this->redis->connect('localhost', 6379);

		// $this->redis->set('asdasd', 5465456);

		// $this->get('abc');

		$this->display();

	}

	/**
	 * 懒加载的测试
	 */
	public function lanjiazai() {
		// 成功插入数据库
		$model = M('upload');

		// 获取图片数据
		$arr = $model->select();
			
		if (IS_AJAX) {
		$arr = $model->limit($_GET['pai'], 3)->select();

			// 返回数据
			$this->ajaxReturn($arr);
		}
		// 分配数据
		$this->assign('arr', $arr);

		// 渲染模板
		$this->display();
	}

	/**
	 * 测试回滚事务
	 * 1 只支持innodb结构
	 * 2 事务管理开启 $model1 或者model2开启都可以 
	 */
	public function huigun()
	{
		$model1 = M('Xdll');
		$model2 = M('Xdl');

		// 开启事务
		$model1->startTrans();

		$bool = true;

		// 删除
		$row1 = $model1->delete(1722);
		$row2 = $model2->delete(3);

		if (!$row1) {
			// 删除不成功
			$bool = false;
		}
		if (!$row2) {
			// 删除成功
			$bool = false;
		}

		if ($bool) {
			echo '成功';
			// 提交事务
			$model1->commit();
		} else {
			echo '失败';
			// 回滚事务
			$model1->rollback();
		}
	}

	/**
	 * 对TP框架redis实现缓存的实验
	 */
	public function redis()
	{	
		// 实例化
		$redis = S(array('type'=>'redis'));

		// 准备数据
		$data = ['username' => '童文杰', 'age' => 18];

		// 存储数据
		$cun = $redis->set('user', $data);

		// 取数据
		$qu = $redis->get('user');

		// 显示模板
		$this->display();
	}

	/**
	 * 讯搜
	 */
	public function xunsearch() {

		vendor('xunsearch.php.lib.XS');
		
		$model = M('Xdll');

		$arr = $model->select();

		// 实例化XS
		$sm = new \XS('user');

		// 获取管理索引的对象
		$index = $sm->search;

		// 创建XSDocument
		$doc = new \XSDocument($arr);

		// 添加索引
		$index->add($doc);
		

		if (IS_POST) {

			// 实例化XS
			$xs = new \XS('Xdll');

			// 管理搜索对象
			$search = $xs->search;



		}
		// 获取索引对象
		// $index = $sm->search;

		// 索引同步
		// $index->flushIndex();

		// $xs = new \XS('Xdll');

		// 获取搜索对象
		// $demo = $xs->search();

		// $arr = $demo->search($_GET);

		// var_dump($arr);

		// $docs = $search->setQuery($_GET)->search();

		// var_dump($docs);
		// 显示模板
		$this->display();
	}


	/**
	 * 测试cookie和session
	 */
	public function ceshineirong()
	{

		// $time = mktime('14', '30', '30', '12', '19', '2017');
		// $c = date('Y-m-d H:i:s', $time);


		// echo time().'<br>';
		// echo $time;
		// $a = $time - time();

		// $b = date('Y-m-d H:i:s', $a);
		// echo "<pre>";
		//   echo "<hr>";
		//     dump($c);
		//     dump($b);
		//   echo "<hr>";
		// echo "</pre>";exit;
		// echo 1+2+"3+4+5";

		// 获取前某天的时间
		// echo date('Y-m-d H:i:s',strtotime('8 day'));
		
		// 获取前某天的时间
		// echo date('Y-m-d H:i:s',strtotime('8 day'));
		
		// $str = '1234567890';


		// 请写一个函数将1234567890转换成1,234,567,890每3位用逗号隔开的形式
		// function demo($str){
			
			// $str = strrev($str);
			// 0987654321

			// $arr = str_split($str,3);
			// array(4) {
			//   [0] => string(3) "098"
			//   [1] => string(3) "765"
			//   [2] => string(3) "432"
			//   [3] => string(1) "1"
			// }
			
			// $str = strrev(implode(',',$arr));
			// "1,234,567,890"
		// }
			// $s = demo($str);


		
		// 将$data=’08/26/2003’变成‘2003/08/26’输出
		// $data = '08/26/2003';
		// echo date('Y-m-d H:i:s',strtotime($data));


		// 写一个函数，尽可能高效的，从一个标准的url里去除文件的扩展名
		function getExt($url="http://192.168.32.108/ceshitpzhuanyong_xuniji/index.php?c=Ceshi&a=ceshineirong"){
			// parse_url — 解析 URL，返回其组成部分 
			$tmp = parse_url($url);
			
			// array(4) {
			//   ["scheme"] => string(4) "http"
			//   ["host"] => string(14) "192.168.32.108"
			//   ["path"] => string(34) "/ceshitpzhuanyong_xuniji/index.php"
			//   ["query"] => string(22) "c=Ceshi&a=ceshineirong"
			// }
		
			// pathinfo — 返回文件路径的信息 
			$urlPath = pathinfo($tmp['path']);

			// array(4) {
			//   ["dirname"] => string(24) "/ceshitpzhuanyong_xuniji"
			//   ["basename"] => string(9) "index.php"
			//   ["extension"] => string(3) "php"
			//   ["filename"] => string(5) "index"
			// }
			
			return $urlPath['extension'];
		}

		// getExt();

		// $str = 'as啊实打实多d';

		// $sss = mb_substr($str, 1);
		// echo "<pre>";
		//   echo "<hr>";
		//     dump($sss);
		//   echo "<hr>";
		// echo "</pre>";exit;
		// echo strchr("Hello world!","w",false);

		// $str = 'open_dooar';

		// echo str_replace("a","Shanghai", $str);
		
		// $str = 'open_door';

		// function demo($a)
		// {

		// 	$b = explode('_', $a);

		// 	for ($i = 0; $i < count($b); $i++) {

		// 		$c[] = ucfirst($b[$i]);
				
		// 	}
		// 	$join = join('', $c);
		// 	echo "<pre>";
		// 	  echo "<hr>";
		// 	    dump($join);
		// 	  echo "<hr>";
		// 	echo "</pre>";exit;
			
		// }
		// demo($str);

		// function demo($a)
		// {
		// 	$s = strlen($a);

		// 	for ($i = 0; $i < strlen($a); $i++) {
		// 		$b[] = $a[$i];
		// 	}

		// 	$c = rsort($b);
		// 	$join = join('', $b);

		// 计算数字不一样
		// $a = 0.2+0.7;
		// $b = 0.9;
		// printf("%0.20f", $a);
		// echo '<br />';
		// printf("%0.20f", $b);


		// 下面的都会解析变量
		// $a = 123;
		// $b = 345;
		// echo "{$a}{$b}"."<br>";
		// echo "${a}";


		/*
			昊翼面试题答案
		 */
		
		/*
			第二题
		 */
		// 无限循环
		// for ($i = 0; $i = 1; $i++) {

		// 	echo $i.'</br>';
		// }

		// 0次循环
		// for ($s = 0; $s == 1; $s++) {
		// 	echo $s;
		// }

		/*
			第六题
		 */
		gettype($a = 1).'<br>'; // 可以查看数据类型
		GetType($a = '123'); // 可以查看数据类型
		// echo Type($a = 1); 	// 报错 不可以


		/*
			第七题  字符串的比较，是按ascll码值比较的
		 */

		/*
			第十题
		 */
		
		// $mysqli = new \mysqli('localhost', 'root', '', 'ceshiku');

		// $sql = 'select * from shop_xdll limit 2';

		// $result = $mysqli->query($sql);


  //    	$info = mysql_info($mysqli); 
		// $row = $result->fetch_All();



		// echo "<pre>";
		//   echo "<hr>";
		//     var_dump($row);
		//     var_dump($info);
		//   echo "<hr>";
		// echo "</pre>";exit;
		

		/*
			比较两个浮点数的大小
		 */

		// $a = 123123.01234546871;
		// $b = 0.1002;
		// function ss($num, $num2) {
		// 	$yi = 1;

		// 	// 把数字转换为数字
		// 	$num1 = explode('.', $num);
		// 	$nunm21 = explode('.', $num2);

		// 	// 获取两个数的长度
		// 	$strnum = strlen($num1[1]);
		// 	$strnum2 = strlen($nunm21[1]);

		// 	if ($strnum > $strnum2) {
		// 		for ($i = 0; $i < $strnum; $i++) {
		// 			$ling .= 0;
		// 		}

		// 		// 拼接成 1和多个0
		// 		$pinjie = $yi.$ling;

		// 	} else {
		// 		for ($i = 0; $i < $strnum2; $i++) {
		// 			$ling .= 0;
		// 		}

		// 		// 拼接成 1和多个0
		// 		$pinjie = $yi.$ling;
		// 	} 
			
		// 	// 转换为数字类型做比较
		// 	$numxin = $num * intval($pinjie);
		// 	$numxin1 = $num2 * intval($pinjie);
		// 	if ($numxin > $numxin1) {
		// 		return '>';
		// 	} else {
		// 		return '<';
		// 	} 
		// }
		// echo ss($a, $b);


		// $i = 1;
		// $a = ($i++)+(++$i);
		// echo $a;
		
		// $a = null;

		// if (isset($a)) {
		// 	echo 123;
		// } else {

		// 	echo 345;
		// }

	  // for ( $i = 0; $i < 5; $i++ ) {

   //      switch ($i)
   //      {

           
   //          case 1:    
   //              echo $i . "我是一";
   //              continue 2;
   //              echo $i . "啊阿仕顿";
   //          case 2:    
   //              echo $i . "我是二";
   //              break;
   //              echo $i . "a";
   //          case 3:
   //              echo $i . "我是第三";
   //              break 2;
   //              echo $i . "a";
   //          case 4:
   //              echo $i;
            
   //      }

   //      echo 9;

   //  }
		$b = array('1', '22', '22', '33', '22');
		// $len = count($b);

		// for ($k = 0; $k <= $len; $k++) {

		// 	for ($j = $len - 1; $j > $k; $j--) {
		// 		if ($b[$j] < $b[$j - 1]) {
		// 			$temp = $b[$j];
		// 			$b[$j] = $b[$j - 1];
		// 			$b[$j - 1] = $temp;
		// 		}
		// 	}
		// }
		
		// echo "<pre>";
		//   echo "<hr>";
		//     var_dump($b);
		//   echo "<hr>";
		// // echo "</pre>";exit;
		// for ($i = 0; $i < count($b); $i++) {

		// 	for ($j = count($b) - 1; $j < $i; $j++) {}
		// }
		

		// session('a', '123');
// 		echo "<pre>";
// 		  echo "<hr>";
// 		    unset($_SESSION['a']);
// 		    var_dump($_SESSION['a']);
// 		  echo "<hr>";
// 		echo "</pre>";exit;

		// $array1 = array(
		// 	array(
		// 	'id' => '哈哈',
		// 	'1' => '哦哦',
		// 	'2' => '啊啊',
		// 	'3' => 'oo',
		// 	'shijian' => time() + 1,
		
		// ),
		// 	array(
		// 	'id' => '哈哈',
		// 	'1' => '哦哦',
		// 	'2' => '啊啊',
		// 	'3' => 'oo',
		// 	'shijian' => time() + 1,
		
		// ),
		// 	array(
		// 	'id' => '哈哈',
		// 	'1' => '哦哦',
		// 	'2' => '啊啊',
		// 	'3' => 'oo',
		// 	'shijian' => time() + 1800,
		
		// ),);

		// session('cart', $array1);
	// dump(session('cart'));

		
		
	// 	foreach (session('cart') as $key => $value) {

	// 		if ($value['shijian'] > time()) {
	// 			// 没过期 不用删除购物车
	// 			echo 123;
 // 			} else {

	// 			// 过期 删除购物车
 				
 // 				echo "<pre>";
 // 				  echo "<hr>";
 // 				    // unset(session('cart')[$key]['id']);
 // 				    // 
 // 				    unset($_SESSION['cart'][$key]); // 删除 session
 // 				  echo "<hr>";
 // 				echo "</pre>";
	// 		}
	// 	}
	// $a = array(

	// 	'keyword' => '菊花',
	// 	'keyword2' => '综合第一页',
	// 	'ask' => array(

	// 		'question' => '',
	// 		'answer' => '',
	// 		'tips' => '123',

	// 	),

	// 	'qqq' => '',

	// );
	// foreach ($a as $k => $v) {
	// echo "<pre>";
	//    	print_r($a);
	// echo "</pre>";
	//    	var_dump($a['keyword']);
		
	// }
	// $where = array('id' => ['in', '1866, 1862, 1877']);
	// $model1 = M('Xdll')
	// 	->where($where)
	// 	->delete();
	// 	echo "<pre>";
	// 	  echo "<hr>";
	// 	  echo M('Xdll')->_sql();
	// 	    var_dump($model1);
	// 	  echo "<hr>";
	// 	echo "</pre>";exit;
	// $where = array('id' => 1880);
	// $model1 = M('Xdll')
	// 	->where($where)
	// 	->delete();
	// 	echo "<pre>";
	// 	  echo "<hr>";
	// 	    var_dump($model1);
	// 	  echo "<hr>";
	// 	echo "</pre>";exit;
		
		
		$a = array(array(
			'id' => '哈哈',
			'1' => '哦哦',
			'2' => '啊啊',
			'3' => 'oo',
			'shijian' => time() + 1800,
		
		), 
		array(
			'id' => '哈哈',
			'1' => '哦哦',
			'2' => '啊啊',
			'3' => 'oo',
			'shijian' => time() + 1800,
		
		), 
		);

		foreach ($a as $k => $v) {


			if ($v['id']) {

				$ab[] = 'assd';
			} 
		}
			echo "<pre>";
			  echo "<hr>";
			    var_dump($ab);
			  echo "<hr>";
			echo "</pre>";
			
		
	}
	/*1
		获取100个长度为6的随机不同字符串
	 */
    public function aaa()
    {
	    $randpwd = '';  
	    // 600  因为是长度为6
	    for ($i = 0; $i < 600; $i++){

	    	// chr返回指定的ascii值字符串
    		$randpwd[] .= chr(mt_rand(33, 126));  
	    	if (count($randpwd) >= 6) {

	    		$join[$i] = join('', $randpwd);

	    		$randpwd = [];
	    	}
    	}
    	
    }  

    /**
     * 对应用传递的测试
     */
    public function hahaha()
    {

    	$a = 1;

    	$this->wowowo($a);

    	echo "<pre>";
    	  echo "<hr>";
    	    var_dump($a);
    	  echo "<hr>";
    	echo "</pre>";exit;
    	

    }

    /**
     * 对引用传递的测试
     */
    public function wowowo(&$a)
    {
    	$a = 2;
    }


    public function newceshi() {

    	// $float = 5.6;
    	// $qie = 54645644;
    	// echo substr($qie, 0, -6);
    	// echo sprintf("%.2f", substr(sprintf("%.4f", $float), 0, -2));
    	// echo sprintf("%.2f", substr(sprintf("%.4f", $float), 0, -2));


    	$miao = 3600;
    }

    public function asd()
    {	
    	$a = 1;
    	 // header('location:index.php?c=Ceshi&a=display&id='.$a);
    	// echo "<script>location.href='".U('Ceshi/display', ['id' => $a])."'</script>";
    	// echo 123;
    	// $this->assign($a);
    	// $this->display('Ceshi/display');
    	// include_once('./Public/jquery-2.1.4/jquery.min.js');
    	echo "<script src='./Public/jquery-2.1.4/jquery.min.js'></script>";
    	echo "<script> $.ajax({
			
			data: {id:".$a."},

			url: 'index.php?c=Ceshi&a=display',

			async: false,

			type: 'type',

			success: function(msg) {
				console.log(msg);
				$.ajax({
					data: {id:msg},

					type: 'post',

					async: false,

					url: 'index.php?c=Ceshi&a=asd',

					success: function(ss) {
						console.log(ss);
					}
				});
			}

    	});</script>";
    		echo "<pre>";
    		  echo "<hr>";
    		    var_dump($_POST);
    		  echo "<hr>";
    		echo "</pre>";exit;
    		
    	
    }

    public function display()
    {	
    	
    	$this->ajaxReturn('大家好哈');
    	
    }
  	public function asdasd()
  	{

  		$this->ajaxReturn($_POST);
  	}

  	public function hahage() 
  	{
  		$str = 'abcdef';
  		$substr = substr($str, -3, 3);//从结尾的d开始，截取3个  所以输出为  def
  		$ucfirst = ucfirst($str);//首字母大写
  		lcfirst();//首字母小写
  		strtolower();//字符串转换为小写
  		strtoupper ();
  		uniqid();//生成唯一id
  		array_unique();//去掉数组重复的值
  		echo "<pre>";
  		  echo "<hr>";
  		    var_dump($a);
  		    var_dump($b);
  		    // var_dump($ucfirst);
  		  echo "<hr>";
  		echo "</pre>";exit;
  		
  	}

  	public function jkl()
  	{
  		$this->display();
  		// $this->ajaxReturn(I('post.'));
  	}

  	public function typeAA() {


  		// $a=$this->ahaha(247);
  		// $where = array('id' => ['in', $a]);
  		// $typeData = M('Type')->where($where)->select();
  		// echo "<pre>";
  		//   echo "<hr>";
  		//     var_dump($typeData);
  		//   echo "<hr>";
  		// echo "</pre>";exit;
  		// 
  		// $a = $this->getList(0);
  		$a = $this->getLike(251);

	  	$where = array('id' => ['in', $a]);
  		
  		$typeData = M('Type')->where($where)->select();
  		
  		foreach ($typeData as $k=>$v) {


  			$name[] =  $v['name'];
  			
  			
  		}
  		$names = join('---->', $name);

  		echo "<pre>";
  		  echo "<hr>";
  		    var_dump($names);
  		  echo "<hr>";
  		echo "</pre>";exit;
  		
  		
  	
  	}

  	/*
  		导航
  		string(43) "点心丨蛋糕---->点心---->点心饼干"
  		@return array ids  返回一串上级ID和本身的ID，外面再用这串ID来 whereIn查询
  	 */
  	public function getLike($pid=0, $ids=array()) {

  		$where['id'] = $pid;
  		$typeData = M('Type')->where($where)->select();
  		foreach ($typeData as $k => $v) {
  			$ids = $this->getLike($v['pid'],$ids);
  			$ids[] = $v['pid'];
  			$ids[] = $pid;
  		}

  		return $ids;
  	}

  	/*
  		获取分类下的子分类
  		@param int $pid 父级ID
  		@param array $result 积累的数据
  		@param int $num 空格
  		return array 二维
  	 */
  	public function getList($pid=0, $result=array(),$num='') {

  		$num = $num+2;
  		$where['pid'] = $pid;
  		$typeData = M('Type')->where($where)->select();

  		foreach ($typeData as $k => $v) {
  			// $row[$k] = $v;
  			$row[$k]['name'] = str_repeat('123;', $num).$v['name'];
  			$result = $row;
  			$this->getList($v['id'],$result,$num);
  		}
  		return $result;
  	}




















  	// public function getList($pid=0, $result=array(),$spac=0) {


  	// 	$spac = $spac+2;
  	// 	$where['pid'] = $pid;
  	// 	$typeData = M('Type')->where($where)->select();
  		
  	// 	// while($typeData) {
  	// 	foreach ($typeData as $k => $v) {
  	// 		$row[] = $v;
  	// 		$row[$k]['name'] = str_repeat('123;', $spac).'|--'.$v['name'];
  	// 		$result	= $row;
  	// 		$this->getList($v['id'], $row, $spac);
  			
  	// 	}

  	// 	return $result;
  	// 	// };
  	// }





  	public function ahaha($id, $ids=array()){
  		$where['pid'] = $id;

  		$ids[] = $id;

  		$typeData = M('Type')
  			->where($where)
  			->select();
 			  			
  		if (!empty($typeData)) {

  			foreach ($typeData as $k=>$v) {
  				$ids = $this->ahaha($v['id'], $ids);
  			}

  		} else {

  			return $ids;
  		}
  		return $ids;
  	}
}

	