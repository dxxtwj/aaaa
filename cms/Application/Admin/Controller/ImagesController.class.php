<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Model;

header("Content-type: text/html; charset=utf-8");
class ImagesController extends Controller {
	
    public function uploads(){
        $type = I('uploadType');
        // $imgs_model = D('ImagesRec');
        $info = $this->upload($_FILES['file'],$type);
        if($info){
            $this->ajaxReturn($info);
        }else{
            $this->ajaxReturn(0);
        }
    }
    private function upload($files,$type){
	    if($type == 'goods'){
			$map['IR_Type'] = 1;
			$dir = "/goods_imgs/";
		}elseif($type == 'category' || $type == 'category_banner' || $type == 'category_PCtop'|| $type == 'category_PCleft'){
			$map['IR_Type'] = 2;
			$dir = "/category_imgs/";
		}elseif($type == 'brand'){
			$map['IR_Type'] = 3;
			$dir = "/brand_imgs/";
		}elseif($type == 'banner'){
			$map['IR_Type'] = 4;
			$dir = "/banner_imgs/";
		}elseif($type == 'navigation'){
			$map['IR_Type'] = 5;
			$dir = "/navigation_imgs/";
		}else{
			return false;
		}
		$upload = new \Think\Upload();// 实例化上传类    
		$upload->maxSize   	=	3145728 ;// 设置附件上传大小    
		$upload->exts      	=	array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
		// $upload->savePath  =      '/uploads_goods_img/'; // 设置附件上传目录    // 上传文件    
		$upload->savePath  	=   $dir ; // 设置附件上传目录    // 上传文件    
		// $info   =   $upload->upload(array($_FILES['pic']));  
		$info =	$upload->upload(array($files));  
		// var_dump('./Uploads/'.$info['savepath'].$info['savename']);exit;
		if(!$info) {
			// 上传错误提示错误信息        
			// $this->error($upload->getError());
			echo $upload->getError();
			exit;
		}else{
			// 上传成功        
			$image = new \Think\Image(); 
			foreach($info as $val){
				$image->open('./Uploads'.$val['savepath'].$val['savename']);
				// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
				$image->thumb(150, 150)->save('./Uploads'.$val['savepath'].'s_'.$val['savename']);

				// $map['IR_AddTime'] = time();
				// $map['IR_Path'] = '/Uploads'.$val['savepath'];
				// $map['IR_Img'] = $val['savename'];
				
				// $id = $this->add($map);
				// $image_load[] = '/Uploads'.$val['savepath'].$val['savename']; 
				$image_load = '/Uploads'.$val['savepath'].$val['savename']; 
			}
			return $image_load;
		}
	}
    public function getImages(){
        $type = I('uploadType');
        $year = (int)I('year');
        $month = (int)I('month');
        
        $imgs_model = D('ImagesRec');
        $info = $imgs_model->getImages($type,$year,$month);
        if($info){
            $this->ajaxReturn($info);
        }else{
            $this->ajaxReturn(0);
        }
    }

    public function getImages2(){
		$type = I('uploadType');
        $year = (int)I('year');
        $month = (int)I('month');
        $page = (int)I('page');
        $num = 36;
        // $type = 'goods';
        // $year = "2017";
        // $month = "";

        if($type == 'goods'){
			$map['IR_Type'] = 1;
			$dir = "/Uploads/goods_imgs";
		}elseif($type == 'category' || $type == 'category_banner'|| $type == 'category_PCtop'|| $type == 'category_PCleft'){
			$map['IR_Type'] = 2;
			$dir = "/Uploads/category_imgs";
		}elseif($type == 'brand'){
			$map['IR_Type'] = 3;
			$dir = "/Uploads/brand_imgs";
		}elseif($type == 'banner'){
			$map['IR_Type'] = 4;
			$dir = "/Uploads/banner_imgs";
		}elseif($type == 'navigation'){
			$map['IR_Type'] = 5;
			$dir = "/Uploads/navigation_imgs";
		}
		if($year && !$month){
			$str1=strtotime($year."-01");
			$str2=(int)strtotime(((int)$year+1)."-01")-1;
			// $where['IR_AddTime'] = array('between',array($str1,$str2));
		}elseif($year && $month){
			$str1=strtotime($year."-".$month);
			if((int)$month == 12){
				$str2=(int)strtotime(((int)$year+1)."-01")-1;
			}else{
				$str2=(int)strtotime($year."-".((int)$month+1))-1;
			}
			$where['IR_AddTime'] = array('between',array($str1,$str2));
		}
		if(IS_WIN){
    		$systemType='C:/wamp64/www/yuki';
    	}else{
    		$systemType='.';
    	}
    	$data=array();
		$data = $this->getfiles($systemType,$dir,$data,$str1,$str2);
		$res['page']['nowpage'] = $page;
		$res['page']['pagecount'] = ceil(count($data)/$num);
		$res['data'] = array_slice($data,$num*$page,$num);
		// dump($data);
		if($res){
            $this->ajaxReturn($res);
        }else{
            $this->ajaxReturn(0);
        }

    }
    public function getfiles($systemType,$path,&$data=array(),$time1=0,$time2=0){
    	if(is_dir($systemType.$path)){
    		$sort = 1;
    	}else{
    		$sort = 0;
    	}
		foreach(scandir($systemType.$path,$sort) as $afile){
			// echo $systemType.$path.'/'.$afile."<br/>";
			if($afile=='.'||$afile=='..') continue; 
			if(is_dir($systemType.$path.'/'.$afile)){
				if($time1 && $time2 && $time1<=strtotime($afile) && $time2>=strtotime($afile)){
					$res1 = $this->getfiles($systemType,$path.'/'.$afile,$data,$time1,$time2); 
				}if(!$time1 && !$time2){
					$res1 = $this->getfiles($systemType,$path.'/'.$afile,$data,$time1,$time2); 
				}else{
					continue;
				}
			}else{
				if(substr($afile, 0,2)!='s_'){
					$res2['path'] = $path.'/';
					$res2['img'] = $afile;
				}
					if($res2!=null){
						$data[] = $res2;
						unset($res2);
					}
			}
			
		} 

		return $data;
	}



    public function removeImages(){
        $path = I('path');
        // if((int)$id>0){
        //     $ir = M('Images_rec');
        //     $ir_info = $ir->where(array('IR_ID'=>$id))->find();
        //     if(IS_WIN){
        //         $path = "C:/wamp64/www/yuki".$ir_info['IR_Path'].$ir_info['IR_Img'];
        //         $path1 = "C:/wamp64/www/yuki".$ir_info['IR_Path'].'s_'.$ir_info['IR_Img'];
        //     }else{
        //         $path = ".".$ir_info['IR_Path'].$ir_info['IR_Img'];
        //         $path1 = ".".$ir_info['IR_Path'].'s_'.$ir_info['IR_Img'];
        //     }
        //     $success = unlink($path);
        //     $success1 = unlink($path1);
        //     if($success&&$success1){
        //         $info = $ir->where(array('IR_ID'=>$id))->delete();
        //     }
        //     if($info){
        //         $this->ajaxReturn(1);
        //     }else{
        //         $this->ajaxReturn(-1);
        //     }
        // }else{
        //     $this->ajaxReturn(0);
        // }
        if($path){
        	$arr = explode("/", $path);
        	if($arr){
        		$arr[count($arr)-1] = 's_'.$arr[count($arr)-1];
	        	$path1 = implode("/", $arr);
        	}
	        	
            if(IS_WIN){
                $img = "C:/wamp64/www/yuki".$path;
                if($path1){
                	$img1 = "C:/wamp64/www/yuki".$path1;
                }
            }else{
                $img = ".".$path;
                if($path1){
	                $img1 = ".".$path1;
	            }
            }
            $success = unlink($img);
            $success1 = unlink($img1);
            if($success&&$success1){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(-1);
            }
        }else{
            $this->ajaxReturn(0);
        }
    }
    function get_image_byurl() {
    	$url = trim(I('url'));
        $type = I('uploadType');
        if ($url == "") { 
        	$this->ajaxReturn(0); 
        }

        	// $this->ajaxReturn($url." ".$type); 
        if($type == 'goods'){
			$map['IR_Type'] = 1;
			$dir = "/goods_imgs/";
		}elseif($type == 'category'){
			$map['IR_Type'] = 2;
			$dir = "/category_imgs/";
		}elseif($type == 'brand'){
			$map['IR_Type'] = 3;
			$dir = "/brand_imgs/";
		}elseif($type == 'banner'){
			$map['IR_Type'] = 4;
			$dir = "/banner_imgs/";
		}elseif($type == 'navigation'){
			$map['IR_Type'] = 5;
			$dir = "/navigation_imgs/";
		}else{
        	$this->ajaxReturn(0); 
		}
		$upload_date = date('Y-d-m',time());
        $ext = strrchr($url, ".");  //得到图片的扩展名

        if($ext != ".gif" && $ext != ".jpg" && $ext != ".bmp") { $ext = ".jpg"; }
		$filename="";
        if($filename == "") { $filename = time().rand(00000,99999). $ext; }  //以时间另起名，在此可指定相对目录 ，未指定则表示同php脚本执行的当前目录

        //以流的形式保存图片

        // $write_fd = @fopen('/Uploads'.$dir.$upload_date."/".$filename,"a");
        // $this->write_log($write_fd.'wwwww');

     //    @fwrite($write_fd, $this->Curl_Get($url));  //将采集来的远程数据写入本地文件

     //    @fclose($write_fd);

        
    	// $this->ajaxReturn('/Uploads'.$dir.$upload_date."/".$filename);  //返回文件名

    }

    //远程获取
    function Curl_Get($url){

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_HEADER, false);

        //curl_setopt($curl, CURLOPT_REFERER,$url);

        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; SeaPort/1.2; Windows NT 5.1; SV1; InfoPath.2)");  //模拟浏览器访问

        curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookie.txt');

        curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookie.txt');

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);

        $values = curl_exec($curl);

        curl_close($curl);

        return($values);

    }

    public function asd(){
		$imgname = "http://f.hiphotos.baidu.com/image/pic/item/060828381f30e92465929cfb45086e061c95f7c2.jpg"; 
		$res = $this->getImage($imgname,'./Uploads','111.jpg',1);
		dump($res);
	}
	function getImage($url,$save_dir='',$filename='',$type=0){
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