<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Admin\Notice;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Notice\NoticeModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;

use Illuminate\Database\QueryException;

class NoticeLogic extends ShoppingLogic
{
   /*
   * 添加通知
   */
   public static function addNotice($data) {

       $noticeModel = new NoticeModel();
       $bool = $noticeModel->setDataByHumpArray($data)->save();

       if (!$bool) {

           throw new RJsonError('添加通知失败', 'NOTICE_ERROR');
       }
   }

   /*
    * 修改
    */
   public static function editNotice($data) {

       $noticeModel = new NoticeModel();
       $update['notice_contents'] = $data['noticeContents'];
       $update['notice_order'] = $data['noticeOrder'];
       $update['notice_is_show'] = $data['noticeIsShow'];
       $bool = $noticeModel->where('notice_id', $data['noticeId'])->updateByHump($update);

       if (!$bool) {

           throw new RJsonError('修改通知失败', 'NOTICE_ERROR');
       }
   }

   /*
    *  查询
    */
   public static function showNotice($data=array()) {

       $bool = false;
       if (empty($data)) {

           $noticeModel = new NoticeModel();
           $noticeData = $noticeModel->orderBy('notice_order', 'DESC')->getDdvPageHumpArray();

       } else {
           $noticeModel = new NoticeModel();
           $noticeData = $noticeModel->orderBy('notice_order', 'DESC')->where('notice_id', $data['noticeId'])->firstHumpArray();
           $bool = true;
       }

       if (!$noticeData) {
           throw new RJsonError('暂无数据', 'NOTICE_ERROR');

       }

       if ($bool) {

           return ['data' => $noticeData];
       }
       return $noticeData;
   }

   /*
    * 删除
    */
   public static function deleteNotice($id) {
       $noticeModel = new NoticeModel();
       $bool = $noticeModel->where('notice_id', $id)->delete();

       if (!$bool) {
           throw new RJsonError('删除通知失败', 'NOTICE_ERROR');

       }
   }
}