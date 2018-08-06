<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10\Honor;

use App\Model\V10\Honor\HonorModel;

use function var_dump;

class HonorLogic
{
    /*
     * 这个是添加荣誉证书的
     */
    public static function honorAdd($data) {

        $HonorModel = new HonorModel();
        $HonorModel->setDataByHumpArray($data)->save();
        return;
    }

    /*
     * 查询全部荣誉证书
     */
    public static function HonorShow() {

        $HonorModel = new HonorModel();
        $HonorModel = $HonorModel->orderBy('sort','DESC');
        $res = $HonorModel->getDdvPageHumpArray(true);

        return $res;
    }
    /*
     * 查询单条
     */
    public static function honorFirst($id) {

        $honorModel = new HonorModel();
        $honorFirst = $honorModel->where('honor_id', $id)->first();

        return $honorFirst;
    }
    /*
     * 修改数据
     */
    public static function honorEdit($data) {

        $honorModel = new HonorModel();
        $honorModel->where('honor_id', $data['honorId'])->updateByHump($data);

        return;
    }
    /*
     * 删除荣誉证书
     */
    public static function honorDelete($id) {

        $honorModel = new HonorModel();
        $honorModel->where('honor_id', $id)->delete();

        return;
    }
}