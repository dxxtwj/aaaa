<?php

namespace App\Http\Controllers\V10\Api\Cases;

use App\Logic\V10\Cases\CasesCateLogic;
use App\Logic\V10\Cases\CasesLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class CasesCateController extends Controller
{

    //列表
    public function getLists()
    {
        $data['isOn']=1;
        $lists = CasesCateLogic::Lists($data);
        return ['lists'=>$lists];
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'casesCateId' => '',//语言名称
            ]
            , 'GET');
        $data = CasesCateLogic::getOne($this->verifyData['casesCateId']);
        return ['data'=>$data];
    }

    //单条
    public function getRecommend()
    {
        $this->verify(
            [
                'number' => '',//语言名称
            ]
            , 'GET');
        $res1 = CasesCateLogic::getRecommend(1);
        $res1['casesLists'] = CasesLogic::recommend($this->verifyData['number'],$res1['casesCateId']);
        $res2 = CasesCateLogic::getRecommend(2);
        $res2['casesLists'] = CasesLogic::recommend($this->verifyData['number'],$res2['casesCateId']);
        $arr['cases1']=$res1;
        $arr['cases2']=$res2;

        return ['data'=>$arr];
    }

}
