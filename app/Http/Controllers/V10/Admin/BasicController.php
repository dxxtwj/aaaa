<?php

namespace App\Http\Controllers\V10\Admin;

use App\Logic\V10\BasicLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use Symfony\Component\Yaml\Tests\B;

class BasicController extends Controller
{
    //添加
    public function Basic()
    {
        $this->verify(
            [
                'company' => '',
                'address'=>'no_required',
                'phone'=>'no_required',
                'personOne'=>'no_required',
                'personTwo'=>'no_required',
                'copyright'=>'no_required',
                'caseNumber'=>'no_required',
                'introduction'=>'no_required',
                'fax'=>'no_required',
                'logo'=>'no_required',
                'contactQq'=>'no_required',
                'scanCode'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required'
            ]
            , 'POST');
        BasicLogic::edit($this->verifyData);
        return;
    }

    //单条
    public function getBasic()
    {
        $data = BasicLogic::getBasic();
        return ['data'=>$data];
    }


}
