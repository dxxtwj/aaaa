<?php

namespace App\Http\Controllers\Api;

use App\Logic\LinkLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use App\Http\Middleware\SiteId;
use \App\Http\Controllers\Controller;

class LinkController extends Controller
{


    //获取全部列表
    public function getLinkLists(){
        $data=[];
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $data['isOn']=1;
        $res = LinkLogic::getLinkList($data);

        return ['lists'=>$res];
    }

    //获取单条
    public function getLinkOne(){
        $this->verify(
            [
                'linkId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = LinkLogic::getLink($this->verifyData['linkId'],$languageId);

        return ['data'=>$res];
    }


}
