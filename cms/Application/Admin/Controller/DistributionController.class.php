<?php
namespace Admin\Controller;
use Think\Controller;
class DistributionController extends CommonController {
	public function index(){
		$this->display();
	}

	public function add(){
		$postage = M('PostageRec');
		if(IS_POST){
			// dump($_POST);
			// exit;
			$postage->startTrans();
			$sql = 'truncate table tb_postage_rec';
			$res = $postage->execute($sql);
			if($res_del === false)
			{
				$postage->rollback();
				$this->error('添加配送方式失败',U('Distribution/add'));
			}
			$sort = 1;
			$default_firstweight = I('default_firstweight');
			$default_firstprice = I('default_firstprice');
			$default_secondweight = I('default_secondweight');
			$default_secondprice = I('default_secondprice');
			$data['PR_Firstweight'] = $default_firstweight;
			$data['PR_Firstprice'] = $default_firstprice;
			$data['PR_Secondweight'] = $default_secondweight;
			$data['PR_Secondprice'] = $default_secondprice;
			$data['PR_Sort'] = $sort;
			$data['PR_Dispatchareas'] = 0;
			$data['PR_Addtime'] = time();
			$res = $postage->add($data);
			if(!$res){
				$postage->rollback();
				$this->error('添加配送方式失败',U('Distribution/add'));
			}
			$random = I('random');
			$citys = I('citys');
			$firstweight = I('firstweight');
			$firstprice = I('firstprice');
			$secondweight = I('secondweight');
			$secondprice = I('secondprice');
			foreach($random as $val){
				$sort++;
				$map['PR_Firstweight'] = $firstweight[$val];
				$map['PR_Firstprice'] = $firstprice[$val];
				$map['PR_Secondweight'] = $secondweight[$val];
				$map['PR_Secondprice'] = $secondprice[$val];
				$map['PR_City'] = $citys[$val];
				$map['PR_Random'] = $val;
				$map['PR_Sort'] = $sort;
				$map['PR_Addtime'] = time();
				$map['PR_Dispatchareas'] = 0;
				$res = $postage->add($map);
				if(!$res){
					$postage->rollback();
					$this->error('添加配送方式失败',U('Distribution/add'));
				}
			}

			/*不配送区域*/
			$nodispatchareas = I('nodispatchareas');
			$no_data['PR_City'] = $nodispatchareas;
			$no_data['PR_Dispatchareas'] = 1;
			$no_data['PR_Addtime'] = time();
			$res = $postage->add($no_data);
			if($res){
				$postage->commit();
				$this->error('添加配送方式成功',U('Distribution/add'));
			}else{
				$postage->rollback();
				$this->error('添加配送方式失败',U('Distribution/add'));
			}
		}else{
			// 查默认的配送方式
			$default_where['PR_Random'] = array('exp', 'is NULL');
			$default_where['PR_Sort'] = 1;
			$default_where['PR_Dispatchareas'] = 0;
			$default_res = $postage->where($default_where)->find();
			// 查不配送的
			$no_where['PR_Random'] = array('exp', 'is NULL');
			$no_where['PR_Sort'] = 0;
			$no_where['PR_Dispatchareas'] = 1;
			$no_res = $postage->where($no_where)->find();
			// 查自定义的
			$where['PR_Random'] = array('exp', 'is not NULL');
			$where['PR_Dispatchareas'] = 0;
			$res = $postage->where($where)->select();
			// dump($default_res);
			// dump($no_res);
			// dump($res);
			$this->assign('default_res', $default_res);
			$this->assign('no_res', $no_res);
			$this->assign('res', $res);
			$this->display();
		}
	}

	// 生成32位随机字符串
     public function getRandStr(){
 		$data = '';
 		$strPol = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
 		// 最大长度
 		$max = strlen($strPol) - 1;
 		for ($i=0; $i<32; $i++){
			//rand($min,$max)生成介于min和max两个数之间的一个随机整数
    		$data .= $strPol[rand(0, $max)];
   		}
		$this->ajaxReturn($data);
	}
}
