<?php if (!defined('THINK_PATH')) exit();?><!-- 登录页 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="/abc/Public/Admin/assets/css/bootstrap.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="/abc/Public/Admin/assets/css/font-awesome.min.css" />
		<!--[if IE 7]>
		  <link rel="stylesheet" href="/abc/Public/Admin/assets/css/font-awesome-ie7.min.css" />
		<![endif]-->
		<link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace.min.css" />
		<link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace-rtl.min.css" />
		<link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace-skins.min.css" />
        <link rel="stylesheet" href="/abc/Public/Admin/css/style.css"/>
		<!--[if lte IE 8]>
		  <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace-ie.min.css" />
		<![endif]-->
		<script src="/abc/Public/Admin/assets/js/ace-extra.min.js"></script>
		<!--[if lt IE 9]>
		<script src="/abc/Public/Admin/assets/js/html5shiv.js"></script>
		<script src="/abc/Public/Admin/assets/js/respond.min.js"></script>
		<![endif]-->
		<script src="/abc/Public/Admin/js/jquery-1.9.1.min.js"></script>        
        <script src="/abc/Public/Admin/assets/layer/layer.js" type="text/javascript"></script>
        <style>
        		#j8_jpg{
        			cursor:pointer;
        		}
        </style>
<title>登陆</title>
</head>

<body class="login-layout Reg_log_style">
<div class="logintop">    
    <span>欢迎后台管理界面平台</span>    
    <ul>
    <li><a href="">返回商城首页</a></li>
    <li><a href="#">帮助</a></li>
    <li><a href="#">关于</a></li>
    </ul>    
    </div>
    <div class="loginbody">
<div class="login-container">
	<div class="center">
	     <img src="/abc/Public/Admin/images/logo1.png" />
							</div>

							<div class="space-6"></div>

							<div class="position-relative">
								<div id="login-box" class="login-box widget-box no-border visible">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header blue lighter bigger">
												<i class="icon-coffee green"></i>
												管理员登陆 
											</h4><i class="frame_style" style="color:#f00;">360安全守护</i>

											<div class="login_icon"><img src="/abc/Public/Admin/images/login.png" /></div>

											<form method="post" action="<?php echo U('Login/login');?>">
												<fieldset>
										<ul>
   <li class="frame_style form_error"><label class="user_icon"></label>
   <input name="namejj" type="text" autocomplete="off" value="<?php echo ($adname); ?>"/><i>　　　　　　　　　　用户名</i></li>
   <li class="frame_style form_error"><label class="password_icon"></label>
   <input name="pwdjj" type="password"   autocomplete="off" value="<?php echo ($adpwd); ?>"/><i>　　　　　　　　　　密码</i></li>
   <li class="frame_style form_error"><label class="Codes_icon"></label>
   <input name="yzm" type="text"  style="width:170px;"  autocomplete="off" placeholder='验证码'/><i></i><div class="Codes_region" ><img src="<?php echo U('yzm');?>" id='j8_jpg'/></div></li>
   
  </ul>
													<div class="space"></div>

													<div class="clearfix">
														<label class="inline">
															<input type="checkbox" class="ace" name="jizhu" value="1">
															<span class="lbl">保存密码</span>
														</label>

														<button class="width-35 pull-right btn btn-sm btn-primary" >
															<i class="icon-key"></i>
															登陆
														</button>
													</div>

													<div class="space-4"></div>
												</fieldset>
											</form>

											<div class="social-or-login center">
												<span class="bigger-110">通知</span>
											</div>

											<div class="social-login center">
											本网站系统不再对IE8以下浏览器支持，请见谅。
											</div>
										</div><!-- /widget-main -->

										<div class="toolbar clearfix">
											

											
										</div>
									</div><!-- /widget-body -->
								</div><!-- /login-box -->
							</div><!-- /position-relative -->
						</div>
                        </div>
                        <div class="loginbm">版权所有  2016  <a href="">本站最终解释权归骚龙所有</a> </div><strong></strong>
</body>
</html>
<script>
		var j8jpg = document.getElementById('j8_jpg');
		
		j8jpg.onclick = function(){
				$.ajax({
					type:'post',
					url:"<?php echo U('Login/yzm');?>",
					success:function(img){
					j8jpg.src = "<?php echo U('yzm');?>";
					}
				})
		}
/************* ***--- --** *******************/
	


</script>