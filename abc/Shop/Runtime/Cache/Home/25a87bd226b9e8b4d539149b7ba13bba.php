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
		<link href="/abc/Public/Home/css/orstyle.css" rel="stylesheet" type="text/css">
		<script src="/abc/Public/Home/AmazeUI-2.4.2/assets/js/jquery.min.js"></script>
		<script src="/abc/Public/Home/AmazeUI-2.4.2/assets/js/amazeui.js"></script>
		<link href="/abc/Public/Home/AmazeUI-2.4.2/assets/css/amazeui.css" rel="stylesheet" type="text/css" />
		<link href="/abc/Public/Home/AmazeUI-2.4.2/assets/css/admin.css" rel="stylesheet" type="text/css" />

		<link href="/abc/Public/Home/css/personal.css" rel="stylesheet" type="text/css">
		<b class="line"></b>
		<div class="center">
			<div class="col-main">
				<div class="main-wrap">

					<div class="user-order">

						<!--标题 -->
						<div class="am-cf am-padding">
							<div class="am-fl am-cf"><strong class="am-text-danger am-text-lg">订单管理</strong> / <small>Order</small></div>
						</div>
						<hr/>

						<div class="am-tabs am-tabs-d2 am-margin" data-am-tabs>

							<ul class="am-avg-sm-5 am-tabs-nav am-nav am-nav-tabs">
								<li class="am-active"><a href="#tab1">订单</a></li>
								<li><a href="#tab2">待付款</a></li>
								<li><a href="#tab3">待发货</a></li>
								<li><a href="#tab4">待收货</a></li>
								<li><a href="#tab5">待评价</a></li>
							</ul>

							<div class="am-tabs-bd">
								<div class="am-tab-panel am-fade am-in am-active" id="tab1">
									<div class="order-top">
										<div class="th th-item">
											<td class="td-inner">商品</td>
										</div>
										<div class="th th-price">
											<td class="td-inner">单价</td>
										</div>
										<div class="th th-number">
											<td class="td-inner">数量</td>
										</div>
										<div class="th th-operation">
											<td class="td-inner">商品操作</td>
										</div>
										<div class="th th-amount">
											<td class="td-inner">合计</td>
										</div>
										<div class="th th-status">
											<td class="td-inner">交易状态</td>
										</div>
										<div class="th th-change">
											<td class="td-inner">交易操作</td>
										</div>
									</div>

									<div class="order-main">
										<div class="order-list">


										<!-- 遍历订单表 -->
										<?php if(is_array($orderArr)): foreach($orderArr as $key=>$v): ?><div class="order-status3">
												<div class="order-title">
													<div class="dd-num">订单编号：<a href="javascript:;"><?php echo ($v['id']); ?></a></div>
													<span>成交时间：<?php echo ($v['atime']); ?></span>
													<!--    <em>店铺：小桔灯</em>-->
												</div>

												<div class="order-content">
													<div class="order-left">

		<!-- 遍历订单详情表 -->
		<?php if(is_array($orderDataList)): foreach($orderDataList as $key=>$val): if(($v['id'] == $val['pid'])): ?><ul class="item-list">
															<li class="td td-item">
																<div class="item-pic">
																	<a href="#" class="J_MakePoint">
																		<img src="/abc/Public/Home/images/62988.jpg_80x80.jpg" class="itempic J_ItemImg">
																	</a>
																</div>
											
																<div class="item-info">
																	<div class="item-basic-info">
																		<a href="#">
																			<p><?php echo ($val['pname']); ?></p>
																			<p class="info-little">口味：<?php echo ($val['fkou']); ?></p>
																			<p class="info-little">包装：<?php echo ($val['fbao']); ?></p>
																			
																			
																		</a>
																	</div>
																</div>

															</li>
															<li class="td td-price">
																<div class="item-price">
																	<?php echo ($val['pmoney']); ?>
																</div>
															</li>
															<li class="td td-number">
																<div class="item-number">
																	<span>×</span><?php echo ($val['num']); ?>
																</div>
															</li>
															<li class="td td-operation">
																<div class="item-operation">
																	<a href="refund.html">退款/退货</a>
																</div>
															</li>
														</ul><?php endif; ?>
		<!-- 遍历订单详情表结束 --><?php endforeach; endif; ?>

													</div>
													<div class="order-right">
														<li class="td td-amount">
															<div class="item-amount">
																合计：<?php echo ($v['productmoney']); ?>
																<p>含运费：<span>10.00</span></p>
															</div>
														</li>
														<div class="move-right">
															<li class="td td-status">
																<div class="item-status">
																	<p class="Mystatus"><?php echo ($v['state']); ?></p>
																	<p class="order-info"><a href="orderinfo.html">订单详情</a></p>
																	<p class="order-info"><a href="logistics.html">查看物流</a></p>
																	<p class="order-info"><a href="#">延长收货</a></p>
																</div>
															</li>
															<?php if(($v['state'] == '待付款')): ?><li class="td td-change">
																	<div class="am-btn am-btn-danger anniu">
																		<a style="color: #fff;" href="<?php echo U('Orders/paySuccessful', ['id' => $v['id']]);?>">去付款</a></div>
																</li><?php endif; ?>
															<?php if(($v['state'] == '待发货')): ?><li class="td td-change">
																	<div class="am-btn am-btn-danger anniu">
																		<a style="color: #fff;" href="<?php echo U('Usercenter/cuidan', ['id' => $v['id']]);?>">提醒发货</a></div>
																</li><?php endif; ?>
															<?php if(($v['state'] == '待收货')): ?><li class="td td-change">
																	<div class="am-btn am-btn-danger anniu">
																		<a style="color: #fff;" href="<?php echo U('Usercenter/ok', ['id' => $v['id']]);?>">确认收货</a></div>
																</li><?php endif; ?>
															<?php if(($v['state'] == '交易成功')): ?><li class="td td-change">
																	<div class="am-btn am-btn-danger anniu">
																		<a style="color: #fff;" href="<?php echo U('Usercenter/del', ['id' => $v['id']]);?>">删除订单</a></div>
																</li><?php endif; ?>
															<?php if(($v['state'] == '立即评价')): ?><li class="td td-change">
																	<div class="am-btn am-btn-danger anniu">
																		<a style="color: #fff;" href="<?php echo U('Usercenter/commentlist', ['id' => $v['id']]);?>">立即评价</a></div>
																</li><?php endif; ?>

														</div>
													</div>
												</div>
											</div>

		<!-- 遍历订单表结束 --><?php endforeach; endif; ?>
										</div>

									</div>

								</div>
								<div class="am-tab-panel am-fade" id="tab2">

									<div class="order-top">
										<div class="th th-item">
											<td class="td-inner">商品</td>
										</div>
										<div class="th th-price">
											<td class="td-inner">单价</td>
										</div>
										<div class="th th-number">
											<td class="td-inner">数量</td>
										</div>
										<div class="th th-operation">
											<td class="td-inner">商品操作</td>
										</div>
										<div class="th th-amount">
											<td class="td-inner">合计</td>
										</div>
										<div class="th th-status">
											<td class="td-inner">交易状态</td>
										</div>
										<div class="th th-change">
											<td class="td-inner">交易操作</td>
										</div>
									</div>

									<div class="order-main">
										<div class="order-list">
	<!-- 待付款区域 -->
	<?php if(is_array($orderFk)): foreach($orderFk as $key=>$ff): ?><div class="order-status1">

												<div class="order-title">
													<div class="dd-num">订单编号：<a href="javascript:;"><?php echo ($ff['id']); ?></a></div>
													<span>成交时间：<?php echo ($v['atime']); ?></span>
													<!--    <em>店铺：小桔灯</em>-->
												</div>


												<div class="order-content">
													<div class="order-left">
	<!-- 遍历待付款详情 -->
	<?php if(is_array($orderFkList)): foreach($orderFkList as $key=>$kk): ?><ul class="item-list">
															<li class="td td-item">
																<div class="item-pic">
																	<a href="#" class="J_MakePoint">
																		<img src="/abc/Public/Home/images/kouhong.jpg_80x80.jpg" class="itempic J_ItemImg">
																	</a>
																</div>
																<div class="item-info">
																	<div class="item-basic-info">
																		<a href="#">
																			<p><?php echo ($kk['pname']); ?></p>
																			<p class="info-little">口味：<?php echo ($kk['fkou']); ?></p>
																			<p class="info-little">包装：<?php echo ($kk['fbao']); ?></p>
																		</a>
																	</div>
																</div>
															</li>
															<li class="td td-price">
																<div class="item-price">
																	<?php echo ($kk['pmoney']); ?>
																</div>
															</li>
															<li class="td td-number">
																<div class="item-number">
																	<span>×</span><?php echo ($kk['num']); ?>
																</div>
															</li>
															<li class="td td-operation">
																<div class="item-operation">

																</div>
															</li>
														</ul>
	<!-- 订单遍历结束 --><?php endforeach; endif; ?>
													</div>
													<div class="order-right">
														<li class="td td-amount">
															<div class="item-amount">
																合计：<?php echo ($ff['productmoney']); ?>
																<p>含运费：<span>10.00</span></p>
															</div>
														</li>
														<div class="move-right">
															<li class="td td-status">
																<div class="item-status">
																	<p class="Mystatus"><?php echo ($ff['state']); ?></p>
																	<p class="order-info"><a href="<?php echo U('Usercenter/exit', ['id' => $ff['id']]);?>">取消订单</a></p>

																</div>
															</li>
															<li class="td td-change">
																<a href="pay.html">
																<div class="am-btn am-btn-danger anniu">
																	一键支付</div></a>
															</li>
														</div>
													</div>

												</div>
											</div><?php endforeach; endif; ?>
										</div>

									</div>
								</div>
								<div class="am-tab-panel am-fade" id="tab3">
									<div class="order-top">
										<div class="th th-item">
											<td class="td-inner">商品</td>
										</div>
										<div class="th th-price">
											<td class="td-inner">单价</td>
										</div>
										<div class="th th-number">
											<td class="td-inner">数量</td>
										</div>
										<div class="th th-operation">
											<td class="td-inner">商品操作</td>
										</div>
										<div class="th th-amount">
											<td class="td-inner">合计</td>
										</div>
										<div class="th th-status">
											<td class="td-inner">交易状态</td>
										</div>
										<div class="th th-change">
											<td class="td-inner">交易操作</td>
										</div>
									</div>

									<div class="order-main">
										<div class="order-list">

	<!-- 发货区域开始 -->
	<?php if(is_array($orderFh)): foreach($orderFh as $key=>$fk): ?><div class="order-status2">
												<div class="order-title">
													<div class="dd-num">订单编号：<a href="javascript:;"><?php echo ($fk['id']); ?></a></div>
													<span>成交时间：<?php echo ($fk['atime']); ?></span>
													<!--    <em>店铺：小桔灯</em>-->
												</div>
												<div class="order-content">
													<div class="order-left">
	<!-- 待发货区域详情开始 -->
	<?php if(is_array($orderFhList)): foreach($orderFhList as $key=>$fks): ?><ul class="item-list">
															<li class="td td-item">
																<div class="item-pic">
																	<a href="#" class="J_MakePoint">
																		<img src="/abc/Public/Home/images/kouhong.jpg_80x80.jpg" class="itempic J_ItemImg">
																	</a>
																</div>
																<div class="item-info">
																	<div class="item-basic-info">
																		<a href="#">
																			<p><?php echo ($fks['pname']); ?></p>
																			<p class="info-little">口味：<?php echo ($fks['fkou']); ?></p>
																			<p class="info-little">包装：<?php echo ($fks['fbao']); ?></p>
																		</a>
																	</div>
																</div>
															</li>
															<li class="td td-price">
																<div class="item-price">
																	<?php echo ($fks['pmoney']); ?>
																</div>
															</li>
															<li class="td td-number">
																<div class="item-number">
																	<span>×</span><?php echo ($fks['num']); ?>
																</div>
															</li>
															<li class="td td-operation">
																<div class="item-operation">
																	<a href="refund.html">退款</a>
																</div>
															</li>
														</ul>
<!-- 待发货区域详情结束 --><?php endforeach; endif; ?>
													</div>
													<div class="order-right">
														<li class="td td-amount">
															<div class="item-amount">
																合计：<?php echo ($fk['productmoney']); ?>
																<p>含运费：<span>10.00</span></p>
															</div>
														</li>
														<div class="move-right">
															<li class="td td-status">
																<div class="item-status">
																	<p class="Mystatus">买家已付款</p>
																	<p class="order-info"><a href="orderinfo.html">订单详情</a></p>
																</div>
															</li>
															<li class="td td-change">
																<div class="am-btn am-btn-danger anniu">
																	<a style="color: #fff" href="<?php echo U('Usercenter/cuidan', ['id' => $fk['id']]);?>">提醒发货</a></div>
															</li>
														</div>
													</div>
												</div>
											</div>
	<!-- 发货区域开始 --><?php endforeach; endif; ?>
										</div>
									</div>
								</div>
								<div class="am-tab-panel am-fade" id="tab4">
									<div class="order-top">
										<div class="th th-item">
											<td class="td-inner">商品</td>
										</div>
										<div class="th th-price">
											<td class="td-inner">单价</td>
										</div>
										<div class="th th-number">
											<td class="td-inner">数量</td>
										</div>
										<div class="th th-operation">
											<td class="td-inner">商品操作</td>
										</div>
										<div class="th th-amount">
											<td class="td-inner">合计</td>
										</div>
										<div class="th th-status">
											<td class="td-inner">交易状态</td>
										</div>
										<div class="th th-change">
											<td class="td-inner">交易操作</td>
										</div>
									</div>

									<div class="order-main">
										<div class="order-list">

	<!-- 待收货区域 -->
	<?php if(is_array($orderSh)): foreach($orderSh as $key=>$ss): ?><div class="order-status3">
												<div class="order-title">
													<div class="dd-num">订单编号：<a href="javascript:;"><?php echo ($ss['id']); ?></a></div>
													<span>成交时间：<?php echo ($ss['atime']); ?></span>
													<!--    <em>店铺：小桔灯</em>-->
												</div>
												<div class="order-content">
													<div class="order-left">

	<?php if(is_array($orderShList)): foreach($orderShList as $key=>$hh): ?><ul class="item-list">
															<li class="td td-item">
																<div class="item-pic">
																	<a href="#" class="J_MakePoint">
																		<img src="/abc/Public/Home/images/kouhong.jpg_80x80.jpg" class="itempic J_ItemImg">
																	</a>
																</div>
																<div class="item-info">
																	<div class="item-basic-info">
																		<a href="#">
																			<p><?php echo ($hh['pname']); ?></p>
																			<p class="info-little">口味：<?php echo ($hh['fkou']); ?></p>
																			<p class="info-little">包装：<?php echo ($hh['fbao']); ?></p>
																		</a>
																	</div>
																</div>
															</li>
															<li class="td td-price">
																<div class="item-price">
																	<?php echo ($ss['productmoney']); ?>
																</div>
															</li>
															<li class="td td-number">
																<div class="item-number">
																	<span>×</span>2
																</div>
															</li>
															<li class="td td-operation">
																<div class="item-operation">
																	<a href="refund.html">退款/退货</a>
																</div>
															</li>
														</ul><?php endforeach; endif; ?>
														
													</div>
													<div class="order-right">
														<li class="td td-amount">
															<div class="item-amount">
																合计：<?php echo ($hh['productmoney']); ?>
																<p>含运费：<span>10.00</span></p>
															</div>
														</li>
														<div class="move-right">
															<li class="td td-status">
																<div class="item-status">
																	<p class="Mystatus">卖家已发货</p>
																	<p class="order-info"><a href="orderinfo.html">订单详情</a></p>
																	<p class="order-info"><a href="logistics.html">查看物流</a></p>
																	<p class="order-info"><a href="#">延长收货</a></p>
																</div>
															</li>
															<li class="td td-change">
																<div class="am-btn am-btn-danger anniu">
																	<a style="color: #fff;" href="<?php echo U('Usercenter/ok', ['id' => $ss['idd']]);?>">确认收货</a></div>
															</li>
														</div>
													</div>
												</div>
											</div><?php endforeach; endif; ?>
										</div>
									</div>
								</div>

								<div class="am-tab-panel am-fade" id="tab5">
									<div class="order-top">
										<div class="th th-item">
											<td class="td-inner">商品</td>
										</div>
										<div class="th th-price">
											<td class="td-inner">单价</td>
										</div>
										<div class="th th-number">
											<td class="td-inner">数量</td>
										</div>
										<div class="th th-operation">
											<td class="td-inner">商品操作</td>
										</div>
										<div class="th th-amount">
											<td class="td-inner">合计</td>
										</div>
										<div class="th th-status">
											<td class="td-inner">交易状态</td>
										</div>
										<div class="th th-change">
											<td class="td-inner">交易操作</td>
										</div>
									</div>

									<div class="order-main">
										<div class="order-list">
	<!-- 待评价区域 -->
	<?php if(is_array($orderPj)): foreach($orderPj as $key=>$pp): ?><!--不同状态的订单	-->
										<div class="order-status4">
												<div class="order-title">
													<div class="dd-num">订单编号：<a href="javascript:;"><?php echo ($pp['id']); ?></a></div>
													<span>成交时间：<?php echo ($pp['atime']); ?></span>

												</div>
												<div class="order-content">
													<div class="order-left">

	<?php if(is_array($orderPjList)): foreach($orderPjList as $key=>$ll): ?><ul class="item-list">
															<li class="td td-item">
																<div class="item-pic">
																	<a href="#" class="J_MakePoint">
																		<img src="/abc/Public/Home/images/kouhong.jpg_80x80.jpg" class="itempic J_ItemImg">
																	</a>
																</div>
																<div class="item-info">
																	<div class="item-basic-info">
																		<a href="#">
																			<p><?php echo ($hh['pname']); ?></p>
																			<p class="info-little">口味：<?php echo ($ll['fkou']); ?></p>
																			<p class="info-little">包装：<?php echo ($ll['fbao']); ?></p>
																		</a>
																	</div>
																</div>
															</li>
															<li class="td td-price">
																<div class="item-price">
																	<?php echo ($ll['pmoney']); ?>
																</div>
															</li>
															<li class="td td-number">
																<div class="item-number">
																	<span>×</span><?php echo ($ll['num']); ?>
																</div>
															</li>
															<li class="td td-operation">
																<div class="item-operation">
																	<a href="refund.html">退款/退货</a>
																</div>
															</li>
														</ul><?php endforeach; endif; ?>
													</div>
													<div class="order-right">
														<li class="td td-amount">
															<div class="item-amount">
																合计：<?php echo ($pp['productmoney']); ?>
																<p>含运费：<span>10.00</span></p>
															</div>
														</li>
														<div class="move-right">
															<li class="td td-status">
																<div class="item-status">
																	<p class="Mystatus">交易成功</p>
																	<p class="order-info"><a href="orderinfo.html">订单详情</a></p>
																	<p class="order-info"><a href="logistics.html">查看物流</a></p>
																</div>
															</li>
															<li class="td td-change">
																<a href="<?php echo U('Usercenter/commentlist', ['id' => $pp['id']]);?>">
																	<div class="am-btn am-btn-danger anniu">
																		评价商品</div>
																</a>
															</li>
														</div>
													</div>
												</div>
											</div><?php endforeach; endif; ?>	

										</div>

									</div>

								</div>
							</div>

						</div>
					</div>
				</div>
				<!--底部-->
			
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