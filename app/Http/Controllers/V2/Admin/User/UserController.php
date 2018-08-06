<?php

namespace App\Http\Controllers\V2\Admin\User;

use App\Logic\V2\Common\VerifyLogic;
use App\Logic\V2\User\UserLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use \App\Logic\User\LoginLogic;
use OSS\OssClient;
use Mail;

class UserController extends Controller
{
    //用户列表
    public function getUserLists()
    {
        $this->verify(
            [
                'userNickname' => 'no_required|string',
            ]
            , 'GET');
        $res = UserLogic::getUserLists($this->verifyData);
        return $res;
    }

    public function getTest()
    {
        $accessKeyId='LTAIgpKchru0UhWp';
        $accessKeySecret='4BeaRPBTf9q72RJrdd7YFwDjmOMhU3';
        $endpoint='oss-cn-shenzhen.aliyuncs.com';
        //$bucket='autotest-oss';
        $objects='test/';
        $OssClient= new OssClient($accessKeyId,$accessKeySecret,$endpoint);
        $res = $OssClient->deleteObject($bucket,$objects);
        var_dump($res);
    }


}
