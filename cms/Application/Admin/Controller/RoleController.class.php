<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;

class RoleController extends CommonController {
    public function index(){
        // $where['NR_PID'] = 0;
        $count = M('auth_group')->count();

        $page_num = 10;
        $page = new \Think\Page($count,$page_num);
        $page->setConfig('first','首页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $show = $page->show();
        $limit = $page->firstRow.','.$page->listRows;

        $data = M('auth_group')->limit($limit)->select();
        $sa = M('sys_admin');
        foreach ($data as $key => $value) {
            $admin_count = $sa->where(array('SA_Role_ID'=>$value['id']))->count();
            $admin = $sa->where(array('SA_Role_ID'=>$value['id']))->select();
            $data[$key]['count'] = $admin_count;
            foreach ($admin as $key1 => $value1) {
                if(strlen($data[$key]['name'])>20){
                    $data[$key]['name'] = substr($data[$key]['name'],0,40)."...";
                    break;
                }
                $data[$key]['name'] .= $value1['SA_Name'].',';

            }
            $data[$key]['name'] = trim($data[$key]['name'],",");
            if(strlen($data[$key]['describe'])>100){
                $data[$key]['describe'] = substr($data[$key]['describe'],0,100)."......";
            }
        }
        $this->assign('data', $data);
        $this->assign('count', $count);
        $this->assign('show',$show);
        // dump($data);
        $this->display();
    }

    Public function add(){
        $role_id = I('rid');
        if(IS_POST){
            $title = I('rolename');
            $describe = I('describe');
            $auth = I('auth');
            sort($auth);
            // dump($node);
            // exit;
            $node = implode(',', $auth);
            $data['title'] = $title;
            $data['describe'] = $describe;
            $data['rules'] = $node;
            $data['time'] = time();
            // dump($data);
            if($role_id){
                $res = M('auth_group')->where(array('id'=>$role_id))->save($data);
                if($res){
                    $this->success('修改角色成功');
                }else{
                    $this->error('修改角色失败');
                }
            }else{
                $res = M('auth_group')->add($data);
                if($res){
                    $this->success('添加角色成功');
                }else{
                    $this->error('添加角色失败');
                }
            }
                
        }else{
            // dump($_SESSION);

            if(!$role_id){
                $map['id'] = array('in', $_SESSION['Admin']['info']['SA_Role_ID']);
                $roleinfo = M('auth_group')->where($map)->find();
                if($roleinfo['id'] == 1){
                    $where['pid'] = 0;
                    $data = M('auth_rule')->where($where)->order('id asc')->select();
                    if($data){
                        // 查出控制器的方法
                        foreach($data as $key=>$val){
                            $where['pid'] = $val['id'];
                            $data[$key]['action'] = M('auth_rule')->where($where)->order('id asc')->select();
                        }
                    }
                }else{
                    $where['id'] = array('in',$roleinfo['rules']);
                    $data = M('auth_rule')->where($where)->order('id asc')->select();
                    if($data){
                        // 查出控制器的方法
                        foreach($data as $key=>$val){
                            $where['pid'] = $val['id'];
                            $data[$key]['action'] = M('auth_rule')->where($where)->order('id asc')->select();
                        }
                    }
                }

                $this->assign('data', $data);
                // dump($data[0]['action']);

                // 查角色
            }else{
                if($role_id == 1){
                    $this->error('不能修改该角色', U('Role/index'));
                }
                // dump($role_id);
                $info = M('auth_group')->where(array('id'=>$role_id))->find();
                if($info){
                    $info['rules'] = explode(',', $info['rules']);
                }else{
                    $info = '';
                }
                // 查权限
                $where['pid'] = 0;
                $data = M('auth_rule')->where($where)->select();
                if($data){
                    // 查出控制器的方法
                    foreach($data as $key=>$val){

                        $where1['pid'] = $val['id'];
                        $data[$key]['action'] = M('auth_rule')->where($where1)->select();
                        foreach ($info['rules'] as $k1 => $v1) {
                            if($val["id"] == $v1){
                                $data[$key]['checked'] = 'checked';
                            }
                            foreach ($data[$key]['action'] as $k2 => $v2) {
                                if($v2["id"] == $v1){
                                    $data[$key]['action'][$k2]['checked'] = 'checked';
                                }
                            }
                            
                        }
                        
                    }
                }
                $this->assign('info', $info);
                $this->assign('data', $data);
            }
            $this->display();
        }
    }


    public function del(){
        $role_id = I('role_id');
        $ids = trim($role_id,',');
        
        $arr = explode(",", $ids);
        if(in_array("1", $arr)){
            $this->ajaxReturn(-1);
        }
        if($ids){
            $where = array('in',$ids);
        }
        $transdb = new Model();
        $transdb->startTrans();
        $res = $transdb->table('tb_auth_group')->where(array('id'=>$where))->delete();
        $res1 = $transdb->table('tb_auth_group_access')->where(array('group_id'=>$where))->delete();
        if($res){
            $transdb->commit();
            $this->ajaxReturn(1);
        }else{
            $transdb->rollback();
            $this->ajaxReturn(0);
        }
    }

    public function admin_userList(){
        $sa = M('sys_admin');
        $ag = M('auth_group');

        $page_num = 10;
        // dump($_GET);
        // exit();
        $admin_name = trim($_GET['admin_name']);
        if($admin_name){
            $where['SA_Name'] = array('like','%'.$admin_name.'%');
        }
        if($_GET['starttime'] && !$_GET['endtime']){

            $where['SA_AddTime'] = array('egt',strtotime($_GET['starttime']));

        }elseif(!$_GET['endtime'] && $_GET['endtime']){

            $where['SA_AddTime'] = array('elt',strtotime($_GET['endtime']));

        }elseif($_GET['starttime'] && $_GET['endtime']){

            $where['SA_AddTime'] = array('between',array(strtotime($_GET['starttime']),strtotime($_GET['endtime'])));
        }
        if((int)$_GET['type']){
            $where['SA_Role_ID'] = (int)$_GET['type'];
        }
        // dump($where);
        // exit();
        $sa_count = $sa->where($where)->count();
        $page = new \Think\Page($sa_count,$page_num);
        $page->setConfig('first','首页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $show = $page->show();
        $limit = $page->firstRow.','.$page->listRows;
        $sa_info = $sa->where($where)->limit($limit)->select();

        foreach ($sa_info as $k1 => $v1) {
            $ag_info = $ag->where(array('id'=>$v1['SA_Role_ID']))->find();
            $sa_info[$k1]['role_name'] = $ag_info['title'];

            if($v1['SA_AddTime']){
                $sa_info[$k1]['SA_AddTime'] = date('Y-m-d H:i:s',$sa_info[$k1]['SA_AddTime']);
            }
        }



        $sa_all_count = $sa->count();
        $role_info2 = $role_info = $ag->select();
        foreach ($role_info as $k2 => $v2) {
            if($v2['id'] == 1){
                unset($role_info2[$k2]);
                sort($role_info2);
            }
            $role_info[$k2]['count'] = $sa->where(array('SA_Role_ID'=>$v2['id']))->count();
        }


        // dump($sa_info);
        $this->assign('getInfo',$_GET);
        $this->assign('role_info',$role_info);
        $this->assign('role_info2',$role_info2);

        $this->assign('sa_all_count',$sa_all_count);
        $this->assign('sa_info',$sa_info);
        $this->assign('sa_count',$sa_count);
        $this->assign('show',$show);
        $this->display();
    }

    public function addAdmin(){
        // dump($_POST);
        $map['SA_Name'] = trim(I('username'));
        if(trim(I('password')) != trim(I('password2'))){
            $this->error('两次输入的密码不一致');
        }
        $map['SA_Salt'] = salt();
        $map['SA_Pwd'] = sha1(trim(I('password')).$map['SA_Salt']);
        $map['SA_Phone'] = I('phone');
        $map['SA_Email'] = I('email');
        $map['SA_Desc'] = I('desc');
        $map['SA_Role_ID'] = I('role');
        $map['SA_AddTime'] = time();
        $map['SA_State'] = 1;
        $sa = M('sys_admin');
        $aga = M('auth_group_access');
        $info = $sa->add($map);

        if($info){
            $map1['uid'] = $info;
            $map1['group_id'] = $map['SA_Role_ID'];
            $aga_info = $aga->add($map1);
            if($aga_info){
                $this->success('添加成功',U('Role/admin_userList'));
            }else{
                $this->error('添加失败',U('Role/admin_userList'));
            }
        }else{
            $this->error('添加失败',U('Role/admin_userList'));
        }
    }


    public function editAdmin(){
        // dump($_POST);
        $where['SA_ID'] = $where1['uid'] = (int)I('editAdmin_id');
        $map['SA_Role_ID'] = $map1['id'] = (int)I('role');
        if($map['SA_ID'] == 1){
            $this->error('超级管理员不可编辑');
        }elseif($where['SA_ID']>0 && $map['SA_Role_ID']>0){
            $sa = M('sys_admin');
            $aga = M('auth_group_access');
            $info = $sa->where($where)->save($map);
            $info1 = $aga->where($where1)->save($map1);
            if($info && $info1){
                $this->success('修改成功',U('Role/admin_userList'));
            }else{
                $this->error('修改失败',U('Role/admin_userList'));
            }
        }else{
            $this->error('传入参数有误',U('Role/admin_userList'));
        }
    }

    public function del_admin(){
        $aids = I('aid');
        if($aids!=null){
            $sa = M('sys_admin');
            $aga = M('auth_group_access');
            $ids = trim($aids,',');
            $arr = explode(",", $ids);
            if(in_array("1", $arr)){
                $this->ajaxReturn(-2);
            }
            if(in_array($_SESSION['Admin']['info']['SA_ID'], $arr)){
                $this->ajaxReturn(-3);
            }

            if($ids){
                $where['SA_ID'] = array('in',$ids);
                $info = $sa->where($where)->delete();
                $where1['uid'] = array('in',$ids);
                $info1 = $aga->where($where1)->delete();
            }
            if($info){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(0);
            }
        }else{
            $this->ajaxReturn(-1);
        }
    }

    public function setAdminShow(){
        $aid = (int)I('aid');

        if($aid == 1){
            $this->ajaxReturn(-2);
        }
        if($_SESSION['Admin']['info']['SA_ID'] == $aid){
            $this->ajaxReturn(-3);
        }
        if($aid>0){
            $sa = M('sys_admin');
            $isShow = $sa->where(array('SA_ID' => $aid))->getField('SA_State');
            if((int)$isShow === 0){
                //不显示改为显示
                $save['SA_State'] = 1;
            }elseif((int)$isShow === 1){
                //不显示改为显示
                $save['SA_State'] = 0;
            }

            $where['SA_ID'] = $aid;
            $info = $sa->where($where)->save($save);

            if($info){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(0);
            }
        }else{
            $this->ajaxReturn(-1);
        }
    }


    public function admin_info(){

        $where['SA_ID'] = $_SESSION['Admin']['info']['SA_ID'];
        $sa = M('sys_admin');
        $ag = M('auth_group');
        $sa_info = $sa->where($where)->field('SA_ID,SA_Name,SA_Email,SA_Phone,SA_AddTime,SA_Role_ID')->find();
        if($sa_info['SA_Role_ID']){
            $ag_info = $ag->where(array('id'=>$sa_info['SA_Role_ID']))->find();
            $sa_info['role'] = $ag_info['title'];
        }
        $sa_info['SA_AddTime'] = date('Y-m-d H:i:s',$sa_info['SA_AddTime']);

        if($_SESSION['Admin']['islogin']){
            if(IS_WIN){
                $path = 'C:/wamp64/www/newBackstage';
            }else{
                $path = '.';
            }
            $logFile = $path.'/Uploads/AdminLog/'.$_SESSION['Admin']['info']['SA_ID'].'.txt';
            $read = file_get_contents($logFile);
            if($read!=null){
            	$str = trim($read,',');
            	$arr = explode(",", $str);
            	foreach ($arr as $k => $v) {
            		$content = unserialize($v);
            		$log_data[] = $content;
            	}
                krsort($log_data);
                if($_GET['p']){
                    $p = $_GET['p'];
                }else{
                    $p = 1;
                }
                $page_num = 10;
                $page = new \Think\Page(count($log_data),$page_num);
                $page->setConfig('first','首页');
                $page->setConfig('prev','上一页');
                $page->setConfig('next','下一页');
                $show = $page->show();
                $limit = $page->firstRow.','.$page->listRows;

                $log_data = array_slice($log_data, $page_num*($p-1),$page_num);
                foreach ($log_data as $key => $value) {
                    $log_data[$key]['time'] = date("Y-m-d H:i:s",$log_data[$key]['time']);
                }
            }


            // echo $content;
        }
        // dump($show);
        $this->assign('show',$show);
        $this->assign('sa_info',$sa_info);
        $this->assign('log_data',$log_data);
        // dump($sa_info);
        $this->display();
    }

    public function save_information(){
        $where['SA_ID'] = (int)I('aid');
        if(!($where['SA_ID']>0)){
            $this->ajaxReturn(-1);
        }
        $map['SA_Name'] = trim(I('username'));
        $map['SA_Phone'] = trim(I('phone'));
        $map['SA_Email'] = trim(I('email'));
        $sa = M('sys_admin');
        $info = $sa->where($where)->save($map);
        if($info){
            $_SESSION['Admin']['info']['SA_Name']=$map['SA_Name'];
            $_SESSION['Admin']['info']['SA_Phone']=$map['SA_Phone'];
            $_SESSION['Admin']['info']['SA_Email']=$map['SA_Email'];
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }
    }


    public function save_password(){
        $where['SA_ID'] = (int)I('aid');
        if(!($where['SA_ID']>0)){
            $this->ajaxReturn(-1);
        }

        $password = trim(I('password'));
        $newpassword1 = trim(I('newpassword1'));
        $newpassword2 = trim(I('newpassword2'));
        if($newpassword1 != $newpassword2){
            $this->ajaxReturn(-2);
        }

        $sa = M('sys_admin');
        $sa_info = $sa->where($where)->find();
        if($sa_info){
            if($sa_info['SA_Pwd']==sha1($password.$sa_info['SA_Salt'])){
                $map['SA_Salt'] = salt();
                $map['SA_Pwd'] = sha1($newpassword1.$map['SA_Salt']);
                $info = $sa->where($where)->save($map);
                if($info){
                    $this->ajaxReturn(1);
                }else{
                    $this->ajaxReturn(0);
                }
            }else{
                $this->ajaxReturn(-3);//原密码错误
            }
                
        }else{
            $this->ajaxReturn(0);
        }
    }

    
}