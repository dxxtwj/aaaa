<?php

namespace App\Http\Controllers\V10\Api;

use App\Logic\V10\MessageLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class MessageController extends Controller
{
    //添加
    public function add()
    {
        $this->verify(
            [
                'name' => '',//语言名称
                'phone'=>'no_required',
                'email'=>'no_required',
                'address'=>'no_required',
                'content'=>'no_required',
                'point'=>'no_required',
            ]
            , 'POST');
        MessageLogic::add($this->verifyData);
        return;
    }

    //
    public function getTest()
    {
        $this->verify(
            [
                'address' => '',//语言名称
            ]
            , 'POST');
        $ak = 'Uj51yuoAVwc1MVZh4GQy9NsyZ8CLF9u0';
        $address = $this->verifyData['address'];
        //http://lbsyun.baidu.com/index.php?title=webapi/guide/webservice-geocoding
        $url = "http://api.map.baidu.com/geocoder/v2/?address=$address&output=json&ak=$ak";
        $json = file_get_contents($url);
        $res = json_decode($json,true);
        if($res['status']==1){
            throw new RJsonError('地址错误', 'ADDRESS_ERROR');
        }
        return $res;
        /*
          array(2) {
              ["status"]=>
              int(0)
              ["result"]=>
              array(4) {
                ["location"]=>
                array(2) {
                  ["lng"]=>
                  float(106.5899081502)
                  ["lat"]=>
                  float(29.549372821965)
                }
                ["precise"]=>
                int(0)
                ["confidence"]=>
                int(18)
                ["level"]=>
                string(6) "道路"
                }
           }*/
    }

}
