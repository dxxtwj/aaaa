<?php
namespace Home\Controller;
use Think\Controller;
class OpenController extends Controller {
	public function isopen(){
		$sys = M('System_rec');

		$res = $sys->field('SR_ISSiteOpen,SR_ShopName')->where(array('SR_ID'=>1))->find();
		if($res){
            $flag['title'] = $res['SR_ShopName'];
			if((int)$res['SR_ISSiteOpen'] == 0){
				$flag['state'] = -1;
				$flag['msg'] = '网站还在维护中哦...';
			}elseif((int)$res['SR_ISSiteOpen'] == 1){
				$flag['state'] = 1;
			}
		}

		$this->ajaxReturn($flag);
	}

	public function asd(){
		$imgname = "http://f.hiphotos.baidu.com/image/pic/item/060828381f30e92465929cfb45086e061c95f7c2.jpg"; 
		$res = $this->getImage($imgname,'./Uploads','111.jpg',1);
		dump($res);
	}

	
	//TP获取远程图片
	public function getImage($url,$save_dir='',$filename='',$type=0){
        if(trim($url)==''){
            return array('file_name'=>'','save_path'=>'','error'=>1);
        }
        if(trim($save_dir)==''){
            $save_dir='./';
        }
        if(trim($filename)==''){//保存文件名
            $ext=strrchr($url,'.');
        if($ext!='.gif'&&$ext!='.jpg'&&$ext!='.png'&&$ext!='.jpeg'){
            return array('file_name'=>'','save_path'=>'','error'=>3);
        }
            $filename=time().rand(0,10000).$ext;
        }
        if(0!==strrpos($save_dir,'/')){
            $save_dir.='/';
        }
        //创建保存目录
        if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
            return array('file_name'=>'','save_path'=>'','error'=>5);
        }
        //获取远程文件所采用的方法
        if($type){
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            //当请求https的数据时，会要求证书，加上下面这两个参数，规避ssl的证书检查
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE );
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            $img=curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $img=ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2=@fopen($save_dir.$filename,'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);
        return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
    }

	

}