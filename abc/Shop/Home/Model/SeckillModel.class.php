<?php
namespace Home\Model;
use Think\Model;

class SeckillModel extends Model
{
	public function Seckill() {
		$seckill = $this->find();
		if ($seckill['data'] == '1') {
			$seckill['data1'] = '请';
			$seckill['data2'] = '稍';
			$seckill['data3'] = '后';
		} else if ($seckill['data'] == '3'){
			$seckill['data1'] = '进';
			$seckill['data2'] = '行';
			$seckill['data3'] = '中';
		} else if ($seckill['data'] == '2') {
			$seckill['data1'] = '到';
			$seckill['data2'] = '计';
			$seckill['data3'] = '时';
		} else if ($seckill['data'] == '4'){
			$seckill['data1'] = '已';
			$seckill['data2'] = '结';
			$seckill['data3'] = '束';
		}
		return $seckill;
	}
}