<?php

namespace App\Model\Shopping\Order;

use App\Model\Model;

use App\Logic\Shopping\Common\Payment\PaymentWxpayCommonLogic;
use App\Logic\Shopping\Common\Payment\PaymentAlipayCommonLogic;

class OrderCommonModel extends Model
{
    protected $table = 'shopping_order';

    protected $primaryKey = 'order_number_origin';

    protected $keyType = 'string';
    public $timestamps= false;

    /**
     * 指定订单号
     * @param $orderNumber
     */
    public function whereOrderNumber($orderNumber)
    {

        try{
            // 试图查找原始单号
            $number = (new OrderNumberOriginCommonModel())->getOrderNumberOrigin($orderNumber);
        }catch (\Exception $e){
            // 否则传入单号就是原始单号
            $number = $orderNumber;
        }
        // 设置原始单号 - 返回数据模型
        return $this->where('order_number_origin', $number);
    }

    /**判断查找下级用户信息
     * @param $uid
     * @return $this
     * @throws Exception
     */
    public function whereSubCustomerOrder($uid)
    {
        if (empty($uid)){
            throw new Exception('用户id错误', 'USER_ID_IS_EMPTY ');
        }
        $roleIdArray = \Session::get('roleIdArray');
        if(!in_array('1',$roleIdArray)){//存在角色1则为超级管理员，则显示所有
            $idArray = (new UserToCustomerCommonModel())->getCustomersIds($uid);//返回逗号拼接字符串
            if ($idArray) {
                return $this->whereIn('created_uid',$idArray);
            }
        }
        return $this;
    }

    /**转换支付方式名称
     * @param $paymentType
     * @throws Exception
     */
    public function getPaymentTypeName($paymentType){
        $fashion = substr($paymentType, 0, 1);
        $platform = substr($paymentType, -1, 1);
        $fashionName = '';
        $platformName = '';
        switch(intval($fashion)){
            case 1 :
                $fashionName = 'wap';
            break;
            case 2 :
                $fashionName = 'pc';
            break;
            case 3 :
                $fashionName = '公众号';
            break;
            case 4 :
                $fashionName = '公众号跳转';
            break;
            case 5 :
                $fashionName = 'app';
            break;
            case 6 :
                $fashionName = '刷卡';
            break;
            case 7 :
                $fashionName = '二维码';
            break;
            case 9 :
                $fashionName = '统一';
            break;
            default :
                $fashionName = '未知方式';
            break;
        }
        switch(intval($platform)){
            case 1 :
            $platformName = '二维码';
            break;
            case 2 :
            $platformName = '支付宝';
            break;
            case 3 :
            $platformName = '微信';
            break;
            case 4 :
            $platformName = 'QQ';
            break;
            case 5 :
            $platformName = '银联';
            break;
            case 6 :
            $platformName = '京东';
            break;
            case 7 :
            $platformName = '百度';
            break;
            case 8 :
            $platformName = '新浪';
            break;
            default :
                $fashionName = '未知平台';
                break;
        }
        return $platformName . '[' . $fashionName . ']' ;
    }

    /**
     * 获取一条订单商品
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|OrderGoodsModel
     */
    public function getOrderGoodsOne(){
        return $this->hasOne(OrderGoodsModel::class, 'order_number_origin', 'orderNumberOrigin');
    }

    /**
     * 获取订单相关商品
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getOrderGoodsUnHump(){
        return $this->hasMany(OrderGoodsModel::class, 'order_number_origin', 'order_number_origin');
    }

    /**
     * 获取订单相关的商品
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getOrderGoodsLists(){
        return $this->hasMany(OrderGoodsCommonModel::class, 'order_number_origin', 'orderNumberOrigin');
    }

    /**
     * 获取订单拥有者信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|UserInfoCommonModel
     */
    public function getOrderUser(){
        $userInfo =  $this->hasOne(UserInfoCommonModel::class,'uid','ownerUid');
        if (empty($userInfo->first())){
            //拥有者为企业
            $userInfo = $this->hasOne(CompanyCommonModel::class,'company_uid','ownerUid');
        }
        return $userInfo;
    }

    //获取订单所属者信息
    public function getOrderOwnerUser(){
        return $this->hasOne(UserInfoCommonModel::class,'uid','ownerUid');
    }

    //获取订单所属企业信息
    public function getOrderCompany(){
        return $this->hasOne(CompanyCommonModel::class,'company_uid','ownerUid');
    }

    /**
     * 获取用户名字
     */
    public function getUserName($inputId){
        return  $this->hasOne(UserInfoCommonModel::class,'uid',$inputId)->firstHump(['name'])->name;
    }

    /**
     * 获取订单用户信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|UserInfoCommonModel
     */
    public function getOrderUserUnHump(){
        return $this->hasOne(UserInfoCommonModel::class,'uid','owner_uid');
    }

    public function getOrderAccountUnHump(){
        return $this->hasOne(AccountCommonModel::class, 'uid', 'owner_uid');
    }

    public function getOrderAccountHump(){
        return $this->hasOne(AccountCommonModel::class, 'uid', 'ownerUid');
    }


    /**
     * 获取一条订单商品
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|OrderGoodsModel
     */
    public function getGoodsLists(){
        return $this->hasOne(OrderGoodsModel::class, 'order_number_origin', 'order_number_origin');
    }

    /**
     * 通过订单号获取发票号
     */
    public function getInvoice(){
        return $this->hasMany(InvoiceOrderCommonModel::class, 'order_number_origin', 'orderNumberOrigin');
    }

    /**
     * 通过订单号获取物流地址信息
     */
    public function getDeliveryAddress(){
        return $this->hasMany(DeliveryCommonModel::class, 'order_number', 'orderNumber');
    }


    /**
     * 通过订单号获取物流地址信息
     */
    public function getDeliveryAddressUnHump(){
        return $this->hasMany(DeliveryCommonModel::class, 'order_number', 'order_number');
    }

    /**
     * @param $orderNumber
     * @param $state
     * @param string $paymentAt
     * @return bool
     * @throws Exception
     */
    public function updateState($orderNumber, $state, $paymentAt = ''){
        if (empty($orderNumber)){
            throw new Exception('订单号必须传入', 'ORDER_NUMBER_MUST_INPUT');
        }
        if (empty($state)){
            throw new Exception('订单状态必须传入', 'STATE_MUST_INPUT');
        }
        $data['order_state'] = $state;
        if (!empty($paymentAt)){
            $data['payment_at'] = $paymentAt;
        }
        $result = $this->where('order_number', $orderNumber)->update($data);
        if (!$result){
            throw new Exception('修改订单状态失败', 'UPDATE_STATE_ERROR');
        }
    }



    /* ******************** 支付状态集合 开始 ******************** */
    // 支付状态 - 未付款
    const PATMENT_STATE_UNPAY = 0;
    // 支付状态 - 超时
    const PATMENT_STATE_TIMEOUT = -1;
    // 支付状态 - 成功
    const PATMENT_STATE_SUCCESS = 1;
    // 支付状态 - 退款一部分
    const PATMENT_STATE_REFUND_PART = 2;
    // 支付状态 - 全额退款
    const PATMENT_STATE_REFUND_ALL = 3;
    /* ******************** 支付状态集合 结束 ******************** */
    /* ******************** 订单状态集合 开始 ******************** */

    // 订单状态 - 管理员取消
    const ORDER_STATE_MANAGE_CANCELED = -3;
    // 订单状态 - 客户取消
    const ORDER_STATE_CLIENT_CANCELED = -2;
    // 订单状态 - 超时关闭
    const ORDER_STATE_TIMEOUT_CLOSED = -1;
    // 订单状态 - 未付款[待支付]
    const ORDER_STATE_UNPAY = 0;
    const ORDER_STATE_WAIT_PAY = 0;
    // 订单状态 - 已收款[待发货]
    const ORDER_STATE_PAY_SUCCESS = 1;
    const ORDER_STATE_GOODS_WAIT_SHIPPED = 1;
    // 订单状态 - 待收货
    const ORDER_STATE_GOODS_SHIPPED = 2;
    // 订单状态 - 待评价
    const ORDER_STATE_GOODS_RECEIVED = 3;
    // 订单状态 - 已评价
    const ORDER_STATE_GOODS_EVALUATED = 4;

    /* ******************** 订单状态集合 结束 ******************** */
    /* ******************** 订单类型集合 开始 ******************** */


    /* ******************** 订单软删除集合 开始 ******************** */

    //删除
    const ORDER_DELETE_YES = 1;
    //正常
    const ORDER_DELETE_NO = 0;

    /**
     * 是否开过发票
     */
    //开过发票
    const ORDER_EXIST_INVOICE = 1;
    //没开过发票
    const ORDER_NOT_INVOICE = 0;

    /**
     * 订单物流状态
     */
    //待发货
    const ORDER_DELIVERY_STATE_WAIT_SHIPPED = 0;
    //运输中
    const ORDER_DELIVERY_STATE_SHIPPING = 1;
    //已收货
    const ORDER_DELIVERY_STATE_SHIPPED = 2;

    /* ****************** 订单软删除集合 结束 ****************** */


    //换货
    const ORDER_TYPE_REPLACE = -2;
    //退款
    const ORDER_TYPE_REFUND = -1;
    //在线下单
    const ORDER_TYPE_ONLINE = 1;
    //收款
    const ORDER_TYPE_COLLECT = 2;
    //充值
    const ORDER_TYPE_RECHARGE = 3;

    /* ****************** 订单类型集合 结束 ****************** */
    /* ****************** 支付方式的名字集合 开始 ****************** */
    // 支付宝
    const PAYMENT_NAME_ALIPAY = 'alipay';
    // 微信
    const PAYMENT_NAME_WXPAY = 'wxpay';
    // 银联
    const PAYMENT_NAME_UNPAY = 'unpay';
    // qq钱包
    const PAYMENT_NAME_QPAY = 'qpay';
    // 京东
    const PAYMENT_NAME_JDPAY = 'jdpay';
    // 支付总集合
    const PAYMENT_NAME_ALL = [
        self::PAYMENT_NAME_ALIPAY,
        self::PAYMENT_NAME_WXPAY,
        self::PAYMENT_NAME_UNPAY,
        self::PAYMENT_NAME_QPAY,
        self::PAYMENT_NAME_JDPAY
    ];
    // 应用转支付名字
    const APP_NAME_TO_PAYMENT_NAME = [
        'alipay' => self::PAYMENT_NAME_ALIPAY,
        'wechat' => self::PAYMENT_NAME_WXPAY,
        'wxpay' => self::PAYMENT_NAME_WXPAY,
        'unpay' => self::PAYMENT_NAME_UNPAY,
        'qq' => self::PAYMENT_NAME_QPAY,
        'qpay' => self::PAYMENT_NAME_QPAY,
        'qqConnet' => self::PAYMENT_NAME_QPAY,
        'jd' => self::PAYMENT_NAME_JDPAY,
        'jdpay' => self::PAYMENT_NAME_JDPAY
    ];
    /* ****************** 支付方式的名字集合 结束 ****************** */
    /**
     * 支付方式[方式+平台]:
     *
     * ** 方式如下 **
     * ** 9:统一
     * ** 1:wap
     * ** 2:pc
     * ** 3:公众号js
     * ** 4:公众号跳转
     * ** 5:app
     * ** 6:刷二维码
     * ** 7:二维码
     *
     * ** 平台如下 **
     * ** 1:刷卡尝试
     * ** 2:支付宝
     * ** 3:微信
     * ** 4:QQ
     * ** 5:银联
     * ** 6:京东
     * ** 7:百度
     * ** 8:新浪
     *
     * *刷二维码支付具备特殊性*
     * *在没有确定具体刷二维码方式时候,先是16
     * *比如微信刷卡是26,刷二维码支付具备特殊性,在没有确定具体刷二维码方式时候,先是16
     */
    /* **************************支付类型开始************************** */

    // ******* 微信 *******
    // 微信 ** H5支付-手机网页支付
    const PATMENT_TYPE_WECHAT_MWEB = 13;
    // 微信 ** 公众号jssdk ** 小程序
    const PATMENT_TYPE_WECHAT_JSAPI = 33;
    // 微信 ** 安卓IOS支付
    const PATMENT_TYPE_WECHAT_APP = 53;
    // 微信 ** 刷卡支付[微信付款二维码支付]
    const PATMENT_TYPE_WECHAT_MICROPAY = 63;
    // 微信-生成二维码
    const PATMENT_TYPE_WECHAT_NATIVE = 73;

    // ******* 支付宝 *******

    // 支付宝 ** 统一下单
    const PATMENT_TYPE_ALIPAY_UNIFIED = 92;
    // 支付宝 ** 统一下单 ** js下单
    const PATMENT_TYPE_ALIPAY_JSAPI = 32;
    // 支付宝 ** 统一下单 ** 手机网页下单
    const PATMENT_TYPE_ALIPAY_WAPPAGE = 12;
    // 支付宝 ** 统一下单 ** 电脑网页下单
    const PATMENT_TYPE_ALIPAY_WEBPAGE = 22;
    // 支付宝 ** 刷卡支付[支付宝付款二维码支付]
    const PATMENT_TYPE_ALIPAY_MICROPAY = 62;

    /* **************************微信支付类型开始************************** */

    // 支持的
    const APP_NAME_TYPE_TO_PATMENT_TYPE = [
        'wechat_h5'=>self::PATMENT_TYPE_WECHAT_MWEB,
        'wechat_mp'=>self::PATMENT_TYPE_WECHAT_JSAPI,
        'wechat_app'=>self::PATMENT_TYPE_WECHAT_APP,
        'wechat_mrc'=>self::PATMENT_TYPE_WECHAT_MICROPAY,
        'wechat_native'=>self::PATMENT_TYPE_WECHAT_NATIVE,
        'alipay_mp'=>self::PATMENT_TYPE_ALIPAY_JSAPI,
        'alipay_web'=>self::PATMENT_TYPE_ALIPAY_WEBPAGE,
        'alipay_wap'=>self::PATMENT_TYPE_ALIPAY_WAPPAGE,
        'alipay_mrc'=>self::PATMENT_TYPE_ALIPAY_MICROPAY,
    ];

    const WECHAT_TRADE_TYPE_MWEB = 'MWEB';
    const WECHAT_TRADE_TYPE_JSAPI = 'JSAPI';
    const WECHAT_TRADE_TYPE_APP = 'APP';
    const WECHAT_TRADE_TYPE_MICROPAY = 'MICROPAY';
    const WECHAT_TRADE_TYPE_NATIVE = 'NATIVE';

    // 转换微信支付类型
    const PATMENT_TYPE_TO_WECHAT_TRADE_TYPE = [
        self::PATMENT_TYPE_WECHAT_MWEB => self::WECHAT_TRADE_TYPE_MWEB,
        self::PATMENT_TYPE_WECHAT_JSAPI => self::WECHAT_TRADE_TYPE_JSAPI,
        self::PATMENT_TYPE_WECHAT_APP => self::WECHAT_TRADE_TYPE_APP,
        self::PATMENT_TYPE_WECHAT_MICROPAY => self::WECHAT_TRADE_TYPE_MICROPAY,
        self::PATMENT_TYPE_WECHAT_NATIVE => self::WECHAT_TRADE_TYPE_NATIVE
    ];
    // 支付类型转具体方法名
    const PATMENT_TYPE_TO_METHOD_UNIFIED = [
        // 微信--公众平台支付
        self::PATMENT_TYPE_WECHAT_JSAPI=>'WechatJsapi',
        // 支付宝--公众平台*统一下单支付
        self::PATMENT_TYPE_ALIPAY_JSAPI=>'AlipayJsapi',
        // 支付宝--电脑网页跳转支付
        self::PATMENT_TYPE_ALIPAY_WEBPAGE=>'AlipayWebpage',
        // 支付宝--手机网页跳转支付
        self::PATMENT_TYPE_ALIPAY_WAPPAGE=>'AlipayWappage',
    ];
    // 支付类型转具体方法名
    const PATMENT_TYPE_TO_METHOD_QUERY = [
        // 微信--公众平台支付
        self::PATMENT_TYPE_WECHAT_JSAPI=>'wechat',
        // 支付宝--公众平台*统一下单支付
        self::PATMENT_TYPE_ALIPAY_JSAPI=>'alipay',
        // 支付宝--电脑网页跳转支付
        self::PATMENT_TYPE_ALIPAY_WEBPAGE=>'alipay',
        // 支付宝--手机网页跳转支付
        self::PATMENT_TYPE_ALIPAY_WAPPAGE=>'alipay',
        // 支付宝--刷卡支付
        self::PATMENT_TYPE_ALIPAY_MICROPAY=>'alipay',
    ];


    // 微信逻辑层
    const PAYMENT_LOGIC_NAME_WXPAY = PaymentWxpayCommonLogic::class;
    // 支付宝逻辑层
    const PAYMENT_LOGIC_NAME_ALIPAY = PaymentAlipayCommonLogic::class;

    // 转扫描类
    const PAYMENT_NAME_TO_SCAN_CLASS_NAME = [
        self::PAYMENT_NAME_ALIPAY => self::PAYMENT_LOGIC_NAME_ALIPAY,
        self::PAYMENT_NAME_WXPAY => self::PAYMENT_LOGIC_NAME_WXPAY,
    ];
    // 查询结果
    const PATMENT_TYPE_TO_CHECK_CLASS_NAME = [
        self::PATMENT_TYPE_WECHAT_MWEB => self::PAYMENT_LOGIC_NAME_WXPAY,
        // 微信--公众平台支付
        self::PATMENT_TYPE_WECHAT_JSAPI => self::PAYMENT_LOGIC_NAME_WXPAY,
        self::PATMENT_TYPE_WECHAT_APP => self::PAYMENT_LOGIC_NAME_WXPAY,
        self::PATMENT_TYPE_WECHAT_MICROPAY => self::PAYMENT_LOGIC_NAME_WXPAY,
        self::PATMENT_TYPE_WECHAT_NATIVE => self::PAYMENT_LOGIC_NAME_WXPAY,
        // 支付宝--统一下单支付
        self::PATMENT_TYPE_ALIPAY_UNIFIED => self::PAYMENT_LOGIC_NAME_ALIPAY,
        // 支付宝--统一下单支付
        self::PATMENT_TYPE_ALIPAY_JSAPI => self::PAYMENT_LOGIC_NAME_ALIPAY,
        // 支付宝--电脑网页跳转支付
        self::PATMENT_TYPE_ALIPAY_WEBPAGE => self::PAYMENT_LOGIC_NAME_ALIPAY,
        // 支付宝--手机网页跳转支付
        self::PATMENT_TYPE_ALIPAY_WAPPAGE => self::PAYMENT_LOGIC_NAME_ALIPAY,

    ];
}
