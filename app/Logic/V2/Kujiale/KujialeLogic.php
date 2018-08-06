<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V2\Kujiale;

use App\Http\Middleware\ClientIp;
use App\Logic\V2\Common\VerifyLogic;
use App\Model\V2\Kujiale\KujialeModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class KujialeLogic
{
    public static function generateVerifyCode($length = 5, $charset = 'abcdefghijklmnpqrstuvwxyz123456789'){
        $phrase = '';
        //把字符串分割到数组中
        $chars = str_split($charset);

        for ($i = 0; $i < $length; $i++) {
            //随机
            $phrase .= $chars[array_rand($chars)];
        }
        return $phrase;
    }
    public static function Kujiale($name,$password,$new=[])
    {
        $kujiale=[];
        //是否存在
        $res = self::getKujialeByName($name);
        //假如为空就是新用户,添加
        if(empty($res)){
            //随机数
            $rand = self::getKeyId();
            $newPassword = md5($password);
            $data=[
                'name'=>$name,
                'password'=>$newPassword,
                'keyId'=>$rand,
            ];
            $id = self::add($data);
            if($id){
                //获取数据
                $kujiale = self::getKujialeById($id);
            }
        }else{
            //获取信息
            $newPassword = md5($password);
            $kujiale = self::getKujiale($name,$newPassword);
            if(empty($kujiale)){
                if(empty($new)){
                    throw new RJsonError('密码错误', 'PASSWORD_ERROR');
                }
                if(!empty($new)){
                    //随机数
                    $rand = self::getKeyId();
                    $newPassword = md5($password);
                    $data=[
                        'name'=>$name,
                        'password'=>$newPassword,
                        'keyId'=>$rand,
                    ];
                    $id = self::add($data);
                    if($id){
                        //获取数据
                        $kujiale = self::getKujialeById($id);
                    }
                }
            }
        }
        return $kujiale;

    }
    public static function add($data)
    {
        $model = new KujialeModel();
        $model->setDataByHumpArray($data)->save();
        return $model->getQueueableId();
    }
    public static function getKujialeById($id)
    {
        $model = new KujialeModel();
        $res = $model->where('kujiale_id',$id)->firstHumpArray(['name','key_id']);
        return $res;
    }
    public static function getKujialeByName($name)
    {
        $model = new KujialeModel();
        $res = $model->where('name',$name)->firstHumpArray();
        return $res;
    }
    public static function getKujiale($name,$password)
    {
        $model = new KujialeModel();
        $model = $model->where('name',$name);
        $res = $model->where('password',$password)->firstHumpArray(['name','key_id']);
        return $res;
    }
    //生成keyId
    public static function getKeyId()
    {
        $rand = 'bird'.self::generateVerifyCode(12);
        $res = self::checkKeyId($rand);
        if(!empty($res)){
            self::getKeyId();
        }
        return $rand;
    }
    //查看keyId是否重复
    public static function checkKeyId($rand)
    {
        $model = new KujialeModel();
        $res = $model->where('key_id',$rand)->firstHumpArray();
        return $res;
    }

    /*
     * 酷家乐登录
     * @param array 登录成功返回数据
     */
    public static function loginKuJiaLe($data) {

        $KuJiaLeModel = new KujialeModel();
        $res = $KuJiaLeModel
            ->where('name', $data['name'])
            ->firstHumpArray(['password', 'name', 'key_id', 'user_name', 'phone', 'email', 'qq', 'is_on']);

        if (empty($res)) {

            throw new RJsonError('账号不存在', 'KUJIALE_ERROR');
        }

        if ((int)$res['isOn'] === 0) {//禁用

            throw new RJsonError('该账户已被禁用', 'STATE_ERROR');
        }

        if (md5($data['password']) != $res['password']) {
            throw new RJsonError('密码错误', 'KUJIALE_ERROR');
        }

        return $res;

    }

    public static function kujialePassword($data)
    {
        //获取账号
        $res = self::getKujialeByName($data['name']);
        if(empty($res)){
            throw new RJsonError('该账号不存在', 'KUJIALE_ERROR');
        }
        if (md5($data['passwordOld']) != $res['password']) {
            throw new RJsonError('密码错误', 'KUJIALE_ERROR');
        }
        $pass['password']=md5($data['passwordNew']);
        self::kuJiaLeEdit($res['name'],$pass);
        return;
    }
    /*
     * 修改个人信息
     */
    public static function kuJiaLeEdit($name,$data) {

        $kuJiaLeModel = new KuJialeModel();
        $kuJiaLeModel->where('name', $name)->updateByHump($data);
        return;
    }



}
