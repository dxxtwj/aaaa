<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\User;
use \App\Model\UserInfoModel;
use \App\Model\UserBaseModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class InfoLogic
{
    /**
     * 登录逻辑
     */
    public static function getBaseInfo($uid)
    {
        $userInfo = UserInfoModel::where('uid', $uid)->firstHumpArray([
            'uid','headimg','sex','isCompany'
        ]);
        if (!$userInfo){
            if (self::isHasBaseInfoByUid($uid)){
                $userInfo = [];
            }else{
                throw new RJsonError('账号不存在', 'NOT_FIND_ACCOUNT');
            }
        }
        $res = AccountLogic::getOkListsByUid($uid, ['account', 'type'])->toArray();
        $account = [];
        foreach ($res as $item){
            $account[(string)$item['type']] = $item['account'];
        }
        $userInfo['account'] = $account['1']??$account['2']??$account['3']??'';
        $userInfo = array_merge([
            'uid'=>$uid,
            'headimg'=>'',
            'sex'=>0
        ], $userInfo);
        $userInfo['isCompany'] = isset($userInfo['isCompany'])&&(boolean)$userInfo['isCompany'];
        return $userInfo;

    }
    public static function isHasBaseInfoByUid($uid)
    {
        $userInfo = UserBaseModel::where('uid', $uid)->first([
            'uid'
        ]);
        return (boolean)$userInfo;
    }
    public static function isLogin(){
        return !empty(session('uid', null));
    }

}
