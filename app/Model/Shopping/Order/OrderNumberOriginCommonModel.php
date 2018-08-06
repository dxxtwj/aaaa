<?php

namespace App\Model\Shopping\Order;

use App\Model\Exception;
use App\Model\Model;

class OrderNumberOriginCommonModel extends Model
{
    public $timestamps = false;
    protected $table = 'shopping_order_number_origin';
    protected $primaryKey = ['order_number', 'order_number_origin'];
    protected $fillable = ['order_number', 'order_number_origin'];
    protected $keyType = 'string';

    /**
     * @param string|integer $orderNumber
     * @return string
     * @throws Exception
     */
    public function getOrderNumberOrigin($orderNumber){
        $model = $this->where('order_number', $orderNumber)->firstHump(['orderNumberOrigin']);
        if (empty($model)){
            throw new \Exception('没有找到原订单号');
        }
        return $model->orderNumberOrigin;
    }
}