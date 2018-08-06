<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shangrui\Api\User;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shangrui\User\UserModel;
use App\Model\Shangrui\UserAddress\UserAddressModel;
use App\Model\Shangrui\UserMessage\UserMessageModel;
use App\Model\V1\User\AddressModel;
use App\User;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;


class UserLogic extends ShoppingLogic
{
    // 前台查看用户信息
    public static function showUser($data=array()){
        $userModel = new UserModel();
        $res = $userModel->where('user_id',\Session::get('userId'))->firstHumpArray();

        return ['data' => $res];
    }


    //  添加收货地址
    public static function addAddress($data = array()){
        $userAddressModel = new UserAddressModel();

        // 查出之前为默认的地址修改
        $default = $userAddressModel
            ->where('user_id',\Session::get('userId'))
            ->where('is_default',1)
            ->getHumpArray();

        if (!empty($default)){
            foreach ($default as $k => $v){
                $update['is_default'] = 0;
                $userAddressModel->where('address_id',$v['addressId'])->updateByHump($update);
            }
        }
        $addressData['user_name'] = $data['userName'];
        $addressData['user_id'] = \Session::get('userId');
        $addressData['user_phone'] = $data['userPhone'];
        $addressData['user_tel'] = empty($data['userTel']) ? '' : $data['userTel'];
        $addressData['area_id1'] = empty($data['areaId1']) ? '' : $data['areaId1'];
        $addressData['area_id2'] = empty($data['areaId2']) ? '' : $data['areaId2'];
        $addressData['area_id3'] = empty($data['areaId3']) ? '' : $data['areaId3'];
        $addressData['address'] = $data['address'];
        $addressData['is_default'] = empty($data['isDefault']) ? '0' : $data['isDefault'];
        $addressData['address_status'] = empty($data['addressStatus']) ? '1' : $data['addressStatus'];
        $addressData['created_at'] = empty($data['createdAt']) ? time() : $data['createdAt'];

        $bool = $userAddressModel->setDataByArray($addressData)->save();
        if (empty($bool)) {
            throw new RJsonError('收货地址添加失败','ADDRESS_ERROR');
        }
    }

    // 前台查看收货地址
    public static function showAddress($data){

        $userAddressModel = new UserAddressModel();

        if (empty($data['addressId'])){ //查全部
            $res = $userAddressModel
                ->where('user_id', \Session::get('userId'))
                ->orderBy('is_default','DESC')
                ->getDdvPageHumpArray();

        } elseif (!empty($data['addressId'])){ //查单条
            $res = $userAddressModel
                ->where('user_id',\Session::get('userId'))
                ->where('address_id',$data['addressId'])
                ->firstHumpArray();

        }
        return $res;

    }

    // 前台修改收货地址
    public static function editAddress($data = array()){
        $userAddressModel = new UserAddressModel();
        // 查出之前为默认的地址修改
        $default = $userAddressModel
            ->where('user_id',\Session::get('userId'))
            ->where('is_default',1)
            ->getHumpArray();

        if (!empty($default)){
            foreach ($default as $k => $v){
                $update['is_default'] = 0;
                $userAddressModel->where('address_id',$v['addressId'])->updateByHump($update);
            }
        }
        $address = $userAddressModel->where('address_id',$data['addressId'])->firstHumpArray();
        if (!empty($address)){
            $addressData['user_name'] = empty($data['userName']) ? $address['userName'] : $data['userName'];
            $addressData['user_phone'] = empty($data['userPhone']) ? $address['userPhone'] : $data['userPhone'];
            $addressData['user_tel'] = empty($data['userTel']) ? $address['userTel'] : $data['userTel'];
            $addressData['area_id1'] = empty($data['areaId1']) ? $address['areaId1'] : $data['areaId1'];
            $addressData['area_id2'] = empty($data['areaId2']) ? $address['areaId2'] : $data['areaId2'];
            $addressData['area_id3'] = empty($data['areaId3']) ? $address['areaId3'] : $data['areaId3'];
            $addressData['address'] = empty($data['address']) ? $address['address'] : $data['address'];
            $addressData['is_default'] = empty($data['isDefault']) ? 0 : 1;
            $addressData['address_status'] = empty($data['addressStatus']) ? $address['addressStatus'] : $data['addressStatus'];
            $addressData['created_at'] = time();

            $bool = $userAddressModel->where('address_id',$data['addressId'])->updateByHump($addressData);
            if (empty($bool)){
                throw new RJsonError('修改收货地址失败','ADDRESS_ERROR');
            }
        } else {
            throw new RJsonError('收货地址有误','ADDRESS_ERROR');
        }
    }

    //删除收货地址
    public static function deleteAddress($data=array()){
        $addressModel = new  UserAddressModel();
        $bool = $addressModel->where('address_id',$data['addressId'])->delete();

        if (!$bool){
            throw new RJsonError('删除用户地址失败','ADDRESS_ERROR');
        }
    }


    //前台添加意见反馈
    public static function addUserMessage($data = array()){

        $userMessageModel = new UserMessageModel();


        $messageData['user_message_contents'] = empty($data['userMessageContents']) ? '' : $data['userMessageContents'];

        $messageData['created_at'] = time();


        if (!empty($messageData['user_message_contents'])){
            $len = mb_strlen($messageData['user_message_contents']);
            if ($len > 200){
                throw new RJsonError('字数不能超出200哦','MESSAGE_ERROR');
            } elseif ($len <= 200) {
                $messageData['user_id'] = \Session::get('userId');   // 从session中取用户ID
                $bool = $userMessageModel->setDataByArray($messageData)->save();
                if (empty($bool)){
                    throw new RJsonError('意见反馈失败','MESSAGE_ERROR');
                }
            }
        } elseif ($messageData['user_message_contents'] == ''){
            throw new RJsonError('留言内容不能为空哦','MESSAGE_ERROR');

//            $messageData['user_id'] = \Session::get('userId'); // 从session取用户ID
//            $messageData['user_id'] = $data['userId'];
//            $bool = $userMessageModel->setDataByArray($messageData)->save();
//            if (empty($bool)){
//                throw new RJsonError('意见反馈失败','MESSAGE_ERROR');
        }
    }
}