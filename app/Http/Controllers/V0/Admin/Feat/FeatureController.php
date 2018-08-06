<?php
namespace App\Http\Controllers\V0\Admin\Feat;

use App\Logic\V0\Feature\FeatLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class FeatureController extends Controller
{
    public function addFeat()
    {
        $this->verify([
            'siteId'=>'',
            'name'=>'',
            'status'=>'',
        ],'POST');
        FeatLogic::addFeat($this->verifyData);
        return;
    }

    public function getFeatLists()
    {
        $this->verify([
            'siteId'=>'no_required',
        ],'GET');
        $res=FeatLogic::getFeatLists($this->verifyData);
        return $res;
    }

    //查单条
    public function getFeatOne()
    {
        $this->verify([
            'featureId'=>'',
        ],'GET');
        $res = FeatLogic::getFeatOne($this->verifyData['featureId']);
        return ['data'=>$res];
    }

    //删除
    public function delFeat()
    {
        $this->verify([
            'featureId'=>'',
        ],'POST');
        FeatLogic::delFeat($this->verifyData['featureId']);

        return;
    }

    //修改
    public function editFeat(){
        $this->verify([
            'featureId'=>'',
            'siteId'=>'',
            'name'=>'',
            'status'=>'',

        ],'POST');
        FeatLogic::editFeat($this->verifyData,$this->verifyData['featureId']);
    }

}