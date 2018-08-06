<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\KuJiale;

use App\Model\KuJiale\KuJialeModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;

use Illuminate\Database\QueryException;
use function var_dump;

class KuJialeLogic
{
    /*
     * 添加用户
     * @param array $data 要添加的用户的数据
     * @param string $data['name'] 用户的账号
     */
    public static function kuJiaLeAdd($data) {

        self::userNameEmpty($data['name']); //判断账号是否存在
        self::passwordOneTwo($data['password1'], $data['password2']);//判断密码两次密码是否一致

        // 以上判断都通过则开始入库操作
        $data['password'] = md5($data['password1']);
        //获取
        unset($data['password1']);
        unset($data['password2']);
        $data['key_id'] = self::getKeyId();
        self::add($data);

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
     * 获取随机字符串
     */
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

    /*
     * 这个方法是判断两次密码是否一致
     * @param string $password1 用户第一次输入的密码
     * @param string $password2 用户第二次输入的密码
     */
    public static function PasswordOneTwo($password1, $password2) {

        if ($password1 != $password2) {
            throw new RJsonError('两次密码不一致', 'PASSWORD_ERROR');
        }
    }
    /*
     * 这个方法是判断用户的账号是否存在
     * @param string $name 用户账号
     */
    public static function userNameEmpty($name) {

        $kuJiaLeModel = new KuJialeModel();
        $nameFirst = $kuJiaLeModel
            ->where('name', $name)
            ->first();
        if (!empty($nameFirst)) {

            throw new RJsonError('已经有该账号了', 'NAME_ERROR');
        }
    }
    /*
     * 入库操作
     * @param array $data 要入库的数据
     * @return $lastById 返回添加成功的数据id
     */
    public static function add($data) {

        $kuJiaLeModel = new KuJialeModel();
        $bool = $kuJiaLeModel->setDataByHumpArray($data)->save();
        if (!$bool) {
            throw new RJsonError('添加失败', 'PERSIBAK_ERROR');

        }
        return;
    }

    /*
     * 获取所有用户数据  分页
     * 模糊搜索数据
     * @param array $data  这个是搜索的数据，如果为空则查全部，不为空则查需要的数据
     */
    public static function kuJiaLeShow($data) {

        $kuJiaLeModel = new KuJialeModel();

        if (!empty($data['userName'])) {
            $kuJiaLeModel = $kuJiaLeModel->where('user_name', 'like', '%'.$data['userName'].'%');
        }
        if (!empty($data['name'])) {

            $kuJiaLeModel = $kuJiaLeModel->where('name', 'like', '%'.$data['name'].'%');

        }
        if (!empty($data['wechat'])) {
            $kuJiaLeModel = $kuJiaLeModel->where('wechat', 'like', '%'.$data['wechat'].'%');

        }
        if (!empty($data['phone'])) {
            $kuJiaLeModel = $kuJiaLeModel->where('phone', 'like', '%'.$data['phone'].'%');

        }
        if (!empty($data['qq'])) {
            $kuJiaLeModel = $kuJiaLeModel->where('qq', 'like', '%'.$data['qq'].'%');

        }
        if (!empty($data['email'])) {
           $kuJiaLeModel = $kuJiaLeModel->where('email', 'like', '%'.$data['email'].'%');
        }

        $kuJiaLeShow = $kuJiaLeModel->getDdvPageHumpArray(true);
        foreach ($kuJiaLeShow['lists'] as $k => $v) {

            unset($kuJiaLeShow['lists'][$k]['password']);
        }
        return $kuJiaLeShow;
    }

    /*
     * 查询一条数据
     */
    public static function kuJiaLeFirst($kujialeId) {

        $kuJiaLeModel = new KuJialeModel();
        $kuJiaLeFirst = $kuJiaLeModel->where('kujiale_id', $kujialeId)->firstHumpArray();
        unset($kuJiaLeFirst['password']);
        return $kuJiaLeFirst;
    }

    /*
     * 修改个人信息
     */
    public static function kuJiaLeEditPersonal($data) {

        $kuJiaLeModel = new KuJialeModel();
        $bool = $kuJiaLeModel->where('kujiale_id', $data['kujialeId'])->updateByHump($data);

        if (!$bool) {
            throw new RJsonError('修改失败', 'PERSIBAK_ERROR');

        }
        return;
    }

    /*
     * 修改密码
     */
    public static function kuJiaLeEditPassword($data) {

        self::passwordOneTwo($data['passwordNew'], $data['passwordCon']);//判断密码两次密码是否一致

        $result['password'] = md5($data['passwordNew']);
        $kuJiaLeModel = new KuJialeModel();
        $bool = $kuJiaLeModel->where('kujiale_id', $data['kujialeId'])->updateByHump($result);

        if (!$bool) {
            throw new RJsonError('修改失败', 'PASSWORD_ERROR');

        }

        return;

    }
    /*
     * 这个方法是修改状态的
     * @param int $ison 0 表示禁用  1表示开启
     */
    public static function kuJiaLeEditIsOn($ison) {

        $kuJiaLeModel = new KuJialeModel();
        $result['is_on'] = $ison['isOn'];

        $kuJiaLeModel->where('kujiale_id', $ison['kujialeId'])->updateByHump($result);

        return;
    }

    /*
     * 删除用户的一个方法
     */
    public static function kuJiaLeDelete($arrayId) {

        foreach ($arrayId as $value){
            $kuJiaLeModel = new KuJialeModel();
            $kuJiaLeModel->where('kujiale_id', $value['kujialeId'])->delete();
        }
        return;
    }


}