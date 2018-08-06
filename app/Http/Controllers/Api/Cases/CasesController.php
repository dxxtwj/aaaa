<?php

namespace App\Http\Controllers\Api\Cases;

use App\Logic\Cases\CasesCateLogic;
use App\Logic\Cases\CasesLogic;
use App\Http\Middleware\SiteId;
use App\Logic\Menu\MenuLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class CasesController extends Controller
{
    //获取全部列表
    public function getCasesLists(){
        $this->verify(
            [
                'tableId'=>'',
                'casesCateId' => 'no_required',//分类id
                'casesTitle' => 'no_required',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $this->verifyData['languageId']=$languageId;
        $this->verifyData['isOn']=1;
        $res = CasesLogic::getCasesList($this->verifyData);

        return $res;
    }

    //获取单条
    public function getCasesOne(){
        $this->verify(
            [
                'casesId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = CasesLogic::getCases($this->verifyData['casesId'],$languageId);
        if(isset($res)){
            $last=CasesLogic::getLast($res['casesCateId'],$languageId,$res['sort']);
            $next=CasesLogic::getNext($res['casesCateId'],$languageId,$res['sort']);

            $res['lastId']=empty($last['casesId']) ? '' : $last['casesId'];
            $res['lastTitle'] =empty($last['casesTitle']) ? '' : $last['casesTitle'];
            $res['lastImg'] =empty($last['casesImage']) ? '' : $last['casesImage'];
            $res['lastDesc'] =empty($last['casesDesc']) ? '' : $last['casesDesc'];
            $res['lastCreatedAt'] =empty($last['createdAt']) ? '' : $last['createdAt'];


            $res['nextId']=empty($next['casesId']) ? '' : $next['casesId'];
            $res['nextTitle'] =empty($next['casesTitle']) ? '' : $next['casesTitle'];
            $res['nextImg'] =empty($next['casesImage']) ? '' : $next['casesImage'];
            $res['nextDesc'] =empty($next['casesDesc']) ? '' : $next['casesDesc'];
            $res['nextCreatedAt'] =empty($next['createdAt']) ? '' : $next['createdAt'];
        }
        return ['data'=>$res];
    }
    //获取推荐
    public function getRecommend(){
        $this->verify(
            [
                'tableId'=>'',
                'number' => '',//查多少条
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = CasesLogic::getRecommend($languageId,$this->verifyData['number'],$this->verifyData['tableId']);

        return ['lists'=>$res];
    }

    //获取导航列表
    public function getCasesMenuName()
    {
        $this->verify(
            [
                'urlName' => 'no_required',
                'tableId' => 'no_required',
                'casesId' => 'no_required',
                'casesCateId'=>'no_required',
                'menuId'=>'no_required'
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res=[];
        if(empty($this->verifyData['casesId']) && empty($this->verifyData['casesCateId']) && empty($this->verifyData['menuId']) && empty($this->verifyData['urlName']) && empty($this->verifyData['tableId'])){
            throw new RJsonError('请输入信息', 'CASES_ERROR');
        }
        if(!empty($this->verifyData['casesId'])){
            $res = CasesLogic::getMenuName($this->verifyData['casesId'],$languageId);
            if(!empty($res)){
               $res = $this->array2D($res, 3);
                return ['lists'=>$res];
            }
        }
        if(!empty($this->verifyData['casesCateId'])){
            $res = CasesCateLogic::getCasesCateParents($this->verifyData['casesCateId'],$languageId);
            if(!empty($res)){
               $res = $this->array2D($res, 2);
                return ['lists'=>$res];
            }
        }
        if(!empty($this->verifyData['menuId'])){
            $res = MenuLogic::getMenuParents($this->verifyData['menuId'],$languageId);
            if(!empty($res)){
                return ['lists'=>$res];
            }
        }
        if(!empty($this->verifyData['urlName']) && !empty($this->verifyData['tableId'])){
            //拿到menuId
            $menu = MenuLogic::getMenuIdByUrl($this->verifyData['urlName'],$this->verifyData['tableId'],$languageId);
            $res = MenuLogic::getMenuParents($menu['menuId'],$languageId);
            if(!empty($res)){
                return ['lists'=>$res];
            }
        }
        return ['lists'=>$res];
    }

    /*
     * @param array $res 这个是查询出来的数据
     * @param int $b 等于2 是分类的   等于3 是案例的
     */
    public function array2D($res, $b)
    {
        if ($b == 2) {
            $bool = in_array($res[0]['menuTitle'], $res[1], true);

            if ($bool) {
                unset($res[1]);
                $res[] = array_shift($res);
                $res=array_reverse($res);

            }
        }
        if ($b == 3) {
            $bool = in_array($res[0]['menuTitle'], $res[1], true);
            if ($bool) {
                unset($res[1]);
                $res[] = array_shift($res);
                $res[] = array_pop($res);
                $res=array_reverse($res);
            }
        }
        return $res;
    }

}
