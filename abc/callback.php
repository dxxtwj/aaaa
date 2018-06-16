<?php

// require_once 'function.php';
require_once "Connect2.1/qqConnectAPI.php";

// 自己总结: echo $_GET['code'];当用户扫码或者登录了QQ  就会获得code
// echo $_GET['code'];
// 请求accesstoken  接收token
$oauth = new Oauth();

// 自己总结 $accesstoken 是没有用的，这个会被重定向走 所以打印也是打印不了的  我们主要拿 $openid
$accesstoken = $oauth->qq_callback();
$openid = $oauth->get_openid();

// 存进cookie
setcookie('qq_accesstoken', $accesstoken, time()+86400);
setcookie('qq_openid', $openid, time()+86400);

// 查看cookie
// echo $_COOKIE["qq_accesstoken"].'<br>';
// echo $_COOKIE["qq_openid"];
header('Location: http://www.wjgo.top/Home/Index/index.html');