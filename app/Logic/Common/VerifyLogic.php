<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2017/8/8
 * Time: 下午2:58
 */

namespace App\Logic\Common;
use \DdvPhp\DdvException;

use Cmzz\AliyunCore\Profile\DefaultProfile;
use Cmzz\AliyunCore\DefaultAcsClient;
use Cmzz\AliyunCore\Regions\Endpoint;
use Cmzz\AliyunCore\Regions\EndpointConfig;
use Cmzz\AliyunCore\Regions\EndpointProvider;
use Cmzz\AliyunSms\Sms\Request\V20160927\SingleSendSmsRequest;
use Cmzz\AliyunCore\Exception\ClientException;
use Cmzz\AliyunCore\Exception\ServerException;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Gregwar\Captcha\CaptchaBuilder;
use \Mail;
use Mockery\Exception;

class VerifyLogic
{
    /**
     * Generates  random phrase of given length with given charset
     */
    public static function generateVerifyCode($length = 5, $charset = 'abcdefghijklmnpqrstuvwxyz123456789'){
        $phrase = '';
        //把字符串分割到数组中
        $chars = str_split($charset);

        for ($i = 0; $i < $length; $i++) {
            //随机
            $phrase .= $chars[array_rand($chars)];
        }

        return $phrase;
    }

    public static function saveAndSendSmsVerify($sessionKey, $verifyGuid, $phone = '', $data = [], $templateCode='SMS_84415005', $code = null, $signName ='斯洛柯'){
        if (empty($code)){
            $code = VerifyLogic::generateVerifyCode(4, '0123456789');
        }
        if (empty($phone)){
            throw new RJsonError('短信号码错误', 'SMS_PHONE_ERROR');
        }
        //\Session::put($sessionKey.'.sms.verify.code', md5($verifyGuid.$code.$phone));

        $data['code'] = $code;
        /*if (empty($data['product'])){
            $data['product'] = '账户';
        }*/
        $res=VerifyLogic::sendSmsVerify($phone, $data, $templateCode, $signName);
        if ($res->Code !=='OK'){
            throw new DdvException($res->Message, $res->Code, 400);
        }
    }
    public static function saveAndSendEmailVerify($sessionKey, $verifyGuid, $email = '', $data = [], $templateCode='emails.verifyCode', $code = null){
        if (empty($code)){
            $code = VerifyLogic::generateVerifyCode(4, '0123456789');
        }
        if (empty($email)){
            throw new RJsonError('邮箱地址错误', 'EMAIL_ADDRESS_ERROR');
        }
        \Session::put($sessionKey.'.email.verify.code', md5($verifyGuid.$code.$email));

        $data['code'] = $code;
        if (empty($data['product'])){
            $data['product'] = '账户';
        }
        VerifyLogic::sendEmailVerify($email, $data, $templateCode);
    }

    public static function checkEmailVerify($sessionKey, $verifyGuid, $email = '', $code){
        $emailVerifyCheck = \Session::get($sessionKey.'.email.verify.code', null);

        if (empty($emailVerifyCheck)){
            throw new RJsonError('邮箱验证码已经过期', 'EMAIL_VERIFY_TIMEOUT');
        }
        if (md5($verifyGuid.$code.$email)!==$emailVerifyCheck){
            throw new RJsonError('邮箱验证码错误', 'EMAIL_VERIFY_ERROR');
        }
    }
    public static function checkSmsVerify($sessionKey, $verifyGuid, $phone = '', $code){
        $smsVerifyCheck = \Session::get($sessionKey.'.sms.verify.code', null);

        if (empty($smsVerifyCheck)){
            throw new RJsonError('短信验证码已经过期', 'SMS_VERIFY_TIMEOUT');
        }
        if (md5($verifyGuid.$code.$phone)!==$smsVerifyCheck){
            throw new RJsonError('短信验证码错误', 'SMS_VERIFY_ERROR');
        }
    }

    public static function checkImgVerify($sessionKey, $verifyGuid, $code){
        $codeCheck = \Session::get($sessionKey.'.img.verify.code', null);

        $verifyType = null;
        if (empty($code)){
            throw new RJsonError('图形验证码已经过期', 'IMG_VERIFY_TIMEOUT');
        }
        if (md5($verifyGuid.$code)!==$codeCheck){
            throw new RJsonError('图形验证码错误', 'IMG_VERIFY_ERROR');
        }
    }

    public static function getImgVerifyBase64($sessionKey, $verifyGuid, $code){

        if (empty($code)){
            $code = VerifyLogic::generateVerifyCode(4);
        }
        $res = self::getImgVerifyRaw($code);
        \Session::put($sessionKey.'.img.verify.code', md5($verifyGuid.$res['code']));

        return 'data:image/jpeg;base64,' .base64_encode ($res['raw']);
    }

    public static function getImgVerifyRaw($phrase = null){
        $phrase = empty($phrase)?null:(string)$phrase;
        $builder = new CaptchaBuilder($phrase);
        //可以设置图片宽高及字体
        $builder->build($width = 108, $height = 40, $font = null);
        $phrase = $builder->getPhrase();
        @ob_start ();
        $image_data_old = ob_get_contents ();
        @ob_end_clean ();
        @ob_start ();

        $builder->output();
        $imageRaw = ob_get_contents ();

        @ob_end_clean ();
        echo $image_data_old;

        return [
            'code'=>$phrase,
            'raw'=>$imageRaw
        ];
    }
    public static function sendEmailVerify($toAddress, $data=[], $templateCode="emails.test", $subject = null){
        $data['subject'] = (empty($subject)&&(!empty($data['subject'])))?$data['subject']:$subject;
        if (empty($data['toAddress'])){
            $data['toAddress'] = $toAddress;
        }
        try{
            Mail::send($templateCode, $data, function(\Illuminate\Mail\Message $message) use ($data, $toAddress) {
                $message->to($data['toAddress'])->subject($data['subject']);
            });
        }catch (\Exception $e){
            throw new DdvException($e->getMessage(), 'EMAIL_SEND_FAIL', $e->getCode());
        }

    }

    public static function sendSmsVerify($phone, $data, $templateCode="SMS_84415005", $signName ="斯洛柯")
    {
        //此处需要替换成自己的AK信息
        $accessKeyId = "LTAIaQqj4rmOWsjP";//参考本文档步骤2
        $accessKeySecret = "blpvHlZ0EpECgJdcrLezF1YOckXAKD";//参考本文档步骤2
        //短信API产品名（短信产品名固定，无需修改）
        $product = "Dysmsapi";
        //短信API产品域名（接口地址固定，无需修改）
        $domain = "dysmsapi.aliyuncs.com";
        //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
        $region = "cn-shenzhen";

        define('ENABLE_HTTP_PROXY', env('ALIYUN_SMS_ENABLE_HTTP_PROXY', false));
        define('HTTP_PROXY_IP',     env('ALIYUN_SMS_HTTP_PROXY_IP', '127.0.0.1'));
        define('HTTP_PROXY_PORT',   env('ALIYUN_SMS_HTTP_PROXY_PORT', '8888'));


        $productDomains = EndpointConfig::getProducDomains();
        $productDomains[] = new \Cmzz\AliyunCore\Regions\ProductDomain($product, $domain);
        $endpoint = new Endpoint("cn-shenzhen", EndpointConfig::getregionIds(), $productDomains);
        EndpointProvider::setEndpoints([$endpoint]);
        $iClientProfile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

        $client = new DefaultAcsClient($iClientProfile);
        $request = new \Aliyun\Api\Sms\Request\V20170525\SendSmsRequest();
        $request->setSignName($signName);                 /*签名名称*/
        $request->setTemplateCode($templateCode);           /*模板code*/
        $request->setPhoneNumbers("$phone");                     /*目标手机号*/
        // $request->setParamString("{\"name\":\"sanyou\"}");/*模板变量，数字一定要转换为字符串*/
        $request->setTemplateParam(json_encode($data));/*模板变量，数字一定要转换为字符串*/

        try {
            $response = $client->getAcsResponse($request);
            return $response;
        } catch (ServerException  $e) {
            throw new DdvException($e->getErrorMessage(), $e->getErrorCode(), $e->getCode());
        } catch (ClientException  $e) {
            throw new DdvException($e->getErrorMessage(), $e->getErrorCode(), $e->getCode());
        } catch (\Exception  $e) {
            throw new DdvException($e->getMessage(), 'SMS_SEND_FAIL', $e->getCode());
        }
    }
}