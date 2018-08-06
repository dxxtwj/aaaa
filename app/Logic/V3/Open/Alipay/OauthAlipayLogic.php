<?php
namespace App\Logic\V3\Open\Alipay;
use App\Http\Middleware\ClientIp;
use App\Logic\Exception;
use App\Logic\V1\User\LoginLogic;
use App\Model\V1\User\OauthAlipayModel;
use App\Logic\V2\Common\LoadDataLogic;

class OauthAlipayLogic extends LoadDataLogic
{
    public $uid = '';
    public $userId = '';
    public $alipayUserId = '';
    public $lastLoginIp = '';
    public $loginNum = '';
    public $registerIp = '';
    public $accessToken = '';
    public $refreshToken = '';
    public $expiresIn = '';
    public $reExpiresIn = '';
    public $avatar = '';
    public $city = '';
    public $gender = '';
    public $isCertified = '';
    public $isStudentCertified = '';
    public $nickName = '';
    public $province = '';
    public $userStatus = '';
    public $userType = '';

    /**
     * @return $model
     * @throws Exception
     */
    public function getOne(){
        $model = (new OauthAlipayModel())->where([
            'user_id' => $this->userId,
        ])->firstHump();
        if(empty($model)){
            throw new Exception('没有找到该账户信息', 'APP_INFO_NOT_FIND');
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
            $model = new OauthAlipayModel();
        }
        // 读取数据
        $this->loginNum = $model->login_num + 1;
        $this->lastLoginIp = ClientIp::getClientIp();
        $this->uid = LoginLogic::getLoginUid();
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