<?php

namespace App\Http\Controllers\Shopping\Api\CustomerService;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Api\CustomerService\CustomerServiceLogic;

class CustomerServiceController extends Controller
{
    public function showCustomerService() {
        $this->verify(
            [
                'shopId' => '',
                'customerServiceId' => 'no_required',
            ]
            , 'POST');
        $res = CustomerServiceLogic::showCustomerService($this->verifyData);
        return $res;
    }

}
