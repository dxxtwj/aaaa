<?php
namespace App\EasykeApiAuth;

use App\Model\AuthModel;
use \DdvPhp\DdvRestfulApi\Exception\AuthError as AuthErrorException;
use \DdvPhp\DdvAuth\Sign;
use \DdvPhp\DdvAuth\AuthSha256;
/**
 *
 */
class AuthSignEasykeAuthV1 extends \DdvPhp\DdvRestfulApi\Auth\AuthAbstract
{
    private static $accessKeyId = null;
    private $regAuth =
        '/^([0-9a-zA-Z,-]+)\/([\d]{4}-[\d]{2}-[\d]{2}T[\d]{2}:[\d]{2}:[\d]{2}Z)\/(\d+)\/([\w\-\;]+|)\/([\da-f]{64})$/i';
    protected function sign()
    {
        // 试图旧授权信息
        $this->checkAuth();
    }
    public static function getAccessKeyId(){
        return AuthSignEasykeAuthV1::$accessKeyId;
    }
    private function checkAuth()
    {
        $auths = array();
        // 试图正则匹配
        preg_match($this->regAuth, $this->authorization,$auths);
        //
        if (count($auths)!==6) {
            throw new AuthErrorException('Authentication Info Length Error','AUTHORIZATION_ERROR_INFO_LENGTH',403);
        }elseif (empty($auths[0])) {
            //抛出授权信息格式异常
            throw new AuthErrorException('Authentication wrong format as content','AUTHORIZATION_ERROR_FORMAT_WRONG',403);
        }
        list(
            ,
            //授权id
            $accessKeyId,
            // 签名时间
            $signTimeString,
            // 过期时间
            $expiredTimeOffset,
            // 需要签名的头的key
            $signHeaderKeysStr,
            // 客户端签名
            $clientSign
            ) = $auths;

        // 授权数据
        $auth = new AuthSha256();

        try{
            // 签名时间 , 签名过期时间, 检查签名时间
            $auth->setSignTimeString($signTimeString)->setExpiredTimeOffset($expiredTimeOffset)->checkSignTime();
        }catch(\DdvPhp\DdvException\Error $e){
            throw new AuthErrorException($e->getMessage(), $e->getErrorId(), $e->getCode());
        }

        // 授权数据
        $data = $this->getAuthData($accessKeyId);
        $data = isset($data) && is_array($data) ? $data : array();
        if (empty($data)||empty($data['isAccessKeySession'])||$data['isAccessKeySession']!==true) {
            $auth = AuthModel::where('access_key_id',$accessKeyId)->first();

            if (empty($auth)||empty($auth['access_key'])) {
                throw new AuthErrorException('access_key is not exist!', 'ACCESS_KEY_NOT_EXIST', 403);
            }
            $data['isAccessKeySession'] = true;
            $data['key'] = $auth->access_key;
            $data['uid'] = $auth->uid;
            $this->saveAuthData($accessKeyId, $data);
        }
        // 会话id
        $auth->setAccessKeyId($accessKeyId)->setAccessKey($data['key']);

        //获取请求的uri
        $requestURI = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'';

        // 请求uri, 签名版本
        $auth->setMethod($this->method)->setUri($requestURI)->setAuthVersion($this->authVersion);

        // 获取签名key
        $signHeaderKeys = Sign::getHeaderKeysByStr($signHeaderKeysStr);

        // 获取签名头
        $signHeaders = $this->getSignHeaders($signHeaderKeys);

        $auth->setHeaders($signHeaders);
        // 获取签名数据
        $authArray = $auth->getAuthArray();
        //签名通过，接下来检测content_md5
        if($authArray['sign']!==$clientSign){
            $errorData = array('debugSign'=>$authArray);
            $errorData['debugSign']['sign.clien'] = $clientSign;
            $errorData['debugSign']['sign.server'] = $authArray['sign'];
            throw new AuthErrorException('Signature authentication failure', 'AUTHORIZATION_SIGNATURE_FAILURE', 403, $errorData);
        }
        $this->signInfo['sessionId'] = $accessKeyId;
        $this->signInfo['accessKeyId'] = $accessKeyId;
        AuthSignEasykeAuthV1::$accessKeyId = $accessKeyId;
        \Session::put('uid', $data['uid']);
        return true;
    }
}
