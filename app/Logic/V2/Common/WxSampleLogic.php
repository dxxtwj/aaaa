<?php
/**
 * wechat php test
 * update time: 20141008
 */
namespace App\Logic\V2\Common;


use App\Model\Subscriber\OauthKeyModel;
use App\Model\Subscriber\ShareTokenModel;
use App\Model\Subscriber\WechatTokenModel;

class WxSampleLogic
{
    /*public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)){

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $RX_TYPE = trim($postObj->MsgType);

            if(!empty( $keyword )){
                switch ($RX_TYPE){
                    case 'text':
                        // $this->texts($fromUsername,$toUsername,$time,'你好');
                        $this->news($postObj);
                        break;
                    case 'event':
                        $Event = $postObj->Event;
                        $this->checkEvent($postObj,$Event);
                        break;
                    default:
                        # code...
                        break;
                }
            }else{
                echo "Input something...";
            }

        }else {
            echo "";
            exit;
        }
    }
    // 普通消息回复
    private function texts($fromUsername,$toUsername,$time,$val){
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>0</FuncFlag>
                    </xml>";

        $resultStr = sprintf($textTpl,$fromUsername, $toUsername, $time,$val);
        echo $resultStr;
    }
    private function checkEvent($postObj,$Event){
        switch ($Event) {
            case 'subscribe':
                $this->news($postObj);
                break;

            default:
                # code...
                break;
        }
    }
    //图文消息
    private function news($postObj){
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>2</ArticleCount>
                    <Articles>
                    <item>
                    <Title><![CDATA[%s]]></Title> 
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                    </item>
                    <item>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                    </item>
                    </Articles>
                    </xml>";
        $title1 = '我是图文1';
        $desc1 = '测试图文1';
        $img1 = 'http://pic.baike.soso.com/p/20130716/20130716232921-1939355802.jpg';
        $url1 = 'www.baidu.com';

        $title2 = '我是图文2';
        $desc2 = '测试图文2';
        $img2 = 'http://www.qqzhuangban.com/uploadfile/2014/07/1/20140720035650616.jpg';
        $url2 = 'http://www.yzmedu.com';
        $time = time();
        $resultStr = sprintf($textTpl,$postObj->FromUserName,$postObj->ToUserName,$time,$title1,$desc1,$img1,$url1,$title2,$desc2,$img2,$url2);
        echo $resultStr;
    }
    //验证token
    private function checkSignature(){
        // 加密签名
        $signature = $_GET["signature"];
        // 时间戳
        $timestamp = $_GET["timestamp"];
        // 随机数
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // 字典排序
        sort($tmpArr, SORT_STRING);
        // 组合字符串
        $tmpStr = implode( $tmpArr );
        // sha1加密
        $tmpStr = sha1( $tmpStr );
        // 加密签名比较
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }*/

    //随机字符串
    public static function randomString($length = 5, $charset = 'abcdefghijklmnpqrstuvwxyz123456789'){
        $phrase = '';
        //把字符串分割到数组中
        $chars = str_split($charset);
        for ($i = 0; $i < $length; $i++) {
            //随机
            $phrase .= $chars[array_rand($chars)];
        }
        return $phrase;
    }
    //扫码登录--test
    public static function WechatUrl($backUrl)
    {
        $randomString=self::randomString(16);
        \Session::put(['random'=>$randomString]);
        $appId='wx8956bb1b3181f683';
        //$backUrl='http://bnj.bnwh.net/wap/page/signInWith';
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appId.'&redirect_uri='.$backUrl.'&response_type=code&scope=snsapi_userinfo&state='.$randomString;
        return $url;
    }

    public static function addOauthKey($data)
    {
        $model = new OauthKeyModel();
        $model->setDataByHumpArray($data)->save();
        return;
    }

    public static function getWechatByKey()
    {
        $key = \Session::get('random');
        $res = OauthKeyModel::where('key',$key)->firstHumpArray(['*']);
        return $res;
    }

    //存token、openid
    public static function addToken($token,$openid)
    {
        $time=time()+7000;
        $data=[
            'openid'=>$openid,
            'token'=>$token,
            'endTime'=>$time
        ];
        $model = new WechatTokenModel();
        $model->setDataByHumpArray($data)->save();
        return;
    }
    //存token、openid
    public static function getToken()
    {
        $time=time();
        $model = new WechatTokenModel();
        $res = $model->firstHumpArray();
        if(!empty($res)){
            if($time > $res['endTime']){
                self::deleteToken($res['openid']);
                return [];
            }
        }
        return $res;
    }
    public static function deleteToken($openid)
    {
        $model = new WechatTokenModel();
        $model->where('openid',$openid)->delete();
        return;
    }

    //存token、openid
    public static function addShareToken($token)
    {
        self::getTokenLists();
        $time=time()+3600;
        $data=[
            'token'=>$token,
            'endTime'=>$time
        ];
        $model = new ShareTokenModel();
        $model->setDataByHumpArray($data)->save();
        return;
    }
    //存token、openid
    public static function getShareToken()
    {
        $time=time();
        $model = new ShareTokenModel();
        $res = $model->firstHumpArray();
        if(!empty($res)){
            if($time > $res['endTime']){
                self::deleteToken($res['token']);
                return [];
            }
        }
        return $res;
    }
    public static function deleteShareToken($token)
    {
        $model = new ShareTokenModel();
        $model->where('token',$token)->delete();
        return;
    }
    public static function getTokenLists()
    {
        $model = new ShareTokenModel();
        $res = $model->count();
        if($res > 1){
            self::deleteShareTokenLists();
        }
        return;
    }
    public static function deleteShareTokenLists()
    {
        $model = new ShareTokenModel();
        $model->where('type',1)->delete();
        return;
    }


}

?>