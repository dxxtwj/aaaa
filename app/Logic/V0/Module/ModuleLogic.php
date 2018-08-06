<?php

namespace App\Logic\V0\Module;
use App\Model\V0\Module\ModuleDetailsModel;
use App\Model\V0\Module\ModuleModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
class ModuleLogic
{
    //模块列表
    public static function getModuleList($data){
        $moduleDesc = new ModuleModel();
        if(!empty($data['name'])){
            $moduleDesc = $moduleDesc->where('name', 'like', '%'.$data['name'].'%');
        }
        return $moduleDesc->getDdvPageHumpArray();
    }

    //模块列表-没有分页
    public static function ModuleList($data){
        $moduleDesc = new ModuleModel();
        $moduleInfo = $moduleDesc->getHump();
        if(!empty($moduleInfo)){
            foreach($moduleInfo as $item){
                $item->moduleDetails = $item->moduleDetails()->getHump();
            }
        }
        return $moduleInfo;
    }
    //添加
    public static function store($data){
        $model = new ModuleModel();
        $model->name = $data['name'];
        $model->is_on = $data['isOn'];
        //self::_checkModelDetails($data['details']);
        \DB::beginTransaction();
        try{
            $model->save();
            $moduleId = $model->getQueueableId();
            /*foreach($data['details'] as $item){
             if((new ModuleDetailsModel())->where(['code' => $item['code']])->first()){
                throw new RJsonError('标识已存在' . $item['code'], 'ERROR_FIND_CODE');
             }
             $modelDetails = new  ModuleDetailsModel();
             $modelDetails->setDataByArray(array_merge($item , ['model_id' => $moduleId]))->save();
            }*/
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return true;
    }
    //模块修改
    public static function update($data){
        $model = (new ModuleModel())->where(['model_id' => $data['modelId']])->first();
        if(empty($model)){
            throw new RJsonError('模型不存在', 'NOT_FIND_MODEL');
        }
        $model->name = $data['name'];
        $model->is_on = $data['isOn'];
        //self::_checkModelDetails($data['details']);
        \DB::beginTransaction();
        try{
            $model->save();
            /*foreach($data['details'] as $item){
                (new ModuleDetailsModel())->where(['model_id' => $data['modelId']])->delete();
                if((new ModuleDetailsModel())->where(['code' => $item['code']])->first()){
                    throw new RJsonError('标识已存在' . $item['code'], 'ERROR_FIND_CODE');
                }
                $modelDetails = new  ModuleDetailsModel();
                $modelDetails->setDataByArray(array_merge($item , ['model_id' => $data['modelId']]))->save();
            }*/
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return true;
    }
    //模块删除
    public static function destroy($data){
        $model = (new ModuleModel())->where(['model_id' => $data['modelId']])->first();
        if(empty($model)){
            throw new RJsonError('模型不存在', 'NOT_FIND_MODEL');
        }
        \DB::beginTransaction();
        try{
            \DB::commit();
            $model->delete();
            (new ModuleDetailsModel())->where(['model_id' => $data['modelId']])->delete();
        }catch(QueryException $exception){
            \DB::rollBack();
            throw new RJsonError($exception->getMessage(), $exception->getCode());
        }
        return true;
    }

    //模块单条
    public static function info($data){
        $info = (new ModuleModel())->where(['model_id' => $data['modelId']])->firstHump();
        if(empty($info)){
            throw new RJsonError('模型不存在', 'NOT_FIND_MODEL');
        }
        //$info->moduleDetails = $info->moduleDetails()->getHump();
        return $info;
    }

    //验证格式
    private static function _checkModelDetails($details){
        foreach($details as $items) {
            if(!is_array($items)){
                throw new RJsonError('格式不正确', 'ERROR_DATA_FORMAT');
            }
            $validator = Validator::make($items, [
                'name' => 'required|string',
                'code' => ['required', 'regex:/^[A-Z]{3,3}[0-9]{3,3}?$/'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->failed() as $key => $item) {
                    throw new RJsonError($key . '验证错误[' . json_encode($item) . ']', strtoupper(\DdvPhp\DdvUtil\String\Conversion::humpToUnderline($key)) . '_ERROR');
                }
            }
        }
    }

    //模块详情列表
    public static function getDetailsLists($data){
        $name=[];
        $model=[];
        if(!empty($data['modelId'])){
            $name='model_id';
            $model=$data['modelId'];
        }
        $Details = (new ModuleDetailsModel())->where($name,$model)->select();
        return $Details->getDdvPageHumpArray();
    }

    //添加详情
    public static function storeDetails($details,$modelId)
    {
        self::_checkModelDetails($details);
        (new ModuleDetailsModel())->where(['model_id' => $modelId])->delete();
        foreach($details as $item){
            if((new ModuleDetailsModel())->where(['code' => $item['code']])->first()){
                throw new RJsonError('标识已存在' . $item['code'], 'ERROR_FIND_CODE');
            }
            $modelDetails = new  ModuleDetailsModel();
            $modelDetails->setDataByHumpArray($item)->save();
        }
        return true;
    }
    //删除模块详情
    public static function destroyDetails($modelDetailsId){
        (new ModuleDetailsModel())->where('model_details_id',$modelDetailsId)->delete();
        return;
    }
    //获取详情单条
    public static function getDetailsOne($modelDetailsId){
        $res = (new ModuleDetailsModel())->where('model_details_id',$modelDetailsId)->firstHumpArray();
        return $res;
    }
    //编辑模块详情
    public static function editDetails($data){
        $res = (new ModuleDetailsModel())->where('model_details_id',$data['modelDetailsId'])->updateByHump($data);;
        return $res;
    }
}