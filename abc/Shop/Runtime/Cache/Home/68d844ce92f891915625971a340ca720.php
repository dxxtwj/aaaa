<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=0">

		<title>修改密码</title>

		<link href="/abc/Public/Home/AmazeUI-2.4.2/assets/css/admin.css" rel="stylesheet" type="text/css">
		<link href="/abc/Public/Home/AmazeUI-2.4.2/assets/css/amazeui.css" rel="stylesheet" type="text/css">

		<link href="/abc/Public/Home/css/personal.css" rel="stylesheet" type="text/css">
		<link href="/abc/Public/Home/css/stepstyle.css" rel="stylesheet" type="text/css">

		<script type="text/javascript" href="/abc/Public/Home/js/jquery-1.7.2.min.js"></script>
		<!--<script src="/abc/Public/Home/AmazeUI-2.4.2/assets/js/amazeui.js"></script>-->
		<script src="/abc/Public/Home/AmazeUI-2.4.2/assets/js/jquery.min.js"></script>
		<script src="/abc/Public/Home/AmazeUI-2.4.2/assets/js/amazeui.min.js"></script>
		<style>
			/*设置输入框的提示信息样式*/
			.prompt-message{color: #C5C5C5;font-size: 16px;}

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
				background:url("/abc/Public/Images/Login/err_small.png") 0 0 no-repeat;
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
				background:url("/abc/Public/Images/Login/reg_icons.png") -80px 0 no-repeat;
			}

			/*密码强度弱的样式*/
			.i-pwd-weak{
				display:inline-block;
				width:16px;
				height:16px;
				vertical-align:text-top;
				margin-right:4px;
				background:url("/abc/Public/Images/Login/icon.png") -17px -133px no-repeat;
				font-style:normal;
			}

			/*密码强度中的样式*/
			.i-pwd-medium{
				display:inline-block;
				width:16px;
				height:16px;
				vertical-align:text-top;
				margin-right:4px;
				background:url("/abc/Public/Images/Login/icon.png") -34px -117px no-repeat;
				font-style:normal;
			}

			/*密码强度强的样式*/
			.i-pwd-strong{
				display:inline-block;
				width:16px;
				height:16px;
				vertical-align:text-top;
				margin-right:4px;
				background:url("/abc/Public/Images/Login/icon.png") -34px -134px no-repeat;
				font-style:normal;
			}
		</style>
	</head>

	<body>
		<!--头 -->
		<header>
			<article>
				<div class="mt-logo">
					<!--顶部导航条 -->
					<div class="am-container header">
							<ul class="message-l">
								<div class="topMessage">
									<div class="menu-hd">
									<?php  if (empty(session('user'))) { echo "<a href=".U('Index/index')." title='点击前往商城首页'>&nbsp;欢迎您来到零食商城,</a>　<a href=".U('Login/login')." title='亲，要登录后才能买东西哦~'>登录</a>　|　<a href=".U('Login/emailRegister')." title='还没账号?点击立即注册'>注册</a>"; } else { echo "<a href=".U('Index/index')." title='点击前往商城首页'>&nbsp;欢迎您,</a><a href=".U('Usercenter/index')." title='点击前往个人中心'>".session('user')['username']."</a> ｜ <a href=".U('Login/logout')." title='点击退出登录'>注销</a>"; } ?>
									</div>
								</div>
							</ul>
							<ul class="message-r">
								<div class="topMessage home">
									<div class="menu-hd"><a href="<?php echo U('Index/index');?>" target="_top" class="h">商城首页</a></div>
								</div>
								<div class="topMessage my-shangcheng">
									<div class="menu-hd MyShangcheng"><a href="<?php echo U('Usercenter/index');?>" target="_top"><i class="am-icon-user am-icon-fw"></i>个人中心</a></div>
								</div>
								<div class="topMessage mini-cart">
									<div class="menu-hd"><a id="mc-menu-hd" href="<?php echo U('Shopcart/index');?>" target="_top"><i class="am-icon-shopping-cart  am-icon-fw"></i><span>购物车</span><strong id="J_MiniCartNum" class="h">0</strong></a></div>
								</div>
								<div class="topMessage favorite">
									<div class="menu-hd"><a href="#" target="_top"><i class="am-icon-heart am-icon-fw"></i><span>收藏夹</span></a></div>
							</ul>
						</div>

						<!--悬浮搜索框-->

						<div class="nav white">
							<div class="logoBig">
								<li><img src="/abc/Public/Home/images/logobig.png" /></li>
							</div>

							<div class="search-bar pr">
								<a name="index_none_header_sysc" href="#"></a>
								<form>
									<input id="searchInput" name="index_none_header_sysc" type="text" placeholder="搜索" autocomplete="off">
									<input id="ai-topsearch" class="submit am-btn" value="搜索" index="1" type="submit">
								</form>
							</div>
						</div>

						<div class="clear"></div>
					</div>
				</div>
			</article>
		</header>
            <div class="nav-table">
					   <div class="long-title"><span class="all-goods">全部分类</span></div>
					   <div class="nav-cont">
							<ul>
								<li class="index"><a href="#">首页</a></li>
                                <li class="qc"><a href="#">闪购</a></li>
                                <li class="qc"><a href="#">限时抢</a></li>
                                <li class="qc"><a href="#">团购</a></li>
                                <li class="qc last"><a href="#">大包装</a></li>
							</ul>
						    <div class="nav-extra">
						    	<i class="am-icon-user-secret am-icon-md nav-user"></i><b></b>我的福利
						    	<i class="am-icon-angle-right" style="padding-left: 10px;"></i>
						    </div>
						</div>
			</div>
			<b class="line"></b>
		<div class="center">
			<div class="col-main">
				<div class="main-wrap">

					<div class="am-cf am-padding">
						<div class="am-fl am-cf"><strong class="am-text-danger am-text-lg">修改密码</strong> / <small>Password</small></div>
					</div>
					<hr/>
					<!--进度条-->
					<div class="m-progress">
						<div class="m-progress-list">
							<span class="step-1 step">
                                <em class="u-progress-stage-bg"></em>
                                <i class="u-stage-icon-inner">1<em class="bg"></em></i>
                                <p class="stage-name">重置密码</p>
                            </span>
							<span class="step-2 step">
                                <em class="u-progress-stage-bg"></em>
                                <i class="u-stage-icon-inner">2<em class="bg"></em></i>
                                <p class="stage-name">完成</p>
                            </span>
							<span class="u-progress-placeholder"></span>
						</div>
						<div class="u-progress-bar total-steps-2">
							<div class="u-progress-bar-inner"></div>
						</div>
					</div>
					<form class="am-form am-form-horizontal" id="savepwdform" action="<?php echo U('Usercenter/savePassword');?>" method="post">
						<div class="am-form-group">
							<label for="user-old-password" class="am-form-label">原密码</label>
							<div class="am-form-content">
								<input type="password" id="user-old-password" name="password" placeholder="请输入原登录密码">
							</div>
						</div>
						<div class="am-form-group">
							<label for="user-new-password" class="am-form-label">新密码</label>
							<div class="am-form-content">
								<input type="password" id="user-new-password" name="newpassword" placeholder="建议至少使用两种字符组合">
							</div>
						</div>
						<div class="am-form-group">
							<label for="user-confirm-password" class="am-form-label">确认密码</label>
							<div class="am-form-content">
								<input type="password" id="user-confirm-password" name="confirmpassword" placeholder="请再次输入上面的密码">
							</div>
						</div>
						<div class="info-btn">
							<button class="am-btn am-btn-danger" type="submit" id="savebtn">保存修改</button>
						</div>
					</form>

				</div>
				<!--底部-->
				<div class="footer">
					<div class="footer-hd">
						<p>
							<a href="#">恒望科技</a>
							<b>|</b>
							<a href="#">商城首页</a>
							<b>|</b>
							<a href="#">支付宝</a>
							<b>|</b>
							<a href="#">物流</a>
						</p>
					</div>
					<div class="footer-bd">
						<p>
							<a href="#">关于恒望</a>
							<a href="#">合作伙伴</a>
							<a href="#">联系我们</a>
							<a href="#">网站地图</a>
							<em>© 2015-2025 Hengwang.com 版权所有. 更多模板 <a href="http://www.cssmoban.com/" target="_blank" title="模板之家">模板之家</a> - Collect from <a href="http://www.cssmoban.com/" title="网页模板" target="_blank">网页模板</a></em>
						</p>
					</div>
				</div>
			</div>

			<aside class="menu">
				<ul>
					<li class="person active">
						<a href="index.html"><i class="am-icon-user"></i>个人中心</a>
					</li>
					<li class="person">
						<p><i class="am-icon-newspaper-o"></i>个人资料</p>
						<ul>
							<li> <a href="information.html">个人信息</a></li>
							<li> <a href="safety.html">安全设置</a></li>
							<li> <a href="address.html">地址管理</a></li>
							<li> <a href="cardlist.html">快捷支付</a></li>
						</ul>
					</li>
					<li class="person">
						<p><i class="am-icon-balance-scale"></i>我的交易</p>
						<ul>
							<li><a href="order.html">订单管理</a></li>
							<li> <a href="change.html">退款售后</a></li>
							<li> <a href="comment.html">评价商品</a></li>
						</ul>
					</li>
					<li class="person">
						<p><i class="am-icon-dollar"></i>我的资产</p>
						<ul>
							<li> <a href="points.html">我的积分</a></li>
							<li> <a href="coupon.html">优惠券 </a></li>
							<li> <a href="bonus.html">红包</a></li>
							<li> <a href="walletlist.html">账户余额</a></li>
							<li> <a href="bill.html">账单明细</a></li>
						</ul>
					</li>

					<li class="person">
						<p><i class="am-icon-tags"></i>我的收藏</p>
						<ul>
							<li> <a href="collection.html">收藏</a></li>
							<li> <a href="foot.html">足迹</a></li>
						</ul>
					</li>

					<li class="person">
						<p><i class="am-icon-qq"></i>在线客服</p>
						<ul>
							<li> <a href="consultation.html">商品咨询</a></li>
							<li> <a href="suggest.html">意见反馈</a></li>							
							
							<li> <a href="news.html">我的消息</a></li>
						</ul>
					</li>
				</ul>

			</aside>
		</div>

	</body>

<script>
$(function() {

	// 当点击输入框时显示提示信息
    $('form :input').click(function() {

    	// 如果是前台个人中心-个人资料-安全设置-修改密码-原密码输入框
		if ($(this).is('#user-old-password')) {
			// nextAll,查找当前元素之后所有的同辈元素。
			$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		// 设置输入的提示信息
			var promptMessage = "请输入原密码";
			$('<span></span>')
			.addClass('prompt-message')
			.text(promptMessage)
			.appendTo($listItem);
    	}  

    	// 如果是前台个人中心-个人资料-安全设置-修改密码-新密码输入框
		if ($(this).is('#user-new-password')) {
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

    	// 如果是前台个人中心-个人资料-安全设置-修改密码-确认密码输入框
		if ($(this).is('#user-confirm-password')) {
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
    });

    // 当输入框失去焦点的时候触发
    $('form :input').blur(function() {

    	// 如果是前台个人中心-个人资料-安全设置-修改密码-原密码输入框
    	if ($(this).is('#user-old-password')) {
    		// nextAll()删除所选输入框的同辈元素 
	    	$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		if (this.value == '') {
    			// 将Input框的颜色设置为红色
    			$('#user-old-password').css('border-color', 'red');

    			// 添加错误按钮
    			$('<i></i>')
    			.addClass('item-error')
    			.appendTo($listItem);

    			// 设置错误信息
    			var errorMessage = '请输入原密码';
    			$('<span></span>')
    			.addClass('error-message')
    			.text(errorMessage)
    			.appendTo($listItem);

    			// 阻止表单提交
				return false;

		    // 当用户有在原密码输入框输入密码,但是输入的原密码不正确
    		} else {
    			// 发起ajax + 判断
    			
				// 获取原密码输入框的值
    			var oldpassword = $('#user-old-password').val();

    			$.ajax({
    				type : 'post',
    				url  : "<?php echo U('Usercenter/passwordJudge');?>",
    				data : {oldpassword:oldpassword},
    				success: function(data) {
    					console.log(data);
    					if (data == '1') {
    						// 将Input框的颜色设置为灰色
    						$('#user-old-password').css('border-color', '#ccc');

    						// 添加正确按钮
    						$('<i></i>')
			    			.addClass('item-succ')
			    			.appendTo($listItem);

			    			// 允许表单提交
							return true;
							
    					} else {
    						// 将Input框的颜色设置为红色
			    			$('#user-old-password').css('border-color', 'red');

			    			// 添加错误按钮
			    			$('<i></i>')
			    			.addClass('item-error')
			    			.appendTo($listItem);

			    			// 设置错误信息
    						var errorMessage = '原密码输入不正确,请重新输入';
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

    	// 如果是前台个人中心-个人资料-安全设置-修改密码-新密码输入框
		if ($(this).is('#user-new-password')) {
			// nextAll()删除所选输入框的同辈元素 
			$(this).nextAll().remove();

			var $listItem = $(this).parents('div:first');
			if (this.value == '') {
				// 将Input框的颜色设置为红色
				$('#user-new-password').css('border-color', 'red');

				// 添加错误按钮
				$('<i></i>')
				.addClass('item-error')
				.appendTo($listItem);

				// 设置错误信息
				var errorMessage = '请输入新密码';
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
				$('#user-new-password').css('border-color', 'red');

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
				$('#user-new-password').css('border-color', 'red');

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
				$('#user-new-password').css('border-color', '#FF9911');

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
				// 将Input框的颜色设置为灰色
				$('#user-new-password').css('border-color', '#ccc');

				// 添加密码强度强按钮
				$('<i></i>')
				.addClass('i-pwd-strong')
				.appendTo($listItem);

				// 允许表单提交
				return true;
			}
		};

    	// 如果是前台个人中心-个人资料-安全设置-修改密码-确认密码输入框
    	if ($(this).is('#user-confirm-password')) {
    		// nextAll()删除所选输入框的同辈元素 
	    	$(this).nextAll().remove();

    		var $listItem = $(this).parents('div:first');
    		if (this.value == '') {
    			// 将Input框的颜色设置为红色
    			$('#user-confirm-password').css('border-color', 'red');

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

    			// 获取前台个人中心-个人资料-安全设置-修改密码-新密码输入框的值
    			var usernewpassword = $("#user-new-password").val();

    			// 获取前台个人中心-个人资料-安全设置-修改密码-确认密码输入框的值
    			var userconfirmpassword= $("#user-confirm-password").val();

    			// 如果输入的新密码与确认密码相同
    			if (usernewpassword == userconfirmpassword) {

    				// 将Input框的颜色设置为灰色
	    			$('#user-confirm-password').css('border-color', '#ccc');

	    			// 添加成功按钮
	    			$('<i></i>')
	    			.addClass('item-succ')
	    			.appendTo($listItem);

	    			// 允许表单提交
					return true;

	    		// 如果输入的密码与确认密码不相同
    			} else {

	    			// 将Input框的颜色设置为红色
	    			$('#user-confirm-password').css('border-color', 'red');

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
    });

	// 设置一个空的定时器
	let timer=null;
	// 如果用户点击保存修改的按钮(防止用户重复点击提交)
	$('#savebtn').click(function() {

		// 清除定时器
		clearTimeout(timer);
		//如果用户点击保存修改按钮的速度太快，小于0.5s就不会提交form表单到后台，但是最后还是会提交一次form表单到后台
	    timer = setTimeout(function(){

			// 当点击保存修改的按钮时,使修改密码的表单上的所有input框失去焦点
			$("#savepwdform :input").trigger("blur");

		},500)


    	// 分别获取上面3个输入框的值
    	var useroldpassword = $('#user-old-password').val();
    	var usernewpassword = $('#user-new-password').val();
    	var userconfirmpassword = $('#user-confirm-password').val();
    	

    	// 如果所有input框的值输入正确才给提交立即注册按钮,否则有一个input框的值不符合要求就不给提交
    	if (useroldpassword && usernewpassword && userconfirmpassword) {

    		return true;

    	} else {
    		return false;
    	}
	
	});
})
</script>
</html>