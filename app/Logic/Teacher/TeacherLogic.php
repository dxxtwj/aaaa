<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Teacher;

use App\Logic\Gallery\GalleryLogic;
use App\Model\Teacher\TeacherDescModel;
use App\Model\Teacher\TeacherModel;
use App\Model\Teacher\TeacherToClassModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class TeacherLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $main=[
            'isOn' => $data['isOn'],
            'sort' => $data['sort'],
            'recommend' => $data['recommend'],
        ];
        self::addAffair($main,$data);
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['headimg'])){
                $GalleryId = GalleryLogic::getGalleryId($data['headimg']);
            }
            $teacherId = self::addTeacher($main);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'teacherId' => $teacherId,
                    'galleryId'=>$GalleryId,
                    'name' => empty($value['name']) ? '' : $value['name'],
                    'position' => empty($value['position']) ? '' : $value['position'],
                    'description' => empty($value['description']) ? '' : $value['description'],
                    'headimg' => empty($data['headimg']) ? '' : $data['headimg'],
                    'languageId'=>$value['languageId'],
                    'content' => empty($value['content']) ? '' : $value['content'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::addTeacherDesc($desc);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //添加主表
    public static function addTeacher ($data=[])
    {
        $model = new TeacherModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //添加详细表
    public static function addTeacherDesc ($data=[])
    {
        $model = new TeacherDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //教师和课程关联
    public static function addTeacherToClass($teacher,$classId)
    {
        foreach ($teacher as $key=>$value){
            $data=[
                'teacherId'=>$value['teacherId'],
                'classId'=>$classId,
            ];
            self::addClass($data);
        }
    }
    public static function addClass($data)
    {
        $model = new TeacherToClassModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return;
    }
    public static function deleteTeacherToClass($classId)
    {
        $model = new TeacherToClassModel();
        $model->setSiteId()->where('class_id',$classId)->delete();
        return;
    }

    //获取列表
    public static function getList($data)
    {
        $model = new TeacherModel();
        $model = $model->whereSiteId();
        if(isset($data['languageId'])){
            $model = $model->where('teacher_description.language_id',$data['languageId']);
        }
        if(isset($data['isOn'])){
            $model = $model->where('teacher.is_on',$data['isOn']);
        }
        if(isset($data['name'])){
            $model = $model->where('teacher_description.name', 'like', '%' . $data['name'] . '%');
        }
        $model = $model->orderby('teacher.sort','DESC');
        $Lists = $model->leftjoin('teacher_description', 'teacher.teacher_id', '=', 'teacher_description.teacher_id')
            ->select([
                'teacher.*',
                'teacher_description.*',
            ]);
        $res = $Lists->getDdvPageHumpArray(true);
        return $res;
    }

    public static function getListsByName($data)
    {
        $model = new TeacherModel();
        $model = $model->whereSiteId();
        $model = $model->where('teacher_description.language_id',1);
        if(isset($data['name'])){
            $model = $model->where('teacher_description.name', 'like', '%' . $data['name'] . '%');
        }
        $Lists = $model->leftjoin('teacher_description', 'teacher.teacher_id', '=', 'teacher_description.teacher_id')
            ->getHumpArray([
                'teacher_description.teacherId',
                'teacher_description.name',
            ]);
        return $Lists;
    }

    //获取住单条
    public static function getTeacherOne($teacherId)
    {
        $model = new TeacherModel();
        $teacher = $model->where('teacher_id', $teacherId)->firstHump(['*']);
        if(isset($teacher)){
            $res=self::getTeacherDesc($teacher['teacherId']);
            if(isset($res)){
                foreach ($res as $key=>$value){
                    $teacher['headimg']=empty($value['headimg']) ? '' : $value['headimg'];
                }
                $teacher['lang']=empty($res) ? [] : $res;
            }
        }
        return $teacher;
    }
    //获取详情全部
    public static function getTeacherDesc($teacherId)
    {
        $model = new TeacherDescModel();
        $res = $model->where('teacher_id', $teacherId)
            ->getHump(['*']);
        return $res;
    }

    //获取教师课程数组
    public static function getTeacherByClassId($classId)
    {
        //获取教师id
        $res = self::getTeacherId($classId);
        //获取教师名称
        if(!empty($res)){
            foreach ($res as $key=>$value){
                $name = self::geTeacherName($value['teacherId']);
                $res[$key]['name'] = empty($name) ?? '';
            }
        }
        return $res;
    }
    public static function getTeacherId($classId)
    {
        $model = new TeacherToClassModel();
        $res = $model->whereSiteId()->where('class_id',$classId)->getHumpArray(['teacher_id']);
        return $res;
    }
    public static function geTeacherName($teacherId)
    {
        $model = new TeacherDescModel();
        $res = $model->where('teacher_id',$teacherId)->firstHumpArray(['name']);
        return $res['name'];
    }

    //编辑全部
    public static function editAll ($data=[])
    {
        $main=[
            'sort' => $data['sort'],
            'isOn' => $data['isOn'],
            'recommend' => $data['recommend'],
        ];
        self::editAffair($main,$data);
    }

    //修改事务
    public static function editAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['headimg'])){
                $GalleryId = GalleryLogic::getGalleryId($data['headimg']);
            }
            $teacherId=$data['teacherId'];
            self::editTeacher($main,$teacherId);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'galleryId'=>$GalleryId,
                    'name' => empty($value['name']) ? '' : $value['name'],
                    'position' => empty($value['position']) ? '' : $value['position'],
                    'description' => empty($value['description']) ? '' : $value['description'],
                    'headimg' => empty($data['headimg']) ? '' : $data['headimg'],
                    'content' => empty($value['content']) ? '' : $value['content'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                //有多语言修改
                self::editTeacherDesc($desc,$teacherId,$value['languageId']);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //编辑主表
    public static function editTeacher($data=[],$teacherId)
    {
        $model = new TeacherModel();
        $model->where('teacher_id', $teacherId)->updateByHump($data);
    }

    //是否显示
    public static function isShow($data=[],$teacherId)
    {
        $model = new TeacherModel();
        $model->where('teacher_id', $teacherId)->updateByHump($data);
    }

    //编辑详细表----有多语言的修改
    public static function editTeacherDesc($data=[],$teacherId,$languageId)
    {
        $model = new TeacherDescModel();
        $model->where('teacher_id', $teacherId)->where('language_id',$languageId)->updateByHump($data);
    }

    //删除事务
    public static function delAffair($teacherId)
    {
        \DB::beginTransaction();
        try{
            self::deleteTeacher($teacherId);
            self::deleteTeacherDesc($teacherId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //删除主
    public static function deleteTeacher($teacherId)
    {
        (new TeacherModel())->where('teacher_id', $teacherId)->delete();
    }
    //删除详
    public static function deleteTeacherDesc($teacherId)
    {
        (new TeacherDescModel())->where('teacher_id', $teacherId)->delete();
    }

    //=========================前端调用单条==============================

    //查单条
    public static function getTeacher($teacherId,$languageId)
    {
        $model = new TeacherModel();
        $res = $model->where('teacher.teacher_id', $teacherId)
            ->where('teacher_description.language_id',$languageId)
            ->leftjoin('teacher_description', 'teacher.teacher_id', '=', 'teacher_description.teacher_id')
            ->firstHumpArray([
                'teacher.*',
                'teacher_description.*',
            ]);
        return $res;
    }

    //获取教师课程数组
    public static function getTeacherApiByClassId($classId,$languageId)
    {
        //获取教师id
        $res = self::getTeacherId($classId);
        //获取教师名称
        if(!empty($res)){
            foreach ($res as $key=>$value){
                $teacher = self::getTeacher($value['teacherId'],$languageId);
                $res[$key]['sort'] = empty($teacher['sort']) ?? '';
                $res[$key]['headimg'] = empty($teacher['headimg']) ?? '';
                $res[$key]['name'] = empty($teacher['name']) ?? '';
                $res[$key]['position'] = empty($teacher['position']) ?? '';
                $res[$key]['description'] = empty($teacher['description']) ?? '';
            }
        }
        return $res;
    }

    //获取推荐
    public static function recommend($number,$languageId)
    {
        $model = new TeacherModel();
        $model = $model->whereSiteId();
        $model = $model->where('teacher.is_on',1);
        $model = $model->where('teacher.recommend', 1);
        $model = $model->where('teacher_description.language_id',$languageId);
        $model = $model->limit($number);
        $res = $model->leftjoin('teacher_description', 'teacher.teacher_id', '=', 'teacher_description.teacher_id')
            ->getHumpArray([
                'teacher.teacher_id',
                'teacher_description.name',
                'teacher_description.headimg',
                'teacher_description.position',
                'teacher_description.description',
            ]);
        return $res;
    }

    //获取课程老师
    public static function getTeacherByClass($classId,$languageId)
    {
        //获取教师id
        $res = self::getTeacherId($classId);
        $ids = array_values($res);
        $teacher = self::getTeacherLists($ids,$languageId);
        return $teacher;
    }
    public static function getTeacherLists($ids,$languageId)
    {
        $model = new TeacherModel();
        $model = $model->whereSiteId();
        $model = $model->whereIn('teacher.teacher_id',$ids);
        $model = $model->where('teacher_description.language_id',$languageId);
        $res = $model->leftjoin('teacher_description', 'teacher.teacher_id', '=', 'teacher_description.teacher_id')
            ->getHumpArray([
                'teacher.teacher_id',
                'teacher_description.name',
                'teacher_description.headimg',
                'teacher_description.position',
                'teacher_description.description',
            ]);
        return $res;
    }

    //-----------------------图库-------------------------

    //清空详情图片
    public static function ClearTeacherImage($galleryId)
    {
        $data['headimg']='';
        $data['galleryId']=0;
        TeacherDescModel::where('gallery_id', $galleryId)->updateByHump($data);
        return;
    }

}