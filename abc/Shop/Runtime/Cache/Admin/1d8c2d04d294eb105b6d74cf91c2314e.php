<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
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
	<title>修改广告分类</title>
</head>
<body>
	<!--修改分类-->
	<form method="post" action="<?php echo U('Adssort/edit');?>">
		<input type="hidden" name="id" value="<?=$data['id']?>">
		<div class="sort_style_add margin" id="sort_style_edit">
			<div class="">
				<ul>
					<li><label class="label_name">分类名称</label><div class="col-sm-9"><input name="sort" value="<?php echo ($data["sort"]); ?>" type="text" id="form-field-1" placeholder="" class="col-xs-10 col-sm-5"></div></li>
					<!-- <li><label class="label_name">分类说明</label><div class="col-sm-9"><textarea name="describe" class="form-control" id="form-field-8" placeholder="" onkeyup="checkLength(this);"></textarea><span class="wordage">剩余字数：<span id="sy" style="color:Red;">200</span>字</span></div></li> -->
					<li><label class="label_name">分类说明</label><div class="col-sm-9"><input name="describe" style="width:500px;height:150px;" value="<?php echo ($data["describe"]); ?>" class="form-control" id="form-field-8" placeholder="" onkeyup="checkLength(this);"><span class="wordage">剩余字数：<span id="sy" style="color:Red;">200</span>字</span></div></li>
					<li><label class="label_name">分类状态</label>
					<span class="add_content"> &nbsp;&nbsp;<label><input name="status" value="1" <?=$data['status']==1 ? 'checked' : '';?> type="radio" checked="checked" class="ace"><span class="lbl">显示</span></label>&nbsp;&nbsp;&nbsp;
						<label><input name="status" value="2" <?=$data['status']==2 ? 'checked' : ''?> type="radio" class="ace"><span class="lbl">隐藏</span></label></span>
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

<script>
	// 检测输入的字数
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
</script>