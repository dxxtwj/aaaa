<?php
// 1. 判断PHP版本是否大于5.3
if (PHP_VERSION < '5.3') die('版本太低');

// 2. 定义项目目录
define('APP_PATH','./Shop/');

//开启调试模式
define('APP_DEBUG', true);

// define('SHOW_PAGE_TRACE' = false);

// 3. 包含主入口文件
require './ThinkPHP/ThinkPHP.php';