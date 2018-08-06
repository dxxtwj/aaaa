<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\SiteAllocation;

use App\Model\V0\SiteAllocation\AllocationProductModel;
use App\Model\V0\SiteAllocation\AllocationNewsModel;
use App\Model\V0\SiteAllocation\AllocationCasesModel;
use App\Model\V0\SiteAllocation\AllocationLinkModel;
use App\Model\V0\SiteAllocation\AllocationMessageModel;
use App\Model\V0\SiteAllocation\AllocationProLangModel;
use App\Model\V0\SiteAllocation\AllocationNewsLangModel;
use App\Model\V0\SiteAllocation\AllocationCasesLangModel;
use App\Model\V0\SiteAllocation\AllocationLinkLangModel;
use App\Model\V0\SiteAllocation\AllocationMessageLangModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class SiteAllocationLogic
{

    /*
     * 添加主表
     * */
    public static function add($data=[])
    {
        $siteId=$data['siteId'];
        if(isset($data['allocationPro'])){
            //查是否添加过
            $res = self::getProBySiteId($siteId);
            $tableId=empty($res['tableId']) ? '' : $res['tableId'];
            foreach ($data['allocationPro'] as $key=>$item) {
                if(!empty($tableId)){
                    $tableId=$tableId+1;
                    $pro=[
                        'tableId'=>$tableId,
                        'siteId'=>$siteId,
                        'languageId'=>0,
                        //'productTypeName'=>$value['productTypeName'],
                        'isOn'=>1
                    ];
                    self::product($pro,$item['lang']);
                }else{
                    $pro=[
                        'tableId'=>$key+1,
                        'siteId'=>$siteId,
                        'languageId'=>0,
                        //'productTypeName'=>$value['productTypeName'],
                        'isOn'=>1
                    ];
                    self::product($pro,$item['lang']);
                }

            }
        }
        if(isset($data['allocationNews'])){
            //查是否添加过
            $res = self::getNewsBySiteId($siteId);
            $tableId=empty($res['tableId']) ? '' : $res['tableId'];
            foreach ($data['allocationNews'] as $key2=>$item) {
                if(!empty($tableId)){
                    $tableId=$tableId+1;
                    $pro=[
                        'tableId'=>$tableId,
                        'siteId'=>$siteId,
                        //'newsTypeName'=>$item['newsTypeName'],
                        'isOn'=>1
                    ];
                    //事务
                    self::news($pro,$item['lang']);
                }else{
                    $pro=[
                        'tableId'=>$key2+1,
                        'siteId'=>$siteId,
                        //'newsTypeName'=>$item['newsTypeName'],
                        'isOn'=>1
                    ];
                    //事务
                    self::news($pro,$item['lang']);
                }
            }
        }
        if(isset($data['allocationCases'])){
            //查是否添加过
            $res = self::getCasesBySiteId($siteId);
            $tableId=empty($res['tableId']) ? '' : $res['tableId'];
            foreach ($data['allocationCases'] as $key3=>$item) {
                if(!empty($tableId)){
                    $tableId=$tableId+1;
                    $pro=[
                        'tableId'=>$tableId,
                        'siteId'=>$siteId,
                        //'casesTypeName'=>$item['casesTypeName'],
                        'isOn'=>1
                    ];
                    self::cases($pro,$item['lang']);

                }else{
                    $pro=[
                        'tableId'=>$key3+1,
                        'siteId'=>$siteId,
                        //'casesTypeName'=>$item['casesTypeName'],
                        'isOn'=>1
                    ];
                    self::cases($pro,$item['lang']);
                }
            }
        }
        if(isset($data['allocationLink'])){
            //查是否添加过
            $res = self::getLinkBySiteId($siteId);
            $tableId=empty($res['tableId']) ? '' : $res['tableId'];
            foreach ($data['allocationLink'] as $key4=>$item) {
                if(!empty($tableId)){
                    $tableId=$tableId+1;
                    $pro=[
                        'tableId'=>$tableId,
                        'siteId'=>$siteId,
                        //'linkName'=>$item['linkName'],
                        'isOn'=>1
                    ];
                    self::link($pro,$item['lang']);
                }else{
                    $pro=[
                        'tableId'=>$key4+1,
                        'siteId'=>$siteId,
                        //'linkName'=>$item['linkName'],
                        'isOn'=>1
                    ];
                    self::link($pro,$item['lang']);
                }
            }
        }
        if(isset($data['allocationMessage'])){
            //查是否添加过
            $res = self::getMessageBySiteId($siteId);
            $tableId=empty($res['tableId']) ? '' : $res['tableId'];
            foreach ($data['allocationMessage'] as $key5=>$item) {
                if(!empty($tableId)){
                    $tableId=$tableId+1;
                    $pro=[
                        'tableId'=>$tableId,
                        'siteId'=>$siteId,
                        //'messageName'=>$item['messageName'],
                        'isOn'=>1
                    ];
                    self::message($pro,$item['lang']);
                }else{
                    $pro=[
                        'tableId'=>$key5+1,
                        'siteId'=>$siteId,
                        //'messageName'=>$item['messageName'],
                        'isOn'=>1
                    ];
                }
                self::message($pro,$item['lang']);
            }
        }

    }
    /*
     * 产品事务
     * */
    public static function product($product,$lang)
    {
        \DB::beginTransaction();
        try{
            $allocationProId = self::addPro($product);
            foreach ($lang as $value){
                $proLang=[
                    'allocationProId'=>$allocationProId,
                    'languageId'=>$value['languageId'],
                    'productTypeName'=>$value['productTypeName'],
                ];
                self::addProLang($proLang);
            }
            \DB::commit();
        }catch(QueryException $e){
            throw new RJsonError('添加失败', 'ADD_FILE');
            \DB::rollBack();
            return false;
        }
        return true;
    }
    /*
     * 产品配置添加
     * */
    public static function increaseProduct($data)
    {
        \DB::beginTransaction();
        try{
            $main=[
                'siteId'=>$data['siteId'],
                'tableId'=>$data['tableId'],
            ];
            //查是否已经存在这个tableId
            self::checkProduct($main);
            $allocationProId = self::addPro($main);
            foreach ($data['lang'] as $value){
                $proLang=[
                    'allocationProId'=>$allocationProId,
                    'languageId'=>$value['languageId'],
                    'productTypeName'=>$value['productTypeName'],
                ];
                self::addProLang($proLang);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError('添加失败', 'ADD_FILE');
        }
        return;
    }
    public static function checkProduct($main)
    {
        $model = new AllocationProductModel();
        $res = $model->where('site_id',$main['siteId'])->where('table_id',$main['tableId'])->firstHumpArray();
        if(!empty($res)){
            throw new RJsonError('已存在这套系统', 'TABLEID_ERROR');
        }
        return;
    }
    /*
     * 添加产品
     * */
    public static function addPro($data)
    {
        $model = new AllocationProductModel();
        $model->setDataByHumpArray($data)->save();
        return $model->getQueueableId();
    }
    /*
     * 添加产品-lang
     * */
    public static function addProLang($data=[])
    {
        $model = new AllocationProLangModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }
    /*
     *新闻配置添加
     *  */
    public static function increaseNews($data)
    {
        \DB::beginTransaction();
        try{
            $main=[
                'siteId'=>$data['siteId'],
                'tableId'=>$data['tableId'],
            ];
            //查是否已经存在这个tableId
            self::checkNews($main);
            $allocationNewsId = self::addNews($main);
            foreach ($data['lang'] as $value){
                $newsLang=[
                    'allocationNewsId'=>$allocationNewsId,
                    'languageId'=>$value['languageId'],
                    'newsTypeName'=>$value['newsTypeName'],
                ];
                self::addNewsLang($newsLang);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError('添加失败', 'ADD_FILE');
        }
        return;
    }
    public static function checkNews($main)
    {
        $model = new AllocationNewsModel();
        $res = $model->where('site_id',$main['siteId'])->where('table_id',$main['tableId'])->firstHumpArray();
        if(!empty($res)){
            throw new RJsonError('已存在这套系统', 'TABLEID_ERROR');
        }
        return;
    }
    /*
     * 新闻事务
     * */
    public static function news($news,$lang)
    {
        \DB::beginTransaction();
        try{
            $allocationNewsId = self::addNews($news);
            foreach ($lang as $value){
                $newsLang=[
                    'allocationNewsId'=>$allocationNewsId,
                    'languageId'=>$value['languageId'],
                    'newsTypeName'=>$value['newsTypeName'],
                ];
                self::addNewsLang($newsLang);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }
    /*
     * 添加新闻
     * */
    public static function addNews($data=[])
    {
        $model = new AllocationNewsModel();
        $model->setDataByHumpArray($data)->save();
        return $model->getQueueableId();
    }
    /*
     * 添加新闻-lang
     * */
    public static function addNewsLang($data=[])
    {
        $model = new AllocationNewsLangModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }
    /*
     * 添加案例配置
     * */
    public static function increaseCases($data)
    {
        \DB::beginTransaction();
        try{
            $main=[
                'siteId'=>$data['siteId'],
                'tableId'=>$data['tableId']
            ];
            //查是否已经存在这个tableId
            self::checkCases($main);
            $allocationCasesId = self::addCases($main);
            foreach ($data['lang'] as $value){
                $casesLang=[
                    'allocationCasesId'=>$allocationCasesId,
                    'languageId'=>$value['languageId'],
                    'casesTypeName'=>$value['casesTypeName'],
                ];
                self::addCasesLang($casesLang);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError('添加失败', 'ADD_FILE');
        }
        return;
    }
    public static function checkCases($main)
    {
        $model = new AllocationCasesModel();
        $res = $model->where('site_id',$main['siteId'])->where('table_id',$main['tableId'])->firstHumpArray();
        if(!empty($res)){
            throw new RJsonError('已存在这套系统', 'TABLEID_ERROR');
        }
        return;
    }
    /*
     * 案例事务
     * */
    public static function cases($cases,$lang)
    {
        \DB::beginTransaction();
        try{
            $allocationCasesId = self::addCases($cases);
            foreach ($lang as $value){
                $casesLang=[
                    'allocationCasesId'=>$allocationCasesId,
                    'languageId'=>$value['languageId'],
                    'casesTypeName'=>$value['casesTypeName'],
                ];
                self::addCasesLang($casesLang);
            }
            \DB::commit();
        }catch(QueryException $e){
            throw new RJsonError('添加失败', 'ADD_FILE');
            \DB::rollBack();
            return false;
        }
        return true;
    }
    /*
     * 添加案例
     * */
    public static function addCases($data=[])
    {
        $model = new AllocationCasesModel();
        $model->setDataByHumpArray($data)->save();
        return $model->getQueueableId();
    }
    /*
     * 添加案例-lang
     * */
    public static function addCasesLang($data=[])
    {
        $model = new AllocationCasesLangModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }
    /*
     * 添加友情链接配置
     * */
    public static function increaseLink($data)
    {
        \DB::beginTransaction();
        try{
            $main=[
                'siteId'=>$data['siteId'],
            ];
            //查是否已经存在
            self::checkLink($main);
            $allocationLinkId = self::addLink($main);
            foreach ($data['lang'] as $value){
                $casesLang=[
                    'allocationLinkId'=>$allocationLinkId,
                    'languageId'=>$value['languageId'],
                    'linkName'=>$value['linkName'],
                ];
                self::addLinkLang($casesLang);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError('添加失败', 'ADD_FILE');
        }
        return;
    }
    public static function checkLink($main)
    {
        $model = new AllocationLinkModel();
        $res = $model->where('site_id',$main['siteId'])->firstHumpArray();
        if(!empty($res)){
            throw new RJsonError('已存在', 'LINK_ERROR');
        }
        return;
    }
    /*
     * 友情事务
     * */
    public static function link($link,$lang)
    {
        \DB::beginTransaction();
        try{
            $allocationLinkId = self::addLink($link);
            foreach ($lang as $value){
                $casesLang=[
                    'allocationLinkId'=>$allocationLinkId,
                    'languageId'=>$value['languageId'],
                    'linkName'=>$value['linkName'],
                ];
                self::addLinkLang($casesLang);
            }
            \DB::commit();
        }catch(QueryException $e){
            throw new RJsonError('添加失败', 'ADD_FILE');
            \DB::rollBack();
            return false;
        }
        return true;
    }
    /*
     * 添加友情链接
     * */
    public static function addLink($data=[])
    {
        $model = new AllocationLinkModel();
        $model->setDataByHumpArray($data)->save();
        return $model->getQueueableId();
    }
    /*
     * 添加友情链接-lang
     * */
    public static function addLinkLang($data=[])
    {
        $model = new AllocationLinkLangModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }
    /*
     * 添加留言配置
     * */
    public static function increaseMessage($data)
    {
        \DB::beginTransaction();
        try{
            $main=[
                'siteId'=>$data['siteId']
            ];
            //查是否已经存在
            self::checkMessage($main);
            $allocationMessageId = self::addMessage($main);
            foreach ($data['lang'] as $value){
                $casesLang=[
                    'allocationMessageId'=>$allocationMessageId,
                    'languageId'=>$value['languageId'],
                    'messageName'=>$value['messageName'],
                ];
                self::addMeassageLang($casesLang);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError('添加失败', 'ADD_FILE');
        }
        return;
    }
    public static function checkMessage($main)
    {
        $model = new AllocationLinkModel();
        $res = $model->where('site_id',$main['siteId'])->firstHumpArray();
        if(!empty($res)){
            throw new RJsonError('已存在', 'MESSAGE_ERROR');
        }
        return;
    }
    /*
     * 留言事务
     * */
    public static function message($message,$lang)
    {
        \DB::beginTransaction();
        try{
            $allocationMessageId = self::addMessage($message);
            foreach ($lang as $value){
                $casesLang=[
                    'allocationMessageId'=>$allocationMessageId,
                    'languageId'=>$value['languageId'],
                    'messageName'=>$value['messageName'],
                ];
                self::addMeassageLang($casesLang);
            }
            \DB::commit();
        }catch(QueryException $e){
            throw new RJsonError('添加失败', 'ADD_FILE');
            \DB::rollBack();
            return false;
        }
        return true;
    }
    /*
     * 添加信息
     * */
    public static function addMessage($data=[])
    {
        $model = new AllocationMessageModel();
        $model->setDataByHumpArray($data)->save();
        return  $model->getQueueableId();
    }
    /*
     * 添加留留言message-lang
     * */
    public static function addMeassageLang($data=[])
    {
        $model = new AllocationMessageLangModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }
    /*
     * 获取网站配置列表
     * */
    public static function getAllocation($siteId,$languageId){
        $arr=[];
        $pro = self::getProduct($siteId);
        if(isset($pro)){
            foreach ($pro as $key=>$value){
                //获取
                $proLang = self::getProLangOne($value['allocationProId'],$languageId);
                $pro[$key]['productTypeName']=empty($proLang['productTypeName']) ? '' : $proLang['productTypeName'];
            }
        }
        $news = self::getNews($siteId);
        if(isset($news)){
            foreach ($news as $key=>$value){
                //获取
                $newsLang = self::getNewsLangOne($value['allocationNewsId'],$languageId);
                $news[$key]['newsTypeName']=$newsLang['newsTypeName'] ?? '';
            }
        }
        $cases = self::getCases($siteId);
        if(isset($cases)){
            foreach ($cases as $key=>$value){
                //获取
                $casesLang = self::getCasesLangOne($value['allocationCasesId'],$languageId);
                $cases[$key]['casesTypeName']=$casesLang['casesTypeName'] ?? '';
            }
        }
        $link = self::getLink($siteId);
        if($link){
            foreach ($link as $key=>$value){
                //获取
                $linkLang = self::getLinkLangOne($value['allocationLinkId'],$languageId);
                $link[$key]['linkName']=$linkLang['linkName'] ?? '';
            }
        }
        $message = self::getMessage($siteId);
        if($message){
            foreach ($message as $key=>$value){
                //获取
                $messageLang = self::getMessageLangOne($value['allocationMessageId'],$languageId);
                $message[$key]['messageName']=$messageLang['messageName'] ?? '';
            }
        }
        $arr['product']=empty($pro) ? [] : $pro;
        $arr['news']=empty($news) ? [] : $news;
        $arr['cases']=empty($cases) ? [] : $cases;
        $arr['link']=empty($link) ? [] : $link;
        $arr['message']=empty($message) ? [] : $message;
        return $arr;
    }
    /*
     * 获取产品
     * */
    public static function getProduct($siteId){
        $pro = (new AllocationProductModel())->where('site_id',$siteId)
            ->getHumpArray(['allocation_pro_id','table_id','product_type_name']);
        return $pro;
    }
    /*
     * 获取单条
     * */
    public static function getProOne($allocationProId)
    {
        $pro = (new AllocationProductModel())->where('allocation_pro_id',$allocationProId)
            ->firstHumpArray(['allocation_pro_id','table_id','site_id']);
        if(!empty($pro)){
            $proLang = self::getProLang($allocationProId);
            $pro['lang']=$proLang;
        }
        return $pro;
    }
    /*
     * 获取产品语言单条
     * */
    public static function getProLang($allocationProId)
    {
        $pro = (new AllocationProLangModel())->where('allocation_pro_id',$allocationProId)->getHumpArray(['*']);
        return $pro;
    }
    /*
     * 获取产品语言单条
     * */
    public static function getProLangOne($allocationProId,$languageId)
    {
        $pro = (new AllocationProLangModel())->where('allocation_pro_id',$allocationProId)
            ->where('language_id',$languageId)
            ->firstHumpArray(['*']);
        return $pro;
    }
    /*
     * 获取单条--siteId
     * */
    public static function getProBySiteId($siteId)
    {
        $pro = (new AllocationProductModel())->where('site_id',$siteId)->orderby('table_id','DESC')->firstHumpArray(['*']);
        return $pro;
    }
    /*
     * 获取新闻
     * */
    public static function getNews($siteId){
        $news = (new AllocationNewsModel())->where('site_id',$siteId)
            ->getHump(['allocation_news_id','table_id','news_type_name']);
        return $news;
    }
    /*
     * 获取单条
     * */
    public static function getNewsOne($allocationNewsId)
    {
        $news = (new AllocationNewsModel())->where('allocation_news_id',$allocationNewsId)
            ->firstHumpArray(['allocation_news_id','site_id','table_id']);
        if(!empty($news)){
            $newsLang = self::getNewsLang($allocationNewsId);
            $news['lang']=$newsLang;
        }
        return $news;
    }
    /*
     * 获取新闻语言列表
     * */
    public static function getNewsLang($allocationNewsId)
    {
        $lang = (new AllocationNewsLangModel())->where('allocation_news_id',$allocationNewsId)->getHumpArray();
        return $lang;
    }
    /*
     * 获取新闻语言单条
     * */
    public static function getNewsLangOne($allocationNewsId,$languageId)
    {
        $news = (new AllocationNewsLangModel)->where('allocation_news_id',$allocationNewsId)->where('language_id',$languageId)->firstHumpArray(['*']);
        return $news;
    }
    /*
     * 获取单条--siteId
     * */
    public static function getNewsBySiteId($siteId)
    {
        $pro = (new AllocationNewsModel)->where('site_id',$siteId)->orderby('table_id','DESC')->firstHumpArray(['*']);
        return $pro;
    }
    /*
     * 获取案例
     * */
    public static function getCases($siteId){
        $cases = (new AllocationCasesModel())->where('site_id',$siteId)
            ->getHump(['allocation_cases_id','table_id','cases_type_name']);
        return $cases;
    }
    /*
     * 获取单条
     * */
    public static function getCasesOne($allocationCasesId)
    {
        $cases = (new AllocationCasesModel())->where('allocation_cases_id',$allocationCasesId)
            ->firstHumpArray(['allocation_cases_id','site_id','table_id']);
        if($cases){
            $casesLang = self::getCasesLang($allocationCasesId);
            $cases['lang'] = $casesLang;
        }
        return $cases;
    }
    /*
     * 获取案例语言列表
     * */
    public static function getCasesLang($allocationCasesId)
    {
        $cases = (new AllocationCasesLangModel())->where('allocation_cases_id',$allocationCasesId)->getHumpArray();
        return $cases;
    }
    /*
     * 获取新闻语言单条
     * */
    public static function getCasesLangOne($allocationCasesId,$languageId)
    {
        $cases = (new AllocationCasesLangModel())->where('allocation_cases_id',$allocationCasesId)->where('language_id',$languageId)->firstHumpArray(['*']);
        return $cases;
    }
    /*
     * 获取单条--siteId
     * */
    public static function getCasesBySiteId($siteId)
    {
        $pro = (new AllocationCasesModel())->where('site_id',$siteId)->orderby('table_id','DESC')->firstHumpArray(['*']);
        return $pro;
    }
    /*
     * 获取友情链接
     * */
    public static function getLink($siteId){
        $link = (new AllocationLinkModel())->where('site_id',$siteId)
            ->getHump(['allocation_link_id','link_name']);
        return $link;
    }
    /*
     * 获取单条
     * */
    public static function getLinkOne($allocationLinkId)
    {
        $link = (new AllocationLinkModel())->where('allocation_link_id',$allocationLinkId)
            ->firstHumpArray(['allocation_link_id','site_id']);
        if($link){
            $linkLang = self::getLinkLang($allocationLinkId);
            $link['lang'] = $linkLang;
        }
        return $link;
    }
    /*
     * 获取友情语言列表
     * */
    public static function getLinkLang($allocationLinkId)
    {
        $link = (new AllocationLinkLangModel())->where('allocation_link_id',$allocationLinkId)->getHumpArray();
        return $link;
    }
    /*
     * 获取新闻语言单条
     * */
    public static function getLinkLangOne($allocationLinkId,$languageId)
    {
        $link = (new AllocationLinkLangModel())->where('allocation_link_id',$allocationLinkId)->where('language_id',$languageId)->firstHumpArray(['*']);
        return $link;
    }
    /*
     * 获取单条--siteId
     * */
    public static function getLinkBySiteId($siteId)
    {
        $pro = (new AllocationLinkModel())->where('site_id',$siteId)->orderby('table_id','DESC')->firstHumpArray(['*']);
        return $pro;
    }
    /*
     * 获取留言
     * */
    public static function getMessage($siteId){
        $message = (new AllocationMessageModel())->where('site_id',$siteId)
            ->getHump(['allocation_message_id','message_name']);
        return $message;
    }
    /*
     * 获取单条
     * */
    public static function getMessageOne($allocationMessageId)
    {
        $message = (new AllocationMessageModel())->where('allocation_message_id',$allocationMessageId)
            ->firstHumpArray(['allocation_message_id','site_id']);
        if($message){
            $messageLang = self::getMessageLang($allocationMessageId);
            $message['lang'] = $messageLang;
        }
        return $message;
    }
    /*
     * 获取留言语言列表
     * */
    public static function getMessageLang($allocationMessageId)
    {
        $message = (new AllocationMessageLangModel())->where('allocation_message_id',$allocationMessageId)->getHumpArray();
        return $message;
    }
    /*
     * 获取新闻语言单条
     * */
    public static function getMessageLangOne($allocationMessageId,$languageId)
    {
        $message = (new AllocationMessageLangModel())->where('allocation_message_id',$allocationMessageId)->where('language_id',$languageId)->firstHumpArray(['*']);
        return $message;
    }
    /*
     * 获取单条--siteId
     * */
    public static function getMessageBySiteId($siteId)
    {
        $message = (new AllocationMessageModel())->where('site_id',$siteId)->orderby('table_id','DESC')->firstHumpArray(['*']);
        return $message;
    }
    /*
     * 产品修改事务
     * */
    public static function editProAffairs($data)
    {
        \DB::beginTransaction();
        try{
            $allocationProId = $data['allocationProId'];
            $siteId = $data['siteId'];
            $main['tableId']=$data['tableId'];
            self::editMainPro($allocationProId,$main,$siteId);
            foreach ($data['lang'] as $value){
                $lang=[
                    'languageId'=>$value['languageId'],
                    'productTypeName'=>$value['productTypeName'],
                ];
                self::editPro($allocationProId,$lang);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError('添加失败', 'ADD_FILE');
        }
        return;
    }
    /*
     * 产品多套修改
     * */
    public static function editMainPro($allocationProId,$data,$siteId)
    {
        (new AllocationProductModel())->where('allocation_pro_id',$allocationProId)
            ->where('site_id',$siteId)
            ->updateByHump($data);
    }
    public static function editPro($allocationProId,$data)
    {
        (new AllocationProLangModel())->where('allocation_pro_id',$allocationProId)
            ->where('language_id',$data['languageId'])
            ->updateByHump($data);
    }
    /*
     * 新闻修改事务
     * */
    public static function editNewsAffairs($data)
    {
        \DB::beginTransaction();
        try{
            $allocationNewsId = $data['allocationNewsId'];
            $siteId = $data['siteId'];
            $main['tableId']=$data['tableId'];
            self::editMainNews($allocationNewsId,$main,$siteId);
            foreach ($data['lang'] as $value){
                $lang=[
                    'languageId'=>$value['languageId'],
                    'newsTypeName'=>$value['newsTypeName'],
                ];
                self::editNews($allocationNewsId,$lang);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError('添加失败', 'ADD_FILE');
        }
        return;
    }
    /*
     * 新闻案例修改
     * */
    public static function editMainNews($allocationNewsId,$data,$siteId)
    {
        (new AllocationNewsModel())->where('allocation_news_id',$allocationNewsId)
            ->where('site_id',$siteId)
            ->updateByHump($data);
    }
    public static function editNews($allocationNewsId,$data)
    {
        (new AllocationNewsLangModel())->where('allocation_news_id',$allocationNewsId)
            ->where('language_id',$data['languageId'])
            ->updateByHump($data);
    }

    /*
     * 案例修改事务
     * */
    public static function editCasesAffairs($data)
    {
        \DB::beginTransaction();
        try{
            $allocationNewsId = $data['allocationCasesId'];
            $siteId = $data['siteId'];
            $main['tableId']=$data['tableId'];
            self::editMainCases($allocationNewsId,$main,$siteId);
            foreach ($data['lang'] as $value){
                $lang=[
                    'languageId'=>$value['languageId'],
                    'casesTypeName'=>$value['casesTypeName'],
                ];
                self::editCases($allocationNewsId,$lang);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError('添加失败', 'ADD_FILE');
        }
        return;
    }
    /*
     * 案例修改
     * */
    public static function editMainCases($allocationCasesId,$data,$siteId)
    {
        (new AllocationCasesModel())->where('allocation_cases_id',$allocationCasesId)
            ->where('site_id',$siteId)
            ->updateByHump($data);
    }
    public static function editCases($allocationCasesId,$data)
    {
        (new AllocationCasesLangModel())->where('allocation_cases_id',$allocationCasesId)
            ->where('language_id',$data['languageId'])
            ->updateByHump($data);
    }


    /*
     * 留言修改事务
     * */
    public static function editMessageAffairs($data)
    {
        \DB::beginTransaction();
        try{
            $allocationMessageId = $data['allocationMessageId'];
            foreach ($data['lang'] as $value){
                $lang=[
                    'languageId'=>$value['languageId'],
                    'messageName'=>$value['messageName'],
                ];
                self::editMessage($allocationMessageId,$lang);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError('添加失败', 'ADD_FILE');
        }
        return;
    }

    /*
     * 留言修改
     * */
    public static function editMessage($allocationMessageId,$data)
    {
        (new AllocationMessageLangModel())->where('allocation_message_id',$allocationMessageId)
            ->where('language_id',$data['languageId'])
            ->updateByHump($data);
    }


    /*
     * 留言修改事务
     * */
    public static function editLinkAffairs($data)
    {
        \DB::beginTransaction();
        try{
            $allocationLinkId = $data['allocationLinkId'];
            foreach ($data['lang'] as $value){
                $lang=[
                    'languageId'=>$value['languageId'],
                    'linkName'=>$value['linkName'],
                ];
                self::editLink($allocationLinkId,$lang);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError('添加失败', 'ADD_FILE');
        }
        return;
    }

    /*
     * 友情链接修改
     * */
    public static function editLink($allocationLinkId,$data)
    {
        (new AllocationLinkLangModel())->where('allocation_link_id',$allocationLinkId)
            ->where('language_id',$data['languageId'])
            ->updateByHump($data);
    }
    /*
     * 产品配置删除
     * */
    public static function deleteProduct($allocationProId)
    {
        \DB::beginTransaction();
        try{
            (new AllocationProductModel())->where('allocation_pro_id',$allocationProId)->delete();
            (new AllocationProLangModel())->where('allocation_pro_id',$allocationProId)->delete();
            \Db::commit();
        }catch(QueryException $e){
            \DB::rollBack();
        }
        return;
    }
    /*
     * 新闻配置删除
     * */
    public static function deleteNews($allocationNewsId)
    {
        \DB::beginTransaction();
        try{
            (new AllocationNewsModel())->where('allocation_news_id',$allocationNewsId)->delete();
            (new AllocationNewsLangModel())->where('allocation_news_id',$allocationNewsId)->delete();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
        }
    }
    /*
     * 案例配置删除
     * */
    public static function deleteCases($allocationCasesId)
    {
        \DB::begintransaction();
        try{
            (new AllocationCasesModel())->where('allocation_cases_id',$allocationCasesId)->delete();
            (new AllocationCasesLangModel())->where('allocation_cases_id',$allocationCasesId)->delete();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
        }
    }
    /*
     * 友情链接配置删除
     * */
    public static function deleteLink($allocationLinkId)
    {
        \DB::beginTransaction();
        try{
            (new AllocationLinkModel())->where('allocation_link_id',$allocationLinkId)->delete();
            (new AllocationLinkLangModel())->where('allocation_link_id',$allocationLinkId)->delete();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
        }
    }
    /*
     * 留言配置删除
     * */
    public static function deleteMessage($allocationMessageId)
    {
        \DB::beginTransaction();
        try{
            (new AllocationMessageModel())->where('allocation_message_id',$allocationMessageId)->delete();
            (new AllocationMessageLangModel())->where('allocation_message_id',$allocationMessageId)->delete();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
        }
    }

    //----------------------------------------------------------

    /*
     * in语法的方法
     * 获取测试分类数据的一个方法
     * @param int $data['languageId'] === 1 中文  === 2 英文
     */
    public static function ceShiList($data){
        $model1 = new AllocationCasesModel();//allocationcases表
        $res = $model1
            ->where('site_id', $data['siteId'])
            ->getHumpArray(['allocation_cases_id', 'table_id', 'site_id', 'cases_type_name']);

        foreach ($res as $k => $v) {
            $ids[] = $v['allocationCasesId'];//语音表id
            $siteId[] = $v['siteId'];//用于查询新闻的id
        }


        $model2 = new AllocationCasesLangModel();//语言表
        $model2->whereIn('allocation_cases_id', $ids);
        $model2->where('language_id', $data['languageId']);
        $langRes = $model2->getHumpArray(['cases_type_name', 'language_id', 'cases_lang_id']);


        $model3 = new AllocationNewsModel();//新闻
        $news = $model3
                ->whereIn('site_id', $siteId)
                ->getHumpArray();

        foreach ($news as $k => $v) {

            $newsLangId[] = $v['siteId'];
        }

        $model4 = new AllocationNewsLangModel();//新闻 语言表
        $newsLangList = $model4
            ->whereIn('allocation_news_id', $newsLangId)
            ->where('language_id', $data['languageId'])
            ->getHumpArray(['news_type_name', 'language_id']);

        $result['lists']['product'] = $res;//追加产品
        $result['lists']['cases'] = $langRes;//追加案例
        $result['lists']['news'] = $newsLangList;//追加新闻

        return $result;
    }

    /*
     * 左链接的方法
     */
    public static function ceShiList2($data) {

        $model1 = new AllocationCasesModel();
        $res = $model1
            ->leftJoin('allocation_cases_lang','allocation_cases.allocation_cases_id', '=', 'allocation_cases_lang.allocation_cases_id')
            ->where('allocation_cases.site_id', $data['siteId'])
            ->where('allocation_cases_lang.language_id', $data['languageId'])
            ->getHumpArray();

        return $res;

    }




}