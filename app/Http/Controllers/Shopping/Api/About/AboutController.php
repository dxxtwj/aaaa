<?php

namespace App\Http\Controllers\Shopping\Api\About;

use \App\Http\Controllers\Controller;
use App\Model\Shopping\About\AboutModel;

class AboutController extends Controller
{
    public function showAbout() {

        $aboutModel = new  AboutModel();
        $res = $aboutModel->firstHumpArray();
        return ['data' =>$res];
    }




}
