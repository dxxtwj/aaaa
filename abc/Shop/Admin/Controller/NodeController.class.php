<?php
namespace Admin\Controller;
use Think\Controller;

/*节点控制器*/
class NodeController extends CommonController
{	
	/**
	 * 节点列表
	 * @Author: Drizzle           2017-11-23
	 * @E-mail: Drizzle88@163.com
	 * @return arr 
	 */
	public function node_list()
	{
		$arr = M('admin_node')->select();
		$this->assign('arr',$arr);
		$this->display();

	}

	/**
	 * 添加模块节点
	 * @Author: Drizzle           2017-11-23
	 * @E-mail: Drizzle88@163.com
	 * @return bool
	 */
	public function node_add()
	{	
		if(IS_POST){
			M('admin_node')->add($_POST);
			$this->success('添加成功...','node_list','1');
			exit;
		}
	}

	/**
	 * 添加控制节点
	 * @Author: Drizzle           2017-11-23
	 * @E-mail: Drizzle88@163.com
	 * @return  bool
	 */
	public function contre_add()
	{	
		if(IS_POST){
			M('admin_node')->add($_POST);
			$this->success('添加成功...','node_list','0');
			exit;
		}	
		$tid = $_GET['tid'];
		//echo $tid;
		$this->assign('tid',$tid);
		$this->display();
		
	}

	/**
	 * 给角色分配权限
	 * @Author: Drizzle           2017-11-24
	 * @E-mail: Drizzle88@163.com
	 */
	public function compile_node()
	{	$id = $_GET['id'];
		//得到角色ID -> 找到角色拥有的节点
		//echo $id;exit;
		//获取角色的所有节点ID
		$role_node = M('admin_role_node')->field('nid')->where("rid='{$id}'")->select();
		//dump($role_node);
		//获取角色所有的节点ID
		foreach ($role_node as $v) {
				$node[] = $v['nid'];
		}
		//dump($node);

		//exit;
		$rol = M('admin_role')->field('id,rolename')->where("id='{$id}'")->find();
		$arr = M('admin_node')->select();
		$this->assign('rol',$rol);
		$this->assign('arr',$arr);
		$this->assign('node',$node);
		$this->display();
	}

	/**
	 * AJAX删除节点
	 * @Author: Drizzle           2017-11-23
	 * @E-mail: Drizzle88@163.com
	 * @return  int
	 */
	public function contre_del()
	{
		if(IS_AJAX){
			$id =  $_POST['id'];
			$tarr = M('admin_node')->where("tid='{$id}'")->select();
			if($tarr==null){
				$di = M('admin_node')->where("id='{$id}'")->delete();
				if($di==1){
				   echo 1;exit;
				}else{
					echo 2;exit;
				}
			}else{
				echo 3;exit;
			}		
		}
	}
}