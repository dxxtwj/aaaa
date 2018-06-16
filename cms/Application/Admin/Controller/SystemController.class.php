<?php
namespace Admin\Controller;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class SystemController extends CommonController {
    public function Systems(){
    	$sys = M('System_rec');
 
    	if(IS_POST){
    		// dump($_POST);
      //       dump($_FILES);
            $file = $_FILES['qrcode']['name'];
      //       exit;
    		$data["SR_AutoReceipt"] = I('receipt');
			$data["SR_OrderClose"] = I('orderclose');
			$data["SR_KeFuPhone"] = I('kefu');
			$data["SR_KeFuWeChat"] = I('wechat');
			$data["SR_ShopName"] = I('shopname');
			$data["SR_ISCustomerService"] = I('custom');
			$data["SR_OrderExchangeGoods"] = I('changeGoods');
			$data["SR_ISSiteOpen"] = I('site');
			$data["SR_ComissionEdu"] = I('edu');
            $data["SR_Switch"] = I('timer');
			$data["SR_UpdateTime"] = time();
            if($file != ''){
                $upload = new \Think\Upload();// 实例化上传类
                $image  = new \Think\Image(); //实例化图片处理类
                $upload->maxSize   =     3145728 ;// 设置附件上传大小
                $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->rootPath  =     './Uploads/defaultimg/'; // 设置附件上传根目录
                $upload->savePath  =     ''; // 设置附件上传（子）目录
                $upload->thumb = true;//设置需要生成缩略图，仅对图像文件有效

                // 上传文件
                $info = $upload->upload();

                $image = new \Think\Image();
                foreach($info as $val){
                    $image->open('./Uploads/defaultimg/'.$val['savepath'].$val['savename']);// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
                    $image->thumb(150,150)->save('./Uploads/defaultimg/'.$val['savepath'].'s_'.$val['savename']);
                    @unlink('./Uploads/defaultimg/'.$val['savepath'].$val['savename']);  //删除原图
                    $filename = '/Uploads/defaultimg/'.$val['savepath'].'s_'.$val['savename'];
                    $data['SR_QrcodeImg'] = $filename;
                }
            }
            
    		$result = $sys->where(array('SR_ID'=>1))->save($data);
    		if($result){
    			$this->success('修改成功');
    		}else{
    			$this->success('修改失败');
    		}
    	}else{
    		$res = $sys->where(array('SR_ID'=>1))->find();
    		// dump($res);
    		$this->assign('res',$res);
    		$this->display();
    	}
    	
        
    }
}