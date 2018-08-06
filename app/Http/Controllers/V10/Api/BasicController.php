<?php

namespace App\Http\Controllers\V10\Api;

use App\Logic\V10\BasicLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use Symfony\Component\Yaml\Tests\B;

class BasicController extends Controller
{
    //单条
    public function getBasic()
    {
        $data = BasicLogic::getBasic();
        return ['data'=>$data];
    }


}
