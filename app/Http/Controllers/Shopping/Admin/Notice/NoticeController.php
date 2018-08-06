<?php

namespace App\Http\Controllers\Shopping\Admin\Notice;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Admin\Notice\NoticeLogic;

class NoticeController extends Controller
{

    /*
     * 添加通知
     */
    public function addNotice() {
        $this->verify(
            [
                'noticeContents' => 'no_required',//通知内容
                'noticeIsShow' => 'no_required',//是否开启 0否  1 是
                'noticeOrder' => 'no_required',//排序
            ]
            , 'POST');

        NoticeLogic::addNotice($this->verifyData);
        return;
    }
    /*
     * 修改
     */
    public function editNotice() {
        $this->verify(
            [
                'noticeId' => '',//id
                'noticeContents' => 'no_required',//通知内容
                'noticeIsShow' => 'no_required',//是否开启 0否  1 是
                'noticeOrder' => 'no_required',//排序
            ]
            , 'POST');

        NoticeLogic::editNotice($this->verifyData);
        return;
    }

    /*
     * 查询
     */
    public function showNotice() {
        $this->verify(
            [
                'noticeId' => 'no_required',//id
            ]
            , 'POST');

        $res = NoticeLogic::showNotice($this->verifyData);
        return $res;
    }
    /*
     * 删除
     */
    public function deleteNotice() {
        $this->verify(
            [
                'noticeId' => '',//id
            ]
            , 'POST');

        NoticeLogic::deleteNotice($this->verifyData['noticeId']);
        return;
    }
}
