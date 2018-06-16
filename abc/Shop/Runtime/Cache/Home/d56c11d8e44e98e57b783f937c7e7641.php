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
<link href="/abc/Public/Home/css/cpstyle.css" rel="stylesheet" type="text/css">
<script src="/abc/Public/Home/AmazeUI-2.4.2/assets/js/jquery.min.js"></script>
<script src="/abc/Public/Home/AmazeUI-2.4.2/assets/js/amazeui.js"></script>
	<b class="line"></b>
		<div class="center">
			<div class="col-main">
				<div class="main-wrap">
					<div class="user-coupon">
						<!--标题 -->
						<div class="am-cf am-padding">
							<div class="am-fl am-cf"><strong class="am-text-danger am-text-lg">优惠券</strong> / <small>Coupon</small></div>
						</div>
						<hr/>

						<div class="am-tabs-d2 am-tabs  am-margin" data-am-tabs>

							<ul class="am-avg-sm-2 am-tabs-nav am-nav am-nav-tabs">
								<li class="am-active"><a href="#tab1">可用优惠券</a></li>
								<li><a href="#tab2">已用/过期优惠券</a></li>

							</ul>

							<div class="am-tabs-bd">
								<div class="am-tab-panel am-fade am-in am-active" id="tab1">
									<div class="coupon-items">
										<div class="coupon-item coupon-item-d">
											<div class="coupon-list">
												<div class="c-type">
													<div class="c-class">
														<strong>购物券</strong>
													</div>
													<div class="c-price">
														<strong>￥8</strong>
													</div>
													<div class="c-limit">
														【消费满&nbsp;95元&nbsp;可用】
													</div>
													<div class="c-time"><span>使用期限</span>2015-12-21--2015-12-31</div>
													<div class="c-type-top"></div>

													<div class="c-type-bottom"></div>
												</div>

												<div class="c-msg">
													<div class="c-range">
														<div class="range-all">
															<div class="range-item">
																<span class="label">券&nbsp;编&nbsp;号：</span>
																<span class="txt">35730144</span>
															</div>
														</div>
													</div>
													<div class="op-btns">
														<a href="#" class="btn"><span class="txt">立即使用</span><b></b></a>
													</div>
												</div>
											</div>
										</div>
										<div class="coupon-item coupon-item-yf">
											<div class="coupon-list">
												<div class="c-type">
													<div class="c-class">
														<strong>运费券</strong>
													</div>
													<div class="c-price">
														<strong>可抵运费</strong>
													</div>
													<div class="c-limit">
														【不含偏远地区】
													</div>
													<div class="c-time"><span>使用期限</span>2015-12-21--2015-12-31</div>
													<div class="c-type-top"></div>

													<div class="c-type-bottom"></div>
												</div>

												<div class="c-msg">
													<div class="c-range">
														<div class="range-all">
															<div class="range-item">
																<span class="label">券&nbsp;编&nbsp;号：</span>
																<span class="txt">35728267</span>
															</div>
														</div>

													</div>
													<div class="op-btns">
														<a href="#" class="btn"><span class="txt">立即使用</span><b></b></a>
													</div>
												</div>
											</div>
										</div>
									</div>

								</div>
								<div class="am-tab-panel am-fade" id="tab2">
									<div class="coupon-items">
										<div class="coupon-item coupon-item-d">
											<div class="coupon-list">
												<div class="c-type">
													<div class="c-class">
														<strong>购物券</strong>
														<span class="am-icon-trash"></span>
													</div>
													<div class="c-price">
														<strong>￥8</strong>
													</div>
													<div class="c-limit">
														【消费满&nbsp;95元&nbsp;可用】
													</div>
													<div class="c-time"><span>使用期限</span>2015-12-21--2015-12-31</div>
													<div class="c-type-top"></div>

													<div class="c-type-bottom"></div>
												</div>

												<div class="c-msg">
													<div class="c-range">
														<div class="range-all">
															<div class="range-item">
																<span class="label">券&nbsp;编&nbsp;号：</span>
																<span class="txt">35730144</span>
															</div>
														</div>
													</div>
													<div class="op-btns c-del">
														<a href="#" class="btn"><span class="txt">删除</span><b></b></a>
													</div>
												</div>
												
												<li class="td td-usestatus ">
													<div class="item-usestatus ">
														<span><img src="/abc/Public/Home/images/gift_stamp_31.png"</span>
													</div>
												</li>												
											</div>
										</div>
										<div class="coupon-item coupon-item-yf">
											<div class="coupon-list">
												<div class="c-type">
													<div class="c-class">
														<strong>运费券</strong>
														<span class="am-icon-trash"></span>
													</div>
													<div class="c-price">
														<strong>可抵运费</strong>
													</div>
													<div class="c-limit">
														【不含偏远地区】
													</div>
													<div class="c-time"><span>使用期限</span>2015-12-21--2015-12-31</div>
													<div class="c-type-top"></div>

													<div class="c-type-bottom"></div>
												</div>

												<div class="c-msg">
													<div class="c-range">
														<div class="range-all">
															<div class="range-item">
																<span class="label">券&nbsp;编&nbsp;号：</span>
																<span class="txt">35728267</span>
															</div>
														</div>

													</div>
													<div class="op-btns c-del">
														<a href="#" class="btn"><span class="txt">删除</span><b></b></a>
													</div>
												</div>
												
												<li class="td td-usestatus ">
													<div class="item-usestatus ">
														<span><img src="/abc/Public/Home/images/gift_stamp_21.png"</span>
													</div>
												</li>
											</div>
										</div>
									</div>	
								</div>
							</div>
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