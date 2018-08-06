<?php

namespace App\Logic\Shopping\Common\Wechat;

use App\Http\Middleware\ClientIp;
use App\Logic\Exception;
use App\Logic\Shopping\Common\LoadDataLogic;
use App\Logic\V3\Common\User\OauthCommonLogic;
use App\Model\V3\Common\UserOauthCommonModel;
use App\Model\V3\Common\UserOauthWechatCommonModel;
use App\Logic\V3\Common\User\LoginCommonLogic;

class OauthWechatCommonLogic extends LoadDataLogic
{
    // 应用唯一标识
    protected $wechatAppId = '';
    // 用户ID
//    protected $uid = '';
    // 授权用户唯一标识
    protected $openId = '';
    // 当且仅当该网站应用已获得该用户的userinfo授权时，才会出现该字段
    protected $unionId = '';
    // 用户特权信息
    protected $privilege = '';
    // 用户昵称
    protected $nickName = '';
    // 性别
    protected $sex = '';
    // 省份
    protected $province = '';
    // 城市
    protected $city = '';
    // 国家
    protected $country = '';
    // 用户头像
    protected $headimgUrl = '';
    // 注册IP
    protected $registerIp = '';
    // 用户当前登录IP
    protected $lastLoginIp = '';
    // 登录次数
    protected $loginNum = '';
    protected $uoaid = '';

    /**
     * @return $model
     * @throws Exception
     */
    public function getOne(){
        $model = (new UserOauthWechatCommonModel())->where([
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
     * @throws \ReflectionException
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
            $userOauthLogic = new OauthCommonLogic();
            $userOauthLogic->load([
                'oauthType' => UserOauthCommonModel::OAUTH_TYPE_TO_WECHAT,
                'headImg'  => $data['headimgUrl'] ?? '',
                'nickName'  => $data['nickName'] ?? '',
                'gender'  => $data['sex'] ?? '',
                ]);
            $this->uoaid = $userOauthLogic->add();
            $model = new UserOauthWechatCommonModel();
            $this->registerIp = ClientIp::getClientIp();
        }catch (\Exception $e){
            \Log::info($e->getMessage());
            \Log::info($e->getCode());
            \Log::info($e->getFile());
        }
        // 读取数据
        $this->privilege = json_encode($this->privilege);
        $this->loginNum = $model->login_num + 1;
        $this->lastLoginIp = ClientIp::getClientIp();
        $data = $this->getAttributes(null, ['', null]);
        try{
            $model->setDataByArray($data);
            $model->toUnderline();
            $model->save();
        }catch (\Exception $e){
            \Log::info($e->getFile());
//            \Log::info($e->getTraceAsString());
            \Log::info($e->getMessage());
            \Log::info($e->getCode());
        }
        if(!$model->save()){
            throw new Exception('操作失败', 'SAVE_FAIL');
        }
        return true;
    }

}
?>