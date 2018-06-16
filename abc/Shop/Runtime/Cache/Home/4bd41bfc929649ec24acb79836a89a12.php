<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=0">
		<title>个人中心</title>
		<link href="/abc/Public/Home/AmazeUI-2.4.2/assets/css/admin.css" rel="stylesheet" type="text/css">
		<link href="/abc/Public/Home/AmazeUI-2.4.2/assets/css/amazeui.css" rel="stylesheet" type="text/css">
		<link href="/abc/Public/Home/css/personal.css" rel="stylesheet" type="text/css">
		<link href="/abc/Public/Home/css/vipstyle.css" rel="stylesheet" type="text/css">
		<script src="/abc/Public/Home/AmazeUI-2.4.2/assets/js/jquery.min.js"></script>
		<script src="/abc/Public/Home/AmazeUI-2.4.2/assets/js/amazeui.js"></script>
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
									<?php  if (empty(session('user'))) { echo "<a href=".U('Index/index')." title='点击前往商城首页'>&nbsp;欢迎您来到零食商城,</a>　<a href=".U('Login/login')." title='亲，要登录后才能买东西哦~'>登录</a>　|　<a href=".U('Login/emailRegister')." title='还没账号?点击立即注册'>注册</a>"; } else { echo "<a href=".U('Index/index')." title='点击前往商城首页'>&nbsp;☺欢迎您,</a><a href=".U('Usercenter/index')." title='点击前往个人中心'>".session('user')['username']."</a> ｜ <a href=".U('Login/logout')." title='点击退出登录'>注销</a>"; } ?>　　
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
								<div class="menu-hd"><a id="mc-menu-hd" href="#" target="_top"><i class="am-icon-shopping-cart  am-icon-fw"></i><span>我的购物车</span><strong id="J_MiniCartNum" class="h">0</strong></a></div>
							</div>
							<div class="topMessage favorite">
								
						</ul>
						</div>

						<!--悬浮搜索框-->

						<div class="nav white">
							<div class="logoBig">
								<a href="<?php echo U('Index/index');?>" title="回商城首页"><li><img src="/abc/Public/Home/images/logobig.png" /></li></a>
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
<!-- 头部 -->
<!--模板 整体继承可重写模块开始 头部->尾部  -->


<link href="/abc/Public/Home/AmazeUI-2.4.2/assets/css/admin.css" rel="stylesheet" type="text/css">
<link href="/abc/Public/Home/AmazeUI-2.4.2/assets/css/amazeui.css" rel="stylesheet" type="text/css">
<link href="/abc/Public/Home/css/personal.css" rel="stylesheet" type="text/css">
<link href="/abc/Public/Home/css/addstyle.css" rel="stylesheet" type="text/css">
<script src="/abc/Public/Home/AmazeUI-2.4.2/assets/js/jquery.min.js" type="text/javascript"></script>
<script src="/abc/Public/Home/AmazeUI-2.4.2/assets/js/amazeui.js"></script>
<script src="/abc/Public/Admin/assets/layer/layer.js" type="text/javascript" ></script>  

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
</style>
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

					<div class="user-address">
						<!--标题 -->
						<div class="am-cf am-padding">
							<div class="am-fl am-cf"><strong class="am-text-danger am-text-lg">地址管理</strong> / <small>Address&nbsp;list</small></div>
						</div>
						<hr/>
						<ul class="am-avg-sm-1 am-avg-md-3 am-thumbnails">
							<?php if(is_array($address)): foreach($address as $k=>$v): ?><li class="user-addresslist <?php if($v['isdefault'] == '1'){ echo 'defaultAddr';}?>" >
									<span class="new-option-r" onclick="default1(this,<?php echo ($v['id']); ?>,<?php echo ($v['uid']); ?>)" ><i class="am-icon-check-circle"></i>默认地址</span>
									<p class="new-tit new-p-re">
										<span class="new-txt"><?php echo ($v['username']); ?></span>
										<span class="new-txt-rd2"><?php echo ($v['userphone']); ?></span>
									</p>
									<div class="new-mu_l2a new-p-re">
										<p class="new-mu_l2cw">
											<span class="title">地址：</span>
											<span class="province"><?php echo ($v['area1']); ?></span>省
											<span class="city"><?php echo ($v['area2']); ?></span>市
											<span class="dist"><?php echo ($v['area3']); ?></span>
											<span class="street"><?php echo ($v['address']); ?></span></p>
									</div>
									<div class="new-addr-btn">
										<a href="<?php echo U('Usercenter/editaddress', 'id='.$v['id']);?>"><i class="am-icon-edit"></i>编辑</a>
										<span class="new-addr-bar">|</span>
										<a href="javascript:void(0);" onclick="delClick(this, '<?php echo ($v["id"]); ?>',<?php echo ($v['uid']); ?>);"><i class="am-icon-trash"></i>删除</a>
									</div>
								</li><?php endforeach; endif; ?>
							<!-- defaultAddr -->
						</ul>
						<div class="clear"></div>
						<a class="new-abtn-type" data-am-modal="{target: '#doc-modal-1', closeViaDimmer: 0}">添加新地址</a>
						<!--例子-->
						<div class="am-modal am-modal-no-btn" id="doc-modal-1">

							<div class="add-dress">

								<!--标题 -->
								<div class="am-cf am-padding">
									<div class="am-fl am-cf"><strong class="am-text-danger am-text-lg">新增地址</strong> / <small>Add&nbsp;address</small></div>
								</div>
								<hr/>

								<div class="am-u-md-12 am-u-lg-8" style="margin-top: 20px;">
									<form class="am-form am-form-horizontal" id="address" action="<?php echo U('Usercenter/address');?>" method="post">

										<div class="am-form-group">
											<label for="user-name" class="am-form-label">收货人</label>
											<div class="am-form-content">
												<input type="text" id="user-name" placeholder="收货人" name="username" maxlength="20">
											</div>
										</div>

										<div class="am-form-group">
											<label for="user-phone" class="am-form-label">手机号码</label>
											<div class="am-form-content">
												<input id="user-phone" placeholder="手机号必填" type="text" name="userphone" maxlength="11">
											</div>
										</div>

										<div class="am-form-group">
											<label for="user-address" class="am-form-label">所在地</label>
											<div class="am-form-content address">
												<select name="province" id="province">
													<option value="0">--请选择--</option>
													<!-- 遍历地区表 -->
													<?php if(is_array($province)): foreach($province as $key=>$v): ?><option value="<?php echo ($v['id']); ?>" class="province"><?php echo ($v['area_name']); ?></option><?php endforeach; endif; ?>
												</select>
												<select id="city" name="city">
													<option value="0">--请选择--</option>
												</select>
												<select id="area" name="area">
													<option value="0">--请选择--</option>
												</select>
											</div>
										</div>

										<div class="am-form-group">
											<label for="user-intro" class="am-form-label">详细地址</label>
											<div class="am-form-content">
												<textarea class="" rows="3" id="user-intro" placeholder="输入详细地址" name="address" maxlength="100"></textarea>
												<small>100字以内写出你的详细地址...</small>
											</div>
										</div>

										<div class="am-form-group">
											<div class="am-u-sm-9 am-u-sm-push-3">
												<button class="am-btn am-btn-danger" type="submit" id="savebtn">保存</button>
												<a href="<?php echo U('Usercenter/address');?>" class="am-close am-btn am-btn-danger" data-am-modal-close>取消</a>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>

<script>

	function default1(obj,id,uid){

		console.log(id,uid)
		 $.ajax({

            type:'post',
            url:"<?php echo U('Usercenter/defaultAddr');?>",
            data:{
                id:id,
                uid:uid
            },
            success:function(res){
            	console.log(res)
            }
        });
	}
	// 删除地址
	function delClick(obj,id) {
		layer.confirm('确定要删除该地址吗?',{icon:0,},function(index){
			$.ajax({
				type: 'get',
				url : "<?php echo U('Usercenter/addressajaxDel');?>",
				data : {id:+id},
				success: function(res) {
					if (res != '-1') {
						$(obj).parents("li").remove();
						layer.msg('已删除!',{icon:1,time:1000});
					} else {
						layer.msg('删除失败!',{icon:1,time:1000});
					}
				},
			});
		});
	}

	// 处理省份
	$('#province').change(function() {
		// console.log($("select[name='city']")[0]);
		// 清空下级的选项(城市)
		$("select[name='city']")[0].length = 1;
		// 清空下级的选项(县区)
		$("select[name='area']")[0].length = 1;

		// 获取省份的ID
		var id = $(this).val();
		var str = '';

		$.ajax({
			type:'get',
			url: "<?php echo U('Usercenter/address');?>",
			data:{id: id},
			success: function(msg) {
				for (var i = 0; i < msg.length; i++) {

					str += "<option value='"+msg[i].id+"'>"+msg[i].area_name+"</option>";
				}

				$('#city').append(str);
			}
		});
	});

	// 处理城市
	$('#city').change(function() {
		// 清空下级的选项(县区)
		$("select[name='area']")[0].length = 1;

		// 获取城市的ID
		var id = $(this).val();
		var str = '';

		$.ajax({
			type:'get',
			url:"<?php echo U('Usercenter/address');?>",
			data:{id:id},

			success: function(msg) {

				for (var i = 0; i < msg.length; i++) {

					str += "<option value='"+msg[i].id+"'>"+msg[i].area_name+"</option>";
				}

				$("#area").append(str);
			}
		});
	});



	$(function() {

		// 当点击输入框时
		$('form :input').click(function() {

			// 如果是收货人的输入框
			if ($(this).is('#user-name')) {
				// nextAll,查找当前元素之后所有的同辈元素。
				$(this).nextAll().remove();

				// 将Input框的颜色设置为灰色
				$('#user-name').css('border-color', '#E4EAEE');
				var $listItem = $(this).parents('div:first');
	    		// 设置输入的提示信息
				var promptMessage = "请输入收货人姓名";
				$('<span></span>')
				.addClass('prompt-message')
				.text(promptMessage)
				.appendTo($listItem);
			}

			// 如果是手机号码的输入框
			if ($(this).is('#user-phone')) {
				// nextAll,查找当前元素之后所有的同辈元素。
				$(this).nextAll().remove();

				// 将Input框的颜色设置为灰色
				$('#user-phone').css('border-color', '#E4EAEE');
				var $listItem = $(this).parents('div:first');
	    		// 设置输入的提示信息
				var promptMessage = "请输入收货人的手机号码";
				$('<span></span>')
				.addClass('prompt-message')
				.text(promptMessage)
				.appendTo($listItem);
			}

			// 如果是收货人的详细地址的输入框
			if ($(this).is('#user-intro')) {
				// nextAll,查找当前元素之后所有的同辈元素。
				$(this).nextAll().remove();

				// 将Input框的颜色设置为灰色
				$('#user-intro').css('border-color', '#E4EAEE');
				var $listItem = $(this).parents('div:first');
	    		// 设置输入的提示信息
				var promptMessage = "请输入收货人的详细地址";
				$('<span></span>')
				.addClass('prompt-message')
				.text(promptMessage)
				.appendTo($listItem);
			}
		});

		// 当输入框失去焦点时
		$('form :input').blur(function() {

			// 如果是收货人输入框
			if ($(this).is('#user-name')) {
				// nextAll,查找当前元素之后所有的同辈元素。
				$(this).nextAll().remove();
				var $listItem = $(this).parents('div:first');
	    		if (this.value == '') {
	    			// 将Input框的颜色设置为红色
	    			$('#user-name').css('border-color', 'red');

	    			// 添加错误按钮
	    			$('<i></i>')
	    			.addClass('item-error')
	    			.appendTo($listItem);

	    			// 设置错误信息
	    			var errorMessage = '请输入收货人姓名';
	    			$('<span></span>')
	    			.addClass('error-message')
	    			.text(errorMessage)
	    			.appendTo($listItem);

	    			// 阻止表单提交
					return false;
				}

				if (this.value != '' && !/^([\u4e00-\u9fa5]{2,20}|[a-zA-Z\.\s]{2,20})$/.test(this.value)) {

		    		// 将Input框的颜色设置为红色
					$('#user-name').css('border-color', 'red');

					// 添加错误按钮
					$('<i></i>')
					.addClass('item-error')
					.appendTo($listItem);

					// 设置错误信息
					var errorMessage = '输入的姓名不正确请重新输入';
			        $('<span></span>')
			          .addClass('error-message')
			          .text(errorMessage)
			          .appendTo($listItem);

			        // 阻止表单提交
					return false;
		    	} else {
		    		// 将Input框的颜色设置为灰色
					$('#user-name').css('border-color', '#E4EAEE');

	    			// 允许表单提交
					return true;
		    	}
			}

			// 如果是收货人的手机号码输入框
			if ($(this).is('#user-phone')) {
				// nextAll,查找当前元素之后所有的同辈元素。
				$(this).nextAll().remove();
				var $listItem = $(this).parents('div:first');
	    		if (this.value == '') {
	    			// 将Input框的颜色设置为红色
	    			$('#user-phone').css('border-color', 'red');

	    			// 添加错误按钮
	    			$('<i></i>')
	    			.addClass('item-error')
	    			.appendTo($listItem);

	    			// 设置错误信息
	    			var errorMessage = '请输入收货人的手机号码';
	    			$('<span></span>')
	    			.addClass('error-message')
	    			.text(errorMessage)
	    			.appendTo($listItem);

	    			// 阻止表单提交
					return false;
				}

				if (this.value != '' && !/^(1[38]\d|14[57]|15[0123]|15[56789]|166|17[3]|17[678]|19[89])\d{8}$/.test(this.value)) {

		    		// 将Input框的颜色设置为红色
					$('#user-phone').css('border-color', 'red');

					// 添加错误按钮
					$('<i></i>')
					.addClass('item-error')
					.appendTo($listItem);

					// 设置错误信息
					var errorMessage = '输入的手机号码不正确请重新输入';
			        $('<span></span>')
			          .addClass('error-message')
			          .text(errorMessage)
			          .appendTo($listItem);

			        // 阻止表单提交
					return false;
		    	} else {
		    		// 将Input框的颜色设置为灰色
					$('#user-phone').css('border-color', '#E4EAEE');

	    			// 允许表单提交
					return true;
		    	}
			}

			// 如果是收货人的详细地址输入框
			if ($(this).is('#user-intro')) {
				// nextAll,查找当前元素之后所有的同辈元素。
				$(this).nextAll().remove();
				var $listItem = $(this).parents('div:first');
	    		if (this.value == '') {
	    			// 将Input框的颜色设置为红色
	    			$('#user-intro').css('border-color', 'red');

	    			// 添加错误按钮
	    			$('<i></i>')
	    			.addClass('item-error')
	    			.appendTo($listItem);

	    			// 设置错误信息
	    			var errorMessage = '请输入收货人的详细地址';
	    			$('<span></span>')
	    			.addClass('error-message')
	    			.text(errorMessage)
	    			.appendTo($listItem);

	    			// 阻止表单提交
					return false;

				} else if (this.value != '' && !/^[\S\s]{1,100}$/.test(this.value)) {

		    		// 将Input框的颜色设置为红色
					$('#user-intro').css('border-color', 'red');

					// 添加错误按钮
					$('<i></i>')
					.addClass('item-error')
					.appendTo($listItem);

					// 设置错误信息
					var errorMessage = '详细地址长度不能超过100个汉字';
			        $('<span></span>')
			          .addClass('error-message')
			          .text(errorMessage)
			          .appendTo($listItem);

			        // 阻止表单提交
					return false;
					
		    	} else {
		    		// 将Input框的颜色设置为灰色
					$('#user-intro').css('border-color', '#E4EAEE');

	    			// 允许表单提交
					return true;
		    	}
			}
		});		


		// 设置一个空的定时器
		let timer=null;
		// 如果用户点击保存的按钮(防止用户重复点击提交)
		$('#savebtn').click(function() {

			// 清除定时器
			clearTimeout(timer);
			//如果用户点击保存按钮的速度太快，小于0.5s就不会提交form表单到后台，但是最后还是会提交一次form表单到后台
		    timer = setTimeout(function(){

				// 当点击保存的按钮时,使新增地址的表单上的所有input框失去焦点
				$("#address :input").trigger("blur");
				
			},500)

		   // return false;

		});
	})
	


</script>

<script type="text/javascript">
	$(document).ready(function() {							
		$(".new-option-r").click(function() {
			$(this).parent('.user-addresslist').addClass("defaultAddr").siblings().removeClass("defaultAddr");
		});
		
		var $ww = $(window).width();
		if($ww>640) {
			$("#doc-modal-1").removeClass("am-modal am-modal-no-btn")
		}
		
	})
</script>

					<div class="clear"></div>

				</div>

<!-- 重写模块结束 -->

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
							<li> <a href="<?php echo U('Usercenter/information');?>">个人信息</a></li>
							<li> <a href="<?php echo U('Usercenter/safety');?>">安全设置</a></li>
							<li> <a href="<?php echo U('Usercenter/address');?>">地址管理</a></li>
							
						</ul>
					</li>
					<li class="person">
						<p><i class="am-icon-balance-scale"></i>我的交易</p>
						<ul>
							<li><a href="<?php echo U('Usercenter/order');?>">订单管理</a></li>
							<li> <a href="#">退款售后</a></li>
							<li> <a href="<?php echo U('Usercenter/comment');?>">评价商品</a></li>
						</ul>
					</li>
					<li class="person">
						<p><i class="am-icon-dollar"></i>我的资产</p>
						<ul>
							<li> <a href="<?php echo U('Usercenter/points');?>">我的积分</a></li>
							<li> <a href="<?php echo U('Usercenter/coupon');?>">代币券 </a></li>
							<li> <a href="<?php echo U('Usercenter/walletlist');?>">账户余额</a></li>
							<li> <a href="<?php echo U('Usercenter/bill');?>">账单明细</a></li>
						</ul>
					</li>

					<li class="person">
						<p><i class="am-icon-tags"></i>我的收藏</p>
						<ul>
							<li> <a href="<?php echo U('Usercenter/collection');?>">收藏</a></li>									
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
		<!--引导 -->
		<div class="navCir">
			<li><a href="<?php echo U('Index/index');?>"><i class="am-icon-home "></i>首页</a></li>
			<li><a href="<?php echo U('');?>"><i class="am-icon-list"></i>分类</a></li>
			<li><a href="<?php echo U;?>"><i class="am-icon-shopping-basket"></i>购物车</a></li>
			<li class="active"><a href="index.html"><i class="am-icon-user"></i>我的</a></li>
		</div>
	</body>

</html>