<?php

namespace App\Http\Controllers\V1\Manage;

use App\Logic\Exception;
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
    public function domain()
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

    //获取站点的域名模板
    public function template(Request $request, $templateId)
    {
        if (empty($templateId)){
            throw new Exception('没有传入模板id', 'TEMPLATE_ID_MUST_INPUT');
        }
        $res = SiteLogic::getTemplate($templateId);
        $res2['nuxtPath']=$res['nuxtPath'] ?? '';
        return ['data'=>$res2];
    }


}
