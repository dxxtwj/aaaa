<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shangrui\Admin\Home;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shangrui\Goods\GoodsModel;
use App\Model\Shangrui\Home\HomeModel;

use \DdvPhp\DdvRestfulApi\Exception\RJsonError;

use Illuminate\Database\QueryException;

class HomeLogic extends ShoppingLogic
{
    // 添加首页轮播图
    public static function addHome($data = array()){
        $homeModel = new HomeModel();
        if (!empty($data['goodsId'])){
            $home = $homeModel->where('goods_id',$data['goodsId'])->firstHumpArray();
            if (!empty($home)){
                throw new RJsonError('已存在此商品推荐','HOME_ERROR');
            }
        }

        $homeData['home_img'] = empty($data['homeImg']) ? '' : $data['homeImg'];
        $homeData['goods_id'] = empty($data['goodsId']) ? 0 : $data['goodsId'];
        $homeData['home_order'] = empty($data['homeOrder']) ? 0 : $data['homeOrder'];
        $homeData['home_contents'] = empty($data['homeContents']) ? '' : $data['homeContents'];
        $homeData['created_at'] = time();

        $res = $homeModel->setDataByArray($homeData)->save();
        if (empty($res)){
            throw new RJsonError('添加失败','HOME_ERROR');
        }
    }

    // 修改首页轮播图
    public static function editHome($data = array()){
        $homeModel = new HomeModel();

        $home = $homeModel->where('home_id',$data['homeId'])->firstHumpArray();

        $homeData['home_img'] = empty($data['homeImg']) ? $home['homeImg'] : $data['homeImg'];
        $homeData['goods_id'] = empty($data['goodsId']) ? $home['goodsId'] : $data['goodsId'];
        $homeData['home_order'] = empty($data['homeOrder']) ? $home['homeOrder'] : $data['homeOrder'];
        $homeData['home_contents'] = empty($data['homeContents']) ? $home['homeContents'] : $data['homeContents'];
        $homeData['updated_at'] = time();

        $bool = $homeModel->where('home_id',$data['homeId'])->updateByHump($homeData);

        if (!$bool){
            throw new RJsonError('修改失败','HOME_ERROR');
        }

    }

    // 查看轮播图
    public static function showHome($data = array()){
        $homeModel = new HomeModel();
        if (!empty($data['homeId'])){
            $res = $homeModel->where('home_id',$data['homeId'])->firstHumpArray();
            return ['data' => $res];
        } elseif (empty($data['homeId'])){
            $res = $homeModel->orderBy('created_at','DESC')->getDdvPageHumpArray();
        }
        return $res;
    }

    // 删除
    public static function deleteHome($data = array()){
        $homeModel = new HomeModel();

        $bool = $homeModel->where('home_id',$data['homeId'])->delete();
        if (!$bool) {
            throw new RJsonError('删除失败','HOME_ERROR');
        }
        return ;
    }

}