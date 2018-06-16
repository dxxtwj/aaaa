<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<!-- 继承头部css样式开始 -->

		<link href="/Public/Home/AmazeUI-2.4.2/assets/css/amazeui.css" rel="stylesheet" type="text/css" />
		<link href="/Public/Home/AmazeUI-2.4.2/assets/css/admin.css" rel="stylesheet" type="text/css" />

		<link href="/Public/Home/basic/css/demo.css" rel="stylesheet" type="text/css" />

		<link href="/Public/Home/css/seastyle.css" rel="stylesheet" type="text/css" />

		<script type="text/javascript" src="/Public/Home/basic/js/jquery-1.7.min.js"></script>
		<!-- <script type="text/javascript" src="/Public/Home/js/script.js"></script> -->
		<!-- 引入懒加载文件 -->
		<script src="/Public/Home/js/jquery.lazyload.min.js"></script>


	<body>

		<div class="hmtop">
			<!--顶部导航条 -->
			<div class="am-container header">
				<ul class="message-l">
					<div class="topMessage">
						<div class="menu-hd">
						<?php if(session('user')): ?><a href="<?php echo U('Index/index');?>" title='点击前往商城首页'>&nbsp;欢迎您,</a><a href="<?php echo U('Usercenter/index');?>" title='点击前往个人中心'><?php echo session('user')['username'];?></a> ｜ <a href="<?php echo U('Login/logout');?>" title='点击退出登录'>注销</a>
								<img style="width: 30px; height: 25px;" src="<?php echo session('user')['figureurl_1'];?>" alt="" />
						<?php else: ?>
							<a href="<?php echo U('Index/index');?>" title='点击前往商城首页'>&nbsp;欢迎您来到零食商城,</a>　<a href="<?php echo U('Login/login');?>" title='亲，要登录后才能买东西哦~'>登录</a>　|　<a href="<?php echo U('Login/emailRegister');?>" title='还没账号?点击立即注册'>注册</a><?php endif; ?>
						
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
					<div class="logo"><img src="/Public/Home/images/logo.png" /></div>
				<!-- 	<div class="logoBig">
						<li><img src="/Public/Home/images/logobig.png" /></li>
					</div> -->
					<div jiucuo="<?php echo ($jiuCuoTiShi); ?>" id="sousuo" class="search-bar pr">
						<a name="index_none_header_sysc" href="#"></a>
						<form action="<?php echo U('Goods/index');?>" method="get">
							<input id="searchInput" name="search" type="text" placeholder="搜索" autocomplete="off">
							<input id="ai-topsearch" class="submit am-btn" value="搜索" index="1" type="submit">
						</form>
					</div>
					<div id="div" style="margin-left: 230px; margin-top: -21px; width: 528px; height: 31px; border: 1px solid #000; float: left; position: absolute; display: none;">
							<span id="spanJiuCuo" style=" display: inline-block"></span>

					</div>

				</div>
	<script>

		// 按钮松开触发
		$('#sousuo').keyup(function() {

			// 获取内容
			var jiucuo = $("#searchInput").val();
			$('#div').css('display', 'block');

			var str = '';

			$.ajax({

				type: 'get',

				data: {jiucuo: jiucuo},

				url: "<?php echo U('Goods/search');?>",

				success: function(msg) {
					console.log(msg);
					if (msg) {

						for (var i = 0; i < msg.length; i++) {
							str += '<span style="font-size: 12px; color: #666"></span><a style="color: #666; font-size: 12px;" href="/Home/Goods/index/search/'+msg[i].name+'">'+msg[i].name+'</a>　';
						}
						if (str) {

							// 替换纠错文本
							$('#spanJiuCuo').html("<span style='font-size: 12px; display: inline-block'>您是不是要找：</span>"+str);
						}
					}

				}

			});

		});

		$('#div').mouseout(function() {
			$('#div').css('display', 'none');
		});
		$('#div').mouseover(function() {
			$('#div').css('display', 'block');

		});




	</script>

					<div class="am-g am-g-fixed">
						<div class="am-u-sm-12 am-u-md-12">
	                  	<div class="theme-popover">														
							<div class="searchAbout">
								<span class="font-pale">相关搜索：</span>
								<a title="坚果" href="#">坚果</a>
								<a title="瓜子" href="#">瓜子</a>
								<a title="鸡腿" href="#">豆干</a>

							</div>
							<ul class="select">
								<p class="title font-normal">
									<span class="fl">松子</span>
									<span class="total fl">搜索到<strong class="num">997</strong>件相关商品</span>
								</p>
								<div class="clear"></div>
								
								<div class="clear"></div>
								<li class="select-list">
									<dl id="select2">
										<dt class="am-badge am-round">分类</dt>
										<div class="dd-conent" id="fenlei">
											<dd class="select-all selected"><a href="<?php echo U('Goods/index', ['id' => I('get.id'), 'pid' => 'a']);?>">全部</a></dd>
											<?php if(!empty($sanjiType)): ?><!-- 分类表 -->
												<?php if(is_array($sanjiType)): foreach($sanjiType as $key=>$vv): ?><dd><a onclick="typeId(this, <?php echo ($vv['id']); ?>)" href="<?php echo U('Goods/index', ['id' => $vv['id']]);?>" ><?php echo ($vv['name']); ?></a></dd><?php endforeach; endif; endif; ?>
											
										</div>
									</dl>
								</li>
								<li class="select-list">
									<dl id="select1">
										<dt class="am-badge am-round">品牌</dt>	
									
										 <div class="dd-conent" id="pinpai">										
											<dd class="select-all selected"><a href="<?php echo U('Goods/index', ['id' => I('get.id'), 'pid' => 'a']);?>">全部</a></dd>
											<?php if(!empty($pinpaiList)): ?><!-- 品牌表 -->
											<?php if(is_array($pinpaiList)): foreach($pinpaiList as $key=>$a): ?><dd><a href="<?php echo U('Goods/index', ['id' => I('get.id'), 'pid' => $a['id']]);?>"  ><?php echo ($a['name']); ?></a></dd><?php endforeach; endif; endif; ?>
											
										 </div>
						
									</dl>
								</li>
							</ul>
							<div class="clear"></div>
                        </div>

							<div class="search-content">
								<div class="sort">
									<li class="first"><span href="#" title="综合"><a title="综合从高到低" href="<?php echo U('Goods/index', ['id' => I('get.id'), 'order' => 'collectnum desc']);?>">∧ </a>综合排序<a title="综合从低到高" href="<?php echo U('Goods/index', ['id' => I('get.id'), 'order' => 'collectnum asc']);?>"><span> ∨</span></a></span></li>
									<li><a href="#"></a><span title="销量"><a title="销量从高到低" href="<?php echo U('Goods/index', ['id' => I('get.id'), 'order' => 'sellnum desc']);?>">∧ </a>销量排序</span><a  title="销量从低到高" href="<?php echo U('Goods/index', ['id' => I('get.id'), 'order' => 'sellnum asc']);?>"><span> ∨</span></a></li>
									<li class="big"><span href="#"><a title="评价从高到低" href="<?php echo U('Goods/index', ['id' => I('get.id'), 'order' => 'commentnum desc']);?>">∧ </a>评论数目</span><a title="评论从低到高" href="<?php echo U('Goods/index', ['id' => I('get.id'), 'order' => 'commentnum asc']);?>"><span> ∨</span></a></li>
									<li><span title="价格"><a title="价格从高到低" href="<?php echo U('Goods/index', ['id' => I('get.id'), 'order' => 'money desc']);?>">∧ </a>商品价格<a title="价格从低到高"  href="<?php echo U('Goods/index', ['id' => I('get.id'), 'order' => 'money asc']);?>"> ∨</a></span></li>
								</div>
								<div class="clear"></div>
							<div class="tb">

							<ul class="am-avg-sm-2 am-avg-md-3 am-avg-lg-4 boxes" id="goods">


							<?php if(empty($goodsList)): ?><li><div style="width: 940px; height: 200px; margin: 0px auto;"><h1 style="font-size: 30px; ">暂无商品</h1></div></li>
							<?php else: ?>
								<!-- 商品表 -->
								<?php if(is_array($goodsList)): foreach($goodsList as $key=>$v): ?><li>
											<div class="i-pic limit">
												<a href="<?php echo U('Detail/index', ['id' => $v['id']]);?>"><img class="lazy" src="/Public/Images/LanJiaZai/timg.gif" data-original="/Public/<?php echo ($v['image0']); ?>" /></a>											
												<p class="title fl"><a href="<?php echo U('Detail/index', ['id' => $v['id']]);?>"><span style="font-size: 15px; text-overflow:ellipsis;  white-space:nowrap;  overflow:hidden; display: block"><?php echo ($v['name']); ?></span></a></p>
												<p class="price fl">
													<b>¥</b>
													<strong><a href="<?php echo U('Detail/index', ['id' => $v['id']]);?>"><span style="color: #e4393c;"><?php echo ($v['money']); ?></span></a></strong>
												</p>
												<p class="number fl">

													销量<span><a href="<?php echo U('Detail/index', ['id' => $v['id']]);?>" ><span style="color: #e4393c;"><?php echo ($v['sellnum']); ?></span></span></a>
												</p>
											</div>
										</li><?php endforeach; endif; endif; ?>
								</div>
							</ul>
							</div>

<script>
	$(function() {
		$("img.lazy").lazyload({
			placeholder: "/Public/Images/LanJiaZai/timg.gif",
			effect: "fadeIn",
			threshold: -200,

		});

	});
</script>
							<div class="search-side">

								<div class="side-title">
									推荐商品
								</div>

						<?php if(!empty($tuiGuangList)): if(is_array($tuiGuangList)): foreach($tuiGuangList as $key=>$vg): ?><li>
									<div class="i-pic check">
										<a href="<?php echo U('Detail/index', ['id' => $vg['gid']]);?>"><img class="tuiGuang" data-original="/Public/<?php echo ($vg['image']); ?>" src="/Public/Images/LanJiaZai/timg.gif" /></a>

										<p class="check-title"><span style="font-size: 15px; text-overflow:ellipsis;  white-space:nowrap;  overflow:hidden; display: block"><?php echo ($vg['name']); ?></span></p>
										<p class="price fl">
											<b>¥</b>
											<strong><?php echo ($vg['money']); ?></strong>
										</p>
										<p class="number fl">
											销量<span style="color: #e4393c";><?php echo ($vg['shouliang']); ?></span>
										</p>
									</div>
								</li><?php endforeach; endif; endif; ?>
<script>
	$(function() {
		$("img.tuiGuang").lazyload({
			placeholder: "/Public/Images/LanJiaZai/timg.gif",
			effect: "fadeIn",
			threshold: -200,

		});

	});
</script>
							</div>
							<div class="clear"></div>
							<!--分页 -->
							<ul class="am-pagination am-pagination-right" id="show">
							<!-- 不为空 -->
							<?php if(!empty($show)): echo ($show); endif; ?>
							</ul>

						</div>
					</div>
				
<script>
	// 分页按钮样式
	$('#show a, #show span').unwrap('<div></div>').wrap("<li style='float: left; display:'inline-block' class='btn btn-primary'></li>");
		

	$('body').delegate('#show a, #show span', 'click', function() {
		// 地址
		var url = $(this).attr('href');
	$.ajax({

		// url
		url: url,
		// 类型
		type: 'get',
		
		// 回调
		success: function(msg) {
			
			// 替换按钮
			$('#show').html(msg.show);

			// 设置按钮样式
			$('#show a, #show span').unwrap('<div></div>').wrap("<li style='float: left; display:'inline-block' class='btn btn-primary'></li>");
			
			// 替换商品表
			$('#goods').html(msg.goodsAllList);

		$(function() {
		$("img.lazy").lazyload({
			placeholder: "/Public/Images/LanJiaZai/timg.gif",
			effect: "fadeIn",
			threshold: -200,

		});

	});
		}
	})
		return false;

	});

	// function typeId($a, $id) {
		
	// 	// 获取跳转路径
	// 	url = $($a).attr('ahref');
	// 	console.log(url);
	// 	// get传值
	// 	$.get(url, { id: $id}, function(msg) {

	// 		console.log('123', msg);
	// 	} );

	// 	return false;
	// }

</script>

<!-- 继承首页结束 -->

		<!-- 底部导航 end -->

<!-- 底部继承，用在商品详情页 -->
		
		<!--引导 -->
		<div class="navCir">
			<li class="active"><a href="home2.html"><i class="am-icon-home "></i>首页</a></li>
			<li><a href="sort.html"><i class="am-icon-list"></i>分类</a></li>
			<li><a href="<?php echo U('Shopcart/index');?>"><i class="am-icon-shopping-basket"></i>购物车</a></li>	
			<li><a href="../person/index.html"><i class="am-icon-user"></i>我的</a></li>					
		</div>
		<!--菜单 -->
		<div class=tip>
			<div id="sidebar">
				<div id="wrap">
					<div id="prof" class="item ">
						<a href="# ">
							<span class="setting "></span>
						</a>
						<div class="ibar_login_box status_login ">
							<div class="avatar_box ">
								<p class="avatar_imgbox "><img src="/Public/Home/images/no-img_mid_.jpg " /></p>
								<ul class="user_info ">
									<li>用户名：sl1903</li>
									<li>级&nbsp;别：普通会员</li>
								</ul>
							</div>
							<div class="login_btnbox ">
								<a href="# " class="login_order ">我的订单</a>
								<a href="# " class="login_favorite ">我的收藏</a>
							</div>
							<i class="icon_arrow_white "></i>
						</div>

					</div>
					<div id="shopCart " class="item ">
						<a href="<?php echo U('Shopcart/index');?> ">
							<span class="message "></span>
						</a>
						<p>
							购物车
						</p>
						<p class="cart_num ">0</p>
					</div>
					<div id="asset " class="item ">
						<a href="# ">
							<span class="view "></span>
						</a>
						<div class="mp_tooltip ">
							我的资产
							<i class="icon_arrow_right_black "></i>
						</div>
					</div>

					<div id="foot " class="item ">
						<a href="# ">
							<span class="zuji "></span>
						</a>
						<div class="mp_tooltip ">
							我的足迹
							<i class="icon_arrow_right_black "></i>
						</div>
					</div>

					<div id="brand " class="item ">
						<a href="#">
							<span class="wdsc "><img src="/Public/Home/images/wdsc.png " /></span>
						</a>
						<div class="mp_tooltip ">
							我的收藏
							<i class="icon_arrow_right_black "></i>
						</div>
					</div>

					<div id="broadcast " class="item ">
						<a href="# ">
							<span class="chongzhi "><img src="/Public/Home/images/chongzhi.png " /></span>
						</a>
						<div class="mp_tooltip ">
							我要充值
							<i class="icon_arrow_right_black "></i>
						</div>
					</div>

					<div class="quick_toggle ">
						<li class="qtitem ">
							<a href="# "><span class="kfzx "></span></a>
							<div class="mp_tooltip ">客服中心<i class="icon_arrow_right_black "></i></div>
						</li>
						<!--二维码 -->
						<li class="qtitem ">
							<a href="#none "><span class="mpbtn_qrcode "></span></a>
							<div class="mp_qrcode " style="display:none; "><img src="/Public/Home/images/weixin_code_145.png " /><i class="icon_arrow_white "></i></div>
						</li>
						<li class="qtitem ">
							<a href="#top " class="return_top "><span class="top "></span></a>
						</li>
					</div>

					<!--回到顶部 -->
					<div id="quick_links_pop " class="quick_links_pop hide "></div>

				</div>

			</div>
			<div id="prof-content " class="nav-content ">
				<div class="nav-con-close ">
					<i class="am-icon-angle-right am-icon-fw "></i>
				</div>
				<div>
					我
				</div>
			</div>
			<div id="shopCart-content " class="nav-content ">
				<div class="nav-con-close ">
					<i class="am-icon-angle-right am-icon-fw "></i>
				</div>
				<div>
					购物车
				</div>
			</div>
			<div id="asset-content " class="nav-content ">
				<div class="nav-con-close ">
					<i class="am-icon-angle-right am-icon-fw "></i>
				</div>
				<div>
					资产
				</div>

				<div class="ia-head-list ">
					<a href="# " target="_blank " class="pl ">
						<div class="num ">0</div>
						<div class="text ">优惠券</div>
					</a>
					<a href="# " target="_blank " class="pl ">
						<div class="num ">0</div>
						<div class="text ">红包</div>
					</a>
					<a href="# " target="_blank " class="pl money ">
						<div class="num ">￥0</div>
						<div class="text ">余额</div>
					</a>
				</div>

			</div>
			<div id="foot-content " class="nav-content ">
				<div class="nav-con-close ">
					<i class="am-icon-angle-right am-icon-fw "></i>
				</div>
				<div>
					足迹
				</div>
			</div>
			<div id="brand-content " class="nav-content ">
				<div class="nav-con-close ">
					<i class="am-icon-angle-right am-icon-fw "></i>
				</div>
				<div>
					收藏
				</div>
			</div>
			<div id="broadcast-content " class="nav-content ">
				<div class="nav-con-close ">
					<i class="am-icon-angle-right am-icon-fw "></i>
				</div>
				<div>
					充值
				</div>
			</div>
		</div>

</ul>
		<script>

			window.jQuery || document.write('<script src="/Public/Home/basic/js/jquery.min.js "><\/script>');

		if ($('#less_hour').html() == '到') {
			var time1 = setInterval("timer()",1000);
			$('#font').html('距离活动开始还有');
			function timer(){
				// Date(<?php echo ($arr["year"]); ?>, <?php echo ($arr["month"]); ?>, <?php echo ($arr["day"]); ?>, <?php echo ($arr["time"]); ?>, <?php echo ($arr["branch"]); ?>, <?php echo ($arr["second"]); ?>);
				
				// 	new Date(<?php echo ($arr["year"]); ?>, <?php echo ($arr["month"]); ?>, <?php echo ($arr["day"]); ?>, <?php echo ($arr["time"]); ?>, <?php echo ($arr["branch"]); ?>, <?php echo ($arr["second"]); ?>);
				// 	new Date();
				var ts = (new Date(<?php echo ($arr["year"]); ?>, <?php echo ($arr["month"]); ?>, <?php echo ($arr["day"]); ?>, <?php echo ($arr["time"]); ?>, <?php echo ($arr["branch"]); ?>, <?php echo ($arr["second"]); ?>)) - (new Date());

				if (ts <= 0) {
					return;
				}
				// console.log(ts);
				var hh = parseInt(ts / 1000 / 60 / 60 % 24, 10);
				var mm = parseInt(ts / 1000 / 60 % 60, 10);
				var ss = parseInt((ts / 1000 ) % 60 , 10);
				
				hh = checkTime(hh);
				mm = checkTime(mm);
				ss = checkTime(ss);

				$("#less_hour").html(hh);
				$("#less_minutes").html(mm);
				$("#less_seconds").html(ss);


				

					// console.log($("#less_hour"));
					// console.log($("#less_hour").html('1'));
					// $("#less_minutes").html('行');
					// $("#less_seconds").html('中');

					if (hh == '00' && mm == '00' && ss == '00') {
						clearTimeout(time1);
						var time2 = setTimeout(times(),1000);
						$.ajax({
							url:"<?php echo U('Index/hand');?>",
							type:'get',
							async:false,
							success:function(res) {
								// if (res == '1') {
								// alert('活动已结束!');
								// }
							},
						})
					}
				}

				function checkTime(i){
					if (i < 10) {  
		   				i = "0" + i;  
					}
						return i;
				}
			
		}
		
		// function times(hh, mm, ss) {
		// 	var time2 = setInterval(timesr(hh, mm, ss),1000);
		// }
		// if (hh == '00' && mm == '00' && ss == '00') {
		// 			var time2 = setInterval(times(hh, mm, ss),1000);
		// 		}
			// $("#less_hour").html(hh);
			// 	$("#less_minutes").html(mm);
			// 	$("#less_seconds").html(ss);
			// 	$('#less_hour').html();
			// if (hh == '00' && mm == '00' && ss == '00') {
			// 	var time2 = setInterval(times(),1000);
			if ($('#less_hour').html() == '进') {
				var time2 = setTimeout(times(),1000);
			}

			var i = 0;	
			function times() {
				$('#font').html('距离活动结束还有');
				
					time3 = setInterval(function(){
						console.log(321);
					var ts = (new Date(<?php echo ($brr["year"]); ?>, <?php echo ($brr["month"]); ?>, <?php echo ($brr["day"]); ?>, <?php echo ($brr["time"]); ?>, <?php echo ($brr["branch"]); ?>, <?php echo ($brr["second"]); ?>)) - (new Date());
					// if (ts <= 0) {
					// 	return;
					// }
					// console.log(ts);
					var hhs = parseInt(ts / 1000 / 60 / 60 % 24, 10);
					var mms = parseInt(ts / 1000 / 60 % 60, 10);
					var sss = parseInt((ts / 1000 ) % 60 , 10);

					// console.log(hhs);
					// console.log(mms);
					// console.log(sss);

					hhs = checkTime(hhs);
					mms = checkTime(mms);
					sss = checkTime(sss);

					// console.log(hhs);
					// console.log(mms);
					// console.log(sss);
					$("#less_hour").html(hhs);
					$("#less_minutes").html(mms);
					$("#less_seconds").html(sss);

					if (hhs == '00' && mms == '00' && sss == '00') {
						clearTimeout(time3);
						$.ajax({
						url:"<?php echo U('Index/end');?>",
						type:'get',
						async:false,
						success:function(res) {
							$('#font').remove();
							$("#less_hour").html('已');
							$("#less_minutes").html('结');
							$("#less_seconds").html('束');
						},
					})
					}

					function checkTime(i){
					if (i < 10) {  
		   				i = "0" + i;  
					}
						return i;
				}

				},1000);
				// function ass() {
				// 	console.log(123);
					
				// }
			}
		// }
		


			function miaosha() {
				if ($("#less_hour").html() == '进' && $("#less_minutes").html() == '行' && $("#less_seconds").html() == '中' || $("#less_hour").html() == '00' && $("#less_minutes").html() == '00' && $("#less_seconds").html() == '00') {
					$.ajax({
						url:"<?php echo U('Index/miaosha');?>",
						type:'get',
						success:function(res) {
							if (res == '1') {
							alert('抱歉,你没有抢到,期待你的下次参与!');
							$("#less_hour").html('已');
							$("#less_minutes").html('结');
							$("#less_seconds").html('束');
							// location.reload(true);
							} else if (res == '2') {
								alert('恭喜你,成功抢到商品');
							}
						},
					})
					// location.href="<?php echo U('Index/miaosha');?>";
				} else if ($("#less_hour").html() == '已' && $("#less_minutes").html() == '结' && $("#less_seconds").html() == '束') {
					alert('活动已结束');
				} else {
					alert('秒杀还未开始');
				}	
			}
		</script>

		<script type="text/javascript " src="/Public/Home/basic/js/quick_links.js "></script>
	
	<script type="text/javascript">
		function shui() {
			var one=new Array();
			var one = $('#title1').html('车厘子');
			var one = $('#content1').html('甜又甜,好吃又不贵,买一斤送一斤');
			var one = $('#image1').attr('src', '/Public/Home/images/1.jpg');
			var one = $('#title2').html('玖原农珍');
			var one = $('#content2').html('广西百香果12个 单果60-85g 西番莲水果');
			var one = $('#image2').attr('src', '/Public/Home/images/aa.jpg');
		}

		function yin1() {
				$('#title1').html(yin[0]);
				$('#content1').html(yin[1]);
				$('#image1').attr('src', '/Public/Home/images/'+yin[2]);
				$('#title2').html(yin[3]);
				$('#content2').html(yin[4]);
				$('#image2').attr('src', '/Public/Home/images/'+yin[5]);
		}

		function shu1() {
				$('#title1').html(shu[0]);
				$('#content1').html(shu[1]);
				$('#image1').attr('src', '/Public/Home/images/'+shu[2]);
				$('#title2').html(shu[3]);
				$('#content2').html(shu[4]);
				$('#image2').attr('src', '/Public/Home/images/'+shu[5]);
		}

		function jian1() {
				$('#title1').html(jian[0]);
				$('#content1').html(jian[1]);
				$('#image1').attr('src', '/Public/Home/images/'+jian[2]);
				$('#title2').html(jian[3]);
				$('#content2').html(jian[4]);
				$('#image2').attr('src', '/Public/Home/images/'+jian[5]);
		}

		window.onload = function() {
			i = 0;
			$(window).scroll(function() {
				var ssa1 = $('.ssa1').attr('data-left');
				var ssa2 = $('.ssa2').attr('data-left');
				// console.log(ls);
				if ($(window).scrollTop() >= ssa1 && i == '0') {
					if ($(window).scrollTop() >= ssa1 && $(window).scrollTop() <= ssa2) {
						$('.ssa1').css('z-index', -100);
					} else {
							i = 1;
						$.ajax({
							url: "<?php echo U('Index/gif');?>",
							type: 'get',
							// data: {id:+id},
							success:function(res) {
								var src = "\\\\Public\\";
								console.log(res[0]['image0']);

								// // console.log(123);
								$('#11').attr('src', src+res[0]['image0']);
								$('#22').attr('src', src+res[1]['image0']);
								$('#33').attr('src', src+res[2]['image0']);
								$('#44').attr('src', src+res[3]['image0']);
								$('#55').attr('src', src+res[4]['image0']);
								$('#66').attr('src', src+res[5]['image0']);
								$('#77').attr('src', src+res[6]['image0']);
								$('.ssa2').css('z-index', -100);
							},	
						})
					}
					
				}
			})
		}
	</script>
	</body>

</html>