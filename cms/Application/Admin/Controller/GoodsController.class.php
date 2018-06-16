<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Model;

header("Content-type: text/html; charset=utf-8");
class GoodsController extends CommonController {
	public function index(){
        $gr = M('Goods_rec');
        $fo = M('format_option');
        // $ir = M('Images_rec');

        $cr = M('category_rec');
        // $cr_where['CR_Is_Show'] = 1;
        $cr_where['CR_PID'] = 0;
        $cr_where['CR_Type'] = 1;
        $cr_info = $cr->where($cr_where)->select();  //分类列表

        $page_num = 10;

        $where['GR_Is_Delete'] = 0;
        $where['GR_Type'] = 1;

        $type = trim($_GET['type']);//左侧状态筛选 。全部all、上架sale、下架nosale、告罄nostock
        // dump($type);
        //左侧状态筛选的数量
        $type_count['all'] = $gr->where($where)->count();//全部
        $type_count['sale'] = $gr->where($where)->where(array('GR_Is_Show' => 1))->count();//上架
        $type_count['nosale'] = $gr->where($where)->where(array('GR_Is_Show' => 0))->count();//下架
        $type_count['nostock'] = 0;//告罄
        $check_nostock = $gr->field('GR_ID,GR_Is_Options,GR_Stock')->where($where)->select();
        foreach ($check_nostock as $key_stock => $value_stock) {
            if($value_stock['GR_Is_Options'] == 1){
                $fo_stock_where['FO_GID'] = $value_stock['GR_ID'];
                $fo_stock_where['FO_Stock'] = 0;
                $fo_info = $fo->where($fo_stock_where)->field('FO_ID')->find();
                unset($fo_stock_where);
                if($fo_info){
                    $type_count['nostock'] ++;
                }

            }else{
                if((int)$value_stock['GR_Stock'] === 0){
                    $type_count['nostock'] ++;
                }
            }
        }
            
        if($type == 'all'){

        }elseif($type == 'sale'){
            $where['GR_Is_Show'] = 1;
        }elseif($type == 'nosale'){
            $where['GR_Is_Show'] = 0;
        }elseif($type == 'nostock'){

        }else{

        }

        //返回属性列表，可自建一张属性表
        $attribute[] = array('name'=>'猜你喜欢','id'=>1);
        $attribute[] = array('name'=>'包邮','id'=>2);
        $attribute[] = array('name'=>'精品推荐','id'=>3);
        $attribute[] = array('name'=>'热销火爆','id'=>4);
        $attribute[] = array('name'=>'限量抢购','id'=>5);
        $attribute[] = array('name'=>'新品推荐','id'=>6);
        //属性筛选
        $attr = (int)$_GET['attribute'];
        foreach ($attribute as $k_attr => $v_attr) {
            if($v_attr['id'] == $attr){
                $attribute[$k_attr]['is_check'] = 1;
            }else{
                $attribute[$k_attr]['is_check'] = 0;
            }
        }
        if($attr == 1){
            $where['GR_Is_Recommend'] = 1;
        }elseif($attr == 2){
            $where['GR_Is_Freeshipping'] = 1;
        }elseif($attr == 3){
            $where['GR_Is_Fine'] = 1;
        }elseif($attr == 4){
            $where['GR_Is_Hot'] = 1;
        }elseif($attr == 5){
            $where['GR_Is_Limit'] = 1;
        }elseif($attr == 6){
            $where['GR_Is_New'] = 1;
        }

        $this->assign('attribute',$attribute);
        //商品名搜索
        if(trim($_GET['goods_name'])){
            $str = str_replace("%","\%",trim($_GET['goods_name']));//特殊符号转义
            // $where['GR_Name'] = array('like','%'.$str.'%');
            // $where['_string']='(GR_Name like "%'.$str.'%") OR (GR_Barcode like "%'.$str.'%")';//复合查询，适用于匹配多个字段
            $where['GR_Name'] = array('like','%'.$str.'%');
        }
        //时间筛选，此为商品创建时间
        if($_GET['starttime'] && !$_GET['endtime']){

            $where['GR_AddTime'] = array('egt',strtotime($_GET['starttime']));

        }elseif(!$_GET['endtime'] && $_GET['endtime']){

            $where['GR_AddTime'] = array('elt',strtotime($_GET['endtime']));

        }elseif($_GET['starttime'] && $_GET['endtime']){

            $where['GR_AddTime'] = array('between',array(strtotime($_GET['starttime']),strtotime($_GET['endtime'])));
        }
        //分类筛选
        if(trim($_GET['cid'])){
            $ids = $this->getSonCateID(trim($_GET['cid']));
            // dump($ids);
            $where['GR_CID'] = array('in',$ids);
        }
        //由子分类，查出前面的父类，并在分类列表中将其标记为选中，使得页面显示相应的分类信息
        $check_category = $this->getParentCategory(trim($_GET['cid']));
        krsort($check_category);
        sort($check_category);
        foreach ($check_category as $k1 => $v1) {
            $check_category_arr[] = $v1['CR_ID'];
        }
        foreach ($cr_info as $k2 => $v2) {
            if(in_array($v2['CR_ID'], $check_category_arr)){
                $cr_info[$k2]['is_check'] = 1;//选中
            }else{
                $cr_info[$k2]['is_check'] = 0;
            }
        }
        $cr_info_all[] = $cr_info;
        for ($i=1; $i < count($check_category); $i++) { 
            $cr_where['CR_PID'] = $check_category[$i-1]['CR_ID'];
            $cr_info_son = $cr->where($cr_where)->select();
            foreach ($cr_info_son as $k3 => $v3) {
                if(in_array($v3['CR_ID'], $check_category_arr)){
                    $cr_info_son[$k3]['is_check'] = true;
                }else{
                    $cr_info_son[$k3]['is_check'] = false;
                }
            }
            $cr_info_all[] = $cr_info_son;
        }

        //分页，除了告罄商品筛选
        if($type != 'nostock'){
            $count = $gr->where($where)->count();
            $page = new \Think\Page($count,$page_num);
            $page->setConfig('first','首页');
            $page->setConfig('prev','上一页');
            $page->setConfig('next','下一页');
            $show = $page->show();
            $limit = $page->firstRow.','.$page->listRows;
        }

        $gr_info = $gr->where($where)->limit($limit)->order('GR_ID desc')->select();
        foreach ($gr_info as $key => $value) {
            //告罄商品需全查，从中筛选库存为0的商品（包括多规格，只要存在有库存为0的规格）
            if($type == 'nostock'){
                if($value['GR_Is_Options'] == 1){
                    $fo_stock_where['FO_GID'] = $value['GR_ID'];
                    $fo_stock_where['FO_Stock'] = 0;
                    $fo_info = $fo->where($fo_stock_where)->field('FO_ID')->find();
                    unset($fo_stock_where);
                    if($fo_info){

                    }else{
                        unset($gr_info[$key]);
                        continue;
                    }

                }else{
                    if((int)$value['GR_Stock'] === 0){

                    }else{
                        unset($gr_info[$key]);
                        continue;
                    }
                }
            }
            $gr_info[$key]['GR_Old_Price'] = $value['GR_Old_Price']*0.01;
            $gr_info[$key]['GR_Cost_Price'] = $value['GR_Cost_Price']*0.01;
            $gr_info[$key]['GR_Price'] = $value['GR_Price']*0.01;
            //多规格显示默认信息
            if($value['GR_Is_Options'] == 1){
                $fo_info = $fo->where(array('FO_GID'=>$value['GR_ID']))->order('FO_Stock ASC')->find();
                $gr_info[$key]['GR_Old_Price'] = $fo_info['FO_Old_Price']*0.01;
                $gr_info[$key]['GR_Cost_Price'] = $fo_info['FO_Cost_Price']*0.01;
                $gr_info[$key]['GR_Price'] = $fo_info['FO_Price']*0.01;
                $gr_info[$key]['GR_Stock'] = $fo_info['FO_Stock'];
            }
            // if($value['GR_IID']){
            //     $ir_info = $ir->where(array('IR_ID'=>$value['GR_IID']))->find();
            //     $gr_info[$key]['goods_img'] = $ir_info['IR_Path'].$ir_info['IR_Img'];
            // }
            if($value['GR_AddTime']){
                $gr_info[$key]['GR_AddTime'] = date('Y-m-d H:i:s',$value['GR_AddTime']);
            }

        }
        ksort($gr_info);
        //告罄商品的分页
        if($type == 'nostock'){
            $count = count($gr_info);
            $page = new \Think\Page($count,$page_num);
            $page->setConfig('first','首页');
            $page->setConfig('prev','上一页');
            $page->setConfig('next','下一页');
            $show = $page->show();
            $limit = $page->firstRow.','.$page->listRows;
            $gr_info = array_slice($gr_info, $page->firstRow,$page->listRows);
            // dump($gr_info1);
            // $gr_info2 = array_slice($gr_info, 1,2);
            // dump($gr_info);
        }
            
        $this->assign('cr_info_all',$cr_info_all);
        $this->assign('getInfo',$_GET);
        $this->assign('type_count',$type_count);
        $this->assign('count',$count);
        $this->assign('show',$show);
        $this->assign('res',$gr_info);
        $this->display();
    }


    //禁用、启用商品
    public function setGoodsShow(){
        $gid = I('gid');
        if($gid!=null){
            $gr = M('goods_rec');
            if((int)I('state')>0){

                $ids = trim($gid,',');
                if($ids){
                    $where['GR_ID'] = array('in',$ids);
                }
                if((int)I('state')==2){
                    $save['GR_Is_Show'] = 1;
                    $info = $gr->where($where)->save($save);
                }elseif((int)I('state')==1){
                    $save['GR_Is_Show'] = 0;
                    $info = $gr->where($where)->save($save);
                }
                if($info){
                    $this->ajaxReturn(1);
                }else{
                    $this->ajaxReturn(0);
                }
            }else{
                $isShow = $gr->where(array('GR_ID' => $gid))->getField('GR_Is_Show');
                if((int)$isShow === 0){
                    //不显示改为显示
                    $save['GR_Is_Show'] = 1;
                }elseif((int)$isShow === 1){
                    //显示改为不显示
                    $save['GR_Is_Show'] = 0;
                }
                $ids = trim($gid,',');
                if($ids){
                    $where['GR_ID'] = array('in',$ids);
                }
                $info = $gr->where($where)->save($save);

                if($info){
                    $this->ajaxReturn(1);
                }else{
                    $this->ajaxReturn(0);
                }
            }
                
        }else{
            $this->ajaxReturn(-1);
        }
    }


    public function add(){
        
        if(IS_WIN){
            $type = "C:/wamp64/www/yuki"; // 个人本地路径
        }else{
            $type ='.';
        }
        if(IS_POST){
            //对传入的参数进行排序。为了键名重置
            foreach ($_POST as $key => $value) {
                $sort_arr = array();
                foreach ($value as $key1 => $value1) {
                    $sort_arr[] = $value1;
                }
                if($sort_arr){
                    $_POST[$key] = $sort_arr;
                }
                // sort($_POST[$key]);
            }
            // dump($_POST);
            // exit;
            $hasoption = I('hasoption');//是否启用多规格;启用：1;不启用：null
            
            $gid = (int)I('gid');//商品ID

            $gr_map['GR_Name'] = $goods_name = trim(I('goods_name'));//商品名称
            $gr_map['GR_Weight'] = $goods_weight = (int)I('goods_weight');//商品重量
            $gr_map['GR_Old_Price'] = $goods_oldPrice = (double)I('goods_oldPrice')*100;//现价
            $gr_map['GR_Cost_Price'] = $goods_costPrice = (double)I('goods_costPrice')*100;//成本价
            $gr_map['GR_Price'] = $goods_price = (double)I('goods_price')*100;//现价
            $gr_map['GR_Sort'] = $goods_sort = (int)(I('goods_sort'));//排序
            $gr_map['GR_Is_Show'] = $is_show = (int)(I('is_show'));//是否启用
            $gr_map['GR_Sale'] = $goods_sale = (int)(I('goods_sale'));//虚拟销量
            $gr_map['GR_IMG'] = $goods_img = I('path');//图片路径
            $gr_map['GR_Less'] = $goods_less = I('less');//减库存方式
            $gr_map['GR_Type'] = 1;//商品类型，此为普通商品
            $gr_map['GR_Stock'] = $goods_stock = I('goods_stock');//库存
            $gr_map['GR_Unit'] = $goods_unit = I('goods_unit');//单位


            if(I('recommend')==1){
                $gr_map['GR_Is_Recommend'] = $goods_recommend = I('recommend');//是否推荐到首页
            }else{
                $gr_map['GR_Is_Recommend'] = $goods_recommend = 0;
            }
            if(I('fine')==1){
                $gr_map['GR_Is_Fine'] = $goods_fine = I('fine');//是否热销
            }else{
                $gr_map['GR_Is_Fine'] = $goods_fine = 0;
            }
            if(I('hot')==1){
                $gr_map['GR_Is_Hot'] = $goods_hot= I('hot');//是否爆品
            }else{
                $gr_map['GR_Is_Hot'] = $goods_hot = 0;
            }
            if(I('limit')==1){
                $gr_map['GR_Is_Limit'] = $goods_limit = I('limit');//是否限量
            }else{
                $gr_map['GR_Is_Limit'] = $goods_limit = 0;
            }
            if(I('new')==1){
                $gr_map['GR_Is_New'] = $goods_new = I('new');//是否新品
            }else{
                $gr_map['GR_Is_New'] = $goods_new = 0;
            }
            if(I('freeshipping')==1){
                $gr_map['GR_Is_Freeshipping'] = $goods_freeshipping = I('freeshipping');//是否免邮
            }else{
                $gr_map['GR_Is_Freeshipping'] = $goods_freeshipping = 0;
            }

            $goods_other_path = I('other_path');//其他图片路径
            if($goods_other_path){
                $gr_map['GR_Other_IMG'] = implode(",", $goods_other_path);
            }
            
            $goods_category = I('goods_category');//分类数组，选择传入的最末一个分类ID
            if($goods_category){
                $category_count = count($goods_category);
                $gr_map['GR_CID'] = $goods_category[$category_count-1];
            }

            //商品参数
            $param_key = I('param_key');
            $param_value = I('param_value');
            foreach ($param_key as $pk_k => $pk_v) {
            	if(!$pk_v && !$param_value[$pk_k]){
            		continue;
            	}
                $param_map['key'] = $pk_v;
                $param_map['value'] = $param_value[$pk_k];
                $goods_param[] = $param_map;
            }
            if($goods_param!=null){
                $goods_param_json = json_encode($goods_param);
                $gr_map['GR_Parameter'] = $goods_param_json;
            }
            // dump($goods_param_json);
            // exit;
            $content = I('editorValue');//商品详情


            $spec_id = I('spec_id');
            $spec_title = I('spec_title');//规格名;颜色+大小
            $option_ids = I('option_ids');
            $option_title = I('option_title');//规格项名组合;红+小
            $options_id = I('option_id');//规格组合id

            $option_OldPrice = I('option_OldPrice');//市场价
            $option_stock = I('option_stock');//库存
            $option_CostPrice = I('option_CostPrice');//成本价
            $option_price = I('option_price');//价格
            $option_weight = I('option_weight');//重量
            $option_img = I('option_path');//规格图片ID


            if($hasoption == '1' && !$spec_title){
                $this->error('无多规格数据');
            }

            //开启事务
            // $transdb = new Model();
            // $transdb->startTrans();
            $gr = M('goods_rec');//商品表
            $fo = M('format_option');//小规格表
            $fr = M('formats_rec');//组合表
            $or = M('options_rec');//大规格表
            
        
            $path = $type.'/Uploads/goods_describe/';
            if(!file_exists($path)){
                @mkdir($path);
            }


            if($gid>0){//此为商品修改
                //此为有规格
                if($hasoption == '1'){
                    //传递的规格组合id是否有所变动(存在空值)
                    $option_isChange = false;//没有变动
                    foreach ($options_id as $k1 => $v1) {
                        if(!$v1){
                            $option_isChange = true;
                            break;
                        }
                    }
                    $gr_map['GR_Is_Options'] = 1;
                    $gr_map['GR_UpdateTime'] = time();
                    $gr_map['GR_Describe'] = '/Uploads/goods_describe/'.$gid.'.txt';
                    $gr_save = $gr->where(array('GR_ID'=>$gid))->save($gr_map);
                    
                    // dump($option_isChange);
                    // exit;
                    if($option_isChange){//规格变动，delete、add--需要重置规格
                        //删除旧有规格
                        $or->where(array('OR_GID'=>$gid))->delete();
                        $fr->where(array('FR_GID'=>$gid))->delete();
                        $fo->where(array('FO_GID'=>$gid))->delete();

                        $fr_count = count($spec_title);
                            // dump($spec_title);
                        foreach ($spec_title as $k4 => $v4) {
                            $fr_map['FR_Name'] = $v4;
                            $fr_map['FR_GID'] = $gid;
                            $fr_map['FR_AddTime'] = time();
                            $fr_id = $fr->add($fr_map);

                            $map['FO_FIDS'] .= $fr_id.',';

                            $or_name_arr = I('spec_item_title_'.$spec_id[$k4]);
                            // dump('spec_item_title_'.$spec_id[$k4]);
                            foreach ($or_name_arr as $k5 => $v5) {
                                $or_map['OR_Name'] = $v5;
                                $or_map['OR_GID'] = $gid;
                                $or_map['OR_FID'] = $fr_id;
                                $or_map['OR_AddTime'] = time();
                                $or_id = $or->add($or_map);
                                $or_id_arr['k'][] = $v5;
                                $or_id_arr['v'][] = $or_id;
                            }


                        }

                        if($or_id_arr){
                            foreach ($option_title as $k6 => $v6) {
                                $ot_arr = explode("@#", $v6);
                                $or_id_splice = '';
                                foreach ($ot_arr as $k7 => $v7) {
                                    $index = array_search($v7, $or_id_arr['k']);//索引
                                    $or_id_splice .= $or_id_arr['v'][$index].",";
                                }
                                $fo_id_newarr[] = rtrim($or_id_splice,",");
                            }
                        }
                        $map['FO_FIDS'] = trim($map['FO_FIDS'],",");
                        $fo_count = count($options_id);
                        for($i = 0;$i<$fo_count;$i++){


                            $map['FO_OIDS'] = $fo_id_newarr[$i];
                            $map['FO_GID'] = $gid;
                            $map['FO_Name'] = $option_title[$i];
                            $map['FO_Stock'] = $option_stock[$i];
                            $map['FO_Old_Price'] = $option_OldPrice[$i]*100;
                            $map['FO_Cost_Price'] = $option_CostPrice[$i]*100;
                            $map['FO_Price'] = $option_price[$i]*100;
                            $map['FO_Weight'] = $option_weight[$i];
                            $map['FO_IMG'] = $option_img[$i];
                            $map['FO_AddTime'] = time();
                            $fo_info = $fo->add($map);
                            if($fo_info){
                                $is_success = true;
                            }
                        }
                        if($fo_info){
                            $write =file_put_contents($type.$gr_map['GR_Describe'], $content);

                            $this->success('修改成功');
                        }else{
                            $this->error('修改失败');
                        }

                    }else{
                        //规格无变动，只有库存、价格的变动，只需修改某字段，save
                        foreach ($options_id as $k2 => $v2) {
                            $map['FO_Name'] = $option_title[$k2];
                            $map['FO_Stock'] = $option_stock[$k2];
                            $map['FO_Old_Price'] = $option_OldPrice[$k2]*100;
                            $map['FO_Cost_Price'] = $option_CostPrice[$k2]*100;
                            $map['FO_Price'] = $option_price[$k2]*100;
                            $map['FO_Weight'] = $option_weight[$k2];
                            $map['FO_IMG'] = $option_img[$k2];
                            $map['FO_UpdateTime'] = time();
                            $fo_info = $fo->where(array('FO_ID'=>$v2))->save($map);
                            if($fo_info){
                                $is_success = true;
                            }
                        }

                        $write =file_put_contents($type.$gr_map['GR_Describe'], $content);
                        if($is_success||$write){//老板的要求，就算没改动任何东西，也要回复保存成功
                            $this->success('修改成功');
                        }else{
                            $this->error('修改失败');
                        }

                    }
                }else{
                    //选择无规格，删除之前可能存在的多规格信息
                    $or->where(array('OR_GID'=>$gid))->delete();
                    $fr->where(array('FR_GID'=>$gid))->delete();
                    $fo->where(array('FO_GID'=>$gid))->delete();
                    $gr_map['GR_Is_Options'] = 0;
                    $gr_map['GR_UpdateTime'] = time();
                    $gr_map['GR_Describe'] = '/Uploads/goods_describe/'.$gid.'.txt';
                    $gr_save = $gr->where(array('GR_ID'=>$gid))->save($gr_map);
                    $write =file_put_contents($type.$gr_map['GR_Describe'], $content);
                    if($gr_save || $write){
                        $this->success('修改成功');
                    }else{
                        $this->error('修改失败');
                    }
                }

            }else{//此为商品添加，逻辑与商品修改类似，但无需判断多规格变动情况，直接入库即可
                
                $gr_map['GR_AddTime'] = time();
                if($hasoption == '1'){
                    $gr_map['GR_Is_Options'] = 1;
                }
                $gr_id = $gr->add($gr_map);
                $gr_map['GR_Describe'] = '/Uploads/goods_describe/'.$gr_id.'.txt';
                $gr_save = $gr->where(array('GR_ID'=>$gr_id))->save($gr_map);
                
                if($hasoption == '1'){
                    $fr_count = count($spec_title);
                    
                    foreach ($spec_title as $k4 => $v4) {//大规格表
                        $fr_map['FR_Name'] = $v4;
                        $fr_map['FR_GID'] = $gr_id;
                        $fr_map['FR_AddTime'] = time();
                        $fr_id = $fr->add($fr_map);

                        $map['FO_FIDS'] .= $fr_id.',';

                        $or_name_arr = I('spec_item_title_'.$spec_id[$k4]);
                        // dump('spec_item_title_'.$spec_id[$k4]);
                        foreach ($or_name_arr as $k5 => $v5) {//小规格表
                            $or_map['OR_Name'] = $v5;
                            $or_map['OR_GID'] = $gr_id;
                            $or_map['OR_FID'] = $fr_id;
                            $or_map['OR_AddTime'] = time();
                            $or_id = $or->add($or_map);
                            $or_id_arr['k'][] = $v5;
                            $or_id_arr['v'][] = $or_id;
                        }


                    }

                    if($or_id_arr){
                        foreach ($option_title as $k6 => $v6) {
                            $ot_arr = explode("@#", $v6);
                            $or_id_splice = '';
                            foreach ($ot_arr as $k7 => $v7) {
                                $index = array_search($v7, $or_id_arr['k']);//索引
                                $or_id_splice .= $or_id_arr['v'][$index].",";
                            }
                            $fo_id_newarr[] = rtrim($or_id_splice,",");
                        }
                    }
                    $map['FO_FIDS'] = trim($map['FO_FIDS'],",");
                    $fo_count = count($options_id);
                    for($i = 0;$i<$fo_count;$i++){//组合表


                        $map['FO_OIDS'] = $fo_id_newarr[$i];
                        $map['FO_GID'] = $gr_id;
                        $map['FO_Name'] = $option_title[$i];
                        $map['FO_Stock'] = $option_stock[$i];
                        $map['FO_Old_Price'] = $option_OldPrice[$i]*100;
                        $map['FO_Cost_Price'] = $option_CostPrice[$i]*100;
                        $map['FO_Price'] = $option_price[$i]*100;
                        $map['FO_Weight'] = $option_weight[$i];
                        $map['FO_IMG'] = $option_img[$i];
                        $map['FO_AddTime'] = time();
                        $fo_info = $fo->add($map);
                        if($fo_info){
                            $is_success = true;
                        }
                    }
                    if($fo_info){
                        $write =file_put_contents($type.$gr_map['GR_Describe'], $content);
                        $this->success('添加成功');
                    }else{
                        $this->error('添加失败');
                    }
                }else{
                    if($gr_id){
                        $write =file_put_contents($type.$gr_map['GR_Describe'], $content);
                        $this->success('添加成功');
                    }else{
                        $this->error('添加失败');
                    }
                }

            }

        }else{
            $cr = M('category_rec');
            // $cr_where['CR_Is_Show'] = 1;
            $cr_where['CR_PID'] = 0;
            $cr_where['CR_Type'] = 1;
            $cr_info = $cr->where($cr_where)->select();

            // $br = M('Brand_rec');
            // $br_where['BR_Is_Show'] = 1;
            // $br_where['BR_ID'] = 0;
            // $br_info = $br->where($br_where)->select();

            //进入商品修改 页面信息显示
            if((int)I('gid')>0){
                $gr = M('goods_rec');
                $gr_info = $gr->where(array('GR_ID'=>I('gid')))->find();
                if($gr_info){
                    $read =file_get_contents($type.$gr_info['GR_Describe']);
                    $gr_info['content'] = $read;
                    // dump($type.$gr_info['GR_Describe']);
                    $gr_info['GR_Old_Price'] = $gr_info['GR_Old_Price']*0.01;
                    $gr_info['GR_Cost_Price'] = $gr_info['GR_Cost_Price']*0.01;
                    $gr_info['GR_Price'] = $gr_info['GR_Price']*0.01;


                    // $ir = M('Images_rec');
                    // if($gr_info['GR_IMG']){
                    //     $ir_info = $ir->where(array('IR_ID'=>$gr_info['GR_IMG']))->find();
                    //     $gr_info['goods_img'] = $ir_info['IR_Path'].$ir_info['IR_Img'];
                    // }
                    if($gr_info['GR_Other_IMG']){
                        $gr_info['goods_other_imgs'] = explode(",", $gr_info['GR_Other_IMG']);
                        // foreach ($other_imgid_arr as $k5 => $v5) {
                        //     $ir_info = $ir->where(array('IR_ID'=>$v5))->find();
                        //     $other_img_info["id"] = $ir_info['IR_ID'];
                        //     $other_img_info["img"] = $ir_info['IR_Path'].$ir_info['IR_Img'];
                        //     $gr_info['goods_other_imgs'][] = $other_img_info;
                        // }
                    }
                    if($gr_info['GR_Parameter']){
                        $gr_info['GR_Parameter'] = json_decode($gr_info['GR_Parameter'],true);
                    }

                    $check_category = $this->getParentCategory($gr_info['GR_CID']);
                    krsort($check_category);
                    sort($check_category);
                    foreach ($check_category as $k1 => $v1) {
                        $check_category_arr[] = $v1['CR_ID'];
                    }
                    foreach ($cr_info as $k2 => $v2) {
                        if(in_array($v2['CR_ID'], $check_category_arr)){
                            $cr_info[$k2]['is_check'] = true;
                        }else{
                            $cr_info[$k2]['is_check'] = false;
                        }
                    }
                    $res[] = $cr_info;
                    for ($i=1; $i < count($check_category); $i++) { 
                        $cr_where['CR_PID'] = $check_category[$i-1]['CR_ID'];
                        $cr_info_son = $cr->where($cr_where)->select();
                        foreach ($cr_info_son as $k3 => $v3) {
                            if(in_array($v3['CR_ID'], $check_category_arr)){
                                $cr_info_son[$k3]['is_check'] = true;
                            }else{
                                $cr_info_son[$k3]['is_check'] = false;
                            }
                        }
                        $res[] = $cr_info_son;
                    }
                    // dump($check_category);


                }
                $this->assign('res',$res);
                $this->assign('br_info',$br_info);
                $this->assign('gr_info',$gr_info);
                // dump($gr_info);
            }else{
                foreach ($cr_info as $key => $value) {
                    $cr_info[$key]['is_check'] = false;
                }
                foreach ($br_info as $key => $value) {
                    $br_info[$key]['is_check'] = false;
                }
                $res[] = $cr_info;
                // dump(count($res));
                $this->assign('add_type',$_GET['add_type']);
                $this->assign('res',$res);
                $this->assign('br_info',$br_info);
            }
            $this->display();
        }
            
        // dump($cr_info);

    }

    //返回多规格信息
    public function getFormatOption(){
        $gid = I('gid');
        if((int)$gid>0){
            $gr = M('goods_rec');
            $gr_info = $gr->field('GR_Is_Options')->where(array('GR_ID'=>$gid))->find();
            if($gr_info['GR_Is_Options'] == 1){
                $info['is_options'] = true;
            }else{
                $info['is_options'] = false;
            }

            if($info['is_options']){
                $fr = M('formats_rec');
                $or = M('options_rec');
                $fo = M('format_option');
                // $ir = M('Images_rec');
                    
                $fr_info = $fr->field('FR_ID,FR_Name')->where(array('FR_GID'=>$gid))->order('FR_ID ASC')->select();
                foreach ($fr_info as $k1 => $v1) {
                    $or_info = $or->field('OR_ID,OR_Name')->where(array('OR_FID'=>$v1['FR_ID']))->order('OR_ID ASC')->select();
                    foreach ($or_info as $k2 => $v2) {
                        $arr[0][] = $v2['OR_ID'];
                        $arr[1][] = ($k1+1)."_".($k2+1);
                        $or_info[$k2]['ids'] = ($k1+1)."_".($k2+1);
                    }
                    $fr_info[$k1]['item'] = $or_info;
                }
                $info['format'] = $fr_info;


                $fo_info = $fo->where(array('FO_GID'=>$gid))->order('FO_ID ASC')->select();
                foreach ($fo_info as $k3 => $v3) {
                    $fo_info[$k3]['FO_Old_Price'] = $v3['FO_Old_Price']*0.01;
                    $fo_info[$k3]['FO_Cost_Price'] = $v3['FO_Cost_Price']*0.01;
                    $fo_info[$k3]['FO_Price'] = $v3['FO_Price']*0.01;
                    // if($fo_info[$k3]['FO_IID']){
                    //     $ir_info = $ir->where(array('IR_ID'=>$fo_info[$k3]['FO_IID']))->find();
                    //     $fo_info[$k3]['FO_Iaddress'] = $ir_info['IR_Path'].$ir_info['IR_Img'];
                    // }

                    $fo_arr = explode(",", $v3['FO_OIDS']);
                    $ids = '';
                    foreach ($fo_arr as $k4 => $v4) {
                        $index = array_search($v4, $arr[0]);
                        if($index||$index===0){
                            $ids .= $arr[1][$index]."_";
                        }else{
                            $ids .= "0_0_";
                        }
                    }
                    $ids = rtrim($ids,"_");
                    $fo_info[$k3]['ids'] = $ids;
                }
                $info['format_option'] = $fo_info;

            }
            $this->ajaxReturn($info);
        }
    }


    public function del(){
        $gid = I('gid');
        if($gid!=null){
            $gr = M('goods_rec');
            $ids = trim($gid,',');
            if($ids){
                $where['GR_ID'] = array('in',$ids);
            }
            $isDel = $gr->where($where)->setField('GR_Is_Delete',"1");
            if($isDel){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(0);
            }


            
        }else{
            $this->ajaxReturn(-1);
        }
    }


    public function setRecommend(){
        $gid = I('gid');
        if($gid!=null){
            $gr = M('goods_rec');
            if((int)I('state')>0){

                $ids = trim($gid,',');
                if($ids){
                    $where['GR_ID'] = array('in',$ids);
                }
                if((int)I('state')==2){
                    $save['GR_Is_Recommend'] = 1;
                    $info = $gr->where($where)->save($save);
                }elseif((int)I('state')==1){
                    $save['GR_Is_Recommend'] = 0;
                    $info = $gr->where($where)->save($save);
                }
                if($info){
                    $this->ajaxReturn(1);
                }else{
                    $this->ajaxReturn(0);
                }
            }else{
                $isShow = $gr->where(array('GR_ID' => $gid))->getField('GR_Is_Recommend');
                if((int)$isShow === 0){
                    //不显示改为显示
                    $save['GR_Is_Recommend'] = 1;
                }elseif((int)$isShow === 1){
                    //显示改为不显示
                    $save['GR_Is_Recommend'] = 0;
                }
                $ids = trim($gid,',');
                if($ids){
                    $where['GR_ID'] = array('in',$ids);
                }
                $info = $gr->where($where)->save($save);

                if($info){
                    $this->ajaxReturn(1);
                }else{
                    $this->ajaxReturn(0);
                }
            }
                
        }else{
            $this->ajaxReturn(-1);
        }
    }
    
    public function detail(){

        $this->display();
    }

    //获取父类
    private function getParentCategory($cid,$data=null){
        $cr = M('category_rec');
        $cr_info = $cr->field('CR_ID,CR_PID,CR_Name')->where(array('CR_ID'=>$cid))->find();
        if($cr_info){
            $data[] = $cr_info;
            if((int)$cr_info['CR_PID'] == 0){
                return $data;
            }else{
                return $this->getParentCategory($cr_info['CR_PID'],$data);
            }
        }else{
            return false;
        }
    }

    //查询下一级子类
    public function checkSoncategory(){
        $cid = I('cid');
        $cr = M('category_rec');
        $cr_info = $cr->where(array('CR_PID'=>$cid))->select();
        if($cr_info){
            $info['status'] = true;
            $info['info'] = $cr_info;
        }else{
            $info['status'] = false;
        }
        $this->ajaxReturn($info);
    }
    
    //获取所有子类id
    public function getSonCateID($id,$ids=array()){
        $cr = M('category_rec');
        $where['CR_PID'] = $id;
        $where['CR_Type'] = 1;
        $where['CR_Is_Show'] = 1;
        $ids[] = $id;
        $cr_info = $cr->where($where)->field('CR_ID,CR_PID')->select();
        if($cr_info){
            foreach ($cr_info as $key => $value) {
                $ids = $this->getSonCateID($value['CR_ID'],$ids);
                // dump($ca);
            }
        }else{
            return $ids;
        }
        return $ids;
    }

    //导入Excel表格
    public function Stu_Excel(){      
        // dump($_FILES);
        // exit;
        //接收前台文件  
        $goods_type = I('goods_type'); 
        $ex = $_FILES['file_stu']; 
        if (!empty($_FILES['file_stu']['name']) && $goods_type){
            $file_types = explode(".", $_FILES['file_stu']['name'] );
            $file_type = $file_types[count($file_types) - 1];
            /*判别是不是.xls文件，判别是不是excel文件*/
            if (strtolower ( $file_type ) != "xls" && strtolower ( $file_type ) != "xlsx"){
                $this->error ( '不是Excel文件，重新上传' );
            }
            /*以时间来命名上传的文件*/
            $str = date('Ymdhis'); 
            $file_name = $str . "." . $file_type;
            if(IS_WIN){
                $type = 'C:/wamp64/www/yuki';
            }else{
                $type = '.';
            }
            $path = $type.'/Uploads/excel/goods/'.$file_name;//设置移动路径  
            // dump($path);
            $result = move_uploaded_file($ex['tmp_name'],$path);  
            // dump($result);
            //表用函数方法 返回数组  
            $exfn = $this->_readExcel($path,$goods_type);  
            //重定向
            // $this->redirect('input');
            if($exfn&&$goods_type==1){
                $this->success('导入成功',U('Goods/index'));  
            }elseif(!$exfn && $goods_type==1){
                $this->error('导入失败',U('Goods/index')); 
            }elseif($exfn && $goods_type==3){
                $this->error('导入成功',U('Goods/bigBag')); 
            }elseif(!$exfn && $goods_type==3){
                $this->error('导入失败',U('Goods/bigBag')); 
            }
            // dump($exfn); 
        }else{
            $this->error('请选择文件');
        } 
        
    }  
  
    //创建一个读取excel数据，可用于入库  
    private function _readExcel($filename,$goods_type){      
        // 引用PHPexcel 类  
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.Writer.Excel2007");
        import("Org.Util.PHPExcel.IOFactory.php");
        //创建PHPExcel对象，注意，不能少了\
        $PHPExcel=new \PHPExcel();
        $file_types = explode(".", $filename);
        $extension = strtolower($file_types[count($file_types) - 1]);

        // dump($extension);
        if ($extension =='xlsx') {
            $PHPReader = new \PHPExcel_Reader_Excel2007();
            $PHPExcel = $PHPReader ->load($filename);
        } else if ($extension =='xls') {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            $PHPExcel = $PHPReader ->load($filename);
        } 
        // $PHPExcel=\PHPExcel_IOFactory::load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet=$PHPExcel->getSheet(0);
        //获取总列数
        $allColumn=$currentSheet->getHighestColumn();
        //获取总行数
        $allRow=$currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for($currentRow=3;$currentRow<=$allRow;$currentRow++){
            //从哪列开始，A表示第一列
            for($currentColumn='A';$currentColumn!='AJ';$currentColumn++){
                //数据坐标
                $address=$currentColumn.$currentRow;
                //读取到的数据，保存到数组$arr中
                $arr[$currentRow][$currentColumn]=$currentSheet->getCell($address)->getValue();
            }
        
        }
        // return $arr;
       
        $gr = M('goods_rec');
        $cr = M('category_rec');
        $gr->startTrans();
        $flag = true;
        foreach($arr as $key => $val){
            $data_res['GR_Name'] = trim($val[B]);
            if($val[C]){
            	$cr_info = $cr->field('CR_ID')->where(array('CR_Name'=>$val[C]))->find();
	            if($cr_info){
	            	$data_res['GR_CID'] = $cr_info['CR_ID'];
	            }
            }
            if($val[D]){
                $data_res['GR_Barcode'] = $val[D];
            }

            if($val[E]){
                $data_res['GR_Origin'] = $val[E];
            }
            if($val[F]){
                $data_res['GR_Brand'] = $val[F];
            }
            if($val[G]){
                $data_res['GR_Carton'] = $val[G];
            }
            if((int)$val[H]){
                $data_res['GR_Sort'] = (int)$val[H];
            }

            if($val[I]){
                $data_res['GR_Price'] = (int)($val[I]*100);
            }

            if($val[J]){
                $data_res['GR_Cost_Price'] = (int)($val[J]*100);
            }
            if((int)$val[K]){
                $data_res['GR_Stock'] = (int)$val[K];
            }

            if((int)$val[L]){
                $data_res['GR_Weight'] = (int)$val[L];
            }

            if((int)$val[M]){
                $data_res['GR_Sale'] = (int)$val[M];
            }
            if($val[N]){
                $data_res['GR_Unit'] = (int)$val[N];
            }
            // [{"key":"\u9762\u6599","value":"\u7eaf\u68c9"},{"key":"\u4ea7\u5730","value":"\u4e2d\u56fd"}]
            if($val[O]){
            	$val[O] = trim($val[O]);
            	$val[O] = trim($val[O],"|");
            	$param_arr = explode("|", $val[J]);
            	foreach ($param_arr as $k1 => $v1) {
            		$param_splice = explode("-", $v1);
            		if(count($param_splice) == 2){
            			$param['key'] = $param_splice[0];
            			$param['value'] = $param_splice[1];
            		}else{
            			continue;
            		}
            		$params[] = $param;
            	}
            	if($params){
            		$data_res['GR_Parameter'] = json_encode($params);
            	}
            }
            $data_res['GR_Type'] = $goods_type;
            if($val[A]){
                $data_res['GR_UpdateTime'] = time();
                $where['GR_ID'] = $val[A];
                $res = $gr->where($where)->save($data_res);
                if(!$res){
                    $flag = false;
                    break;
                }
            }else{
                $data_res['GR_AddTime'] = time();
                unset($where);
                $res = $gr->add($data_res);
                if(!$res){
                    $flag = false;
                    break;
                }
            }
            // dump($res);
            unset($data_res);    
        }
        // foreach($arr as $key => $val){
        //     $where['GR_Barcode'] = $val[B];
        //     $save['GR_Unit'] = $val[N];
        //     $save['GR_Sale'] = $val[O];
        //     $save['GR_Stock'] = $val[P];
        //     $res = $gr->where($where)->save($save);
        //     // echo $res['GR_Unit']."<br />";
        // }
        // exit;
        if($flag){
            $gr->commit();
            return true;
        }else{
            $gr->rollback();
            return false;
        }
    } 

    public function ExcelDownload(){
        // $path = '/Uploads/excel/goods/添加修改商品模板.xlsx';
        $path = '/Uploads/excel/goods/base.xlsx';
        
        if(IS_WIN){
            $type='C:/wamp64/www/yuki';
        }else{
            $type='.';
        }
        $file_xls=$type.$path;   //   文件的保存路径

        $example_name=basename($file_xls);  //获取文件名

        header('Content-Description: File Transfer'); 
        header('Content-Type: application/octet-stream'); 
        header('Content-Disposition: attachment; filename='.mb_convert_encoding($example_name,"gb2312","utf-8"));  //转换文件名的编码 
        header('Content-Transfer-Encoding: binary'); 
        header('Expires: 0'); 
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0'); 
        header('Pragma: public'); 
        header('Content-Length: ' . filesize($file_xls)); 
        ob_clean(); 
        flush(); 
        readfile($file_xls);
    }
    
}