<?php

namespace App\Http\Controllers\V0\Admin\Module;

use App\Logic\V0\Module\ModuleLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class ModuleController extends Controller
{
    public function index(){
        $this->validate(null, [
           'name' => 'string',
        ]);
        $res = ModuleLogic::getModuleList($this->verifyData);
        return $res;
    }

    public function store(){
        $this->validate(null, [
            'name' => 'required|string',
            'isOn' => 'required|integer',
            //'details' => 'no_required|array'
        ]);
        if(ModuleLogic::store($this->verifyData)){
            return [];
        }
        throw new RJsonError('新增模块失败');
    }

    public function update(){
        $this->validate(null, [
            'modelId' => 'required|integer',
            'name' => 'required|string',
            'isOn' => 'required|integer',
            //'details' => 'no_required|array'
        ]);
        if(ModuleLogic::update($this->verifyData)){
            return [];
        }
        throw new RJsonError('修改模块失败');
    }

    public function destroy(){
        $this->validate(null, [
            'modelId' => 'required|integer',
        ]);
        if(ModuleLogic::destroy($this->verifyData)){
            return [];
        }
        throw new RJsonError('删除模块失败');
    }

    public function info(){
        $this->validate(null, [
            'modelId' => 'required|integer',
        ]);
        $info = ModuleLogic::info($this->verifyData);
        return [
            'data' => $info
        ];
    }



    public function getDetailsLists(){
        $this->verify(
            [
                'modelId' => 'no_required',
            ]
            , 'GET');
        $details = ModuleLogic::getDetailsLists($this->verifyData);
        return $details;
    }

    //没有分页
    public function getModuleList(){
        $this->validate(null, [
            'name' => 'string',
        ]);
        $res = ModuleLogic::ModuleList($this->verifyData);
        return ['lists'=>$res];
    }

    public function storeDetails(){
        $this->validate(null, [
            'modelId' => 'required|integer',
            'details' => 'required|array'
        ]);
        ModuleLogic::storeDetails($this->verifyData['details'],$this->verifyData['modelId']);
        return;
    }

    public function destroyDetails(){
        $this->verify(
            [
                'modelDetailsId' => '',
            ]
            , 'POST');
        ModuleLogic::destroyDetails($this->verifyData['modelDetailsId']);
        return;
    }

    public function getDetailsOne(){
        $this->validate(null, [
            'modelDetailsId' => 'required|integer',
        ]);
        $res = ModuleLogic::getDetailsOne($this->verifyData['modelDetailsId']);
        return ['data'=>$res];
    }

    public function editDetails(){
        $this->validate(null, [
            'modelDetailsId' => 'required|integer',
            'modelId' => 'required|integer',
            'name' => 'required|string',
            'code' => ['required', 'regex:/^[A-Z]{3,3}[0-9]{3,3}?$/'],
        ]);
        $res = ModuleLogic::editDetails($this->verifyData);
        return ['data'=>$res];
    }



}