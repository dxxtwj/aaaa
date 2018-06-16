<?php
namespace Admin\Controller;

/****************自定义函数库*********************/
/*
	 * 记录登录IP值
	 * @Author: Drizzle           2017-11-16
	 * @E-mail: Drizzle88@163.com
	 * @return  登录IP
	 */
    function getip()
    {
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $online_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
            $online_ip = $_SERVER['HTTP_CLIENT_IP'];
        }else{
            $online_ip = $_SERVER['REMOTE_ADDR'];
        }
            return $online_ip;  
    }

    