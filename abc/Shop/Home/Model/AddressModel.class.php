<?php
namespace Home\Model;
use Think\Model;

class AddressModel extends Model
{
	public function PayModel() {
		// $address = M('address');
		$info = $this->select();
		$areas = M('areas');
		foreach ($info as $k=>$v) {
			$info[$k]['area1'] = $areas->where(['id'=>$v['area1']])->field('area_name')->find()['area_name'];
			$info[$k]['area2'] = $areas->where(['id'=>$v['area2']])->field('area_name')->find()['area_name'];
			$info[$k]['area3'] = $areas->where(['id'=>$v['area3']])->field('area_name')->find()['area_name'];
		}
		// $info = 
		return $info;
		// select();
	}
}