<?php
return array(
        'DB_TYPE'       => 'mysql',             //数据库类型
        'DB_HOST'       => '192.168.32.144',    //数据库地址
        'DB_NAME'       => 'tpshop',
        'DB_USER'       => 'root',              //用户名
        'DB_PWD'        => '123456',            //密码
        'DB_PREFIX'     => 'shop_',             //表前缀
        'LOG_RECORD'    => true,                //开启错误日志
        'DB_SLQ_LOG'    => true,                //开始SQL日志
        'URL_MODEL'     => 2,                   //URL为重写模式
        'URL_CASE_INSENSITIVE'  =>  true,       //URL不区分大小写
        // 'DB_DSN'                => 'mysql:host=localhost;dbname=shop;charset=UTF8',


        /*配置邮件发送服务器*/
        'MAIL_HOST'     => 'smtp.qq.com',        /*smtp服务器的名称、smtp.126.com*/
        'MAIL_SMTPAUTH' => TRUE,                 /*启用smtp认证*/
        'MAIL_DEBUG'    => TRUE,                 /*是否开启调试模式*/
        'MAIL_USERNAME' => '571761472@qq.com',   /*邮箱名称*/
        'MAIL_FROM'     => '571761472@qq.com',   /*发件人邮箱*/
        'MAIL_FROMNAME' => '零食商城',           /*发件人昵称*/
        'MAIL_PASSWORD' => 'imxhektrmmowbddd',   /*发件人邮箱的授权码*/
        'MAIL_CHARSET'  => 'utf-8',              /*字符集*/
        'MAIL_ISHTML'   => TRUE,                 /*是否HTML格式邮件*/
        'MAIL_PORT'     => 465,                  /*邮箱服务器端口*/
        'MAIL_SECURE'   => 'ssl',                /*smtp服务器的验证方式，注意：要开启PHP中的openssl扩展*/  

        /* 数据缓存设置 */
        'DATA_CACHE_TIME'       =>  1,      // 数据缓存有效期 0表示永久缓存
        'DATA_CACHE_TYPE'       =>  'memcache',
        'MEMCACHE_HOST' => '127.0.0.1',
        'MEMCACHE_PORT' => 11211,

        // 'SHOW_PAGE_TRACE'=>TRUE,
);