<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<title>发送重置密码邮件</title>
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
		.res-banner{height:420px;}
		/*登录框的高度*/
		.login-box{margin-top:48px;height:300px;}
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


		/*邮箱注册验证码样式*/
		#code_img{vertical-align:top;cursor:pointer;}

		/*手机号注册验证码样式*/
		#codeimg{vertical-align:top;cursor:pointer;}

		/*底部与登录框的上边距*/
		.footer{margin-top: 0px;}
	</style>
</head>
<body>
	<div class="login-boxtitle">
		<a href="<?php echo U('Index/index');?>" title="去往零食商城首页"><img alt="" src="/Public/Home/images/logobig.png" /></a>
		<div style="float:right;line-height:60px;"><a href="<?php echo U('Login/login');?>">返回登录</a></div>
	</div>
	<div class="res-banner">
		<div class="res-main">
			<div class="login-banner-bg"><span></span><img src="/Public/Home/images/big.jpg" /></div>
			<div class="login-box">
				<div class="am-tabs" id="doc-my-tabs">
					<ul class="am-tabs-nav am-nav am-nav-tabs am-nav-justify">
						<li class="am-active"><a href="">发送重置密码邮件</a></li>
					</ul>
				<div class="am-tabs-bd">
					<!-- 发送重置密码邮件 -->
					<div class="am-tab-panel am-active">
						<form method="post" action="<?php echo U('Login/forgetPassword');?>" id="emailform">							
                 			<div class="user-email">
								<label for="email"><i class="am-icon-envelope-o"></i></label>
								<input class="email-email" type="email" name="email" id="email" placeholder="请输入邮箱账号">
             				</div>	
             				<div class="user-phone">
							    <label for="code"><i class="am-icon-code-fork"></i></label>
							    <input class="email-code" type="text" name="emailcode" id="email-code" placeholder="请输入验证码" maxlength="4" autocomplete="off" style="width:178px;"><img title="换一张" src="<?php echo U('code');?>" id="code_img"/>
         					</div>	
							<div class="am-cf">
								<input type="submit" name="" id="email-sub" value="提交" class="am-btn am-btn-primary am-btn-sm am-fl">
							</div>
						</form>						
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



    // 当点击输入框时显示提示信息
    $('form :input').click(function() {

    	

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
    				type : 'post',
    				url  : "<?php echo U('Login/activationMailJudge');?>",
    				data : {email:email},
    				success: function(data) {
    					console.log(data);
    					if (data == '-1') {
    						
    						// 将Input框的颜色设置为红色
			    			$('#email').css('border-color', 'red');

			    			// 添加错误按钮
			    			$('<i></i>')
			    			.addClass('item-error')
			    			.appendTo($listItem);

			    			// 设置错误信息
    						var errorMessage = '该邮箱尚未注册,请先注册';
    						$('<span></span>')
    						.addClass('error-message')
    						.text(errorMessage)
    						.appendTo($listItem);

    						// 阻止表单提交
							return false;

    					} else {
    						
					    	// 发起ajax + 判断
			    			
							// 获取邮箱输入框的值
			    			var email = $('.email-email').val();
			    			$.ajax({
			    				type : 'get',
			    				url  : "<?php echo U('Login/activationMailJudge');?>",
			    				data : {email:email},
			    				success: function(data) {
			    					console.log(data);
			    					if (data == '-1') {
			    						// 将Input框的颜色设置为红色
						    			$('#email').css('border-color', 'red');

						    			// 添加错误按钮
						    			$('<i></i>')
						    			.addClass('item-error')
						    			.appendTo($listItem);

						    			// 设置错误信息
			    						var errorMessage = '该邮箱还未激活,请先激活';
			    						$('<span></span>')
			    						.addClass('error-message')
			    						.text(errorMessage)
			    						.appendTo($listItem);

			    						// 阻止表单提交
										return false;

			    					} else {
			    						// 将Input框的颜色设置为白色
						    			$('#email').css('border-color', '#fff');

						    			// 添加错误按钮
						    			$('<i></i>')
						    			.addClass('item-succ')
						    			.appendTo($listItem);

			    					}
			    				},
			    			});
    					}
    				},
    			});

    		}
    	};

    	// 如果是重置密码的验证码输入框
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
    			
    			// 获取注册验证码输入框的值
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

    });


	// 设置一个空的定时器
	let timer=null;
	// 如果用户点击提交的按钮(防止用户重复点击提交)
	$('#email-sub').click(function() {

		// 清除定时器
		clearTimeout(timer);
		//如果用户点击立即注册按钮的速度太快，小于0.5s就不会提交form表单到后台，但是最后还是会提交一次form表单到后台
	    timer = setTimeout(function(){

			// 当点击立即注册按钮时,使邮箱注册的表单上的所有input框失去焦点
			$("#emailform :input").trigger("blur");

		},500)


    	// 分别获取上面2个输入框的值
    	var emailemail = $('.email-email').val();
    	var emailcode = $('#email-code').val();

    	// 如果所有input框的值输入正确才给提交按钮,否则有一个input框的值不符合要求就不给提交
    	if (emailemail && emailcode) {
    		return true;
		    $("#email-sub").removeAttr("disabled");

    	} else {
    		return false;
    		$("#email-sub").attr("disabled", true);
    	}
	
	});
	


})
</script>
</html>