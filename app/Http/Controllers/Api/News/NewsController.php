<?php

namespace App\Http\Controllers\Api\News;

use App\Logic\News\NewsCateLogic;
use App\Logic\News\NewsLogic;
use App\Logic\Menu\MenuLogic;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class NewsController extends Controller
{


    //获取全部列表
    public function getNewsLists(){
        $this->verify(
            [
                'tableId'=>'',//第几套新闻，1、2、3、4、5
                'newsCateId' => 'no_required',//分类id
                'newsTitle' => 'no_required',
                'newsSort'=>'no_required',
                'nameSort'=>'no_required'
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $this->verifyData['languageId']=$languageId;
        $res = NewsLogic::getNewsList($this->verifyData,$this->verifyData['tableId']);

        return $res;
    }

    //获取单条
    public function getNewsOne(){
        $this->verify(
            [
                'newsId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = NewsLogic::getNews($this->verifyData['newsId'],$languageId);

        if(isset($res)){
            $last=NewsLogic::getLast($res['newsCateId'],$languageId,$res['sort']);
            $next=NewsLogic::getNext($res['newsCateId'],$languageId,$res['sort']);
            $res['lastId']=empty($last['newsId']) ? '' : $last['newsId'];
            $res['lastTitle'] =empty($last['newsTitle']) ? '' : $last['newsTitle'];
            $res['lastImg'] =empty($last['newsThumb']) ? '' : $last['newsThumb'];
            $res['lastDesc'] =empty($last['newsDesc']) ? '' : $last['newsDesc'];
            $res['lastCreatedAt'] =empty($last['createdAt']) ? '' : $last['createdAt'];

            $res['nextId']=empty($next['newsId']) ? '' : $next['newsId'];
            $res['nextTitle'] =empty($next['newsTitle']) ? '' : $next['newsTitle'];
            $res['nextImg'] =empty($next['newsThumb']) ? '' : $next['newsThumb'];
            $res['nextDesc'] =empty($next['newsDesc']) ? '' : $next['newsDesc'];
            $res['nextCreatedAt'] =empty($next['createdAt']) ? '' : $next['createdAt'];

        }

        return ['data'=>$res];
    }

    //获取推荐
    public function getRecommend(){
        $this->verify(
            [
                'tableId'=>'',//第几套新闻，1、2、3、4、5
                'number' => '',//查多少条
                'newsCateId' => 'no_required',//查多少条
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = NewsLogic::getRecommend($languageId,$this->verifyData['number'],$this->verifyData['tableId'],'',$this->verifyData['newsCateId'] ?? '');

        return ['lists'=>$res];
    }

    //按点击量，每套新闻的推荐
    public function getHitLists(){
        $this->verify(
            [
                'number' => '',//查多少条
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = NewsLogic::getAllNews($languageId,$this->verifyData['number']);
        return ['lists'=>$res];
    }

    //获取导航列表
    public function getMenuName()
    {
        $this->verify(
            [
                'urlName' => 'no_required',
                'tableId' => 'no_required',
                'newsId' => 'no_required',
                'newsCateId'=>'no_required',
                'menuId'=>'no_required'
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res=[];
        if(empty($this->verifyData['newsId']) && empty($this->verifyData['newsCateId']) && empty($this->verifyData['menuId']) && empty($this->verifyData['urlName']) && empty($this->verifyData['tableId'])){
            throw new RJsonError('请输入信息', 'NEWS_ERROR');
        }
        if(!empty($this->verifyData['newsId'])){
            $res = NewsLogic::getMenuName($this->verifyData['newsId'],$languageId);
            if(!empty($res)){
                $res = $this->array2D($res, 3);
                return ['lists'=>$res];
            }
        }
        if(!empty($this->verifyData['newsCateId'])){
            $res = NewsCateLogic::getNewsCateParents($this->verifyData['newsCateId'],$languageId);
            if(!empty($res)){
                $res = $this->array2D($res, 2);
                return ['lists'=>$res];
            }
        }
        if(!empty($this->verifyData['urlName']) && !empty($this->verifyData['tableId'])){
            //拿到menuId
            $menu = MenuLogic::getMenuIdByUrl($this->verifyData['urlName'],$this->verifyData['tableId'],$languageId);
            if (!empty($menu['menuId'])) {

                $res = MenuLogic::getMenuParents($menu['menuId'],$languageId);
            }
            if(!empty($res)){
                return ['lists'=>$res];
            }
        }
        if(!empty($this->verifyData['menuId'])){
            $res = MenuLogic::getMenuParents($this->verifyData['menuId'],$languageId);
            if(!empty($res)){
                return ['lists'=>$res];
            }
        }
        return ['lists'=>$res];
    }

    //该站点的全局搜索
    public function getNewsByName()
    {
        $this->verify(
            [
                'newsTitle' => '',//标题搜索
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = NewsLogic::getNewsByNameList($languageId,$this->verifyData);
        return $res;
    }
    public function array2D($res, $b)
    {
        if ($b == 2) {
            if(!empty($res[0])){
                $bool = in_array($res[0]['menuTitle'], $res[1], true);
                if ($bool) {
                    unset($res[1]);
                    $res[] = array_shift($res);
                    $res=array_reverse($res);
                }
            }else{
                unset($res[0]);
                $res[] = array_shift($res);
                $res=array_reverse($res);
            }
        }
        if ($b == 3) {
            if(!empty($res[0])){
                $bool = in_array($res[0]['menuTitle'], $res[1], true);
                if ($bool) {
                    unset($res[1]);
                    $res[] = array_shift($res);
                    $res[] = array_pop($res);
                    $res=array_reverse($res);
                }
            }else{
                unset($res[0]);
                $res[] = array_shift($res);
                $res[] = array_pop($res);
                $res=array_reverse($res);
            }
        }
        return $res;
    }

}
