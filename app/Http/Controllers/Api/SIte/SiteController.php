<?php

namespace App\Http\Controllers\Api\Site;

use App\Logic\Site\SiteLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class SiteController extends Controller
{

    //获取单条
    public function getSiteLogo(){
        $res = SiteLogic::getSiteLogo();
        return ['data'=>$res];
    }
    //获取站点的域名模板
    public function getSiteByDomain()
    {
        $this->verify(
            [
                'domainUrl' => '',
                //'code'=> '',
            ]
            , 'GET');
        $res = SiteLogic::getSiteByDomain($this->verifyData);
        return ['data'=>$res];
    }


}
