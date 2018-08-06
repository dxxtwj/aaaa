<?php

namespace App\Http\Controllers\Admin\Works;

use App\Logic\Works\WorksAdvertiseLogic;
use App\Logic\Works\WorksLogic;
use App\Logic\Works\WriterLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class WorksAdvertiseController extends Controller
{
    //添加
    public function AddWorksAdvertise()
    {
        $this->verify(
            [
                'name' => '',//名称
                'content' => '',//内容
                'isOn' => '',//是否显示
            ]
            , 'POST');
        WorksAdvertiseLogic::addWorksAdvertise($this->verifyData);

    }

    //获取全部列表
    public function getWorksAdvertiseLists(){
        $this->verify(
            [
                'name' => 'no_required',
            ]
            , 'GET');
        $res = WorksAdvertiseLogic::getWorksAdvertiseList($this->verifyData);

        return $res;
    }

    //获取单条
    public function getWorksAdvertise(){
        $this->verify(
            [
                'advertiseId' => '',
            ]
            , 'GET');
        $res = WorksAdvertiseLogic::getWorksAdvertise($this->verifyData['advertiseId']);

        return ['data'=>$res];
    }

    public function editWorksAdvertise(){
        $this->verify(
            [
                'advertiseId' => '',//名称
                'name' => 'no_required',//名称
                'content' => '',//内容
                'isOn' => '',//是否显示
            ]
            , 'POST');
        WorksAdvertiseLogic::editWorksAdvertise($this->verifyData);
        return;
    }
    //删除
    public function deleteWriter(){
        $this->verify(
            [
                'writerId' => '',//新闻ID
            ]
            , 'POST');
        WorksAdvertiseLogic::deleteWorksAdvertise($this->verifyData['writerId']);
        return;
    }



}
