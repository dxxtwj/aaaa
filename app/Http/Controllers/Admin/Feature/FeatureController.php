<?php
//功能
namespace App\Http\Controllers\Admin\Feature;

use App\Logic\Feature\FeatureLogic;
use App\Logic\LanguageLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class FeatureController extends Controller
{
    //获取全部列表
    public function getFeatureLists(){

        $res = FeatureLogic::getFeatureLists();
        return $res;
    }

    //修改状态
    public function editFeatureStatus()
    {
        $this->validate(null, [
            'featureId' => 'required|integer',
            'status' => 'required|integer',
        ]);
        FeatureLogic::editFeature($this->verifyData);
        return;
    }

}
