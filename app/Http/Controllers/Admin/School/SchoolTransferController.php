<?php

namespace App\Http\Controllers\Admin\School;

use App\Logic\School\SchoolTransferLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class SchoolTransferController extends Controller
{
    //获取全部列表
    public function getArtclassLists(){
        /*$this->verify(
            [
                'languageId' => 'no_required',
            ]
            , 'GET');*/
        $res = SchoolTransferLogic::getArtclass();

        return ['lists'=>$res];
    }




    
    //获取全部列表
    public function getArtLists(){
        $this->verify(
            [
                'class' => '',
            ]
            , 'GET');
        $res = SchoolTransferLogic::getArt($this->verifyData['class']);

        return ['lists'=>$res];
    }

}
