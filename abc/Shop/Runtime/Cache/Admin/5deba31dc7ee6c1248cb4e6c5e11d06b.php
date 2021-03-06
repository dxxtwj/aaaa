<?php if (!defined('THINK_PATH')) exit();?><!-- 权限管理 -->
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
        <link rel="stylesheet" href="/Public/Admin/font/css/font-awesome.min.css" />
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
<title>节点管理</title>
</head>
<body>
 <h2 style="color:#f00;">节点为便于识别与扩展,添加可为任意字段,非专业人士请谨慎添加!!</h2>
 <div class="margin clearfix">
   <div class="border clearfix">
   	  	<a href="javascript:ovid()" id="administrator_add" class="btn btn-warning"><i class="fa fa-plus"></i> 添加模块节点</a>
     </div>
     <div class="compete_list">
       <table id="sample-table-1" class="table table-striped table-bordered table-hover" align="right">
		 <thead>
			<tr>
			  <th class="center" >节点ID</th>
			  <th>权限名称</th>
              <th>节点内容</th>
			  <th>权限级别</th>
              <th>操作</th>
             </tr>
		    </thead>

  <tbody>
	<!-- 模块遍历开始 -->
<?php if(is_array($arr)): foreach($arr as $key=>$tid1): if($tid1['tid']=='1'){ ?>
			<tr>
				<td ><?php echo ($tid1["id"]); ?></td>
				<td><?php echo ($tid1["nodetitle"]); ?></td>
				<td><?php echo ($tid1["nodebody"]); ?></td>
				<td><b>模块节点</b></td>
				<td>
					<a href="<?php echo U('node/contre_add');?>?tid=<?php echo ($tid1["id"]); ?>"  class="btn btn-danger  btn-xs" id="Competence_add" title="<?php echo ($tid1["nodetitle"]); ?> > 添加控制" ><i class="fa fa-plus"></i> 添加控制</a>
            		<a  href="javascript:;"  onclick="Competence_del(this,<?php echo ($tid1["id"]); ?>)" class="btn btn-xs btn-warning "><i class="fa fa-trash  bigger-120"></i>删除</a>  
				</td>
			  </tr>
	
	<!-- 控制遍历开始 -->
	<?php if(is_array($arr)): foreach($arr as $key=>$tid2): if($tid2['tid']===$tid1['id']){ ?>
			<tr >	
				<td>　　　𠃊<?php echo ($tid2["id"]); ?></td>
				<td><?php echo ($tid2["nodetitle"]); ?></td>
				<td><?php echo ($tid2["nodebody"]); ?></td>
				<td> 　>　控制节点</td>
				<td>
					<a href="<?php echo U('node/contre_add');?>?tid=<?php echo ($tid2["id"]); ?>"  class="btn btn-info  btn-xs" id="Competence_add" title="<?php echo ($tid2["nodetitle"]); ?> > 添加方法"><i class="fa fa-plus"></i> 添加方法</a>
            		<a  href="javascript:;"  onclick="Competence_del(this,<?php echo ($tid2["id"]); ?>)" class="btn btn-xs btn-warning "><i class="fa fa-trash  bigger-120"></i>删除</a> 
				</td>
			 </tr>	
		<?php if(is_array($arr)): foreach($arr as $key=>$tid3): if($tid3['tid']===$tid2['id'] && $tid3['tid']===$tid2['id']){ ?>
			<tr >	
				<td>　　　　　𠃊 𠃊<?php echo ($tid3["id"]); ?></td>
				<td><?php echo ($tid3["nodetitle"]); ?></td>
				<td><?php echo ($tid3["nodebody"]); ?></td>
				<td>　　>　>　<font size="1" color="red">方法节点</font></td>
				<td><a href="<?php echo U('node/contre_add');?>?tid=<?php echo ($tid2["id"]); ?>"  class="btn btn-e  btn-xs"><i class="fa fa-edit bigger-120"></i> 编辑方法</a>
				<a  href="javascript:;"  onclick="Competence_del(this,<?php echo ($tid3["id"]); ?>)" class="btn btn-xs btn-warning "><i class="fa fa-trash  bigger-120"></i>删除</a> </td>
			 </tr>
	<?php  } endforeach; endif; ?>	
	<?php } endforeach; endif; ?>
	<?php } ?> 
	<!-- 控制遍历结束 --><?php endforeach; endif; ?>
	<!-- 模板遍历结束 -->
	 </tbody>
	</table>
   </div>
 </div>

 <!-- 添加模块名 -->
 <div id="add_administrator_style" class="add_menber" style="display:none">
    <form action="<?php echo U('Node/node_add');?>" method="post" id="form-admin-add">
		<div class="form-group">
			<label class="form-label">模块名</label>
			<div class="formControls">
				<input type="text" class="input-text" placeholder="模块名" id="username" name="nodetitle" >
			</div>
			<input type="hidden"  name="tid" value="1"/>
			<div class="col-4"> <span class="Validform_checktip"></span></div>
		</div>
		<div class="form-group">
			<label class="form-label">模块内容</label>
			<div class="formControls">
			<input type="text" placeholder="模块内容" name="nodebody" autocomplete="off"  class="input-text" >
			</div>
		<div class="form-group">
			<div class="col-4"> </div>
		</div>
		<div> 
        <input class="btn btn-primary radius" type="submit" id="Add_Administrator" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
	</form>
   </div>
 </div>
 <!-- 添加模块名结束 -->

  
</body>
</html>
<script type="text/javascript">
 /*节点-删除*/
function Competence_del(obj,id){
	layer.confirm('删除后无法恢复,确认要删除吗',function(){
				$.ajax({
					type:'post',
					url:"<?php echo U('Node/contre_del');?>",
					data:"id="+id,
					success:function(s1){
						if(s1==1){
							$(obj).parents("tr").remove();
				 			layer.msg('已删除!',{icon:1,time:1000});
						}else{
							if(s1==3){
								layer.msg('请先删除子节点',{icon:1,time:1000});
							}else{
								layer.msg('未知错误',{icon:1,time:1000});
							}
						}
					}
				})		
	     });
}
/*修改权限*/
function Competence_modify(id){
		window.location.href ="Competence.html?="+id;
};
/*字数限制*/
function checkLength(which) {
	var maxChars = 200; //
	if(which.value.length > maxChars){
	   layer.open({
	   icon:2,
	   title:'提示框',
	   content:'您出入的字数超多限制!',	
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
//面包屑返回值
var index = parent.layer.getFrameIndex(window.name);
parent.layer.iframeAuto(index);
$('.Order_form ,#Competence_add').on('click', function(){
	var cname = $(this).attr("title");
	var cnames = parent.$('.Current_page').html();
	var herf = parent.$("#iframe").attr("src");
    parent.$('#parentIframe span').html(cname);
	parent.$('#parentIframe').css("display","inline-block");
    parent.$('.Current_page').attr("name",herf).css({"color":"#4c8fbd","cursor":"pointer"});
	//parent.$('.Current_page').html("<a href='javascript:void(0)' name="+herf+">" + cnames + "</a>");
    parent.layer.close(index);
	
});


$('#administrator_add').on('click', function(){
	layer.open({
    type: 1,
	title:'添加模块节点',
	area: ['700px',''],
	shadeClose: false,
	content: $('#add_administrator_style'),
	
	});
});

//面包屑返回值
var index = parent.layer.getFrameIndex(window.name);
parent.layer.iframeAuto(index);
$('.Order_form ,#Competence_add').on('click', function(){
	var cname = $(this).attr("title");
	var cnames = parent.$('.Current_page').html();
	var herf = parent.$("#iframe").attr("src");
    parent.$('#parentIframe span').html(cname);
	parent.$('#parentIframe').css("display","inline-block");
    parent.$('.Current_page').attr("name",herf).css({"color":"#4c8fbd","cursor":"pointer"});
	//parent.$('.Current_page').html("<a href='javascript:void(0)' name="+herf+">" + cnames + "</a>");
    parent.layer.close(index);
	
});
</script>