<?php	
	namespace Admin\Model;
	use \Think\Model;

	class ImagesRecModel extends Model{
		/*
		* $dir 上传文件夹
		* $files 上传图片
		*/
		public function upload($files,$type){
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
					$ids .=$id.','; 
				}
				$ids = trim($ids,',');
			return $ids;
			}
		}

		public function getImages($type,$year = 0,$month = 0,$page = 0){
			if($type == 'goods'){
				$where['IR_Type'] = 1;
			}elseif($type == 'category'){
				$where['IR_Type'] = 2;
			}elseif($type == 'brand'){
				$where['IR_Type'] = 3;
			}elseif($type == 'banner'){
				$where['IR_Type'] = 4;
			}elseif($type == 'navigation'){
				$where['IR_Type'] = 5;
			}
			if($year && !$month){
				$str1=strtotime($year."-01");
				$str2=(int)strtotime(((int)$year+1)."-01")-1;
				$where['IR_AddTime'] = array('between',array($str1,$str2));
			}elseif($year && $month){
				$str1=strtotime($year."-".$month);
				if((int)$month == 12){
					$str2=(int)strtotime(((int)$year+1)."-01")-1;
				}else{
					$str2=(int)strtotime($year."-".((int)$month+1))-1;
				}
				$where['IR_AddTime'] = array('between',array($str1,$str2));
			}
			$info = $this->where($where)->order('IR_AddTime DESC')->select();
			return $info;
		}


		

	}