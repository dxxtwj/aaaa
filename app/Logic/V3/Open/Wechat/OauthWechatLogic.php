<?php
namespace App\Logic\V3\Open\Wechat;
use App\Logic\Exception;
use App\Logic\V2\Common\LoadDataLogic;
use App\Model\V1\Domain\DomainModel;
use App\Model\V1\Open\OpenAppWechatModel;
use App\Model\V1\Open\OpenPaymchWechatModel;
use App\Model\V1\User\OauthWechatModel;
use DdvPhp\DdvUrl;
use EasyWeChat\Factory;


class OauthWechatLogic extends LoadDataLogic
{
    public $wechatAppId = '';
    public $uid = '';
    public $openId = '';
    public $unionId = '';
    public $privilege = '';
    public $nickName = '';
    public $sex = '';
    public $province = '';
    public $city = '';
    public $country = '';
    public $headimgUrl = '';

    /**
     * @return $model
     * @throws Exception
     */
    public function getOne(){
        $model = (new OauthWechatModel())->where([
            'wechat_app_id' => $this->wechatAppId,
            'open_id'=>$this->openId
        ])->firstHump();
        if(empty($model)){
            throw new Exception('没有找到该公众号信息', 'APP_INFO_NOT_FIND');
        }
        return $model;
    }
    /**
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function update($data = []){
        // 如果传入了数据就直接加载到逻辑层
        if (!empty($data)){
            $this->load($data);
        }
        // 试图获取这条数据
        try{
            $model = $this->getOne();
        }catch (Exception $e){
            // 不存在就新增
            $model = new OauthWechatModel();
        }
        // 读取数据
        $data = $this->getAttributes(null, ['', null]);
        $model->setDataByArray($data);
        $model->toUnderline();
        if(!$model->save()){
            throw new Exception('操作失败', 'SAVE_FAIL');
        }
        return true;
    }
    
}
?>