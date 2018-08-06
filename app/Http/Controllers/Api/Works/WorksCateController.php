<?php

namespace App\Http\Controllers\Api\Works;

use App\Logic\Works\WorksCateLogic;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class WorksCateController extends Controller
{


    //获取全部列表
    public function getWorksCateLists(){
        $data=[];
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $data['isOn']=1;
        $res = WorksCateLogic::getWorksCateLists($data);
        return ['lists'=>$res];
    }

    //获取单条
    public function getWorksCateOne(){
        $this->verify(
            [
                'worksCateId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = WorksCateLogic::getWorksCate($this->verifyData['worksCateId'],$languageId);
        return ['data'=>$res];
    }

    //获取父级ID
    public function getParentsId(){
        $this->verify(
            [
                'worksCateId' => '',
            ]
            , 'GET');
        $res = WorksCateLogic::getWorksCateId($this->verifyData['worksCateId']);
        return ['data'=>$res];
    }

    //获取下一级
    public function getChild(){
        $this->verify(
            [
                'worksCateId' => '',
            ]
            , 'GET');
        $res = WorksCateLogic::getChildId($this->verifyData['worksCateId']);
        return ['data'=>$res];
    }


}
