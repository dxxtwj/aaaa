<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2017/8/8
 * Time: 下午2:58
 */

namespace App\Logic\V0\Common;
use App\Model\V0\User\CodeModel;
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
    //改版--图形验证码
    public static function getImageBase64($phrase = null)
    {
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

        $res = [
            'code'=>$phrase,
            'raw'=>$imageRaw
        ];
        \Session::put(['codeImg'=>md5($res['code'])]);
        return 'data:image/jpeg;base64,' .base64_encode ($res['raw']);
    }
    //改版--验证图形验证码
    public static function imageCode($codeImage)
    {
        $code = \Session::get('codeImg');
        if(empty($code)){
            throw new RJsonError('图形验证码已经过期', 'IMG_VERIFY_TIMEOUT');
        }
        if(md5($codeImage)!=$code){
            throw new RJsonError('图形验证码错误', 'IMG_VERIFY_ERROR');
        }
        \Session::remove('codeImg');
    }

    //修改邮箱配置
    public static function mailDeploy()
    {
        \Config::set('mail.from', array('address' => '13592957850@163.com', 'name' => 'Test'));
        \Config::set('mail.username', '13592957850@163.com');
        \Config::set('mail.password', 'czs123456');
    }
    //验证、文章使用
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
    //验证、文章、附件使用
    public static function sendEmailAnnex($toAddress, $data=[], $templateCode="emails.test", $subject = null){
        $data['subject'] = (empty($subject)&&(!empty($data['subject'])))?$data['subject']:$subject;
        if (empty($data['toAddress'])){
            $data['toAddress'] = $toAddress;
        }
        //$data['file']='http://automakesize-oss.oss-cn-shenzhen.aliyuncs.com//upload/other/ae7828D65B38045a5e313B6A09.docx';
        try{
            Mail::send($templateCode, $data, function(\Illuminate\Mail\Message $message) use ($data, $toAddress) {
                $message->to($data['toAddress'])->subject($data['subject']);
                $attachment = storage_path('app/files/Feedback.docx');
                //在邮件中上传附件
                $message->attach($attachment,['as'=>'笨鸟奖-项目申报表.docx']);
            });
        }catch (\Exception $e){
            throw new DdvException($e->getMessage(), 'EMAIL_SEND_FAIL', $e->getCode());
        }
    }

    public static function sendSmsVerify($phone, $data, $templateCode="SMS_119078155", $signName ="易思客")
    {
        //此处需要替换成自己的AK信息
        $accessKeyId = "LTAItrfu5ksGlSpK";//参考本文档步骤2
        $accessKeySecret = "ayVPKByxsl7U1UeU39F4z2Co4UBbUk";//参考本文档步骤2
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

    //旧版发送短信验证码
    public static function getCodeSend($tel,$code)
    {
        define('ENABLE_HTTP_PROXY', env('ALIYUN_SMS_ENABLE_HTTP_PROXY', false));
        define('HTTP_PROXY_IP',     env('ALIYUN_SMS_HTTP_PROXY_IP', '127.0.0.1'));
        define('HTTP_PROXY_PORT',   env('ALIYUN_SMS_HTTP_PROXY_PORT', '8888'));
        $endpoint = new Endpoint("cn-shenzhen", EndpointConfig::getregionIds(), EndpointConfig::getProducDomains());
        $endpoints = array($endpoint);
        EndpointProvider::setEndpoints($endpoints);
        $iClientProfile = DefaultProfile::getProfile("cn-shenzhen", "LTAIjA90mwujFyFy", "T2MZrz5hszKRvB8npQaGpGXzBDly7D");
        $client = new DefaultAcsClient($iClientProfile);
        $request = new SingleSendSmsRequest();
        $request->setSignName("尚瑞");                 /*签名名称*/
        $request->setTemplateCode("SMS_77180028");           /*模板code*/
        $request->setRecNum("$tel");                     /*目标手机号*/
        // $request->setParamString("{\"name\":\"sanyou\"}");/*模板变量，数字一定要转换为字符串*/
        $request->setParamString(json_encode(['code'=>"$code"]));/*模板变量，数字一定要转换为字符串*/
        // var_dump($request);die;
        try {
            $response = $client->getAcsResponse($request);
            print_r($response);
        } catch (ClientException  $e) {
            print_r($e->getErrorCode());
            print_r($e->getErrorMessage());
            $fail=1;
        } catch (ServerException  $e) {
            print_r($e->getErrorCode());
            print_r($e->getErrorMessage());
        }
        if(isset($fail)){
            throw new RJsonError('发送短信无效达到频率限制', 'SMS_LIMIT');
        }
    }
    //保存手机验证吗到数据库
    public static function addCode($phone,$code,$type)
    {
        \Session::put(['phone'=>$phone]);
        \Session::put(['codeSms'=>$code]);
        $data=[
            'phone'=>$phone,
            'code'=>$code,
            'siteId'=>1,
            'type'=>$type
        ];
        $model = new CodeModel();
        $model->setDataByHumpArray($data)->save();
        return;
    }
    //验证短信
    public static function codeSms($phone,$codeSms)
    {
        $phones = \Session::get('phone');
        if(empty($phones)){
            throw new RJsonError('请接收验证码', 'PHONE_VERIFY_TIMEOUT');
        }
        if($phones!=$phone){
            throw new RJsonError('手机号码跟接收短信的手机号码不一致', 'PHONE_VERIFY_ERROR');
        }
        $code = \Session::get('codeSms');
        if(empty($code)){
            throw new RJsonError('短信验证码已经过期', 'SMS_VERIFY_TIMEOUT');
        }
        if($codeSms!=$code){
            throw new RJsonError('短信验证码错误', 'SMS_VERIFY_ERROR');
        }
        \Session::remove('phone');
        \Session::remove('codeSms');
    }
    //邮箱验证
    public static function EmailCode($email,$emailCode)
    {
        $emails = \Session::get('email');
        if(empty($emails)){
            throw new RJsonError('请接收验证码', 'EMAIL_VERIFY_TIMEOUT');
        }
        if($emails!=$email){
            throw new RJsonError('邮箱跟接收验证码的邮箱不一致', 'EMAIL_VERIFY_ERROR');
        }
        $code = \Session::get('emailCode');
        if(empty($code)){
            throw new RJsonError('邮箱验证码已经过期', 'EMAIL_VERIFY_TIMEOUT');
        }
        if($emailCode!=$code){
            throw new RJsonError('邮箱验证码错误', 'EMAIL_VERIFY_ERROR');
        }
        \Session::remove('email');
        \Session::remove('emailCode');
    }
    //验证手机号码
    public static function verifyPhone($phone)
    {
        if(!preg_match('^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(18[0,5-9]))\\d{8}$^',$phone)){
            throw new RJsonError('手机格式不对', 'PHONE_ERROR');
        }
    }
    //验证邮箱
    public static function verifyEmail($email)
    {
        if(!preg_match('/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/ ',$email)){
            throw new RJsonError('邮箱格式不对', 'EMAIL_ERROR');
        }
    }
}