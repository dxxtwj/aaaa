<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V2\User;
use App\Http\Middleware\ClientIp;
use App\Logic\V2\Common\VerifyLogic;
use App\Model\Subscriber\OauthModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class WechatLogic
{

    //配置
    public static function WeChatConfig()
    {
        $config=[
            'appid'=>'wx8956bb1b3181f683',
            'secret'=>'02f407f9d122ab51aabff431ccc17558'
        ];
        return $config;
    }
    //获取微信信息
    public static function getWechatByOpenid($openid)
    {
        $res = OauthModel::whereSiteId()->where('openid',$openid)->firstHumpArray();
        return $res;
    }
    //获取微信信息
    public static function getWechatByUid($uid)
    {
        $res = OauthModel::whereSiteId()->where('uid',$uid)->where('open_type',2)->firstHumpArray();
        return $res;
    }
    //获取微信信息
    public static function getWechatByUoaid($uoaid)
    {
        $res = OauthModel::where('uoaid',$uoaid)->firstHumpArray();
        return $res;
    }

    //添加微信
    public static function addWechat($data,$json)
    {
        $ip = ClientIp::get();
        $wechat=[
            //'uid'=>empty($data['uid']) ? 0 : $data['uid'],
            'openid'=>$data['openid'],
            'openType'=>2,
            'registerIp'=>$ip,
            'registerTime'=>time(),
            'jsondata'=>$json,
            'jsondataFirst'=>$json,
        ];
        $model = new OauthModel();
        $model->setSiteId()->setDataByHumpArray($wechat)->save();
        return $model->getQueueableId();
    }

    //绑定微信
    public static function bindWechat($data)
    {
        $ip = ClientIp::get();
        $user = UserLogic::checkAccountByWechat($data);
        if(empty($user)){
            $res['noRegister']=false;
            return $res;
        }
        if(md5($data['password'])!=$user['password']){
            throw new RJsonError('密码错误', 'NOT_PASSWORD_ACCOUNT');
        }
        //检测这个用户有没有绑定过微信
        $userWechat = self::getWechatByUid($user['uid']);
        if(!empty($userWechat)){
            throw new RJsonError('该账号已近被绑定过,请换一个', 'USER_BIND');
        }
        $uoaid = \Session::get('uoaid');
        //$uoaid=1;
        //修改微信
        $res=[
            'uid'=>$user['uid'],
            'lastLoginIp'=>$ip,
            'lastLoginAt'=>time(),
        ];
        self::editWechat($res,$uoaid);
        $res = self::getWechatByUoaid($uoaid);
        if($res['uid']==0){
            throw new RJsonError('绑定失败', 'WECHAT_BIND');
        }
        //从session拿信息
        $wechatInfo = \Session::get('wechatInfo');
        //修改用户信息--头像和昵称是否为空
        $account['uid']=$user['uid'];
        if(empty($user['userNickname'])){
            $account['userNickname']=empty($wechatInfo['nickname']) ? '' : $wechatInfo['nickname'];
        }
        if(empty($user['userAvatar'])){
            $account['userAvatar']=empty($wechatInfo['avatar']) ? '' : $wechatInfo['avatar'];
        }
        if($user['sex']){
            $account['sex']=empty($wechatInfo['sex']) ? $user['sex'] : $wechatInfo['sex'];
        }
        UserLogic::editUser($account,$user['uid']);
        //直接登录
        \Session::put(['uid'=>$res['uid']]);
        \Session::remove('uoaid');
        $user['noRegister']=true;
        return $user;
    }

    //修改信息
    public static function editWechat($data,$uoaid)
    {
        OauthModel::where('uoaid', $uoaid)->updateByHump($data);
    }

    //微信、微博、QQ、注册
    public static function wechatRegister($data)
    {
        //检测账号是否存在
        UserLogic::existAccount($data);
        if($data['type']==2){
            VerifyLogic::codeSms($data['account'],$data['code']);
        }
        if($data['type']==3){
            VerifyLogic::EmailCode($data['account'],$data['code']);
        }
        //加密密码
        $password=md5($data['password']);
        //添加用户
        $main=[
            'account'=>$data['account'],
            'password'=>$password,
            'registerTime'=>time(),
        ];
        self::WechatAffair($main,$data);
    }
    public static function WechatAffair($main,$data)
    {
        $ip = ClientIp::get();
        $uoaid = \Session::get('uoaid');
        \DB::beginTransaction();
        try{
            $uid = UserLogic::addAccount($main);
            $user=[
                'uid'=>$uid,
                'userNickname'=>$data['userNickname'],
                'userAvatar'=>$data['userAvatar'],
                'sex'=>empty($data['sex']) ? 0 : $data['sex'],
            ];
            UserLogic::addAccountDesc($user);
            $res=[
                'uid'=>$uid,
                'lastLoginIp'=>$ip,
                'lastLoginAt'=>time(),
            ];
            self::editWechat($res,$uoaid);
            $auth = self::getWechatByUoaid($uoaid);
            if($auth['uid']==0){
                throw new RJsonError('绑定失败', 'WECHAT_BIND');
            }
            //\Session::remove('codeImg');
            \Session::put(['uid'=>$uid]);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        }
        return;
    }

    //绑定、微博
    public static function bind($data)
    {
        $ip = ClientIp::get();
        $user = UserLogic::checkAccountByWechat($data);
        if(empty($user)){
            $res['noRegister']=false;
            return $res;
        }
        if(md5($data['password'])!=$user['password']){
            throw new RJsonError('密码错误', 'NOT_PASSWORD_ACCOUNT');
        }
        //检测这个用户有没有绑定过微博、QQ
        if($data['authType']=='weibo'){
            $userWeibo = self::getWeiboByUid($user['uid']);
            if(!empty($userWeibo)){
                throw new RJsonError('该账号已近被绑定过,请换一个', 'USER_BIND');
            }
        }
        $uoaid = OauthLogic::getSessionUoaid();
        //$uoaid=1;
        //修改微信
        $res=[
            'uid'=>$user['uid'],
            'lastLoginIp'=>$ip,
            'lastLoginAt'=>time(),
        ];
        self::editWechat($res,$uoaid);
        $res = self::getWechatByUoaid($uoaid);
        if($res['uid']==0){
            throw new RJsonError('绑定失败sss', 'WECHAT_BIND');
        }
        //从session拿信息
        $authInfo = \Session::get('authInfo');
        //修改用户信息--头像和昵称是否为空
        $account['uid']=$user['uid'];
        if(empty($user['userNickname'])){
            $account['userNickname']=empty($authInfo['nickname']) ? '' : $authInfo['nickname'];
        }
        if(empty($user['userAvatar'])){
            $account['userAvatar']=empty($authInfo['avatar']) ? '' : $authInfo['avatar'];
        }
        if($user['sex']){
            $account['sex']=empty($authInfo['sex']) ? $user['sex'] : $authInfo['sex'];
        }
        UserLogic::editUser($account,$user['uid']);
        //直接登录
        \Session::put(['uid'=>$res['uid']]);
        \Session::remove('uoaid');
        $user['noRegister']=true;
        return $user;
    }
    //获取微博信息
    public static function getWeiboByUid($uid)
    {
        $res = OauthModel::whereSiteId()->where('uid',$uid)->where('open_type',41)->firstHumpArray();
        return $res;
    }
    //获取QQ信息
    public static function getQQByUid($uid)
    {
        $res = OauthModel::whereSiteId()->where('uid',$uid)->where('open_type',41)->firstHumpArray();
        return $res;
    }

    //获取微信用户信息--导出用
    public static function getWechatUser($siteId,$id,$number)
    {
        $model=new OauthModel();
        $res = $model->where('site_id',$siteId)->where('open_type',2)->where('uoaid','>',$id)->limit($number)->getHumpArray(['jsondata']);
        $arr=[];
        foreach ($res as $key=>$value){
            $info =json_decode($value['jsondata'],true);
            $rea1['nickname']=$info['nickname'] ?? '';
            $rea1['sex']=$info['sex'];
            $rea1['city']=$info['city'];
            $rea1['province']=$info['province'];
            $rea1['country']=$info['country'];
            $rea1['openid']=$info['openid'];
            $arr[]=$rea1;
        }
        return $arr;
    }
    //获取微信用户信息--导出用
    public static function getWechatUserByOpenid($openid)
    {
        $model=new OauthModel();
        $res = $model->where('openid',$openid)->firstHumpArray(['jsondata']);
        return $res;
    }

}
