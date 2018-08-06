<?php

namespace App\Http\Controllers\V10\Api;

use App\Logic\V10\LinkLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use Symfony\Component\Yaml\Tests\B;

class LinkController extends Controller
{
    //单条
    public function getLists()
    {
        $data['isOn']=1;
        $res = LinkLogic::Lists($data);
        return ['lists'=>$res];
    }


}
