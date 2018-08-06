<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Admin\About;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\About\AboutModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;

class AboutLogic extends ShoppingLogic
{
//    public static function addAbout($data) {

//        $aboutModel = new  AboutModel();

//        $res = $aboutModel->getHumpArray();

//        if (!empty($res)) {

//            throw new RJsonError('关于我们已存在','ABOUT_ERROR');
//        }

//        $saveData['about_content'] = $data['aboutContent'];
//        $aboutModel->setDataByHumpArray($saveData)->save();

//    }

    public static function editAbout($data) {

        $aboutModel = new  AboutModel();
        $aboutData['about_content'] = empty($data['aboutContent']) ? '' :$data['aboutContent'];

        $res = $aboutModel->select(['about_id'])->firstHumpArray();
        if (empty($res)) {
            $aboutModel->setDataByHumpArray($aboutData)->save();
        } else {
            $where['about_id'] = $res['aboutId'];
            $aboutModel->where($where)->update($aboutData);
        }

        return ;

    }

    public static function showAbout() {

        $aboutModel = new  AboutModel();
        $res = $aboutModel->firstHumpArray();
        return ['data' =>$res];

    }

//    public static function deleteAbout($data) {
//
//        $aboutModel = new  AboutModel();
//        $where['about_id'] = $data['aboutId'];
//        $aboutModel->where($where)->delete();
//        return ;
//    }

}