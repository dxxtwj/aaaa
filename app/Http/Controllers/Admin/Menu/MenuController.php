<?php

namespace App\Http\Controllers\Admin\Menu;

use App\Logic\Menu\MenuLogic;
use App\Logic\News\NewsCateLogic;
use App\Logic\Product\ProductCateLogic;
use App\Logic\SiteAllocation\SiteAllocationLogic;
use App\Logic\Cases\CasesCateLogic;
use App\Logic\AboutLogic;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class MenuController extends Controller
{
    /*
     *  lang[
     *      menuTitle  分类名称
     *      menuType   类型
     *      menuUrl    链接
     *      classType  类型值
     *      classId    选中的ID值
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *      menuPath[
     *          menuDescId  菜单详情ID
     *          menuType    类型
     *          urlLink     外网链接 --可空
     *          urlName     名称、news、product  --可空
     *          tableId     第几套ID  --可空
     *          cateId      分类ID  --可空
     *          descId      详情页ID  --可空
     *      ]
     *  ]
     */
    //添加
    public function AddMenu()
    {
        $this->verify(
            [
                'pid' => '',//分类id
                'isOn'=>'',//是否显示
                /*'menuType'=>'',//类型*/
                'sort'=>'',//排序
                'newOpen'=>'',//排序
                /*'menuUrl'=>'',//链接*/
                'menuThumb' => 'no_required',//图片
                'lang'=>'',
            ]
            , 'POST');
        MenuLogic::addAll($this->verifyData);
        return;
    }

    //获取全部列表
    public function getMenuLists(){
        $this->verify(
            [
                'languageId' => 'no_required',
                'isOn' => 'no_required',
            ]
            , 'GET');
        $res = MenuLogic::getMenuList($this->verifyData);

        return ['lists'=>$res];
    }

    //获取菜单顶级列表--广告图用
    public function getMenuParentsLists(){
        $this->verify(
            [
                'languageId' => '',
            ]
            , 'GET');
        $res = MenuLogic::getMenuParentsLists($this->verifyData);

        return ['lists'=>$res];
    }

    //获取单条
    public function getMenuOne(){
        $this->verify(
            [
                'menuId' => '',
            ]
            , 'GET');
        $res = MenuLogic::getMenuOne($this->verifyData['menuId']);

        return ['data'=>$res];
    }


    /*
     *  lang[
     *      MenuTitle  分类名称
     *      menuType   类型
     *      menuUrl    链接
     *      classType  类型值
     *      classId    选中的ID值
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *  ]
     */
    //修改
    public function editMenu(){
        $this->verify(
            [
                'menuId' => '',//分类id
                'pid' => '',//分类id
                'isOn'=>'',//是否显示
                /*'menuType'=>'',//类型*/
                'sort'=>'',//排序
                'newOpen'=>'',//排序
                /*'menuUrl'=>'',//链接*/
                'menuThumb' => 'no_required',//图片
                'lang'=>'',
            ]
            , 'POST');
        MenuLogic::editAll($this->verifyData);

        return;
    }
    //删除
    public function deleteMenu(){
        $this->verify(
            [
                'menuId' => '',//新闻ID
            ]
            , 'POST');
        MenuLogic::delAffair($this->verifyData['menuId']);

        return;
    }

    //获取父级ID
    public function getParentsId(){
        $this->verify(
            [
                'menuId' => '',
            ]
            , 'GET');
        $res = MenuLogic::getMenuId($this->verifyData['menuId']);
        return ['data'=>$res];
    }

    //获取下一级
    public function getChild(){
        $this->verify(
            [
                'menuId' => '',
            ]
            , 'GET');
        $res = MenuLogic::getChildId($this->verifyData['menuId']);
        return ['data'=>$res];
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'menuId' => '',
                'isOn' => ''
            ]
            , 'POST');
        MenuLogic::isShow($this->verifyData,$this->verifyData['menuId']);
        return;
    }

    //测试--顶级类型列表--使用中
    public function getMenuType(){
        $this->verify(
            [
                'languageId' => '',
            ]
            , 'GET');
        $languageId=$this->verifyData['languageId'];
        if($languageId==1){
            $proName='产品';
            $newsName='新闻';
            $casesName='案例';
            $aboutName='单页';
            $homeName='首页';
        }
        if($languageId==2){
            $proName='product';
            $newsName='news';
            $casesName='case';
            $aboutName='one';
            $homeName='home';
        }
        $arr=[];
        $res['classType']=1;
        $res['menuTypeName']=$proName;
        $res['menuClassName']='product';
        $res['kid']=[];
        $res2['classType']=2;
        $res2['menuTypeName']=$newsName;
        $res2['menuClassName']='news';
        $res2['kid']=[];
        $res3['classType']=3;
        $res3['menuTypeName']=$casesName;
        $res3['menuClassName']='case';
        $res3['kid']=[];
        $res4['classType']=4;
        $res4['menuTypeName']=$aboutName;
        $res4['menuClassName']='page';
        $res4['kid']=[];
        $res5['classType']=5;
        $res5['menuTypeName']=$homeName;
        $res5['menuClassName']='/';
        $res5['kid']=[];

        $arr[]=$res;
        $arr[]=$res2;
        $arr[]=$res3;
        $arr[]=$res4;
        $arr[]=$res5;
        return ['lists'=>$arr];
    }

    //获取第二个顶级类型-列出有多少套
    public function getTableListsTest()
    {
        $this->verify(
            [
                'languageId' => '',
                'classType'=>'',
            ]
            , 'GET');
        $siteId = SiteId::getSiteId();
        $languageId=$this->verifyData['languageId'];
        $classType=$this->verifyData['classType'];
        $arr=[];
        //产品
        if($classType==1){
            if($languageId==1){
                if($siteId==11){
                    $productName1='产品';
                    $productName2='设备厂房';
                }
                if($siteId==13){
                    $productName1='产品';
                }
                if($siteId==14){
                    $productName1='产品';
                    $productName2='案例工程';
                }
                if($siteId==17){
                    $productName1='产品中心';
                }
                if($siteId==18){
                    $productName1='万胜影业';
                }
                if($siteId==19){
                    $productName1='产品中心';
                }
                /*if($siteId==21){
                    $productName1='参赛作品';
                }*/
                if($siteId==22){
                    $productName1='课程介绍';
                }
                if($siteId==24){
                    $productName1='产品系统';
                }
                if($siteId==25){
                    $productName1='产品中心';
                }
                if($siteId==28){
                    $productName1='产品展示';
                    $productName2='视频展播';
                    $productName3='人才招聘';
                }
                if($siteId==31){
                    $productName1='设备介绍';
                }
                //华农
                if($siteId==32){
                    $productName1='即将开课';
                    $productName2='金牌课程';
                }
                //笨鸟文化
                if($siteId==34){
                    $productName1='合伙人';
                }
                //名演
                if($siteId==35){
                    $productName1='明星经纪';
                    $productName2='网红经纪';
                    $productName3='名人经纪';
                }
                //百度信誉
                if($siteId==36){
                    $productName1='产品';
                }
                //广用机械
                if($siteId==37){
                    $productName1='产品世界';
                }
                //叁好
                if($siteId==38){
                    $productName1='产品中心';
                }
            }
            if($languageId==2){
                if($siteId==17){
                    $productName1='ProductCenter';
                }
                if($siteId==24){
                    $productName1='ProductCenter';
                }
                if($siteId==28){
                    $productName1='Product';
                    $productName2='Video';
                    $productName3='Talent recruitment';
                }
            }
            //恩菲特
            if($siteId==11){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product1';
                $res1['tableId']=1;
                $res2['classType']=1;
                $res2['menuTypeName']=$productName2;
                $res2['menuClassName']='product2';
                $res2['tableId']=2;
                $arr[]=$res1;
                $arr[]=$res2;
            }
            //珠宝
            if($siteId==13){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product1';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //音响
            if($siteId==14){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product1';
                $res1['tableId']=1;
                $res2['classType']=1;
                $res2['menuTypeName']=$productName2;
                $res2['menuClassName']='product2';
                $res2['tableId']=2;
                $arr[]=$res1;
                $arr[]=$res2;
            }
            //学校
            if($siteId==16){
                $arr=[];
            }
            if($siteId==17){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product1';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //万胜
            if($siteId==18){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            if($siteId==19){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
           /* //笨鸟
            if($siteId==21){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product';
                $res1['tableId']=1;
                $arr[]=$res1;
            }*/
            if($siteId==22){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product1';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //易思客线上测试
            if($siteId==24){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            if($siteId==25){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //机械
            if($siteId==28){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product';
                $res1['tableId']=1;
                $res2['classType']=1;
                $res2['menuTypeName']=$productName2;
                $res2['menuClassName']='product';
                $res2['tableId']=2;
                $res3['classType']=1;
                $res3['menuTypeName']=$productName3;
                $res3['menuClassName']='product';
                $res3['tableId']=3;
                $arr[]=$res1;
                $arr[]=$res2;
                $arr[]=$res3;
            }
            if($siteId==31){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //华农
            if($siteId==32){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product';
                $res1['tableId']=1;
                $res2['classType']=1;
                $res2['menuTypeName']=$productName2;
                $res2['menuClassName']='product';
                $res2['tableId']=2;
                $arr[]=$res1;
                $arr[]=$res2;
            }
            //笨鸟文化
            if($siteId==34){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //名演
            if($siteId==35){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product';
                $res1['tableId']=1;
                $res2['classType']=1;
                $res2['menuTypeName']=$productName2;
                $res2['menuClassName']='product';
                $res2['tableId']=2;
                $res3['classType']=1;
                $res3['menuTypeName']=$productName3;
                $res3['menuClassName']='product';
                $res3['tableId']=3;
                $arr[]=$res1;
                $arr[]=$res2;
                $arr[]=$res3;
            }
            //百度信誉
            if($siteId==36){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //广用机械
            if($siteId==37){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //
            if($siteId==38){
                $res1['classType']=1;
                $res1['menuTypeName']=$productName1;
                $res1['menuClassName']='product';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
        }
        //新闻
        if($classType==2){
            if($languageId==1){
                //恩菲特
                if($siteId==11){
                    $newsName1='新闻';
                }
                //珠宝
                if($siteId==13){
                    $newsName1='新闻';
                }
                //音响
                if($siteId==14){
                    $newsName1='新闻';
                }
                //学校
                if($siteId==16){
                    $newsName1='学校信息';
                    $newsName2='教学科研';
                    $newsName3='德育之窗';
                    $newsName4='学科在线';
                    $newsName5='校园快讯';
                    $newsName6='校园文化';
                    $newsName7='学生园地';
                    $newsName8='社团活动';
                    $newsName9='本站推荐';
                    $newsName10='公告通知';
                }
                if($siteId==17){
                    $newsName1='新闻中心';
                    $newsName2='技术支持';
                }
                if($siteId==18){
                    $newsName1='万胜星闻';
                    $newsName2='演唱会';
                    $newsName3='明星视频';
                }
                if($siteId==19){
                    $newsName1='新闻中心';
                    $newsName2='招聘专区';
                    $newsName3='投资者专区';
                    $newsName4='企业成员';
                }
                if($siteId==21){
                    $newsName1='大赛评委';
                    $newsName2='合租赞助';
                    $newsName3='相关资讯';
                    $newsName4='大赛赛区';
                }
                if($siteId==22){
                    $newsName1='新闻中心';
                    $newsName2='团队介绍';
                }
                //易思客线上--测试
                if($siteId==24){
                    $newsName1='新闻系统';
                }
                if($siteId==25){
                    $newsName1='技术支持';
                    $newsName2='新闻动态';
                    $newsName3='合作伙伴';
                }
                if($siteId==28){
                    $newsName1='新闻动态';
                }
                //模板
                if($siteId==29){
                    $newsName1='行业资讯';
                    $newsName2='景观知识';
                }
                //云莲模板
                if($siteId==31){
                    $newsName1='新闻中心';
                    $newsName2='常见问题';
                    $newsName3='广东服务';
                    $newsName4='广州服务';
                }
                //华农
                if($siteId==32){
                    $newsName1='中心介绍';
                    $newsName2='学员反馈';
                    //$newsName3='师资团队';
                }
                //笨鸟文化
                if($siteId==34){
                    $newsName1='新闻';
                    $newsName2='团队';
                }
                //名演
                if($siteId==35){
                    $newsName1='明星合影';
                    $newsName2='经纪新闻';
                }
                //百度信誉
                if($siteId==36){
                    $newsName1='服务商';
                }
                //广用机械
                if($siteId==37){
                    $newsName1='新闻动态';
                }
                //
                if($siteId==38){
                    $newsName1='新闻中心';
                }
            }
            if($languageId==2){
                if($siteId==17){
                    $newsName1='NewsCenter';
                    $newsName2='Technical';
                }
                //易思客线上--测试
                if($siteId==24){
                    $newsName1='NewsCenter';
                }
                if($siteId==28){
                    $newsName1='News information';
                }
            }
            //恩菲特
            if($siteId==11){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news1';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //珠宝
            if($siteId==13){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news1';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //音响
            if($siteId==14){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news1';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //学校
            if($siteId==16){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news1';
                $res1['tableId']=1;
                $res2['classType']=2;
                $res2['menuTypeName']=$newsName2;
                $res2['menuClassName']='news2';
                $res2['tableId']=2;
                $res3['classType']=2;
                $res3['menuTypeName']=$newsName3;
                $res3['menuClassName']='news3';
                $res3['tableId']=3;
                $res4['classType']=2;
                $res4['menuTypeName']=$newsName4;
                $res4['menuClassName']='news4';
                $res4['tableId']=4;
                $res5['classType']=2;
                $res5['menuTypeName']=$newsName5;
                $res5['menuClassName']='news5';
                $res5['tableId']=5;
                $res6['classType']=2;
                $res6['menuTypeName']=$newsName6;
                $res6['menuClassName']='news6';
                $res6['tableId']=6;
                $res7['classType']=2;
                $res7['menuTypeName']=$newsName7;
                $res7['menuClassName']='news7';
                $res7['tableId']=7;
                $res8['classType']=2;
                $res8['menuTypeName']=$newsName8;
                $res8['menuClassName']='news8';
                $res8['tableId']=8;
                $res9['classType']=2;
                $res9['menuTypeName']=$newsName9;
                $res9['menuClassName']='news9';
                $res9['tableId']=9;
                $res10['classType']=2;
                $res10['menuTypeName']=$newsName10;
                $res10['menuClassName']='news10';
                $res10['tableId']=10;
                $arr[]=$res1;
                $arr[]=$res2;
                $arr[]=$res3;
                $arr[]=$res4;
                $arr[]=$res5;
                $arr[]=$res6;
                $arr[]=$res7;
                $arr[]=$res8;
                $arr[]=$res9;
                $arr[]=$res10;
            }
            if($siteId==17){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news1';
                $res1['tableId']=1;
                $res2['classType']=2;
                $res2['menuTypeName']=$newsName2;
                $res2['menuClassName']='news2';
                $res2['tableId']=2;
                $arr[]=$res1;
                $arr[]=$res2;
            }
            if($siteId==18){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news';
                $res1['tableId']=1;
                $res2['classType']=2;
                $res2['menuTypeName']=$newsName2;
                $res2['menuClassName']='news';
                $res2['tableId']=2;
                $res3['classType']=2;
                $res3['menuTypeName']=$newsName3;
                $res3['menuClassName']='news';
                $res3['tableId']=3;
                $arr[]=$res1;
                $arr[]=$res2;
                $arr[]=$res3;
            }
            //
            if($siteId==19){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news';
                $res1['tableId']=1;
                $arr[]=$res1;
                $res2['classType']=2;
                $res2['menuTypeName']=$newsName2;
                $res2['menuClassName']='news';
                $res2['tableId']=2;
                $arr[]=$res2;
                $res3['classType']=2;
                $res3['menuTypeName']=$newsName3;
                $res3['menuClassName']='news';
                $res3['tableId']=3;
                $arr[]=$res3;
                $res4['classType']=2;
                $res4['menuTypeName']=$newsName4;
                $res4['menuClassName']='news';
                $res4['tableId']=4;
                $arr[]=$res4;
            }
            if($siteId==21){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news';
                $res1['tableId']=1;
                $res2['classType']=2;
                $res2['menuTypeName']=$newsName2;
                $res2['menuClassName']='news';
                $res2['tableId']=2;
                $res3['classType']=2;
                $res3['menuTypeName']=$newsName3;
                $res3['menuClassName']='news';
                $res3['tableId']=3;
                $res4['classType']=2;
                $res4['menuTypeName']=$newsName4;
                $res4['menuClassName']='news';
                $res4['tableId']=4;
                $arr[]=$res1;
                $arr[]=$res2;
                $arr[]=$res3;
                $arr[]=$res4;
            }
            if($siteId==22){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news1';
                $res1['tableId']=1;
                $res2['classType']=2;
                $res2['menuTypeName']=$newsName2;
                $res2['menuClassName']='news2';
                $res2['tableId']=2;
                $arr[]=$res1;
                $arr[]=$res2;
            }
            //易思客线上--测试
            if($siteId==24){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            if($siteId==25){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news';
                $res1['tableId']=1;
                $res2['classType']=2;
                $res2['menuTypeName']=$newsName2;
                $res2['menuClassName']='news';
                $res2['tableId']=2;
                $res3['classType']=2;
                $res3['menuTypeName']=$newsName3;
                $res3['menuClassName']='news';
                $res3['tableId']=3;
                $arr[]=$res1;
                $arr[]=$res2;
                $arr[]=$res3;
            }
            if($siteId==28){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            if($siteId==29){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news';
                $res1['tableId']=1;
                $res2['classType']=2;
                $res2['menuTypeName']=$newsName2;
                $res2['menuClassName']='news';
                $res2['tableId']=2;
                $arr[]=$res1;
                $arr[]=$res2;
            }
            //云莲
            if($siteId==31){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news';
                $res1['tableId']=1;
                $res2['classType']=2;
                $res2['menuTypeName']=$newsName2;
                $res2['menuClassName']='news';
                $res2['tableId']=2;
                $res3['classType']=2;
                $res3['menuTypeName']=$newsName3;
                $res3['menuClassName']='news';
                $res3['tableId']=3;
                $res4['classType']=2;
                $res4['menuTypeName']=$newsName4;
                $res4['menuClassName']='news';
                $res4['tableId']=4;
                $arr[]=$res1;
                $arr[]=$res2;
                $arr[]=$res3;
                $arr[]=$res4;
            }
            //华农
            if($siteId==32){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news';
                $res1['tableId']=1;
                $res2['classType']=2;
                $res2['menuTypeName']=$newsName2;
                $res2['menuClassName']='news';
                $res2['tableId']=2;
                /*$res3['classType']=2;
                $res3['menuTypeName']=$newsName3;
                $res3['menuClassName']='news';
                $res3['tableId']=3;*/
                $arr[]=$res1;
                $arr[]=$res2;
                //$arr[]=$res3;
            }
            //笨鸟文化
            if($siteId==34){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news';
                $res1['tableId']=1;
                $res2['classType']=2;
                $res2['menuTypeName']=$newsName2;
                $res2['menuClassName']='news';
                $res2['tableId']=2;
                $arr[]=$res1;
                $arr[]=$res2;
            }
            //名演
            if($siteId==35){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news';
                $res1['tableId']=1;
                $res2['classType']=2;
                $res2['menuTypeName']=$newsName2;
                $res2['menuClassName']='news';
                $res2['tableId']=2;
                $arr[]=$res1;
                $arr[]=$res2;
            }
            //百度信誉
            if($siteId==36){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //广用机械人
            if($siteId==37){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //
            if($siteId==38){
                $res1['classType']=2;
                $res1['menuTypeName']=$newsName1;
                $res1['menuClassName']='news';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
        }
        //案例
        if($classType==3){
            if($languageId==1){
                if($siteId==11){
                    $casesName1='案例';
                }
                if($siteId==17){
                    $casesName1='案例展示';
                }
                if($siteId==18){
                    $casesName1='明星经纪';
                }
                if($siteId==21){
                    $casesName1='组织单位';
                }
                if($siteId==24){
                    $casesName1='案例系统';
                }
                if($siteId==28){
                    $casesName1='案例展示';
                }
                //模板
                if($siteId==29){
                    $casesName1='案例工程';
                    $casesName2='精品设计';
                }
                //云莲管道
                if($siteId==31){
                    $casesName1='服务项目';
                    $casesName2='施工现场';
                    $casesName3='施工视频';
                }
                //名演
                if($siteId==35){
                    $casesName1='成功案例';
                }
                //百度信誉
                if($siteId==36){
                    $casesName1='商家承若';
                }
                //广用机械人
                if($siteId==37){
                    $casesName1='解决方案';
                }
                //广用机械人
                if($siteId==38){
                    $casesName1='工厂展示';
                    $casesName2='贴牌订制';
                }

            }
            if($languageId==2){
                if($siteId==17){
                    $casesName1='CasesShow';
                }
                if($siteId==24){
                    $casesName1='CasesShow';
                }
                if($siteId==28){
                    $casesName1='CasesShow';
                }
            }
            //恩菲特
            if($siteId==11){
                $res1['classType']=3;
                $res1['menuTypeName']=$casesName1;
                $res1['menuClassName']='cases1';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //珠宝
            if($siteId==13){
                $arr=[];
            }
            //音响
            if($siteId==14){
                $arr=[];
            }
            //学校
            if($siteId==16){
                $arr=[];
            }
            //
            if($siteId==17){
                $res1['classType']=3;
                $res1['menuTypeName']=$casesName1;
                $res1['menuClassName']='cases1';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            if($siteId==18){
                $res1['classType']=3;
                $res1['menuTypeName']=$casesName1;
                $res1['menuClassName']='cases';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            if($siteId==21){
                $res1['classType']=3;
                $res1['menuTypeName']=$casesName1;
                $res1['menuClassName']='cases';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            if($siteId==24){
                $res1['classType']=3;
                $res1['menuTypeName']=$casesName1;
                $res1['menuClassName']='cases';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            if($siteId==28){
                $res1['classType']=3;
                $res1['menuTypeName']=$casesName1;
                $res1['menuClassName']='cases';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            if($siteId==29){
                $res1['classType']=3;
                $res1['menuTypeName']=$casesName1;
                $res1['menuClassName']='cases';
                $res1['tableId']=1;
                $res2['classType']=3;
                $res2['menuTypeName']=$casesName2;
                $res2['menuClassName']='cases';
                $res2['tableId']=2;
                $arr[]=$res1;
                $arr[]=$res2;
            }
            if($siteId==31){
                $res1['classType']=3;
                $res1['menuTypeName']=$casesName1;
                $res1['menuClassName']='cases';
                $res1['tableId']=1;
                $res2['classType']=3;
                $res2['menuTypeName']=$casesName2;
                $res2['menuClassName']='cases';
                $res2['tableId']=2;
                $res3['classType']=3;
                $res3['menuTypeName']=$casesName3;
                $res3['menuClassName']='cases';
                $res3['tableId']=3;
                $arr[]=$res1;
                $arr[]=$res2;
                $arr[]=$res3;
            }
            //名演
            if($siteId==35){
                $res1['classType']=3;
                $res1['menuTypeName']=$casesName1;
                $res1['menuClassName']='cases';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //百度信誉
            if($siteId==36){
                $res1['classType']=3;
                $res1['menuTypeName']=$casesName1;
                $res1['menuClassName']='cases';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //广用机械人
            if($siteId==37){
                $res1['classType']=3;
                $res1['menuTypeName']=$casesName1;
                $res1['menuClassName']='cases';
                $res1['tableId']=1;
                $arr[]=$res1;
            }
            //
            if($siteId==38){
                $res1['classType']=3;
                $res1['menuTypeName']=$casesName1;
                $res1['menuClassName']='cases';
                $res1['tableId']=1;
                $res2['classType']=3;
                $res2['menuTypeName']=$casesName2;
                $res2['menuClassName']='cases';
                $res2['tableId']=2;
                $arr[]=$res1;
                $arr[]=$res2;
            }
        }
        if($classType==4){
            /*if($languageId==1){
                $aboutName='关于我们';
            }
            $res1['classType']=1;
            $res1['menuTypeName']=$aboutName;
            $res1['menuClassName']='about';
            $res1['tableId']=1;
            $arr[]=$res1;*/
            $arr = AboutLogic::getAboutMenuList($this->verifyData['languageId']);
        }
        return ['lists'=>$arr];
    }

    public function getTableLists()
    {
        $this->verify(
            [
                'languageId' => '',
                'classType'=>'',
            ]
            , 'GET');
        $siteId = SiteId::getSiteId();
        $languageId=$this->verifyData['languageId'];
        $classType=$this->verifyData['classType'];
        $arr=[];
        //产品
        if($classType==1){
            //获取产品
            $pro = SiteAllocationLogic::getAllocationProduct($siteId,$languageId);
            if(!empty($pro)){
                foreach ($pro as $key=>$value){
                    $pro[$key]['classType']=1;
                    $pro[$key]['menuClassName']='product';
                }
            }
            $arr=$pro;
        }
        //新闻
        if($classType==2){
            //获取新闻
            $news = SiteAllocationLogic::getAllocationNews($siteId,$languageId);
            if(!empty($news)){
                foreach ($news as $key=>$value){
                    $news[$key]['classType']=2;
                    $news[$key]['menuClassName']='news';
                }
            }
            $arr=$news;
        }
        //案例
        if($classType==3){
            //获取案例
            $cases = SiteAllocationLogic::getAllocationCases($siteId,$languageId);
            if(!empty($cases)){
                foreach ($cases as $key=>$value){
                    $cases[$key]['classType']=3;
                    $cases[$key]['menuClassName']='cases';
                }
            }
            $arr=$cases;
        }
        if($classType==4){
            $arr = AboutLogic::getAboutMenuList($this->verifyData);
        }
        return ['lists'=>$arr];
    }

    //地址页面
    public function getMenuUrlLists(){
        $this->verify(
            [
                'languageId' => 'no_required',
                'classType'=>'',
                'tableId'=>'',
                'isOn' => 'no_required'
            ]
            , 'GET');
        $siteId = SiteId::getSiteId();
        $arr=[];
        $classType=$this->verifyData['classType'];
        $tableId=$this->verifyData['tableId'];
        if($classType==1){
            //产品
            $productType=$tableId;
            $arr = ProductCateLogic::getCateLists($this->verifyData,$productType);
        }
        if($classType==2){
            //新闻
            $arr = NewsCateLogic::getCateLists($this->verifyData,$tableId);
        }
        if($classType==3){
            //案例
            /*$casesType=1;*/
            $arr = CasesCateLogic::getCateLists($this->verifyData);
        }
        /*if($classType==4){
            //关于我们
            $arr = AboutLogic::getAboutMenuList($this->verifyData);
        }*/
        return ['lists'=>$arr];
    }

    //获取上一级
    public function getMenuUrlLast(){
        $this->verify(
            [
                'languageId' => 'no_required',
                'classType'=>'',
                'classId'=>'',
            ]
            , 'GET');
        $siteId = SiteId::getSiteId();
        $classId=$this->verifyData['classId'];
        $arr=[];
        $classType=$this->verifyData['classType'];
        if($classType==1){
            //产品
            $arr['classId']='';
            $res = ProductCateLogic::getProductCateId($classId,$productType=null);
            if($res['pid']!=0){
                $arr['classId']=empty($res['pid']) ? '' : $res['pid'];
            }
            if(empty($arr['classId'])){
                $arr['tableId']=empty($res['tableId']) ? '' : $res['tableId'];
            }
            //$arr['tableId']=empty($res['tableId']) ? '' : $res['tableId'];
        }

        if($classType==2){
            //新闻
            $arr['classId']='';
            $arr['tableId']='';
            $res = NewsCateLogic::getNewsCateId($classId,$newsType=null);
            if($res['pid']!=0){
                $arr['classId']=empty($res['pid']) ? '' : $res['pid'];
            }
            if(empty($arr['classId'])){
                $arr['tableId']=empty($res['tableId']) ? '' : $res['tableId'];
            }
        }
        if($classType==3){
            //案例
            $arr['classId']='';
            $res = CasesCateLogic::getCasesCateId($this->verifyData['classId']);
            if($res['pid']!=0){
                $arr['classId']=empty($res['pid']) ? '' : $res['pid'];
            }
            if(empty($arr['classId'])){
                $arr['tableId']=empty($res['tableId']) ? '' : $res['tableId'];
            }
        }
        if($classType==4){
            //关于我们
            $arr['classId']='';
            $res = AboutLogic::getAboutMenuList($this->verifyData['classId']);
        }

        return ['data'=>$arr];
    }



}
