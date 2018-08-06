<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shangrui\Admin\Goods;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shangrui\Goods\GoodsModel;

use App\Model\Shangrui\Type\TypeModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;

class GoodsLogic extends ShoppingLogic
{
    // 添加商品
    public static function addGoods($data = array()){

        $goodsModel = new GoodsModel();

        $goodsData['goods_name'] = empty($data['goodsName']) ? '' : $data['goodsName'];
        $goodsData['goods_img'] = empty($data['goodsImg']) ? '' : $data['goodsImg'];
        $goodsData['goods_code'] = empty($data['goodsCode']) ? '' : $data['goodsCode'];
        $goodsData['goods_contents'] = empty($data['goodsContents']) ? '' : $data['goodsContents'];
        $goodsData['goods_status'] = empty($data['goodsStatus']) ? 1 : 0 ;
        $goodsData['goods_is_recommend'] = empty($data['goodsIsRecommend']) ? 0 : 1;
        $goodsData['goods_is_new'] = empty($data['goodsIsNew']) ? 0 : 1;
        $goodsData['type_id'] = empty($data['typeId']) ? '0' : $data['typeId'];
        $goodsData['goods_price'] = $data['goodsPrice'];
        $goodsData['goods_format'] = !empty($data['goodsFormat']) ? $data['goodsFormat'] : '';//是否开启了多规格
        $goodsData['goods_introduce'] = !empty($data['goodsIntroduce']) ? json_encode($data['goodsIntroduce'], true): '';
        $goodsData['goods_order'] = empty($data['goodsOrder']) ? 0 : $data['goodsOrder'];
        $goodsData['created_at'] = time();
        $goodsData['updated_at'] = time();

        $goodsModel->setDataByHumpArray($goodsData)->save();
        $lastId = $goodsModel->getQueueableId();
        if (!$lastId){
            throw new RJsonError('添加商品失败','GOODS_ERROR');
        }
    }

    // 修改商品
    public static function editGoods($data = array()){
        $goodsModel = new GoodsModel();

        $goods = $goodsModel->where('goods_id',$data['goodsId'])->firstHumpArray();


        $goodsData['goods_price'] = empty($data['goodsPrice']) ? $goods['goodsPrice']: $data['goodsPrice'];//默认价格
        $goodsData['goods_name'] = !empty($data['goodsName']) ? $data['goodsName'] : $goods['goodsName'];
        $goodsData['type_id'] = !empty($data['typeId']) ? $data['typeId'] : $goods['typeId'];
        $goodsData['goods_status'] = empty($data['goodsStatus']) ? 0 : 1;
        $goodsData['goods_contents'] = !empty($data['goodsContents']) ? $data['goodsContents'] : $goods['goodsContents'];
        $goodsData['goods_order'] = empty($data['goodsOrder']) ? $goods['goodsOrder'] : $data['goodsOrder'];
        $goodsData['goods_img'] = !empty($data['goodsImg']) ? $data['goodsImg'] : $goods['goodsImg'];
        $goodsData['goods_format'] = !empty($data['goodsFormat']) ? $data['goodsFormat'] : '';//是否开启了多规格
        $goodsData['goods_introduce'] = !empty($data['goodsIntroduce']) ? json_encode($data['goodsIntroduce'], true) : '';
        $goodsData['goods_is_recommend'] = empty($data['goodsIsRecommend']) ? 0 : 1;
        $goodsData['goods_is_new'] = empty($data['goodsIsNew']) ? 0 : 1;
        $goodsData['goods_code'] = !empty($data['goodsCode']) ? $data['goodsCode'] : $goods['goodsCode'];
        $goodsData['updated_at'] = time();
        $bool = $goodsModel->where('goods_id',$data['goodsId'])->updateByHump($goodsData);
        if (!$bool){
            throw new RJsonError('修改商品失败','GOODS_ERROR');
        }
    }

    // 查询商品

    public static function showGoods($data = array()){
        $goodsModel = new GoodsModel();

        if (empty($data['goodsId'])){  //查全部
            if (!empty($data['goodsName'])) {

                $goodsModel = $goodsModel->where('goods_name', 'like', '%'.$data['goodsName'].'%');
            }

            if (!empty($data['typeId'])) {

                $goodsModel = $goodsModel->where('type_id', $data['typeId']);
            }

            if (!empty($data['goodsCode'])) {

                $goodsModel = $goodsModel->where('goods_code', 'like', '%'.$data['goodsCode'].'%');

            }
            if (!empty($data['goodsIsNew'])){  // 查询新品 goodsIsNew 1
                $goodsModel = $goodsModel->where('goods_is_new',$data['goodsIsNew']);
            }
            if (!empty($data['goodsIsRecommend'])){  //查询热门商品 goodsIsRecommend 1
                $goodsModel = $goodsModel->where('goods_is_recommend',$data['goodsIsRecommend']);
            }
            $res = $goodsModel->orderBy('goods_order','DESC')->getDdvPageHumpArray();

            if (!empty($res['lists'])) {

                foreach ($res['lists'] as $k => $v) {
                    $res['lists'][$k]['goodsIntroduce'] = empty($v['goodsIntroduce']) ? array() : json_decode($v['goodsIntroduce'], true);//其他图片

                }
            }
            return $res;
        } elseif (!empty($data['goodsId'])){
            $res = $goodsModel->where('goods_id',$data['goodsId'])->firstHumpArray();
            if (!empty($res)){
                $res['goodsIntroduce'] = empty($res['goodsIntroduce']) ? array() : json_decode($res['goodsIntroduce'], true);//商品介绍图
            }
            return ['data'=>$res];
        }

    }


    // 删除商品
    public static function deleteGoods($data = array()){
        $goodsModel = new GoodsModel();

        \DB::beginTransaction();

        try{

            $arrayWhere['goods_id'] = $data['goodsId'];
            $arrayWhere['goods_is_recommend'] = 1;

            $bool = $goodsModel->where($arrayWhere)->firstHumpArray();

            if (!empty($bool)) {
                throw new RJsonError('首页还有此商品的显示，删除失败', 'GOODS_ERROR');
            }

            $arrayWhere['goods_is_new'] = 1;
            $bool = $goodsModel->where($arrayWhere)->firstHumpArray();
            if (!empty($bool)){
                throw new RJsonError('首页还有此商品的显示，删除失败','GOODS_ERROR');
            }

            $bool = $goodsModel->where('goods_id', $data['goodsId'])->delete();

            if (empty($bool)) {
                throw new RJsonError('删除商品失败', 'GOODS_ERROR');
            }

            \DB::commit();
        } catch(QueryException $e) {

            \DB::rollBack();
            throw new RJsonError($e->getMessage(), 'GOODS_ERROR');
        }
        return ;
    }
}