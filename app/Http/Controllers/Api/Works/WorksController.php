<?php

namespace App\Http\Controllers\Api\Works;

use App\Http\Middleware\SiteId;
use App\Logic\Menu\MenuLogic;
use App\Logic\V2\User\LoginLogic;
use App\Logic\Works\WorksAdvertiseLogic;
use App\Logic\Works\WorksLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class WorksController extends Controller
{
    //获取全部列表
    public function getWorksLists(){
        $this->verify(
            [
                'worksCateId' => 'no_required',//分类id
                'worksTitle' => 'no_required',
                'worksNumber' => 'no_required',
                'worksSearch' => 'no_required',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $this->verifyData['languageId']=$languageId;
        $this->verifyData['isOn']=1;
        $res = WorksLogic::getWorksList($this->verifyData);
        return $res;
    }

    public function getWorksVote(){
        $this->verify(
            [
                'worksCateId' => '',//分类id
                'number' => 'no_required',//分类id
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $number = empty($this->verifyData['number']) ? 10 : $this->verifyData['number'];
        $res = WorksLogic::getCateWorksVote($this->verifyData['worksCateId'],$languageId,$number);
        return ['lists'=>$res];
    }

    //获取单条
    public function getWorksOne(){
        $this->verify(
            [
                'worksId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = WorksLogic::getWorks($this->verifyData['worksId'],$languageId);
        if(isset($res)){
            $last=WorksLogic::getLast($res['worksCateId'],$languageId,$res['sort']);
            $next=WorksLogic::getNext($res['worksCateId'],$languageId,$res['sort']);
            $res['lastId']=empty($last['worksId']) ? '' : $last['worksId'];
            $res['lastTitle'] = $last['worksTitle'] ?? '';
            $res['nextId']=empty($next['worksId']) ? '' : $next['worksId'];
            $res['nextTitle'] =empty($next['worksTitle']) ? '' : $next['worksTitle'];
        }
        return ['data'=>$res];
    }

    //点赞
    public function getPraise(Request $request){
        $this->verify(
            [
                'worksId' => '',
            ]
            , 'GET');
        //拿IP地址
        $request->setTrustedProxies(array('10.10.0.0/16'));
        $ip = $request->getClientIp();
        WorksLogic::Praise($this->verifyData['worksId'],$ip);
        return;
    }

    //投票
    public function getVote(Request $request){
        $this->verify(
            [
                'worksId' => '',
            ]
            , 'GET');
        //是否登录
        $uid = LoginLogic::isLogin();
        if(empty($uid)){
            throw new RJsonError('没有登录', 'NO_LOGIN');
        }
        $languageId = SiteId::getLanguageId();
        //活动是否已经结束
        WorksLogic::voteOver($this->verifyData['worksId'],$languageId);
        //拿IP地址
        $request->setTrustedProxies(array('10.10.0.0/16'));
        $ip = $request->getClientIp();
        //WorksLogic::Vote($this->verifyData['worksId'],$uid,$ip);
        return;
    }

    //面包屑
    public function getWorksMenuName(){
        $this->verify(
            [
                'urlName' => 'no_required',
                'tableId' => 'no_required',
                'menuId'=>'no_required'
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $menu=[];
        if(empty($this->verifyData['menuId']) && empty($this->verifyData['urlName']) && empty($this->verifyData['tableId'])){
            throw new RJsonError('请输入信息', 'PRODUCT_ERROR');
        }
        if(!empty($this->verifyData['urlName']) && !empty($this->verifyData['tableId'])){
            //拿到menuId
            //$this->verifyData['urlName']='http://10.7.7.2:1000/lists/works/26';
            //$this->verifyData['urlName']='http://10.7.7.2:1000/item/works/26';
            //$this->verifyData['urlName']='http://10.7.7.2:1000/lists/works/1?methodId=6';
            //$url='/web/item/works/1?xxxId=17';
            $menu = MenuLogic::getMenuNameByWorks($this->verifyData['urlName'],$this->verifyData['tableId'],$languageId);
        }
        return ['lists'=>$menu];
    }

    //作品总数、投票总数、浏览总数
    public function getCount()
    {
        $this->verify(
            [
                'worksCateId' => 'no_required',
            ]
            , 'GET');
        //作品总数
        $worksCount=WorksLogic::worksCount($this->verifyData);
        //投票总数
        $worksBrowse=WorksLogic::worksBrowse();
        //浏览总数
        $worksVote=WorksLogic::worksVote();
        $arr=[
            'worksCount'=>$worksCount,
            'browseCount'=>$worksBrowse,
            'voteCount'=>$worksVote,
        ];
        return ['data'=>$arr];
    }

    //作品广告
    public function getWorksAdvertise()
    {
        $this->verify(
            [
                'advertiseId' => '',
            ]
            , 'GET');
        $res = WorksAdvertiseLogic::getWorksAdvertiseShow($this->verifyData['advertiseId']);
        return ['data'=>$res];
    }
    //作品广告
    public function getWorksAdvertiseLists()
    {
        $data['isOn']=1;
        $res = WorksAdvertiseLogic::getWorksAdvertiseList($data);
        return $res;
    }

    public function getWorksVoteBrush()
    {
        WorksLogic::getWorksVote();
        return [];
    }

    public function redisTest()
    {
        $res = WorksLogic::redisTest();
        //$res = WorksLogic::getRedisTest();
        return ['data'=>$res];
    }

}
