<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10;

use App\Model\V10\BasicModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class BasicLogic
{

    //修改主表
    public static function edit($data)
    {
        \DB::beginTransaction();
        try{
            /*if(!empty($data['address'])){
                $point = self::getPoint($data['address']);
                $data['point'] = $point;
            }*/
            $model = new BasicModel();
            $model->where('basic_id', 1)->updateByHump($data);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    public static function getBasic()
    {
        $model = new BasicModel();
        $basic = $model->where('basic_id',1)->firstHumpArray(['*']);
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
        $res1 = $res['result'];
        $res2 = $res1['location'];
        $point = json_encode($res2);
        return $point;
    }

}