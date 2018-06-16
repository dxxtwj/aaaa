<?php
namespace Home\Model;
use Think\Model;

class PinlunModel extends Model 
{

	/**
	 * 这个方法是好、中、差评的
	 * @return [type] [description]
	 */
	public function pinlun() {
		// 统计好评  中评  差评
		$arr = $this->select();
		
		foreach ($arr as $k => $v) {

			$dafen[] = $v['dafen'];

		}
		

		// 把好评的和差评的都分组处理
		for ($i = 0; $i < count($dafen); $i++) {

			if ($dafen[$i] == 1) {

				$hao[] = $dafen[$i];

			}
			if ($dafen[$i] == 2) {

				$zhong[] = $dafen[$i];

			}

			if ($dafen[$i] == 3) {

				$cha[] = $dafen[$i];

			}
		}

		// 全部评价
		$arr['zong'] = count($hao) + count($zhong) + count($cha);

		// 好评
		$arr['haoPing'] = count($hao);

		// 中评
		$arr['zhongPing'] = count($zhong);

		// 差评
		$arr['chaPing'] = count($cha);

		$array = ['1' => '好评', '中评', '差评'];

		foreach ($arr as $k => $v) {

			$arr[$k]['dafen'] = $array[$v['dafen']];

		}

		return $arr;
	}


}