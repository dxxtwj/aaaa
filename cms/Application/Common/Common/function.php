<?php
    //微信数据验证
    function valid(){
        // 只有第一次验证的时候，微信服务器会给我们 $echoStr 这个数据
        // 第二次用户发送数据，微信服务器是不会发送 $echoStr 给我们服务器
        $echoStr = $_GET["echostr"];

        //验证签名
        if(checkSignature()&&$echoStr){

            //这是第一次验证的时候要返回的数据
            echo $echoStr;
            exit;

        }else{

            //接收到用户发送数据
//            responseMsg();
        }
    }

    /**
     * [checkSignature 验证我们处理出来的加密字符串与微信给我们的加密字符串是否一致]
     * @return [type] [description]
     */
    function checkSignature()
    {

        //接收到微信的给我们服务器的数据
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);

        //排序
        sort($tmpArr, SORT_STRING);

        //弄成一个字符串
        $tmpStr = implode( $tmpArr );

        //加密  得到我们的加密字符串$tmpStr
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }


// 发送文本消息
    function makeText($postObj,$text){
        $fromUsername = $postObj->FromUserName;//发送消息方id
        $toUsername = $postObj->ToUserName;//接受消息方id
        $keyword = trim($postObj->Content);//用户发送的消息
        $time = time();//发送时间
         $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";             
     
            $msgType = "text";
            $contentStr = $text;
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            echo $resultStr;
      
    }

    // 发送图片消息
    function makePicture($postObj,$media_id){
      $fromUsername = $postObj->FromUserName;//发送消息方id
        $toUsername = $postObj->ToUserName;//接受消息方id
        $keyword = trim($postObj->Content);//用户发送的消息
        $time = time();//发送时间
       $textTpl = " <xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Image>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Image>
                    </xml>";             
     
            $msgType = "image";
            $media_id = $media_id;
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $media_id);
            echo $resultStr;
            exit;
      
    }

    // 图文发送$newsContent是自己定义的主文件如title，description
    function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }

        $itemTpl = "<item>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                    </item>";

        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl  =  "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>%s</ArticleCount>
                    <Articles>
                    $item_str</Articles>
                    </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        echo $result;
        exit;
    }

     //自定义菜单
    function zidingyi($access_token,$content){
        // $content = '{
 
        //  "button":[
        //      {  
        //           "type":"view",
        //           "name":"美亚商城",
        //           "url":"http://yq.forhuman.cn/Application/Home/Mobile/Index/index.html"
        //       },
        //       {
        //           "type":"view",
        //            "name":"幸运一折购",
        //            "url":"http://yq.forhuman.cn/Application/Home/Mobile/Yizhegou/zhegou.html"
        //       },
        //       {
                   
        //            "name":"会员中心",
        //             "sub_button":[
        //                {    
        //                    "type":"view",
        //                    "name":"个人中心",
        //                    "url":"http://yq.forhuman.cn/Application/Home/Mobile/Person/user.html"
        //                 },
        //                 {
        //                    "type":"view",
        //                    "name":"我的订单",
        //                    "url":"http://yq.forhuman.cn/Application/Home/Mobile/MyOrder/myorder.html"
        //                 },
        //                 {    
        //                    "type":"view",
        //                    "name":"我的一折购",
        //                    "url":"http://yq.forhuman.cn/Application/Home/Mobile/Person/index_myfaw_par.html"
        //                 },{    
        //                    "type":"view",
        //                    "name":"分享中心",
        //                    "url":"http://yq.forhuman.cn/Application/Home/Mobile/Share/index_myfaw_sharecenter.html"
        //                 },{    
        //                    "type":"click",
        //                    "name":"推广二维码",
        //                    "key":"MYQRCODE"
        //                 }]
        //       }]
        //  }';

         $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
         $res = curlPost($url,$content);
         // write_log($res.'caidan');
         return $res;
         
    }


   
    // 事件推送
    function resEvent($postObj,$key){
        //获取到用户的微信ID
        $fromUsername = $postObj->FromUserName;

        //获取到微信公众号ID
        $toUsername = $postObj->ToUserName;

        $eventTpl = '<xml>
                    <ToUserName><![CDATA['.$fromUsername.']]></ToUserName>
                    <FromUserName><![CDATA['.$toUsername.']]></FromUserName>
                    <CreateTime>'.time().'</CreateTime>
                    <MsgType><![CDATA[event]]></MsgType>
                    <Event><![CDATA[CLICK]]></Event>
                    <EventKey><![CDATA['.$key.']]></EventKey>
                    </xml>';

        echo $eventTpl;

    }


    // curl通过get方式请求
    function curlGet($url)
    {
         //curl来获取远程url数据
        $ch = curl_init();//开启curl

        //设置参数    
        //设定要获取的url的地址
        curl_setopt($ch, CURLOPT_URL, $url);
        //不能让结果直接输出到浏览器
        curl_setopt($ch,CURLOPT_HEADER,false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);//这个是重点。
        //执行curl操作
        $res = curl_exec($ch);

        //关闭curl
        curl_close($ch);
        $res = json_decode($res,true);
        return $res;
    }

   

    // curl通过post方式请求
    function curlPost($url,$data){
        // 　 $url = "http://localhost/web_services.php";
        // 　　$post_data = array ("username" => "bob","key" => "12345");
           //curl来获取远程url数据
        $ch = curl_init();//开启curl

        //设置参数    
        //设定要获取的url的地址
        curl_setopt($ch, CURLOPT_URL, $url);
        //不能让结果直接输出到浏览器
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //执行curl操作
        // post数据
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);

        $res = curl_exec($ch);

        //关闭curl
        curl_close($ch);
        // $this->write_log($res.'res');
        $res = json_decode($res,true);
        return $res;
    }

     // 将获取到的access_token写入文件
    function putaccess(){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.WX_APPID.'&secret='.WX_APPSECRET;
        $access = curlGet($url);
        $acc = $access['access_token'].','.$access['expires_in'].','.time();  //将access_token,过期时间，和当前时间戳存入文件
        file_put_contents('access_token.txt',$acc);

    }

     // 获取access_token
    function getAccess(){
        $res = file_get_contents('access_token.txt');

        $ret = explode(',',$res);  

        $time = $ret[2];    //获取当时存入的时间戳

        $old_time = $time + $ret[1];   //过期时间
        $new_time = time();            //现在的时间

        if($new_time > $old_time){
            putaccess();
            $r = getAccess();
            // $r = file_get_contents(DOMAIN_NAME.'/access_token.txt');
            $re = explode(',',$r);
            return $re[0];
        }else{
            return $ret[0];
        }
    }

    // 查询菜单
    function caidanQuery($access_token){
        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token={$access_token}";

        $res = curlGet($url);

        return $res;
    }

     // 删除菜单
    function caidanDelete($access_token){
        $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$access_token}";

        $res = curlGet($url);

        return $res;
    }

    // 写日志
   function write_log($log)
   {
       //这里是你记录调试信息的地方请自行完善以便中间调试
       
       error_log($log."\r\n", 3, "log.txt");
   }


   /**
     *  作用：生成可以获得code的url
     */
    function createOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = WX_APPID;
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = formatBizQueryParaMap($urlObj, false);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }


    /**
     *  作用：格式化参数，签名过程需要使用
     */
    function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
               $v = urlencode($v);
            }
            // $buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0) 
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    // 将xml转化为数组
    function xmlToArray($xml){ 
 
         //禁止引用外部xml实体 
        libxml_disable_entity_loader(true); 
         // 把 XML 字符串载入对象中
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA); 
         
        $val = json_decode(json_encode($xmlstring),true); 
         
        return $val; 
     
    } 
      //盐
     function salt(){
          //生成一个32位的随机字符串
          $string = 'fjdsavsz5&$#*&@*#*)(fsjalff092349G@#*(@#*())FIUSOIUfaffOIQ8082JOGJIJSD';

          for($i=0;$i<=31;$i++){

            $char .= ($i%2) ? $string[mt_rand(12,20)]:$string[mt_rand(30,40)];
          }
          
          $salt = $char;
          //返回盐、hash
          return $salt;

    }

    // 发送短信接口，传递用户名、密码、手机号、短信内容 返回r发送失败，返回i发送成功。
    function MyPost($content,$mobile)
        {
          header('Content-type:text/html;charset=UTF-8'); 
          $flag = 0; 
          $params='';//要post的数据 
          //以下信息自己填以下
          $mobile=$mobile;//手机号
          // $content=rawurlencode($content);
          $content=rawurlencode($content); //将字符串编码成 URL 专用格式。
          $argv = array( 
            'action'=>'send',       //发送任务命令
            'userid'=>'4208',     //企业ID
            'account'=>'YfgX_ihuman_prj',   //用户帐号，由系统管理员
            'password'=>'8y8F8G8x8',     //用户账号对应的密码）
            'content'=>$content,   //短信的内容，内容需要UTF-8编码
            'mobile'=>$mobile,   //必填参数。手机号码。多个以英文逗号隔开
            'sendTime'=>'',       //为空表示立即发送，定时发送格式2010-10-24 09:08:10
            'extno'=>'',          //扩展子号
          ); 
          // print_r($argv);exit;
          //构造要post的字符串 
          // echo $argv['content'];
          foreach ($argv as $key=>$value) { 
            if ($flag!=0) { 
              $params .= "&"; 
              $flag = 1; 
            } 
            $params.= $key."="; $params.= $value;// 
            $flag = 1; 
          } 
          $url = "http://139.196.204.23:8888/sms.aspx?".$params; //提交的url地址
          $con=  file_get_contents($url);  //获取信息发送后的状态
          
          return $con;
        }


    // 生成(永久)带参数的二维码，$index为用户id，$path为生成后二维码存放的路径
    function Generate_QRcode($index,$path){
          // 从文件中获取access_token
          $access_token = getAccess();

          // 生成永久二维码放入文件夹
         // for($index=1;$index<10;$index++)
            // {
                $ch = curl_init();
                @$obj->action_name = "QR_LIMIT_SCENE";
                // @$obj->action_info->scene->scene_id =$index;
                @$obj->action_info->scene->scene_id =$index;   //场景值id
                $data_string = json_encode($obj);
                $ch = curl_init('https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
                //curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string))
                );
                $out = curl_exec($ch);
                curl_close($ch);
                $json = json_decode($out,true);
                $ticket = $json['ticket'];
                $ch = curl_init('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $out = curl_exec($ch);
                file_put_contents($path, $out);
                // echo $index."jpg<br/>";
            // }
        return $index;
    }


    //生成唯一的id
    function uuid() {
          if (function_exists ( 'com_create_guid' )) {
            return com_create_guid ();
          } else {
            mt_srand ( ( double ) microtime () * 10000 ); //optional for php 4.2.0 and up.随便数播种，4.2.0以后不需要了。
            $charid = strtoupper ( md5 ( uniqid ( rand (), true ) ) ); //根据当前时间（微秒计）生成唯一id.
            $hyphen = chr ( 45 ); // "-"
            $uuid = '' . //chr(123)// "{"
            substr ( $charid, 0, 8 ) . $hyphen . substr ( $charid, 8, 4 ) . $hyphen . substr ( $charid, 12, 4 ) . $hyphen . substr ( $charid, 16, 4 ) . $hyphen . substr ( $charid, 20, 12 );
            //.chr(125);// "}"
            return $uuid;
          }
    }
    function write_adminLog(){
        if($_SESSION['Admin']['islogin']){
            if(IS_WIN){
                $path = 'C:/wamp64/www/newBackstage';
            }else{
                $path = '.';
            }
            $logFile = $path.'/Uploads/AdminLog/'.$_SESSION['Admin']['info']['SA_ID'].'.txt';

            $data['ip'] = $_SERVER['REMOTE_ADDR'];
            $address = getCity($data['ip']);
            $data['address'] = $address['region'].' '.$address['city'];
            // $data['ip'] = '127.0.0.1';
            $data['time'] = time();
            $data['name'] = $_SESSION['Admin']['info']['SA_Name'];
            $data['msg'] = "登录成功";
            // dump($data['ip']);
            // dump($data['address']);
            // exit;
            $content = serialize($data).',';
            // echo $content;
            // print_r($data);
            $success = file_put_contents($logFile,$content,FILE_APPEND);
        }
            
    }

    function getCity($ip = ''){
        if($ip == ''){
            $url = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json";
            $ip=json_decode(file_get_contents($url),true);
            $data = $ip;
        }else{
            $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
            $ip=json_decode(file_get_contents($url));   
            if((string)$ip->code=='1'){
               return false;
            }
            $data = (array)$ip->data;
        }
        
        return $data;   
    }

    function is_mobile(){ 
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte"); 
        $is_mobile = false; 
        foreach ($mobile_agents as $device) {//这里把值遍历一遍，用于查找是否有上述字符串出现过 
            if (stristr($user_agent, $device)) { //stristr 查找访客端信息是否在上述数组中，不存在即为PC端。 
              $is_mobile = true; 
              break; 
            } 
        } 
        return $is_mobile; 
    }
?>