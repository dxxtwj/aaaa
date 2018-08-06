<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Api\WechatLogin;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Goods\GoodsModel;
use App\Model\Shopping\Order\OrderModel;
use App\Model\Shopping\OrderGoods\OrderGoodsModel;
use App\Model\Shopping\User\UserModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use JPush\Exceptions\JPushException;

class WechatLoginLogic extends ShoppingLogic
{

//    const APPID = 'wxa34b8d82c86bd3c5';//APPID
//    const RESPONSETYPE = 'code';//获取code
//    const APPSECRET = '86f5576164f5bb226eb824eb08a8c698';
//    const SCOPE = 'snsapi_userinfo';// 授权的方式


    const APPID = 'wx3d1656447b0c8f6a';//APPID
    const RESPONSETYPE = 'code';//获取code
    const APPSECRET = 'e371443f0c000921d773b7b1dab8591d';
    const SCOPE = 'snsapi_userinfo';// 授权的方式

    /*
     * 获取CODE
     * @param array $data 回调地址
     * @param string $returnUrl 自定义的参数，前段需要
     * @param string $redirectUrl 回调地址
     * reuturn string|array 已登录返回用户数据  否则 返回一个url地址
     */
    public static function login($data) {

        $userId = self::isLogin('userId');
        \Log::info('isLogin'.json_encode($userId));
        // 测试的回调地址   ： http://wxtest.cdn.easyke.top   如果要用来测试的话请在微信开发者工具，再用这个回调网站来测试
        if (!empty($userId)) {// 是登录状态

//            $userRes = $userModel->where('user_id', $userId)->firstHumpArray();
            $isLogin['isLogin'] = true;

            return ['data' => $isLogin];

        } else {//没有登录状态

            $returnUrl = urlencode($data['returnUrl']);
            $redirectUrl = urlencode($data['redirectUrl'].'?returnUrl='.$returnUrl);//前段需要的参数
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.self::APPID.'&redirect_uri='.$redirectUrl.'&response_type='.self::RESPONSETYPE.'&scope='.self::SCOPE.'&state=123#wechat_redirect';

            $array['url'] = $url;
            $array['isLogin'] = false;
            \Log::info(json_encode($array));
            return ['data' => $array];
        }

    }

    /*
     * 拉取用户的信息
     * @param string $code 前段会带着这个code访问这个方法
     * @return array $userInfo  返回用户的基本信息  这里是静默登录，所以没有用到
     * $return 用户数据
     */
    public static function redirect($code) {
        $userModel = new UserModel();
//
//        // 2、获取网页授权的access_token
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".self::APPID."&secret=".self::APPSECRET."&code=".$code."&grant_type=authorization_code";
        $res = self::http_curl($url, 'get');
        $access_token = $res['access_token'];
        $openid = $res['openid'];



//        if (!empty($res['openid'])) {
//            $userData =  $userModel->where('user_openid', $res['openid'])->firstHumpArray();
//        } else {
//            throw  new RJsonError('openid没有获取到', 'OPENID_ERROR');
//        }
//        if (!empty($userData)) {//存在 登录
//
//            \Session::put('userId', $userData['userId']);
//
//            $isLogin['isLogin'] = true;
//            return ['data' => $isLogin];
//
//        } else { // 不存在添加数据库并且登录
//
//            $data['access_token'] = $res['access_token'];
//            $data['user_openid'] = $res['openid'];
//            $data['expires_in'] = $res['expires_in'];
//            $data['scope'] = $res['scope'];
//
//            $userModel->setDataByHumpArray($data)->save();
//            $id = $userModel->getQueueableId();
//            \Session::put('userId', $id);
//            $isLogin['isLogin'] = true;
//            return ['data' => $isLogin];
//
//        }



        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $userInfo = self::http_curl($url);
//        \Log::info('-=------userInfo-----'.json_encode($userInfo));
        if (empty($userInfo)) {

            throw  new RJsonError('授权失败', 'USER_ERROR');
        }

        if (!empty($userInfo)) {


            $userRes = $userModel->where('user_openid', $openid)->firstHumpArray();
//            \Log::info('--------=-=-=-====-UserRes:'.json_encode($userRes));
//            \Log::info(json_encode($userInfo));
            if (!empty($userRes)) {

                \Session::put('userId', $userRes['userId']);

                return ['data' => $userRes, 'isLogin' => true];
            }

            $userData['user_city'] = $userInfo['city'];
            $userData['user_country'] = $userInfo['country'];
            $userData['user_headimgurl'] = $userInfo['headimgurl'];
            $userData['user_openid'] = $openid;
            $userData['user_nickname'] = $userInfo['nickname'];
            $userData['user_province'] = $userInfo['province'];
            $userData['user_sex'] = $userInfo['sex'];
//            $userData['user_unionid'] = $userInfo['unionid'];
            $userData['user_code'] = $code;
            $userData['access_token'] = $res['access_token'];
            $userData['expires_in'] = $res['expires_in'];
            $userData['refresh_token'] = $res['refresh_token'];
            $userData['scope'] = $res['scope'];

            $userModel->setDataByHumpArray($userData)->save();
            $id = $userModel->getQueueableId();

            \Session::put('userId', $id);
//            \Log::info(json_encode($userData));
//            \Log::info('____-------------------session'.\Session::get('userId'));
            return ['data' => $userData, 'isLogin' => true];
        }

    }

    /**
     * $url 接口url string
     * $type 请求类型 string
     * $res 返回数据类型 string
     * $arr post 请求参数 string
     */
    public static function http_curl($url, $type = 'get', $res = 'json', $arr = '')
    {
        //1.初始化curl
        $ch = curl_init();

        //2.设置curl参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//不直接输出，以变量的方式存储起来

        if($type == 'post'){

            curl_setopt($ch,CURLOPT_POST,1);

            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);

        } elseif ($type == 'get') {

        }

        //3.采集
        $output = curl_exec($ch);

        //4.关闭
        curl_close($ch);

        //请求成功
        return json_decode($output,true);
    }

    /*
    * 测试的一个方法
    */
    public static function aa() {
        var_dump(config('wechat.open.app_id'));exit;

        // 1、创建集合
        $collect1  = collect([1,2,3])->all();// 方法简单返回集合表示的底层数组
        $collect2  = collect([1,2,3])->toArray();// 方法简单返回集合表示的底层数组


        // 2、avg() 返回平均值，是根据你给出的值来运输的，比如50，下面有五个值，哪50就是分开五份来运算
        $collect3  = collect([1,2,3])->avg();// 方法返回所有集合项的平均值：
        $collect4  = collect([['a' =>50],['b'=>10],['c'=>20],['d'=>40],['e'=>50]])->avg('a');// 方法返回所有集合项的平均值,指定下标


        // 3、chunk 方法将一个集合分割成多个小尺寸的小集合
        $collection = collect([1, 2, 3, 4, 5, 6, 7]);
        $chunks = $collection->chunk(3);
        $collect5 = $chunks->toArray();// [[1, 2, 3, 4], [5, 6, 7]]

        foreach ($collect5 as $k=>$v) {

        }


        //4、collapse 方法将一个多维数组集合收缩成一个一维数组：
        // 如数组之间有重复的值如何才能不覆盖
        $collection = collect([['a'=>1, 2, 3], ['a'=>4, 5, 6], [7, 8, 9]]);
        $collapsed = $collection->collapse();
        $collect6 = $collapsed->all();
        $merge = array_merge(['a'=>1, 2, 3], ['a'=>4, 5, 6], [7, 8, 9]);// 没区别


        //combine 方法可以将一个集合的键和另一个数组或集合的值连接起来
        $collection = collect(['name', 'age']);//健
        $combined = $collection->combine(['George', 29]);// 值
        $collect7 = $combined->all();


        //concat 方法可用于追加给定数组或集合数据到集合末尾
        $collection = collect(['John Doe']);
        $concatenated = $collection->concat(['1111'=>'Jane Doe'])->concat(['name' => 'Johnny Doe','asd']);
        $collect8 = $concatenated->all();

        // contains方法判断集合是否包含一个给定项：
        $collection = collect(['name' => 'Desk', 'price' => 100]);
        $collection->contains('Desk');
        \DB::enableQueryLog();


//
//        $modes = (new OrderModel())->whereHas('orderGoods', function($req) {
//
//
//            $req->where('order_id', 61);
//
//        })->with(['orderGoods' => function($req){
//
//
//        }])->getHumpArray();


//        $modes = (new OrderGoodsModel())->whereHas('order', function($req) {
//
//
//        })->with(['order'=>function($req) {}])->getHumpArray();


//        $modes = (new OrderModel())->whereHas('orderGoods', function($req) {
//
//            $req->where('order_id', 61);
//
//        })->with(['orderGoods' => function($req) {
//
//            $req->where('goods_id', 147);
//
//        }])->getHumpArray();
//
//        var_dump($modes);exit;

//            \Session::put('ab',123);
//            var_dump(\Session::get('ab'));
//        var_dump(\DB::getQueryLog());exit;
//        var_dump($collect8);exit;

    }
}