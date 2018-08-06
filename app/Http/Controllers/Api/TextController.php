<?php

/*
 * 这个类是专门用于其他请求的，好比如获取时间戳
 * */
namespace App\Http\Controllers\Api;


use \App\Http\Controllers\Controller;

class TextController extends Controller
{
    /*
     * 返回当前时间戳
     */
    public function getTime() {
        date_default_timezone_set('PRC');
        $startTime = mktime(13,30,0, 4, 12, 2018);//2018.4.12
        $arr = [
            'code' => 0,
            'lists' => '',
            'errorId' => 'OK',
            'data' => array(
                'startTime' => json_encode($startTime, true),//开始时间
                'time' => json_encode(time(), true)
            ),
            'message' => '',
            'page' => '',
            'statusCode' => 200,
        ];

        return $arr;
    }

}
