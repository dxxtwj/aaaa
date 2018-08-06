<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Admin\CustomerService;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\CustomerService\CustomerServiceModel;
use App\Model\Shopping\Shop\ShopModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;


class CustomerServiceLogic extends ShoppingLogic
{
    public static function addCustomerService($data) {

        $shopModel = new ShopModel();
//        $ids=array();
//        $shopData = $shopModel->getHumpArray(['shop_id']);
//        if (empty($shopData)) {
//
//            throw new RJsonError('无商家店铺','SHOP_ERROR');
//        }
//        foreach ($shopData as $k => $v) {
//            $ids[] = $v['shopId'];
//        }
//        $shopId = join(',',$ids);//商户ID
        foreach ($data['customerServiceArray'] as $k => $v) {

            $customerModel = new CustomerServiceModel();
            $addData['customer_service_contact'] = empty($v['customerServiceContact']) ? '' : $v['customerServiceContact'];
            $addData['customer_service_order'] = empty($v['customerServiceOrder']) ? '' : $v['customerServiceOrder'];
            $addData['customer_service_name'] = empty($v['customerServiceName']) ? '' : $v['customerServiceName'];
            $addData['customer_service_type'] = empty($v['customerServiceType'])  ? 0 : $v['customerServiceType'];//1=电话 2=QQ

            $addData['shop_id'] = empty($v['shopId']) ? 0 : join(',',$v['shopId']);
            $customerModel->setDataByHumpArray($addData)->save();
        }
        return ;

    }

    public static function editCustomerService($data) {

        $customerModel = new CustomerServiceModel();

        $where['customer_service_id'] = $data['customerServiceId'];

        $addData['customer_service_contact'] = empty($data['customerServiceContact']) ? '' : $data['customerServiceContact'];
        $addData['customer_service_type'] = empty($data['customerServiceType']) ? '' : $data['customerServiceType'];
        $addData['customer_service_order'] = empty($data['customerServiceOrder']) ? '' : $data['customerServiceOrder'];
        $addData['customer_service_name'] = empty($data['customerServiceName']) ? '' : $data['customerServiceName'];

        $customerModel->where($where)->updateByHump($addData);

        return ;
    }

    public static function showCustomerService($data) {

        $customerModel = new CustomerServiceModel();

        if (empty($data['customerServiceId'])) {

            $res = $customerModel->orderBy('customer_service_id','DESC')->getHumpArray();
            return ['lists' => $res];

        } else {

            $where['customer_service_id'] = $data['customerServiceId'];
            $res = $customerModel->where($where)->firstHumpArray();

            if (!empty($res['shopId'])) {

                $shopModel = new ShopModel();
                $whereArray = explode(',', $res['shopId']);
                $res['shopData'] = $shopModel->whereIn('shop_id', $whereArray)->getHumpArray();
            }
            return ['data' => $res];
        }
    }

    public static function deleteCustomerService($data) {

        $customerModel = new CustomerServiceModel();
        $customerModel->where('customer_service_id', $data['customerServiceId'])->delete();
        return ;

    }
}














