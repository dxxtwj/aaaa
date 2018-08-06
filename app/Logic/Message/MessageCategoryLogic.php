<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Message;

use App\Model\Message\MessageCategoryModel;
use App\Model\Message\MessageCategoryDescModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class MessageCategoryLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $main=[
            'isOn'=>$data['isOn'],
            'sort'=>$data['sort']
        ];
        self::addAffair($main,$data);
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $messageCateId = self::addMessageCate($main);
            foreach ($data['lang'] as $key=>$value ){
                $desc=[
                    'messageCateId' => $messageCateId,
                    'messageCateTitle' => $value['messageCateTitle'],
                    'languageId'=>$value['languageId'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::addMessageCateDesc($desc);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //添加主表
    public static function addMessageCate ($data=[])
    {
        $model = new MessageCategoryModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //添加详细表
    public static function addMessageCateDesc ($data=[])
    {
        $model = new MessageCategoryDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //获取列表
    public static function getMessageCateLists($data)
    {
        $name=[];
        $where=[];
        if(isset($data['languageId'])){
            $name='message_category_description.language_id';
            $where=$data['languageId'];
        }
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='message_category.is_on';
            $show=$data['isOn'];
        }
        $messageLists = MessageCategoryModel::whereSiteId()
            ->where($name,$where)
            ->where($showName,$show)
            ->orderby('message_category.sort','DESC')
            ->leftjoin('message_category_description', 'message_category.message_cate_id', '=', 'message_category_description.message_cate_id')
            ->getHumpArray([
                'message_category.*',
                'message_category_description.*'
            ]);
        return $messageLists;
    }

    //查单条
    public static function getMessageCateOne($messageCateId)
    {
        $message = MessageCategoryModel::where('message_cate_id', $messageCateId)
            ->firstHump(['*']);
        if(isset($message)){
            $messageCateDesc = self::getMessageCateDesc($message['messageCateId']);
            $message['lang']=empty($messageCateDesc) ? '[]' : $messageCateDesc;
        }

        return $message;
    }

    //获取详情全部
    public static function getMessageCateDesc($messageCateId)
    {
        $model = new MessageCategoryDescModel();
        $messageCateDesc = $model->where('message_cate_id', $messageCateId)->getHump(['*']);
        return $messageCateDesc;
    }

    //获取详情全部
    public static function getMessageCateById($messageCateId)
    {
        $model = new MessageCategoryDescModel();
        $messageCateDesc = $model->where('message_cate_id', $messageCateId)->firstHumpArray(['*']);
        return $messageCateDesc;
    }


    //编辑全部
    public static function editAll ($data=[])
    {
        $messageCateId=$data['messageCateId'];
        $main=[
            'sort' => $data['sort'],
            'isOn' => $data['isOn']
        ];
        self::editAffair($main,$data);
        //self::editMessageCate($main,$messageCateId);
        /*foreach ($data['lang'] as $key=>$value){
            $desc=[
                'messageCateTitle' => empty($value['messageCateTitle']) ? '' : $value['messageCateTitle'],
                'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
            ];
            self::editMessageCateDesc($desc,$messageCateId,$value['languageId']);
        }*/
    }

    //修改事务
    public static function editAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $messageCateId=$data['messageCateId'];
            self::editMessageCate($main,$messageCateId);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'messageCateTitle' => empty($value['messageCateTitle']) ? '' : $value['messageCateTitle'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::editMessageCateDesc($desc,$messageCateId,$value['languageId']);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //编辑主表
    public static function editMessageCate($data=[],$messageCateId)
    {
        MessageCategoryModel::where('message_cate_id', $messageCateId)->updateByHump($data);
    }

    //是否显示
    public static function isShow($data=[],$messageCateId)
    {
        MessageCategoryModel::where('message_cate_id', $messageCateId)->updateByHump($data);
    }

    //编辑主表
    public static function editMessageCateDesc($data=[],$messageCateId,$languageId)
    {
        MessageCategoryDescModel::where('message_cate_id', $messageCateId)->where('language_id',$languageId)->updateByHump($data);
    }

    //删除事务
    public static function delAffair($messageCateId)
    {
        \DB::beginTransaction();
        try{
            self::deleteMessageCate($messageCateId);
            self::deleteMessageCateDesc($messageCateId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //删除主
    public static function deleteMessageCate($messageCateId)
    {
        (new MessageCategoryModel())->where('message_cate_id', $messageCateId)->delete();
    }

    //删除详
    public static function deleteMessageCateDesc($messageId)
    {
        (new MessageCategoryDescModel())->where('message_cate_id', $messageId)->delete();
    }




}