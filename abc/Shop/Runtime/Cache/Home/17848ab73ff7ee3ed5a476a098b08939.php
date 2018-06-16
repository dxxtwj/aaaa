<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<title>欢迎登录</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="renderer" content="webkit">
	<meta http-equiv="Cache-Control" content="no-siteapp" />
	<link rel="stylesheet" href="/Public/Home/AmazeUI-2.4.2/assets/css/amazeui.css" />
	<link href="/Public/Home/css/dlstyle.css" rel="stylesheet" type="text/css">
	<script src="/Public/Home/AmazeUI-2.4.2/assets/js/jquery.min.js"></script>
	<script src="/Public/Home/AmazeUI-2.4.2/assets/js/amazeui.min.js"></script>
	<style>
		/*登录页面粉红色背景色*/
		.login-banner, .res-banner{height:455px;}

		/*登录输入框之间的间距*/
		.user-name,.user-pass{margin-bottom: 23px;}

		/*设置输入框的提示信息样式*/
		.prompt-message{color: #C5C5C5;font-size: 12px;}

		/*错误图标样式*/
		.item-error{
			float:left;
			position:relative;
			top:4px;
			color:#fc4343;
			height:16px;
			line-height:14px;
			padding-left:20px;
			background:url("/Public/Images/Login/err_small.png") 0 0 no-repeat;
		}

		/*错误提示信息类样式*/
		.error-message {color:red;}
		
		/*重发激活邮件样式*/
		.retransmission-activation-mail{color:#9B9B9B;font-size:14px;}
	</style>
</head>
<body>
	<div class="login-boxtitle">
		<a href="<?php echo U('Index/index');?>" title="去往零食商城首页"><img alt="logo" src="/Public/Home/images/logobig.png" /></a>
	</div>

	<div class="login-banner">
		<div class="login-main">
			<div class="login-banner-bg"><span></span><img src="/Public/Home/images/big.jpg" /></div>
			<div class="login-box">
				<h3 class="title">登录商城</h3>

				<div class="clear"></div>
					
				<div class="login-form">
					<form method="post" action="<?php echo U('Login/login');?>" id="form">
						<div class="user-name">
						    <label for="user"><i class="am-icon-user"></i></label>
						    <input type="text" name="username" class="user" id="user" placeholder="邮箱/手机/用户名">
	                 	</div>
	                	<div class="user-pass">
							<label for="password"><i class="am-icon-lock"></i></label>
							<input type="password" name="password" id="password" placeholder="请输入密码">
	                	</div>
	            </div>
        
	            <div class="login-links">
	                <label for="remember-me" title="为了确保你的信息安全，不建议在网吧等公共环境勾选此项"><input id="remember-me" type="checkbox">记住密码</label>
					<a href="<?php echo U('Login/forgetPassword');?>" class="am-fr" title="通过邮箱重置密码">忘记密码</a>
					<a href="<?php echo U('Login/emailRegister');?>" class="zcnext am-fr am-btn-default">注册</a>
					<br />
	            </div>
				<div class="am-cf">
					<input type="submit" name="" value="登 录" id="login" class="am-btn am-btn-primary am-btn-sm">
				</div>
				</form>
				<div class="partner">		
						<h3>合作账号</h3>
					<div class="am-btn-group">

					<?php if (!isset($_COOKIE['qq_accesstoken']) || !isset($_COOKIE['qq_openid'])) { ?>
						<li><a href="../../../../qqlogin.php"><i class="am-icon-qq am-icon-sm"></i><span>QQ登录</span></a></li>
					<?php }else { ?>
						<li><a href="#"><i class="am-icon-qq am-icon-sm"></i><span>已登录</span></a></li>
					<?php }?>
					</div>
				</div>	
				<center class="retransmission-activation-mail" title="重发获取激活邮件"><a href="<?php echo U('Login/activationMail');?>">重发获取激活邮件</a></center>
			</div>
		</div>
	</div>


	<div class="footer">
		<div class="footer-hd ">
			<p>
				<a href="# ">恒望科技</a>
				<b>|</b>
				<a href="# ">商城首页</a>
				<b>|</b>
				<a href="# ">支付宝</a>
				<b>|</b>
				<a href="# ">物流</a>
			</p>
		</div>
		<div class="footer-bd ">
			<p>
				<a href="# ">关于恒望</a>
				<a href="# ">合作伙伴</a>
				<a href="# ">联系我们</a>
				<a href="# ">网站地图</a>
				<em>© 2015-2025 Hengwang.com 版权所有. 更多模板 <a href="http://www.cssmoban.com/" target="_blank" title="模板之家">模板之家</a> - Collect from <a href="http://www.cssmoban.com/" title="网页模板" target="_blank">网页模板</a></em>
			</p>
		</div>
	</div>


</body>
<script>

$(function() {

	// 当点击输入框时显示提示信息
    $('form :input').click(function() {

    	// 如果是登录用户名输入框
    	if ($(this).is('#user')) {
    		// nextAll,查找当前元素之后所有的同辈元素。
			$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		// 设置输入的提示信息
			var promptMessage = "请输入账号";
			$('<span></span>')
			.addClass('prompt-message')
			.text(promptMessage)
			.appendTo($listItem);
    	}

    	// 如果是登录密码输入框
		if ($(this).is('#password')) {
			// nextAll,查找当前元素之后所有的同辈元素。
			$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		// 设置输入的提示信息
			var promptMessage = "请输入账号密码";
			$('<span></span>')
			.addClass('prompt-message')
			.text(promptMessage)
			.appendTo($listItem);
    	}  

    });

     // 当输入框失去焦点的时候触发
    $('form :input').blur(function() {

    	// 如果是登录用户名输入框
    	if ($(this).is('#user')) {
    		// nextAll()删除所选输入框的同辈元素 
	    	$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');

    		// 当用户没有在登录用户名输入框输入的时候
    		if (this.value == '') {
    			// 将Input框的颜色设置为红色
    			$('#user').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('item-error')
    			.appendTo($listItem);

    			// 设置错误信息
    			var errorMessage = '请输入账号名';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);

    			// 阻止表单提交
				return false;

				// 当用户有在登录账号输入框输入,但是用户输入非法字符的时候
    		}  else if (this.value != '' && !/^[a-zA-Z0-9\u4E00-\u9FFF]{1,}$/.test(this.value)) {
    			// 将Input框的颜色设置为红色
    			$('#user').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('item-error')
    			.appendTo($listItem);

    			// 设置错误信息
    			var errorMessage = '格式错误，仅支持汉字、字母、数字组合';
		        $('<span></span>')
		          .addClass('error-message')
		          .css("font-size", "15px")
		          .text(errorMessage)
		          .appendTo($listItem);

		        // 阻止表单提交
				return false;

			} else {
    			// 发起ajax + 判断
    			
				// 获取登录账号名输入框的值
    			var username = $('.user').val();

    			$.ajax({
    				type : 'post',
    				url  : "<?php echo U('Login/loginJudge');?>",
    				data : {username:username},
    				success: function(data) {
    					console.log(data);
    					if (data == '1') {
    						// 将Input框的颜色设置为白色
    						$('#user').css('border-color', '#fff');

			    			// 允许表单提交
							return true;

    					} else {
    						// 将Input框的颜色设置为红色
			    			$('#user').css('border-color', 'red');

			    			// 添加错误按钮
			    			$('<i></i>')
			    			.addClass('item-error')
			    			.appendTo($listItem);

			    			// 设置错误信息
    						var errorMessage = '账户名不存在，请重新输入';
    						$('<span></span>')
    						.addClass('error-message')
    						.text(errorMessage)
    						.appendTo($listItem);

    						// 阻止表单提交
							return false;
    					}
    				},
    			});
    		}
    	};

    	// 如果是登录密码输入框
    	if ($(this).is('#password')) {
    		// nextAll()删除所选输入框的同辈元素 
	    	$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		if (this.value == '') {
    			// 将Input框的颜色设置为红色
    			$('#password').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('item-error')
    			.appendTo($listItem);

    			// 设置错误信息
    			var errorMessage = '请输入密码';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);

    			// 阻止表单提交
				return false;

    		} else {
				// 将Input框的颜色设置为白色
    			$('#password').css('border-color', '#fff');

    			// 允许表单提交
				return true;
			}
    	};
    });

    // 设置一个空的定时器
	let timer=null;
	// 如果用户点击登录的按钮(防止用户重复点击提交)
	$('#login').click(function() {

		// 清除定时器
		clearTimeout(timer);
		//如果用户点击登录按钮的速度太快，小于0.5s就不会提交form表单到后台，但是最后还是会提交一次form表单到后台
	    timer = setTimeout(function(){

			// 当点击立即注册按钮时,使登录的表单上的所有input框失去焦点
			$("#form :input").trigger("blur");

		},500)


    	// 分别获取上面5个输入框的值
    	var user = $('#user').val();
    	var password = $('#password').val();

    	// 如果所有input框的值输入正确才给提交立即注册按钮,否则有一个input框的值不符合要求就不给提交
    	if (user && password) {

    		return true;
		    $("#login").removeAttr("disabled");
		    // 找到登录按钮的input标签
	        // var login = $('#login');
	        // login.value = '登录中.....';

    	} else {
    		return false;
    		$("#login").attr("disabled", true);
    	}
	
	});
})
</script>	
</html>