<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\Join;

use App\Logic\V0\Common\VerifyLogic;
use App\Model\V0\Join\JoinModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class JoinLogic
{
    //添加主表
    public static function addJoin ($data=[])
    {
        if(!empty($data['phone'])){
            VerifyLogic::verifyPhone($data['phone']);
        }
        if(empty($data['type'])){
            if(empty($data['codeImg'])){
                throw new RJsonError('请输入验证码', 'NO_CODEIMG');
            }
            //图片验证
            VerifyLogic::imageCode($data['codeImg']);
            unset($data['codeImg']);
        }else{
            unset($data['type']);
        }
        $model = new JoinModel();
        $model->setDataByHumpArray($data)->save();
        return $model;

    }

    public static function JoinLists($data)
    {
        $model = new JoinModel();
        if(!empty($data['phone'])){
            $model = $model->where('phone',$data['phone']);
        }
        $model->orderBy('created_at','DESC');
        $JoinLists = $model->select(['*']);
        return $JoinLists->getDdvPageHumpArray(true);
    }

    public static function JoinOne($joinId)
    {
        self::isSee($joinId);
        $model = new JoinModel();
        $join = $model->where('join_id',$joinId)->firstHumpArray(['*']);
        return $join;
    }

    public static function isSee($joinId)
    {
        $res['isSee']=1;
        $model = new JoinModel();
        $model->where('join_id',$joinId)->updateByHump($res);
        return;
    }



}