<?php

namespace App\Http\Controllers\Api\About;

use App\Logic\About\SidebarLogic;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class SidebarController extends Controller
{

    //获取全部列表
    public function getSidebarLists(){
        $this->verify(
            [
                'group' => 'no_required',
            ]
            , 'GET');
        $data=[];
        if(!empty($this->verifyData['group'])){
            $data['group']=$this->verifyData['group'];
        }
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $data['isOn']=1;
        $res = SidebarLogic::getSidebarList($data);
        return ['lists'=>$res];
    }

}