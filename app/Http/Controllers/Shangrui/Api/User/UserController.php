<?php

namespace App\Http\Controllers\Shangrui\Api\User;

use \App\Http\Controllers\Controller;
use App\Logic\Shangrui\Api\User\UserLogic;

class UserController extends Controller
{
    /*
     * 查询用户信息
     */
    public function showUser() {

        $res = UserLogic::showUser();
        return $res;
    }

    /*
     * 用户添加意见反馈
     */
    public function addUserMessage(){
        $this->verify(
            [
                'userMessageContents' => 'no_required', //意见反馈
            ]
            ,'POST');
        $res = UserLogic::addUserMessage($this->verifyData);
        return $res;
    }

    /*
     *
     */

    /*
     * 用户添加收货地址
     */
    public function addAddress(){

        $this->verify(
            [
                'userName' => '',  // 用户名
                'userPhone' => '', //用户手机号
                'userTel' => 'no_required', // 电话
                'areaId1' => 'no_required', // 省
                'areaId2' => 'no_required', // 市
                'areaId3' => 'no_required', // 区
                'address' => '', // 详细地址
                'isDefault' => 'no_required',  //是否为默认地址， 0：否 1：是
                'addressStatus' => 'no_required', // 是否有效 -1:无效 1：有效
                'createdAt' => 'no_required',
            ]
            ,'POST');
        $res = UserLogic::addAddress($this->verifyData);
        return $res;
    }
    /*
     * 查看用户收货地址
     * 传addressId 查单条
     */
    public function showAddress(){
        $this->verify(
            [
                'addressId' => 'no_required', //地址信息id
            ]
            ,'GET');
            $res = UserLogic::showAddress($this->verifyData);
            return $res;
    }

    /*
     * 用户修改收货地址
     */
    public function editAddress(){

        $this->verify(
            [
                'addressId' => '', //地址信息ID
                'userName' => 'no_required',
                'userPhone' => 'no_required',
                'userTel' => 'no_required',
                'areaId1' => 'no_required',
                'areaId2' => 'no_required',
                'areaId3' => 'no_required',
                'address' => 'no_required',
                'isDefault' => 'no_required',
                'addressStatus' => 'no_required',
            ]
            ,'POST');
        $res = UserLogic::editAddress($this->verifyData);
        return $res;
    }

    /*
     * 用户删除收货地址
     */
    public function deleteAddress(){
        $this->verify(
            [
                'addressId' => '', //地址信息ID
            ]
            ,'POST');
        $res = UserLogic::deleteAddress($this->verifyData);
        return $res;
    }
}
