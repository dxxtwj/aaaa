<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic;

use App\Logic\Gallery\GalleryLogic;
use App\Model\Basic\BasicModel;
use App\Model\Basic\BasicDescModel;
use App\Logic\Site\SiteLogic;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class BasicLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $main=[];
        self::addAffair($main,$data);
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['logo'])){
                $GalleryId = GalleryLogic::getGalleryId($data['logo']);
            }
            if(!empty($data['scanCode'])){
                $GalleryId = GalleryLogic::getGalleryId($data['scanCode']);
            }
            $basicId = self::addBasic($main);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'basicId' => $basicId,
                    'basicCopyright' => empty($data['basicCopyright']) ? '' : $data['basicCopyright'],
                    'basicRecord'=> empty($data['basicRecord']) ? '' : $data['basicRecord'],//备案号
                    'basicCompany' => empty($value['basicCompany']) ? '' : $value['basicCompany'],
                    'logo' => empty($data['logo']) ? '' : $data['logo'],
                    'basicEmail' => empty($data['basicEmail']) ? '' : $data['basicEmail'],
                    'contacts' => empty($value['contacts']) ? '' : $value['contacts'],
                    'basicDesc' => empty($value['basicDesc']) ? '' : $value['basicDesc'],
                    'basicProject'=>empty($value['basicProject']) ? '' : $value['basicProject'],//项目名称
                    'companyTel' => empty($data['companyTel']) ? '' : $data['companyTel'],
                    'basicContact' => empty($data['basicContact']) ? '' : $data['basicContact'],//联系
                    'basicWebsite' => empty($data['basicWebsite']) ? '' : $data['basicWebsite'],//网址
                    'companyPhone' => empty($data['companyPhone']) ? '' : $data['companyPhone'],
                    'scanCode' => empty($data['scanCode']) ? '' : $data['scanCode'],
                    'point' => empty($data['point']) ? '' : $data['point'],
                    'companyAddress' => empty($value['companyAddress']) ? '' : $value['companyAddress'],
                    'languageId'=>$value['languageId'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                    'weChat' => empty($data['weChat']) ? '' : $data['weChat'],
                ];
                self::addBasicDesc($desc);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //添加主表
    public static function addBasic ($data=[])
    {
        $basic = self::getBasicId();
        if(isset($basic)){
            throw new RJsonError('已经添加过了', 'ADD_BASIC_BASIC');
        }
        $model = new BasicModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //添加详细表
    public static function addBasicDesc ($data=[])
    {
        $model = new BasicDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //获取主单条
    public static function getBasic()
    {
        $basic = BasicModel::whereSiteId()->firstHump(['*']);
        if(isset($basic)){
            $basicDesc = self::getBasicDesc($basic['basicId']);
            if(isset($basicDesc)){
                foreach ($basicDesc as $key=>$value){
                    $basic['basicCopyright']=empty($value['basicCopyright']) ? '' : $value['basicCopyright'];
                    $basic['logo']=empty($value['logo']) ? '' : $value['logo'];
                    $basic['basicEmail']=empty($value['basicEmail']) ? '' : $value['basicEmail'];
                    $basic['basicRecord']=empty($value['basicRecord']) ? '' : $value['basicRecord'];
                    $basic['basicContact']=empty($value['basicContact']) ? '' : $value['basicContact'];
                    $basic['basicWebsite']=empty($value['basicWebsite']) ? '' : $value['basicWebsite'];
                    $basic['companyTel']=empty($value['companyTel']) ? '' : $value['companyTel'];
                    $basic['companyPhone']=empty($value['companyPhone']) ? '' : $value['companyPhone'];
                    $basic['scanCode']=empty($value['scanCode']) ? '' : $value['scanCode'];
                    $basic['point']=empty($value['point']) ? '' : $value['point'];
                    $basic['basicProject']=empty($value['basicProject']) ? '' : $value['basicProject'];
                    $basic['weChat']=empty($value['weChat']) ? '' : $value['weChat'];
                }
            }
            $basic['lang']=empty($basicDesc) ? [] : $basicDesc;
        }
        return $basic;
    }

    //获取单条
    public static function getBasicDesc($basicId)
    {
        $basicDesc = BasicDescModel::where('basic_id',$basicId)->getHump(['*']);
        return $basicDesc;
    }

    //编辑全部
    public static function editAll ($data=[])
    {
        $GalleryId=0;
        if(!empty($data['logo'])){
            $GalleryId = GalleryLogic::getGalleryId($data['logo']);
        }
        if(!empty($data['scanCode'])){
            $GalleryId = GalleryLogic::getGalleryId($data['scanCode']);
        }
        $basicId = self::getBasicId();
        foreach ($data['lang'] as $key=>$value){
            $desc=[
                'basicCopyright' => empty($data['basicCopyright']) ? '' : $data['basicCopyright'],
                'basicRecord'=> empty($data['basicRecord']) ? '' : $data['basicRecord'],//备案号
                'basicCompany' => empty($value['basicCompany']) ? '' : $value['basicCompany'],
                'logo' => empty($data['logo']) ? '' : $data['logo'],
                'contacts' => empty($value['contacts']) ? '' : $value['contacts'],
                'basicDesc' => empty($value['basicDesc']) ? '' : $value['basicDesc'],
                'basicProject'=>empty($value['basicProject']) ? '' : $value['basicProject'],//项目名称
                'basicEmail' => empty($data['basicEmail']) ? '' : $data['basicEmail'],
                'basicContact' => empty($data['basicContact']) ? '' : $data['basicContact'],
                'basicWebsite' => empty($data['basicWebsite']) ? '' : $data['basicWebsite'],
                'companyTel' => empty($data['companyTel']) ? '' : $data['companyTel'],
                'companyPhone' => empty($data['companyPhone']) ? '' : $data['companyPhone'],
                'scanCode' => empty($data['scanCode']) ? '' : $data['scanCode'],
                //'point' => empty($point) ? '' : $data['point'],
                'companyAddress' => empty($value['companyAddress']) ? '' : $value['companyAddress'],
                'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                'weChat' => empty($data['weChat']) ? '' : $data['weChat'],

            ];
            if(!empty($value['companyAddress']) && $value['languageId']==1){
                $point = self::getPoint($value['companyAddress']);
                $desc['point']=$point;
            }
            self::editBasicDesc($desc,$basicId,$value['languageId']);
        }
    }

    //默认数据
    public static function addBasicDefault($siteId,$lang)
    {
        $arr=[];
        //获取站点的语言列表
        $lang=SiteLogic::getLangLists($siteId);
        if(isset($lang)){
            foreach ($lang as $key=>$value) {
                $arr[]=$value;
            }
        }
        $main=[
            'siteId'=>$siteId,
        ];
        foreach ($arr as $key=>$value){
            if($value['languageId']==1){
                $arr[$key]['basicId']=1;
                $arr[$key]['basicCopyright']='版权';
                $arr[$key]['basicDesc']='描述';
                $arr[$key]['basicCompany']='公司';
                $arr[$key]['companyAddress']='地址';
            }
            if($value['languageId']==2){
                $arr[$key]['basicId']=1;
                $arr[$key]['basicCopyright']='copyright';
                $arr[$key]['basicDesc']='description';
                $arr[$key]['basicCompany']='company';
                $arr[$key]['companyAddress']='address';
            }
        }
        \DB::beginTransaction();
        try{
            $basicId = self::addBasic($main);
            foreach ($arr as $key=>$value){
                $desc=[
                    'basicId' => $basicId,
                    'basicCopyright' => empty($value['basicCopyright']) ? '' : $value['basicCopyright'],
                    'basicCompany' => empty($value['basicCompany']) ? '' : $value['basicCompany'],
                    'basicDesc' => empty($value['basicDesc']) ? '' : $value['basicDesc'],
                    'companyAddress' => empty($value['companyAddress']) ? '' : $value['companyAddress'],
                    'languageId'=>$value['languageId'],
                ];
                self::addBasicDesc($desc);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;


    }

    //获取主表id
    public static function getBasicId()
    {
        $basic = BasicModel::whereSiteId()->firstHump(['*']);
        return $basic['basicId'];
    }

    //编辑详细表
    public static function editBasicDesc($data=[],$basicId,$languageId)
    {
        BasicDescModel::where('basic_id',$basicId)->where('language_id',$languageId)->updateByHump($data);
    }

    //=========================前端调用=================================

    //查
    public static function getBasicAll($languageId)
    {
        $basic = BasicModel::whereSiteId()
            ->where('basic_description.language_id',$languageId)
            ->leftjoin('basic_description', 'basic.basic_id', '=', 'basic_description.basic_id')
            ->firstHump([
                'basic.*',
                'basic_description.*',
            ]);
        if(!empty($basic) && $languageId!=1){
            $res = BasicModel::whereSiteId()->where('basic_description.language_id',1)
                ->leftjoin('basic_description', 'basic.basic_id', '=', 'basic_description.basic_id')
                ->firstHump(['basic_description.point']);
            $basic['point'] = $res ?? '';
        }
        if(!empty($basic['point'])){
            $basic['point']=json_decode($basic['point']);
        }

        return $basic;
    }

    //百度地图经纬度
    public static function getPoint($address)
    {
        $ak = 'Uj51yuoAVwc1MVZh4GQy9NsyZ8CLF9u0';
        //$address = '重庆市南岸区南滨路';
        //http://lbsyun.baidu.com/index.php?title=webapi/guide/webservice-geocoding
        $url = "http://api.map.baidu.com/geocoder/v2/?address=$address&output=json&ak=$ak";
        $json = file_get_contents($url);
        $res = json_decode($json,true);
        if($res['status']==1){
            throw new RJsonError('地址错误', 'ADDRESS_ERROR');
        }
        $res1 = $res['result'];
        $res2 = $res1['location'];
        $point = json_encode($res2);
        return $point;
    }


}