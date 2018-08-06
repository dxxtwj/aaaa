<?php

namespace App\Http\Controllers\Api\SiteAllocation;

use App\Logic\AboutLogic;
use App\Logic\SiteAllocation\SiteAllocationLogic;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class SiteAllocationController extends Controller
{
    public function getSeoOne()
    {
        $this->verify(
            [
                'type' => '',//类型
                'tableId'=>'',//系统ID
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $this->verifyData['languageId']=$languageId;
        $res = SiteAllocationLogic::getSeoApi($this->verifyData);
        return ['data'=>$res];
    }
}
