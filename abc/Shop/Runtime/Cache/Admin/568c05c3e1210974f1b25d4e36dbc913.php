<?php if (!defined('THINK_PATH')) exit();?><!-- 分类管理页面 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="/abc/Public/Admin/assets/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" src="/abc/Public/Admin/css/style.css"/>       
        <link href="/abc/Public/Admin/assets/css/codemirror.css" rel="stylesheet">
        <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace.min.css" />
        <link rel="stylesheet" href="/abc/Public/Admin/Widget/zTree/css/zTreeStyle/zTreeStyle.css" type="text/css">
        <link rel="stylesheet" href="/abc/Public/Admin/assets/css/font-awesome.min.css" />
        
		<!--[if IE 7]>
		  <link rel="stylesheet" href="/abc/Public/Admin/assets/css/font-awesome-ie7.min.css" />
		<![endif]-->
        <!--[if lte IE 8]>
		  <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace-ie.min.css" />
		<![endif]-->
			<script src="/abc/Public/Admin/assets/js/jquery.min.js"></script>
		<!-- <![endif]-->
		<!--[if IE]>
       <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <![endif]-->
		<!--[if !IE]> -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='/abc/Public/Admin/assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
		</script>
		<!-- <![endif]-->
		<!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='/abc/Public/Admin/assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->
        <script src="/abc/Public/Admin/assets/js/ace-elements.min.js"></script>
		<script src="/abc/Public/Admin/assets/js/ace.min.js"></script>
        <script src="/abc/Public/Admin/assets/js/bootstrap.min.js"></script>
		<script src="/abc/Public/Admin/assets/js/typeahead-bs2.min.js"></script>
        <script type="text/javascript" src="/abc/Public/Admin/Widget/zTree/js/jquery.ztree.all-3.5.min.js"></script> 
        <script src="/abc/Public/Admin/js/lrtk.js" type="text/javascript" ></script>
</head>

<body>
<div class=" clearfix">
 <div id="category">
<!---->
 <iframe style='width: 1500px; height: 1000px;' ID="testIframe" Name="testIframe" FRAMEBORDER=0 SCROLLING=AUTO  SRC="<?php echo U('Type/shopnews');?>" class="page_right_style"></iframe>
 </div>
</div>
</body>
</html>