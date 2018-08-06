<?php

namespace App\Http\Controllers\V10\Api\News;

use App\Logic\V10\News\NewsLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class NewsController extends Controller
{
    //列表
    public function getLists()
    {
        $this->verify(
            [
                'newsTitle' => 'no_required',//语言名称
                'newsCateId' => 'no_required',//语言名称
            ]
            , 'GET');
        $this->verifyData['isOn']=1;
        $lists = NewsLogic::Lists($this->verifyData);
        return $lists;
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'newsId' => '',//语言名称
            ]
            , 'GET');
        $data = NewsLogic::getOne($this->verifyData['newsId']);
        return ['data'=>$data];
    }

}
