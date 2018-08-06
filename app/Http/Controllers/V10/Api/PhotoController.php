<?php

namespace App\Http\Controllers\V10\Api;

use App\Logic\V10\PhotoLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class PhotoController extends Controller
{
    //列表
    public function getLists()
    {
        $this->verify(
            [
                'photoTitle' => 'no_required',//语言名称
            ]
            , 'GET');
        $this->verifyData['isOn']=1;
        $lists = PhotoLogic::Lists($this->verifyData);
        return $lists;
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'photoId' => '',//语言名称
            ]
            , 'GET');
        $data = PhotoLogic::getOne($this->verifyData['photoId']);
        return ['data'=>$data];
    }

    //推荐
    public function getRecommend()
    {
        $this->verify(
            [
                'number' => '',//语言名称
            ]
            , 'GET');
        $res = PhotoLogic::recommend($this->verifyData['number']);
        return ['lists'=>$res];
    }


}
