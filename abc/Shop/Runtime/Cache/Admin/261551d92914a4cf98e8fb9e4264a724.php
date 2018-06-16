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
        <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace.min.css" />
        <link rel="stylesheet" href="/abc/Public/Admin/font/css/font-awesome.min.css" />
        <!--[if lte IE 8]>
		  <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace-ie.min.css" />
		<![endif]-->
		<script src="/abc/Public/Admin/js/jquery-1.9.1.min.js"></script>
		<script src="/abc/Public/Admin/assets/js/typeahead-bs2.min.js"></script>   
        <script src="/abc/Public/Admin/js/lrtk.js" type="text/javascript" ></script>		
		<script src="/abc/Public/Admin/assets/js/jquery.dataTables.min.js"></script>
		<script src="/abc/Public/Admin/assets/js/jquery.dataTables.bootstrap.js"></script>
        <script src="/abc/Public/Admin/assets/layer/layer.js" type="text/javascript" ></script>          
        <script type="text/javascript" src="/abc/Public/Admin/Widget/swfupload/swfupload.js"></script>
        <script type="text/javascript" src="/abc/Public/Admin/Widget/swfupload/swfupload.queue.js"></script>
        <script type="text/javascript" src="/abc/Public/Admin/Widget/swfupload/swfupload.speed.js"></script>
        <script type="text/javascript" src="/abc/Public/Admin/Widget/swfupload/handlers.js"></script>
<title>广告管理</title>
	<style type="text/css">
    .num{
        padding:4px;
        font-size: 18px;
        border:1px solid #ccc;
        color:#000;
    }
    .current{
        padding:4px;
        font-size: 18px;
        border:1px solid #ccc;
        color:#000;      
    }
</style>
</head>

<body>
<div class=" clearfix" id="advertising">
       <div id="scrollsidebar" class="left_Treeview">
    <div class="show_btn" id="rightArrow"><span></span></div>
    <div class="widget-box side_content" >
    <div class="side_title"><a title="隐藏" class="close_btn"><span></span></a></div>
     <div class="side_list">
      <div class="widget-header header-color-green2">
          <h4 class="lighter smaller">等级管理</h4>
      </div>
      <div class="widget-body">
         <ul class="b_P_Sort_list">
             <li><i class="orange  fa fa-user-secret"></i><a href="<?php echo U('User/grade');?>" name="">全部(<?php echo ($total['0']); ?>)</a></li>
             <li><i class="fa fa-image pink "></i> <a href="<?php echo U('User/grade', ['id'=>1]);?>">普通会员(<?php echo ($total['1']); ?>)</a></li>
             <li> <i class="fa fa-image pink "></i> <a href="<?php echo U('User/grade', ['id'=>2]);?>">VIP会员(<?php echo ($total['2']); ?>)</a> </li>
             <li> <i class="fa fa-image pink "></i> <a href="<?php echo U('User/grade', ['id'=>3]);?>">钻石会员(<?php echo ($total['3']); ?>)</a></li>
             <li> <i class="fa fa-image pink "></i> <a href="<?php echo U('User/grade', ['id'=>4]);?>">拥有优惠劵会员(<?php echo ($total['4']); ?>)</a></li>
             <li> <i class="fa fa-image pink "></i> <a href="<?php echo U('User/grade', ['id'=>5]);?>" class="str">无优惠劵会员(<?php echo ($total['5']); ?>)</a></li>
             <!-- <li><i class="fa fa-image pink "></i> <a href="#">单个广告(6)</a></li> -->
         </ul>
  </div>
  </div>
  </div>  
  </div><div class="Ads_list">
   <div class="border clearfix">
       <span class="l_f" style="<?php echo ($zs); ?>">
        <a href="javascript:ovid()" id="member_add" class="btn btn-warning" onclick="Delivery_stop(this,10001,2)"><i class="icon-plus"></i>优惠券</a>
        <!-- <a href="javascript:ovid()" class="btn btn-danger"><i class="icon-trash"></i>批量删除</a> -->
       </span>
       <span class="r_f">共：<b>2345</b>条</span>
     </div>
     <!---->
     <!-- <div class="table_menu_list"> -->
       <table class="table table-striped table-bordered table-hover" id="sample-table">
		<thead>
		 <tr>
				<th width="25"><label><input type="checkbox" class="ace"><span class="lbl"></span></label></th>
				<th width="50" height="20">ID</th>
				<th width="80">用户名</th>
				<th width="80">性别</th>
				<th width="120">手机</th>
				<th width="120">邮箱</th>
				<th width="80">优惠券</th>
				<th width="80">积分</th>
				<th width="120">加入时间</th>
                <th width="80">等级</th>
				<th width="60">状态</th>   
				
			</tr>
		</thead>
	<tbody>
  <?php if(is_array($arr)): foreach($arr as $key=>$v): ?><tr>
          <td><label><input type="checkbox"  class="ace" value="<?php echo ($v['id']); ?>"><span class="lbl"></span></label></td>
          <td class="sad"><?php echo ($v['id']); ?></td>
          <td><u style="cursor:pointer;text-decoration: none;" class="text-primary" onclick="member_show('张三','member-show.html','10001','500','400')"><?php echo ($v['username']); ?></u></td>
          <td><?php echo ($v['sex']); ?></td>
          <td><?php echo ($v['phone']); ?></td>
          <td><?php echo ($v['email']); ?></td>
          <td><?php echo ($v['coupon']); ?></td>
          <td class="text-l"><?php echo ($v['integral']); ?></td>
          <td><?php echo ($v['addtime']); ?></td>
          <td><?php echo ($v['grade']); ?></td>
          <td>正常</td>
		</tr><?php endforeach; endif; ?>
      
         <!--  <?php if($v['status'] == '禁用'): ?><td class="td-status"><span class="label label-defaunt radius"><?php echo ($v['status']); ?></span></span></td>
            <td class="td-manage">
          <a onclick="member_start(this,'10001', <?php echo ($v['id']); ?>)" name="" title="启用"  class="btn btn-xs btn-success"><i class="icon-ok bigger-120"></i></a>
          <?php else: ?>
          <td class="td-status"><span class="label label-success radius"><?php echo ($v['status']); ?></span></td></tr> -->
          <!-- <td class="td-manage">
          <a onclick="member_stop(this,'10001', <?php echo ($v['id']); ?>)" name="" title="停用"  class="btn btn-xs btn-success"><i class="icon-ok bigger-120"></i></a><?php endif; ?>


          <a title="编辑" href="<?php echo U('User/edit', ['id'=>$v['id']]);?>" name="" class="btn btn-xs btn-info" ><i class="icon-edit bigger-120"></i></a> --> 
<!--           <a title="删除" href="javascript:;"  onclick="member_del(this,'1')" class="btn btn-xs btn-warning" ><i class="icon-trash  bigger-120"></i></a> -->
          <!-- </td> -->
		
    </tbody>
    </table></div>
 <div class="page" style="margin-top:360px;position:absolute;margin-left:280px;">
  <?php echo ($brr); ?></div>
     </div>
 </div>
   <!--  
</div>

<!--添加广告样式-->
<!-- <div id="add_ads_style"  style="display:none">
 <div class="add_adverts">
 <ul>
  <li>
  <label class="label_name">所属分类</label>
  <span class="cont_style">
  <select class="form-control" id="form-field-select-1">
    <option value="">选择分类</option>
    <option value="AL">首页大幻灯片</option>
    <option value="AK">首页小幻灯片</option>
    <option value="AZ">单广告图</option>
    <option value="AR">其他广告</option>
    <option value="CA">板块栏目广告</option>
  </select></span>
  </li>
  <li><label class="label_name">图片尺寸</label><span class="cont_style">
  <input name="长" type="text" id="form-field-1" placeholder="0" class="col-xs-10 col-sm-5" style="width:80px">
  <span class="l_f" style="margin-left:10px;">x</span><input name="宽" type="text" id="form-field-1" placeholder="0" class="col-xs-10 col-sm-5" style="width:80px"></span></li>
  <li><label class="label_name">显示排序</label><span class="cont_style"><input name="排序" type="text" id="form-field-1" placeholder="0" class="col-xs-10 col-sm-5" style="width:50px"></span></li>
  <li><label class="label_name">链接地址</label><span class="cont_style"><input name="地址" type="text" id="form-field-1" placeholder="地址" class="col-xs-10 col-sm-5" style="width:450px"></span></li>
   <li><label class="label_name">状&nbsp;&nbsp;态：</label>
   <span class="cont_style">
     &nbsp;&nbsp;<label><input name="form-field-radio1" type="radio" checked="checked" class="ace"><span class="lbl">显示</span></label>&nbsp;&nbsp;&nbsp;
     <label><input name="form-field-radio1" type="radio" class="ace"><span class="lbl">隐藏</span></label></span><div class="prompt r_f"></div>
     </li>
     <li><label class="label_name">图片</label><span class="cont_style">
 <div class="demo">
	           <div class="logobox"><div class="resizebox"><img src="images/image.png" width="100px" alt="" height="100px"/></div></div>	
               <div class="logoupload">
                  <div class="btnbox"><a id="uploadBtnHolder" class="uploadbtn" href="javascript:;">上传替换</a></div>
                  <div style="clear:both;height:0;overflow:hidden;"></div>
                  <div class="progress-box" style="display:none;">
                    <div class="progress-num">上传进度：<b>0%</b></div>
                    <div class="progress-bar"><div style="width:0%;" class="bar-line"></div></div>
                  </div>  <div class="prompt"><p>图片大小小于5MB,支持.jpg;.gif;.png;.jpeg格式的图片</p></div>  
              </div>                                
           </div>           
   </span>
  </li>
 
  
 </ul>
 </div>
</div> -->
<div id="Delivery_stop" style=" display:none">
  <div class="">
    <div class="content_style">
  <div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1">优惠 </label>
       <div class="col-sm-9"><select class="form-control" id="form-field-select-1" name="coupon">
			<option value="">--请选择--</option>
			<option value="10">满88-10</option>
			<option value="20">满88-20</option>
			<option value="30">满88-30</option>
		</div>
	</div>
   <!-- <div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 快递单号 </label>
    <div class="col-sm-9"><input type="text" id="form-field-1" placeholder="快递单号" class="col-xs-10 col-sm-5" style="margin-left:0px;"></div>
	</div> -->
</body>
</html>
<script>

//初始化宽度、高度  
 $(".widget-box").height($(window).height()); 
 $(".Ads_list").width($(window).width()-220);
  //当文档窗口发生改变时 触发  
    $(window).resize(function(){
	$(".widget-box").height($(window).height());
	 $(".Ads_list").width($(window).width()-220);
	});
	$(function() { 
	$("#advertising").fix({
		float : 'left',
		//minStatue : true,
		skin : 'green',	
		durationTime :false,
		stylewidth:'220',
		spacingw:30,//设置隐藏时的距离
	    spacingh:250,//设置显示时间距
		set_scrollsidebar:'.Ads_style',
		table_menu:'.Ads_list'
	});
});
/*广告图片-停用*/
function member_stop(obj,id){
	layer.confirm('确认要关闭吗？',{icon:0,},function(index){
		$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs " onClick="member_start(this,id)" href="javascript:;" title="显示"><i class="fa fa-close bigger-120"></i></a>');
		$(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已关闭</span>');
		$(obj).remove();
		layer.msg('关闭!',{icon: 5,time:1000});
	});
}
/*广告图片-启用*/
function member_start(obj,id){
	layer.confirm('确认要显示吗？',{icon:0,},function(index){
		$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs btn-success" onClick="member_stop(this,id)" href="javascript:;" title="关闭"><i class="fa fa-check  bigger-120"></i></a>');
		$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">显示</span>');
		$(obj).remove();
		layer.msg('显示!',{icon: 6,time:1000});
	});
}
/*广告图片-删除*/
function member_del(obj,id){
	layer.confirm('确认要删除吗？',{icon:0,},function(index){
		$(obj).parents("tr").remove();
		layer.msg('已删除!',{icon:1,time:1000});
	});
}
/*******添加广告*********/
//  $('#ads_add').on('click', function(){
// 	  layer.open({
//         type: 1,
//         title: '添加广告',
// 		maxmin: true, 
// 		shadeClose: false, //点击遮罩关闭层
//         area : ['800px' , ''],
//         content:$('#add_ads_style'),
// 		btn:['提交','取消'],
// 		yes:function(index,layero){	
// 		 var num=0;
// 		 var str="";
//      $(".add_adverts input[type$='text']").each(function(n){
//           if($(this).val()=="")
//           {
               
// 			   layer.alert(str+=""+$(this).attr("name")+"不能为空！\r\n",{
//                 title: '提示框',				
// 				icon:0,								
//           }); 
// 		    num++;
//             return false;            
//           } 
// 		 });
// 		  if(num>0){  return false;}	 	
//           else{
// 			  layer.alert('添加成功！',{
//                title: '提示框',				
// 			icon:1,		
// 			  });
// 			   layer.close(index);	
// 		  }		  		     				
// 		}
//     });
// })

//发送优惠券
function Delivery_stop(obj,id,a){

	// $("input:checked").parent().parent().next().html();
	// arr = $("input:checked");
	// console.log(arr);
	// obj = document.getElementsByName("test");
	obj = document.getElementsByClassName("ace");
	check_val = [];
	for(k in obj){
        if(obj[k].checked)
            check_val.push(obj[k].value);
    }
    // console.log(check_val);
    if 	(check_val == '') {
    	// console.log(123);
    	return;
	}
	
	// alert(123);
	 // console.log(obj);
	// for (i=0;i<input:checked.length;i++) 
	// brr = $(arr[0]).val();
	// console.log(brr);
	// for (k in arr) {
	// 	checked.push($(arr[k]).val());
	// }
	// console.log(check_val);
	// console.log($(".asd").text());
	layer.open({
        type: 1,
        title: '福利',
		maxmin: true, 
		shadeClose:false,
        area : ['500px' , ''],
        content:$('#Delivery_stop'),
		btn:['确定','取消'],
		yes: function(index, layero){		
		if($('#form-field-select-1').val()==""){
			layer.alert('你没有选择优惠折扣！',{
               title: '提示框',				
			  icon:0,		
			  }) 
		
			}else{			
			 // layer.confirm('提交成功！',function(index){
        var val = $("#form-field-select-1").val();
        // var arr = $(".col-xs-10").val();
        // console.log(arr);
        $.ajax({
          type:'get',
          url : "<?php echo U('User/check_val');?>",
          data : {id: check_val, coupon:+val},
          success: function(res) {
          	// console.log(reses);
      //       if (res == '2') {
      //         alert('订单号填写错误,请重新填写！');
      //       } else {
      //         if (res == '1') {
                alert('发送成功！');
                
      //         } else {
      //         $(obj).parent().prev().html(res);    
    		// $(obj).parents("tr").find(".td-manage").prepend('<a style=" display:none" class="btn btn-xs btn-success" onClick="member_stop(this,id)" href="javascript:;" title="已发货"><i class="fa fa-cubes bigger-120"></i></a>');
    		// $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已发货</span>');
    		// $(obj).remove();
    		// layer.msg('已发货!',{icon: 6,time:2000});
    		location.reload(true); 
//     		$('.str').on('click',function() {
// 	$('.l_f').css('display', 'block');	
// })
      //         }
      //       }
      },
    })
			layer.close(index);    		  
		  }
		
		}
	})
};
</script>
<script type="text/javascript">
function updateProgress(file) {
	$('.progress-box .progress-bar > div').css('width', parseInt(file.percentUploaded) + '%');
	$('.progress-box .progress-num > b').html(SWFUpload.speed.formatPercent(file.percentUploaded));
	if(parseInt(file.percentUploaded) == 100) {
		// 如果上传完成了
		$('.progress-box').hide();
	}
}

function initProgress() {
	$('.progress-box').show();
	$('.progress-box .progress-bar > div').css('width', '0%');
	$('.progress-box .progress-num > b').html('0%');
}

function successAction(fileInfo) {
	var up_path = fileInfo.path;
	var up_width = fileInfo.width;
	var up_height = fileInfo.height;
	var _up_width,_up_height;
	if(up_width > 120) {
		_up_width = 120;
		_up_height = _up_width*up_height/up_width;
	}
	$(".logobox .resizebox").css({width: _up_width, height: _up_height});
	$(".logobox .resizebox > img").attr('src', up_path);
	$(".logobox .resizebox > img").attr('width', _up_width);
	$(".logobox .resizebox > img").attr('height', _up_height);
}

var swfImageUpload;
$(document).ready(function() {
	var settings = {
		flash_url : "Widget/swfupload/swfupload.swf",
		flash9_url : "Widget/swfupload/swfupload_fp9.swf",
		upload_url: "upload.php",// 接受上传的地址
		file_size_limit : "5MB",// 文件大小限制
		file_types : "*.jpg;*.gif;*.png;*.jpeg;",// 限制文件类型
		file_types_description : "图片",// 说明，自己定义
		file_upload_limit : 100,
		file_queue_limit : 0,
		custom_settings : {},
		debug: false,
		// Button settings
		button_image_url: "Widget/swfupload/upload-btn.png",
		button_width: "95",
		button_height: "30 ",
		button_placeholder_id: 'uploadBtnHolder',
		button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
		button_cursor : SWFUpload.CURSOR.HAND,
		button_action: SWFUpload.BUTTON_ACTION.SELECT_FILE,
		
		moving_average_history_size: 40,
		
		// The event handler functions are defined in handlers.js
		swfupload_preload_handler : preLoad,
		swfupload_load_failed_handler : loadFailed,
		file_queued_handler : fileQueued,
		file_dialog_complete_handler: fileDialogComplete,
		upload_start_handler : function (file) {
			initProgress();
			updateProgress(file);
		},
		upload_progress_handler : function(file, bytesComplete, bytesTotal) {
			updateProgress(file);
		},
		upload_success_handler : function(file, data, response) {
			// 上传成功后处理函数
			var fileInfo = eval("(" + data + ")");
			successAction(fileInfo);
		},
		upload_error_handler : function(file, errorCode, message) {
			alert('上传发生了错误！');
		},
		file_queue_error_handler : function(file, errorCode, message) {
			if(errorCode == -110) {
				alert('您选择的文件太大了。');	
			}
		}
	};
	swfImageUpload = new SWFUpload(settings);
});
</script>
<script>
jQuery(function($) {
				var oTable1 = $('#sample-table').dataTable( {
				"aaSorting": [[ 1, "desc" ]],//默认第几个排序
		"bStateSave": true,//状态保存
		"aoColumnDefs": [
		  //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
		  {"orderable":false,"aTargets":[0,2,3,4,5,7,8,]}// 制定列不参与排序
		] } );
				
				
				$('table th input:checkbox').on('click' , function(){
					var that = this;
					$(this).closest('table').find('tr > td:first-child input:checkbox')
					.each(function(){
						this.checked = that.checked;
						$(this).closest('tr').toggleClass('selected');
					});
						
				});
			
			
				$('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});
				function tooltip_placement(context, source) {
					var $source = $(source);
					var $parent = $source.closest('table')
					var off1 = $parent.offset();
					var w1 = $parent.width();
			
					var off2 = $source.offset();
					var w2 = $source.width();
			
					if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
					return 'left';
				}
			})


// $(function(){
//   $('.row').last().remove();
//   alter(123);
// })();
window.onload = function () {
	$('.row').last().remove();
}
window.alert=function(){};
</script>