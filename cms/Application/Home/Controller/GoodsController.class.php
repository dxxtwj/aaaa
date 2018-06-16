<?php
namespace Home\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class GoodsController extends HomeController {
	public function index(){
		$this->display();
	}

	/**
	 * 商品详情页
	 */
	public function goods_detail(){

		$gr = M('goods_rec'); // 商品表
		$fr = M('formats_rec');  // 规格名表
		$or = M('options_rec'); // 规格项表
		$fo = M('format_option'); // 规格组合表
		
		if (!$_COOKIE['gid:'.$_GET['gid'].'@#sessionId:'.$_SERVER['REMOTE_ADDR']]) {
			// 过期了添加浏览量
			$browse = $gr->where(array('GR_ID' => $_GET['gid']))
			    ->field('GR_Browse as browse')
			    ->find(); // 获取准确浏览量
			if ($browse['browse'] == null || $browse['browse'] == '0') { // 为null或0 则加1浏览量
				$addBrowse['GR_Browse'] = 1;
			} else { // 原有基础上 + 1 浏览量
				$addBrowse['GR_Browse'] = $browse['browse'] + 1;
			}
			$browse = $gr->where(array('GR_ID' => $_GET['gid']))->save($addBrowse); // 修改浏览量
			if ($browse) {
				setCookie('gid:'.$_GET['gid'].'@#sessionId:'.$_SERVER['REMOTE_ADDR'], '1', 1800, '/'); // 三十分钟内同一台电脑同一个商品不可以加浏览量
			}
		}

		if((int)I('gid') > 0){
			$where['GR_ID'] = (int)I('gid');
			$where['GR_Is_Show'] = 1; // 是否上架 
			$where['GR_Type'] = ['neq', 2]; // 不是一折购的商品
			$where['GR_Is_Delete'] = 0; // 是否删除了
			//商品信息
			$gr_info = $gr->where($where)
			   ->field('GR_Is_Options as options, GR_Stock as stock, GR_Cost_Price as costPrice, GR_Weight as weight, GR_Price as price, GR_ID as id, GR_Sale as sale, GR_Name as name, GR_IMG as img, GR_Other_IMG as imgs, GR_Parameter parameter, GR_Old_Price as oldPrice, GR_ID as id, GR_Describe')
			   ->find();
			$gr_info['price'] = $gr_info['price'] * 0.01; // 更改现价格
			$gr_info['oldPrice'] = $gr_info['oldPrice'] * 0.01; // 更改原价格
			
			if(!$gr_info){
				echo "<script>window.history.back(-1);</script>";
			}
			$fr_info = $fr
			    ->where(array('FR_GID'=>$gr_info['id']))
			    ->field('FR_Name as name, FR_ID as id')
			    ->order('FR_ID')
			    ->select();

			$or_info = $or // 规格表
			    ->where(array('OR_GID'=>$gr_info['id']))
			    ->field('OR_FID as fid, OR_Name as orName')
			    ->order('OR_ID')
			    ->select();

			foreach ($fr_info as $k1 => $v1) {
				foreach ($or_info as $k2 => $v2) {
					if($v2['fid'] == $v1['id']){ // 规格名id和规格项id对应
						$fr_info[$k1]['option'][] = $v2;
					}
				}
				$format[] =  $fr_info[$k1];
			}
		
			//轮播图
			if($gr_info['img']){
				$gr_info['banner_img'][] = $gr_info['img'];
			}
			if($gr_info['imgs']){
				if($gr_info['banner_img']){
					$gr_info['banner_img'] = array_merge($gr_info['banner_img'],explode(",", $gr_info['imgs']));
				}else{
					$gr_info['banner_img'] = explode(",", $gr_info['imgs']);
				}
			}
			//商品参数
			if($gr_info['parameter']){
				$gr_info['parameter'] = json_decode($gr_info['parameter'],true);
			}

			if($gr_info['GR_Describe']){
				if(IS_WIN){
					$system_type = "C:/wamp64/www/yuki";
				}else{
					$system_type = ".";
				}
				$content = file_get_contents($system_type.$gr_info['GR_Describe']);
				$gr_info['descImg'] = $content;
				
			}
			
			//查出可以领取的优惠券
			$co = M('coupon_rec');
			$push = M('coupon_push');
			//系统可领取的
			$co_where['CR_ISget'] = 1;
			$co_where['CR_State'] = 1;//开启
			$co_where['CR_ISDelete'] = 1;//未删
			$co_res = $co->field('CR_ID as id')->where($co_where)->select();
			foreach ($co_res as $k=>$v) {
				$co_ids[] = $v['id'];
			}
           // dump($co_ids);
			//个人现在已得到的优惠券
            $p_where['CP_UID'] = (int)$_SESSION['Home']['userid'];
            $push_res = $push->field('CP_CID as cpid')->where($p_where)->select();
            foreach ($push_res as $k=>$v) {
				$push_ids[] = $v['cpid'];
			}
//            dump($push_ids);
            if($co_ids){
                foreach ($co_ids as $k=>$v) {
                    if(in_array($v,$push_ids)){
                        unset($co_ids[$k]);
                    }
                }
                $coupon_res = $co_ids;
            }else{
                $coupon_res = -1;//系统没有优惠券
            }
            if($coupon_res != -1 && $coupon_res){
                $yes_where['CR_ID'] = array('in',$coupon_res);
                $co_result = $co->field("CR_ID as cid,CR_Minus as price,CR_Condition as rulePrice,DATE_FORMAT(FROM_UNIXTIME(CR_StartTime),'%Y.%m.%d') as date_begin,DATE_FORMAT(FROM_UNIXTIME(CR_EndTime),'%Y.%m.%d') as date_end")->where($yes_where)->order('CR_Sort desc')->select();
                foreach ($co_result as $k=>$v) {
                    $co_result[$k]['status'] = 1;
                }
                $coupon_arr = $co_result;
            }else{
                $coupon_arr = -1;
            }
            // dump($coupon_arr);
			$this->assign('coupon_json',json_encode($coupon_arr));
			$this->assign('gr_info_json',json_encode($gr_info));
			$this->assign('format_json',json_encode($format));
			$this->display();
		}else{
			echo "<script>window.history.back(-1);</script>";
		}
	
	}

	/**
	 * 点击不同的规格显示不同的库存和价格  ajax请求
	 * @param  array  $goodsOption 返回对应对规格的价钱和库存和图片
	 */
	public function ajaxFormatsOption()
	{
		$goodsWhere['FO_Name'] = str_replace('，', '@#', $_GET['showString']);
		$goodsWhere['FO_GID'] = $_GET['gid'];

		$gormatsOption = M('FormatOption')->where($goodsWhere)
			->field('FO_Price as price, FO_Stock as stock, FO_IMG as img, FO_ID as fid')
			->find();
		$gormatsOption['price'] = $gormatsOption['price'] * 0.01; // 更改现价

		if (empty($gormatsOption)) {

			$goodsOption['data'] = null;
			$goodsOption['msg'] = '暂无相关数据';
			$goodsOption['code'] = 0;

		} else {

			$goodsOption['data'] = $gormatsOption;
			$goodsOption['msg'] = '返回数据 data';
			$goodsOption['code'] = 1;
		}

		$this->ajaxReturn($goodsOption);
		exit;
	}

	/**
	 * 商品列表页
	 */
	public function hot_sale(){
		
		$cate = $_GET['cate'] ? $_GET['cate'] : 0;//分类id
		$search = $_GET['search'] ? $_GET['search'] : null;//搜索内容
		$goodsIds = $_GET['goodsIds'] ? $_GET['goodsIds'] : null;//商品id,如果是搜索进来的则除开这个商品id的搜索出来

		if (IS_AJAX && !empty($_GET['page'])) { // ajax请求区间,即分页
			$page = $_GET['page'];
			$gr_info = $this->getHotsale($page, $cate, $search, $goodsIds);
			
			if (!empty($gr_info)) { // 有数据

				$data['data'] = $gr_info;
				$data['msg'] = '返回数据 data';
				$data['success'] = true;
				$data['code'] = 1;

			} else { // 没有数据

				$data['data'] = null;
				$data['msg'] = '暂无数据';
				$data['success'] = true;
				$data['code'] = 0;

			}

			$this->ajaxReturn($data); // 返回
			exit;
		}

		// 初次点击进来
		$page = 0;
		$gr_info = $this->getHotsale($page, $cate, $search, $goodsIds);
		$this->assign('gr_info_json',json_encode($gr_info));
		$this->assign('get_info_json',json_encode($_GET)); // 为后面多条件搜索用
		
		if ($search != null) { //为后面搜索用的
			$this->assign('searchJson',json_encode($search));
		}
		$this->display();
	}

	/**
	 * 分页默认显示条数和默认显示搜索条件
	 * @param  int  $page 页码
	 * @param  int $cate 分类id
	 * @param  string $search 搜索的商品名字
	 * @param  array $goodsIds 除了这个数组里面的商品id
	 * @return array $gr_info 处理好的商品数据 
	 */
	public function getHotsale($page, $cate=0, $search=null, $goodsIds=array()){
		$gr = M('goods_rec');
		if ((int)$cate != 0) {//如果不是通过搜索进来的话是存在分类id的
			$cate_ids = $this->getSonCateID($cate);
			$gr_where['GR_CID'] = array('in', $cate_ids);

		}
		if ($search) { // 用户是通过搜索进来的
			$gr_where['GR_Name'] = ['like', "%".$search."%"];
		}
		
		$gr_where['GR_Is_Show'] = 1; // 是否上架 
		$gr_where['GR_Is_Delete'] = 0;
		$gr_where['GR_Type'] = ['neq', 2]; // 不是一折购
		$page_num = 5; // 默认显示5条
		$goodsInfo = $gr
		    ->where($gr_where)
		    ->field('GR_ID as id, GR_IMG as img, GR_Name as name, GR_Price as price, GR_Other_IMG as imgs, GR_Old_Price as oldPrice, GR_Browse as browse, GR_Sale as sale')
		    ->limit($page*$page_num, $page_num)
		    ->select();

		foreach ($goodsInfo as $k => $v) { // 删除已经显示的商品
			if (array_keys($goodsInfo, $v['id'])) {
				unset($goodsInfo[$k]);
			}
		}

		$gr_info = $this->bubbling($goodsInfo, 'desc'); // 调用权重
		
		foreach ($gr_info as $key => $value) {

			$gr_info[$key]['oldPrice'] = $gr_info[$key]['oldPrice'] * 0.01;//原价
			$gr_info[$key]['price'] = $gr_info[$key]['price'] * 0.01;//现价

		}
		return $gr_info;
	}
	/**
	 * 获取分类id
	 */
	public function getSonCateID($id, $ids=array()){
		$cr = M('category_rec');
		$where['CR_PID'] = $id;
		$where['CR_Type'] = 1;
		$where['CR_Is_Show'] = 1;
		$ids[] = $id;
		$cr_info = $cr->where($where)->field('CR_ID,CR_PID')->select();
		if($cr_info){
			foreach ($cr_info as $key => $value) {
				$ids = $this->getSonCateID($value['CR_ID'], $ids);
			}
		}else{
			return $ids;
		}
		return $ids;
	}

	/**
	 * 列表页点击多条件搜索的时候的一个方法  
	 * ajax请求
	 */
	public function ajaxOrder()
	{
		if (empty($_GET['sortDir']) || empty($_GET['sortName'])) { // 为空

			$goodsOrder['code'] = 0;
			$goodsOrder['data'] = null;
			$goodsOrder['msg'] = '请求数据库失败，请检查参数';
			$goodsOrder['success'] = false;

			$this->ajaxReturn($goodsOrder);
			exit;
		}


		switch ($_GET['sortName']) {

			case '综合':
				$sortName = 'GR_Sale'; // 需求： 销量占7 点击量占3
				break;

			case '价格':
				$sortName = 'GR_Price';
				break;

			case '销量':
				$sortName = 'GR_Sale';
				break;

			case '新品':
				$sortName = 'GR_Is_New';
				break;
			
			default:
				
				$goodsOrder['code'] = 0;
				$goodsOrder['data'] = null;
				$goodsOrder['msg'] = '请求数据库失败，请检查参数';
				$goodsOrder['success'] = false;

				$this->ajaxReturn($goodsOrder);
				break;
		}

		switch ($_GET['sortDir']) {

			case 'top':
				$sortDir = 'desc';
				break;

			case 'bottom':
				$sortDir = 'asc'; // 小->大
				break;
			
			case 'null':
				$sortDir = 'desc';
				break;
			
			default:

				$goodsOrder['code'] = 0;
				$goodsOrder['data'] = null;
				$goodsOrder['msg'] = '请求数据库失败，请检查参数';
				$goodsOrder['success'] = false;

				$this->ajaxReturn($goodsOrder);
				break;
		}
		if ((int)$_GET['cate'] != 0) {

			$typeIds = $this->getSonCateID($_GET['cate']);//获取分类id
			$goodsWhere['GR_CID'] = ['in', $typeIds];
		}

		if ($_GET['search']) {

			$goodsWhere['GR_Name'] = ['like', '%'.$_GET['search'].'%']; // 是否上架 
		}

		$goodsWhere['GR_Is_Show'] = 1; // 是否上架 
		$goodsWhere['GR_Is_Delete'] = 0;
		$goodsWhere['GR_Type'] = ['neq', 2]; // 不是一折购
		$goodsList = M('GoodsRec') // 商品表
			->where($goodsWhere)
			->field('GR_ID as id, GR_Name as name, GR_IMG as img, GR_Price as price, GR_Old_Price as oldPrice, GR_Sale as sale, GR_Browse as browse, GR_AddTime as addtime')
			->order("$sortName $sortDir")
			->limit(7)
			->select();

		if ($sortName == 'GR_Sale') { // 点击综合则要权重排序
			$goodsList = $this->bubbling($goodsList, $sortDir); // 调用权重排序
			
		}

		if (empty($goodsList)) {

			$goodsOrder['code'] = 0;
			$goodsOrder['data'] = null;
			$goodsOrder['msg'] = '暂无相关数据';
			$goodsOrder['success'] = true; // 数据库请求成功

		} else {

			$goodsOrder['code'] = 1;
			$goodsOrder['data'] = $goodsList;
			$goodsOrder['msg'] = '返回数据  data';
			$goodsOrder['success'] = true; // 数据库请求成功

		}

		$this->ajaxReturn($goodsOrder);
		exit;
	}

	/**
	 * 权重排序
	 * 需求： 销售量占7 浏览量占3
	 * @param  array $goods 商品数据 
	 * @param  string $order 排序条件
	 * @return array  $goods  排序完的数据
	 */
	public function bubbling($goods, $order)
	{
		foreach ($goods as $k => $v) { // bubbling权重

			$goods[$k]['bubbling'] = ($v['sale'] * 0.07) + ($v['browse'] * 0.03);
		}
		for ($i = 0, $len = count($goods) - 1; $i < $len; $i++) { // 冒泡排序

			for ($k = $i + 1 ; $k <= $len; $k++) {
			
				if ($order == 'desc') { // 降序

					 if($goods[$i]['bubbling'] < $goods[$k]['bubbling']){
                        $goodsTemp = $goods[$i];
                        $goods[$i] = $goods[$k];
                        $goods[$k] = $goodsTemp;
                    }

				} else { // 小->大 升序

					if($goods[$i]['bubbling'] > $goods[$k]['bubbling']){
                        $goodsTemp = $goods[$i];
                        $goods[$i] = $goods[$k];
                        $goods[$k] = $goodsTemp;
                    }

				}
			}
		}
		return $goods;
	}


	/**
	* 全部分类  用户点击下部导航中的全部分类触发的方法
    * @return array [第一个顶级分类和他的二级分类]
	*/
	public function classify(){

		$cr = M('category_rec');
		$where['CR_PID'] = 0;
		$where['CR_Type'] = 1;
		$where['CR_Is_Show'] = 1;

		$cr_info = $cr->where($where)
			->field('CR_ID as id, CR_Name as text ,CR_IMG as img')
			->order('CR_Sort desc')
			->select();
		
		if (!empty($cr_info)) {

			$where['CR_PID'] = $cr_info[0]['id']; // 左边框第一个分类的id
			$cr_info[0]['son'] = M('category_rec')
			   ->where($where)
			   ->field('CR_ID as id, CR_Name as title, CR_IMG as img')
			   ->select();
			

			foreach ($cr_info[0]['son'] as $key => $value) { // 追加href

				$cr_info[0]['son'][$key]['href'] = '/index.php/Home/Goods/hot_sale/cate/'.$cr_info[0]['son'][$key]['id'];
			}
		}
	
		
		$this->assign('cr_info_json',json_encode($cr_info));
		$this->display();
	}

	/**
	 * 用户点击全部分类页面左边框时候触发    ajax请求
	 * @return json 返回顶级分类和该顶级分类下的所有二级分类
	 */
	public function type() 
	{
		$TypeWhere['CR_Type'] = ['neq', 2]; // 不是一折购的
		$TypeWhere['CR_ID'] = $_GET['id'];  // 顶级分类id
		$TypeWhere['CR_Is_Show'] = 1;	// 显示

		$type['maxType'] = M('category_rec') // 顶级分类
		    ->where($TypeWhere)
			->field('CR_IMG as photo, CR_Name as name, CR_ID as id')
			->find();

		if ($type['maxType']) { // 顶级分类找到则找二级分类

			unset($TypeWhere['CR_ID']); // 删除条件ID

			$TypeWhere['CR_PID'] = $_GET['id'];  // pid

			$type['minType'] = M('category_rec') // 二级分类
			    ->where($TypeWhere)
				->field('CR_IMG as img, CR_Name as title, CR_ID as id')
				->select();

			foreach ($type['minType'] as $k => $v) { //追加 href

				$type['minType'][$k]['href'] = '/index.php/Home/Goods/hot_sale/cate/'.$v['id'];

			}
		}

		$this->ajaxReturn($type);
		exit;
	}



}