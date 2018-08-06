<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Message;

use App\Logic\Message\MessageCategoryLogic;
use App\Model\Message\MessageModel;
use App\Model\Message\MessageDescModel;
use App\Logic\Subscriber\LoginLogic;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class MessageLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $main=[
            'messageCateId'=>empty($data['messageCateId']) ? 0 : $data['messageCateId'],
            'productId'=>empty($data['productId']) ? 0 : $data['productId'],
            'newsId'=>empty($data['newsId']) ? 0 : $data['newsId'],
            'uid'=>empty($data['uid']) ? 0 : $data['uid'],
        ];
        self::addAffair($main,$data);
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $messageId=self::addMessage($main);
            $desc=[
                'messageId' => $messageId,
                'languageId'=>$data['languageId'],
                'sex'=>empty($data['sex']) ? 0 : $data['sex'],
                //'productId'=>empty($data['productId']) ? 0 : $data['productId'],
                'declare'=>empty($data['declare']) ? '' : $data['declare'],
                'declareType'=>empty($data['declareType']) ? 1 : $data['declareType'],
                'declareCategory'=>empty($data['declareCategory']) ? 1 : $data['declareCategory'],
                'position'=>empty($data['position']) ? '' : $data['position'],
                'messagePerson'=>empty($data['messagePerson']) ? '' : $data['messagePerson'],
                'messagePhone'=>empty($data['messagePhone']) ? '' : $data['messagePhone'],
                'messageEmial'=>empty($data['messageEmial']) ? '' : $data['messageEmial'],
                'messageCompany' => empty($data['messageCompany']) ? '' : $data['messageCompany'],
                'messageAddress' => empty($data['messageAddress']) ? '' : $data['messageAddress'],
                'messageContent' => empty($data['messageContent']) ? '' : $data['messageContent'],
                'messageTime' => empty($data['messageTime']) ? '' : $data['messageTime'],
                'messageCourse' => empty($data['messageCourse']) ? '' : $data['messageCourse'],
            ];
            self::addMessageDesc($desc);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //用户留言--要登录
    public static function addUserMessage ($data=[])
    {
        $uid=LoginLogic::getUid();
        $mian=[
            'newsId'=>empty($data['newsId']) ? 0 : $data['newsId'],
            'uid'=>$uid
        ];
        $messageId=self::addMessage($mian);
        $desc=[
            'messageId' => $messageId,
            'languageId'=>$data['languageId'],
            'messagePerson'=>$data['messagePerson'],
            'sex'=>empty($data['sex']) ? 0 : $data['sex'],
            'declare'=>empty($data['declare']) ? '' : $data['declare'],
            'declareType'=>empty($data['declareType']) ? 1 : $data['declareType'],
            'declareCategory'=>empty($data['declareCategory']) ? 1 : $data['declareCategory'],
            'position'=>empty($data['position']) ? '' : $data['position'],
            'messagePhone'=>empty($data['messagePhone'])? '' : $data['messagePhone'],
            'messageEmial'=>empty($data['messageEmial']) ? '' : $data['messageEmial'],
            'messageCompany' => empty($data['messageCompany']) ? '' : $data['messageCompany'],
            'messageAddress' => empty($data['messageAddress']) ? '' : $data['messageAddress'],
            'messageContent' => empty($data['messageContent']) ? '' : $data['messageContent'],
        ];
        self::addMessageDesc($desc);
    }

    //添加主表
    public static function addMessage ($data=[])
    {
        $model = new MessageModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //添加详细表
    public static function addMessageDesc ($data=[])
    {
        $model = new MessageDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //获取列表
    public static function getMessageLists($data)
    {
        $name=[];
        $where=[];
        if(isset($data['languageId'])){
            $name='message_description.language_id';
            $where=$data['languageId'];
        }
        //分类
        $cateName=[];
        $messageCateId=[];
        if(isset($data['messageCateId'])){
            $cateName='message.message_cate_id';
            $messageCateId=$data['messageCateId'];
        }
        if (isset($data['messagePerson'])) {
            $messagePerson = '%' . $data['messagePerson'] . '%';
        } else {
            $messagePerson = '%';
        }
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='message.is_on';
            $show=$data['isOn'];
        }
        $messageLists = MessageModel::whereSiteId()
            ->where($name,$where)
            ->where($showName,$show)
            ->where($cateName,$messageCateId)
            ->where('message_description.message_person', 'like', $messagePerson)
            ->orderby('message.created_at','DESC')
            ->leftjoin('message_description', 'message.message_id', '=', 'message_description.message_id')
            ->select([
                'message.*',
                'message_description.*',
            ]);
        $message = $messageLists->getDdvPageHumpArray(true);
        if(!empty($message['lists'])){
            foreach ($message['lists'] as $key=>$value){
                //获取分类
                $res = MessageCategoryLogic::getMessageCateById($value['messageCateId']);
                $message['lists'][$key]['messageCateTitle']=$res['messageCateTitle'] ?? '';
            }
        }
        return $message;
    }

    //查单条
    public static function getMessage($messageId)
    {
        $banner = MessageModel::where('message.message_id', $messageId)
            ->leftjoin('message_description', 'message.message_id', '=', 'message_description.message_id')
            ->firstHump([
                'message.*',
                'message_description.*',
            ]);

        return $banner;
    }


    //编辑主表
    public static function editMessage($data=[],$messageId)
    {
        MessageModel::where('message_id', $messageId)->updateByHump($data);
    }

    //是否显现
    public static function isShow($data=[],$messageId)
    {
        MessageModel::where('message_id', $messageId)->updateByHump($data);
    }

    //删除事务
    public static function delAffair($messageId)
    {
        \DB::beginTransaction();
        try{
            self::deleteMessage($messageId);
            self::deleteMessageDesc($messageId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //删除主
    public static function deleteMessage($messageId)
    {
        (new MessageModel())->where('message_id', $messageId)->delete();
    }
    //删除详
    public static function deleteMessageDesc($messageId)
    {
        (new MessageDescModel())->where('message_id', $messageId)->delete();
    }




}