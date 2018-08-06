<?php

namespace App\Http\Controllers\Shopping\Admin\CustomerService;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Admin\CustomerService\CustomerServiceLogic;

class CustomerServiceController extends Controller
{

    public function addCustomerService() {
        $this->verify(
            [
                'customerServiceArray' => '',
            ]
            ,'POST');
        $res = CustomerServiceLogic::addCustomerService($this->verifyData);
        return $res;
    }


    public function editCustomerService() {
        $this->verify(
            [
                'customerServiceId' => '',
                'customerServiceOrder' => 'no_required',
                'customerServiceName' => 'no_required',
                'customerServiceContact' => 'no_required',
                'customerServiceType' => 'no_required',
            ]
            ,'POST');
        $res = CustomerServiceLogic::editCustomerService($this->verifyData);
        return $res;
    }

    public function showCustomerService() {
        $this->verify(
            [
                'customerServiceId' => 'no_required',
            ]
            ,'POST');
        $res = CustomerServiceLogic::showCustomerService($this->verifyData);
        return $res;
    }

    public function deleteCustomerService() {
        $this->verify(
            [
                'customerServiceId' => '',
            ]
            ,'POST');
        $res = CustomerServiceLogic::deleteCustomerService($this->verifyData);
        return $res;
    }
}
