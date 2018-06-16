<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0 ,minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<title>结算页面</title>

		<link href="/abc/Public/Home/AmazeUI-2.4.2/assets/css/amazeui.css" rel="stylesheet" type="text/css" />

		<link href="/abc/Public/Home/basic/css/demo.css" rel="stylesheet" type="text/css" />
		<link href="/abc/Public/Home/css/cartstyle.css" rel="stylesheet" type="text/css" />

		<link href="/abc/Public/Home/css/jsstyle.css" rel="stylesheet" type="text/css" />

		<script type="text/javascript" src="/abc/Public/Home/js/address.js"></script>

	</head>

	<body>

		<!--顶部导航条 -->
		<div class="am-container header">
			<ul class="message-l">
				<div class="topMessage">
					<div class="menu-hd">
						<a href="#" target="_top" class="h">亲，请登录</a>
						<a href="#" target="_top">免费注册</a>
					</div>
				</div>
			</ul>
			<ul class="message-r">
				<div class="topMessage home">
					<div class="menu-hd"><a href="#" target="_top" class="h">商城首页</a></div>
				</div>
				<div class="topMessage my-shangcheng">
					<div class="menu-hd MyShangcheng"><a href="#" target="_top"><i class="am-icon-user am-icon-fw"></i>个人中心</a></div>
				</div>
				<div class="topMessage mini-cart">
					<div class="menu-hd"><a id="mc-menu-hd" href="#" target="_top"><i class="am-icon-shopping-cart  am-icon-fw"></i><span>购物车</span><strong id="J_MiniCartNum" class="h">0</strong></a></div>
				</div>
				<div class="topMessage favorite">
					<div class="menu-hd"><a href="#" target="_top"><i class="am-icon-heart am-icon-fw"></i><span>收藏夹</span></a></div>
			</ul>
			</div>

			<!--悬浮搜索框-->

			<div class="nav white">
				<div class="logo"><img src="/abc/Public/Home/images/logo.png" /></div>
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
			<div class="concent">
				<!--地址 -->
				<div class="paycont">
					<div class="address">
						<h3>确认收货地址 </h3>
						<div class="control" onclick="address()">
							<div class="tc-btn createAddr theme-login am-btn am-btn-danger">使用新地址</div>
						</div>
						<div class="clear"></div>
						<ul>
						<?php if(is_array($sel)): foreach($sel as $k=>$v): ?><div class="per-border"></div>
								<?php if($v['isdefault'] == '0'): ?><li class="user-addresslist">
								<?php else: ?>
								<li class="user-addresslist defaultAddr"><?php endif; ?>
								<div class="address-left">
									<div class="user DefaultAddr">
										<!-- <?php echo ($v['id']); ?> -->
										<span class="buy-address-detail">   
                  		<span class="buy-user"><?php echo ($v['username']); ?> </span>
										<span class="buy-phone"><?php echo ($v['userphone']); ?></span>
										</span>
									</div>
									<div cla	ss="default-address DefaultAddr">
										<span class="buy-line-title buy-line-title-type">收货地址：</span>
										<span class="buy--address-detail">
								   <span class="province"><?php echo ($v['area1']); ?></span>省
										<span class="city"><?php echo ($v['area2']); ?></span>市
										<span class="dist"><?php echo ($v['area3']); ?></span>
										<span class="street"><?php echo ($v['address']); ?></span>
										</span>

										</span>
									</div>
									<?php if($v['isdefault'] == '0'): ?><ins class="deftip" style="cursor:pointer;" data-left="<?php echo ($v['id']); ?>">默认地址</ins>
									<?php else: ?>
									<ins class="deftip" style="cursor:pointer;color:red;" data-left="<?php echo ($v['id']); ?>">默认地址</ins><?php endif; ?>
								</div>
								<div class="address-right">
									<a href="../person/address.html">
										<span class="am-icon-angle-right am-icon-lg"></span></a>
								</div>
								<div class="clear"></div>

								<div class="new-addr-btn">
									<a href="#" class="hidden">设为默认</a>
									<span class="new-addr-bar hidden">|</span>
									<a href="javascript:void(0);" onclick="edit(event,<?php echo ($v['id']); ?>)">编辑</a>
									<span class="new-addr-bar">|</span>
									<a href="javascript:void(0);" onclick="delClick(this,<?php echo ($v['id']); ?>, event);">删除</a>
								</div>

							</li><?php endforeach; endif; ?>
							<!-- <div class="per-border"></div>
							<li class="user-addresslist">
								<div class="address-left">
									<div class="user DefaultAddr">

										<span class="buy-address-detail">   
                   <span class="buy-user">艾迪 </span>
										<span class="buy-phone">15877777777</span>
										</span>
									</div>
									<div class="default-address DefaultAddr">
										<span class="buy-line-title buy-line-title-type">收货地址：</span>
										<span class="buy--address-detail">
								   <span class="province">湖北</span>省
										<span class="city">武汉</span>市
										<span class="dist">武昌</span>区
										<span class="street">东湖路75号众环大厦9栋9层999</span>
										</span>

										</span>
									</div>
									<ins class="deftip hidden">默认地址</ins>
								</div>
								<div class="address-right">
									<span class="am-icon-angle-right am-icon-lg"></span>
								</div>
								<div class="clear"></div>

								<div class="new-addr-btn">
									<a href="#">设为默认</a>
									<span class="new-addr-bar">|</span>
									<a href="#">编辑</a>
									<span class="new-addr-bar">|</span>
									<a href="javascript:void(0);"  onclick="delClick(this);">删除</a>
								</div>

							</li> -->

						</ul>

						<div class="clear"></div>
					</div>
					<!--物流 -->
					<div class="logistics">
						<h3>选择物流方式</h3>
						<ul class="op_express_delivery_hot">
							<li data-value="yuantong" class="OP_LOG_BTN selected" data-left="2"><i class="c-gap-right"  style="background-position:0px -468px"></i>圆通<span></span></li>
							<li data-value="shentong" class="OP_LOG_BTN" data-left="5"><i class="c-gap-right" style="background-position:0px -1008px"></i>申通<span></span></li>
							<li data-value="yunda" class="OP_LOG_BTN " data-left="7"><i class="c-gap-right" style="background-position:0px -576px"></i>韵达<span></span></li>
							<li data-value="zhongtong" class="OP_LOG_BTN op_express_delivery_hot_last " data-left="3"><i class="c-gap-right" style="background-position:0px -324px"></i>中通<span></span></li>
							<li data-value="shunfeng" class="OP_LOG_BTN  op_express_delivery_hot_bottom" data-left="4"><i class="c-gap-right" style="background-position:0px -180px"></i>顺丰<span></span></li>
						</ul>
					</div>
					<div class="clear"></div>

					<!--支付方式-->
					<!-- <div class="logistics">
						<h3>选择支付方式</h3>
						<ul class="pay-list">
							<li class="pay card"><img src="/abc/Public/Home/images/wangyin.jpg" />银联<span></span></li>
							<li class="pay qq"><img src="/abc/Public/Home/images/weizhifu.jpg" />微信<span></span></li>
							<li class="pay taobao"><img src="/abc/Public/Home/images/zhifubao.jpg" />支付宝<span></span></li>
						</ul>
					</div>
					<div class="clear"></div> -->

					<!--订单 -->
					<div class="concent">
						<div id="payTable">
							<h3>确认订单信息</h3>
							<div class="cart-table-th">
								<div class="wp">

									<div class="th th-item">
										<div class="td-inner">商品信息</div>
									</div>
									<div class="th th-price">
										<div class="td-inner">单价</div>
									</div>
									<div class="th th-amount">
										<div class="td-inner">数量</div>
									</div>
									<div class="th th-sum">
										<div class="td-inner">金额</div>
									</div>
									<div class="th th-oplist">
										<div class="td-inner">配送方式</div>
									</div>

								</div>
							</div>
							<div class="clear"></div>

							<?php if(is_array($res)): foreach($res as $key=>$v): ?><tr class="item-list">
								<div class="bundle  bundle-last">

									<div class="bundle-main">
										<ul class="item-content clearfix">
											<div class="pay-phone">
												<li class="td td-item">
													<div class="item-pic">
														<a href="#" class="J_MakePoint">
															<img src="/abc/Public/Home/<?php echo ($v['image']); ?>" class="itempic J_ItemImg"></a>
													</div>
													<div class="item-info">
														<div class="item-basic-info">
															<a href="#" class="item-title J_MakePoint" data-point="tbcart.8.11"><?php echo ($v['name']); ?></a>
														</div>
													</div>
												</li>
												<li class="td td-info">
													<div class="item-props">
														<span class="sku-line">包装：<?php echo ($v['baozhuang']); ?></span>
														<span class="sku-line">口味：<?php echo ($v['kouwei']); ?></span>
													</div>
												</li>
												<li class="td td-price">
													<div class="item-price price-promo-promo">
														<div class="price-content">
															<em class="J_Price price-now"><?php echo ($v['price']); ?></em>
														</div>
													</div>
												</li>
											</div>
											<li class="td td-amount">
												<div class="amount-wrapper ">
													<div class="item-amount ">
														<span class="phone-title id" data-left="<?php echo ($v['id']); ?>">购买数量</span>
														<div class="sl" style="color:rgb(60, 60, 60);font-size: 12px;margin-top: 5px;" id="<?php echo ($v['id']); ?>"><?php echo ($v['num']); ?></div>
													</div>
												</div>
											</li>
											<li class="td td-sum">
												<div class="td-inner">
													<em tabindex="0" class="J_ItemSum number"><?php echo ($v['num'] * $v['price']); ?></em>
												</div>
											</li>
											<li class="td td-oplist">
												<div class="td-inner">
													<span class="phone-title">配送方式</span>
													<div class="pay-logis">
														快递<b class="sys_item_freprice"></b>
													</div>
												</div>
											</li>

										</ul>
										<div class="clear"></div>

									</div>
							</tr><?php endforeach; endif; ?>
							<div class="clear"></div>
							</div>

							<!-- <tr id="J_BundleList_s_1911116345_1" class="item-list">
								<div id="J_Bundle_s_1911116345_1_0" class="bundle  bundle-last">
									<div class="bundle-main">
										<ul class="item-content clearfix">
											<div class="pay-phone">
												<li class="td td-item">
													<div class="item-pic">
														<a href="#" class="J_MakePoint">
															<img src="/abc/Public/Home/images/kouhong.jpg_80x80.jpg" class="itempic J_ItemImg"></a>
													</div>
													<div class="item-info">
														<div class="item-basic-info">
															<a href="#" target="_blank" title="美康粉黛醉美唇膏 持久保湿滋润防水不掉色" class="item-title J_MakePoint" data-point="tbcart.8.11">美康粉黛醉美唇膏 持久保湿滋润防水不掉色</a>
														</div>
													</div>
												</li>
												<li class="td td-info">
													<div class="item-props">
														<span class="sku-line">颜色：10#蜜橘色+17#樱花粉</span>
														<span class="sku-line">包装：两支手袋装（送彩带）</span>
													</div>
												</li>
												<li class="td td-price">
													<div class="item-price price-promo-promo">
														<div class="price-content">
															<em class="J_Price price-now">39.00</em>
														</div>
													</div>
												</li>
											</div>

											<li class="td td-amount">
												<div class="amount-wrapper ">
													<div class="item-amount ">
														<span class="phone-title">购买数量</span>
														<div class="sl">
															<input class="min am-btn" name="" type="button" value="-" />
															<input class="text_box" name="" type="text" value="3" style="width:30px;" />
															<input class="add am-btn" name="" type="button" value="+" />
														</div>
													</div>
												</div>
											</li>
											<li class="td td-sum">
												<div class="td-inner">
													<em tabindex="0" class="J_ItemSum number">117.00</em>
												</div>
											</li>
											<li class="td td-oplist">
												<div class="td-inner">
													<span class="phone-title">配送方式</span>
													<div class="pay-logis">
														包邮
													</div>
												</div>
											</li>

										</ul>
										<div class="clear"></div>

									</div>
							</tr> -->
							<!-- </div>
							<div class="clear"></div> -->
							<div class="pay-total">
						<!--留言-->
							<div class="order-extra">
								<div class="order-user-info">
									<div id="holyshit257" class="memo">
										<label>买家留言：</label>
										<input type="text" title="选填,对本次交易的说明（建议填写已经和卖家达成一致的说明）" placeholder="选填,建议填写和卖家达成一致的说明" class="memo-input J_MakePoint c2c-text-default memo-close">
										<div class="msg hidden J-msg">
											<p class="error">最多输入500个字符</p>
										</div>
									</div>
								</div>

							</div>
							<!--优惠券 -->
							<div class="buy-agio">
								<li class="td td-coupon">

									<span class="coupon-title" data-left="<?php echo ($info['coupon']); ?>">优惠券</span>
									<select data-am-selected>
										<option value="a">
											<div class="c-price" id="prices">
												<strong>￥<?php echo ($info['coupon']); ?></strong>
											</div>
											<div class="c-limit">
												【消费满88元可用】
											</div>
										</option>
										<!-- <option value="b" selected>
											<div class="c-price">
												<strong>￥3</strong>
											</div>
											<div class="c-limit">
												【无使用门槛】
											</div>
										</option> -->
									</select>
								</li>

								<!-- <li class="td td-bonus">

									<span class="bonus-title">红包</span>
									<select data-am-selected>
										<option value="a">
											<div class="item-info">
												¥50.00<span>元</span>
											</div>
											<div class="item-remainderprice">
												<span>还剩</span>10.40<span>元</span>
											</div>
										</option>
										<option value="b" selected>
											<div class="item-info">
												¥50.00<span>元</span>
											</div>
											<div class="item-remainderprice">
												<span>还剩</span>50.00<span>元</span>
											</div>
										</option>
									</select>

								</li> -->

							</div>
							<div class="clear"></div>
							</div>
							<!--含运费小计 -->
							<div class="buy-point-discharge ">
								<p class="price g_price ">
									合计（含运费） <span>¥</span><em class="pay-sum"></em> 
								</p>
									
							</div>

							<!--信息 -->
							<div class="order-go clearfix">
								<div class="pay-confirm clearfix">
									<div class="box">
										<div tabindex="0" id="holyshit267" class="realPay"><em class="t">实付款：</em>
											<span class="price g_price ">
                                    <span>¥</span> <em class="style-large-bold-red " id="J_ActualFee">244.00</em>
											</span>
										</div>

										<div id="holyshit268" class="pay-address">

											<p class="buy-footer-address">
												<span class="buy-line-title buy-line-title-type">寄送至：</span>
												<span class="buy--address-detail">
								   <span class="province"><?php echo ($so[0]['area1']); ?></span>省
												<span class="city"><?php echo ($so[0]['area2']); ?></span>市
												<span class="dist"><?php echo ($so[0]['area3']); ?></span>
												<span class="street"><?php echo ($so[0]['address']); ?></span>
												</span>
												</span>
											</p>
											<p class="buy-footer-address">
												<span class="buy-line-title">收货人：</span>
												<span class="buy-address-detail">   
                                         <span class="buy-user"><?php echo ($so[0]['username']); ?> </span>
												<span class="buy-phone"><?php echo ($so[0]['userphone']); ?></span>
												</span>
											</p>
										</div>
									</div>

									<div id="holyshit269" class="submitOrder">
										<div class="go-btn-wrap">
											<a id="J_Go" onclick="submit()" class="btn-go" tabindex="0" title="点击此按钮，提交订单">提交订单</a>
										</div>
									</div>
									<div class="clear"></div>
								</div>
							</div>
						</div>

						<div class="clear"></div>
					</div>
				</div>
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
			<div class="theme-popover-mask" id="add"></div>
			<div class="theme-popover" id="adds">

				<!--标题 -->
				<div class="am-cf am-padding">
					<div class="am-fl am-cf"><strong class="am-text-danger am-text-lg">新增地址</strong> / <small>Add address</small></div>
				</div>
				<hr/>

				<div class="am-u-md-12">
					<form class="am-form am-form-horizontal" id="testForm" action="<?php echo U('Pay/address');?>" method="post">

						<div class="am-form-group">
							<label for="user-name" class="am-form-label" >收件人</label>
							<div class="am-form-content">
								<input type="text" id="user-name" placeholder="收货人" name="username" >
							</div>
						</div>
						<div class="am-form-group">
							<label for="user-phone" class="am-form-label">手机号码</label>
							<div class="am-form-content">
								<input id="user-phone" placeholder="手机号必填" type="text" name="userphone">
							</div>
						</div>

						<div class="am-form-group">
							<label for="user-phone" class="am-form-label">所在地</label>
							<div class="am-form-content address">
								<select data-am-selected name="area1" id="sf" style="border-color: rgb(204,204,204)">
									<option value="">--请选择--</option>
									<?php if(is_array($arr)): foreach($arr as $key=>$v): ?><option value="<?php echo ($v['id']); ?>"><?php echo ($v['area_name']); ?></option><?php endforeach; endif; ?>
								</select>
								<select data-am-selected name="area2" class="city" id="city">
									<option value="">--请选择--</option>
								</select>
								<select data-am-selected name="area3" class="area" id="area">
									<option value="">--请选择--</option>
								</select>
							</div>
						</div>

						<div class="am-form-group">
							<label for="user-intro" class="am-form-label">详细地址</label>
							<div class="am-form-content">
								<textarea class="" rows="3" name="address" id="user-intro" placeholder="输入详细地址"></textarea>
								<small>100字以内写出你的详细地址...</small>
							</div>
						</div>

						<div class="am-form-group theme-poptit">
							<div class="am-u-sm-9 am-u-sm-push-3">
								<button class="asd" style="background:none;background-color:none;border:0;"><div class="am-btn am-btn-danger">保存</div></button>
								<div class="am-btn am-btn-danger close">取消</div>
							</div>
						</div>
					</form>
				</div>

			</div>

			<div class="clear" id="after"></div>


			<!-- 编辑 -->
			<div class="theme-popover-mask edit" id="edit"></div>
			<div class="theme-popover edits" id="edits">

				<!--标题 -->
				<div class="am-cf am-padding">
					<div class="am-fl am-cf"><strong class="am-text-danger am-text-lg">编辑地址</strong> / <small>Add address</small></div>
				</div>
				<hr/>

				<div class="am-u-md-12">
					<form class="am-form am-form-horizontal" id="form" action="<?php echo U('Pay/edit');?>" method="post">
						<input type="hidden" name="id" id="hidden">
						<div class="am-form-group">
							<label for="user-name" class="am-form-label" >收件人</label>
							<div class="am-form-content">
								<input type="text" id="user" placeholder="收货人" name="username" >
							</div>
						</div>
						<div class="am-form-group">
							<label for="user-phone" class="am-form-label">手机号码</label>
							<div class="am-form-content">
								<input id="phone" placeholder="手机号必填" type="text" name="userphone">
							</div>
						</div>

						<div class="am-form-group">
							<label for="user-phone" class="am-form-label">所在地</label>
							<div class="am-form-content address">
								<select data-am-selected name="area1" id="sheng" style="border-color: rgb(204,204,204)">
									<option value="">--请选择--</option>
									<?php if(is_array($arr)): foreach($arr as $key=>$v): ?><option value="<?php echo ($v['id']); ?>"><?php echo ($v['area_name']); ?></option><?php endforeach; endif; ?>
								</select>
								<select data-am-selected name="area2" class="city" id="cheng">
									<option value="">--请选择--</option>
								</select>
								<select data-am-selected name="area3" class="area" id="qu">
									<option value="">--请选择--</option>
								</select>
							</div>
						</div>

						<div class="am-form-group">
							<label for="user-intro" class="am-form-label">详细地址</label>
							<div class="am-form-content">
								<textarea class="" rows="3" name="address" id="intro" placeholder="输入详细地址"></textarea>
								<small>100字以内写出你的详细地址...</small>
							</div>
						</div>

						<div class="am-form-group theme-poptit">
							<div class="am-u-sm-9 am-u-sm-push-3">
								<button class="dsa" style="background:none;background-color:none;border:0;"><div class="am-btn am-btn-danger">保存</div></button>
								<div class="am-btn am-btn-danger close">取消</div>
							</div>
						</div>
					</form>
				</div>

			</div>
			</div>
			<div class="clear"></div>
			
	</body>
	<script>
	$('.asd').click(function() {
			var users = $('#user-name').val();
			var phones  = $('#user-phone').val();
			var sf = $('#sf').val();
			var city = $('#city').val();
			var area = $('#area').val();
			var ress = $('#user-intro').val();
			if (users == '') {
				$('#user-name').css('border-color', 'red');
				return;
			}
			if (phones == '') {
				$('#user-phone').css('border-color', 'red');
				return;
			}
			if (sf == '') {
				$('#sf').css('border-color', 'red');
				return;
			}
			if (city == '') {
				$('#city').css('border-color', 'red');
				return;
			}
			if (area == '') {
				$('#area').css('border-color', 'red');
				return;
			}
			if (ress == '') {
				$('#user-intro').css('border-color', 'red');
				return;
			}
		})

	$('#user-name').change(function() {
		$('#user-name').css('border-color', 'rgb(204,204,204)');
	})

	$('#user-phone').change(function() {
		$('#user-phone').css('border-color', 'rgb(204,204,204)');
	})

	$('#sf').change(function() {
		$('#sf').css('border-color', 'rgb(204,204,204)');
	})

	$('#city').change(function() {
		$('#city').css('border-color', 'rgb(204,204,204)');
	})

	$('#area').change(function() {
		$('#area').css('border-color', 'rgb(204,204,204)');
	})

	$('#user-intro').change(function() {
		$('#user-intro').css('border-color', 'rgb(204,204,204)');
	})

	var testForm = document.getElementById('testForm');
		testForm.sf.onchange = function() {
			testForm.city.length = 1;
			testForm.area.length = 1;
		// console.log(this.value);
			$.ajax({
				url: "<?php echo U('Pay/index');?>",
				type: 'get',
				data: {id:+this.value},
				success:function(res) {
					// var i = 0;
					// console.log(res.length);
					// console.log(res[1]['area_name']);
					for(i=0;i<res.length;i++) {
						console.log(res[i]['area_name']);
						var option = document.createElement('option');
						option.value = res[i]['id'];
						option.innerHTML = res[i]['area_name'];
						testForm.city.appendChild(option);
					}
				}
			})
		}

		testForm.city.onchange = function() {
			testForm.area.length = 1;
			$.ajax({
				url: "<?php echo U('Pay/index');?>",
				type: 'post',
				data: {id:+this.value},
				success:function(res) {
					for(i=0;i<res.length;i++) {
						var option = document.createElement('option');
						option.value = res[i]['id'];
						option.innerHTML = res[i]['area_name'];
						testForm.area.appendChild(option);
					}
				}
			})
		}

		$('#testForm').click(function() {
		//判断是否是汉字、字母组成
				var user = $('#user-name').val();
				// console.log(user);
				var regu = "^[a-zA-Z\u4e00-\u9fa5]+$"; 
				var re = new RegExp(regu);
				var phone  = $('#user-phone').val();
				var sf = $('#sf').val();
				var city = $('#city').val();
				var area = $('#area').val();
				var ress = $('#user-intro').val();
				// $('#sf').css('border-color', 'red');

				if (re.test(user) === true) {

				} else {
					// console.log(re.test(user));
					if (user == '') {
						// console.log(123);
						$('#user-name').attr('placeholder', '请输入收件人');
					} else {
						// console.log('123a');
						// $('input[name=username]').focus().val("");
						$('#user-name').attr('placeholder', '只能输入中文或者英文');
						$('#user-name').css('border-color', 'red');
					}
						$("#user-name").val(""); 
						return false;
				}	
				
				if (phone == '') {
						$('#user-phone').attr('placeholder', '请输入手机号');
						return false;
				} else {
					if (!(/^1[34578]\d{9}$/.test(phone))) {
						$("#user-phone").val("");		
						$('#user-phone').attr('placeholder', '手机号填写错误');
						$('#user-phone').css('border-color', 'red');
						return false;
					}
				}

				if (sf == '') {
					return false;
				}

				if (city == '') {
					return false;
				}

				if (area == '') {
					return false;
				}

				if (ress == '') {
					return false;
				}
				// var sf = $('.sf').val();
				// if (sf == '') {
				// $('#sf').css('border-color', 'red');
						// return false;
				// }
		})

		//设置默认地址
		$('.deftip').click(function() {
			var i = $(this);
			// console.log($(this).attr('data-left'));
			// //取消事件冒泡
			// var e = e || event;
			// e.cancelBubble = true;
			$.ajax({
      			type:'get',
      			url : "<?php echo U('Pay/address');?>",
     			data : {id:+ $(this).attr('data-left')},
      			success: function(res) {
					if (res == '1') {
						$('.deftip').css('color', 'rgb(255,255,255)');
						i.css('color', 'red');
					} else {
						alert('设置失败');
					}
				}
			})
			
		})

		//点击更换地址信息
		$('.user-addresslist').click(function() {
			var name = $(this).children().children().children().children().eq(0).html();
			var phone = $(this).children().children().children().children().eq(1).html();
			var sf = $(this).children().children().eq(1).children().eq(1).children().html();
			var city = $(this).children().children().eq(1).children().eq(1).children().eq(1).html();
			var area = $(this).children().children().eq(1).children().eq(1).children().eq(2).html();
			var ress = $(this).children().children().eq(1).children().eq(1).children().eq(3).html();

			$('.pay-address').children().children().eq(3).children().eq(0).html(name);
			$('.pay-address').children().children().eq(3).children().eq(1).html(phone);
			$('.pay-address').children().children().children().eq(0).html(sf);
			$('.pay-address').children().children().children().eq(1).html(city);
			$('.pay-address').children().children().children().eq(2).html(area);
			$('.pay-address').children().children().children().eq(3).html(ress);
		})

		//删除地址
		function delClick($this, $id, e) {
			if(confirm("确定删除该地址?")){
				$.ajax({
      				type:'get',
      				url : "<?php echo U('Pay/del');?>",
     				data : {id:+$id},
      				success: function(res) {
      					if (res == 'false') {
      						alert('删除失败');
      					} else {
      						$($this).parent().parent().remove();
      	// 					$('.pay-address').children().children().eq(3).children().eq(0).html('');
							// $('.pay-address').children().children().eq(3).children().eq(1).html('');
							// $('.pay-address').children().children().children().eq(0).html('');
							// $('.pay-address').children().children().children().eq(1).html('');
							// $('.pay-address').children().children().children().eq(2).html('');
							// $('.pay-address').children().children().children().eq(3).html('');
      					}
					}
				})　　
			}
			//取消事件冒泡
			var e = e || event;
			e.cancelBubble = true;
		}

		function edit(e,$id) {
			//取消事件冒泡
			var e = e || event;
			e.cancelBubble = true;
			$('#edit').css('display', 'block');
			$('#edits').css('display', 'block');
			$.ajax({
      			type:'get',
      			url : "<?php echo U('Pay/edit');?>",
     			data : {id:+$id},
      			success: function(res) {
      				$('#phone').val(res['userphone']);
      				$('#user').val(res['username']);
      				$('#hidden').val(res['id']);
      			}
      		})
		}






		//修改地址的三级联动
		$('#sheng').change(function() {
			$('#cheng')[0].length = 1;
			$('#qu')[0].length = 1;
			var i = $(this).val();
			$.ajax({
				url: "<?php echo U('Pay/index');?>",
				type: 'get',
				data: {id:+i},
				success:function(res) {
					for(i=0;i<res.length;i++) {
						// console.log(res[i]['area_name']);
						var option = document.createElement('option');
						option.value = res[i]['id'];
						option.innerHTML = res[i]['area_name'];
						$('#cheng').append(option);
					}
				}
			})
		})

		$('#cheng').change(function() {
			$('#qu')[0].length = 1;
			$.ajax({
				url: "<?php echo U('Pay/index');?>",
				type: 'post',
				data: {id:+this.value},
				success:function(res) {
					for(i=0;i<res.length;i++) {
						var option = document.createElement('option');
						option.value = res[i]['id'];
						option.innerHTML = res[i]['area_name'];
						$('#qu').append(option);
					}
				}
			})
		})


		$('.dsa').click(function() {
			var users = $('#user').val();
			var phones  = $('#phone').val();
			var sf = $('#sheng').val();
			var city = $('#cheng').val();
			var area = $('#qu').val();
			var ress = $('#intro').val();
			if (users == '') {
				$('#user').css('border-color', 'red');
				return;
			}
			if (phones == '') {
				$('#user').css('border-color', 'red');
				return;
			}
			if (sf == '') {
				$('#sheng').css('border-color', 'red');
				return;
			}
			if (city == '') {
				$('#cheng').css('border-color', 'red');
				return;
			}
			if (area == '') {
				$('#qu').css('border-color', 'red');
				return;
			}
			if (ress == '') {
				$('#intro').css('border-color', 'red');
				return;
			}
		})

		$('#user').change(function() {
			$('#user').css('border-color', 'rgb(204,204,204)');
		})

		$('#phone').change(function() {
			$('#phone').css('border-color', 'rgb(204,204,204)');
		})

		$('#sheng').change(function() {
			$('#sheng').css('border-color', 'rgb(204,204,204)');
		})

		$('#cheng').change(function() {
			$('#cheng').css('border-color', 'rgb(204,204,204)');
		})

		$('#qu').change(function() {
			$('#qu').css('border-color', 'rgb(204,204,204)');
		})

		$('#intro').change(function() {
			$('#intro').css('border-color', 'rgb(204,204,204)');
		})

		$('#form').click(function() {
		//判断是否是汉字、字母组成
				var user = $('#user').val();
				// console.log(user);
				var regu = "^[a-zA-Z\u4e00-\u9fa5]+$"; 
				var re = new RegExp(regu);
				var phone  = $('#phone').val();
				var sf = $('#sheng').val();
				var city = $('#cheng').val();
				var area = $('#qu').val();
				var ress = $('#intro').val();
				// $('#sf').css('border-color', 'red');

				if (re.test(user) === true) {

				} else {
					// console.log(re.test(user));
					if (user == '') {
						// console.log(123);
						$('#user').attr('placeholder', '请输入收件人');
					} else {
						// console.log('123a');
						// $('input[name=username]').focus().val("");
						$('#user').attr('placeholder', '只能输入中文或者英文');
						$('#user').css('border-color', 'red');
					}
						$("#user").val(""); 
						return false;
				}	
				
				if (phone == '') {
						$('#phone').attr('placeholder', '请输入手机号');
						return false;
				} else {
					if (!(/^1[34578]\d{9}$/.test(phone))) {
						$("#phone").val("");		
						$('#phone').attr('placeholder', '手机号填写错误');
						$('#phone').css('border-color', 'red');
						return false;
					}
				}

				if (sf == '') {
					return false;
				}

				if (city == '') {
					return false;
				}

				if (area == '') {
					return false;
				}

				if (ress == '') {
					return false;
				}
				
		})

		//点击新增地址时，隐藏修改地址
		function address() {
			$('#edit').css('display', 'none');
			$('#edits').css('display', 'none');
		}

		//计算总价
		var total = 0;
		for(i=0;i<$('.number').length;i++) {
			total += parseInt($('.number').eq(i).html());
		}
		if (total >= '88') {
			$('.pay-sum').html(total.toFixed(2));
			total = total - parseInt($('.coupon-title').attr('data-left'));
			$('#J_ActualFee').html(total.toFixed(2));
		} else {
			total = total + 10;
			$('.g_price').append('<div class="ding" style="font-size: 12px;">(若订单不满88元,需10元运费)</div>');
			$('.pay-sum').html(total.toFixed(2));
			$('#J_ActualFee').html(total.toFixed(2));
		}
		// console.log($('.pay-sum').html(total));
		// console.log($('#J_ActualFee').);

		//提交订单
		function submit() {
			var arr = new Array();
			var len = $('.id').length;
			// console.log($('.id').length);
			for(i=0;i<len;i++) {
				var id = $('.id').eq(i).attr('data-left');
				var num = $('.id').eq(i).next().html();
				// console.log(id);
				// console.log(num);
				var pin = id+ '.' +num;
				// console.log(pin);
				arr[i] = pin;
			}

			$.ajax({
			url: "<?php echo U('Pay/query');?>",
			type: 'get',
			data: {id:arr},
			async:false,
			success:function(res) {
				if (res == '2') {
					join();
				} else {
					$('.kc').remove();
					for (i=0;i<res.length;i++) {
					// $('#s'+res[i]).parent().css("margin-top", 16);
					// $('#s'+res[i]).parent().html('库存不足');
					alert('部分商品库存不足,订单提交失败');
					$('#'+res).append('<div class="kc">库存不足</div>');
					}
				}
			}
		})
	}



			// console.log($('.id').next().html());

		//下单
		function join() {
			var arr = new Array();
			var brr = new Array();
			var tot = 0;
			var len = $('.id').length;
			// console.log($('.id').length);
			for(i=0;i<len;i++) {
				//获取商品ID
				var id = $('.id').eq(i).attr('data-left');
				//获取购买数量
				var num = $('.id').eq(i).next().html();
				tot += parseInt($('.id').eq(i).next().html());
				var pin = id+ '.' +num;
				arr[i] = pin;
			}
			// console.log(tot);
			//获取省份
			brr[0] = $('.province').html();
			//获取城市
			brr[1] = $('.city').html();
			//获取区县
			brr[2] = $('.dist').html();
			//获取具体地址
			brr[3] = $('.street').html();
			//获取用户名
			brr[4] = $('.buy-user').html();
			//获取手机
			brr[5] = $('.buy-phone').html();
			//获取实付款
			brr[6] = $('#J_ActualFee').html();
			
			//获取买家留言
			brr[7] = $('.memo-close').val();

			//商品总额
			brr[8] = $('.pay-sum').html();

			//快递公司
			brr[9] = $('.selected').attr('data-left');

			//商品数量
			brr[10] = tot;

			// console.log(brr[9]);
			$.ajax({
				url: "<?php echo U('Pay/join');?>",
				type: 'post',
				data: {id:arr, data:brr},
				success:function(res) {
					window.location.href= '<?php echo U("Orders/success");?>?id='+res[0]['id'];
					// console.log(res);
					//获取商品ID
					// if (res == 'true') {
					// 	window.location.href= '<?php echo U("Login/login");?>';;
					// }
				}
			})
		}
	</script>	
</html>