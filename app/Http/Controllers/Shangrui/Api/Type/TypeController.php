<?php

namespace App\Http\Controllers\Shangrui\Api\Type;

use \App\Http\Controllers\Controller;
use App\Logic\Shangrui\Api\Type\TypeLogic;

class TypeController extends Controller
{
    public function showType(){
        $this->verify(
            [
                'typeId' => '',
            ]
            ,'GET');
        $res = TypeLogic::showType($this->verifyData);
        return $res;
    }

}
