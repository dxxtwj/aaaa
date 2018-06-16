<?php if (!defined('THINK_PATH')) exit();?><!-- 管理员列表 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Cache-Control" content="no-siteapp" />
         <link href="/Public/Admin/assets/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="/Public/Admin/css/style.css"/>       
        <link href="/Public/Admin/assets/css/codemirror.css" rel="stylesheet">
        <link rel="stylesheet" href="/Public/Admin/assets/css/ace.min.css" />
        <link rel="stylesheet"href="/Public/Admin/font/css/font-awesome.min.css" />
        <!--[if lte IE 8]>
		  <link rel="stylesheet" href="/Public/Admin/assets/css/ace-ie.min.css" />
		<![endif]-->
		<script src="/Public/Admin/js/jquery-1.9.1.min.js"></script>
        <script src="/Public/Admin/assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/Public/Admin/Widget/Validform/5.3.2/Validform.min.js"></script>
		<script src="/Public/Admin/assets/js/typeahead-bs2.min.js"></script>           	
		<script src="/Public/Admin/assets/js/jquery.dataTables.min.js"></script>
		<script src="/Public/Admin/assets/js/jquery.dataTables.bootstrap.js"></script>
        <script src="/Public/Admin/assets/layer/layer.js" type="text/javascript" ></script>          
		<script src="/Public/Admin/js/lrtk.js" type="text/javascript" ></script>
         <script src="/Public/Admin/assets/layer/layer.js" type="text/javascript"></script>	
        <script src="/Public/Admin/assets/laydate/laydate.js" type="text/javascript"></script>
<title>管理员</title>
</head>
<body>
<div class="page-content clearfix">
  <div class="administrator">
       <div class="d_Confirm_Order_style">
    <div class="search_style">
     
      <ul class="search_content clearfix">
       <li><label class="l_f"></label><inpt name="" type="text"  class="text_add" placeholder=""  style=" width:400px"/></li>
       <li><label class="l_f"></label><inut class="" id="start" style=" margin-left:10px;"></li>
       <li style="width:90px;"></li>
      </ul>
    </div>
    <!--操作-->
     <div class="border clearfix">
       <span class="l_f">
        <a href="javascript:ovid()" id="administrator_add" class="btn btn-warning"><i class="fa fa-plus"></i> 添加管理员</a>
       </span>
       <span class="r_f">共：<b>5</b>人</span>
     </div>
     <!--管理员列表-->
     <div class="clearfix administrator_style" id="administrator">
      <div class="left_style">
      <div id="scrollsidebar" class="left_Treeview">
        <div class="show_btn" id="rightArrow"><span></span></div>
        <div class="widget-box side_content" >
         <div class="side_title"><a title="隐藏" class="close_btn"><span></span></a></div>
         <div class="side_list"><div class="widget-header header-color-green2"><h4 class="lighter smaller">管理员分类列表</h4></div>
         <div class="widget-body">
           <ul class="b_P_Sort_list">
           <li><i class="fa fa-users green"></i> <a href="#">全部管理员（13）</a></li>
            <li><i class="fa fa-users orange"></i> <a href="#">超级管理员（1）</a></li>
            <li><i class="fa fa-users orange"></i> <a href="#">普通管理员（5）</a></li>
            <li><i class="fa fa-users orange"></i> <a href="#">产品编辑管理员（4）</a></li>
           </ul>
        </div>
       </div>
      </div>  
      </div>
      </div>
      <div class="table_menu_list"  id="testIframe">
           <table class="table table-striped table-bordered table-hover" id="sample_table">
		<thead>
		     <tr>
				<th width="25px"><label><span class="lbl"></span></label></th>
				<th width="80px">ID</th>
				<th width="250px">名称</th>
				<th width="100px">手机</th>
				<th width="100px">邮箱</th>
                <th width="100px">角色</th>				
				<th width="180px">加入时间</th>
				<th width="70px">状态</th>
				<th width="200px">操作</th>
			</tr>
		</thead>
	<tbody>

<!--列表遍历开始-->
<?php if(is_array($arrall)): foreach($arrall as $key=>$v): ?><tr>
      <td><label><span class="lbl"></span></label></td>
      <td><?php echo ($v["id"]); ?></td>
      <td style="color:#f00;"><?php echo ($v["username"]); ?></td>
      <td style="width:100px;"><?php echo ($v["phone"]); ?></td>
      <td ><?php echo ($v["mail"]); ?></td>
      <td><?php echo ($v["role"]); ?></td>
      <td><?php echo ($v["atime"]); ?></td>
      <td class="td-status">
		   	<?php  if($v['status']=='已启用'){ echo '<span class="label label-success radius">已启用</span>'; } if($v['status']=='已停用'){ echo '<span class="label label-defaunt radius">已停用</span>'; } ?>
      </td>
      <td class="td-manage">
       	<?php  if($v['status']=='已启用'){ echo '<a style="text-decoration:none" class="btn btn-xs btn-success" onClick="member_stop(this,'.$v['id'].')" href="javascript:;" title="停用"><i class="fa fa-check  bigger-120"></i></a>'; } if($v['status']=='已停用'){ echo '<a style="text-decoration:none" class="btn btn-xs " onClick="member_start(this,'.$v['id'].')" href="javascript:;" title="启用"><i class="fa fa-close bigger-120"></i></a>'; } ?>
        <a title="删除" href="javascript:;"  onclick="member_del(this,<?php echo ($v["id"]); ?>)" class="btn btn-xs btn-warning" id=""><i class="fa fa-trash  bigger-120"></i></a>
       </td><?php endforeach; endif; ?> 

 <!-- 列表遍历结束 -->
    </tbody>
    </table>
      </div>
     </div>
  </div>
</div>

 <!--添加管理员-->
 <div id="add_administrator_style" class="add_menber" style="display:none">
    <form action="<?php echo U('Adminuser/admin_add');?>" method="post" id="form-admin-add">
		<div class="form-group">

			<label class="form-label"><span class="c-red">*</span>管理员：</label>
			<div class="formControls">
				<input type="text" class="input-text" placeholder="登录名" id="username" name="username" datatype="/\w{4,10}/i" nullmsg="用户名不能为空" >
			只支持4-10位的数字及字母下划线组合,不能使用纯数字用户名
			</div>
			<div class="col-4"> <span class="Validform_checktip"></span></div>
		</div>
		<div class="form-group">
			<label class="form-label"><span class="c-red">*</span>初始密码：</label>
			<div class="formControls">
			<input type="password" placeholder="密码" name="password" autocomplete="off"  class="input-text" datatype="*6-20" nullmsg="密码不能为空">
			</div>
			<div class="col-4"> <span class="Validform_checktip"></span></div>
		</div>

		<div class="form-group">
			<label class="form-label "><span class="c-red">*</span>确认密码：</label>
			<div class="formControls ">
		<input type="password" placeholder="确认新密码" autocomplete="off" class="input-text Validform_error" errormsg="您两次输入的新密码不一致！" datatype="*" nullmsg="请再输入一次新密码！" recheck="password" id="newpassword2" name="password2">
		</div>
			<div class="col-4"> <span class="Validform_checktip"></span></div>
		</div>
	
		<div class="form-group">
			<label class="form-label "><span class="c-red">*</span>性别：</label>
			<div class="formControls  skin-minimal">
            <label><input name="sex" type="radio" class="ace" value="1"><span class="lbl">男</span></label>&nbsp;&nbsp;
            <label><input name="sex" type="radio" class="ace" value="2"><span class="lbl">女</span></label>
			</div>
			<div class="col-4"> <span class="Validform_checktip"></span></div>
		</div>

		<div class="form-group">
			<label class="form-label "><span class="c-red">*</span>手机：</label>
			<div class="formControls ">
				<input type="text" class="input-text"  placeholder="" id="user-tel" name="phone" datatype="m" nullmsg="手机不能为空">
			</div>
			<div class="col-4"><span class="Validform_checktip"></span></div>
		</div>

		<div class="form-group">
			<label class="form-label"><span class="c-red">*</span>邮箱：</label>
			<div class="formControls ">
				<input type="text" class="input-text" placeholder="@" name="mail" id="email" datatype="e" nullmsg="请输入邮箱">
			</div>
			<div class="col-4"> <span class="Validform_checktip"></span></div>
		</div>
		<div class="form-group">
			<!-- 角色遍历表 -->
			
			<label class="form-label">角色：</label>
			<div class="formControls "> <span class="select-box" style="width:300px;">
				 <?php if(is_array($roleall)): foreach($roleall as $key=>$role): ?><label class="checkbox">
  							<input type="checkbox" value="<?php echo ($role["id"]); ?>" name="<?php echo ($role["rolename"]); ?>"><?php echo ($role["rolename"]); ?>
					</label><?php endforeach; endif; ?>
			</span> </div>
			<!-- 角色遍历表结束 -->
		</div>
		<div class="form-group">
			
			<div class="col-4"> </div>
		</div>
		<div> 
        <input class="btn btn-primary radius" type="submit" id="Add_Administrator" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
	</form>
   </div>
 </div>

</body>
</html>
<script type="text/javascript">
	
	var j8name = document.getElementById('username');

	j8name.onblur=function(){
		if(j8name.value.length >= 4){
			$.ajax({
				type:'post',
				url:"<?php echo U('Adminuser/admin_add');?>",
				data:"name="+j8name.value,
				success:function(k1){
					
					if(k1==1){
						//alert('用户名可以使用');
					}else{
						layer.msg('用户名已存在',{icon:1,time:1000});
						j8name.value =null;
					}
					if(k1==3){
						layer.msg('用户名不能为纯数字',{icon:1,time:1000});
						j8name.value =null;
					}

				}
			});
		}		
	}					
/****************奔放的JS代码*********************/
jQuery(function($) {

		var oTable1 = $('#sample_table').dataTable( {
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
			});

</script>
<script type="text/javascript">
$(function() { 
	$("#administrator").fix({
		float : 'left',
		//minStatue : true,
		skin : 'green',	
		durationTime :false,
		spacingw:50,//设置隐藏时的距离
	    spacingh:270,//设置显示时间距
	});
});
//字数限制
function checkLength(which) {
	var maxChars = 100; //
	if(which.value.length > maxChars){
	   layer.open({
	   icon:2,
	   title:'提示框',
	   content:'您输入的字数超过限制!',	
    });
		// 超过限制的字数了就将 文本框中的内容按规定的字数 截取
		which.value = which.value.substring(0,maxChars);
		return false;
	}else{
		var curr = maxChars - which.value.length; //250 减去 当前输入的
		document.getElementById("sy").innerHTML = curr.toString();
		return true;
	}
};
//初始化宽度、高度  
 $(".widget-box").height($(window).height()-215); 
$(".table_menu_list").width($(window).width()-260);
 $(".table_menu_list").height($(window).height()-215);
  //当文档窗口发生改变时 触发  
    $(window).resize(function(){
	$(".widget-box").height($(window).height()-215);
	 $(".table_menu_list").width($(window).width()-260);
	  $(".table_menu_list").height($(window).height()-215);
	})
 laydate({
    elem: '#start',
    event: 'focus' 
});

/*用户-停用*/
function member_stop(obj,id){
	layer.confirm('确认要停用吗？',function(index){
		$.ajax({
				type:'post',
				url:"<?php echo U('Adminuser/forbidden');?>",
				data:"id="+id,
				success:function(k1){
					if(k1==2){
						layer.msg('无法此账号!',{icon:1,time:1000});
					}
					if(k1==1){

				    $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs " onClick="member_start(this,'+id+')" href="javascript:;" title="启用"><i class="fa fa-close bigger-120"></i></a>');
						
				    $(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已停用</span>');
				    $(obj).remove();
				     layer.msg('已停用!',{icon: 5,time:1000});
					}
				}
			});


	});
}


/*用户-启用*/
function member_start(obj,id){
	layer.confirm('确认要启用吗？',function(index){
		$.ajax({
				type:'post',
				url:"<?php echo U('Adminuser/start');?>",
				data:"id="+id,
				success:function(k1){
					if(k1==1){
					$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs btn-success" onClick="member_stop(this,'+id+')" href="javascript:;" title="停用"><i class="fa fa-check  bigger-120"></i></a>');	
					$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
					$(obj).remove();
					layer.msg('已启用!',{icon: 6,time:1000});
					}
				}
			});

	});
}
/*-编辑*/
function member_edit(title,url,id,w,h){
	layer_show(title,url,w,h);
}

/*-删除*/
function member_del(obj,id){
	layer.confirm('删除后无法恢复,确认要删除吗？',function(){
		$.ajax({
				type:'post',
				url:"<?php echo U('Adminuser/delete');?>",
				data:"id="+id,
				success:function(k1){

					if(k1==2){
						layer.msg('无法删除此账号!',{icon:1,time:1000});
					}
					if(k1==1){
						$(obj).parents("tr").remove();
						layer.msg('已删除!',{icon:1,time:1000});
					}
				}
			});

	});

		
}
/*添加管理员*/
$('#administrator_add').on('click', function(){
	layer.open({
    type: 1,
	title:'添加管理员',
	area: ['700px',''],
	shadeClose: false,
	content: $('#add_administrator_style'),
	
	});
})
	//表单验证提交
$("#form-admin-add").Validform({
		
		 tiptype:2,
	
		callback:function(data){
		//form[0].submit();
		if(data.status==1){ 
                layer.msg(data.info, {icon: data.status,time: 1000}, function(){ 
                    location.reload();//刷新页面 
                    });   
            } 
            else{ 
                layer.msg(data.info, {icon: data.status,time: 3000}); 
            } 		  
			var index =parent.$("#iframe").attr("src");
			parent.layer.close(index);
			//
		}
		
		
	});	
</script>