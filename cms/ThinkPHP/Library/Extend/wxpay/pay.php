<?php
    session_start();
    header('Content-Type:text/html;charset=utf-8');
    include_once("./WxPayPubHelperpp/WxPayPubHelper.php");
    // var_dump($_GET);

    //使用jsapi接口
    $jsApi = new JsApi_pub();

    //=========步骤1：网页授权获取用户openid============
    
    $out_trade_no = $_POST['WIDout_trade_no'];
    $money = $_POST['WIDtotal_fee'];
    $openid = $_POST['o'];
    $good_info = $_POST['WIDsubject'];
    // $uid=$_GET['u']

    //=========步骤2：使用统一支付接口，获取prepay_id============
    //使用统一支付接口
    $unifiedOrder = new UnifiedOrder_pub();
    //设置统一支付接口参数
    //设置必填参数
    //appid已填,商户无需重复填写
    //mch_id已填,商户无需重复填写
    //noncestr已填,商户无需重复填写
    //spbill_create_ip已填,商户无需重复填写
    //sign已填,商户无需重复填写
    $unifiedOrder->setParameter("openid","$openid");//商品描述
    $unifiedOrder->setParameter("body",$_POST['WIDsubject']);//商品描述
    //自定义订单号，此处仅作举例
    $timeStamp = time();
    // $out_trade_no = WxPayConf_pub::APPID."$timeStamp";
    $unifiedOrder->setParameter("out_trade_no",$out_trade_no);//商户订单号 
    $unifiedOrder->setParameter("total_fee",$money*100);//总金额
    $unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 
    $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
    //非必填参数，商户可根据实际情况选填
    //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
    //$unifiedOrder->setParameter("device_info","XXXX");//设备号 
    //$unifiedOrder->setParameter("attach","XXXX");//附加数据 
    //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
    //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
    //$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
    //$unifiedOrder->setParameter("openid","XXXX");//用户标识
    //$unifiedOrder->setParameter("product_id","XXXX");//商品ID

    $prepay_id = $unifiedOrder->getPrepayId();
    //=========步骤3：使用jsapi调起支付============
    $jsApi->setPrepayId($prepay_id);

    $jsApiParameters = $jsApi->getParameters();
    //echo $jsApiParameters;
?>


<script type="text/javascript">

    //调用微信JS api 支付
    function jsApiCall()
    {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            <?php echo $jsApiParameters; ?>,
            function(res){
                // WeixinJSBridge.log(res.err_msg);
                // alert(res.err_code+res.err_desc+res.err_msg);
                if(res.err_msg == "get_brand_wcpay_request:ok"){
                    // window.location.href="/Application/Home/Mobile/MyOrder/myorder.html?loca=deliver";
                    window.location.href="/Application/Home/Mobile/Altogether/pay_success.html";
                }
            }
        );
    }

    function callpay()
    {
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        }else{
            jsApiCall();
        }
    }
</script>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
        <title>微信安全支付</title>
        <link rel="stylesheet" type="text/css" href="/Public/home/css/mui.css"/>
        <link rel="stylesheet" type="text/css" href="/Public/home/css/app.css" />
        <style type="text/css">
        body{
            font-family:微软雅黑;
        }
        .mui-content{
            text-align: center;
            margin:13px 0 0 0;
        }
        .textZF{
            display: inline-block;
            padding:10px 0;
        }
        .queZF{
            background: #1AAD19;
            padding:12px 0;
            display:inline-block;
            color:#fff;
            margin:8px 0 0 0;
            border:none;
            font-size:18px;
            margin:0 12px;
        }
        .solid{
            padding:15px 8px;
            margin:15px 0;
            background:#fff;
        }
        h2{
            font-size:40px;
            border-bottom: 1px solid #eee;
            margin-bottom: 0;
            margin-top:18px;
            padding-bottom: 10px;
        }
        h4{
            padding:0;
            margin:0;
        }
        .mui-pull-left{
            float:left;
            color:#777;
        }
        .mui-pull-right{
        margin-left: 80px;
        text-align:right;
        }
        .mui-block{
            text-decoration: none;
            width:90%;
            border-radius: 6px;
        }
        .mui-row2{
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
            padding-top:0;
        }
    </style>
    </head>
    <body>

        <div class="mui-content">
            <div class="textZF">订单编号:<?php echo $_POST['WIDout_trade_no']; ?></div>
            <h2>￥<span><?php echo $_POST['WIDtotal_fee']; ?></span></h2>
            <div class="solid mui-row">
                <div class="mui-pull-left">收款方</div>
                <div class="mui-pull-right">佛山市油富共享新能源有限公司</div>
            </div>
            <div class="solid mui-row mui-row2">
                <div class="mui-pull-left">商品</div>
                <div class="mui-pull-right"><?php echo $_POST['WIDsubject']; ?></div>
            </div>
            <a href="javascript:callpay();" class="mui-btn queZF mui-block">立即支付</a>
        </div>
        
        <script type="text/javascript" src="/Public/home/js/jquery.js"></script>
        <script src="/Public/home/js/mui.min.js" type="text/javascript" charset="utf-8"></script>
       
    </body>
</html>