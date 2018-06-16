<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<link href="/Public/Home/css/optstyle.css" rel="stylesheet" type="text/css" />
		<link href="/Public/Home/basic/css/demo.css" rel="stylesheet" type="text/css" />
		<link href="/Public/Home/css/cartstyle.css" rel="stylesheet" type="text/css" />
		<link href="/Public/Home/AmazeUI-2.4.2/assets/css/amazeui.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="/Public/Home/js/jquery.js"></script>
		 <script src="/Public/Admin/js/jquery-1.9.1.min.js"></script>
       <script src="/Public/Admin/assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/Public/Admin/Widget/Validform/5.3.2/Validform.min.js"></script>
       	<script src="/Public/Admin/assets/js/typeahead-bs2.min.js"></script>           	
       	<script src="/Public/Admin/assets/js/jquery.dataTables.min.js"></script>
       	<script src="/Public/Admin/assets/js/jquery.dataTables.bootstrap.js"></script>
        <script src="/Public/Admin/assets/layer/layer.js" type="text/javascript" ></script>          
       	<script src="/Public/Admin/js/lrtk.js" type="text/javascript" ></script>
        <script src="/Public/Admin/assets/layer/layer.js" type="text/javascript"></script>

	</head>

	<body>
	
				
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
			
			<div class="clear"></div>

			<!--购物车 -->
			<div class="concent">
				<div id="cartTable">
					<div class="cart-table-th">
						<div class="wp">
							<div class="th th-chk">
								<div id="J_SelectAll1" class="select-all J_SelectAll">

								</div>
							</div>
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
							<div class="th th-op">
								<div class="td-inner">操作</div>
							</div>
						</div>
					</div>
					
					
			<!-- 遍历开始 -->
					<tr class="item-list">

						<div class="bundle  bundle-last ">

							<div class="bundle-hd">

								<div class="bd-promos">

									<div class="bd-has-promo">已享优惠:<span class="bd-has-promo-content">省￥19.50</span>&nbsp;&nbsp;</div>

									<div class="act-promo">
										<a href="#" target="_blank">第二支半价，第三支免费<span class="gt">&gt;&gt;</span></a>
									</div>

									<span class="list-change theme-login">编辑</span>

								</div>

							</div>

							<div class="clear"></div>

							<div class="bundle-main">

									<?php if(is_array($redis)): foreach($redis as $key=>$v): ?><ul class="item-content clearfix">
									<li class="td td-chk">

										<div class="cart-checkbox ">

											<input class="check" id="J_CheckBox_170769542747" name="items[]" value="170769542747" type="checkbox" checked data-left="<?php echo ($v['id']); ?>">
											<label for="J_CheckBox_170769542747"></label>

										</div>

									</li>

									<li class="td td-item">

										<div class="item-pic">

											<a href="#" target="_blank" data-title="美康粉黛醉美东方唇膏口红正品 持久保湿滋润防水不掉色护唇彩妆" class="J_MakePoint" data-point="tbcart.8.12">
											<img src="/Public/<?php echo ($v['src']); ?>" class="itempic J_ItemImg"></a>

										</div>

										<div class="item-info">

											<div class="item-basic-info">

												<a href="#" target="_blank" title="美康粉黛醉美唇膏 持久保湿滋润防水不掉色" class="item-title J_MakePoint" data-point="tbcart.8.11"><?php echo ($v['name']); ?></a>

											</div>

										</div>

									</li>

									<li class="td td-info">
										<div class="item-props item-props-can" style="margin-top: 15px;">
											<span class="sku-line">包装：<?php echo ($v['baozhuang']); ?></span><br>
											<span class="sku-line">口味：<?php echo ($v['kouwei']); ?></span>
											<!-- <span tabindex="0" class="btn-edit-sku theme-login">修改</span> -->
											<i class="theme-login am-icon-sort-desc"></i>
										</div>
									</li>

									<li class="td td-price">
										<div class="item-price price-promo-promo">
											<div class="price-content">
												<div class="price-line">
													<em class="price-original"></em>
												</div>
												<div class="price-line" style="margin-top: 20px;margin-right: 18px;">
													<em class="J_Price price-now" tabindex="0" id="a<?php echo ($v['id']); ?>"><?php echo ($v['price']); ?></em>
												</div>
											</div>
										</div>
									</li>

									<li class="td td-amount">
										<div class="amount-wrapper ">
											<div class="item-amount ">
												<div class="sl" style="margin-top: 10px;">
													<input class="min am-btn jian" type="button" data-left="-<?php echo ($v['id']); ?>" value="-"  />
													<input class="text_box" name="" type="text" value="<?php echo ($v['num']); ?>" style="width:30px;" onkeyup="inputs(this, <?php echo ($v['id']); ?>)" id="s<?php echo ($v['id']); ?>"/>
													<?php if(($v['jin'] == 1)): ?><input class="add am-btn jia" type="button" value="+" data-left="j<?php echo ($v['id']); ?>" disabled="disabled" /><br id="kucun">
													<?php else: ?>
														<input class="add am-btn jia" type="button" value="+" data-left="j<?php echo ($v['id']); ?>"/><br id="kucun"><?php endif; ?>
													<?php if(($v['ku'] == 1)): ?><div id="ss">库存不足</div><?php endif; ?>
												</div>
											</div>
										</div>
									</li>
									
									<li class="td td-sum">
										<div class="td-inner" style="margin-top: 16px;">
											<em tabindex="0" class="J_ItemSum number" id="<?php echo ($v['id']); ?>"><?php echo ($v['price'] * $v['num']); ?></em>
										</div>
									</li>
									
									<li class="td td-op">
										<div class="td-inner">
											<a title="移入收藏夹" class="btn-fav" href="#">移入收藏夹</a>
											<a href="javascript:;" data-point-url="#" class="delete" onClick="delete_goods(this, <?php echo ($v['id']); ?>)" >删除</a>
										</div>
									</li>
								</ul><?php endforeach; endif; ?>

							</div>

						</div>

					</tr>

				</div>				
				<div class="clear"></div>
				<!--遍历结束  -->
				<form id="abc" method="post" action="<?php echo U('Pay/index');?>">
				<input type="hidden" name="id" id="inputs">
				<div class="float-bar-wrapper">
					<div id="J_SelectAll2" class="select-all J_SelectAll">
						<div class="cart-checkbox">
							<input class="check-all check" id="J_SelectAllCbx2" name="select-all" value="true" type="checkbox">
							<label for="J_SelectAllCbx2"></label>
						</div>
						<span>全选</span>
					</div>
					<div class="operations">
						<a href="#" hidefocus="true" class="deleteAll">删除</a>
						<a href="#" hidefocus="true" class="J_BatchFav">移入收藏夹</a>
					</div>

					<div class="float-bar-right">
						<div class="amount-sum">
							<span class="txt">已选商品</span>
							<em id="J_SelectedItemsCount">0</em><span class="txt">件</span>
							<div class="arrow-box">
								<span class="selected-items-arrow"></span>
								<span class="arrow"></span>
							</div>
						</div>
						<div class="price-sum">
							<span class="txt">合计:</span>
							<strong class="price">¥<em id="J_Total">0.00</em></strong>
						</div>
						<!-- <button type="submit" style="border:0px;" disabled="disabled" id="buttons"> -->
						<div class="btn-area">
							<a id="J_Go" class="submit-btn submit-btn-disabled" aria-label="请注意如果没有选择宝贝，将无法结算" onClick="go()">
								<span>结&nbsp;算</span></a>
						</div>
						<!-- </button> -->
					</div>
				</div>
				</div>
				</div>
		<div class="footer">

						<div  style="height:200px;margin-left:170px;margin-top: 30px;">
						<div class="bnav1" style="display: block;position: absolute;">

				<h3><b></b> <em style="font-size: 18px;">购物指南</em></h3>
				<ul class="ulul">
					<li style="margin-top:10px";><a href="" style="color: rgb(102,102,102);">购物流程</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">会员介绍</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">常见问题</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">快速运输</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">联系客服</a></li>
				</ul>
			</div>
			<div class="bnav2" style="margin-left:200px; display: block;position: absolute;">
				<h3><b></b> <em style="font-size: 18px;">配送方式</em></h3>
				<ul class="ulul">
					<li style="margin-top:10px";><a href="" style="color: rgb(102,102,102);">上门自取</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">211限时达</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">配送服务查询</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">配送费收取标准</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">海外配送</a></li>
				</ul>
			</div>
			<div class="bnav3" style="margin-left:400px; display: block;position: absolute;">
				<h3><b></b> <em style="font-size: 20px;">支付方式</em></h3>
				<ul class="ulul"> 
					<li style="margin-top:10px";><a href="" style="color: rgb(102,102,102);">货到付款</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">在线支付</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">分期付款</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">邮局汇款</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">公司转账</a></li>
				</ul>
			</div>
			<div class="bnav4" style="margin-left:600px; display: block;position: absolute;">
				<h3><b></b> <em style="font-size: 18px;">售后服务</em></h3>
				<ul class="ulul">
					<li style="margin-top:10px";><a href="" style="color: rgb(102,102,102);">售后政策</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">价格保护</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">退款说明</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">返修/退换货</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">取消订单</a></li>
				</ul>
			</div>
			<div class="bnav5" style="margin-left:800px; display: block;position: absolute;">
				<h3><b></b> <em style="font-size: 18px;">特色服务</em></h3>
				<ul class="ulul">
					<li style="margin-top:10px";><a href="" style="color: rgb(102,102,102);">夺宝岛</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">DIY装机</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">延保服务</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">拉拉E卡</a></li>
					<li style="margin-top:4px";><a href="" style="color: rgb(102,102,102);">拉拉通信</a></li>
				</ul>
			</div>
		</div>
		<!-- </div> -->
						<div class="footer-bd ">
							<p style="margin:0 auto;width:31%;">
								<a href="# ">关于我们</a>
								<a href="# ">联系我们</a>
								<a href="# ">联系客服</a>
								<a href="# ">合作招商</a>
								<a href="# ">营销中心</a>
								<a href="# ">友情链接</a>
								<br>
								<em style="margin:31%;">© 2015-2025 版权所有</em>
							</p>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		</div>

								<!-- <div class="footer">
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
							<em>© 2015-2025 哈色哥<a href="http://www.cssmoban.com/" target="_blank"></a> - Collect from <a href="http://www.cssmoban.com/"  target="_blank">☺</a></em>
						</p>
					</div>
				</div>

			</div> -->

			<!--操作页面-->

			<!-- <div class="theme-popover-mask"></div>
			<div class="theme-popover">
				<div class="theme-span"></div>
				<div class="theme-poptit h-title">
					<a href="javascript:;" title="关闭" class="close">×</a>
				</div>
				<div class="theme-popbod dform">
					<form class="theme-signin" name="loginform" action="" method="post">

						<div class="theme-signin-left">

							<li class="theme-options">
								<div class="cart-title">颜色：</div>
								<ul>
									<li class="sku-line selected">12#川南玛瑙<i></i></li>
									<li class="sku-line">10#蜜橘色+17#樱花粉<i></i></li>
								</ul>
							</li>
							<li class="theme-options">
								<div class="cart-title">包装：</div>
								<ul>
									<li class="sku-line selected">包装：裸装<i></i></li>
									<li class="sku-line">两支手袋装（送彩带）<i></i></li>
								</ul>
							</li>
							<div class="theme-options">
								<div class="cart-title number">数量</div>
								<dd>
									<input class="min am-btn am-btn-default" name="" type="button" value="-" />
									<input class="text_box" name="" type="text" value="1" style="width:30px;" />
									<input class="add am-btn am-btn-default" name="" type="button" value="+" />
									<span  class="tb-hidden">库存<span class="stock">1000</span>件</span>
								</dd>

							</div>
							<div class="clear"></div>
							<div class="btn-op">
								<div class="btn am-btn am-btn-warning">确认</div>
								<div class="btn close am-btn am-btn-warning">取消</div>
							</div>

						</div>
						<div class="theme-signin-right">
							<div class="img-info">
								<img src="/Public/Home/images/kouhong.jpg_80x80.jpg" />
							</div>
							<div class="text-info">
								<span class="J_Price price-now">¥39.00</span>
								<span id="Stock" class="tb-hidden">库存<span class="stock">1000</span>件</span>
							</div>
						</div>

					</form>
				</div>
			</div> -->
		<!--引导 -->
		<div class="navCir">
			<li><a href="home2.html"><i class="am-icon-home "></i>首页</a></li>
			<li><a href="sort.html"><i class="am-icon-list"></i>分类</a></li>
			<li class="active"><a href="shopcart.html"><i class="am-icon-shopping-basket"></i>购物车</a></li>	
			<li><a href="../person/index.html"><i class="am-icon-user"></i>我的</a></li>					
		</div>
	</body>
	
</html>


<script>
	window.onload=function() {
		$('.ulul').css('padding-left', '0px');
	}
	//删除事件->购物车元素
	function delete_goods(obj,id){
		layer.confirm('确定要删除吗',function(index){
			console.log(id);
			$.ajax({
				url:"<?php echo U('Shopcart/del');?>",
				type:'get',
				data:{id:+id},
				success:function(res) {
					console.log(res);
				}
			})

			$(obj).parent().parent().parent().remove();
			layer.msg('已删除!',{icon: 6,time:1000});
			var put = $("input[class=check]:checked").length;
			var arr = 0;
			for (i=0;i<put;i++) {
				arr+= parseInt($("input[class=check]:checked").eq(i).parent().parent().next().next().next().next().next().children().children().html());
			}
			$('#J_Total').html(arr.toFixed(2));
		})
	}

	//减少商品处理
	$('.jian').click(function(event) {
		$('#ss').remove();
		if ($(this).next().val()-2 == '0'){
			$(this).attr('disabled', true);
		}
		if ($(this).next().val()-1 == '0') {
			$(this).next().val(2);
		} else {
			var id = $(this).attr('data-left');
			var bs = $(this).next().next();
			// console.log(bs.parent().append('<div id="aa">库存不足</div>'));
			$.ajax({
				url: "<?php echo U('Shopcart/jian');?>",
				type: 'get',
				data: {id:+id},
				async:false,
				success:function(res) {
					if (res == '1') {
						bs.removeAttr("disabled");
						bs.next().next().remove();
					} else {
						if (res == '2') {
							bs.next().next().remove();
							// bs.parent().append('<div id="aa">库存不足</div>');
						} else if (res == '3') {
							bs.attr("disabled", true);
							bs.next().next().remove();
						} else {
							bs.removeAttr("disabled");
							bs.next().next().remove();
						}
					}
				}
			})
		}
			var price = $(this).parent().parent().parent().parent().prev().children().children().children().next().children().html();
			// console.log(price);
			var num = $(this).next().val()-1;
			// console.log(num);
			$(this).parent().parent().parent().parent().next().children().children().html(price * num);
			// var all = 0;
			// for (i=0;i<len;i++) {
			// 	all+= parseInt($('.J_ItemSum').eq(i).html());
			// }
			var put = $("input[class=check]:checked").length;
			var arr = 0;
			for (i=0;i<put;i++) {
				arr+= parseInt($("input[class=check]:checked").eq(i).parent().parent().next().next().next().next().next().children().children().html());
			}
			$('#J_Total').html(arr.toFixed(2));
	})

	//输入商品数量处理
	function inputs($this, $id) {
		var re = "^[0-9]*[1-9][0-9]*$";
		// console.log($($this).val());
		if (!$($this).val() == '') {
			var number = parseInt($($this).val());
			if (!(/(^[1-9]\d*$)/.test(number))) {
					$($this).val(1);
			}	
		} else {
			return;
		}				
		if ($($this).val() == 1) {
			$($this).prev().attr("disabled", true);
		}

		if ($($this).val() > 100) {
			$($this).val(100);
			alert('数量不能大于100');
		} 
		$($this).val(parseInt($($this).val()));

		var val = $($this).val();

		// console.log(val);
		// return;
		$.ajax({
				url: "<?php echo U('Shopcart/zy');?>",
				type: 'get',
				data: {id:$id, num:val},
				async: false,
				success:function(res) {
					// console.log(res);
					if (res == '-1') {
						$($this).next().attr("disabled", true);
					} else if (res == '-2') {
						$($this).next().removeAttr('disabled');
						$('#ss').remove();
					} else {
						$('#ss').remove();
						$($this).val(res);
						$($this).parent().append('<div id="ss">最多只能购买'+res+'</div>');
						$($this).next().attr("disabled", true);
						$($this).prev().removeAttr('disabled');
					}
				}
			})

		var price = $('#'+'a'+$id).html();
		// console.log(price);
		var num = $($this).val();
		$('#'+$id).html(num * price);
		var put = $("input[class=check]:checked").length;
		var arr = 0;
			for (i=0;i<put;i++) {
				arr+= parseInt($("input[class=check]:checked").eq(i).parent().parent().next().next().next().next().next().children().children().html());
			}
		$('#J_Total').html(arr.toFixed(2));
	}

	//增加商品处理
	$('.jia').click(function(event) {
		if (parseInt($(this).prev().val())+1 == '2') {
			$(this).prev().prev().removeAttr('disabled');
		}
		if (parseInt($(this).prev().val())+1 == '101') {
			parseInt($(this).prev().val(99));
		} else {
			var id = $(this).attr('data-left');
			var bs = $(this);
			$.ajax({
				url: "<?php echo U('Shopcart/jia');?>",
				type: 'get',
				data: {id:id},
				async: false,
				success:function(res) {
					if (res == '-1') {
						bs.attr("disabled", true);
					}
				}
			})
			var price = $(this).parent().parent().parent().parent().prev().children().children().children().next().children().html();
			var num = parseInt($(this).prev().val())+1;
			$(this).parent().parent().parent().parent().next().children().children().html(price * num);
			var put = $("input[class=check]:checked").length;
			var arr = 0;
			for (i=0;i<put;i++) {
				arr+= parseInt($("input[class=check]:checked").eq(i).parent().parent().next().next().next().next().next().children().children().html());
			}
			$('#J_Total').html(arr.toFixed(2));
		}
		
	})

	$('.text_box').focus(function() {
		quan = $(this).val();
	})

	$('.text_box').change(function() {
		var len = $('.text_box').length;
		var val = $(this).val();
		for(i=0;i<len;i++) {
			if (!(/(^[1-9]\d*$)/.test(val))) {
				$(this).val(quan);
			}
		}
	})
	

	//点击input框计算价钱
	$("input[class=check]").click(function() {
		var arr = 0;
		var put = $("input[class=check]:checked").length;
		for (i=0;i<put;i++) {
			arr+= parseInt($("input[class=check]:checked").eq(i).parent().parent().next().next().next().next().next().children().children().html());
		}
		$('#J_Total').html(arr.toFixed(2));
	})


	//计算总价
	var len = $('.J_ItemSum').length;
	var all = 0;
	for (i=0;i<len;i++) {
		all+= parseInt($('.J_ItemSum').eq(i).html());
	}
	$('#J_Total').html(all.toFixed(2));
	
	
	
	function go() {
		$.ajax({
			url: "<?php echo U('Shopcart/go');?>",
				type: 'get',
				// data: {id:id},
				async:false,
				success:function(res) {
					if (res == '1') {
						alert('你未登录,请先登录');
						window.location.href = '<?php echo U("Login/login");?>';
						// 跳转到登录页
					}
				}
		})
		// console.log($("input[class=check]:checked").eq(0).attr('data-left'));
		var len = $("input[class=check]:checked").length;
		var arr = new Array();
		for (i=0;i<len;i++) {
			var id = $("input[class=check]:checked").eq(i).attr('data-left');
			var v = $('#s' + id).val();
			var pin = id+ '.' +v;
			arr[i] = pin;
			// console.log(id,v);
			// $("input[class=check]:checked");
		}
		$.ajax({
			url: "<?php echo U('Shopcart/go');?>",
			type: 'post',
			data: {id:arr},
			async:false,
			success:function(res) {
				// console.log(res.length);
				// console.log(res[0]);
				// console.log();
				brr = new Array();
				if (res == '2') {
					// alert(1);
					var len = $("input[class=check]:checked").length;;
					for(i=0;i<len;i++) {
						// console.log($('.item-content').eq(0));
						//获取ID
						var id = $("input[class=check]:checked").eq(i).attr('data-left');
						// var id = $('.item-content').eq(i).children().children().children().attr('data-left');
						//获取数量
						// var num = $('.item-content').children('li:last-child').eq(i).prev().prev().children().children().children().children().eq(1).val();
						var v = $('#s' + id).val();
						var pin = id + '.' + v;
						brr[i] = pin;
						// res.url = "<?php echo U('Pay/index', ['id'=>1]);?>";
					}	
						if (brr.length == 0) {
							alert('你未选中任何商品');
						} else {
							$('#inputs').val(brr);
							$('.btn-area').wrap('<button type="submit" style="border:0px;"id="buttons"></button>');
							$('#abc').submit();
						}
						// console.log(brr);
						// console.log($('#inputs').val(brr));
						// $('#buttons').removeAttr('disabled');
						// var sss = JSON.stringify(brr);
						// $.post("<?php echo U('Pay/index');?>", "brr", function(){

						// }, 'josn');
						// var ss = JSON.stringify(brr);
						// console.log(sss);
						// window.location.href = '<?php echo U("Pay/index");?>?id='+brr+'';
						// console.log(brr);
				} else {
					for (i=0;i<res.length;i++) {
					// $('#s'+res[i]).parent().css("margin-top", 16);
					// $('#s'+res[i]).parent().html('库存不足');
					$('#kucun').next().remove();
					$('#s'+res[i]).parent().append('<div id="aa">库存不足</div>');
					$('#s'+res[i]).next().attr("disabled", true);
					alert('部分商品库存不足,无法提交');
					}
				}
			}
		})

		// $.ajax({
		// 	url: "<?php echo U('Pay/index');?>",
		// 	type: 'post',
		// 	data: {id:brr},
		// 	async:false,
		// 	success:function(res) {
		// 		$this-> 
		// 	}
		// })


	}
</script>