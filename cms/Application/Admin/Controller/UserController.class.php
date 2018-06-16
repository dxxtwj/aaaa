<?php
namespace Admin\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class UserController extends CommonController {
    

    //会员列表 
    public function UserList(){
        // dump($_GET);
        $info = M('User_info');
        //根据用户昵称搜索
        if(is_numeric($_GET['username'])){
            $where['UI_Mobile']=array("like","%".$_GET['username']."%");
        }else{
            $where['UI_Name']=array("like","%".$_GET['username']."%");
        }
        $user_status = I('user_status');
        if($user_status){
            $where['UI_Status'] = $user_status;
        }

        $addtime = $_GET['addtime'];
        if($addtime){
            $startTime = $addtime.' 00:00:00';
            $start = strtotime($startTime);
            $end = $start + 24 * 3600 - 1;
            $endTime = date('Y-m-d H:i:s',$end);
            // dump($startTime);
            // dump($endTime);
            // dump($start);
            // dump($end);
            $where['UI_Addtime'] = array('between',array($start,$end));
        }

        // $res = $info->where($where)->order('UI_Addtime desc')->select();
        $s_data['num'] = $info->where($where)->count();

        $page = new \Think\Page($s_data['num'],10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $page->setConfig('frist','首页');
        $page->setConfig('last','尾页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->lastSuffix==false;
        $page -> setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $show       = $page->show();// 分页显示输出
        // dump($show);
        $res = $info->where($where)->order('UI_Addtime desc')->limit($page->firstRow.','.$page->listRows)->select();
        $count = $info->count();

        foreach($res as $key=>$val){
            $res[$key]['UI_Addtime'] = date('Y-m-d H:i',$val['UI_Addtime']);
            if($val['UI_Sex'] == 1){
                $res[$key]['UI_Sex'] = '男';
            }elseif($val['UI_Sex'] == 0){
                $res[$key]['UI_Sex'] = '女';
            }else{
                $res[$key]['UI_Sex'] = '保密';
            }
        }

        if($_GET['username']){
            $this->assign('username',$_GET['username']);
        }
        if($addtime){
            $this->assign('addtime',$addtime);
        }
        $this->assign('res',$res);
        $this->assign('count',$count);
        $this->assign("show",$show);
        $this->display();
    }

    //用户详情
    public function UserDetail(){
        header('Content-type:text/html; charset=utf-8');
        $info = M('User_info');
        
        if(IS_POST && IS_AJAX){
           //  dump($_POST);
           // exit;
            $region = I('regionx');
            $where['UI_ID'] = (int)I('id');
            $data["UI_Name"] = I('realname');
            $data["UI_Status"] = (int)I('level');
            $data["UI_Sex"] = I('sex');
            $data["UI_LastUpdateTime"] = time();
            if((int)I('level') == 4){
                if($region == ''){
                    $flag['state'] = -1;
                    $flag['mes'] = '选择区域合伙人身份必须选择其管辖的区域';
                }else{
                    $wherex['UI_Status'] = 4;
                    $info_res = $info->field('UI_RegionAddress')->where($wherex)->select();
                    if($info_res){
                        foreach($info_res as $key=>$val){
                            $address = explode('@#',$val['UI_RegionAddress']);
                            foreach($region as $k=>$v){
                                if(in_array($v,$address)) {
                                    $flag['state'] = -1;
                                    $flag['mes'] = '所选区域--'.$v.'已有合伙人管辖';
                                    break;
                                }else{
                                    $flag['state'] = 1;
                                }
                            }
                        }
                        
                        if((int)$flag['state'] > 0){
                            $data['UI_RegionAddress'] = implode('@#',$region);
                            $ret = $info->where($where)->save($data);
                            if($ret){
                                $flag['state'] = 1;
                                $flag['mes'] = '修改成功';
                            }else{
                                $flag['state'] = -1;
                                $flag['mes'] = '修改失败';
                            }
                        }
                        
                    }else{
                        $data['UI_RegionAddress'] = implode('@#',$region);
                        $ret = $info->where($where)->save($data);
                        if($ret){
                            $flag['state'] = 1;
                            $flag['mes'] = '修改成功';
                        }else{
                            $flag['state'] = -1;
                            $flag['mes'] = '修改失败';
                        }
                    }
                }
            }else{
                $ret = $info->where($where)->save($data);
                if($ret){
                    $flag['state'] = 1;
                    $flag['mes'] = '修改成功';
                }else{
                    $flag['state'] = -1;
                    $flag['mes'] = '修改失败';
                }
            }
            
            $this->ajaxReturn($flag);

        }else{
            $uid = $_GET['uid'];
            $res = $info->where(array('UI_ID'=>$uid))->find();
            if($res){
                // dump($_GET);
                // dump($res);
                switch($res['UI_Status']){
                    case 1:
                        $res['UI_Status'] = '逛客';
                        break;

                    case 2:
                        $res['UI_Status'] = '创客';
                        break;

                    case 3:
                        $res['UI_Status'] = '市场经理';
                        break;

                    case 4:
                        $res['UI_Status'] = '区域合伙人';
                        break;
                }
                $res['UI_Addtime'] = date('Y-m-d H:i:s',$res['UI_Addtime']);
                if($res['UI_LastUpdateTime']){
                    $res['UI_LastUpdateTime'] = date('Y-m-d H:i:s',$res['UI_LastUpdateTime']);
                }else{
                    $res['UI_LastUpdateTime'] = '';
                }
                if($res['UI_MakerStartTime']){
                    $res['UI_MakerStartTime'] = date('Y-m-d H:i:s',$res['UI_MakerStartTime']);
                }else{
                    $res['UI_MakerStartTime'] = '';
                }
                if($res['UI_MakerLoseTime']){
                    $res['UI_MakerLoseTime'] = date('Y-m-d H:i:s',$res['UI_MakerLoseTime']);
                }else{
                    $res['UI_MakerLoseTime'] = '';
                }

                if($res['UI_FID'] != 0){
                    $name = $info->field('UI_Name')->where(array('UI_ID'=>$res['UI_FID']))->find();
                    $res['UI_FID'] = $name['UI_Name'];
                }else{
                    $res['UI_FID'] = '无';
                }
                if($res['UI_RegionAddress'] == ''){
                    $res['region'] = '无';
                }else{
                    $res['region'] = $res['UI_RegionAddress'];
                }
                // dump($res);
                
                $this->assign('res',$res);

            }else{
                $this->error('用户数据未找到');
            }
            $this->display();
        }
        
    }

    //添加用户
    public function AddUser(){
        // dump($_POST);
        // exit;
        $info = M('User_info');

        $name = I('uname');
        $sex = I('sex');
        $pwd = I('pwd');
        $mobile = I('mobile');
        $ustatus = I('ustatus');

        $c_mobile = $this->checkMobile($mobile);
        if($mobile && $c_mobile){
            $c_pwd = $this->checkPwd($pwd);
            if($pwd && $c_pwd){
                $res = $info->where(array('UI_Mobile'=>$mobile))->find();
                if(!$res){
                    //盐
                    $salt=salt();
                    //把得出的盐拼接到密码的后面，再对其使用sha1进行哈希
                    $password=sha1($pwd.$salt);
                    $add_data['UI_FID'] = 0;
                    if(I('regionprovince') != '' && I('regioncity') != '' && I('regioncounty') != ''){
                        $add_data['UI_RegionAddress'] = I('regionprovince').I('regioncity').I('regioncounty');
                    }
                    $add_data['UI_Name'] = $name;
                    $add_data['UI_Sex'] = $sex;
                    $add_data['UI_Pwd'] = $password;
                    $add_data['UI_Salt'] = $salt;
                    $add_data['UI_ImgUrl'] = '/Uploads/defaultimg/defaultimg.png';
                    $add_data['UI_Mobile'] = $mobile;
                    $add_data['UI_Status'] = (int)$ustatus;
                    if((int)$ustatus == 2){
                        $add_data['UI_MakerTime'] = time();
                    }
                    $add_data['UI_Addtime'] = time();
                    $add_data['UI_Port'] = 4;
                    $add_data['UI_Address'] = I('province').I('city').I('county');

                    $result = $info->add($add_data);
                    if($result){
                        $flag['state'] = 1;
                        $flag['mes'] = '添加用户成功';
                    }else{
                        $flag['state'] = -4;
                        $flag['mes'] = '添加用户失败';
                    }
                }else{
                    $flag['state'] = -1;
                    $flag['mes'] = '该手机要已被注册';
                }
            }else{
                $flag['state'] = -1;
                $flag['mes'] = '密码只能是数字+字母组合';
            }
        }else{
            $flag['state'] = -1;
            $flag['mes'] = '请输入正确的手机号码';
        }
        $this->ajaxReturn($flag);
        
    }

    //手机号码验证
    function checkMobile($str){ 
        $pattern = "/^1[34578]{1}\d{9}$/"; 
        if(preg_match($pattern,$str)){ 
            return true; 
        }else{ 
            return false; 
        } 
    } 
    //密码验证（只能数字+字母）
    public function checkPwd($str){
        $pattern = '/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9a-zA-Z]+$/'; 
        if(preg_match($pattern,$str)){ 
            return true; 
        }else{ 
            return false; 
        } 
    }

    //会员等级管理
    public function member_Grading(){
        // dump($_GET);
        $info = M('User_info');
        $loc = $_GET['loc'];
        if($loc){
            $where['UI_Status'] = $loc;
        }else{
             $where['UI_Status'] = 1; //默认查逛客
        }

        // $res = $info->where($where)->order('UI_Addtime desc')->select();
        $s_data['num'] = $info->where($where)->count();

        $page = new \Think\Page($s_data['num'],10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $page->setConfig('frist','首页');
        $page->setConfig('last','尾页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->lastSuffix==false;
        $page -> setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $show       = $page->show();// 分页显示输出
        // dump($show);
        $res = $info->where($where)->order('UI_Addtime desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach($res as $key=>$val){
            $res[$key]['UI_Addtime'] = date('Y-m-d H:i',$val['UI_Addtime']);
            if($val['UI_Sex'] == 1){
                $res[$key]['UI_Sex'] = '男';
            }else{
                $res[$key]['UI_Sex'] = '女';
            }
        }
        $g_num = $info->where(array('UI_Status'=>1))->count();
        $c_num = $info->where(array('UI_Status'=>2))->count();
        $s_num = $info->where(array('UI_Status'=>3))->count();
        $q_num = $info->where(array('UI_Status'=>4))->count();

        if($loc){
            $this->assign('loc',$loc);
        }else{
             $this->assign('loc',1);
        }
        $this->assign('g_num',$g_num);
        $this->assign('c_num',$c_num);
        $this->assign('s_num',$s_num);
        $this->assign('q_num',$q_num);
        $this->assign("show",$show);
        $this->assign('res',$res);
        $this->display();
    }

    //推客设置
    public function UserHistory(){
        $twitter = M('Twitter_rec');
        if(IS_POST){
            // dump($_POST);
              $data["TR_TouristsOfflineProfit"] = I('TouristsOfflineProfit');
              $data["TR_MakerOneSelfProfit"] = I('MakerOneSelfProfit');
              $data["TR_MakerOfflineProfit"] = I('MakerOfflineProfit');
              $data["TR_MakerOfflineToMaker"] = I('MakerOfflineToMaker');
              $data["TR_ManagerOneSelfProfit"] = I('ManagerOneSelfProfit');
              $data["TR_ManagerTeamToMaker"] = I('TR_ManagerTeamToMaker');
              $data["TR_ManagerTeamSalesProfit"] = I('ManagerTeamSalesProfit');
              $data["TR_FirstManagerSuperiorProfit"] = I('ManagerTeamToManager');
              $data["TR_PartnerOneSelfProfit"] = I('PartnerOneSelfProfit');
              $data["TR_PartnerRegionSpreeProfit"] = I('PartnerRegionSpreeProfit');
              $data["TR_PartnerRegionSalesProfit"] = I('PartnerRegionSalesProfit');
              $data["TR_DirectUpgrade"] = I('DirectUpgrade');
              $data["TR_UpdateTime"] = time();

              $result = $twitter->where(array('TR_ID'=>1))->save($data);
              if($result){
                    $this->success('修改成功');
              }else{
                    $this->success('修改失败');
              }

        }else{
            $res = $twitter->where(array('TR_ID'=>1))->find();
            // dump($res);
            $this->assign('res',$res);
            $this->display();

        }

    }

    // 导出excel表格
    public function orderExcel(){
        header('Content-Type:text/html;charset=gb2312');
        // 接收过来的为日期 2016-08-08
        $info = M('User_info');
        //根据用户昵称搜索
        if(is_numeric($_GET['username'])){
            $where['UI_Mobile']=array("like","%".$_GET['username']."%");
        }else{
            $where['UI_Name']=array("like","%".$_GET['username']."%");
        }
        $user_status = I('user_status');
        if($user_status){
            $where['UI_Status'] = $user_status;
        }

        $addtime = $_GET['addtime'];
        if($addtime){
            $startTime = $addtime.' 00:00:00';
            $start = strtotime($startTime);
            $end = $start + 24 * 3600 - 1;
            $endTime = date('Y-m-d H:i:s',$end);
            // dump($startTime);
            // dump($endTime);
            // dump($start);
            // dump($end);
            $where['UI_Addtime'] = array('between',array($start,$end));
        }

        $res = $info->where($where)->order('UI_Addtime desc')->select();

        // dump($res);exit;
        $data=array(); 
        foreach ($res as $key=>$val){
            // 注册时间
            $res[$key]['UI_Addtime'] = date('Y-m-d H:i:s',$val['UI_Addtime']);

            switch($val['UI_Status']){
                case 1:
                    $res[$key]['UI_Status'] = '逛客';
                    break;
                case 2:
                    $res[$key]['UI_Status'] = '创客';
                    break;

                case 3:
                    $res[$key]['UI_Status'] = '市场经理';
                    break;
                case 4:
                    $res[$key]['UI_Status'] = '区域合伙人';
                    break;
                
            }
            switch($val['UI_Sex']){
                case 0:
                    $res[$key]['UI_Sex'] = '女';
                    break;
                case 1:
                    $res[$key]['UI_Sex'] = '男';
                    break;
                case 2:
                    $res[$key]['UI_Sex'] = '保密';
                    break;
                
            }
            
            array_push($data, array(
                //这里的需要导出的内容，要注意键名跟上面的字段键名要一致
                'UI_Name'=>$val['UI_Name'],
                'UI_Status'=>$res[$key]['UI_Status'].' ',
                'UI_Mobile'=>$val['UI_Mobile'],
                'UI_Sex'=>$res[$key]['UI_Sex'],
                'UI_Province'=>$val['UI_Province'].' '.$val['UI_City'].' '.$val['UI_County'],
                'UI_Addtime'=>$res[$key]['UI_Addtime'],
                'UI_RegionProvince'=>$val['UI_RegionProvince'].' '.$val['UI_RegionCity'].' '.$val['UI_RegionCounty'],
                ));
        } 
          //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能import导入
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.Writer.Excel2007");
        import("Org.Util.PHPExcel.IOFactory.php");

        $filename="YUKI全球优品会员列表";
        $headArr=array("昵称","等级","手机号","性别","所在地址","注册时间","管辖区域");
        // dump($where);
        $this->getExcel($filename,$headArr,$data);
    }

    private function getExcel($fileName,$headArr,$data){
        header('Content-Type:text/html;charset=utf-8');
        //对数据进行检验
        if(empty($data) || !is_array($data)){
            // die("data must be a array");
            // die("数据必须是一个数组");
            die("未找到相关数据");
        }
        //检查文件名
        if(empty($fileName)){
            exit;
        }

        $date = date("Y_m_d",time());
        $fileName .= "_{$date}.xls";

      //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();
      
        //设置表头
        $key = ord("A");
        foreach($headArr as $v){
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        
        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        foreach($data as $key => $rows){ //行写入
            $span = ord("A");
            foreach($rows as $keyName=>$value){// 列写入
                $j = chr($span);
                $objActSheet->setCellValue($j.$column, $value);
                $span++;
            }
            $column++;
      }

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //重命名表
        // $objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }

    //升级规则设置
    public function upgrade(){
        $sys = M('Upgrade_rec');
        $goods = M('Goods_rec');
        if(IS_POST){
            // dump($_POST);
            // exit;
            $yesorno = (int)I('yesorno');
            if($yesorno == 1){//经理处选择指定商品，两种方式并存
                $data["UR_UpgradeManager"] = I('UpgradeManager');
                $data["UR_UpgradeHaveMaker"] = I('UpgradeMaker');
                $data["UR_UpgradeMakerTeam"] = I('UpgradeMakerTeam');
                $data["UR_SpreeGoodsID"] = (int)I('mangood');
                if(I('creategood') == ''){
                     $data["UP_MakeSpreeGoodsID"] = 0;
                }else{
                    $data["UP_MakeSpreeGoodsID"] = (int)I('creategood');
                    $data["UP_UpdateTime"] = time();
                    $data["UR_UpgradeStatus"] = 2;
                }
            }else{
                $data["UR_UpgradeManager"] = I('UpgradeManager');
                $data["UR_UpgradeHaveMaker"] = I('UpgradeMaker');
                $data["UR_UpgradeMakerTeam"] = I('UpgradeMakerTeam');
                $data["UP_UpdateTime"] = time();
                $data["UR_UpgradeStatus"] = 1;
                if(I('creategood') == ''){
                     $data["UP_MakeSpreeGoodsID"] = 0;
                }else{
                    $data["UP_MakeSpreeGoodsID"] = (int)I('creategood');
                }
            }
           
            $result = $sys->where(array('UR_ID'=>1))->save($data);
            if($result){
                $this->success('修改成功');
            }else{
                $this->success('修改失败');
            }
        }else{
            $res = $sys->where(array('UR_ID'=>1))->find();
            //经理指定商品
            $goodsname1 = $goods->field('GR_Name')->where(array('GR_ID'=>(int)$res['UR_SpreeGoodsID']))->find();
            if($goodsname1){
                $res['goodsname1'] = $goodsname1;
            }else{
                $res['goodsname1'] = '';
            }

            //创客
            $goodsname2 = $goods->field('GR_Name')->where(array('GR_ID'=>(int)$res['UP_MakeSpreeGoodsID']))->find();
            if($goodsname2){
                $res['goodsname2'] = $goodsname2;
            }else{
                $res['goodsname2'] = '';
            }

            // dump($res);
            $this->assign('res',$res);
            $this->display();
        }
    }

    //搜索指定商品
    public function SelectGoods(){
        $goods = M('Goods_rec');
        $goods_name = I('goods_name');
        if($goods_name){
            $where['GR_Name'] = array('like','%'.$goods_name.'%');
        }
        $where['GR_Is_Show'] = 1;
        $where['GR_Is_Delete'] = 0;
        $where['GR_Type'] = 3;//大礼包

        $res = $goods->field('GR_ID,GR_Name,GR_IMG')->where($where)->select();
        // dump($res);
        if($res){
            $this->ajaxReturn($res);
        }else{
            $this->ajaxReturn(0);
        }
    }   

    //我的团队
    public function my_team_yuanlai(){
        $info = M('User_info');
        $uname = (int)I('uid');
        $loc = I('loc');
        if($loc){
            $where['UI_Status'] = (int)$loc;
        }else{
             $where['UI_Status'] = 1; //默认查逛客
        }
        $where['UI_FID'] = $uname;
        $s_data['num'] = $info->where($where)->count();

        $page = new \Think\Page($s_data['num'],10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $page->setConfig('frist','首页');
        $page->setConfig('last','尾页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->lastSuffix==false;
        $page -> setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $show       = $page->show();// 分页显示输出
        // dump($show);
        $res = $info->where($where)->order('UI_Addtime desc')->limit($page->firstRow.','.$page->listRows)->select();

        foreach($res as $key=>$val){
            $res[$key]['UI_Addtime'] = date('Y-m-d H:i',$val['UI_Addtime']);
            if($val['UI_Sex'] == 1){
                $res[$key]['UI_Sex'] = '男';
            }else{
                $res[$key]['UI_Sex'] = '女';
            }
            switch($val['UI_Status']){
                case 1:
                    $res[$key]['UI_Status'] = '逛客';
                    break;

                case 2:
                    $res[$key]['UI_Status'] = '创客';
                    break;

                case 3:
                    $res[$key]['UI_Status'] = '市场经理';
                    break;

                case 4:
                    $res[$key]['UI_Status'] = '区域合伙人';
                    break;
            }
            if($val['UI_RegionProvince'] == null || $val['UI_RegionCity'] == null || $val['UI_RegionCounty'] == null){
                $res[$key]['region'] = '无';
            }else{
                $res[$key]['region'] = $val['UI_RegionProvince'].'-'.$val['UI_RegionCity'].'-'.$val['UI_RegionCounty'];
            }
        }

        $tou_where['UI_Status'] = 1;
        $tou_where['UI_FID'] = $uname;
        $g_num = $info->where($tou_where)->count();

        $m_where['UI_Status'] = 2;
        $m_where['UI_FID'] = $uname;
        $c_num = $info->where($m_where)->count();

        $n_where['UI_Status'] = 3;
        $n_where['UI_FID'] = $uname;
        $s_num = $info->where($n_where)->count();

        $p_where['UI_Status'] = 4;
        $p_where['UI_FID'] = $uname;
        $q_num = $info->where($p_where)->count();

        if($loc){
            $this->assign('loc',$loc);
        }else{
             $this->assign('loc',1);
        }
        // dump($res);
        $this->assign('g_num',$g_num);
        $this->assign('c_num',$c_num);
        $this->assign('s_num',$s_num);
        $this->assign('q_num',$q_num);
        $this->assign("show",$show);
        $this->assign('res',$res);
        $this->display();
    }
    //我的团队2
    public function my_team(){
        $info = M('User_info');
        $uname = (int)I('uid');
        $loc = I('loc');
        $u_status = $info->field('UI_Status,UI_RegionAddress')->where(array('UI_ID'=>$uname))->find();
        if($loc){
            $where['UI_Status'] = (int)$loc;
        }else{
             $where['UI_Status'] = 1; //默认查逛客
        }
        if((int)$u_status['UI_Status'] == 4){
            $region = explode('@#',$u_status['UI_RegionAddress']);
            $where['UI_ID'] = array('neq',$uname);
            $where['UI_Address'] = array('in',$region);
        }else{
            $where['UI_FID'] = $uname;
        }
        $s_data['num'] = $info->where($where)->count();

        $page = new \Think\Page($s_data['num'],10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $page->setConfig('frist','首页');
        $page->setConfig('last','尾页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->lastSuffix==false;
        $page -> setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $show       = $page->show();// 分页显示输出
        // dump($show);
        $res = $info->where($where)->order('UI_Addtime desc')->limit($page->firstRow.','.$page->listRows)->select();

        foreach($res as $key=>$val){
            $res[$key]['UI_Addtime'] = date('Y-m-d H:i',$val['UI_Addtime']);
            if($val['UI_Sex'] == 1){
                $res[$key]['UI_Sex'] = '男';
            }else{
                $res[$key]['UI_Sex'] = '女';
            }
            switch($val['UI_Status']){
                case 1:
                    $res[$key]['UI_Status'] = '逛客';
                    break;

                case 2:
                    $res[$key]['UI_Status'] = '创客';
                    break;

                case 3:
                    $res[$key]['UI_Status'] = '市场经理';
                    break;

                case 4:
                    $res[$key]['UI_Status'] = '区域合伙人';
                    break;
            }
            if($val['UI_RegionAddress'] == null || $val['UI_RegionAddress'] == ''){
                $res[$key]['region'] = '无';
            }else{
                $res[$key]['region'] = $val['UI_RegionAddress'];
            }
        }
        if((int)$u_status['UI_Status'] == 4){
            $region = explode('@#',$u_status['UI_RegionAddress']);

            $where_o['UI_ID'] = array('neq',$uname);
            $where_o['UI_Address'] = array('in',$region);
            $where_o['UI_Status'] = 1;
            $g_num = $info->where($where_o)->count();
            
            $where_x['UI_ID'] = array('neq',$uname);
            $where_x['UI_Address'] = array('in',$region);
            $where_x['UI_Status'] = 2;
            $c_num = $info->where($where_x)->count();
            
            $where_y['UI_ID'] = array('neq',$uname);
            $where_y['UI_Address'] = array('in',$region);
            $where_y['UI_Status'] = 3;
            $s_num = $info->where($where_y)->count();
            
            $where_z['UI_ID'] = array('neq',$uname);
            $where_z['UI_Address'] = array('in',$region);
            $where_z['UI_Status'] = 4;
            $q_num = $info->where($where_z)->count();
        
        }else{
            $tou_where['UI_Status'] = 1;
            $tou_where['UI_FID'] = $uname;
            $g_num = $info->where($tou_where)->count();

            $m_where['UI_Status'] = 2;
            $m_where['UI_FID'] = $uname;
            $c_num = $info->where($m_where)->count();

            $n_where['UI_Status'] = 3;
            $n_where['UI_FID'] = $uname;
            $s_num = $info->where($n_where)->count();

            $p_where['UI_Status'] = 4;
            $p_where['UI_FID'] = $uname;
            $q_num = $info->where($p_where)->count();
        }

        if($loc){
            $this->assign('loc',$loc);
        }else{
             $this->assign('loc',1);
        }
        // dump($res);
        $this->assign('g_num',$g_num);
        $this->assign('c_num',$c_num);
        $this->assign('s_num',$s_num);
        $this->assign('q_num',$q_num);
        $this->assign("show",$show);
        $this->assign('res',$res);
        $this->display();
    }

    //推广订单
    public function promotion_order(){
        $or = M('Order_rec');
        $uname = (int)I('uid');
        $orderType = I('orderType');
        if(!$orderType){
            $where['OR_State'] = array('in','1,2,3,4,5,8');
        }else{
            switch((int)$orderType){
                case 10:
                    $where['OR_State'] = array('in','1,2,3,4,5,8');
                    break;
                // case 0:
                //     $where['OR_State'] = 99;
                //     break;
                case 1:
                    $where['OR_State'] = 1;
                    break;
                case 2:
                    $where['OR_State'] = 2;
                    break;
                case 3:
                    $where['OR_State'] = 3;
                    break;
                // case 4:
                //     $where['OR_State'] = 
                //     break;
                case 5:
                    $where['OR_State'] = array('in','4,5,8');
                    break;
            }
        }

        $result = array();
        $where['OR_DeleteState'] = 0;
        $where['OR_FID'] = $uname;
        $info['num'] = $or->where($where)->count();

        $page = new \Think\Page($info['num'],10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $page->setConfig('frist','首页');
        $page->setConfig('last','尾页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->lastSuffix==false;
        $page -> setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $show       = $page->show();// 分页显示输出
        // dump($show);
        $orderList = $or->where($where)->order('OR_PayTime desc,OR_ID desc')->limit($page->firstRow.','.$page->listRows)->select();
        $info['total'] =0.00;

        $og = M('Order_goods');
        foreach ($orderList as $key => $val) {
            $og_where['OG_OID'] = $val['OR_ID'];
            $og_info = $og->where($og_where)->select();
            $small_total = 0.00;
            foreach ($og_info as $key1 => $value1) {
                $og_info[$key1]['OG_Price'] = $value1['OG_Price']/100;
                $og_info[$key1]['OG_Img'] = ltrim($og_info[$key1]['OG_Img'],".");
                if(!$value1['OG_Guige']){
                    $og_info[$key1]['OG_Guige'] = '';
                }else{
                    $og_info[$key1]['OG_Guige'] = $value1['OG_Guige'];
                }
                if($key1==0){
                    $og_info[$key1]['rowspan']=count($og_info);
                }else{
                    $og_info[$key1]['rowspan']=0;
                }
            }
            $info['total'] += $val['OR_OrderTotal'];  //订单总金额
            $yingshou = $val['OR_OrderTotal'];
            
            $orderList[$key]['small_total'] = number_format($val['OR_GoodsPrice'],2);  //商品价格
            $orderList[$key]['OR_YouFei'] = number_format($val['OR_YouFei'],2); //邮费
            $orderList[$key]['yingshou'] = number_format($yingshou,2); 

            $orderList[$key]['og_info'] = $og_info;
            $orderList[$key]['OR_CreateTime'] = date('Y-m-d H:i:s',$val['OR_CreateTime']);
            if($orderList[$key]['OR_PayTime']){
                $orderList[$key]['OR_PayTime'] = date('Y-m-d H:i:s',$val['OR_PayTime']);
            }else{
                $orderList[$key]['OR_PayTime'] = '';
            }
        }
        $info['total'] = number_format($info['total'],2);

        if($orderType){
            $o_type = $orderType;
        }else{
            $o_type = 1;
        }

        $alln['OR_DeleteState'] = 0;
        $alln['OR_State'] = array('in','1,2,3,4,5,8');
        $alln['OR_FID'] = $uname;
        $all_num = $or->where($alln)->count();//全部订单

        // $weiw['OR_DeleteState'] = 0;
        // $weiw['OR_State'] = 0;
        // $weiw['OR_FID'] = $uname;
        // $wei_num = $or->where($weiw)->count();//代付款

        $daiw['OR_DeleteState'] = 0;
        $daiw['OR_State'] = 1;
        $daiw['OR_FID'] = $uname;
        $dai_num = $or->where($daiw)->count();//待发货

        $shouw['OR_DeleteState'] = 0;
        $shouw['OR_State'] = 2;
        $shouw['OR_FID'] = $uname;
        $shou_num = $or->where($shouw)->count();//待收货

        $wanw['OR_DeleteState'] = 0;
        $wanw['OR_State'] = 3;
        $wanw['OR_FID'] = $uname;
        $wan_num = $or->where($wanw)->count();//已完成

        // $guanw['OR_DeleteState'] = 0;
        // $guanw['OR_State'] = array('in','6,7');
        // $guanw['OR_FID'] = $uname;
        // $guan_num = $or->where($guanw)->count();//已关闭

        $houw['OR_DeleteState'] = 0;
        $houw['OR_State'] = array('in','4,5,8');//售后
        $houw['OR_FID'] = $uname;
        $hou_num = $or->where($houw)->count();

        $this->assign('all_num',$all_num);
        $this->assign('wei_num',$wei_num);
        $this->assign('dai_num',$dai_num);
        $this->assign('shou_num',$shou_num);
        $this->assign('wan_num',$wan_num);
        $this->assign('guan_num',$guan_num);
        $this->assign('hou_num',$hou_num);
        $this->assign('orderType',$o_type);
        $this->assign("orderList",$orderList);
        $this->assign('order_res',$result);
        $this->assign("info",$info);
        $this->assign("show",$show);
        $this->display();
    }


    //收益明细
    public function income_details(){
        // dump($_GET);
        $uname = (int)I('uid');
        $state = (int)I('get_type');
        if(!$state){
            $state = 1;
        }
        // if(!$state){
        //     $state = 0;
        // }

        switch($state){
            // case 0:
            //     $rest = array();
            //     $res1 = $this->ShoppingBenefits($uname);
            //     $res2 = $this->SalesRevenue_two($uname);
            //     $res3 = $this->MonthlyReturn($uname);
            //     array_push($rest,$res1);
            //     array_push($rest,$res2);
            //     array_push($rest,$res3);

            //     $res = array();
            //     if($res1 == -1 && $res2 == -1 && $res3 == -1){
            //         $res = -1;
            //     }else{
            //         foreach($rest as $k=>$v){
            //             if($v != -1){
            //                 foreach($v as $key=>$val){
            //                     array_push($res,$val);
            //                 }
            //             }else{
            //                 continue;
            //             }
            //         }
            //     }
            //     $type = 0;
            //     break;

            case 1:
                $res = $this->ShoppingBenefits($uname);
                $type = 1;
                // dump($res);
                break;

            case 2:
                $res = $this->SalesRevenue_two($uname);
                $type = 2;
                break;

            case 3:
                $res = $this->MonthlyReturn($uname);
                $type = 3;
                break;
        }

        $this->assign('back_uid',$uname);
        $this->assign('type',$type);
        $this->assign('order',$res);
        $this->display();
    }

    //购物收益
    public function ShoppingBenefits($uname){
        $order = M('Order_rec');
        //1.自己购物收益
        $gou_where['OR_UID'] = $uname;
        $gou_where['OR_State'] = array('in','3,4,5,8');
        $gou_where['OR_DeleteState'] = 0;
        $gou_where['OR_ISPackage'] = 1;//只限购买的普通商品
        $gou_order = $order->field('OR_Key,OR_SelfCommission,OR_CreateTime,OR_GoodsPrice')->where($gou_where)->order('OR_QianShouTime desc')->select();
        $gou_arr = array();
        // dump($gou_order);
        if($gou_order){
            foreach($gou_order as $k=>$v){
                //判断自己收益是否为0，当身份为逛客的时候，自己购买是没有提成的
                if((float)$v['OR_SelfCommission'] == 0.00){
                    continue;
                }else{
                    $gou_arr[$k]['num'] = $v['OR_Key'];
                    $gou_arr[$k]['status'] = '购物收益';
                    $gou_arr[$k]['score'] = '+'.$v['OR_SelfCommission'];
                    $gou_arr[$k]['price'] = number_format($v['OR_GoodsPrice'],2);
                    $gou_arr[$k]['date'] = date('Y-m-d H:i:s',$v['OR_CreateTime']);
                }
            }
            if($gou_arr == null || empty($gou_arr)){
                $gou_arr = -1;
            }
        }else{
            $gou_arr = -1;
        }
        return $gou_arr;
    }
    //销售收益
    public function SalesRevenue($uname){
        $order = M('Order_rec');
        $us = M('User_info');
        //销售收益（直属下线产生的订单），包括大礼包，普通商品
        $xiao_where['OR_FID'] = $uname;
        $xiao_where['OR_State'] = array('in','3,4,5,8');
        $xiao_where['OR_DeleteState'] = 0;
        $xiao_order = $order->field('OR_UID,OR_FID,OR_SID,OR_Key,OR_FCommission,OR_SCommission,OR_CreateTime,OR_GoodsPrice,OR_ISPackage')->where($xiao_where)->order('OR_QianShouTime desc')->select();
        $xiao_arr = array();
        if($xiao_order){
            foreach($xiao_order as $k=>$v){
                //判断直属下线产生的佣金，如果FID=SID,说明当时直属下线下单时，我是经理，佣金存放在OR_SCommission中
                $us_info = $us->field('UI_Name')->where(array('UI_ID'=>$v['OR_UID']))->find();
                if($v['OR_FID'] == $v['OR_SID']){
                    //下单时可能计算错误，SCommission==0，所以不显示
                    if((float)$v['OR_SCommission'] == 0.00){
                        continue;
                    }else{
                        $xiao_arr[$k]['num'] = $v['OR_Key'];
                        $xiao_arr[$k]['name'] = $us_info['UI_Name'];
                        if((int)$v['OR_ISPackage'] == 1){
                            $xiao_arr[$k]['type'] = '普通商品收益';
                        }else{
                            $xiao_arr[$k]['type'] = '大礼包收益';
                        }
                        $xiao_arr[$k]['status'] = '销售收益';
                        $xiao_arr[$k]['score'] = '+'.$v['OR_SCommission'];
                        $xiao_arr[$k]['price'] = number_format($v['OR_GoodsPrice'],2);
                        $xiao_arr[$k]['date'] = date('Y-m-d H:i:s',$v['OR_CreateTime']);
                    }

                }else{
                    //下单时可能计算错误，FCommission==0，所以不显示
                    if((float)$v['OR_FCommission'] == 0.00){
                        continue;
                    }else{
                        $xiao_arr[$k]['num'] = $v['OR_Key'];
                        $xiao_arr[$k]['name'] = $us_info['UI_Name'];
                        if((int)$v['OR_ISPackage'] == 1){
                            $xiao_arr[$k]['type'] = '普通商品收益';
                        }else{
                            $xiao_arr[$k]['type'] = '大礼包收益';
                        }
                        $xiao_arr[$k]['status'] = '销售收益';
                        $xiao_arr[$k]['score'] = '+'.$v['OR_FCommission'];
                        $xiao_arr[$k]['price'] = number_format($v['OR_GoodsPrice'],2);
                        $xiao_arr[$k]['date'] = date('Y-m-d H:i:s',$v['OR_CreateTime']);
                    }
                }
                
            }
            if($xiao_arr == null || empty($xiao_arr)){
                $xiao_arr = -1;
            }
        }else{
            $xiao_arr = -1;
        }
        return $xiao_arr;
    }
    //销售收益(2,查询下线所有销售订单)
    public function SalesRevenue_two($uname){
        $order = M('Order_rec');
        $us = M('User_info');
        //销售收益（直属下线产生的订单），包括大礼包，普通商品
        $map['OR_State&OR_DeleteState'] =array(array('in','3,4,5,8'),0,'_multi'=>true);
        $map['_query'] = 'OR_FID='.$uname.'&OR_SID='.$uname.'&OR_PID='.$uname.'&OR_MID='.$uname.'&_logic=or';
        $xiao_order = $order->field('OR_UID,OR_FID,OR_SID,OR_PID,OR_MID,OR_Key,OR_FCommission,OR_SCommission,OR_FirstManagerSuperiorComm,OR_PCommission,OR_CreateTime,OR_GoodsPrice,OR_ISPackage')->where($map)->order('OR_QianShouTime desc')->select();
        // var_dump($xiao_order);
        $xiao_arr = array();
        if($xiao_order){
            foreach($xiao_order as $k=>$v){
                $us_info = $us->field('UI_Name')->where(array('UI_ID'=>$v['OR_UID']))->find();

            
                //1.直属上级跟经理是同一个
                if((int)$v['OR_FID']==$uname && (int)$v['OR_SID']==$uname){
                    $sc = (float)$v['OR_FCommission'] + (float)$v['OR_SCommission'];
                    if((float)$sc <=0){
                        continue;
                    }else{
                        $xiao_arr[$k]['score'] = '+'.$sc;
                    }
                    
                }elseif((int)$v['OR_FID']==$uname && (int)$v['OR_PID']==$uname){//2.直属上级跟合伙人是同一个
                    $pc = (float)$v['OR_FCommission'] + (float)$v['OR_PCommission'];
                    if((float)$pc <=0){
                        continue;
                    }else{
                        $xiao_arr[$k]['score'] = '+'.$pc;
                    }
                }else{
                    if((int)$v['OR_FID'] == $uname){
                        if((float)$v['OR_FCommission'] == 0.00){
                            continue;
                        }else{
                            $xiao_arr[$k]['score'] = '+'.$v['OR_FCommission'];
                        }
                    }elseif((int)$v['OR_SID'] == $uname){
                        if((float)$v['OR_SCommission'] == 0.00){
                            continue;
                        }else{
                            $xiao_arr[$k]['score'] = '+'.$v['OR_SCommission'];
                        }
                    }elseif((int)$v['OR_PID'] == $uname){
                        if((float)$v['OR_PCommission'] == 0.00){
                            continue;
                        }else{
                            $xiao_arr[$k]['score'] = '+'.$v['OR_PCommission'];
                        }
                    }elseif((int)$v['OR_MID'] == $uname){
                        if((float)$v['OR_FirstManagerSuperiorComm'] == 0.00){
                            continue;
                        }else{
                            $xiao_arr[$k]['score'] = '+'.$v['OR_FirstManagerSuperiorComm'];
                        }
                    }
                }

                $xiao_arr[$k]['num'] = $v['OR_Key'];
                $xiao_arr[$k]['name'] = $us_info['UI_Name'];
                if((int)$v['OR_ISPackage'] == 1){
                    $xiao_arr[$k]['type'] = '普通商品收益';
                }else{
                    $xiao_arr[$k]['type'] = '大礼包收益';
                }
                $xiao_arr[$k]['status'] = '销售收益';
                $xiao_arr[$k]['price'] = number_format($v['OR_GoodsPrice'],2);
                $xiao_arr[$k]['date'] = date('Y-m-d',$v['OR_CreateTime']);
                $xiao_arr[$k]['time'] = date('H:i',$v['OR_CreateTime']);
                
            }
            if($xiao_arr == null || empty($xiao_arr)){
                $xiao_arr = -1;
            }
        }else{
            $xiao_arr = -1;
        }
        return $xiao_arr;
    }
    //月结收益
    public function MonthlyReturn($uname){
        $monthly = M('Monthly_rec');

        $month_order = $monthly->where(array('MR_UID'=>$uname))->select();
        $month_arr = array();
        if($month_order){
            foreach($month_order as $k=>$v){
                $month_arr[$k]['num'] = $v['MR_Key'];
                $month_arr[$k]['status'] = '月结收益';
                $month_arr[$k]['score'] = '+'.number_format($v['MR_MonthProfit'],2);
                $month_arr[$k]['date'] = date('Y-m-d H:i:s',$v['MR_ShouYiTime']);
                switch($v['MR_ProfitType']){
                    case 1:
                        $month_arr[$k]['type'] = '区域收益';
                        break;

                    case 2:
                        $month_arr[$k]['type'] = '销售团队收益';
                        break;

                    case 3:
                        $month_arr[$k]['type'] = '下级市场经理收益';
                        break;
                }
                
            }
        }else{
            $month_arr = -1;
        }
        return $month_arr;
    }


}