<?php

namespace App\Http\Controllers\V10\Api;

use App\Logic\V10\BannerLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class BannerController extends Controller
{

    //列表
    public function getLists()
    {
        $data['isOn']=1;
        $lists = BannerLogic::Lists($data);
        return ['lists'=>$lists];
    }

}
