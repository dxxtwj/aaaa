<?php

namespace App\Http\Controllers\Api\Cases;

use App\Logic\Cases\CasesCateLogic;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class CasesCateController extends Controller
{


    //获取全部列表
    public function getCasesCateLists(){
        $this->verify(
            [
                'tableId' => '',
            ]
            , 'GET');
        $data=[];
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $data['isOn']=1;
        $data['tableId']=$this->verifyData['tableId'];
        $res = CasesCateLogic::getCasesCateList($data);
        return ['lists'=>$res];
    }

    //获取单条
    public function getCasesCateOne(){
        $this->verify(
            [
                'casesCateId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = CasesCateLogic::getCasesCate($this->verifyData['casesCateId'],$languageId);

        return ['data'=>$res];
    }

    //获取父级ID
    public function getParentsId(){
        $this->verify(
            [
                'casesCateId' => '',
            ]
            , 'GET');
        $res = CasesCateLogic::getCasesCateId($this->verifyData['casesCateId']);
        return ['data'=>$res];
    }

    //获取下一级
    public function getChild(){
        $this->verify(
            [
                'casesCateId' => '',
            ]
            , 'GET');
        $res = CasesCateLogic::getChildId($this->verifyData['casesCateId']);
        return ['data'=>$res];
    }

    //获取下一级所有
    public function getCasesKids()
    {
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'casesCateId' => '',//新闻ID
            ]
            , 'POST');
        $languageId = SiteId::getLanguageId();
        $res = CasesCateLogic::getCasesCateKids($this->verifyData['casesCateId'],$this->verifyData['tableId'],$languageId);
        return ['lists'=>$res];
    }

    //获取父亲
    public function getParents()
    {
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
            ]
            , 'POST');
        $languageId = SiteId::getLanguageId();
        $res = CasesCateLogic::getParents($this->verifyData['tableId'],$languageId);
        return ['lists'=>$res];
    }

}
