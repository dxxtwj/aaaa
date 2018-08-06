<?php

namespace App\Http\Controllers\Shopping\Admin\About;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Admin\About\AboutLogic;

class AboutController extends Controller
{

//    public function addAbout() {
//        $this->verify(
//            [
//                'aboutContent' => '',//关于我们内容
//            ]
//            , 'POST');
//        $res = AboutLogic::addAbout($this->verifyData);
//        return $res;
//    }

    public function editAbout() {
        $this->verify(
            [
                //'aboutId' => '',//关于我们
                'aboutContent' => 'no_required',//关于我们
            ]
            , 'POST');
        $res = AboutLogic::editAbout($this->verifyData);
        return $res;
    }


    public function showAbout() {

        $res = AboutLogic::showAbout();
        return $res;
    }


//    public function deleteAbout() {
//        $this->verify(
//            [
//                'aboutId' => '',//关于我们
//            ]
//            , 'POST');
//        $res = AboutLogic::deleteAbout($this->verifyData);
//        return $res;
//    }

}
