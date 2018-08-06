<?php

namespace App\Http\Controllers\Api;

use App\Logic\BasicLogic;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class BasicController extends Controller
{

    //获取单条
    public function getBasic(){
        $languageId = SiteId::getLanguageId();
        $res = BasicLogic::getBasicAll($languageId);
        return ['data'=>$res];
    }

}