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
		<link href="/abc/Public/Home/css/appstyle.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="/abc/Public/Home/js/jquery-1.7.2.min.js"></script>

			<b class="line"></b>
		<div class="center">
			<div class="col-main">
				<div class="main-wrap">

					<div class="user-comment">
						<!--标题 -->
						<div class="am-cf am-padding">
							<div class="am-fl am-cf"><strong class="am-text-danger am-text-lg">发表评论</strong> / <small>Make&nbsp;Comments</small></div>
						</div>
						<hr/>
<!-- form表单开始 -->
<form action="<?php echo U('Usercenter/pinlun');?>" method="post">
						<div class="comment-main">

	<!-- 遍历订单详情表 -->
	<?php if(is_array($orderDataArr)): foreach($orderDataArr as $key=>$v): ?><input type="hidden" name="gid[]" value="<?php echo ($v['id']); ?>">
<input type="hidden" name="pid[]" value="<?php echo ($v['pid']); ?>">

							<div class="comment-list">
								<div class="item-pic">
									<a href="#" class="J_MakePoint">

									<!-- 遍历商品图片 -->
								<img src="/abc/Public/<?php echo ($v['image']); ?>" class="itempic">
									</a>
								</div>

								<div class="item-title">

									<div class="item-name">
										<a href="#">
											<p class="item-basic-info"><?php echo ($v['pname']); ?></p>
										</a>
									</div>
									<div class="item-info">
										<div class="info-little">
											<span>口味：<?php echo ($v['fkou']); ?></span><br>
											<span>包装：<?php echo ($v['fbao']); ?></span>
										</div>
										<div class="item-price">
											价格：<strong><?php echo ($v['pmoney']); ?>元</strong>
										</div>										
									</div>
								</div>
								<div class="clear"></div>
								<div class="item-comment">
									<textarea name="content[]" placeholder="请写下对宝贝的感受吧，对他人帮助很大哦！"></textarea>
								</div>
								<div class="filePic">
									<input type="file" class="inputPic" allowexts="gif,jpeg,jpg,png,bmp" accept="image/*" >
									<span>晒照片(0/5)</span>

									<img src="/abc/Public/Home/images/image.jpg" alt="">

								</div>
								<div class="item-opinion">
									<input type="checkbox" value="3" name="dafen[]"><i class="op2"></i>差评</li><li>
									<input type="checkbox" value="1" name="dafen[]"><i class="op1"></i>好评</li><li>
									<input type="checkbox" value="2" name="dafen[]"><i class="op3"></i>中评</li><li>
								</div>
							</div>
	<!-- 遍历订单详情表结束 --><?php endforeach; endif; ?>

													
							<div class="info-btn">
								<button><div class="am-btn am-btn-danger">发表评论</div></button>
							</div>
<!-- form表单结束 -->
</form>					
					<script type="text/javascript">
						$(document).ready(function() {
							$(".comment-list .item-opinion li").click(function() {	
								$(this).prevAll().children('i').removeClass("active");
								$(this).nextAll().children('i').removeClass("active");
								$(this).children('i').addClass("active");
								
							});
				     })
					</script>					
					
												
							
						</div>

					</div>

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