<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;

class UserController extends  CommonController {
	//显示用户首页
	public function index() {
		
		$user = D('User');

		//显示分页类
        $total = $user->count();
        $p = new Page($total, 5);
        $brr = $p->show();

		$arr = $user->order('id desc')->limit($p->firstRow, $p->listRows)->field('id,username,sex,phone,email,addtime,grade,status,integral')->User();
		$this->assign('brr', $brr);
		$this->assign('arr', $arr);
		$this->display();
	}

	//处理修改用户的数据
	public function doEdit() {

		$user = D('User');
		$add = I('POST.');
		$data = $user->create($add);
		// var_dump($data);//exit;
		if (!$data){
			 exit($user->getError());
		}else{
			if (empty($data['password'])) {
				unset($data['password']);
			} else {
				$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
			}
			$arr = $user->field('sex,phone,email,password,age,grade,integral')->filter('strip_tags')->where(['id'=>$add['id']])->save($data);
			$this->redirect('User/index');
		}
	}
	
	//用户修改状态
    public function save() {
        $user = M('User');
        $id = I('get.id');
        // dump($id);exit;
        $crr = $user->where(["id"=>$id])->select();
        $crr = $crr[0]['status'];
        // $drr = $crr[0]['id'];
        if ($crr == '1') {
           $arr['status'] = '2'; 
        } else {
            $arr['status'] = '1';
        }
        $brr = $user->where('id='.$id)->save($arr);
        
        if ($brr == '1') {
            $this->ajaxReturn($id);
        } else {
            $this->ajaxReturn('-1');
        }     
    }

    //修改用户信息
    public function edit() {
    	$user = D('user');
    	$id = I('get.id');
    	$arr = $user->field('username,tname,phone,email,sex,grade,age,integral')->where(['id'=>$id])->Edit();
    	// var_dump($arr);
    	$this->assign('arr',$arr);
    	$this->display();
    }


    //显示用户等级模版
    public function grade() {
    	$user = D('User');
    	$id = I('get.id');

    	//显示分页类
        $total[] = $user->count();
        $total[] = $user->where(['grade'=>'1'])->count();
        $total[] = $user->where(['grade'=>'2'])->count();
        $total[] = $user->where(['grade'=>'3'])->count();
        $total[] = $user->where(['coupon'=>['neq',0]])->count();
        $total[] = $user->where(['coupon'=>['eq',0]])->count();
        // dump($arr);exit;
        $zs = "display: none;";

        //判断要显示的用户等级
        if ($id == '1') {
        	$p = new Page($total['1'], 5);
        	$arr = $user->ordinary($p);
        } else {
        	if ($id == '2') {
        		$p = new Page($total['2'], 5);
        		$arr = $user->vip($p);
        	} else {
        		if ($id == '3') {
        			$p = new Page($total['3'], 5);
        			$arr = $user->dirmond($p);
        		} else {
                    if ($id == '4') {
                       $p = new Page($total['4'], 5);
                       $arr = $user->nocoupon($p);
                    } else {
                        if ($id == '5') {
                            $p = new Page($total['5'], 5);
                             $arr = $user->coupon($p);
                             $zs = '';
                            } else {
        			            $p = new Page($total['0'], 5);
	    		                $arr = $user->whole($p);     
                        }
                    }
        		}
        	}	
        }
        
		$brr = $p->show();
        $this->assign('zs',$zs);
    	$this->assign('total', $total);
    	$this->assign('brr', $brr);
		$this->assign('arr', $arr);
    	$this->display();
    }

    //修改优惠劵信息
    public function check_val() {
        $arr = I('get.');
        $get['coupon'] = $arr['coupon'];
        $join = join(',',$arr['id']);
        $array = array('id' => ['in', $join]);
        $order = M('user');
        $order = $order->where($array)->save($get);
        $this->ajaxReturn($join);
    }
}