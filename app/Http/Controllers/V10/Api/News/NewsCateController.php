<?php

namespace App\Http\Controllers\V10\Api\News;

use App\Logic\V10\News\NewsCateLogic;
use App\Logic\V10\News\NewsLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class NewsCateController extends Controller
{
    //列表
    public function getLists()
    {
        $data['isOn']=1;
        $lists = NewsCateLogic::Lists($data);
        return ['lists'=>$lists];
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'newsCateId' => '',//语言名称
            ]
            , 'GET');
        $data = NewsCateLogic::getOne($this->verifyData['newsCateId']);
        return ['data'=>$data];
    }

    public function getNewsByCateId()
    {
        $this->verify(
            [
                'number1' => 'no_required',//公司动态
                'number2' => 'no_required',//娱乐新闻
                'number3' => 'no_required',//行程通告
                'number4' => 'no_required',//代言新闻
            ]
            , 'GET');
        if(empty($this->verifyData)){
            throw new RJsonError('请输入数据', 'ERROR');
        }
        //行程通告
        $res1['newsCateTitle']='行程通告';
        $res1['newsCateId']=3;
        $res1['newsLists'] = NewsLogic::recommend($this->verifyData['number3'],3);
        //公司动态
        $res2['newsCateTitle']='公司动态';
        $res2['newsCateId']=1;
        $res2['newsLists'] = NewsLogic::recommend($this->verifyData['number1'],1);
        //娱乐新闻
        $res3['newsCateTitle']='娱乐新闻';
        $res3['newsCateId']=2;
        $res3['newsLists'] = NewsLogic::recommend($this->verifyData['number2'],2);
        //代言新闻
        $res4['newsCateTitle']='代言新闻';
        $res4['newsCateId']=4;
        $res4['newsLists'] = NewsLogic::recommend($this->verifyData['number4'],4);
        $arr['news1']=$res2;
        $arr['news2']=$res3;
        $arr['news3']=$res1;
        $arr['news4']=$res4;
        return ['data'=>$arr];
    }

}
