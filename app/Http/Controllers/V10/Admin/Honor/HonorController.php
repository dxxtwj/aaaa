<?php

namespace App\Http\Controllers\V10\Admin\Honor;

use App\Logic\V10\Honor\HonorLogic;
use \App\Http\Controllers\Controller;

class HonorController extends Controller
{
    /*
     * 这个是添加荣誉证书的方法
     */
    public function honorAdd() {

        $this->verify(
            [
                'name'=>'',//荣誉证书名字
                'pic'=>'',//图片
                'sort'=>'',//排序
            ]
            , 'POST');
        HonorLogic::honorAdd($this->verifyData);
        return;
    }
    /*
     * 查询全部荣誉证书
     */
    public function honorShow() {

        $res = HonorLogic::HonorShow();
        return $res;
    }

    /*
     * 查询单条
     */
    public function honorFirst() {

        $this->verify(
            [
                'honorId'=>'egnum',//id
            ]
            , 'POST');
        $res = HonorLogic::honorFirst($this->verifyData['honorId']);
        return ['data'=>$res];
    }
    /*
     * 修改荣誉证书
     */
    public function honorEdit() {
        $this->verify(
            [
                'honorId'=>'egnum',//id
                'name'=>'',//荣誉证书名字
                'pic'=>'',//图片
                'sort'=>'',//排序
            ]
            , 'POST');
        HonorLogic::honorEdit($this->verifyData);
        return;
    }
    /*
     * 删除荣誉证书
     */
    public function honorDelete() {

        $this->verify(
            [
                'honorId'=>'egnum',//id
            ]
            , 'POST');
        HonorLogic::honorDelete($this->verifyData['honorId']);
        return;
    }

}
