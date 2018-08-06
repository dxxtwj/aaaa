<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V2\User;
use App\Model\Subscriber\OauthModel;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvException;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class OauthLogic
{

    public static function getOneByUoaid($uoaid){
        $res = (new OauthModel())->where('uoaid', $uoaid)->firstHump();
        if (empty($res)){
            throw new RJsonError('没有找到该数据', 'NOT_FIND');
        }
        return $res;
    }

    /**
     * @param $openType int 数据
     * @param $openId string 开放id
     * @return \Illuminate\Database\Eloquent\Model|null|static
     * @throws RJsonError
     */
    public static function getOneByTypeOpenid($openType, $openId,$siteId){
        $res = (new OauthModel)->where('site_id',$siteId)->where('openid', $openId)->where('open_type', $openType)->firstHump();
        if (empty($res)){
            throw new RJsonError('没有找到该数据', 'NOT_FIND');
        }
        return $res;
    }
    public static function getOneByTypeUnionid($unionType, $unionId,$siteId){
        $res = (new OauthModel)->where('site_id',$siteId)->where('unionid', $unionId)->where('union_type', $unionType)->firstHump();
        if (empty($res)){
            throw new RJsonError('没有找到该数据', 'NOT_FIND');
        }
        return $res;
    }

    /** 通过openid 修改数据
     * @param $openType int 类型
     * @param $openId string 开放id
     * @param $data array 数据
     * @return mixed
     */
    public static function putOneByTypeOpenid($openType, $openId, $data,$siteId){
        try{
            $res = self::getOneByTypeOpenid($openType, $openId,$siteId);
            $uoaid = $res->uoaid;
            (new OauthModel)->where('site_id',$siteId)->where('uoaid', $uoaid)->updateByHump($data);
        }catch (DdvException $e){
            $model = new OauthModel();
            $model->setDataByHumpArray(array_merge([
                'unionid'=>'',
                'unionType'=>'',
                'site_id'=>$siteId,
            ], $data, [
                'openid'=>$openId,
                'openType'=>$openType,
                'jsondataFirst'=>$data['jsondata'],
                'registerIp'=>$data['lastLoginIp']
            ]))->save();
            $uoaid = $model->getQueueableId();
        }
        return $uoaid;
    }
    public static function setSessionUoaid($uoaid){
        \Session::put('oauth.uoaid', $uoaid);
    }
    public static function getSessionUoaid(){
        $uoaid = session('oauth.uoaid', null);
        return $uoaid;
    }
}
















