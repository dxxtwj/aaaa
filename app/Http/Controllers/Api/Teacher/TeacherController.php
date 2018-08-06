<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Middleware\SiteId;
use App\Logic\Teacher\TeacherLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class TeacherController extends Controller
{
    //获取全部列表
    public function getLists(){
        $data=[];
        $languageId = SiteId::getLanguageId();
        $data['languageId'] = $languageId;
        $data['isOn'] = 1;
        $res = TeacherLogic::getList($data);
        return $res;
    }

    //获取单条
    public function getOne(){
        $this->verify(
            [
                'teacherId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = TeacherLogic::getTeacher($this->verifyData['teacherId'],$languageId);
        return ['data'=>$res];
    }

    //获取推荐列表
    public function getRecommend()
    {
        $this->verify(
            [
                'number' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = TeacherLogic::recommend($this->verifyData['number'],$languageId);
        return ['lists'=>$res];
    }

    //获取课程老师
    public function getTeacherByClass()
    {
        $this->verify(
            [
                'classId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = TeacherLogic::getTeacherByClass($this->verifyData['classId'],$languageId);
        return ['lists'=>$res];
    }

}
