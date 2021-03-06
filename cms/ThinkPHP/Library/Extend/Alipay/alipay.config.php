<?php
/* *
 * 配置文件
 * 版本：3.5
 * 日期：2016-06-25
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 * 安全校验码查看时，输入支付密码后，页面呈灰色的现象，怎么办？
 * 解决方法：
 * 1、检查浏览器配置，不让浏览器做弹框屏蔽设置
 * 2、更换浏览器或电脑，重新登录查询。
 */
 
//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner
$alipay_config['partner']		= '2088721067703333';

//收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
$alipay_config['seller_id']	= $alipay_config['partner'];

//商户的私钥,此处填写原始私钥去头去尾，RSA公私钥生成：https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.nBDxfy&treeId=58&articleId=103242&docType=1
$alipay_config['private_key']	= 'MIICWwIBAAKBgQC8fTCIkq/6x6nShiNhZu6phwCa1sOwVPrKEceD69TviMAhxkzBXMkOb4e4ow5CQOrN/QjKHc0gDzANFA6LyVHS5xfwzmJvzEwSrVXXB3gdHwN+gcsimppkmmxmG/8LEA4TtAznR19Ka1ZR2Y9/6NQfiavDAx/pBdGUGJZ4lqmMvwIDAQABAoGAUYuTsLJNgiBZXyP7MIt4m001vMdHOAU7zqosd+1SXSL7hGEnGcyLizxDKmUYqR8utllPMSn9cW5AqpfPxzgctnR/r088NXy1hiNNid/aiZfLbnWjW8vLPWPkrOYRG3Vg1CYOToD04oYLUB4pg+zD2Xpb/yPPJroxzOlqDsOWDekCQQDetm/wkzsxhMrW/0MGh4/2zLhGi0ueZKA1pCkIc7jxSycfKbIgAs6VCH8dmY/R8Ck38b20TfI7hdrdey7HJcy1AkEA2KlEj/KJ4B8LTJgi5L0AKnPz5k5RhElCWO99WOe4FvCPpZidITDg09KOtIrKI0xjxGi4B7tlSMmmgwOYNC5QIwJAGFJKFTau7zB2SlZkBnGFeklPEn0hy/FnQZRk2sRwfQFdC10tU6uazdG4uA3OE/orNi3TLr6YZoGuriNGr37kRQJAViSOzxDmwsZ1DqIalYmT7E0n9zTCcm9SQtwNoXCYH/7LjlKsfsrZST8ZYC0ii4BO1oOwT9qxPYx3ePXP6UdUswJAYvv0nNdMOIos0SUOHiBvBrFYV3h72H8Nz3JXsoN25BOb6bXVLXeVhNLkd94OdVFuB8+oaodT5X4WhCzkg2G6qw==';


//支付宝的公钥，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner
$alipay_config['alipay_public_key']= 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB';

// 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
$alipay_config['notify_url'] = "http://".$_SERVER['SERVER_NAME']."/Application/Home/Mobile/Order/alipay/notify_url.php";

// 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
$alipay_config['return_url'] = "http://".$_SERVER['SERVER_NAME']."/Application/Home/Mobile/Order/alipay/return_url.php";

//签名方式
$alipay_config['sign_type']    = strtoupper('RSA');

//字符编码格式 目前支持utf-8
$alipay_config['input_charset']= strtolower('utf-8');

//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中
$alipay_config['cacert']    = getcwd().'\\cacert.pem';

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$alipay_config['transport']    = 'http';

// 支付类型 ，无需修改
$alipay_config['payment_type'] = "1";
		
// 产品类型，无需修改
$alipay_config['service'] = "alipay.wap.create.direct.pay.by.user";

//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑


?>