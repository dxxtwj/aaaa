<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>添加控制器</title>
</head>
   <link href="/abc/Public/Admin/assets/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="/abc/Public/Admin/css/style.css"/>       
        <link href="/abc/Public/Admin/assets/css/codemirror.css" rel="stylesheet">
        <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace.min.css" />
        <link rel="stylesheet" href="/abc/Public/Admin/font/css/font-awesome.min.css" />
        <script src="/abc/Public/Admin/js/jquery-1.9.1.min.js"></script>
        <script src="/abc/Public/Admin/assets/js/bootstrap.min.js"></script>
		<script src="/abc/Public/Admin/assets/js/typeahead-bs2.min.js"></script>           	
		<script src="/abc/Public/Admin/assets/js/jquery.dataTables.min.js"></script>
		<script src="/abc/Public/Admin/assets/js/jquery.dataTables.bootstrap.js"></script>
        <script src="/abc/Public/Admin/assets/layer/layer.js" type="text/javascript" ></script>          
        <script src="/abc/Public/Admin/assets/laydate/laydate.js" type="text/javascript"></script>
        <script src="/abc/Public/Admin/js/dragDivResize.js" type="text/javascript"></script>
<body>

	 <!-- 添加控制名 -->
	
 <b class="btn btn-primary radius"><h1 style="color:#f00;font-size:40px;">控制器-->方法添加</h1></b>
 <div id="add_administrator_style" class="add_menber" style="border:5px solid #428bca;width:500px;" >

    <form action="<?php echo U('Node/contre_add');?>" method="post" id="form-admin-add">
		<div class="form-group">
			<label class="form-label">控制或方法:</label>
			<div class="formControls">
				<input type="text" class="input-text" placeholder="节点名称" id="username" name="nodetitle" >
			</div>
			<input type="hidden"  name="tid" value="<?php echo ($tid); ?>"/>
			<div class="col-4"> <span class="Validform_checktip"></span></div>
		</div>
		<div class="form-group">
			<label class="form-label">控制或方法:</label>
			<div class="formControls">
			<input type="text" placeholder="节点字段(用于判断)" name="nodebody" autocomplete="off"  class="input-text" >
			</div>
		<div class="form-group">
			<div class="col-4"> </div>
		</div>
		<div> 
        　　　　<input class="btn btn-warning radius" type="submit" id="Add_Administrator" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
        　　　　<a href="<?php echo U('Node/node_list');?>" class="btn btn-primary radius" >返回</a>
	</form>
   </div>
 </div>
</body>
<script>
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
</html>