<?php
namespace App\Http\Controllers\V0\Api\Template;

use App\Logic\V0\Template\TemplateCateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class TemplateCategoryController extends Controller
{
    public function getTemplateCateLists()
    {
        $this->verify([
            //前台传过来的数据
            'isOn'=>'no_required',
            'templateCateTitle'=>'no_required',

        ],'GET');
        $this->verifyData['isOn']=1;
        $res = TemplateCateLogic::getTemplateCateList($this->verifyData);
        return ['lists'=>$res];
    }


}