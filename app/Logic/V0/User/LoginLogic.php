<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\User;

use App\Http\Controllers\V2\Api\User\UserController;
use App\Http\Middleware\ClientIp;
use App\Logic\V0\Common\VerifyLogic;
use App\Model\V0\User\UserLoginModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class LoginLogic
{
    public static function login($data)
    {
        $ip = ClientIp::get();
        //是否存在
        $user = UserLogic::getUser($data['phone']);
        if(empty($user)){
            throw new RJsonError('该账号不存在', 'ACCOUNT_ERROR');
        }
        //登录失败次数
        $count = self::UserLogin($data['phone']);
        if($count > 3){
            if(empty($data['codeImg'])){
                throw new RJsonError('请输入图片验证码', 'NO_CODEIMG');
            }
            VerifyLogic::imageCode($data['codeImg']);
        }
        if(md5($data['password'])!=$user['password']){
            throw new RJsonError('密码错误', 'NOT_PASSWORD_ACCOUNT');
        }
        \Session::put(['uid'=>$user['uid']]);
        //修改登录Ip
        self::UserLoginIp($user['uid'],$ip);
        self::UserLoginDel($data['phone']);
        return $count;
    }

    //登录次数
    public static function UserLogin($phone)
    {
        $ip = ClientIp::get();
        $data = [
            'phone'=>$phone,
            'loginIp'=>$ip,
        ];
        $model = new UserLoginModel();
        $model->setDataByHumpArray($data)->save();
        $count = self::LoginCount($phone);
        return $count;
    }
    //删除登录次数
    public static function UserLoginDel($phone)
    {
        $model = new UserLoginModel();
        $model->where('phone',$phone)->delete();
        return;
    }
    public static function UserLoginIp($uid,$ip)
    {
        $data['loginIp'] = $ip;
        UserLogic::edit($uid,$data);
        return;
    }

    public static function LoginCount($phone)
    {
        $model = new UserLoginModel();
        $count = $model->where('phone',$phone)->count();
        return $count;
    }

    //登录状态
    public static function isLogin()
    {
        //\Session::put(['uid'=>1]);
        $uid = \Session::get('uid');//
        return $uid;
    }

    //退出登录
    public static function logout()
    {
        \Session::remove('uid');
        return true;
    }


}
