<?php

namespace App\Http\Controllers\Admin\KuJiale;

use \App\Http\Controllers\Controller;
use App\Logic\KuJiale\KuJialeLogic;
use App\Model\KuJiale\KuJialeModel;

class KuJialeController extends Controller
{

    /*
     * 添加
     */
    public function kuJiaLeAdd() {

        $this->verify(
            [
                'name' => '',//用户账号
                'userName' => 'no_required',//用户姓名
                'password1'=>'',
                'password2'=>'',
                'wechat'=>'no_required',//微信
                'keyId'=>'no_required',
                'sex'=>'no_required',
                'phone'=>'no_required|mobile',
                'qq'=>'no_required',
                'email'=>'no_required|email',
                'centent'=>'no_required',//备注
                'isOn' => 'no_required',//状态  0表示禁用 1表示启用
            ]
            , 'POST');
        KuJialeLogic::kuJiaLeAdd($this->verifyData);
        return;

    }
    /*
     * 查看全部用户数据
     * 分页
     */
    public function kuJiaLeShow() {

        $this->verify(
            [
                'userName' => 'no_required',
                'name' => 'no_required',
                'wechat' => 'no_required',
                'phone' => 'no_required',
                'qq' => 'no_required',
                'email' => 'no_required',

            ]
            , 'GET');

       $res = KuJialeLogic::kuJiaLeShow($this->verifyData);
       return $res;
    }

    /*
     * 查询单条数据
     */
    public function kuJiaLeFirst() {


        $this->verify(
            [
                'kujialeId' => 'egnum',//用户id

            ]
            , 'GET');
        $res = KuJialeLogic::kuJiaLeFirst($this->verifyData['kujialeId']);
        return ['data' => $res];

    }

    /*
     * 这个方法是修改个人信息的
     */
    public function kuJiaLeEditPersonal() {


        $this->verify(
            [
                'kujialeId' => 'egnum',
                'userName' => 'no_required',//用户姓名
                'wechat'=>'no_required',//微信
                //'keyId'=>'no_required',
                'sex'=>'no_required',
                'phone'=>'no_required|mobile',
                'qq'=>'no_required',
                'email'=>'no_required|email',
                'centent'=>'no_required',//备注
            ]
            , 'POST');

        KuJialeLogic::kuJiaLeEditPersonal($this->verifyData);
        return;

    }

    /*
     * 这个方法是修改状态的
     */
    public function kuJiaLeIson() {
        $this->verify(
            [
                'kujialeId' => 'egnum',
                'isOn'=>'no_required',//0 禁用  1启用
            ]
            , 'POST');
        KuJialeLogic::kuJiaLeEditIsOn($this->verifyData);
        return;
    }

    /*
     * 这个方法是修改密码的
     */
    public function kuJiaLeEditPassword() {

        $this->verify(
            [
                'kujialeId' => 'egnum',
                'passwordNew'=>'',//新密码
                'passwordCon'=>'',//第二次密码
            ]
            , 'POST');
        KuJialeLogic::kuJiaLeEditPassword($this->verifyData);
        return;
    }

    /*
     * 删除用户
     */
    public function kuJiaLeDelete() {

        $this->verify(
            [
                'kujiale' => '',// 用户id
            ]
            , 'POST');
        KuJialeLogic::kuJiaLeDelete($this->verifyData['kujiale']);
        return;
    }




}
