<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<title>个人注册</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="renderer" content="webkit">
	<meta http-equiv="Cache-Control" content="no-siteapp" />
	<link rel="stylesheet" href="/Public/Home/AmazeUI-2.4.2/assets/css/amazeui.min.css" />
	<link href="/Public/Home/css/dlstyle.css" rel="stylesheet" type="text/css">
	<script src="/Public/Home/AmazeUI-2.4.2/assets/js/jquery.min.js"></script>
	<script src="/Public/Home/AmazeUI-2.4.2/assets/js/amazeui.min.js"></script>
	<style>
		body{height:720px;}
		/*注册页面粉红色背景色*/
		.res-banner{height:582px;}
		/*登录框的高度*/
		.login-box{margin-top:9px;height:560px;}
		/*注册输入框之间的间距*/
		.user-name,.user-pass,.user-email,.user-phone,.verification{margin-bottom: 23px;}

		/*设置输入框的提示信息样式*/
		.prompt-message{color: #C5C5C5;font-size: 12px;}

		/*设置输入框正确提示信息*/
		.right-message {color:green;}

		/*错误提示信息类样式*/
		.error-message {color:red;}
		
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

		/*正确图标样式*/
		.item-succ{
			float:left;
			position:relative;
			top:5px;
			color:#fc4343;
			height:16px;
			line-height:12px;
			padding-left:20px;
			background:url("/Public/Images/Login/reg_icons.png") -80px 0 no-repeat;
		}

		/*密码强度弱的样式*/
		.i-pwd-weak{
			display:inline-block;
			width:16px;
			height:16px;
			vertical-align:text-top;
			margin-right:4px;
			background:url("/Public/Images/Login/icon.png") -17px -133px no-repeat;
			font-style:normal;
		}

		/*密码强度中的样式*/
		.i-pwd-medium{
			display:inline-block;
			width:16px;
			height:16px;
			vertical-align:text-top;
			margin-right:4px;
			background:url("/Public/Images/Login/icon.png") -34px -117px no-repeat;
			font-style:normal;
		}

		/*密码强度强的样式*/
		.i-pwd-strong{
			display:inline-block;
			width:16px;
			height:16px;
			vertical-align:text-top;
			margin-right:4px;
			background:url("/Public/Images/Login/icon.png") -34px -134px no-repeat;
			font-style:normal;
		}

		/*邮箱注册验证码样式*/
		#code_img{vertical-align:top;cursor:pointer;}

		/*手机号注册验证码样式*/
		#codeimg{vertical-align:top;cursor:pointer;}

		/*底部与登录框的上边距*/
		.footer{margin-top: 100px;}
	</style>
</head>
<body>
	<div class="login-boxtitle">
		<a href="<?php echo U('Index/index');?>" title="去往零食商城首页"><img alt="" src="/Public/Home/images/logobig.png" /></a>
		<div style="float:right;line-height:60px;">已有账号 ? <a href="<?php echo U('Login/login');?>">请登录</a></div>
	</div>
	<div class="res-banner">
		<div class="res-main">
			<div class="login-banner-bg"><span></span><img src="/Public/Home/images/big.jpg" /></div>
			<div class="login-box">
				<div class="am-tabs" id="doc-my-tabs">
					<ul class="am-tabs-nav am-nav am-nav-tabs am-nav-justify">
						<li class="am-active"><a href="">邮箱注册</a></li>
						<li><a href="">手机号注册</a></li>
					</ul>
				<div class="am-tabs-bd">
					<!-- 邮箱账号注册 -->
					<div class="am-tab-panel am-active">
						<form method="post" action="<?php echo U('Login/emailRegister');?>" id="emailform">
							<div class="user-name">
							    <label for="username"><i class="am-icon-user "></i></label>
							    <input class="email-username" type="username" name="username" id="email-username" placeholder="请输入用户名" maxlength="20">
         					</div>
                 			<div class="user-pass">
								<label for="password"><i class="am-icon-lock"></i></label>
								<input class="email-password" type="password" name="password" id="email-password" placeholder="建议至少使用两种字符组合" maxlength="20">
                 			</div>										
                 			<div class="user-pass">
								<label for="passwordRepeat"><i class="am-icon-lock"></i></label>
								<input class="email-passwordRepeat" type="password" name="passwordRepeat" id="email-passwordRepeat" placeholder="确认密码" maxlength="20">
                 			</div>	
                 			<div class="user-email">
								<label for="email"><i class="am-icon-envelope-o"></i></label>
								<input class="email-email" type="email" name="email" id="email" placeholder="请输入邮箱账号">
             				</div>	
             				<div class="user-phone">
							    <label for="code"><i class="am-icon-code-fork"></i></label>
							    <input class="email-code" type="text" name="emailcode" id="email-code" placeholder="请输入验证码" maxlength="4" autocomplete="off" style="width:178px;"><img title="换一张" src="<?php echo U('code');?>" id="code_img"/>
         					</div>	
							<div class="am-cf">
								<input type="submit" name="" id="email-sub" value="立即注册" class="am-btn am-btn-primary am-btn-sm am-fl">
							</div>
						</form>
						<div class="login-links">
							<label for="email-agree">
								<input id="email-agree" type="checkbox"> 点击表示您同意商城<a href="#">《服务协议》</a>
							</label>
						</div>
					</div>
	
					<!-- 手机号注册 -->
					<div class="am-tab-panel">
						<form method="post" action="<?php echo U('Login/phoneRegister');?>">
							<div class="user-name">
							    <label for="username"><i class="am-icon-user "></i></label>
							    <input class="phone-username" type="username" name="username" id="phone-username" placeholder="请输入用户名" maxlength="20">
         					</div>
         					<div class="user-pass">
								<label for="password"><i class="am-icon-lock"></i></label>
								<input class="phone-password" type="password" name="password" id="phone-password" placeholder="建议至少使用两种字符组合" maxlength="20">
		                    </div>										
		                    <div class="user-pass">
								<label for="passwordRepeat"><i class="am-icon-lock"></i></label>
								<input class="phone-passwordRepeat" type="password" name="" id="passwordRepeat" placeholder="确认密码" maxlength="20">
		                    </div>	
         					<div class="user-phone">
							    <label for="phone"><i class="am-icon-mobile-phone am-icon-md"></i></label>
							    <input  class="phone" type="number" name="" id="phone" placeholder="请输入手机号" maxlength="11">
         					</div>
         					<div class="user-phone">
							    <label for="code"><i class="am-icon-code-fork"></i></label>
							    <input class="phone-code" type="text" name="phonecode" id="phone-code" placeholder="请输入验证码" maxlength="4" autocomplete="off" style="width:178px;"><img title="换一张" src="<?php echo U('code');?>" id="codeimg"/>
         					</div>														
							<div class="verification">
								<label for="phonecode"><i class="am-icon-envelope"></i></label>
								<input type="tel" name="" id="phonecode" placeholder="请输入手机短信验证码">
								<a class="btn" href="javascript:void(0);" onclick="sendMobileCode();" id="sendMobileCode">
								<span id="dyMobileButton">获取</span></a>
							</div>
						
						<div class="am-cf">
							<input type="submit" name="" id="phone-sub" value="立即注册" class="am-btn am-btn-primary am-btn-sm am-fl">
						</div>
						</form>
						<div class="login-links">
							<label for="phone-agree">
								<input id="phone-agree" type="checkbox"> 点击表示您同意商城<a href="#">《服务协议》</a>
							</label>
					  	</div>
						<!-- <hr> -->
					</div>
				</div>
			</div>
		</div>
	</div>

		
	<div class="footer ">
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
	// 切换邮箱账号注册和手机号码注册
    $('#doc-my-tabs').tabs();

    // 找邮箱注册的验证码对象
	var code_img = document.getElementById('code_img');
	// 邮箱注册时验证码的点击事件 
	code_img.onclick = function(){
		// 更换验证码
		$.ajax({
			type:'post',
			url:"<?php echo U('Login/code');?>",
			success:function(img){
			code_img.src = "<?php echo U('code');?>";
			}
		})

		// 更新邮箱注册验证码,清空输入框的值
		$("input[name=emailcode]").focus().val("");
	}

	// 找手机号码注册的验证码对象
	var codeimg = document.getElementById('codeimg');
	// 手机号码注册时验证码的点击事件 
	codeimg.onclick = function(){
		$.ajax({
			type:'post',
			url:"<?php echo U('Login/code');?>",
			success:function(img){
			codeimg.src = "<?php echo U('code');?>";
			}
		})

		// 更新手机号注册验证码,清空输入框的值
		$("input[name=phonecode]").focus().val("");
	}


    // 当点击输入框时显示提示信息
    $('form :input').click(function() {

    	// 如果是邮箱注册用户名输入框
    	if ($(this).is('#email-username')) {
    		// nextAll,查找当前元素之后所有的同辈元素。
			$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		// 设置输入的提示信息
			var promptMessage = "支持中文、字母、数字的组合，4-20个字符";
			$('<span></span>')
			.addClass('prompt-message')
			.text(promptMessage)
			.appendTo($listItem);
    	}

    	// 如果是邮箱注册设置密码输入框
		if ($(this).is('#email-password')) {
			// nextAll,查找当前元素之后所有的同辈元素。
			$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		// 设置输入的提示信息
			var promptMessage = "建议使用字母、数字和符号两种及以上组合,6-20个字符";
			$('<span></span>')
			.addClass('prompt-message')
			.text(promptMessage)
			.appendTo($listItem);
    	}  

    	// 如果是邮箱注册确认密码输入框
		if ($(this).is('#email-passwordRepeat')) {
			// nextAll,查找当前元素之后所有的同辈元素。
			$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		// 设置输入的提示信息
			var promptMessage = "请再次输入密码";
			$('<span></span>')
			.addClass('prompt-message')
			.text(promptMessage)
			.appendTo($listItem);
    	} 

    	// 如果是邮箱输入框
		if ($(this).is('#email')) {
			// nextAll,查找当前元素之后所有的同辈元素。
			$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		// 设置输入的提示信息
			var promptMessage = "完成验证后,你可以用该邮箱登录和找回密码";
			$('<span></span>')
			.addClass('prompt-message')
			.text(promptMessage)
			.appendTo($listItem);
    	} 


    	// 如果是手机号注册用户名输入框
    	if ($(this).is('#phone-username')) {
    		// nextAll,查找当前元素之后所有的同辈元素。
			$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		// 设置输入的提示信息
			var promptMessage = "支持中文、字母、数字的组合，4-20个字符";
			$('<span></span>')
			.addClass('prompt-message')
			.text(promptMessage)
			.appendTo($listItem);
    	}

    	// 如果是手机号注册设置密码输入框
		if ($(this).is('#phone-password')) {
			// nextAll,查找当前元素之后所有的同辈元素。
			$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		// 设置输入的提示信息
			var promptMessage = "建议使用字母、数字和符号两种及以上组合,6-20个字符";
			$('<span></span>')
			.addClass('prompt-message')
			.text(promptMessage)
			.appendTo($listItem);
    	}  
    	
    	// 如果是手机号注册确认密码输入框
		if ($(this).is('.phone-passwordRepeat')) {
			// nextAll,查找当前元素之后所有的同辈元素。
			$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		// 设置输入的提示信息
			var promptMessage = "请再次输入密码";
			$('<span></span>')
			.addClass('prompt-message')
			.text(promptMessage)
			.appendTo($listItem);
    	}    	

    	// 如果是手机号码输入框
		if ($(this).is('#phone')) {
			// nextAll,查找当前元素之后所有的同辈元素。
			$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		// 设置输入的提示信息
			var promptMessage = "完成验证后,你可以用该手机登录和找回密码";
			$('<span></span>')
			.addClass('prompt-message')
			.text(promptMessage)
			.appendTo($listItem);
    	}  

    	// 如果是手机验证码输入框
		if ($(this).is('#phonecode')) {
			// 找到手机验证码btn按钮后面的span标签删掉
			$('.btn').nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		// 设置输入的提示信息
			var promptMessage = "请输入手机短信验证码";
			$('<span></span>')
			.addClass('prompt-message')
			.text(promptMessage)
			.appendTo($listItem);
    	}  

    	// 如果是邮箱验证码输入框
		if ($(this).is('#email-code')) {
			// 将图片验证码后的span标签删除掉
			$('img').nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		// 设置输入的提示信息
			var promptMessage = "看不清?点击图片更换验证码";
			$('<span></span>')
			.addClass('prompt-message')
			.text(promptMessage)
			.appendTo($listItem);
    	}  	     	     	     	  	
    });


    // 当输入框失去焦点的时候触发
    $('form :input').blur(function() {

    	// 如果是邮箱注册用户名输入框
    	if ($(this).is('#email-username')) {
    		// nextAll()删除所选输入框的同辈元素 
	    	$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');

    		// 当用户没有在邮箱用户名输入框输入的时候
    		if (this.value == '') {
    			// 将Input框的颜色设置为红色
    			$('#email-username').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('item-error')
    			.appendTo($listItem);

    			// 设置错误信息
    			var errorMessage = '请输入用户名';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);

    			// 阻止表单提交
				return false;

    		// 当用户有在邮箱用户名输入框输入,但是用户名输入非法字符的时候
    		} else if (this.value != '' && !/^[a-zA-Z0-9\u4E00-\u9FFF]{1,}$/.test(this.value)) {
    			// 将Input框的颜色设置为红色
    			$('#email-username').css('border-color', 'red');

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

		    // 当用户有在邮箱用户名输入框输入,并且用户没有输入非法字符,但是用户名长度小于4位的时候     
			} else if (this.value != '' && this.value == '习近平') {
				// 将Input框的颜色设置为红色
    			$('#email-username').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('item-error')
    			.appendTo($listItem);

    			// 设置错误信息
    			var errorMessage = '您的用户名不可用';
		        $('<span></span>')
		          .addClass('error-message')
		          .css("font-size", "15px")
		          .text(errorMessage)
		          .appendTo($listItem);

		        // 阻止表单提交
				return false;
				
			} else if (this.value != '' && !/^[a-zA-Z0-9\u4E00-\u9FFF]{4,20}$/.test(this.value)) {
				// 将Input框的颜色设置为红色
    			$('#email-username').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('item-error')
    			.appendTo($listItem);

    			// 设置错误信息
				var errorMessage = '用户名长度只能在4-20个字符之间';
		        $('<span></span>')
		          .addClass('error-message')
		          .text(errorMessage)
		          .appendTo($listItem);

		        // 阻止表单提交
				return false;

		    // 当用户有在邮箱用户名输入框输入,并且用户没有输入非法字符,而且用户名长度不小于4位,但是输入的用户名为纯数字的时候 
			} else if (this.value != '' && /^[0-9]{1,}$/.test(this.value)) {
				// 将Input框的颜色设置为红色
    			$('#email-username').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('item-error')
    			.appendTo($listItem);

    			// 设置错误信息
				var errorMessage = '用户名不能是纯数字，请重新输入！';
		        $('<span></span>')
		          .addClass('error-message')
		          .text(errorMessage)
		          .appendTo($listItem);

		        // 阻止表单提交
				return false;

		    // 当用户有在邮箱用户名输入框输入,并且用户名通过各种验证的时候,就去查询数据库该用户名是否已被注册       
			} else {
    			// 发起ajax + 判断
    			
				// 获取邮箱注册用户名输入框的值
    			var username = $('.email-username').val();

    			$.ajax({
    				type : 'post',	   
    				url  : "<?php echo U('Login/emailRegisterJudge');?>",
    				data : {username:username},
    				success: function(data) {
    					console.log(data);
    					if (data == '-1') {
    						// 将Input框的颜色设置为白色
    						$('#email-username').css('border-color', '#fff');

    						// 添加正确按钮
    						$('<i></i>')
			    			.addClass('item-succ')
			    			.appendTo($listItem);

			    			// 允许表单提交
							return true;
    					} else {
    						// 将Input框的颜色设置为红色
			    			$('#email-username').css('border-color', 'red');

			    			// 添加错误按钮
			    			$('<i></i>')
			    			.addClass('item-error')
			    			.appendTo($listItem);

			    			// 设置错误信息
    						var errorMessage = '此用户名太受欢迎,请更换一个';
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

    	// 如果是邮箱注册设置密码输入框
    	if ($(this).is('#email-password')) {
    		// nextAll()删除所选输入框的同辈元素 
	    	$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		if (this.value == '') {
    			// 将Input框的颜色设置为红色
    			$('#email-password').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('item-error')
    			.appendTo($listItem);

    			// 设置错误信息
    			var errorMessage = '请设置密码';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);

    			// 阻止表单提交
				return false;

		    // 当用户有在设置密码输入框输入密码,但是密码长度小于6位的时候
    		} else if (!/[\w\s`~!@#$%^&*()-_+=<>?:"{},.|\/;'[\]]{6,20}/.test(this.value)) {
    			console.log(this.value);
    			// 将Input框的颜色设置为红色
    			$('#email-password').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('item-error')
    			.appendTo($listItem);

    			// 设置错误信息
    			var errorMessage = '密码长度只能在6-20个字符之间';
		        $('<span></span>')
		          .addClass('error-message')
		          .text(errorMessage)
		          .appendTo($listItem);

		        // 阻止表单提交
				return false;

	        // 当用户有在设置密码输入框输入密码,并且密码长度不小于6位,但是密码属于长度在6-10位同种类型的密码,提醒用户密码强度弱
	        // 用户密码强度弱的四种情况,密码为:纯数字/纯字母/纯特殊字符/纯空格
	        
			} else if ( 
				// 如果用户在设置密码框输入的是6-10位纯数字
				/^[\d]{6,10}$/.test(this.value) 

				// 如果用户在设置密码框输入的是6-10位纯字母
				|| /^[a-zA-Z]{6,10}$/.test(this.value) 

				// 如果用户在设置密码框输入的是6-10位纯特殊字符
				|| /^[`~!@#$%^&*()-_+=<>?:"{},.|\/;'[\]]{6,10}$/.test(this.value) 

				// 如果用户在设置密码框输入的是6-10位纯空格
				|| /^[\s]{6,10}$/.test(this.value) )
			{
				// 将Input框的颜色设置为红色
    			$('#email-password').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('i-pwd-weak')
    			.appendTo($listItem);

    			// 设置错误信息
				var errorMessage = '有被盗风险,建议使用字母、数字和符号两种以上组合';
		        $('<span></span>')
		          .addClass('error-message')
		          .text(errorMessage)
		          .css("color", "red")
		          .css("font-size", "12px")
		          .appendTo($listItem);

		        // 阻止表单提交
				return false;

	        // 当用户有在设置密码输入框输入密码,并且密码长度不小于11位,但是密码属于同种类型的密码,提醒用户密码强度中,还可以提升密码强度
			} else if ( 
				// 如果用户在设置密码框输入的是11-20位纯数字
				/^[\d]{11,20}$/.test(this.value) 

				// 如果用户在设置密码框输入的是11-20位纯字母
				|| /^[a-zA-Z]{11,20}$/.test(this.value) 

				// 如果用户在设置密码框输入的是11-20位纯特殊字符
				|| /^[`~!@#$%^&*()-_+=<>?:"{},.|\/;'[\]]{11,20}$/.test(this.value) 

				// 如果用户在设置密码框输入的是11-20位纯空格
				|| /^[\s]{11,20}$/.test(this.value) 


				// 下面是数字+字母、数字+特殊字符、数字+空格、字母+特殊字符、字母+空格、特殊字符+空格共6种组合


				// 如果用户在设置密码框输入的是6-10位数字和字母的组合
				|| /^[\da-zA-Z]{6,10}$/.test(this.value)

				// 如果用户在设置密码框输入的是6-10位数字和特殊字符的组合
				|| /^[`~!@#$%^&*()-_+=<>?:"{},.|\/;'[\]\d]{6,10}$/.test(this.value)

				// 如果用户在设置密码框输入的是6-10位数字和空格的组合
				|| /^[\d\s]{6,10}$/.test(this.value)

				// 如果用户在设置密码框输入的是6-10位字母和特殊字符的组合
				|| /^[`~!@#$%^&*()-_+=<>?:"{},.|\/;'[\]a-zA-Z]{6,10}$/.test(this.value)

				// 如果用户在设置密码框输入的是6-10位字母和空格的组合
				|| /^[a-zA-Z\s]{6,10}$/.test(this.value)

				// 如果用户在设置密码框输入的是6-10位特殊字符和空格的组合
				|| /^[`~!@#$%^&*()-_+=<>?:"{},.|\/;'[\]\s]{6,10}$/.test(this.value)

				) 
			{
				// 将Input框的颜色设置为红色
    			$('#email-password').css('border-color', '#FF9911');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('i-pwd-medium')
    			.appendTo($listItem);

    			// 设置错误信息
				var errorMessage = '安全强度适中，可以使用三种组合来提高安全强度';
		        $('<span></span>')
		          .addClass('error-message')
		          .text(errorMessage)
		          .css({"color":"#FF9911","font-size":"8px"})
		          .appendTo($listItem);

		        // 阻止表单提交
				return false;

			} else {
				// 将Input框的颜色设置为白色
    			$('#email-password').css('border-color', '#fff');

    			// 添加密码强度强按钮
    			$('<i></i>')
    			.addClass('i-pwd-strong')
    			.appendTo($listItem);

    			// 允许表单提交
				return true;

    			// 设置正确信息
				// var rightMessage = '您的密码很安全';
		  //       $('<span></span>')
		  //         .addClass('right-message')
		  //         .text(rightMessage)
		  //         .appendTo($listItem);
			}
    	};

    	// 如果是邮箱注册确认密码输入框
    	if ($(this).is('#email-passwordRepeat')) {
    		// nextAll()删除所选输入框的同辈元素 
	    	$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		if (this.value == '') {
    			// 将Input框的颜色设置为红色
    			$('#email-passwordRepeat').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('item-error')
    			.appendTo($listItem);

    			// 设置错误信息
    			var errorMessage = '请输入确认密码';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);

    			// 阻止表单提交
				return false;

    			// 如果没有输入确认密码
    		} else {
    			console.log(this.value);

    			// 获取邮箱注册输入密码框的值
    			var emailpassword = $("#email-password").val();

    			// 获取邮箱注册确认密码框的值
    			var emailpasswordRepeat= $("#email-passwordRepeat").val();

    			// 如果输入的密码与确认密码相同
    			if (emailpassword == emailpasswordRepeat) {

    				// 将Input框的颜色设置为白色
	    			$('#email-passwordRepeat').css('border-color', '#fff');

	    			// 添加成功按钮
	    			$('<i></i>')
	    			.addClass('item-succ')
	    			.appendTo($listItem);

	    			// 允许表单提交
					return true;

	    		// 如果输入的密码与确认密码不相同
    			} else {

	    			// 将Input框的颜色设置为红色
	    			$('#email-passwordRepeat').css('border-color', 'red');

	    			// 添加错误按钮
	    			$('<i></i>')
	    			.addClass('item-error')
	    			.appendTo($listItem);

	    			// 设置错误信息
	    			var errorMessage = '两次密码不一致';
	    			$('<span></span>')
	    			.addClass('error-message')
	    			.text(errorMessage)
	    			.appendTo($listItem);

	    			// 阻止表单提交
					return false;
    			}   			
    		} 
    	}


	    // 如果是邮箱输入框
    	if ($(this).is('#email')) {
    		// nextAll()删除所选输入框的同辈元素 
	    	$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		// 当用户没有在邮箱输入框输入的时候
    		if (this.value == '') {

    			// 将Input框的颜色设置为红色
    			$('#email').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('item-error')
    			.appendTo($listItem);

    			// 设置错误信息
    			var errorMessage = '请输入邮箱';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);

    			// 阻止表单提交
				return false;

    		// 当用户有在邮箱输入框输入的时候,但是输入错误的邮箱地址
    		} else if (this.value != '' && !/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/.test(this.value)) {

    			// 将Input框的颜色设置为红色
    			$('#email').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('item-error')
    			.appendTo($listItem);

    			// 设置错误信息
		        var errorMessage = '邮箱账号格式不正确';
		        $('<span></span>')
		          .addClass('error-message')
		          .text(errorMessage)
		          .appendTo($listItem);

		        // 阻止表单提交
				return false;
		        // $listItem.addClass('warning');
		        
		    // 当用户有在邮箱输入框输入的时候,并且输入正确的邮箱地址,就去查询数据库邮箱账号是否已经占用     
	      	} else {

    			// 发起ajax + 判断
    			
				// 获取邮箱输入框的值
    			var email = $('.email-email').val();

    			$.ajax({
    				type : 'get',
    				url  : "<?php echo U('Login/emailregisterjudge');?>",
    				data : {email:email},
    				success: function(data) {
    					console.log(data);
    					if (data == '-1') {
    						// 将Input框的颜色设置为白色
			    			$('#email').css('border-color', '#fff');

			    			// 添加正确按钮
			    			$('<i></i>')
			    			.addClass('item-succ')
			    			.appendTo($listItem);

			    			// 允许表单提交
							return true;

    					} else {
    						// 将Input框的颜色设置为红色
			    			$('#email').css('border-color', 'red');

			    			// 添加错误按钮
			    			$('<i></i>')
			    			.addClass('item-error')
			    			.appendTo($listItem);

			    			// 设置错误信息
    						var errorMessage = '该邮箱已被注册';
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

    	// 如果是邮箱注册的验证码输入框
    	if ($(this).is('#email-code')) {
    		// 将图片验证码后的span标签删除掉
			$('img').nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		if (this.value == '') {

    			// 将Input框的颜色设置为红色
    			$('#email-code').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('item-error')
    			.appendTo($listItem);

    			// 设置错误信息
    			var errorMessage = '请输入验证码';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);

    			// 阻止表单提交
				return false;

    		} else {
    			// 发起ajax请求
    			
    			// 获取邮箱注册验证码输入框的值
    			var emailcode = $('.email-code').val();

    			$.ajax({
    				type: 'get',
    				url : "<?php echo U('Login/emailcodeJudge');?>",
    				data: {emailcode:emailcode},
    				success: function(data) {
    					console.log(data);
    					if (data == '1') {

    						// 将Input框的颜色设置为白色
			    			$('#email-code').css('border-color', '#fff');

			    			// 添加正确按钮
			    			$('<i></i>')
			    			.addClass('item-succ')
			    			.appendTo($listItem);

			    			// 允许表单提交
							return true;

			    			// 设置正确信息
    						// var rightMessage = '验证码正确';
    						// $('<span></span>')
    						// .addClass('right-message')
    						// .text(rightMessage)
    						// .appendTo($listItem);
    					} else {

    						// 将Input框的颜色设置为红色
			    			$('#email-code').css('border-color', 'red');

			    			// 添加错误按钮
			    			$('<i></i>')
			    			.addClass('item-error')
			    			.appendTo($listItem);

			    			// 设置错误信息
    						var errorMessage = '验证码不正确,或已过期';
    						$('<span></span>')
    						.addClass('error-message')
    						.text(errorMessage)
    						.appendTo($listItem);

    						// 阻止表单提交
							return false;
    					}
    				}
    			});
    		}
    	}


    	// 如果是手机号注册用户名输入框
    	if ($(this).is('#phone-username')) {
    		// nextAll()删除所选输入框的同辈元素 
	    	$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');

    		// 当用户没有在手机号用户名输入框输入的时候
    		if (this.value == '') {
    			var errorMessage = '*请输入用户名';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);

    		// 当用户有在手机号用户名输入框输入,但是用户名输入非法字符的时候
    		} else if (this.value != '' && !/^[a-zA-Z0-9\u4E00-\u9FFF]{1,}$/.test(this.value)) {
    			var errorMessage = '格式错误，仅支持汉字、字母、数字组合';
		        $('<span></span>')
		          .addClass('error-message')
		          .text(errorMessage)
		          .appendTo($listItem);

		    // 当用户有在手机号用户名输入框输入,并且用户没有输入非法字符,但是用户名长度小于4位的时候     
			} else if (this.value != '' && !/[a-zA-Z0-9\u4E00-\u9FFF]{4,20}/.test(this.value)) {
				var errorMessage = '用户名长度只能在4-20个字符之间';
		        $('<span></span>')
		          .addClass('error-message')
		          .text(errorMessage)
		          .appendTo($listItem);

		    // 当用户有在手机号用户名输入框输入,并且用户没有输入非法字符,而且用户名长度不小于4位,但是输入的用户名为纯数字的时候 
			} else if (this.value != '' && /^[0-9]{1,}$/.test(this.value)) {
				var errorMessage = '用户名不能是纯数字，请重新输入！';
		        $('<span></span>')
		          .addClass('error-message')
		          .text(errorMessage)
		          .appendTo($listItem);

		    // 当用户有在手机号用户名输入框输入,并且用户名通过各种验证的时候,就去查询数据库该用户名是否已被注册       
			} else {
    			// 发起ajax + 判断
    			
				// 获取手机号注册用户名输入框的值
    			var username = $('.phone-username').val();

    			$.ajax({
    				type : 'post',
    				url  : "<?php echo U('Login/phoneRegisterJudge');?>",
    				data : {username:username},
    				success: function(data) {
    					console.log(data);
    					if (data == '-1') {
    						var rightMessage = '该用户名可以注册';
    						$('<span></span>')
    						.addClass('right-message')
    						.text(rightMessage)
    						.appendTo($listItem);
    					} else {
    						var errorMessage = '此用户名太受欢迎,请更换一个';
    						$('<span></span>')
    						.addClass('error-message')
    						.text(errorMessage)
    						.appendTo($listItem);
    					}
    				},
    			});
    		}
    	};

    	// 如果是手机号注册设置密码输入框
    	if ($(this).is('#phone-password')) {
    		// nextAll()删除所选输入框的同辈元素 
	    	$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		if (this.value == '') {
    			var errorMessage = '请设置密码';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);

		    // 当用户有在设置密码输入框输入密码,但是密码长度小于6位的时候
    		} else if (!/[\w\s`~!@#$%^&*()-_+=<>?:"{},.|\/;'[\]]{6,20}/.test(this.value)) {

    			var errorMessage = '密码长度只能在6-20个字符之间';
		        $('<span></span>')
		          .addClass('error-message')
		          .text(errorMessage)
		          .appendTo($listItem);

	        // 当用户有在设置密码输入框输入密码,并且密码长度不小于6位,但是密码属于长度在6-10位同种类型的密码,提醒用户密码强度弱
	        // 用户密码强度弱的四种情况,密码为:纯数字/纯字母/纯特殊字符/纯空格
	        
			} else if ( 
				// 如果用户在设置密码框输入的是6-10位纯数字
				/^[\d]{6,10}$/.test(this.value) 

				// 如果用户在设置密码框输入的是6-10位纯字母
				|| /^[a-zA-Z]{6,10}$/.test(this.value) 

				// 如果用户在设置密码框输入的是6-10位纯特殊字符
				|| /^[`~!@#$%^&*()-_+=<>?:"{},.|\/;'[\]]{6,10}$/.test(this.value) 

				// 如果用户在设置密码框输入的是6-10位纯空格
				|| /^[\s]{6,10}$/.test(this.value) )
			{

				var errorMessage = '有被盗风险,建议使用字母、数字和符号两种及以上组合';
		        $('<span></span>')
		          .addClass('error-message')
		          .text(errorMessage)
		          .css("color", "red")
		          .css("font-size", "12px")
		          .appendTo($listItem);

	        // 当用户有在设置密码输入框输入密码,并且密码长度不小于11位,但是密码属于同种类型的密码,提醒用户密码强度中,还可以提升密码强度
			} else if ( 
				// 如果用户在设置密码框输入的是11-20位纯数字
				/^[\d]{11,20}$/.test(this.value) 

				// 如果用户在设置密码框输入的是11-20位纯字母
				|| /^[a-zA-Z]{11,20}$/.test(this.value) 

				// 如果用户在设置密码框输入的是11-20位纯特殊字符
				|| /^[`~!@#$%^&*()-_+=<>?:"{},.|\/;'[\]]{11,20}$/.test(this.value) 

				// 如果用户在设置密码框输入的是11-20位纯空格
				|| /^[\s]{11,20}$/.test(this.value) 


				// 下面是数字+字母、数字+特殊字符、数字+空格、字母+特殊字符、字母+空格、特殊字符+空格共6种组合


				// 如果用户在设置密码框输入的是6-10位数字和字母的组合
				|| /^[\da-zA-Z]{6,10}$/.test(this.value)

				// 如果用户在设置密码框输入的是6-10位数字和特殊字符的组合
				|| /^[`~!@#$%^&*()-_+=<>?:"{},.|\/;'[\]\d]{6,10}$/.test(this.value)

				// 如果用户在设置密码框输入的是6-10位数字和空格的组合
				|| /^[\d\s]{6,10}$/.test(this.value)

				// 如果用户在设置密码框输入的是6-10位字母和特殊字符的组合
				|| /^[`~!@#$%^&*()-_+=<>?:"{},.|\/;'[\]a-zA-Z]{6,10}$/.test(this.value)

				// 如果用户在设置密码框输入的是6-10位字母和空格的组合
				|| /^[a-zA-Z\s]{6,10}$/.test(this.value)

				// 如果用户在设置密码框输入的是6-10位特殊字符和空格的组合
				|| /^[`~!@#$%^&*()-_+=<>?:"{},.|\/;'[\]\s]{6,10}$/.test(this.value)

				) 
			{

				var errorMessage = '安全强度适中，可以使用三种以上的组合来提高安全强度';
		        $('<span></span>')
		          .addClass('error-message')
		          .text(errorMessage)
		          .css("color", "#FF9911")
		          .appendTo($listItem);

			} else {

				var rightMessage = '您的密码很安全';
		        $('<span></span>')
		          .addClass('right-message')
		          .text(rightMessage)
		          .appendTo($listItem);
			}
    	};

    	// 如果是手机号注册确认密码输入框
    	if ($(this).is('.phone-passwordRepeat')) {
    		// nextAll()删除所选输入框的同辈元素 
	    	$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		if (this.value == '') {
    			var errorMessage = '请输入确认密码';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);
    		} else if (this.value != '#phone-password.value') {
    			var errorMessage = '两次密码不一致';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);
    		}
    	}

    	// 如果是电话输入框
	    if ($(this).is('#phone')) {
	    	// nextAll()删除所选输入框的同辈元素 
	    	$(this).nextAll().remove();

	      	var $listItem = $(this).parents('div:first');
	      	if (this.value == '') {
    			var errorMessage = '*请输入手机号码';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);

    		} else if (this.value != '' && !/^(1[38]\d|14[57]|15[0123]|15[56789]|166|17[3]|17[678]|19[89])\d{8}$/.test(this.value)) {

    			var errorMessage = '手机号码格式不正确';
	        	$('<span></span>')
	          	.addClass('error-message')
	          	.text(errorMessage)
	          	.appendTo($listItem);
	        	// $listItem.addClass('warning');
    		} else {
    			// 发起ajax请求
    			
    			// 获取手机输入框的值
    			var phone = $('.phone').val();

    			$.ajax({
    				type : 'get',
    				url  : "<?php echo U('Login/phoneRegisterJudge');?>",
    				data : {phone:phone},
    				success : function(data) {
    					console.log(data);
    					if (data == '-1') {
    						var rightMessage = '该手机号码可以注册';
    						$('<span></span>')
    						.addClass('right-message')
    						.text(rightMessage)
    						.appendTo($listItem);
    					} else {
    						var errorMessage = '号码已占用，';
    						$('<span></span>')
    						.addClass('error-message')
    						.text(errorMessage)
    						.appendTo($listItem)
    						.after('<a href="#">找回账号？</a>');
    					}
    				}
    			});
    		}
	    };

	    // 如果是手机号注册的验证码输入框
    	if ($(this).is('#phone-code')) {
    		// 将图片验证码后的span标签删除掉
			$('img').nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		if (this.value == '') {
    			var errorMessage = '请输入邮箱';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);
    		} else {
    			// 发起ajax请求
    			
    			// 获取邮箱注册验证码输入框的值
    			var phonecode = $('.phone-code').val();

    			$.ajax({
    				type: 'get',
    				url : "<?php echo U('Login/phonecodeJudge');?>",
    				data: {phonecode:phonecode},
    				success: function(data) {
    					console.log(data);
    					if (data == '1') {
    						var rightMessage = '验证码正确';
    						$('<span></span>')
    						.addClass('right-message')
    						.text(rightMessage)
    						.appendTo($listItem);
    					} else {
    						var errorMessage = '验证码不正确,或已过期';
    						$('<span></span>')
    						.addClass('error-message')
    						.text(errorMessage)
    						.appendTo($listItem);
    					}
    				}
    			});
    		}
    	}

    	// 如果是手机验证码输入框
    	if ($(this).is('#phonecode')) {
    		// 找到手机验证码btn按钮后面的span标签删掉
			$('.btn').nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		if (this.value == '') {
    			var errorMessage = '*请输入手机验证码';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);
    		}
    	}
    });


	// 如果用户没有同意邮箱注册服务协议,就不给用户注册
	if ($('#email-agree').prop("checked") == false) {

		$("#email-sub").css({"background":"#69b3f2","cursor":"not-allowed"});
		$("#email-sub").attr("disabled", true);
	}

	// 当用户点击邮箱注册服务协议复选框时
	$('#email-agree').click(function() {
		// 如果用户选中同意服务协议的复选框,用户就可以点击立即注册按钮
		if ($('#email-agree').prop("checked") == true) {

			$('#email-sub').css({"background":"blue","cursor":"pointer"});
			// 清除禁选
			$("#email-sub").removeAttr("disabled");
		} else {

		// 如果用户没选中同意服务协议的复选框,用户就不可以点击立即注册按钮	
			$("#email-sub").css({"background":"#69b3f2","cursor":"not-allowed"});
			$("#email-sub").attr("disabled", true);
		}
	});


	// 设置一个空的定时器
	let timer=null;
	// 如果用户点击邮箱注册立即注册的按钮(防止用户重复点击提交)
	$('#email-sub').click(function() {

		// 清除定时器
		clearTimeout(timer);
		//如果用户点击立即注册按钮的速度太快，小于0.5s就不会提交form表单到后台，但是最后还是会提交一次form表单到后台
	    timer = setTimeout(function(){

			// 当点击立即注册按钮时,使邮箱注册的表单上的所有input框失去焦点
			$("#emailform :input").trigger("blur");

		},500)


    	// 分别获取上面5个输入框的值
    	var emailusername = $('#email-username').val();
    	var emailpassword = $('#email-password').val();
    	var emailpasswordRepeat = $('#email-passwordRepeat').val();
    	var emailemail = $('.email-email').val();
    	var emailcode = $('#email-code').val();

    	// 如果所有input框的值输入正确才给提交立即注册按钮,否则有一个input框的值不符合要求就不给提交
    	if (emailusername && emailpassword && emailpasswordRepeat && emailemail && emailcode) {

    		return true;
		    $("#email-sub").removeAttr("disabled");

    	} else {
    		return false;
    		$("#email-sub").attr("disabled", true);
    	}
	
	});
	
	

	
	

	// 如果用户没有同意手机号注册服务协议,就不给用户注册
	if ($('#phone-agree').prop("checked") == false) {

		$("#phone-sub").css({"background":"#69b3f2","cursor":"not-allowed"});
		$("#phone-sub").attr("disabled", true);
	}

	// 当用户点击手机号注册服务协议复选框时
	$('#phone-agree').click(function() {
		// 如果用户选中同意服务协议的复选框
		if ($('#phone-agree').prop("checked") == true) {

			$('#phone-sub').css({"background":"blue","cursor":"pointer"});
			$("#phone-sub").removeAttr("disabled");
		} else {

			$("#phone-sub").css({"background":"#69b3f2","cursor":"not-allowed"});
			$("#phone-sub").attr("disabled", true);
		}

	});

})
</script>
</html>