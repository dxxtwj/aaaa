<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\OurMessage;

use App\Model\V0\OurMessage\OurmessageModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class OurMessageLogic
{

    //添加主表
    public static function addOurMessage ($data=[])
    {
        $model = new OurmessageModel();
        $model->setDataByHumpArray($data)->save();
        return $model;

    }


    //获取列表
    public static function getOurMessageLists($data)
    {
        //站点
        $name=[];
        $where=[];
        if(isset($data['siteId'])){
            $name='our_message.site_id';
            $where=$data['siteId'];
        }
        //分类
        $name1=[];
        $where1=[];
        if(isset($data['messageCateId'])){
            $name1='our_message.message_cate_id';
            $where1=$data['messageCateId'];
        }
        //查看状态搜索
        $name2=[];
        $where2=[];
        if(isset($data['isSee'])){
            $name2='is_see';
            $where2=$data['isSee'];
        }
        if (isset($data['ourMessagePerson'])) {
            $messagePerson = '%' . $data['ourMessagePerson'] . '%';
        } else {
            $messagePerson = '%';
        }
        $messageLists = OurmessageModel::where($name,$where)
            ->where($name1,$where1)
            ->where($name2,$where2)
            ->where('our_message_person', 'like', $messagePerson)
            ->orderby('created_at','DESC')
            ->leftjoin('our_message_category', 'our_message.message_cate_id', '=', 'our_message_category.message_cate_id')
            ->leftjoin('site', 'our_message.site_id', '=', 'site.site_id')
            ->select(['our_message.*','our_message_category.message_cate_name','site.site_name']);
        return $messageLists->getDdvPageHumpArray(true);
    }

    //查单条
    public static function getOurMessage($ourMessageId)
    {
        $message = OurmessageModel::where('our_message_id', $ourMessageId)
            ->leftjoin('our_message_category', 'our_message.message_cate_id', '=', 'our_message_category.message_cate_id')
            ->leftjoin('site', 'our_message.site_id', '=', 'site.site_id')
            ->firstHump(['our_message.*','our_message_category.message_cate_name','site.site_name']);
        //改变状态--已查看
        self::editOurMessage($ourMessageId);
        return $message;
    }

    //改变状态
    public static function editOurMessage ($ourMessageId)
    {
        $data['isSee']=1;
        OurmessageModel::where('our_message_id', $ourMessageId)->updateByHump($data);
    }


    //删除主
    public static function deleteOurMessage($ourMessageId)
    {
        (new OurmessageModel())->where('our_message_id', $ourMessageId)->delete();
    }




}