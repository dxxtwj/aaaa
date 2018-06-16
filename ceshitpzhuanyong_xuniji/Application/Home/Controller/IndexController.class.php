<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function index() {

		// 1 libray目录下的Vendor为第三方类库,Org也可以作为第三方类库

		// $model = new \Fuck\Test();

		// $model->say();


		// vendor('PHPMailer.classphpmailer');
		// 2 用vender函数导入Vendor下的类库
		// vendor/PHPMailer/class.pop3.php
		// vendor('PHPMailer.class', '', '.pop3.php');

		// new \POP3();
		// test();

		// 发给谁？  主题  内容
		// echo sendMail('wjgocom@126.com', 'abc', 'test');
		// vendor('PHPMailer.class', '', '.pop3.php');

        // vendor('PHPMailer.classphpmailer');
        // new \POP3();

        // echo sendMail('wwjso88@163.com', '吴伟健儿子', '<h1>哈珀人</h1>');


		// var_dump($_SESSION);

		if(IS_POST) {
			// $v = new \Think\Verify();

			// 验证码检测
			// if (!$v->check(I('yzm'))) {

				// $this->error('请输入正确的验证码');
				// exit;
			// }

			$up = new \Think\Upload();

			// 设置文件上传的保存路径(设置根目录)
			$up->rootPath = './Image/';

			// 文件上传的保存路径（相对于根路径）(存到子目录)
			$up->savePath = './Goods/';

			// 自动使用子目录保存上传文件 默认为true
			$up->autoSub = false;

			// 执行上传
			$pic = $up->upload();

			if ($pic) {
				dump($pic);
			} else {
				// 获取报错信息
				echo $up->getError();
			}

		} else {
			$this->display();
		}

	}

	public function yzm()
	{	

		// 实例化验证码
		// $v = new \Think\Verify();

		// 设置验证码数量
		// $v->length = 4;

		// 设置没杂点
		// $v->useNoise = true;

		// 指定中文要那些
		// $v->zhSet = '吃屎屎';

		// 设置中文
		// 找字体 （C:\Windows\Fonts 找字体）(扔到\Think\Verify\zhttfs目录下)
		// $v->useZh = true;

		// 生成验证码
		// $v->entry();	
	}


	public function shiwu() {
		// 只有innodb才支持事务管理

		// 实例化
		// $model = M('Xdll');

		// 查询操作
		// var_dump($model->select());

		// 1 开启事务
		// $model->startTrans();

		// 删除操作
		// dump($model->where(true)->delete());

		// 2 提交事务
		// $model->commit();

		// 3 回滚事务
		// $model->rollback();
	}

	public function cache()
	{	
		// 缓存初始化
		// S(array('type'=>'memcache', 'expire'=>60));
		// echo phpinfo();

		// 设置缓存 
		// S('Key', '文杰');

		// 获取缓存
		// echo S('Key');

		// 查询缓存
		// dump(M('Xdll')->cache()->select());
		$model = M('Xdll');
		// 如果设置了配置项默认是memcache 就可以动态设置了 就是在cache(C(''))  来设置
		$arr = $model->cache('ss')->select();
		dump(S('ss'));
		// dump($arr);

	}
}