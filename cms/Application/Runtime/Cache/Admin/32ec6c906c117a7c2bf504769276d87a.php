<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="/Public/admin/assets/css/bootstrap.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="/Public/admin/assets/css/font-awesome.min.css" />
		<!--[if IE 7]>
		  <link rel="stylesheet" href="/Public/admin/assets/css/font-awesome-ie7.min.css" />
		<![endif]-->
		<link rel="stylesheet" href="/Public/admin/assets/css/ace.min.css" />
		<link rel="stylesheet" href="/Public/admin/assets/css/ace-rtl.min.css" />
		<link rel="stylesheet" href="/Public/admin/assets/css/ace-skins.min.css" />
        <link rel="stylesheet" href="/Public/admin/css/style.css"/>
		<!--[if lte IE 8]>
		  <link rel="stylesheet" href="/Public/admin/assets/css/ace-ie.min.css" />
		<![endif]-->
		<script src="/Public/admin/assets/js/ace-extra.min.js"></script>
		<!--[if lt IE 9]>
		<script src="/Public/admin/assets/js/html5shiv.js"></script>
		<script src="/Public/admin/assets/js/respond.min.js"></script>
		<![endif]-->
		<script src="/Public/admin/js/jquery-1.9.1.min.js"></script>        
        <script src="/Public/admin/assets/layer/layer.js" type="text/javascript"></script>
<title>登陆</title>
</head>
<style>
#login-box{
	background-color:rgba(255,255,255,0.3);
}
.login-layout .widget-box{
	padding:20px;
	border-radius: 15px;
	overflow: hidden;
}
.widget-body{
	border-radius: 15px;
	overflow: hidden;
}
.login-layout .widget-box .widget-main{
	background:#fff;
}
.header{
	border-bottom:none;
	margin-bottom:25px;
}
.clearfix span{
	float: left;
	line-height: 40px;
}
.clearfix span:first-child{
	line-height: 40px;
}
.login-layout .input-icon{
	margin-left:15px;
	height: 40px;
	width:240px;
}
.login-layout label{
	margin-left:50px;
}
.input-icon.input-icon-right>input , .login-layout label{
	height: 40px;
	margin-bottom:20px;
}
.login-layout .input-icon>[class*="icon-"]{
	line-height: 40px;
}
.position-relative{
	top:50px;
}
</style>

<body class="login-layout">

<!-- <div class="logintop">    
    <span>欢迎后台管理界面平台</span>    
    <ul>
	    <li><a href="#">返回首页</a></li>
	    <li><a href="#">帮助</a></li>
	    <li><a href="#">关于</a></li>
    </ul>    
</div> -->
<div class="loginbody">
	<div class="login-container">
		<div class="center">
			<h1>
				<!-- <i class="icon-leaf green"></i> -->
				<!-- <span class="orange">思美软件</span> -->
				<span class="white">后台管理系统</span>
			</h1>
			<h4 class="white">Background Management System</h4>
		</div>

		<div class="space-6"></div>

		<div class="position-relative">
			<div id="login-box" class="login-box widget-box no-border visible">
				<div class="widget-body">
					<div class="widget-main">
						<h4 class="header blue lighter bigger">
							<!-- <i class="icon-coffee green"></i> -->
							管理员登陆
						</h4>

						<!-- <div class="login_icon"><img src="/Public/admin/images/login.png" /></div> -->

						<form class="/Admin/Login/index" method="POST">
							<fieldset>
								<label class="block clearfix">
									<span>用户名</span>
									<span class="block input-icon input-icon-right">
										<input type="text" class="form-control" placeholder="登录名"  name="username" value="<?php echo ($username); ?>">
										<i class="icon-user"></i>
									</span>
								</label>

								<label class="block clearfix">
									<span>密&nbsp;&nbsp;&nbsp;码</span>
									<span class="block input-icon input-icon-right">
										<input type="password" class="form-control" placeholder="密码" name="password" value="<?php echo ($password); ?>">
										<i class="icon-lock"></i>
									</span>
								</label>

								<div class="space"></div>

								<div class="clearfix">
									<label class="inline">
										<?php if($username != '' and $password != ''): ?><input type="checkbox" class="ace" name="is_savePwd" checked value="1">
										<?php else: ?>
											<input type="checkbox" class="ace" name="is_savePwd" value="1"><?php endif; ?>
										<span class="lbl">保存密码</span>
									</label>

									<button type="submit" class="width-35 pull-right btn btn-sm btn-primary" id="login_btn"><i class="icon-key"></i>
										登陆
									</button>
								</div>

								<div class="space-4"></div>
							</fieldset>
						</form>

						<!-- <div class="social-or-login center">
							<span class="bigger-110">通知</span>
						</div>

						<div class="social-login center">
						本网站系统不再对IE8以下浏览器支持，请见谅。
						</div> -->
					</div><!-- /widget-main -->

					<!-- <div class="toolbar clearfix"></div> -->

				</div><!-- /widget-body -->
			</div><!-- /login-box -->
		</div><!-- /position-relative -->

	</div>
</div>
<!-- <div class="loginbm">版权所有  2016  <a href="">南京思美软件系统有限公司</a> </div><strong></strong> -->

</body>
</html>
<script>
// $('#login_btn').on('click', function(){
// 	var num=0;
// 	var str="";
//     $("input[type$='text']").each(function(n){
// 		if($(this).val()==""){

// 			layer.alert(str+=""+$(this).attr("name")+"不能为空！\r\n",{
// 				title: '提示框',				
// 				icon:0,								
// 			}); 

// 			num++;

// 			return false;            
// 		} 
// 	});

// 	if(num>0){
// 		return false;
// 	}else{
// 		layer.alert('登陆成功！',{
// 			title: '提示框',				
// 			icon:1,		
// 		});
// 		location.href="../Index/index.html";
// 		layer.close(index);	
// 	}		  		     						
		
// })

</script>