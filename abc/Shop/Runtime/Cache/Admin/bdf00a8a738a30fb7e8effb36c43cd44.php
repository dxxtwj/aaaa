<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
	<meta http-equiv="Cache-Control" content="no-siteapp" />
	<link href="/abc/Public/Admin/assets/css/bootstrap.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="/abc/Public/Admin/css/style.css"/>       
	<link href="/abc/Public/Admin/assets/css/codemirror.css" rel="stylesheet">
	<link rel="stylesheet" href="/abc/Public/Admin/assets/css/colorbox.css"> 
	 <!--图片相册-->   
	<link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace.min.css" />

	<link rel="stylesheet" href="/abc/Public/Admin/font/css/font-awesome.min.css" />        
	<!--[if lte IE 8]>
	  <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace-ie.min.css" />
	<![endif]-->

	<script src="/abc/Public/Admin/js/jquery-1.9.1.min.js"></script>  
	<script src="/abc/Public/Admin/assets/js/jquery.colorbox-min.js"></script>
	<script src="/abc/Public/Admin/assets/js/typeahead-bs2.min.js"></script>        	
	<script src="/abc/Public/Admin/assets/js/jquery.dataTables.min.js"></script>
	<script src="/abc/Public/Admin/assets/js/jquery.dataTables.bootstrap.js"></script>
	<script src="/abc/Public/Admin/assets/layer/layer.js" type="text/javascript" ></script>  
	<script type="text/javascript" src="/abc/Public/Admin/Widget/swfupload/swfupload.js"></script>
	<script type="text/javascript" src="/abc/Public/Admin/Widget/swfupload/swfupload.queue.js"></script>
	<script type="text/javascript" src="/abc/Public/Admin/Widget/swfupload/swfupload.speed.js"></script>
	<script type="text/javascript" src="/abc/Public/Admin/Widget/swfupload/handlers.js"></script>        
	<title>列表</title>
</head>

<body>
	<div class="page-content clearfix">
		<div class="sort_adsadd_style">
	  	    <div class="border clearfix">
	        <span class="l_f">
		        <a href="javascript:void(0)"  id="ads_add" title="添加广告" class="btn btn-warning Order_form"><i class="fa fa-plus"></i> 添加广告</a>
		        <button class="btn btn-danger" id="checkdel" href="javascript:;"><i class="fa fa-trash"></i> 批量删除</button>
		        <a href="javascript:void(0)" onClick="javascript :history.back(-1);" class="btn btn-info"><i class="fa fa-reply"></i> 返回上一步</a>
	       	</span>
	       	<span class="r_f">共：<b><?php echo ($adCount); ?></b>条广告</span>
	        </div> 	
	 		<!--列表样式-->
	        <div class="sort_Ads_list">
		       	<table class="table table-striped table-bordered table-hover" id="sample-table">
					<thead>
					 	<tr>
							<th width="25"><label><input type="checkbox" class="all"><span class="lbl"></span></label></th>
							<th width="80px">ID</th>               
							<th width="100">分类</th>
			                <!-- <th width="80px">排序</th> -->
							<th width="240px">图片</th>
							<!-- <th width="150px">尺寸（大小）</th> -->
							<!-- <th width="250px">链接地址</th> -->
							<th width="180">加入时间</th>
							<th width="70">状态</th>                
							<th width="250">操作</th>
						</tr>
					</thead>
					<tbody id="sample-tbody">
						<?php if(empty($list)): ?><tr><td colspan="10"><h3>暂无数据~~~</h3></td></tr>
						<?php else: ?>
						<?php if(is_array($list)): foreach($list as $k=>$v): ?><tr>
						       	<td><label><input name="id[]" type="checkbox" value="<?php echo ($v["id"]); ?>"><span class="lbl"></span></label></td>
						       	<td><?php echo ($v["id"]); ?></td>
				       			<td><?php echo ($res[$v['aid']]); ?></td>
						        <!-- <td><input name="" type="text" style=" width:50px" placeholder="<?php echo ($v["order"]); ?>"/></td> -->
						       	<td><span class="ad_img"><a href="/abc/Public/<?php echo ($v["pic"]); ?>" data-rel="colorbox" data-title="广告图">
						       	<img src="/abc/Public/<?php echo ($v["pic"]); ?>"  width="100%" height="100%"/></a></span></td>      
						       	<!-- <td><?php echo ($v["length"]); ?>x<?php echo ($v["width"]); ?></td> -->
						       	<!-- <td><a href="#" target="_blank"><?php echo ($v["url"]); ?></a></td> -->
						       	<td><?php echo ($v["addtime"]); ?></td>
						      	<?php if($v['status']=='隐藏'): ?><td class="td-status"><span class="label label-success radius"><?php echo ($v["status"]); ?></span></td>
						      		<td class="td-manage">
							        <a onClick="member_start(this,'10001','<?php echo ($v["id"]); ?>')"  href="javascript:void(0)" title="显示"  class="btn btn-xs btn-success"><i class="fa fa-check  bigger-120"></i></a>  
						        <?php else: ?>
							        <td class="td-status"><span class="label label-success radius"><?php echo ($v["status"]); ?></span></td>
						      		<td class="td-manage">
							        <a onClick="member_stop(this,'10001','<?php echo ($v["id"]); ?>')"  href="javascript:void(0)" title="隐藏"  class="btn btn-xs btn-success"><i class="fa fa-check  bigger-120"></i></a><?php endif; ?>
						        <!-- <a title="编辑" href="/abc/Admin/Ads/edit/id/<?php echo ($v["id"]); ?>" class="btn btn-xs btn-info" ><i class="fa fa-edit bigger-120"></i></a>       -->
						        <a title="编辑" href="<?php echo U('Ads/edit', 'id='.$v['id']);?>" class="btn btn-xs btn-info" ><i class="fa fa-edit bigger-120"></i></a>      
						        <!-- <a id="ads_edit" onClick="member_edit(this,'10001','<?php echo ($v["id"]); ?>')" title="编辑" href="javascript:void(0)"  class="btn btn-xs btn-info" ><i class="fa fa-edit bigger-120"></i></a>       -->
						        <a title="删除" href="javascript:void(0)"  onClick="member_del(this,'10001','<?php echo ($v["id"]); ?>')" class="btn btn-xs btn-warning" ><i class="fa fa-trash  bigger-120"></i></a>
						       	</td>
						    </tr><?php endforeach; endif; endif; ?>     
				    </tbody>
		    	</table>
		    	<div class="pagination">
					<ul>
						<?php echo ($btn); ?>
					</ul>
				</div>
	        </div>
	 	</div>
	</div>

	<!--添加广告-->
	<form action="<?php echo U('Ads/add');?>" method="post" enctype="multipart/form-data">
		<div id="add_ads_style"  style="display:none">
			<div class="add_adverts">
				<ul>
					<li>
					  <label class="label_name">所属分类</label>
					  <span class="cont_style">
						  <select name="aid" class="form-control" id="form-field-select-1">
						    <option value="">选择分类</option>
						    <?php if(is_array($sort)): foreach($sort as $k=>$val): ?><option name="sort" value="<?php echo ($val["id"]); ?>"><?php echo ($val["sort"]); ?></option><?php endforeach; endif; ?>
						  </select>
					  </span>
					</li>
					<!-- <li>
						<label class="label_name">图片尺寸</label>
						<span class="cont_style">
							<input name="length" type="text" id="form-field-1" placeholder="0" class="col-xs-10 col-sm-5" style="width:80px">
							<span class="l_f" style="margin-left:10px;">x</span>
							<input name="width" type="text" id="form-field-1" placeholder="0" class="col-xs-10 col-sm-5" style="width:80px">
						</span>
					</li> -->
					<!-- <li>
						<label class="label_name">显示排序</label>
						<span class="cont_style">
							<input name="order" type="text" id="form-field-1" placeholder="0" class="col-xs-10 col-sm-5" style="width:50px">
						</span>
					</li> -->
				  	<!-- <li><label class="label_name">链接地址</label><span class="cont_style"><input name="url" type="text" id="form-field-1" placeholder="地址" class="col-xs-10 col-sm-5" style="width:450px"></span></li> -->
				  	<li>
					  	<label class="label_name">状&nbsp;&nbsp;态：</label>
					   	<span class="cont_style">
					     	&nbsp;&nbsp;<label><input name="status" value="1" type="radio" checked="checked" class="ace"><span class="lbl">显示</span></label>&nbsp;&nbsp;&nbsp;
					     	<label><input name="status" value="2" type="radio" class="ace"><span class="lbl">隐藏</span></label>
					    </span>
					    <div class="prompt r_f"></div>
				 	</li>
				    <li>
				     	<label class="label_name">图片:</label>
				     	<span class="cont_style">
				 			<div class="demo">
					           <!-- <div class="logobox"><div class="resizebox"><img src="/abc/Public/Admin/images/image.png" width="100px" alt="" height="100px"/></div></div>	 -->
				               <div class="logoupload">
				                	<div class="btnbox"><a id="uploadBtnHolder" class="uploadbtn" href="javascript:void(0)">
				                	<input type="file" id="pic" name="pic"></a></div>
				                	<div style="clear:both;height:0;overflow:hidden;"></div>
				                	<div class="progress-box" style="display:none;">
				                		<div class="progress-num">上传进度：<b>0%</b></div>
				                		<div class="progress-bar"><div style="width:0%;" class="bar-line"></div></div>
				                  	</div>  
				                  	<div class="prompt"><p>图片大小小于5MB,支持.jpg;.gif;.png;.jpeg格式的图片</p></div>  
				              </div>                                
				           </div>           
				   		</span>
				  	</li>
				  	<center>
				  		<li>
				  			<button class="btn btn-small btn-default">提交</button>
				  			<a href="<?php echo U('Ads/Ads_list');?>" class="btn btn-small btn-danger">取消</a>
				  		</li>
				  	</center>
				</ul>
			</div>
		</div>
	</form>
</body>
</html>

<script>
/*广告图片-隐藏*/
function member_stop(obj,id,a){
	layer.confirm('确认要隐藏吗？',{icon:0,},function(index){
		$.ajax({
			type:'get',
			url : "<?php echo U('Ads/doStatus');?>",
			data : {id:+ a},
			success: function(res) {
				console.log(res);
				if (res != '-1') {
					$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs " onClick="member_start(this,id,'+res+')" href="javascript:void(0)" title="显示"><i class="fa fa-close bigger-120"></i></a>');
					$(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">隐藏</span>');
					$(obj).remove();
					layer.msg('隐藏!',{icon: 5,time:1000});
				} else {
					layer.msg('显示!',{icon: 5,time:1000});
				}
			},
		});
	});
}

/*广告图片-显示*/
function member_start(obj,id,a){
	layer.confirm('确认要显示吗？',{icon:0,},function(index){
		$.ajax({
			type:'get',
			url : "<?php echo U('Ads/doStatus');?>",
			data : {id:+ a},
			success: function(res) {
				console.log(res);
				if (res != '-1') {
					$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs btn-success" onClick="member_stop(this,id,'+res+')" href="javascript:void(0)" title="隐藏"><i class="fa fa-check  bigger-120"></i></a>');
					$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">显示</span>');
					$(obj).remove();
					layer.msg('显示!',{icon: 6,time:1000});
				} else {
					layer.msg('隐藏!',{icon: 5,time:1000});
				}
			},
		});
	});
}

/*广告图片-删除*/
function member_del(obj,id,a){
	layer.confirm('确认要删除吗？',{icon:0,},function(index){
		$.ajax({
			type: 'get',
			url : "<?php echo U('Ads/ajaxDel');?>",
			data : {id:+a},
			success: function(res) {
				console.log(res);
				if (res != '-1') {
					$(obj).parents("tr").remove();
					layer.msg('已删除!',{icon:1,time:1000});
				} else {
					layer.msg('删除失败!',{icon:1,time:1000});
				}
			},
		});
	});
}


 /*******添加广告*********/
 $('#ads_add').on('click', function(){
	  layer.open({
        type: 1,
        title: '添加广告',
		maxmin: true, 
		shadeClose: false, //点击遮罩关闭层
        area : ['800px' , ''],
        content:$('#add_ads_style'),
		// btn:['提交','取消'],
		yes:function(index,layero){	
		 var num=0;
		 var str="";
     $(".add_adverts input[type$='text']").each(function(n){
          if($(this).val()=="")
          {
               
			   layer.alert(str+=""+$(this).attr("name")+"不能为空！\r\n",{
                title: '提示框',				
				icon:0,								
          }); 
		    num++;
            return false;            
          } 
		 });
		  if(num>0){  return false;}	 	
          else{
			  layer.alert('添加成功！',{
               title: '提示框',				
			icon:1,		
			  });
			   layer.close(index);	
		  }		  		     				
		}
    });
})

jQuery(function($) {

	$('table th input:checkbox').on('click' , function(){
		var that = this;
		$(this).closest('table').find('tr > td:first-child input:checkbox')
		.each(function(){
			this.checked = that.checked;
			$(this).closest('tr').toggleClass('selected');
		});
						
	});
});

// 批量删除
$(function(){
	$('.all').click(function(){
		if(this.checked){
			$('.list :checkbox').prop('checked', true);
		} else {
			$('.list :checkbox').prop('checked', false);
		}
	});
	// 点击批量删除按钮
	$('#checkdel').click(function(){
		var ids = '';

		$("input[name='id[]']:checkbox:checked").each(function () {
			// 用,拼接id
			ids += $(this).val() + ',';
		})

		if(confirm("确认要删除吗？")){
			window.location.href = "/abc/Admin/Ads/checkdel?id="+ids;
		}
	})

}) 
</script>