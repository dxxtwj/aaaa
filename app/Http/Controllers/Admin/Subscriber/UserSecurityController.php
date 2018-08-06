<?php

namespace App\Http\Controllers\Admin\Subscriber;

use App\Logic\Subscriber\UserSecurityLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use App\Http\Middleware\SiteId;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class UserSecurityController extends Controller
{
    /*
     *  lang[
     *      languageId          语言ID
     *      userSecurityTitle   问题标题
     *  ]
     */
    //添加
    public function AddSecurity()
    {
        $this->verify(
            [
                'lang'=> '',//
            ]
            , 'POST');
        UserSecurityLogic::add($this->verifyData);

    }

    public function getSecurity()
    {
        $data=[];
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $res = UserSecurityLogic::getSecurity($data);
        return ['data'=>$res];
    }

}
