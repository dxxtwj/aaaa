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
	<title>分类管理</title>
</head>

<body>
<div class="page-content clearfix">
	<div class="sort_style">
		<div class="border clearfix">
			<span class="l_f">
				<a href="javascript:void(0)" id="sort_add" class="btn btn-warning"><i class="fa fa-plus"></i> 添加分类</a>
				<button class="btn btn-danger" id="checkdel" href="javascript:;"><i class="fa fa-trash"></i> 批量删除</button>
		        <a href="javascript:void(0)" onClick="javascript :history.back(-1);" class="btn btn-info"><i class="fa fa-reply"></i> 返回上一步</a>
			</span>
			<span class="r_f">共：<b><?php echo ($count); ?></b>个分类</span>
		</div>
		<div class="sort_list">
			<table class="table table-striped table-bordered table-hover" id="sample-table">
				<thead>
					<tr>
						<th width="25px"><label><input type="checkbox" class="all"><span class="lbl"></span></label></th>
						<th width="50px">ID</th>
						<th width="100px">分类名称</th>
						<th width="50px">数量</th>
						<th width="350px">描述</th>
						<th width="180px">加入时间</th>
						<th width="70px">状态</th>                
						<th width="250px">操作</th>
					</tr>
				</thead>
				<tbody id="sample-tbody">
					<?php if(empty($list)): ?><tr><td colspan="8"><h3>暂无数据~~~</h3></td></tr>
					<?php else: ?>
					<?php if(is_array($list)): foreach($list as $k=>$v): ?><tr class="list">
						    <td><label><input type="checkbox" name='id[]' value="<?php echo ($v["id"]); ?>"><span class="lbl"></span></label></td>
						    <td><?php echo ($v["id"]); ?></td>
						    <td><?php echo ($v["sort"]); ?></td>
						    <!-- 如果新添加的广告分类下面没有广告就把广告分类的数量赋值为0 -->
						    <?php if($v['num']==''): ?><td>0</td>
						    <?php else: ?>
						    	<td><?php echo ($v["num"]); ?></td><?php endif; ?>
						    <td><?php echo ($v["describe"]); ?></td>
						    <td><?php echo ($v["addtime"]); ?></td>
						    <?php if($v['ststus']=='隐藏'): ?><td class="td-status"><span class="label label-success radius"><?php echo ($v["status"]); ?></span></td>
						    	<td class="td-manage">
						    	<a onClick="member_start(this,'10001','<?php echo ($v["id"]); ?>')"  href="javascript:;" title="显示"  class="btn btn-xs btn-success"><i class="fa fa-check  bigger-120"></i></a>   
						    <?php else: ?>
						    	<td class="td-status"><span class="label label-success radius"><?php echo ($v["status"]); ?></span></td>
						    	<td class="td-manage">
						    	<a onClick="member_stop(this,'10001','<?php echo ($v["id"]); ?>')"  href="javascript:;" title="隐藏"  class="btn btn-xs btn-success"><i class="fa fa-check  bigger-120"></i></a><?php endif; ?>	  
						    	<a title="编辑" id="sort_edit" href="/abc/Admin/Adssort/edit/id/<?php echo ($v["id"]); ?>;"  class="btn btn-xs btn-info" ><i class="fa fa-edit bigger-120"></i></a>    
						    	<a title="删除" href="javascript:;"  onclick="member_del(this,'1','<?php echo ($v["id"]); ?>')" class="btn btn-xs btn-warning" ><i class="fa fa-trash  bigger-120"></i></a>
						    	<a href="javascript:void(0)" name="<?php echo U('Ads/Ads_list');?>" class="btn btn-xs btn-pink ads_link" onclick="AdlistOrders('561');" title="幻灯片广告列表"><i class="fa  fa-bars  bigger-120"></i></a>
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

<!--添加分类-->
<form method="post" action="<?php echo U('Adssort/add');?>">
	<div class="sort_style_add margin" id="sort_style_add" style="display:none">
		<div class="">
			<ul>
				<li><label class="label_name">分类名称</label><div class="col-sm-9"><input name="sort" type="text" id="form-field-1" placeholder="" class="col-xs-10 col-sm-5"></div></li>
				<li><label class="label_name">分类说明</label><div class="col-sm-9"><textarea name="describe" class="form-control" id="form-field-8" placeholder="" onkeyup="checkLength(this);"></textarea><span class="wordage">剩余字数：<span id="sy" style="color:Red;">200</span>字</span></div></li>
				<li><label class="label_name">分类状态</label>
				<span class="add_content"> &nbsp;&nbsp;<label><input name="status" value="1" type="radio" checked="checked" class="ace"><span class="lbl">显示</span></label>&nbsp;&nbsp;&nbsp;
					<label><input name="status" value="2" type="radio" class="ace"><span class="lbl">隐藏</span></label></span>
				</li>
				<center>
			  		<li>
			  			<button class="btn btn-small btn-default">提交</button>
			  			<a href="<?php echo U('Adssort/Sort_ads');?>" class="btn btn-small btn-danger">取消</a>
			  		</li>
			  	</center>
			</ul>
		</div>
	</div>
</form>

</body>
</html>
<script type="text/javascript">
// 弹出添加分类小框
$('#sort_add').on('click', function(){
	  layer.open({
        type: 1,
        title: '添加分类',
		maxmin: true, 
		shadeClose: false, //点击遮罩关闭层
        area : ['750px' , ''],
        content:$('#sort_style_add'),
		// btn:['提交','取消'],
		yes:function(index,layero){	
		 var num=0;
		 var str="";
     $(".sort_style_add input[type$='text']").each(function(n){
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

function checkLength(which) {
	var maxChars = 200; //
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

/*广告分类-隐藏*/
function member_stop(obj,id,a){
	layer.confirm('确认要隐藏吗？',{icon:0,},function(index){
		$.ajax({
			type:'get',
			url : "<?php echo U('Adssort/doStatus');?>",
			data : {id:+ a},
			success: function(res) {
				console.log(res);
				if (res != '-1') {
					$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs " onClick="member_start(this,id, '+res+')" href="javascript:;" title="显示"><i class="fa fa-close bigger-120"></i></a>');
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

/*广告分类-显示*/
function member_start(obj,id,a){
	layer.confirm('确认要显示吗？',{icon:0,},function(index){
		$.ajax({
			type:'get',
			url : "<?php echo U('Adssort/doStatus');?>",
			data : {id:+ a},
			success: function(res) {
				console.log(res);
				if (res != '-1') {
					$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs btn-success" onClick="member_stop(this,id, '+res+')" href="javascript:;" title="隐藏"><i class="fa fa-check  bigger-120"></i></a>');
					$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">显示</span>');
					$(obj).remove();
					layer.msg('显示!',{icon: 6,time:1000});
				} else {
					layer.msg('隐藏!',{icon: 6,time:1000});
				}
			},
		});
	});
}

/*广告分类-删除*/
function member_del(obj,id,a){
	layer.confirm('确认要删除吗？',{icon:0,},function(index){
		$.ajax({
			type: 'get',
			url : "<?php echo U('Adssort/ajaxDel');?>",
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

//面包屑返回值
var index = parent.layer.getFrameIndex(window.name);
parent.layer.iframeAuto(index);
$('.Order_form ,.ads_link').on('click', function(){
	var cname = $(this).attr("title");
	var cnames = parent.$('.Current_page').html();
	var herf = parent.$("#iframe").attr("src");
    parent.$('#parentIframe span').html(cname);
	parent.$('#parentIframe').css("display","inline-block");
    parent.$('.Current_page').attr("name",herf).css({"color":"#4c8fbd","cursor":"pointer"});
	//parent.$('.Current_page').html("<a href='javascript:void(0)' name="+herf+">" + cnames + "</a>");
    parent.layer.close(index);
	
});

function AdlistOrders(id){
	window.location.href = "<?php echo U('Ads/Ads_list');?>?="+id;
};
</script>
<script type="text/javascript">
jQuery(function($) {
	var oTable1 = $('#sample-table').dataTable( {
		"aaSorting": [[ 1, "desc" ]],//默认第几个排序
		"bStateSave": true,//状态保存
		"aoColumnDefs": [
		//{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
		{"orderable":false,"aTargets":[0,2,4,6,7,]}// 制定列不参与排序
	] } );

	// $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});
	// function tooltip_placement(context, source) {
	// 	var $source = $(source);
	// 	var $parent = $source.closest('table')
	// 	var off1 = $parent.offset();
	// 	var w1 = $parent.width();

	// 	var off2 = $source.offset();
	// 	var w2 = $source.width();

	// 	if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
	// 	return 'left';
	// }
})

// 批量删除
$(function(){
   	$(".all").click(function(){  
       	if(this.checked){  
       		// prop获取在匹配的元素集中的第一个元素的属性值。
        	$(".list :checkbox").prop("checked", true);  
       	}else{  
        	$(".list :checkbox").prop("checked", false); 
       	}  
    });
    // 点击批量删除按钮
    $("#checkdel").click(function(){
        var ids = '';

        $("input[name='id[]']:checkbox:checked").each(function () {
        	// 用,拼接id
        	ids += $(this).val() + ',';
        })
        
        if(confirm("确认要删除？")){ 
        	window.location.href = "/abc/Admin/Adssort/checkdel?id="+ids;
        }
    })
}) 

// 移除原来的分页按钮
window.onload=function() {
	$('.row').last().remove();
}
</script>