<?php

namespace App\Http\Controllers\V10\Api\Cases;

use App\Logic\V10\Cases\CasesLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class CasesController extends Controller
{


    //列表
    public function getLists()
    {
        $this->verify(
            [
                'casesTitle' => 'no_required',//语言名称
                'casesCateId' => 'no_required',//语言名称
            ]
            , 'GET');
        $this->verifyData['isOn']=1;
        $lists = CasesLogic::Lists($this->verifyData);
        return $lists;
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'casesId' => '',//语言名称
            ]
            , 'GET');
        $data = CasesLogic::getOne($this->verifyData['casesId']);
        return ['data'=>$data];
    }


}
