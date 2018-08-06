<?php

namespace App\Http\Controllers\V10\Api\Honor;

use App\Logic\V10\Honor\HonorLogic;
use \App\Http\Controllers\Controller;

class HonorController extends Controller
{

    /*
     * 查询全部荣誉证书
     */
    public function honorShow() {
        $this->verify(
            [
                'aboutId' => '',
            ]
            , 'GET');
        $res=[];
        if($this->verifyData['aboutId']==5){
            $res = HonorLogic::HonorShow();
        }
        return $res;
    }

}
