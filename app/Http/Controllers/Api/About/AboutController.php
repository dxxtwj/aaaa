<?php

namespace App\Http\Controllers\Api\About;

use App\Logic\AboutLogic;
use App\Logic\About\SidebarLogic;
use App\Http\Middleware\SiteId;
use App\Logic\Menu\MenuLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use PhpParser\Node\Expr\Empty_;

class AboutController extends Controller
{

    //获取全部列表
    public function getAboutLists()
    {
        $data=[];
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $data['isOn']=1;
        $res = AboutLogic::getAboutList($data);
        return ['lists'=>$res];
    }

    //获取单条
    public function getAboutOne()
    {
        $this->verify(
            [
                'aboutId' => 'no_required',
                'aboutType'=>'no_required'
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res=[];
        if(empty($this->verifyData['aboutId']) && empty($this->verifyData['aboutType'])){
            throw new RJsonError('请输入类型', 'ABOUT_ERROR');
        }
        if(!empty($this->verifyData['aboutId'])){
            $res = AboutLogic::getAbout($this->verifyData['aboutId'],$languageId);
        }else{
            if(!empty($this->verifyData['aboutType'])){
                $res = AboutLogic::getAboutType($this->verifyData['aboutType'],$languageId);
            }
        }
        if(!empty($res)){
            //从侧栏获取group
            $sidebar = SidebarLogic::getSidebarByAboutId($res['aboutId']);
            $res['group']=$sidebar['group'] ?? '';
        }
        return ['data'=>$res];
    }

    //获取信息导航列表
    public function getAboutMenuName()
    {
        $this->verify(
            [
                'aboutId' => 'no_required',
                'aboutType'=>'no_required'
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        if(empty($this->verifyData['aboutId']) && empty($this->verifyData['aboutType'])){
            throw new RJsonError('请输入类型', 'ABOUT_ERROR');
        }
        if(!empty($this->verifyData['aboutId'])){
            $aboutId = $this->verifyData['aboutId'];
        }else{
            if(!empty($this->verifyData['aboutType'])){
                $about = AboutLogic::getAboutType($this->verifyData['aboutType'],$languageId);
                $aboutId = empty($about['aboutId']) ? '' : $about['aboutId'];
            }
        }
        //获取本身
        /*$res2 = AboutLogic::getAbout($aboutId,$languageId);
        if(empty($res2)){
            throw new RJsonError('没有信息', 'ABOUT_ERROR');
        }
        $res3[] = $res2;*/
        $res3=[];
        //获取菜单标题
        $arr = MenuLogic::getMenuNameByClassId($aboutId,$languageId,4);
        if(empty($arr)){
            //获取关于我们列表
            $data['languageId']=$languageId;
            $data['isOn']=1;
            $res = AboutLogic::getAboutList($data);
            foreach ($res as $key => $value){
                $arr = MenuLogic::getMenuNameByClassId($value['aboutId'],$languageId,4);
                if(!empty($arr)){
                    $arr = array_merge($arr,$res3);
                    return ['lists'=>$arr];
                }
            }
        }else{
            $arr = array_merge($arr,$res3);
        }
        return ['lists'=>$arr];
    }

    //获取广告图
    public function getAboutBanner()
    {
        $this->validate(null, [
            'aboutType' => 'required|string',
        ]);
        $languageId = SiteId::getLanguageId();
        $arr=[];
        $res = AboutLogic::getAboutType($this->verifyData['aboutType'],$languageId);
        if($res){
            $arr=AboutLogic::getAboutBanner($res['aboutId']);
        }
        return ['lists'=>$arr];
    }

}
