<?php if (!defined('THINK_PATH')) exit();?><!-- 品牌列表页 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
 <link href="/abc/Public/Admin/assets/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="/abc/Public/Admin/css/style.css"/>       
        <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace.min.css" />
        <link rel="stylesheet" href="/abc/Public/Admin/assets/css/font-awesome.min.css" />
        <link href="/abc/Public/Admin/Widget/icheck/icheck.css" rel="stylesheet" type="text/css" />
		<!--[if IE 7]>
		  <link rel="stylesheet" href="/abc/Public/Admin/assets/css/font-awesome-ie7.min.css" />
		<![endif]-->
        <!--[if lte IE 8]>
		  <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace-ie.min.css" />
		<![endif]-->
	    <script src="/abc/Public/Admin/js/jquery-1.9.1.min.js"></script>
        <script src="/abc/Public/Admin/assets/js/bootstrap.min.js"></script>
        <script src="/abc/Public/Admin/assets/js/typeahead-bs2.min.js"></script>
       
		<!-- page specific plugin scripts -->
		<script src="/abc/Public/Admin/assets/js/jquery.dataTables.min.js"></script>
		<script src="/abc/Public/Admin/assets/js/jquery.dataTables.bootstrap.js"></script>
        <script type="text/javascript" src="/abc/Public/Admin/js/H-ui.js"></script> 
        <script type="text/javascript" src="/abc/Public/Admin/js/H-ui.admin.js"></script> 
        <script src="/abc/Public/Admin/assets/layer/layer.js" type="text/javascript" ></script>
        <script src="/abc/Public/Admin/assets/laydate/laydate.js" type="text/javascript"></script>
         <script src="/abc/Public/Admin/assets/dist/echarts.js"></script>
         <script src="/abc/Public/Admin/js/lrtk.js" type="text/javascript"></script>
<title>回收站</title>
</head>

<body>
<div class="page-content clearfix">
  <div id="brand_style">
    <div class="search_style">
     
      <ul class="search_content clearfix">
       <li><label class="l_f">品牌名称</label><input name="" type="text"  class="text_add" placeholder="输入品牌名称"  style=" width:250px"/></li>
       <li><label class="l_f">添加时间</label><input class="inline laydate-icon" id="start" style=" margin-left:10px;"></li>
       <li><select name="" class="text_add"><option  value="1">国内品牌</option><option value="2">国外品牌</option></select></li>
       <li style="width:90px;"><button type="button" class="btn_search"><i class="icon-search"></i>查询</button></li>
      </ul>
    </div>
     <!--品牌列表-->
      <div class="table_menu_list" style="width: 1400px;">
       <table class="table table-striped table-bordered table-hover" id="sample-table">
		<thead>
		 	<tr>
				<th width="25px"><label><input type="checkbox" class="ace"><span class="lbl"></span></label></th>
				<th width="120px">ID</th>
				<th width="150px">商品名称</th>
				<th width="150px">商品图片</th>
				<th width="800px;">添加时间</th>
				<th width="800px;">还原商品</th>
				<th width="800px;">彻底删除</th>
			</tr>
		</thead>
	<tbody>
	<?php if(empty($goodsListDle)): ?><tr><td colspan='7'><h1>暂无数据</h1></td></tr>
	<?php else: ?>
	<?php if(is_array($goodsListDle)): foreach($goodsListDle as $key=>$v): ?><tr>
          <td width="25px"><label><input type="checkbox" class="ace" ><span class="lbl"></span></label></td>
          <td width="80px"><?php echo ($v['id']); ?></td>
          <td><a href="javascript:ovid()" name="Brand_detailed.html" style="cursor:pointer" class="text-primary brond_name" onclick="generateOrders('561');" title="<?php echo ($v['name']); ?>"><?php echo ($v['name']); ?></a></td>
          <td><img src="/abc/Public/<?php echo ($v['image0']); ?>" width="130"/></td>
          <td><?php echo ($v['atime']); ?></td>
			<td class="td-manage">
          <a title="还原商品" href="<?php echo U('Huishouzhan/huanyuan', 'id='.$v['id']);?>" class="btn btn-xs btn-warning" ><i class="icon-arrow-right"></i></a>
          </td>

          <td class="td-manage">
          <a onclick="del(this, <?php echo ($v['id']); ?>)" title="彻底删除" href="#" class="btn btn-xs btn-warning" ><i class="icon-trash  bigger-120"></i></a>
          </td>
		</tr><?php endforeach; endif; endif; ?> 
        </tbody>
        </table>
        </div>
     </div>
    
  </div>
</div>
</body>
</html>
<script>
	//图层隐藏限时参数		 
$(function() { 
	$("#category").fix({
		float : 'left',
		//minStatue : true,
		skin : 'green',	
		durationTime :false,
		stylewidth:'400',
		spacingw:30,//设置隐藏时的距离
	    spacingh:440,//设置显示时间距
	});
});
//面包屑返回值
var index = parent.layer.getFrameIndex(window.name);
parent.layer.iframeAuto(index);
$('.Order_form ,.brond_name').on('click', function(){
	var cname = $(this).attr("title");
	var cnames = parent.$('.Current_page').html();
	var herf = parent.$("#iframe").attr("src");
    parent.$('#parentIframe span').html(cname);
	parent.$('#parentIframe').css("display","inline-block");
    parent.$('.Current_page').attr("name",herf).css({"color":"#4c8fbd","cursor":"pointer"});
	//parent.$('.Current_page').html("<a href='javascript:void(0)' name="+herf+">" + cnames + "</a>");
    parent.layer.close(index);
	
});
function generateOrders(id){
	window.location.href = "Brand_detailed.html?="+id;
};
/*品牌-查看*/
function member_show(title,url,id,w,h){
	layer_show(title,url,w,h);
}
/*品牌-停用*/
function member_stop(obj,id){
	layer.confirm('确认要停用吗？',function(index){
		$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs " onClick="member_start(this,id)" href="javascript:;" title="启用"><i class="icon-ok bigger-120"></i></a>');
		$(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已停用</span>');
		$(obj).remove();
		layer.msg('已停用!',{icon: 5,time:1000});
	});
}

/*用户-启用*/
function member_start(obj,id){
	layer.confirm('确认要启用吗？',function(index){
		$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs btn-success" onClick="member_stop(this,id)" href="javascript:;" title="停用"><i class="icon-ok bigger-120"></i></a>');
		$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
		$(obj).remove();
		layer.msg('已启用!',{icon: 6,time:1000});
	});
}
/*品牌-编辑*/
function member_edit(title,url,id,w,h){
	layer_show(title,url,w,h);
}

/*品牌-删除*/
function member_del(obj,id){
	layer.confirm('确认要删除吗？',function(index){
		$(obj).parents("tr").remove();
		layer.msg('已删除!',{icon:1,time:1000});
	});
}
laydate({
    elem: '#start',
    event: 'focus' 
});

	function del(a, id) {
		// 弹窗
		var tiShi = confirm('确定要彻底删除该商品？');
		if (tiShi) {

			$.ajax({

				type: 'get',

				url: "<?php echo U('Huishouzhan/del');?>",

				data: {id: id},

				success: function(msg) {
					if (msg == 1) {

						alert('删除成功');
						$(a).parent().parent().parent().remove();
						return;
					} else if (msg == 2) {

						alert('删除失败');
						return;
					}
				}



			});
		}
	}

</script>
 
</script>