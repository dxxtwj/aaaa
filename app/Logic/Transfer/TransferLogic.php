<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Transfer;

//管理员
use App\Model\Admin\AdminModel;
use App\Model\Admin\AdminDescModel;
//关于我们
use App\Model\About\AboutModel;
use App\Model\About\AboutDescModel;
use App\Model\About\SidebarModel;
//广告图
use App\Model\Banner\BannerModel;
use App\Model\Banner\BannerDescModel;
//友情
use App\Model\Link\LinkModel;
use App\Model\Link\LinkDescModel;
//菜单
use App\Model\Menu\MenuModel;
use App\Model\Menu\MenuDescModel;
use App\Model\Menu\MenuUrlModel;
//留言
use App\Model\Message\MessageCategoryModel;
use App\Model\Message\MessageCategoryDescModel;
use App\Model\Message\MessageModel;
use App\Model\Message\MessageDescModel;
//基本信息
use App\Model\Basic\BasicModel;
use App\Model\Basic\BasicDescModel;
//案例
use App\Model\Cases\CasesCateModel;
use App\Model\Cases\CasesCateDescModel;
use App\Model\Cases\CasesModel;
use App\Model\Cases\CasesDescModel;
use App\Model\Cases\CasesBannerModel;
use App\Model\Cases\CasesimageModel;
//新闻
use App\Model\News\NewsCateModel;
use App\Model\News\NewsCateDescModel;
use App\Model\News\NewsModel;
use App\Model\News\NewsDescModel;
use App\Model\News\NewsBannerModel;
use App\Model\News\NewsimageModel;
//产品
use App\Model\Product\ProductCateModel;
use App\Model\Product\ProductCateDescModel;
use App\Model\Product\ProductModel;
use App\Model\Product\ProductDescModel;
use App\Model\Product\ProductAttributeModel;
use App\Model\Product\ProductBannerModel;
use App\Model\Product\ProductimageModel;



use App\Model\SiteModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class TransferLogic
{
    //获取文章分类
    public static function get($data)
    {
        if(!empty($data['type']=='admin')){
            $res = self::getAdmin($data['siteId']);
        }
        if(!empty($data['type']=='about')){
            $res = self::getAbout($data['siteId']);
        }
        /*if(!empty($data['type']=='sidebar')){
            $res = self::getSidebar($data['siteId']);
        }*/
        if(!empty($data['type']=='banner')){
            $res = self::getBanner($data['siteId']);
        }
        if(!empty($data['type']=='basic')){
            $res = self::getBasic($data['siteId']);
        }
        if(!empty($data['type']=='link')){
            $res = self::getLink($data['siteId']);
        }
        if(!empty($data['type']=='menu')){
            $res = self::getMenu($data['siteId']);
        }
        if(!empty($data['type']=='messagecate')){
            $res = self::getMessageCate($data['siteId']);
        }
        if(!empty($data['type']=='message')){
            $res = self::getMessage($data['siteId']);
        }
        if(!empty($data['type']=='casescate')){
            $res = self::getCasesCate($data['siteId']);
        }
        /*if(!empty($data['type']=='cases')){
            $res = self::getCases($data['siteId']);
        }*/
        if(!empty($data['type']=='newscate')){
            $res = self::getNewsCate($data['siteId']);
        }
        /*if(!empty($data['type']=='news')){
            $res = self::getNews($data['siteId']);
        }*/
        if(!empty($data['type']=='productcate')){
            $res = self::getProductCate($data['siteId']);
        }
        /*if(!empty($data['type']=='product')){
            $res = self::getProduct($data['siteId']);
        }*/
        return $res;
    }

    //管理员
    public static function getAdmin($siteId)
    {
        $res = AdminModel::where('site_id',$siteId)
            ->leftJoin('admin_description','admin.admin_id','=','admin_description.admin_id')
            ->getHumpArray(['admin.*','admin_description.*']);
        self::AdminAffair($res);
        return $res;
    }
    public static function AdminAffair($Admin)
    {
        \DB::beginTransaction();
        try{
            foreach ($Admin as $key=>$value){
                $main=[
                    'siteId' => $value['siteId'],
                ];
                $adminId = self::addAdmin($main);
                $desc = [
                    'adminId'=> $adminId,
                    'siteId'=> $value['siteId'],
                    'adminName'=> $value['adminName'],
                    'adminPassword'=> $value['adminPassword'],
                    'siteName'=> $value['siteName'],
                ];
                self::addAdminDesc($desc);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }
    public static function addAdmin($main)
    {
        //$model = new AdminModel();
        //$model->setDataByHumpArray($main)->save();
        //return $model->getQueueableId();
    }
    public static function addAdminDesc($desc)
    {
        //$model = new AdminDescModel();
        //$model->setDataByHumpArray($desc)->save();
        //return $model->getQueueableId();
    }

    //关于我们
    public static function getAbout($siteId)
    {
        $res = AboutModel::where('site_id',$siteId)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                $res2 = self::getAboutDesc($value['aboutId']);
                $res[$key]['lang']=empty($res2) ? [] : $res2;
                //侧栏
                $sidebar = self::getSidebar($value['aboutId']);
                $res[$key]['sidebar'] = empty($sidebar) ? [] : $sidebar;
            }
        }
        //self::AboutAffair($res);
        return $res;
    }
    public static function getAboutDesc($aboutId)
    {
        $res = AboutDescModel::where('about_id',$aboutId)->getHumpArray(['*']);
        return $res;
    }
    public static function getSidebar($aboutId)
    {
        $res = SidebarModel::where('about_id',$aboutId)->firstHumpArray(['*']);
        return $res;
    }
    public static function AboutAffair($About)
    {
        \DB::beginTransaction();
        try{
            foreach ($About as $key=>$value){
                $main=[
                    'sort' => $value['sort'],
                    'siteId' => $value['siteId'],
                    'aboutType' => $value['aboutType'],
                    'isOn' => $value['isOn'],
                    //'aboutCateId' => $value['aboutCateId'],
                ];
                //$aboutId = self::addAbout($main);
                foreach ($value['lang'] as $val){
                    $desc = [
                        //'aboutId'=>$aboutId,
                        'languageId'=>$val['languageId'],
                        'aboutTitle'=>$val['aboutTitle'],
                        'aboutThumb'=>$val['aboutThumb'],
                        'aboutContent'=>$val['aboutContent'],
                        'siteTitle'=>$val['siteTitle'],
                        'siteKeywords'=>$val['siteKeywords'],
                        'siteDescription'=>$val['siteDescription'],
                    ];
                    //self::addAboutDesc($desc);
                }
                if(!empty($value['sidebar'])){
                    $res = $value['sidebar'];
                    $sidebar = [
                        'group'=> $res['group'],
                        'sort'=> $res['sort'],
                        'siteId'=> $res['siteId'],
                        //'aboutId'=> $aboutId,
                        'isOn'=> $res['isOn'],
                    ];
                    //self::addSidebar($sidebar);
                }
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }
    public static function addAbout($main)
    {
        //$model = new AboutModel();
        //$model->setDataByHumpArray($main)->save();
        //return  = $model->getQueueableId();
    }
    public static function addAboutDesc($desc)
    {
        //$model = new AboutDescModel();
        //$model->setDataByHumpArray($desc)->save();
        //return  = $model->getQueueableId();
    }
    public static function addSidebar($sidebar)
    {
        //$model = new SidebarModel();
        //$model->setDataByHumpArray($sidebar)->save();
        //return  = $model->getQueueableId();
    }


    //广告图Banner
    public static function getBanner($siteId)
    {
        $res = BannerModel::where('site_id',$siteId)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                $res2 = self::getBannerDesc($value['bannerId']);
                $res[$key]['lang']=empty($res2) ? [] : $res2;
            }
        }
        //self::BannerAffair($res);
        return $res;
    }
    public static function getBannerDesc($bannerId)
    {
        $res = BannerDescModel::where('banner_id',$bannerId)->getHumpArray(['*']);
        return $res;
    }
    public static function BannerAffair($Banner)
    {
        \DB::beginTransaction();
        try{
            foreach ($Banner as $key=>$value){
                $main=[
                    'sort' => $value['sort'],
                    'siteId' => $value['siteId'],
                    'menuId' => $value['menuId'],
                    'bannerHit' => $value['bannerHit'],
                    'isOpen' => $value['isOpen'],
                    'isOn' => $value['isOn'],
                    'isUrl' => $value['isUrl'],
                    'bannerUrlType' => $value['bannerUrlType'],
                    'menuUrl' => $value['menuUrl'],
                    'systemType' => empty($value['systemType']) ? '' : $value['systemType'],
                    'labelId' => $value['labelId'],
                ];
                //$bannerId = self::addBanner($main);
                foreach ($value['lang'] as $val){
                    $desc = [
                        //'bannerId'=>$bannerId,
                        'languageId'=>$val['languageId'],
                        'bannerTitle'=>$val['bannerTitle'],
                        'bannerImage'=>$val['bannerImage'],
                        'bannerDesc'=>$val['bannerDesc'],
                        'bannerUrl'=>$val['bannerUrl'],
                    ];
                    //self::addBannerDesc($desc);
                }
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }
    public static function addBanner($main)
    {
        //$model = new BannerModel();
        //$model->setDataByHumpArray($main)->save();
        //return  = $model->getQueueableId();
    }
    public static function addBannerDesc($desc)
    {
        //$model = new BannerDescModel();
        //$model->setDataByHumpArray($desc)->save();
        //return  = $model->getQueueableId();
    }


    //友情链接
    public static function getLink($siteId)
    {
        $res = LinkModel::where('site_id',$siteId)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                $res2 = self::getLinkDesc($value['linkId']);
                $res[$key]['lang']=empty($res2) ? [] : $res2;
            }
        }
        //self::LinkAffair($res);
        return $res;
    }
    public static function getLinkDesc($linkId)
    {
        $res = LinkDescModel::where('link_id',$linkId)->getHumpArray(['*']);
        return $res;
    }
    //友情链接
    public static function LinkAffair($Link)
    {
        \DB::beginTransaction();
        try{
            foreach ($Link as $key=>$value){
                $main=[
                    'sort' => $value['sort'],
                    'siteId' => $value['siteId'],
                    'isOn' => $value['isOn'],
                ];
                //$linkId = self::addLink($main);
                foreach ($value['lang'] as $val){
                    $desc = [
                        //'linkId'=>$linkId,
                        'languageId'=>$val['languageId'],
                        'linkTitle'=>$val['linkTitle'],
                        'linkUrl'=>$val['linkUrl'],
                        'linkImage'=>$val['linkImage'],
                    ];
                    //self::addLinkDesc($desc);
                }
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }
    public static function addLink($main)
    {
        //$model = new LinkModel();
        //$model->setDataByHumpArray($main)->save();
        //return  = $model->getQueueableId();
    }
    public static function addLinkDesc($desc)
    {
        //$model = new LinkDescModel();
        //$model->setDataByHumpArray($desc)->save();
        //return  = $model->getQueueableId();
    }

    //菜单
    public static function getMenu($siteId)
    {
        $res = MenuModel::where('site_id',$siteId)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                $res2 = self::getMenuDesc($value['menuId']);
                $res[$key]['lang']=empty($res2) ? [] : $res2;
            }
        }
        return $res;
    }
    public static function getMenuDesc($menuId)
    {
        $res = MenuDescModel::where('menu_id',$menuId)
            ->leftJoin('menu_url','menu_description.menu_desc_id','=','menu_url.menu_desc_id')
            ->getHumpArray(['menu_description.*','menu_url.*']);
        return $res;
    }

    //留言--Message
    public static function getMessageCate($siteId)
    {
        $res = MessageCategoryModel::where('site_id',$siteId)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                $res2 = self::getMessageCateDesc($value['messageCateId']);
                $res[$key]['lang']=empty($res2) ? [] : $res2;
                //留言列表
                $message = self::getMessage($siteId,$value['messageCateId']);
                $res[$key]['messageLists'] = empty($message) ? [] : $message;
            }
        }
        return $res;
    }
    public static function getMessageCateDesc($messageCateId)
    {
        $res = MessageCategoryDescModel::where('message_cate_id',$messageCateId)->getHumpArray(['*']);
        return $res;
    }
    //留言分类-事务
    public static function MessageCateAffair($messageCate)
    {
        \DB::beginTransaction();
        try{
            foreach ($messageCate as $key=>$value){
                $main=[
                    'sort' => $value['sort'],
                    'siteId' => $value['siteId'],
                    'messageCateId' => $value['messageCateId'],
                    'isOn' => $value['isOn'],
                ];
                //$messageId = self::addMessageCate($main);
                foreach ($value['lang'] as $val){
                    $desc = [
                        'messageCateId'=>$val['messageCateId'],
                        'languageId'=>$val['languageId'],
                        'messageCateTitle'=>$val['messageCateTitle'],
                        'siteTitle'=>$val['siteTitle'],
                        'siteKeywords'=>$val['siteKeywords'],
                        'siteDescription'=>$val['siteDescription'],
                    ];
                    //self::addMessageCateDesc($desc);
                }
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }
    public static function addMessageCate($main)
    {
        //$model = new MessageCateModel();
        //$model->setDataByHumpArray($main)->save();
        //return  = $model->getQueueableId();
    }
    public static function addMessageCateDesc($desc)
    {
        //$model = new MessageCateModel();
        //$model->setDataByHumpArray($desc)->save();
        //return  = $model->getQueueableId();
    }
    public static function getMessage($siteId)
    {
        $res = MessageModel::where('site_id',$siteId)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                $res2 = self::getMessageDesc($value['messageId']);
                $res[$key]['lang']=empty($res2) ? [] : $res2;
            }
        }
        return $res;
    }
    public static function getMessageDesc($messageId)
    {
        $res = MessageDescModel::where('message_id',$messageId)->getHumpArray(['*']);
        return $res;
    }
    //留言-事务
    public static function MessageAffair($message)
    {
        \DB::beginTransaction();
        try{
            foreach ($message as $key=>$value){
                $main=[
                    'uid' => $value['uid'],
                    'siteId' => $value['siteId'],
                    'messageCateId' => $value['messageCateId'],
                    'isOn' => $value['isOn'],
                ];
                //$messageId = self::addMessage($main);
                foreach ($value['lang'] as $val){
                    $desc = [
                        //'messageId'=>$messageId,
                        'languageId'=>$val['languageId'],
                        'messagePerson'=>$val['messagePerson'],
                        'messagePhone'=>$val['messagePhone'],
                        'messageEmial'=>$val['messageEmial'],
                        'messageCompany'=>$val['messageCompany'],
                        'messageAddress'=>$val['messageAddress'],
                        'messageContent'=>$val['messageContent'],
                    ];
                    //self::addMessageDesc($desc);
                }
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }
    public static function addMessage($main)
    {
        //$model = new MessageModel();
        //$model->setDataByHumpArray($main)->save();
        //return  = $model->getQueueableId();
    }
    public static function addMessageDesc($desc)
    {
        //$model = new MessageDescModel();
        //$model->setDataByHumpArray($desc)->save();
        //return  = $model->getQueueableId();
    }

    //基本信息
    public static function getBasic($siteId)
    {
        $res = BasicModel::where('site_id',$siteId)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                $res2 = self::getBasicDesc($value['basicId']);
                $res[$key]['lang']=empty($res2) ? [] : $res2;
            }
        }
        self::BasicAffair($res);
        return $res;
    }
    public static function getBasicDesc($basicId)
    {
        $res = BasicDescModel::where('basic_id',$basicId)->getHumpArray(['*']);
        return $res;
    }
    //基本信息--事务
    public static function BasicAffair($basic)
    {
        \DB::beginTransaction();
        try{
            foreach ($basic as $key1=>$value1){
                $main=[
                    'siteId' => $value1['siteId'],
                ];
                //$basicId = self::addBasic($main);
                foreach ($value1['lang'] as $key=>$value){
                    $desc=[
                        //'basicId' => $basicId,
                        'basicCopyright' => empty($value['basicCopyright']) ? '' : $value['basicCopyright'],
                        'basicRecord'=> empty($value['basicRecord']) ? '' : $value['basicRecord'],//备案号
                        'basicCompany' => empty($value['basicCompany']) ? '' : $value['basicCompany'],
                        'logo' => empty($value['logo']) ? '' : $value['logo'],
                        'basicEmail' => empty($value['basicEmail']) ? '' : $value['basicEmail'],
                        'contacts' => empty($value['contacts']) ? '' : $value['contacts'],
                        'basicDesc' => empty($value['basicDesc']) ? '' : $value['basicDesc'],
                        'basicProject'=>empty($value['basicProject']) ? '' : $value['basicProject'],//项目名称
                        'companyTel' => empty($value['companyTel']) ? '' : $value['companyTel'],
                        'companyPhone' => empty($value['companyPhone']) ? '' : $value['companyPhone'],
                        'scanCode' => empty($value['scanCode']) ? '' : $value['scanCode'],
                        'companyAddress' => empty($value['companyAddress']) ? '' : $value['companyAddress'],
                        'languageId'=>$value['languageId'],
                        'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                        'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                        'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                    ];
                    //var_dump($desc);
                    //self::addBasicDesc($desc);
                }
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }
    public static function addBasic($main)
    {
        //$model = new BasicModel();
        //$model->setDataByHumpArray($main)->save();
        //return $model->getQueueableId();
    }
    public static function addBasicDesc($desc)
    {
        //$model = new BasicDescModel();
        //$model->setDataByHumpArray($main)->save();
        //return $model->getQueueableId();
    }

    //案例
    public static function getCases($siteId,$casesCateId)
    {
        $res = CasesModel::where('site_id',$siteId)->where('cases_cate_id',$casesCateId)->limit(2)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value){
                //banner
                $banner = self::getCasesBanner($value['casesId']);
                //desc
                $desc = self::getCasesDesc($value['casesId']);
                $res[$key]['casesbanner']=empty($banner) ? [] : $banner;
                $res[$key]['lang']=empty($desc) ? [] : $desc;
            }
        }
        return $res;
    }
    public static function getCasesBanner($casesId)
    {
        $banner = CasesBannerModel::where('cases_id',$casesId)->getHumpArray(['*']);
        return $banner;
    }
    public static function getCasesDesc($casesId)
    {
        $res = CasesDescModel::where('cases_id',$casesId)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                //image
                $image = self::getCasesImage($value['casesId']);
                $res[$key]['photos'] = empty($image) ? [] : $image;
            }
        }
        return $res;
    }
    public static function getCasesImage($casesId)
    {
        $res = CasesimageModel::where('cases_id',$casesId)->getHumpArray(['*']);
        return $res;
    }
    //案例--分类
    public static function getCasesCate($siteId)
    {
        $res = CasesCateModel::where('site_id',$siteId)->where('pid',0)->limit(20)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                $child = self::getCasesCateChild($siteId,$value['casesCateId']);
                $res[$key]['child'] = empty($child) ? [] : $child;
                $res2 = self::getCasesCateDesc($value['casesCateId']);
                $res[$key]['lang'] = empty($res2) ? [] : $res2;
                $cases = self::getCases($siteId,$value['casesCateId']);
                $res[$key]['casesLists'] = empty($cases) ? [] : $cases;
            }
        }
        //把案例拿出来一起添加
        self::cases($res);
        return $res;
    }
    public static function getCasesCateChild($siteId,$casesCateId)
    {
        $res = CasesCateModel::where('site_id',$siteId)->where('pid',$casesCateId)->limit(20)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                $res2 = self::getCasesCateDesc($value['casesCateId']);
                $res[$key]['lang'] = empty($res2) ? [] : $res2;
                //把案例拿出来一起添加
                $cases = self::getCases($siteId,$value['casesCateId']);
                $res[$key]['casesLists'] = empty($cases) ? [] : $cases;
            }
        }
        return $res;
    }
    public static function getCasesCateDesc($casesCateId)
    {
        $res = CasesCateDescModel::where('cases_cate_id',$casesCateId)->getHumpArray(['*']);
        return $res;
    }

    //拿到数据处理--cases
    public static function cases($data)
    {
        foreach ($data as $key=>$value){

            self::CasesParents($value);
        }
        return true;
    }
    //父级
    public static function CasesCate($parents)
    {
        $main=[
            "pid"=> $parents['pid'],
            "sort"=> $parents['sort'],
            //"casesCateId"=> $parents['casesCateId'],
            "tableId"=> $parents['tableId'],
            "siteId"=> $parents['siteId'],
            "isOn"=> $parents['isOn'],
        ];
        //$model = new CasesCateModel();
        //$model->setDataByHumpArray($main)->save();
        //return  = $model->getQueueableId();
    }
    //孩子
    public static function CasesCateChild($child,$casesCateId)
    {
        $main=[
            "pid"=> $child['pid'],
            "sort"=> $child['sort'],
            "casesCateId"=> $casesCateId,
            "tableId"=> $child['tableId'],
            "siteId"=> $child['siteId'],
            "isOn"=> $child['isOn'],
        ];
        //$model = new CasesCateModel();
        //$model->setDataByHumpArray($main)->save();
        //return $model->getQueueableId();
    }
    //案例分诶-主-事务
    public static function CasesParents($parents)
    {
        \DB::beginTransaction();
        try{
            $casesCateId = self::CasesCate($parents);
            self::CasesCateAffair($parents['child'],$casesCateId);
            self::addCasesCateDesc($parents['lang'],$casesCateId);
            self::CasesAffair($parents['casesLists'],$casesCateId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return true;
    }
    //案例分类-事务
    public static function CasesCateAffair($data,$casesCateId)
    {
        \DB::beginTransaction();
        try{
            $casesCateChildId = self::CasesCateChild($data,$casesCateId);
            self::addCasesCateDesc($data['lang'],/*$casesCateChildId*/1);
            self::CasesAffair($data['casesLists'],/*$casesCateChildId*/1);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return true;
    }
    //案例-事务
    public static function CasesAffair($casesLists,$casesCateId)
    {
        \DB::beginTransaction();
        try{
            foreach ($casesLists as $value){
                $casesId=self::casesMain($value,$casesCateId);
                self::addCasesBanner($value['casesbanner'],/*$casesId*/1);
                self::CasesDescAffair($value['lang'],/*$casesId*/1);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return true;
    }
    //案例-详情事务
    public static function CasesDescAffair($caseLang,$casesId)
    {
        \DB::beginTransaction();
        try{
            foreach ($caseLang as $value){
                self::addCasesDesc($value,$casesId);
                self::addCasesPhotos($value['photos'],$casesId);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return true;
    }
    //案例分类-详情
    public static function addCasesCateDesc($data,$casesCateId)
    {
        foreach ($data as $datum) {
            $desc=[
                'casesCateTitle'=> $datum['casesCateTitle'],
                'casesCateImage'=> $datum['casesCateImage'],
                'casesCateId'=> $casesCateId,
                'languageId'=> $datum['languageId'],
                'siteTitle'=> $datum['siteTitle'],
                'siteKeywords'=> $datum['siteKeywords'],
                'siteDescription'=> $datum['siteDescription'],
            ];
            //$model = new CasesCateDescModel();
            //$model->setDataByHumpArray($desc)->save();
            //return $model->getQueueableId();
        }
        return;
    }
    //案例-主
    public static function casesMain($data,$casesCateId)
    {
        $main = [
            'sort'=> $data['sort'],
            'recommend'=> $data['recommend'],
            'tableId'=> $data['tableId'],
            'casesHit'=> $data['casesHit'],
            'siteId'=> $data['siteId'],
            'casesCateId'=> $casesCateId,
            'isOn'=> $data['isOn'],
        ];
        //$model = new CasesModel();
        //$model->setDataByHumpArray($main)->save();
        //return $model->getQueueableId();
    }
    //案例-详情
    public static function addCasesDesc($data,$casesId)
    {
        $main = [
            'casesId'=> $casesId,
            'languageId'=> $data['languageId'],
            'casesTitle'=> $data['casesTitle'],
            'casesContent'=> $data['casesContent'],
            'casesImage'=> $data['casesImage'],
            'siteTitle'=> $data['siteTitle'],
            'siteKeywords'=> $data['siteKeywords'],
            'siteDescription'=> $data['siteDescription'],
            'casesDesc'=> $data['casesDesc'],
        ];
        //$model = new CasesDescModel();
        //$model->setDataByHumpArray($main)->save();
        //return $model->getQueueableId();
    }
    //案例-banner
    public static function addCasesBanner($data,$casesId)
    {
        foreach ($data as $key=>$value){
            $banner=[
                'casesId'=> $casesId,
                'casesBannerPic'=> $value['casesBannerPic'],
                'sort'=> $value['sort'],
            ];
            //$model = new CasesBannerModel();
            //$model->setDataByHumpArray($main)->save();
            //return $model->getQueueableId();
        }
        return;
    }
    //案例-图片
    public static function addCasesPhotos($data,$casesId)
    {
        foreach ($data as $key=>$value){
            $banner=[
                'casesId'=> $casesId,
                'casesBannerPic'=> $value['casesBannerPic'],
                'sort'=> $value['sort'],
            ];
            //$model = new CasesimageModel();
            //$model->setDataByHumpArray($main)->save();
            //return $model->getQueueableId();
        }
        return ;
    }

    //新闻
    public static function getNews($siteId)
    {
        $res = NewsModel::where('site_id',$siteId)->limit(3)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value){
                //banner
                $banner = self::getNewsBanner($value['newsId']);
                //desc
                $desc = self::getNewsDesc($value['newsId']);
                $res[$key]['newsbanner']=empty($banner) ? [] : $banner;
                $res[$key]['lang']=empty($desc) ? [] : $desc;
            }
        }
        return $res;
    }
    public static function getNewsBanner($newsId)
    {
        $banner = NewsBannerModel::where('news_id',$newsId)->getHumpArray(['*']);
        return $banner;
    }
    public static function getNewsDesc($newsId)
    {
        $res = NewsDescModel::where('news_id',$newsId)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                //image
                $image = self::getNewsImage($value['newsId']);
                $res[$key]['photos'] = empty($image) ? [] : $image;
            }
        }
        return $res;
    }
    public static function getNewsImage($newsId)
    {
        $res = NewsimageModel::where('news_id',$newsId)->getHumpArray(['*']);
        return $res;
    }
    //新闻--分类
    public static function getNewsCate($siteId)
    {
        $res = NewsCateModel::where('site_id',$siteId)->where('pid',0)->limit(20)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                $child = self::getNewsCateChild($siteId,$value['newsCateId']);
                $res[$key]['child'] = empty($child) ? [] : $child;
                $res2 = self::getNewsCateDesc($value['newsCateId']);
                $res[$key]['lang'] = empty($res2) ? [] : $res2;
                $news = self::getNews($siteId,$value['newsCateId']);
                $res[$key]['newsLists'] = empty($news) ? [] : $news;
            }
        }
        self::news($res);
        return $res;
    }
    public static function getNewsCateChild($siteId,$newsCateId)
    {
        $res = NewsCateModel::where('site_id',$siteId)->where('pid',$newsCateId)->limit(20)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                $res2 = self::getNewsCateDesc($value['newsCateId']);
                $res[$key]['lang'] = empty($res2) ? [] : $res2;
                //把案例拿出来一起添加
                $news = self::getNews($siteId,$value['newsCateId']);
                $res[$key]['newsLists'] = empty($news) ? [] : $news;
            }
        }
        return $res;
    }
    public static function getNewsCateDesc($newsCateId)
    {
        $res = NewsCateDescModel::where('news_cate_id',$newsCateId)->getHumpArray(['*']);
        return $res;
    }

    //拿到数据处理--news
    public static function news($data)
    {
        foreach ($data as $key=>$value){

            self::NewsParents($value);
        }
        return true;
    }
    //父级
    public static function NewsCate($parents)
    {
        $main=[
            "pid"=> $parents['pid'],
            "sort"=> $parents['sort'],
            //"newsCateId"=> $parents['casesCateId'],
            "tableId"=> $parents['tableId'],
            "siteId"=> $parents['siteId'],
            "isOn"=> $parents['isOn'],
        ];
        //$model = new NewsCateModel();
        //$model->setDataByHumpArray($main)->save();
        //return  = $model->getQueueableId();
    }
    //孩子
    public static function NewsCateChild($child,$newsCateId)
    {
        $main=[
            "pid"=> $child['pid'],
            "sort"=> $child['sort'],
            "newsCateId"=> $newsCateId,
            "tableId"=> $child['tableId'],
            "siteId"=> $child['siteId'],
            "isOn"=> $child['isOn'],
        ];
        //$model = new NewsCateModel();
        //$model->setDataByHumpArray($main)->save();
        //return $model->getQueueableId();
    }
    //新闻分诶-主-事务
    public static function NewsParents($parents)
    {
        \DB::beginTransaction();
        try{
            $newsCateId = self::NewsCate($parents);
            self::NewsCateAffair($parents['child'],$newsCateId);
            self::addNewsCateDesc($parents['lang'],$newsCateId);
            self::NewsAffair($parents['newsLists'],$newsCateId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return true;
    }
    //新闻分类-事务
    public static function NewsCateAffair($data,$newsCateId)
    {
        \DB::beginTransaction();
        try{
            $newsCateChildId = self::NewsCateChild($data,$newsCateId);
            self::addNewsCateDesc($data['lang'],/*$newsCateChildId*/1);
            self::NewsAffair($data['newsLists'],/*$newsCateChildId*/1);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return true;
    }
    //新闻-事务
    public static function NewsAffair($newsLists,$newsCateId)
    {
        \DB::beginTransaction();
        try{
            foreach ($newsLists as $value){
                $newsId=self::newsMain($value,$newsCateId);
                self::addNewsBanner($value['newsbanner'],/*$newsId*/1);
                self::NewsDescAffair($value['lang'],/*$newsId*/1);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return true;
    }
    //新闻-详情事务
    public static function NewsDescAffair($newsLang,$newsId)
    {
        \DB::beginTransaction();
        try{
            foreach ($newsLang as $value){
                self::addNewsDesc($value,$newsId);
                self::addNewsPhotos($value['photos'],$newsId);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return true;
    }
    //新闻分类-详情
    public static function addNewsCateDesc($data,$newsCateId)
    {
        foreach ($data as $datum) {
            $desc=[
                'newsCateTitle'=> $datum['newsCateTitle'],
                'newsCateImage'=> $datum['newsCateImage'],
                'newsCateId'=> $newsCateId,
                'languageId'=> $datum['languageId'],
                'siteTitle'=> $datum['siteTitle'],
                'siteKeywords'=> $datum['siteKeywords'],
                'siteDescription'=> $datum['siteDescription'],
            ];
            //$model = new NewsCateDescModel();
            //$model->setDataByHumpArray($desc)->save();
            //return $model->getQueueableId();
        }
        return;
    }
    //新闻-主
    public static function newsMain($data,$newsCateId)
    {
        $main = [
            'sort'=> $data['sort'],
            'recommend'=> $data['recommend'],
            'tableId'=> $data['tableId'],
            'newsHit'=> $data['newsHit'],
            'siteId'=> $data['siteId'],
            'newsCateId'=> $newsCateId,
            'isOn'=> $data['isOn'],
        ];
        //$model = new NewsModel();
        //$model->setDataByHumpArray($main)->save();
        //return $model->getQueueableId();
    }
    //新闻-详情
    public static function addNewsDesc($data,$newsId)
    {
        $main = [
            'newsId'=> $newsId,
            'languageId'=> $data['languageId'],
            'newsTitle'=> $data['newsTitle'],
            'newsAuthor'=> $data['newsAuthor'],
            'newsContent'=> $data['newsContent'],
            'newsThumb'=> $data['newsThumb'],
            'siteTitle'=> $data['siteTitle'],
            'siteKeywords'=> $data['siteKeywords'],
            'siteDescription'=> $data['siteDescription'],
            'newsDesc'=> $data['newsDesc'],
        ];
        //$model = new NewsDescModel();
        //$model->setDataByHumpArray($main)->save();
        //return $model->getQueueableId();
    }
    //新闻-banner
    public static function addNewsBanner($data,$newsId)
    {
        foreach ($data as $key=>$value){
            $banner=[
                'newsId'=> $newsId,
                'newsBannerPic'=> $value['newsBannerPic'],
                'sort'=> $value['sort'],
            ];
            //$model = new NewsBannerModel();
            //$model->setDataByHumpArray($banner)->save();
            //return $model->getQueueableId();
        }
        return;
    }
    //新闻-图片
    public static function addNewsPhotos($data,$casesId)
    {
        foreach ($data as $key=>$value){
            $banner=[
                'newsId'=> $casesId,
                'newsBannerPic'=> $value['newsBannerPic'],
                'sort'=> $value['sort'],
            ];
            //$model = new NewsimageModel();
            //$model->setDataByHumpArray($banner)->save();
            //return $model->getQueueableId();
        }
        return ;
    }

    //产品
    public static function getProduct($siteId)
    {
        $res = ProductModel::where('site_id',$siteId)->limit(3)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value){
                //banner
                $banner = self::getProductBanner($value['productId']);
                //desc
                $desc = self::getProductDesc($value['productId']);
                $res[$key]['probanner']=empty($banner) ? [] : $banner;
                $res[$key]['lang']=empty($desc) ? [] : $desc;
            }
        }
        return $res;
    }
    public static function getProductBanner($productId)
    {
        $banner = ProductBannerModel::where('product_id',$productId)->getHumpArray(['*']);
        return $banner;
    }
    public static function getProductDesc($productId)
    {
        $res = ProductDescModel::where('product_id',$productId)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                //image
                $image = self::getProductImage($value['productId']);
                $res[$key]['photos'] = empty($image) ? [] : $image;
                //属性
                $attribute = self::getProductAttribute($value['productId']);
                $res[$key]['attribute'] = empty($attribute) ? [] : $attribute;
            }
        }
        return $res;
    }
    public static function getProductImage($productId)
    {
        $res = ProductimageModel::where('product_id',$productId)->getHumpArray(['*']);
        return $res;
    }
    public static function getProductAttribute($productId)
    {
        $res = ProductAttributeModel::where('product_id',$productId)->getHumpArray(['*']);
        return $res;
    }
    //产品--分类
    public static function getProductCate($siteId)
    {
        $res = ProductCateModel::where('site_id',$siteId)->where('pid',0)->limit(3)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                $child = self::getProductCateChild($siteId,$value['productCateId']);
                $res[$key]['child'] = empty($child) ? [] : $child;
                $res2 = self::getProductCateDesc($value['productCateId']);
                $res[$key]['lang'] = empty($res2) ? [] : $res2;
                //把案例拿出来一起添加
                $product = self::getProduct($siteId,$value['productCateId']);
                $res[$key]['productLists'] = empty($product) ? [] : $product;
            }
        }
        self::product($res);
        return $res;
    }
    public static function getProductCateChild($siteId,$productCateId)
    {
        $res = ProductCateModel::where('site_id',$siteId)->where('pid',$productCateId)->limit(3)->getHumpArray(['*']);
        if(!empty($res)){
            foreach ($res as $key=>$value) {
                $res2 = self::getProductCateDesc($value['productCateId']);
                $res[$key]['lang'] = empty($res2) ? [] : $res2;
                //把案例拿出来一起添加
                $product = self::getProduct($siteId,$value['productCateId']);
                $res[$key]['productLists'] = empty($product) ? [] : $product;
            }
        }
        return $res;
    }
    public static function getProductCateDesc($productCateId)
    {
        $res = ProductCateDescModel::where('product_cate_id',$productCateId)->getHumpArray(['*']);
        return $res;
    }

    //拿到数据处理--product
    public static function product($data)
    {
        foreach ($data as $key=>$value){

            self::ProductParents($value);
        }
        return true;
    }
    //父级
    public static function ProductCate($parents)
    {
        $main=[
            "pid"=> $parents['pid'],
            "sort"=> $parents['sort'],
            //"productCateId"=> $parents['productCateId'],
            "tableId"=> $parents['tableId'],
            "siteId"=> $parents['siteId'],
            "isOn"=> $parents['isOn'],
            "searchNum"=> $parents['searchNum'],
        ];
        //$model = new ProductCateModel();
        //$model->setDataByHumpArray($main)->save();
        //return  = $model->getQueueableId();
    }
    //孩子
    public static function ProductCateChild($child,$productCateId)
    {
        $main=[
            "pid"=> $child['pid'],
            "sort"=> $child['sort'],
            "productCateId"=> $productCateId,
            "tableId"=> $child['tableId'],
            "siteId"=> $child['siteId'],
            "isOn"=> $child['isOn'],
            "searchNum"=> $child['searchNum'],
        ];
        //$model = new ProductCateModel();
        //$model->setDataByHumpArray($main)->save();
        //return $model->getQueueableId();
    }
    //产品分诶-主-事务
    public static function ProductParents($parents)
    {
        \DB::beginTransaction();
        try{
            $productCateId = self::ProductCate($parents);
            self::ProductCateAffair($parents['child'],$productCateId);
            self::addProductCateDesc($parents['lang'],$productCateId);
            self::ProductAffair($parents['productLists'],$productCateId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return true;
    }
    //产品分类-事务
    public static function ProductCateAffair($data,$productCateId)
    {
        \DB::beginTransaction();
        try{
            $productCateChildId = self::ProductCateChild($data,$productCateId);
            self::addProductCateDesc($data['lang'],/*$productCateChildId*/1);
            self::ProductAffair($data['productLists'],/*$productCateChildId*/1);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return true;
    }
    //产品-事务
    public static function ProductAffair($productLists,$productCateId)
    {
        \DB::beginTransaction();
        try{
            foreach ($productLists as $value){
                $productId=self::productMain($value,$productCateId);
                self::addProductBanner($value['productbanner'],/*$productId*/1);
                self::ProductDescAffair($value['lang'],/*$productId*/1);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return true;
    }
    //产品-详情事务
    public static function ProductDescAffair($productLang,$productId)
    {
        \DB::beginTransaction();
        try{
            foreach ($productLang as $value){
                self::addProductDesc($value,$productId);
                self::addProductPhotos($value['photos'],$productId);
                self::addProductAttribute($value['attribute'],$productId);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return true;
    }
    //产品分类-详情
    public static function addProductCateDesc($data,$productCateId)
    {
        foreach ($data as $datum) {
            $desc=[
                'productCateTitle'=> $datum['productCateTitle'],
                'productCateImage'=> $datum['productCateImage'],
                'productCateId'=> $productCateId,
                'languageId'=> $datum['languageId'],
                'siteTitle'=> $datum['siteTitle'],
                'siteKeywords'=> $datum['siteKeywords'],
                'siteDescription'=> $datum['siteDescription'],
            ];
            //$model = new ProductCateDescModel();
            //$model->setDataByHumpArray($desc)->save();
            //return $model->getQueueableId();
        }
        return;
    }
    //产品-主
    public static function productMain($data,$productCateId)
    {
        $main = [
            'sort'=> $data['sort'],
            'recommend'=> $data['recommend'],
            'tableId'=> $data['tableId'],
            'productHit'=> $data['productHit'],
            'siteId'=> $data['siteId'],
            'productCollect'=> $data['productCollect'],
            'productNumber'=> $data['productNumber'],
            'productCateId'=> $productCateId,
            'isOn'=> $data['isOn'],
        ];
        //$model = new ProductModel();
        //$model->setDataByHumpArray($main)->save();
        //return $model->getQueueableId();
    }
    //产品-详情
    public static function addProductDesc($data,$productId)
    {
        $main = [
            'productId'=> $productId,
            'languageId'=> $data['languageId'],
            'productDesc'=> $data['productDesc'],
            'productTitle'=> $data['productTitle'],
            'productContent'=> $data['productContent'],
            'productOldPrice'=> $data['productOldPrice'],
            'productSalePrice'=> $data['productSalePrice'],
            'productThumb'=> $data['productThumb'],
            'siteTitle'=> $data['siteTitle'],
            'siteKeywords'=> $data['siteKeywords'],
            'siteDescription'=> $data['siteDescription'],
            'productDesc'=> $data['productDesc'],
        ];
        //$model = new ProductDescModel();
        //$model->setDataByHumpArray($main)->save();
        //return $model->getQueueableId();
    }
    //产品-banner
    public static function addProductBanner($data,$productId)
    {
        foreach ($data as $key=>$value){
            $banner=[
                'productId'=> $productId,
                'productBannerPic'=> $value['productBannerPic'],
                'sort'=> $value['sort'],
            ];
            //$model = new ProductBannerModel();
            //$model->setDataByHumpArray($banner)->save();
            //return $model->getQueueableId();
        }
        return;
    }
    //产品-图片
    public static function addProductPhotos($data,$productId)
    {
        foreach ($data as $key=>$value){
            $banner=[
                'productId'=> $productId,
                'productImagePic'=> $value['productBannerPic'],
                'languageId'=> $value['languageId'],
                'productImageDesc'=> $value['productImageDesc'],
                'sort'=> $value['sort'],
            ];
            //$model = new ProductimageModel();
            //$model->setDataByHumpArray($banner)->save();
            //return $model->getQueueableId();
        }
        return ;
    }
    //产品-属性
    public static function addProductAttribute($data,$productId)
    {
        foreach ($data as $key=>$value){
            $Attribute=[
                'productId'=> $productId,
                'languageId'=> $value['languageId'],
                'attributeName'=> $value['attributeName'],
                'attributeContent'=> $value['attributeContent'],
            ];
            //$model = new ProductAttributeModel();
            //$model->setDataByHumpArray($Attribute)->save();
            //return $model->getQueueableId();
        }
        return ;
    }


}