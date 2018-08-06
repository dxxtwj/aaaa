<?php

namespace App\Http\Controllers\Admin;

use App\Logic\BasicLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class BasicController extends Controller
{
    /*
     *  lang[
     *      languageId      语言ID
     *      basicDesc       基本信息的描述或者内容
     *      basicCompany    公司
     *      companyAddress  公司地址
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *  ]
     */
    //添加
    public function AddBasic()
    {
        $this->verify(
            [
                'basicCopyright' => 'no_required',//版权
                'basicRecord'=>'no_required',//备案号
                'basicEmail'=>'no_required',//邮件
                'companyTel' => 'no_required',//固话
                'basicContact' => 'no_required',//联系
                'basicWebsite' => 'no_required',//网址
                'companyPhone' => 'no_required',//手机
                'scanCode'=>'no_required',//二维码
                'point'=>'no_required',//百度经纬度
                'logo'=>'no_required',
                'lang'=> '',
                'weChat' => 'no_required',
            ]
            , 'POST');
        BasicLogic::addAll($this->verifyData);

    }

    //获取单条
    public function getBasic(){
        $res = BasicLogic::getBasic();
        return ['data'=>$res];
    }

    /*
     *  lang[
     *      languageId      语言ID
     *      basicDesc       基本信息的描述或者内容
     *      basicCompany    公司
     *      companyAddress  公司地址
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *  ]
     */
    //修改
    public function editBasic(){
        $this->verify(
            [
                'basicCopyright' => 'no_required',//版权
                'basicRecord'=>'no_required',//备案号
                'basicEmail'=>'no_required',//邮件
                'companyTel' => 'no_required',//固话
                'basicContact' => 'no_required',//联系
                'basicWebsite' => 'no_required',//网址
                'companyPhone' => 'no_required',//手机
                'scanCode'=>'no_required',//二维码
                'point'=>'no_required',//百度经纬度
                'logo'=>'no_required',
                'lang'=> '',
                'weChat' => 'no_required',


            ]
            , 'POST');
        BasicLogic::editAll($this->verifyData);

        return;
    }

    

}
