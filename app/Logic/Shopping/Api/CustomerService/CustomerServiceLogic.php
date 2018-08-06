<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Api\CustomerService;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\CustomerService\CustomerServiceModel;
use App\Model\Shopping\Shop\ShopModel;

class CustomerServiceLogic extends ShoppingLogic
{

    public static function showCustomerService($data) {
        $customerModel = new CustomerServiceModel();
        $res=array();
        if (empty($data['customerServiceId'])) {

            $custData1 = $customerModel
                ->whereRaw('FIND_IN_SET(?,shop_id)', $data['shopId'])
                ->orderBy('customer_service_order','DESC')
                ->getHumpArray(['customer_service_id']);

            $custData2 = $customerModel
                ->where('shop_id', 0)
                ->orderBy('customer_service_order','DESC')
                ->getHumpArray(['customer_service_id']);

            if (!empty($custData1)) {

                foreach ($custData1 as $k => $v) {

                    $ids[] = $v['customerServiceId'];

                }
            }

            if (!empty($custData2)) {

                foreach ($custData2 as $k => $v) {

                    $ids[] = $v['customerServiceId'];

                }
            }

            if (!empty($ids)) {

                $res = $customerModel->whereIn('customer_service_id', $ids)->getHumpArray();
            }

            return ['lists' => $res];

        } else {

            $where['customer_service_id'] = $data['customerServiceId'];
            $res = $customerModel
                ->whereRaw('FIND_IN_SET(?,shop_id)', $data['shopId'])
                ->where($where)
                ->firstHumpArray();

            if (!empty($res['shopId'])) {

                $shopModel = new ShopModel();
                $whereArray = explode(',', $res['shopId']);
                $res['shopData'] = $shopModel->whereIn('shop_id', $whereArray)->getHumpArray();
            }

            return ['data' => $res];
        }
    }


}