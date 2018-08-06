<?php

namespace App\Http\Controllers\V10\Old;

use App\Logic\V1\Old\CasesOldLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class CasesOldController extends Controller
{

    //案例
    public function getLists()
    {
        $this->verify(
            [
                'numberId' => '',
                'number' => '',
            ]
            , 'GET');
        $lists = CasesOldLogic::Lists($this->verifyData['numberId'],$this->verifyData['number']);
        return ['lists'=>$lists];
    }

    //新闻
    public function getNews()
    {
        $this->verify(
            [
                'numberId' => '',
                'number' => '',
            ]
            , 'GET');
        $lists = CasesOldLogic::getNews($this->verifyData['numberId'],$this->verifyData['number']);
        return ['data'=>$lists];
    }

    //合影
    public function getPhoto()
    {
        $this->verify(
            [
                'numberId' => '',
                'number' => '',
            ]
            , 'GET');
        $lists = CasesOldLogic::getPhoto($this->verifyData['numberId'],$this->verifyData['number']);
        return ['data'=>$lists];
    }

    //明星
    public function getStar()
    {
        $this->verify(
            [
                'numberId' => '',
                'number' => '',
            ]
            , 'GET');
        $lists = CasesOldLogic::getStar($this->verifyData['numberId'],$this->verifyData['number']);
        return ['data'=>$lists];
    }

}
