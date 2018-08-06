<?php
namespace App\Http\Controllers\V0\Api\Template;


use App\Logic\V0\Template\TemplateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class TemplateController extends Controller
{
    public function getTemplateList()
    {
        $this->verify(
            [
                //根据传过来的数据去模糊查询这些字段
                'templateCateId' => 'no_required',
                'templateTitle' => 'no_required',
            ]
            , 'GET');
        $this->verifyData['isOn']=1;
        $res = TemplateLogic::getTemplateList($this->verifyData);

        return $res;
    }

    public function getTemplateOne()
    {
        $this->verify([
            //前台传过来的数据
            'templateId'=>''
        ],'GET');
        $res = TemplateLogic::getTemplateOne($this->verifyData);
        $desc = TemplateLogic::getTemplateDesc($this->verifyData);
        if(!empty($res)){
            $res['desc']=empty($desc) ? [] : $desc;
        }
        return ['data'=>$res];
    }

    public function getRecommend()
    {
        $this->verify([
            //前台传过来的数据
           'number'=>''
        ],'GET');
        $res = TemplateLogic::getRecommend($this->verifyData['number']);
        return ['lists'=>$res];
    }
}