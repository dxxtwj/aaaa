<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Works;

use App\Logic\Gallery\GalleryLogic;
use App\Logic\User\LoginLogic;
use App\Model\Works\VoteModel;
use App\Model\Works\WorksDescModel;
use App\Model\Works\WorksModel;
use App\Model\Works\WorksPraiseModel;
use DdvPhp\DdvPage;
use Illuminate\Contracts\Redis;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class WorksLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $GalleryId=0;
        if(!empty($data['worksThumb'])){
            $GalleryId = GalleryLogic::getGalleryId($data['worksThumb']);
        }
        //查这个编号是否存在
        if(!empty($data['worksNumber'])){
            self::checkNumber($data['worksNumber']);
        }
        $main=[
            'worksCateId' => $data['worksCateId'],
            'galleryId' => $GalleryId,
            'writerId' => $data['writerId'],
            'sort' => $data['sort'],
            'worksNumber'=>empty($data['worksNumber']) ? '' : $data['worksNumber'],//编号
            'worksThumb' => empty($data['worksThumb']) ? '' : $data['worksThumb'],
            'isOn' => $data['isOn'],
        ];
        self::addAffair($main,$data);
    }
    //检测编号是否相同
    public static function checkNumber($number)
    {
        $works = WorksModel::whereSiteId()
            ->where('works_number', $number)
            ->firstHump(['*']);
        if(!empty($works)){
            throw new RJsonError("该编号已存在", 'WORKS_NUMBER');
        }
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $worksId=self::addWorks($main);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'worksId' => $worksId,
                    'worksTitle' => $value['worksTitle'],
                    'worksContent' => empty($value['worksContent']) ? '' : $value['worksContent'],
                    'worksDesc' => empty($value['worksDesc']) ? '' : $value['worksDesc'],
                    'languageId'=>$value['languageId'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::addWorksDesc($desc);
                //编辑器里面的文件
                if(isset($value['contentFile'])){
                    foreach ($value['contentFile'] as $item){
                        GalleryLogic::getGalleryId($item['galleryUrl']);
                    }
                }
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
        }
        return true;
    }

    //添加主表
    public static function addWorks ($data=[])
    {
        //检查排序
        self::Sort($data['worksCateId'],$data['sort']);
        $model = new WorksModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();
    }

    //查排序是否唯一
    public static function Sort ($worksCateId,$sort)
    {
        $res = WorksModel::whereSiteId()->where('works_cate_id',$worksCateId)->where('sort',$sort)->firstHump(['*']);
        $sort = WorksModel::whereSiteId()->where('works_cate_id',$worksCateId)->orderby('sort','DESC')->firstHump(['sort']);
        if(!empty($res)){
            throw new RJsonError("排序重复,目前排序为:"."$sort", 'WORKS_SORT');
        }
    }

    //添加详细表
    public static function addWorksDesc ($data=[])
    {
        $model = new WorksDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //获取列表
    /*public static function getWorksListTest($data)
    {
        //名称和number
        if(isset($data['worksSearch'])){
            $res = self::getWorksListsBySearch($data['worksSearch']);
        }else{
            $name=[];
            $where=[];
            //是否显示
            $showName=[];
            $show=[];
            if(isset($data['isOn'])){
                $showName='works.is_on';
                $show=$data['isOn'];
            }
            if(isset($data['languageId'])){
                $name='works_description.language_id';
                $where=$data['languageId'];
            }
            if (isset($data['worksTitle'])) {
                $worksTitle = '%' . $data['worksTitle'] . '%';
            } else {
                $worksTitle = '%';
            }
            $name3=[];
            $where3=[];
            if(isset($data['worksNumber'])){
                $name3='works.works_number';
                $where3=$data['worksNumber'];
            }
            if(isset($data['worksCateId'])){
                $model = new WorksModel();
                if(!empty($data['api'])){
                    $model = $model->where('news_description.language_id',$data['languageId']);
                }
                $name2='works.works_cate_id';
                $cateId = WorksCateLogic::getCateId($data['worksCateId']);
                $worksLists = $model->whereSiteId()
                    ->where($name,$where)
                    ->where($name3,$where3)
                    ->where($showName,$show)
                    ->whereIn($name2,$cateId)
                    ->where('works_description.works_title', 'like', $worksTitle)
                    ->orderby('works.sort','DESC')
                    ->leftjoin('works_description', 'works.works_id', '=', 'works_description.works_id')
                    ->select([
                        'works.*',
                        'works_description.*',
                    ])
                ;
            }else{
                $worksLists = WorksModel::whereSiteId()
                    ->where($name,$where)
                    ->where($name3,$where3)
                    ->where($showName,$show)
                    ->where('works_description.works_title', 'like', $worksTitle)
                    ->orderby('works.sort','DESC')
                    ->leftjoin('works_description', 'works.works_id', '=', 'works_description.works_id')
                    ->select([
                        'works.*',
                        'works_description.*',
                    ]);
            }
            $res = $worksLists->getDdvPageHumpArray(true);
        }
        if(isset($res)){
            foreach ($res['lists'] as $key=>$value){
                //点赞
                $count = self::getPraiseCount($value['worksId']);
                $res['lists'][$key]['worksClick']=$count;
                //投票
                $voteCount = self::getVoteCount($value['worksId']);
                $res['lists'][$key]['worksVote']=$voteCount;
                //投票排名
                $number = self::editWorksVote($value['worksId'],$value['worksCateId']);
                $res['lists'][$key]['ranking']=$number;
                //作者名称
                $writer=WriterLogic::getWriter($value['writerId']);
                $res['lists'][$key]['name'] =empty($writer['name']) ? '' : $writer['name'];
                $res['lists'][$key]['headimg'] =empty($writer['headimg']) ? '' : $writer['headimg'];
                //分类名称
                $name=WorksCateLogic::getWorksCateName($value['worksCateId'],$value['languageId']);
                $res['lists'][$key]['worksCateTitle']=empty($name) ? '' : $name;
            }
            if(!empty($data['worksCateId']) && !empty($data['api'])){
                $sort = array_column($res['lists'], 'ranking');
                array_multisort($sort, SORT_ASC, $res['lists']);
            }
        }
        return $res;
    }*/
    //获取列表
    public static function getWorksList($data)
    {
        //名称和number
        if(isset($data['worksSearch'])){
            $res = self::getWorksListsBySearch($data['worksSearch']);
        }else{
            $model = new WorksModel();
            if(isset($data['isOn'])){
                $model = $model->where('works.is_on',$data['isOn']);
            }
            if(isset($data['languageId'])){
                $model = $model->where('works_description.language_id',$data['languageId']);
            }
            if (isset($data['worksTitle'])) {
                $model = $model->where('works_description.works_title', 'like','%' . $data['worksTitle'] . '%');
            }
            if(isset($data['worksNumber'])){
                $model = $model->where('works.works_number',$data['worksNumber']);
            }
            if(isset($data['worksCateId'])){
                $cateId = WorksCateLogic::getCateId($data['worksCateId']);
                $model = $model->whereIn('works.works_cate_id',$cateId);
            }
            $worksLists = $model->whereSiteId()
                ->orderby('works.sort','DESC')
                ->leftjoin('works_description', 'works.works_id', '=', 'works_description.works_id')
                ->select([
                    'works.*',
                    'works_description.*',
                ])
            ;
            $res = $worksLists->getDdvPageHumpArray(true);
        }
        if(isset($res)){
            foreach ($res['lists'] as $key=>$value){
                //点赞
                $count = self::getPraiseCount($value['worksId']);
                $res['lists'][$key]['worksClick']=$count;
                //投票
                /*$voteCount = self::getVoteCount($value['worksId']);
                $res['lists'][$key]['worksVote']=$voteCount;*/
                //作者名称
                $writer=WriterLogic::getWriter($value['writerId']);
                $res['lists'][$key]['name'] =empty($writer['name']) ? '' : $writer['name'];
                $res['lists'][$key]['headimg'] =empty($writer['headimg']) ? '' : $writer['headimg'];
                //分类名称
                $name=WorksCateLogic::getWorksCateName($value['worksCateId'],$value['languageId']);
                $res['lists'][$key]['worksCateTitle']=empty($name) ? '' : $name;
            }
        }
        return $res;
    }

    public static function getWorksVote()
    {
        /*$model = new WorksModel();
        $works = $model->whereSiteId()->getHumpArray(['works_id']);
        foreach ($works as $key=>$value){
            $voteCount=self::worksVoteByWorksId($value['worksId']);
            $works[$key]['worksVote'] = $voteCount;
        }
        $sort = array_column($works, 'worksVote');
        rsort($sort);
        $arr2=array_unique($sort);
        foreach ($works as $key1=>$value1){
            foreach ($arr2 as $k=>$v){
                if($value1['worksVote']==$v){
                    $count =$k+1;
                    $works[$key1]['ranking']=$count;
                }
            }
        }
        self::editWorksVoteCount($works);*/
        return [];
    }

    public static function editWorksVoteCount($data)
    {
        foreach ($data as $datum) {
            $model = new WorksModel();
            $res['ranking'] = $datum['ranking'];
            $res['worksVote'] = $datum['worksVote'];
            $model->where('works_id', $datum['worksId'])->updateByHump($res);
        }
        return;
    }

    public static function getWorksListsBySearch($worksNumber)
    {
        $worksTitle = '%' . $worksNumber . '%';
        $Number = '%' . $worksNumber . '%';
        $worksLists = WorksModel::whereSiteId()
            ->where('is_on',1)
            ->where('works.works_number', 'like', '%'. $Number.'%')
            ->orderby('works.sort','DESC')
            ->leftjoin('works_description', 'works.works_id', '=', 'works_description.works_id')
            ->getHumpArray([
                'works.*',
                'works_description.*',
            ]);
        $works = WorksModel::whereSiteId()
            ->where('works_description.works_title', 'like','%' . $worksTitle . '%')
            ->orderby('works.sort','DESC')
            ->leftjoin('works_description', 'works.works_id', '=', 'works_description.works_id')
            ->getHumpArray([
                'works.*',
                'works_description.*',
            ]);
        $res2 = array_merge($worksLists,$works);
        $arr=[];
        foreach ($res2 as $key=>$value){
            $arr[]=$value['worksId'];
        }
        $arr2=array_unique($arr);
        $worksList = WorksModel::whereSiteId()
            ->whereIn('works.works_id',$arr2)
            ->orderby('works.sort','DESC')
            ->leftjoin('works_description', 'works.works_id', '=', 'works_description.works_id')
            ->select([
                'works.*',
                'works_description.*',
            ]);
        $res3 = $worksList->getDdvPageHumpArray(true);
        return $res3;
    }

    //获取分类下的10条作品投票排名
    public static function getCateWorksVote($worksCateId,$languageId,$number)
    {
        $cateId = WorksCateLogic::getCateId($worksCateId);
        $model = new WorksModel();
        $res = $model->whereSiteId()
            ->where('works_description.language_id',$languageId)
            ->whereIn('works.works_cate_id',$cateId)
            ->where('works.is_on',1)
            ->orderBy('works.ranking','ASC')
            ->limit($number)
            ->leftjoin('works_description', 'works.works_id', '=', 'works_description.works_id')
            ->getHumpArray([
                'works.works_id',
                'works.works_cate_id',
                'works.works_vote',
                'works.writer_id',
                'works.ranking',
                'works_description.language_id',
                'works_description.works_title',
            ]);
        foreach ($res as $key=>$value){
            //作者名称
            $writer=WriterLogic::getWriter($value['writerId']);
            $res[$key]['name'] =empty($writer['name']) ? '' : $writer['name'];

        }
        return $res;
    }

    //获取住单条
    public static function getWorksOne($worksId)
    {
        $works = WorksModel::whereSiteId()
            ->where('works_id', $worksId)
            ->firstHump(['*']);
        if(isset($works)){
            //作者名称
            $writer=WriterLogic::getWriter($works['writerId']);
            $works['name'] =empty($writer['name']) ? '' : $writer['name'];
            $works['headimg'] =empty($writer['headimg']) ? '' : $writer['headimg'];
            //点赞
            $count = self::getPraiseCount($works['worksId']);
            $works['worksClick']=$count;
            //投票
            $voteCount = self::getVoteCount($works['worksId']);
            $works['worksVote']=$voteCount;
            //详情
            $worksDesc=self::getWorksDesc($works['worksId']);
            $works['lang']=empty($worksDesc) ? [] : $worksDesc;
        }
        return $works;
    }

    //获取详情全部
    public static function getWorksDesc($worksId)
    {
        $worksDesc = WorksDescModel::where('works_id', $worksId)
            ->getHump(['*']);
        return $worksDesc;
    }

    //查主表单条
    public static function getCateWorks($worksCateId)
    {
        $Cases = WorksModel::where('works_cate_id', $worksCateId)
            ->firstHump(['*']);
        return $Cases;
    }

    //编辑全部
    public static function editAll ($data=[])
    {
        $GalleryId=0;
        if(!empty($data['worksThumb'])){
            $GalleryId = GalleryLogic::getGalleryId($data['worksThumb']);
        }
        //查这个编号是否存在
        if(!empty($data['worksNumber'])){
            self::checkNumberTwo($data['worksNumber'],$data['worksId']);
        }
        $main=[
            'worksCateId' => $data['worksCateId'],
            'writerId' => $data['writerId'],
            'galleryId' => $GalleryId,
            'sort' => $data['sort'],
            'worksNumber'=>empty($data['worksNumber']) ? '' : $data['worksNumber'],//编号
            'worksThumb' => empty($data['worksThumb']) ? '' : $data['worksThumb'],
            'isOn' => $data['isOn'],
        ];
        self::editAffair($main,$data);
    }
    //检测编号是否相同
    public static function checkNumberTwo($number,$worksId)
    {
        $works = WorksModel::whereSiteId()
            ->where('works_id','<>',$worksId)
            ->where('works_number', $number)
            ->firstHump(['*']);
        if(!empty($works)){
            throw new RJsonError("该编号已存在", 'WORKS_NUMBER');
        }
    }
    //修改事务
    public static function editAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $worksId=$data['worksId'];
            self::editWorks($main,$worksId);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'worksId' => $worksId,
                    'worksTitle' => $value['worksTitle'],
                    'worksContent' => empty($value['worksContent']) ? '' : $value['worksContent'],
                    'worksDesc' => empty($value['worksDesc']) ? '' : $value['worksDesc'],
                    'languageId'=>$value['languageId'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::editWorksDesc($desc,$worksId,$value['languageId']);
                //编辑器里面的文件
                if(isset($value['contentFile'])){
                    foreach ($value['contentFile'] as $item){
                        GalleryLogic::getGalleryId($item['galleryUrl']);
                    }
                }
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
        }
        return true;
    }


    //编辑主表
    public static function editWorks($data=[],$worksId)
    {
        //检查排序
        self::SortTwo($data['worksCateId'],$data['sort'],$worksId);
        WorksModel::where('works_id', $worksId)->updateByHump($data);

    }

    //是否显示
    public static function isShow($data=[],$worksId)
    {
        WorksModel::where('works_id', $worksId)->updateByHump($data);
    }

    //查排序是否唯一
    public static function SortTwo ($worksCateId,$sort,$worksId)
    {
        $res = WorksModel::whereSiteId()->where('works_cate_id',$worksCateId)->where('works_id','<>',$worksId)->where('sort',$sort)->firstHump(['*']);
        if(!empty($res)){
            $sort = WorksModel::whereSiteId()->where('works_cate_id',$worksCateId)->orderby('sort','DESC')->firstHump(['sort']);
            throw new RJsonError("排序重复,目前排序为:"."$sort", 'WORKS_SORT');
        }
    }

    //编辑详细表
    public static function editWorksDesc($data=[],$worksId,$languageId)
    {
        WorksDescModel::where('works_id', $worksId)->where('language_id',$languageId)->updateByHump($data);
    }

    //根据作者获取数据
    public static function getWorksByWriter($writerId)
    {
        $works = WorksModel::whereSiteId()->where('writer_id', $writerId)->firstHumpArray(['*']);
        return $works;
    }

    //删除事务
    public static function delAffair($worksId)
    {
        \DB::beginTransaction();
        try{
            self::deleteWorks($worksId);
            self::deleteWorksDesc($worksId);
            self::deleteWorksPraise($worksId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }


    //删除主
    public static function deleteWorks($worksId)
    {
        (new WorksModel())->where('works_id', $worksId)->delete();
    }
    //删除详
    public static function deleteWorksDesc($worksId)
    {
        (new WorksDescModel())->where('works_id', $worksId)->delete();
    }
    //删除点赞
    public static function deleteWorksPraise($worksId)
    {
        (new WorksPraiseModel())->where('works_id', $worksId)->delete();
    }


    //=========================前端调用单条===============================

    //查单条
    public static function getWorks($worksId,$languageId)
    {
        $works = WorksModel::where('works.works_id', $worksId)
            ->where('works_description.language_id',$languageId)
            ->leftjoin('works_description', 'works.works_id', '=', 'works_description.works_id')
            ->leftjoin('works_category_description', 'works.works_cate_id', '=', 'works_category_description.works_cate_id')
            ->firstHumpArray([
                'works.*',
                'works_description.*',
                'works_category_description.works_cate_title',
            ]);
        if(isset($works)){
            //点击量
            $hit['worksBrowse']=$works['worksBrowse']+1;
            self::Click($hit,$works['worksId']);
            //点赞
            $count = self::getPraiseCount($works['worksId']);
            $works['worksClick']=$count;
            //投票
            $voteCount = self::getVoteCount($works['worksId']);
            $works['worksVote']=$voteCount;
        }
        return $works;
    }
    //获取上一条数据
    public static function getLast($worksCateId,$languageId,$sort)
    {
        $model = new WorksModel();
        $last= $model->whereSiteId()
            ->where('works.works_cate_id', $worksCateId)
            ->where('works_description.language_id',$languageId)
            ->where('works.sort','>',$sort)
            ->orderby('works.sort','ASC')
            ->leftjoin('works_description', 'works.works_id', '=','works_description.works_id')
            ->firstHump([
                'works.works_id',
                'works_description.works_title',
            ]);
        return $last;
    }
    //获取下一条数据
    public static function getNext($worksCateId,$languageId,$sort)
    {
        $model = new WorksModel();
        $next= $model->whereSiteId()
            ->where('works.works_cate_id', $worksCateId)
            ->where('works_description.language_id',$languageId)
            ->where('works.sort','<',$sort)
            ->orderby('works.sort','DESC')
            ->leftjoin('works_description', 'works.works_id', '=', 'works_description.works_id')
            ->firstHump([
                'works.works_id',
                'works_description.works_title',
            ]);

        return $next;
    }

    //浏览量
    public static function Click($data,$worksId){
        WorksModel::where('works_id', $worksId)->updateByHump($data);
    }

    //点赞
    public static function Praise($worksId,$ip)
    {
        //今天的凌晨时间
        $time = strtotime(date('Y-m-d'))-28800;
        $praise = self::getPraise($worksId,$ip);
        if(!empty($praise) && $praise['praiseTime']==$time){
            throw new RJsonError("今天已点赞过，请明天继续，感谢您的支持", 'WORKS_PRAISE');
        }
        $data=[
            'worksId'=>$worksId,
            'worksIp'=>$ip,
            'praiseTime'=>$time
        ];
        $model = new WorksPraiseModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return;
    }
    public static function getPraise($worksId,$ip)
    {
        $praise = WorksPraiseModel::whereSiteId()->where('works_id', $worksId)->where('works_ip',$ip)->orderBy('praise_time','DESC')->firstHumpArray(['*']);
        return $praise;
    }
    public static function getPraiseCount($worksId)
    {
        $praise = WorksPraiseModel::whereSiteId()->where('works_id', $worksId)->count();
        return $praise;
    }

    //投票
    public static function Vote($worksId,$uid,$ip)
    {
        //今天的凌晨时间
        $time = strtotime(date('Y-m-d'))-28800;
        $vote = self::getVote($worksId,$uid);
        if(!empty($vote) && $vote['voteTime']==$time){
            throw new RJsonError("同一作品每天只能投票一次哦", 'WORKS_VOTE');
        }
        $data=[
            'voteId'=>$worksId,
            'uid'=>$uid,
            'voteIp'=>$ip,
            'voteTime'=>$time
        ];
        $model = new VoteModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return;
    }
    public static function getVote($worksId,$uid)
    {
        $vote = VoteModel::whereSiteId()->where('vote_id', $worksId)->where('uid',$uid)->orderBy('vote_time','DESC')->firstHumpArray(['*']);
        return $vote;
    }
    public static function getVoteCount($worksId)
    {
        $vote = VoteModel::whereSiteId()->where('vote_id', $worksId)->count();
        return $vote;
    }
    //投票活动是否已经结束
    public static function voteOver($worksId,$languageId)
    {
        $time=time();
        $res = self::getWorks($worksId,$languageId);
        if(!empty($res)){
            //获取分类
            $cate = WorksCateLogic::getWorksCate($res['worksCateId'],$languageId);
            if(!empty($cate)){
                if($cate['endTime'] < $time){
                    throw new RJsonError("投票活动已经结束，感谢您的支持！", 'VOTE_OVER');
                }
            }
        }
    }

    //作品总数
    public static function worksCount($data)
    {
        if(!empty($data)){
            $works = WorksModel::whereSiteId()->where('works_cate_id',$data['worksCateId'])->count();
        }else{
            $works = WorksModel::whereSiteId()->count();
        }
        return $works;
    }
    //浏览数
    public static function worksBrowse()
    {
        $count=0;
        $works = WorksModel::whereSiteId()->getHumpArray();
        if(!empty($works)){
            foreach ($works as $value){
                $count +=$value['worksBrowse'];
            }
        }
        return $count;
    }
    //投票数
    public static function worksVote()
    {
        $vote = VoteModel::whereSiteId()->count();
        return $vote;
    }
    public static function worksVoteByWorksId($worksId)
    {
        $vote = VoteModel::whereSiteId()->where('vote_id',$worksId)->count();
        return $vote;
    }
    //修改作品票数
    public static function worksVoteNumber($number,$worksId)
    {
        $data['worksVote']=empty($number) ? 0 : $number;
        WorksModel::where('works_id', $worksId)->updateByHump($data);
    }

    //投票排名
    public static function editWorksVote($worksId,$worksCateId)
    {
        //先获取全部作品ID
        $works=WorksModel::whereSiteId()->getHumpArray(['works_id']);
        foreach ($works as $key=>$value){
            $count = self::getVoteCount($value['worksId']);
            self::worksVoteNumber($count,$value['worksId']);
        }
        /*$count = self::getVoteCount($worksId);
        $worksLists=WorksModel::whereSiteId()->where('works_cate_id',$worksCateId)->where('works_vote','>',$count)->orderby('works_vote','DESC')->getHumpArray(['works_vote']);
        $res=[];
        foreach($worksLists as $k=>$val){
            $res[]=$val['worksVote'];
        }
        $arr2=array_unique($res);
        $number=count($arr2);
        $count2=array_slice($arr2,-1,1);;
        if($count!=$count2){
            $number=$number+1;
        }*/
        $number=0;
        $worksLists = self::getWorksByCateId($worksCateId);
        foreach ($worksLists as $k=>$val){
            if($worksId==$val['worksId']){
                $number=$val['ranking'];
            }

        }
        return $number;
    }

    //获取该类下的所有作品
    public static function getWorksByCateId($worksCateId)
    {
        $works = WorksModel::where('works.works_cate_id', $worksCateId)
            ->orderby('works.works_vote','DESC')
            ->leftjoin('works_description', 'works.works_id', '=', 'works_description.works_id')
            ->leftjoin('writer', 'works.writer_id', '=', 'writer.writer_id')
            ->leftjoin('works_category_description', 'works.works_cate_id', '=', 'works_category_description.works_cate_id')
            ->getHumpArray([
                'works.works_id',
                'works.works_vote',
                'works.works_number',
                'works_description.works_title',
                'writer.name',
                'works_category_description.works_cate_title',
            ]);
        $sort = array_column($works, 'worksVote');
        $arr2=array_unique($sort);
        foreach ($works as $key=>$value){
            foreach ($arr2 as $k=>$v){
                if($value['worksVote']==$v){
                    $count =$k+1;
                    $works[$key]['ranking']=$count;
                }
            }
        }
        return $works;
    }

    //检测该类下是否有作品
    public static function checkWorksByCateId($worksCateId)
    {
        $works = WorksModel::where('works_cate_id', $worksCateId)->firstHumpArray();
        return $works;
    }


    public static function redisTest()
    {
        /*$redis = new \Redis();
        $redis->set('test',"11111111111");
        $redis->set('shen',"666666");*/
        $redis = new \Redis();
        $ret = $redis->connect("r-wz9057b6a7f2c234.redis.rds.aliyuncs.com",6379);
        $redis->set("user","xuwenqiang");
        $res =  $redis->get("user");
        return $res;
    }
    public static function getRedisTest()
    {
        $redis = new \Redis();
        $result = $redis->get('shen');
        return $result;
    }

}