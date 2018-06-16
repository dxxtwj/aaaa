<?php
namespace Admin\Controller;
use Think\Controller;

class BannerController extends CommonController {

	// 列表
	public function index(){
		$banner = M('banner_rec');
		$res = $banner->order('BR_Sort desc')->select();
        $count = $banner->count();
		foreach($res as $key=>$val)
		{
			$res[$key]['BR_AddTime'] = date('Y-m-d H:i:s',$val['BR_AddTime']);
		}
		
		$this->assign('res', $res);
        $this->assign('count', $count);
        // dump($res);
		$this->display();
	}

    	// 添加
	public function add(){
		$bannerModel = D('bannerRec');
		if(IS_POST){
			$name = I('name');
			$url = I('url');
			$sort = I('sort');
			$show = I('show');
			$banner = M('BannerRec');
			// 处理数据
			$map['BR_Name'] = $name;
			$map['BR_Url'] = $url;
			$map['BR_Sort'] = $sort;
			$map['BR_Show'] = $show;
			$map['BR_AddTime'] = time();
			// $map['BR_Updatetime'] = time();
			$info = $bannerModel->upload('/uploads_banner_img/',$_FILES['pic']);
			// dump($info);exit;
           	if(!$info){
           		$this->error('添加轮播图失败',U('Banner/add'));
            }
           	foreach($info as $val){
        		$map['BR_Path'] = $val['savepath'];
    			$map['BR_Img'] = $val['savename'];
           	}
			$res = $banner->add($map);
			if($res)
			{
				$this->success('添加轮播图成功',U('Banner/index'));
			}else
			{
				$this->error('添加轮播图失败',U('Banner/add'));
			}
    	}else{
	      $this->display();
    	}
	}

    	// 修改
    	public function edit()
    	{
	    	$banner = M('BannerRec');
	    	if(IS_POST){
            	$id = I('banner_id');
            	$name = I('name');
            	$url = I('url');
            	$sort = I('sort');
            	$show = I('show');
            	$map['BR_ID'] = $id;
            	// 查出自己的数据
          		$res = $banner->where($map)->find();
          		// 定义接受数据
            	$data['BR_Name'] = $name;
            	$data['BR_Url'] = $url;
            	$data['BR_Sort'] = $sort;
            	$data['BR_Show'] = $show;
            	// $data['BR_Updatetime'] = time();
            	if(!empty($_FILES['pic']['name'][0])){	
		        	$bannerModel = D('bannerRec');
                	$info = $bannerModel->upload('/uploads_banner_img/',$_FILES['pic']);
                	if(!$info){
                   		$this->error('修改失败', U('Banner/edit', array('banner_id'=>$id)));
                    }

                	foreach($info as $val){
                		$data['BR_Path'] = $val['savepath'];
                		$data['BR_Img'] = $val['savename'];
                	}
                	if(IS_WIN){
						$type='C:/wamp64/www/youka/Uploads';
					}else{
						// $path='/alidata/www/woai';
						$type='./Uploads';
					}
                	@unlink($type.$res['BR_Path'].$res['BR_Img']);
                	@unlink($type.$res['BR_Path'].'s_'.$res['BR_Img']);
            	}

                $result = $banner->where($map)->save($data);

	           	if($result !== false){
	           		$this->success('修改成功', U('Banner/index'));
	           	}else{
	           		$this->error('修改失败', U('Banner/edit', array('banner_id'=>$id)));
	           	}
	        }else{

		  		$id = I('banner_id');
	          	$map['BR_ID'] = $id;
	          	$info = $banner->where($map)->find();
	          	if(IS_WIN){
					$info['IS_WIN']=1;
				}else{
					$info['IS_WIN']=0;
				}
	          	
	          	$this->assign('info',$info);
	          	$this->display();
        	}
    	}

   	// 删除
	public function delete(){
    	// $bannerModel->startTrans();
    	$banner = D('BannerRec');
    	$id = I('banner_id');
    	// 判断轮播图ID是否为空
		if($id == ''){

			$this->ajaxReturn('-2');

		}else{
			// 查自己的数据
			$where['BR_ID'] = $id;
        	$res = $banner->where($where)->find();
        	if($res){
	        	// 删除图片
	        	if(IS_WIN){
					$type='F:/wamp64/www/youka/Uploads';
				}else{
					// $path='/alidata/www/woai';
					$type='./Uploads';
				}
	        	@unlink($type.$res['BR_Path'].$res['BR_Img']);
	          	@unlink($type.$res['BR_Path'].'s_'.$res['BR_Img']);
	        	$result = $banner->where($where)->delete();
	          	// 删除类别
				if($result){
					// $bannerModel->commit(); 
					$this->ajaxReturn('1');
				}else{
					// $bannerModel->rollback();
					$this->ajaxReturn('-1');
				}
        	}else{
        		$this->ajaxReturn('-2');
        	}
		}
	}

    /**
     * authod:梁聪
     * time  ：6/1
     *
     * ajax修改轮播图排序；
     */
	public function ajaxChangeSort()
    {
        if(!IS_POST) $this->ajaxReturn(-1);
        $banner = D('BannerRec');
        $this->ajaxReturn($banner->modelChangeSort());
    }
    	
}