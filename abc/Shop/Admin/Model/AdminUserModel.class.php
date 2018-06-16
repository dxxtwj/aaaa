<?php 
namespace Admin\Model;
use Think\Model;

/**
* 管理员数据处理器
*/
class AdminUserModel extends Model
{	
	/**
	 * 处理管理员个人信息
	 * @Author: Drizzle           2017-11-16
	 * @E-mail: Drizzle88@163.com
	 * @return [arr] 返回当前管理员单条信息
	 */
	public function user_select()
	{	
		$name = session('admin');//拿出缓存查找当前用户
		$arr = $this->field(true)->where("username='{$name}'")->find();
		$sex = [1 => '男',2 => '女'];
		$arr['sex'] = $sex[$arr['sex']];
		$arr['atime'] = date('Y-m-d H:i:s',$arr['atime']);

		//找到用户的角色id,
		/*$user = M('admin_user_role');
		$user->where('')
*/
		return $arr;
	}

	/**
	 * 处理所有管理员信息
	 * @Author: Drizzle           2017-11-17
	 * @E-mail: Drizzle88@163.com
	 * @return  [arr]  返回所有的管理员信息
	 */
	public function user_selectall()
	{
		$arr = $this->field(true)->order('id desc')->select();
		$sex = [1=>'男',2=>'女'];
		$sta = [1=>'已启用',2=>'已停用'];
		//将角色数据表的角色字段取出
		$role = M('admin_role');
		$rolename = $role->field('rolename')->select();
		//将管理员与角色向对应
		foreach ($arr as $k => $v) {
			//性别字段处理
			$arr[$k]['sex'] = $sex[$v['sex']];
			//时间戳处理
			$arr[$k]['atime'] = date('Y-m-d H:i:s',$v['atime']);
			//状态处理
			$arr[$k]['status'] = $sta[$v['status']];
			//根据角色的role字段换成对应角色表的角色
			//0=>超级管理员 1=>商品管理员 ...
			//关联表user[role] => role[id]
			$arr[$k]['role']  = $rolename[$v['role']['rolename']]['rolename'];
		}
		/*dump($rolename);
		dump($arr);exit;*/
		return $arr;
	}

	/**
	 * 用户登录成功后,根据用户名找到用户的所有权限
	 * @Author: Drizzle           2017-11-24
	 * @E-mail: Drizzle88@163.com
	 * @return  arr   拥有的节点
	 */
	public function user_authority()
	{	
		//获取拥有的角色ID
		$name = session('admin');//拿出缓存查找当前用户
		$arr = $this->field(true)->where("username='{$name}'")->find();
		$aid = $arr['id'];//获取当前用户ID用于找到角色
		$user_role = M('admin_user_role')->where("aid='{$aid}'")->select();//找到拥有的角色id 
		$role = M('admin_role');
		foreach ($user_role as $v1) {
			$rid=$v1['rid'];
			$role_arr = $role->where("id='{$rid}'")->select();
			//dump($role_arr);
			foreach ($role_arr as $v2) { //获取当前用户的角色
				$nid[] = $v2['id'];//收集用户拥有的所有 角色id,用于遍历
			}
				
		}

		//获取节点ID
		//dump($nid);//获取用户的角色ID
		$role_node = M('admin_role_node');//通过角色ID找节点
		foreach ($nid as $v3) {
			$role_node_arr = $role_node->where("rid='{$v3}'")->select();
			//dump($role_node_arr);
			foreach ($role_node_arr as $v4) {
			 	$nid[] =  $v4['nid'];//获得所有的节点id
			 } 
		}

		//找到所有的节点并返回
		//dump($nid);//当前用户拥有的所有节点 的id
		$node = M('admin_node');
		foreach ($nid as $v5) {
			$node_body_arr[] = $node->field('nodebody')->where("id='{$v5}'")->find();
		}
		//dump($node_body_arr);
		foreach ($node_body_arr as $body_arr) {
			$arr2[] = $body_arr['nodebody'];
		}
		$arr2[] = 'Index/index';
        $arr2[] = 'Index/home';
		return $arr2;
	}
	
}