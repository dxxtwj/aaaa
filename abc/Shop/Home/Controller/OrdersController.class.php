<?php
namespace Home\Controller;
use Think\Controller;
use Think\Page;
use \Redis;

/**
 * 订单控制器
* @Author: Roway
* @E-mail: 18933582553@189.cn
* @blogs: www.lowewe.com
* @phone: 18933582553
 */
class OrdersController extends Controller
{
	/**
	 * 显示提交订单成功页面
	 */
	public function success()
	{
		// 连接商品订单表
		$order = M('order');
		$id =I('get.id');
		$data = $order->where(['id'=>$id])->find();
		$this->assign('data', $data);	
		$this->display();
	}

	/**
	*显示支付成功的页面
	*/
	public function paySuccessful($id)
	{
		
		// 连接商品订单表
		$order = D('order');

		$data = $order->where(['id'=>$id])->find();
		// 处理订单状态,1 待付款 → 2 待发货
		$data['state'] = '2';
		
		$data = $order->where(['id'=>$id])->save($data);		
		if ($data) {
			$this->assign('data', $data);	
            $this->success('付款成功','paySuccessful');
            exit;
		} else {
			$this->assign('data', $data);	
			$this->error('付款失败','success');
            exit;
		}

		$this->assign('data', $data);
		$this->display();
	}
}
