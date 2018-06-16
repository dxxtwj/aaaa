<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<!-- 继承头部css样式开始 -->

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<title>首页</title>


		<link href="/abc/Public/Home/AmazeUI-2.4.2/assets/css/amazeui.css" rel="stylesheet" type="text/css" />
		<link href="/abc/Public/Home/AmazeUI-2.4.2/assets/css/admin.css" rel="stylesheet" type="text/css" />

		<link href="/abc/Public/Home/basic/css/demo.css" rel="stylesheet" type="text/css" />

		<link href="/abc/Public/Home/css/hmstyle.css" rel="stylesheet" type="text/css" />
		<script src="/abc/Public/Home/AmazeUI-2.4.2/assets/js/jquery.min.js"></script>
		<script src="/abc/Public/Home/AmazeUI-2.4.2/assets/js/amazeui.min.js"></script>
		<style>
		</style>
	</head>
<!-- 头部css样式继承结束 -->


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
					<div class="logo"><img src="/abc/Public/Home/images/logo.png" /></div>
				<!-- 	<div class="logoBig">
						<li><img src="/abc/Public/Home/images/logobig.png" /></li>
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
							str += '<span style="font-size: 12px; color: #666"></span><a style="color: #666; font-size: 12px;" href="/abc/Home/Index/index/search/'+msg[i].name+'">'+msg[i].name+'</a>　';
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

             <b class="line"></b>
			<div class="shopNav">
				<div class="slideall">
			        
					   <div class="long-title"><span class="all-goods">全部分类</span></div>
					   <div class="nav-cont">
							<ul>
								<li class="index"><a href="#" style="width:90px;">首页</a></li>
							</ul>
						</div>

			    <div class="bannerTwo">
                      <!--轮播 -->
						<div class="am-slider am-slider-default scoll" data-am-flexslider id="demo-slider-0">
							<ul class="am-slides">
								<!-- <li class="banner1"><a><img src="/abc/Public/Home/images/ad5.jpg" /></a></li>
								<li class="banner2"><a><img src="/abc/Public/Home/images/ad6.jpg" /></a></li>
								<li class="banner3"><a><img src="/abc/Public/Home/images/ad7.jpg" /></a></li>
								<li class="banner4"><a><img src="/abc/Public/Home/images/ad8.jpg" /></a></li> -->
								<!-- 遍历首页大轮播图 -->
								<?php if(is_array($Carousel)): foreach($Carousel as $key=>$val): ?><li class="banner1"><a href="#"><img src="/abc/Public/<?php echo ($val['pic']); ?>" style="width:760px;height:320px;" /></a></li><?php endforeach; endif; ?>
							</ul>
						</div>
						<div class="clear"></div>	
			    </div>

						<!--侧边导航 -->
						<div id="nav" class="navfull" style="position: static;">
							<div class="area clearfix">
								<div class="category-content" id="guide_2">
									
									<div class="category" style="box-shadow:none ;margin-top: 2px;">
										<ul class="category-list navTwo" id="js_climit_li">
										<?php if(is_array($data)): foreach($data as $key=>$v): ?><li>
												<div class="category-info" value="<?php echo ($v['id']); ?>">
													<h3 class="category-name b-category-name"><i><img src="/abc/Public/Home/images/cake.png"></i><a href="<?php echo U('Goods/index', ['id' => $v['id']]);?>" class="ml-22" title="点心"><?php echo ($v['name']); ?></a></h3>
													<em>&gt;</em></div>
												<div class="menu-item menu-in top">
													<div class="area-in">
														<div class="area-bg">
															<div class="menu-srot">
																<div class="sort-side">
																

																</div>
																
															</div>
														</div>
													</div>
												</div>
											<b class="arrow"></b>	
											</li><?php endforeach; endif; ?>

										</ul>
									</div>
								</div>

							</div>
						</div>
						<!--导航 -->
						<script type="text/javascript">
							(function() {
								$('.am-slider').flexslider();
							});
							$(document).ready(function() {
								$("li").hover(function() {
									$(".category-content .category-list li.first .menu-in").css("display", "none");
									$(".category-content .category-list li.first").removeClass("hover");
									$(this).addClass("hover");
									$(this).children("div.menu-in").css("display", "block")
								}, function() {
									$(this).removeClass("hover")
									$(this).children("div.menu-in").css("display", "none")
								});
							})
							
							$('.category-info').mouseover(function() {
								
								var id = $(this).attr('value');
								$.ajax({
									url: "<?php echo U('Index/index');?>",
									type: 'post',
									data: {id:+id},
									success:function(res) {
										$('.sort-side').html(res);
									},
								})
								$(this).attr('value');
							});
							// console.log();
						</script>


					<!--小导航 -->
					<div class="am-g am-g-fixed smallnav">
						<div class="am-u-sm-3">
							<a href="sort.html"><img src="/abc/Public/Home/images/navsmall.jpg" />
								<div class="title">商品分类</div>
							</a>
						</div>
						<div class="am-u-sm-3">
							<a href="#"><img src="/abc/Public/Home/images/huismall.jpg" />
								<div class="title">大聚惠</div>
							</a>
						</div>
						<div class="am-u-sm-3">
							<a href="#"><img src="/abc/Public/Home/images/mansmall.jpg" />
								<div class="title">个人中心</div>
							</a>
						</div>
						<div class="am-u-sm-3">
							<a href="#"><img src="/abc/Public/Home/images/moneysmall.jpg" />
								<div class="title">投资理财</div>
							</a>
						</div>
					</div>


				<!--各类活动-->
				<div class="row" style="position: relative; left: 0px; top: 341px;">
				<!-- <?php if(is_array($Immediate)): foreach($Immediate as $k=>$v): ?><li><a><img src="/abc/Public/Home/images/row1.jpg"/></a></li>
					<li><a><img src="/abc/Public/Home/images/row2.jpg"/></a></li>
					<li><a><img src="/abc/Public/Home/images/row3.jpg"/></a></li><?php endforeach; endif; ?> -->
				<!-- 遍历立即抢购的商品 -->
				<?php if(is_array($Immediate)): foreach($Immediate as $k=>$v): ?><li><a href="#" title="<?php echo ($res[$v['aid']]); ?>"><img src="/abc/Public/<?php echo ($v["pic"]); ?>"/ style="width:245px;height:202px;"></a></li><?php endforeach; endif; ?>
					<li style="border-bottom: 0.8px solid rgb(210,209,209);padding-bottom: 5px;">
						<ul><!-- <div style="width:250px;height:206px;background: #F8F8F8;position:absolute;z-index: -10"></div> -->
							<li style="cursor:pointer;width:56px;height:20px;margin-left: 5px;text-align: center;border-right: 0.8px solid rgb(210,209,209);" class="xuan1" data-left="0">水果
								<div class="yidong" style="border:1px solid red;width:50px;height:1px;position: absolute;margin-top:5px;margin-left: 0px;"></div>
								<img src="/abc/Public/Home/images/aa.jpg" style="margin-top: 34px; width:80px;height:80px;position: absolute;margin-left: 85px;" id="image2">
								<div style="position: absolute;margin-left: 142px;margin-top: 100px;" id="title2">玖原农珍</div>
								<div style="position: absolute;margin-left: 116px;margin-top: 120px;width:100px;height:110px;width:100px;height:110px;" id="content2">广西百香果12个 单果60-85g 西番莲水果</div>
								<div style="width:0.1px;height:150px;/*border-right:0.1px solid rgb(102,102,102);*/position: absolute;margin-top: 22px;margin-left:110px;"></div>
								<img id="image1" src="/abc/Public/Home/images/1.jpg" style="margin-top: 15px; width:80px;height:80px;margin-left: 15px;">
								<div id="title1" style="position: absolute;margin-left:30px;margin-top:4px;">车厘子</div>
								<div id="content1" style="margin-top:26px;width:100px;height:110px;margin-left:5px;">甜又甜,好吃又不贵,买一斤送一斤</div>
							</li>
							<li data-left="58" class="xuan1" style="cursor:pointer;text-align: center;border-right: 0.8px solid rgb(210,209,209);">饮料</li>
							<li data-left="122" style="cursor:pointer;text-align: center;border-right: 0.8px solid rgb(210,209,209);" class="xuan1">薯片</li>
							<li data-left="184" style="cursor:pointer;text-align: center;" class="xuan1">坚果</li>
						</ul>
					</li><!-- <img src="/abc/Public/Home/images/row4.jpg"/> -->
				</div>
				<div class="clear"></div>	
					<!--走马灯 -->

					<div class="marqueenTwo">
						<span class="marqueen-title"><i class="am-icon-volume-up am-icon-fw"></i>商城头条<em class="am-icon-angle-double-right"></em></span>
						<div class="demo">

							<ul>
								<li class="title-first"><a target="_blank" href="#">
									<img src="/abc/Public/Home/images/TJ2.jpg"></img>
									<span>[特惠]</span>洋河年末大促，低至两件五折							
								</a></li>
								<li class="title-first"><a target="_blank" href="#">
									<span>[公告]</span>商城与广州市签署战略合作协议
								     <img src="/abc/Public/Home/images/TJ.jpg"></img>
								     <p>XXXXXXXXXXXXXXXXXX</p>
							    </a></li>																    							    
								<li><a target="_blank" href="#"><span>[特惠]</span>女生节商城爆品1分秒	</a></li>
								<li><a target="_blank" href="#"><span>[公告]</span>华北、华中部分地区配送延迟</a></li>
								<li><a target="_blank" href="#"><span>[特惠]</span>家电狂欢千亿礼券 买1送1！</a></li>
								<li><a target="_blank" href="#"><span>[特惠]</span>洋河年末大促，低至两件五折</a></li>
								<li><a target="_blank" href="#"><span>[公告]</span>华北、华中部分地区配送延迟</a></li>
						
							</ul>
                       
						</div>
					</div>
					<div class="clear"></div>
				
				</div>

				
				
	
				<script type="text/javascript">
					// console.log($('.xuan1'));
					brr = [];
					
					
					
					$('.xuan1').mouseover(function(){
						// console.log(evn);
						// $('.yidong').css('margin-left', $(this).attr('data-left') + 'px');
						$('.yidong').animate({'margin-left': $(this).attr('data-left') + 'px'}, 90);
						var i = $(this).attr('data-left');
						

						if (i == '0') {
							shui();
						} else {
							var result = $.inArray(i, brr);
							if (result == '-1') {
								$.ajax({
								url: "<?php echo U('Index/index');?>",
								type: 'get',
								data: {id:+i},
								async: false,
								success:function(res) {
										if (i == '58') {
											yin = [];
									 		yin.push(res[0]['title']);
											yin.push(res[0]['content']);
											yin.push(res[0]['image']);
											yin.push(res[1]['title']);
											yin.push(res[1]['content']);
											yin.push(res[1]['image']);
									 		brr.push(i);
									 		yin1();
									 	} else if (i == '122') {
									 		shu = [];
									 		shu.push(res[0]['title']);
											shu.push(res[0]['content']);
											shu.push(res[0]['image']);
											shu.push(res[1]['title']);
											shu.push(res[1]['content']);
											shu.push(res[1]['image']);
									 		brr.push(i);
									 		shu1();
									 	} else if (i == '184') {
									 		jian = [];
									 		jian.push(res[0]['title']);
											jian.push(res[0]['content']);
											jian.push(res[0]['image']);
											jian.push(res[1]['title']);
											jian.push(res[1]['content']);
											jian.push(res[1]['image']);
									 		brr.push(i);
									 		jian1();
									 	}
									} 
								});
							} else {
								if (i == '58') {
									yin1();
								} else if (i == '122') {
									shu1();
								} else if (i == '184') {
									jian1();
								}
							}
						}
					});
					

					// function demo(yidong, i) {
					// 	console.log(i);
					// 	var yi = yidong.css('margin-left');
					// 	// console.log(parseInt(yi));
					// 	// console.log(yi);
					// 	var yi = parseInt(yi);
					// 	var times = setInterval(function() {
					// 		if (yi != i) {
					// 			// 58 122 184
					// 			// console.log(123);
					// 			yi++;
					// 			// console.log(yi);
					// 			$('.yidong').css('margin-left', yi + 'px');
					// 			// clearTimeout(times);
					// 		} else {
					// 			clearTimeout(times);
					// 		}

					// 	}, 5);

					// }
					 // function move() {
						// arr = $('.yidong').css('margin-left');
						// // console.log($('.yidong').css('margin-left'));
						// if ($('.yidong').css('margin-left') != '58px') {
						// 	setInterval(times, 5);
						// 	i = 0;
						// 	function times() {
						// 		if (i < '58') {
						// 			i++;
						// 			arr = $('.yidong').css('margin-left', i);
						// 			clearTimeout(times);
						// 		}
						// 	}
						// }
					
					if ($(window).width() < 640) {
						function autoScroll(obj) {
							$(obj).find("ul").animate({
								marginTop: "-39px"
							}, 500, function() {
								$(this).css({
									marginTop: "0px"
								}).find("li:first").appendTo(this);
							})
						}
						$(function() {
							setInterval('autoScroll(".demo")', 3000);
						})
					}
				</script>
			</div>
			<div style="clear:both; margin-top: 30px;margin-bottom: 5px;  height: 1px; width: 1470px;"></div>
			<div class="shopMainbg">
				<div class="shopMain" id="shopmain">

					<!--热门活动 -->

					<div class="am-container">
					
                     <div class="sale-mt">
		                   <i></i>
		                   <em class="sale-title">限时秒杀</em>
		                   <!-- <div class="s-time" id="countdown">
			                    <span class="hh" id="less_hour"><?php echo ($arr['data1']); ?></span>
			                    <span class="mm" id="less_minutes"><?php echo ($arr['data2']); ?></span>
			                    <span class="ss" id="less_seconds"><?php echo ($arr['data3']); ?></span>
		                   </div> -->
		                   <div id="font" style="margin-left:870px;position:absolute;margin-top:34px;color: rgb(0,0,0);font-size:18px;"></div>
		                   <div class="s-time" id="countdown">
			                    <span class="hh" id="less_hour"><?php echo ($arr['data1']); ?></span>
			                    <span class="mm" id="less_minutes"><?php echo ($arr['data2']); ?></span>
			                    <span class="ss" id="less_seconds"><?php echo ($arr['data3']); ?></span>
		                   </div>
	                  </div>

					
					  <div class="am-g am-g-fixed sale">
						<div class="am-u-sm-3 sale-item">
							<div class="s-img">
								<a href="<?php echo U('Detail/index', ['id'=>$ord[0]['id']]);?>"><img src="/abc/Public/<?php echo ($ord[0]['image0']); ?>" /></a>
							</div>
                           <div class="s-info">
                           	   <a href="#"><p class="s-title"><?php echo ($ord[0]['name']); ?></p></a>
                           	   <div class="s-price" style="margin-left: 100px;">￥<b>9.90</b>
                           	   	  <!-- <a class="s-buy"  onclick="miaosha()" style="cursor:pointer;">秒杀</a> -->
                           	   </div>                          	  
                           </div>								
						</div>
						
						<div class="am-u-sm-3 sale-item">
							<div class="s-img">
								<a href="<?php echo U('Detail/index', ['id'=>$ord[1]['id']]);?> "><img src="/abc/Public/<?php echo ($ord[1]['image0']); ?>" /></a>
							</div>
                           <div class="s-info">
                           	   <a href="#"><p class="s-title"><?php echo ($ord[1]['name']); ?></p></a>
                           	   <div class="s-price" style="margin-left: 100px;">￥<b>9.90</b>
                           	   	  <!-- <a class="s-buy" href="#">秒杀</a> -->
                           	   </div>                          	  
                           </div>								
						</div>					
						
						<div class="am-u-sm-3 sale-item">
							<div class="s-img">
								<a href="<?php echo U('Detail/index', ['id'=>$ord[2]['id']]);?> "><img src="/abc/Public/<?php echo ($ord[2]['image0']); ?>" /></a>
							</div>
                           <div class="s-info">
                           	   <a href="#"><p class="s-title"><?php echo ($ord[2]['name']); ?></p></a>
                           	   <div class="s-price" style="margin-left: 100px;">￥<b>9.90</b>
                           	   	  <!-- <a class="s-buy" href="#">秒杀</a> -->
                           	   </div>                          	  
                           </div>								
						</div>
						
						<div class="am-u-sm-3 sale-item">
							<div class="s-img">
								<a href="<?php echo U('Detail/index', ['id'=>$ord[3]['id']]);?> "><img src="/abc/Public/<?php echo ($ord[3]['image0']); ?> " /></a>
							</div>
                           <div class="s-info">
                           	   <a href="#"><p class="s-title"><?php echo ($ord[3]['name']); ?></p></a>
                           	   <div class="s-price" style="margin-left: 100px;">￥<b>9.90</b>
                           	   	  <!-- <a class="s-buy" href="#">秒杀</a> -->
                           	   </div>                          	  
                           </div>								
						</div>
						
					  </div>
                   </div>
					<div class="clear "></div>
					
			
				 <div class="am-container activity ">
				 	<div class="ssa1" style="width:100%;height:355.6px;background: #fff;z-index:10;position: absolute;" data-left="900"><img src="/abc/Public/Home/images/1.gif" style="height:100px;width:100px;margin-left:550px;margin-top:158px;"></div>
						<div class="shopTitle ">
							<h4>热门商品</h4>
							<h3>每期活动 优惠享不停 </h3>
							<span class="more ">
                              <a class="more-link " href="# ">全部活动</a>
                            </span>
						</div>
					
					  <div class="am-g am-g-fixed ">
						<div class="am-u-sm-3 ">
							<div class="icon-sale one "></div>	
								<h4>1</h4>							
							<div class="activityMain " style="height:296px;width:296px;">
								<img src="/abc/Public/<?php echo ($order[0]['image0']); ?> " style="height:240px;width:240px;"></img>
							</div>
							<div class="info ">
								<h3><?php echo ($order[0]['name']); ?></h3>
							</div>														
						</div>
						
						<div class="am-u-sm-3 ">
						  <div class="icon-sale two "></div>	
							<h4>2</h4>
							<div class="activityMain " style="height:296px;width:296px;">
								<img src="/abc/Public/<?php echo ($order[1]['image0']); ?> " style="height:240px;width:240px;"></img>
							</div>
							<div class="info ">
								<h3><?php echo ($order[1]['name']); ?></h3>								
							</div>							
						</div>						
						
						<div class="am-u-sm-3 ">
							<div class="icon-sale three "></div>
							<h4>3</h4>
							<div class="activityMain " style="height:296px;width:296px;">
								<img src="/abc/Public/<?php echo ($order[2]['image0']); ?> " style="height:240px;width:240px;"></img>
							</div>
							<div class="info ">
								<h3><?php echo ($order[2]['name']); ?></h3>
							</div>							
						</div>						

						<div class="am-u-sm-3 last ">
							<div class="icon-sale "></div>
							<h4>4</h4>
							<div class="activityMain " style="height:296px;width:296px;">
								<img src="/abc/Public/<?php echo ($order[3]['image0']); ?> " style="height:240px;width:240px;"></img>
							</div>
							<div class="info ">
								<h3><?php echo ($order[3]['name']); ?></h3>
							</div>													
						</div>

					  </div>
                   </div>
					<div class="clear "></div>

			
            <div class="f1">
					<!--甜点-->
				<div class="ssa2" style="width:1632.45px;height:453.6px;background: #fff;z-index:10;position: absolute;" data-left="1100"><img src="/abc/Public/Home/images/1.gif" style="height:100px;width:100px;display: block;margin:130px auto;" ></div>
					
					<div class="am-container " >
						<div class="shopTitle ">
							<h4 class="floor-title">糕点</h4>
							<div class="floor-subtitle"><em class="am-icon-caret-left"></em><h3>每一道甜品都有一个故事</h3></div>
							<div class="today-brands " style="right:0px ;top:13px;">
								<a href="# ">桂花糕</a>|
								<a href="# ">奶皮酥</a>|
								<a href="# ">栗子糕 </a>|
								<a href="# ">马卡龙</a>|
								<a href="# ">铜锣烧</a>|
								<a href="# ">豌豆黄</a>
							</div>

						</div>
					</div>
					
					<div class="am-g am-g-fixed floodSix ">				
						<div class="am-u-sm-5 am-u-md-3 text-one list">
							<!-- <div class="word">
								<a class="outer" href="#"><span class="inner"><b class="text"><?php echo ($zy[0]['name']); ?></b></span></a>
								<a class="outer" href="#"><span class="inner"><b class="text"><?php echo ($zy[1]['name']); ?></b></span></a>
								<a class="outer" href="#"><span class="inner"><b class="text"><?php echo ($zy[2]['name']); ?></b></span></a>	
								<a class="outer" href="#"><span class="inner"><b class="text"><?php echo ($zy[3]['name']); ?></b></span></a>
								<a class="outer" href="#"><span class="inner"><b class="text"><?php echo ($zy[4]['name']); ?></b></span></a>
								<a class="outer" href="#"><span class="inner"><b class="text"><?php echo ($zy[1]['name']); ?></b></span></a>	
								<a class="outer" href="#"><span class="inner"><b class="text"><?php echo ($zy[1]['name']); ?></b></span></a> -->
								<!-- <a class="outer" href="#"><span class="inner"><b class="text">核桃</b></span></a>
								<a class="outer" href="#"><span class="inner"><b class="text">核桃</b></span></a> -->								
							<!-- </div>							 -->
							<a href="# ">
								<img id="11" src="/abc/Public/Home/images/1.gif" />
								<div class="outer-con ">
									<div class="title ">
										零食大礼包开抢啦
									</div>
									<div class="sub-title ">
										当小鱼儿恋上软豆腐
									</div>
								</div>
							</a>
							<div class="triangle-topright"></div>	
						</div>
						
						<div class="am-u-sm-7 am-u-md-5 am-u-lg-2 text-two big">
							
								<div class="outer-con ">
									<div class="title ">
										雪之恋和风大福
									</div>
									<div class="sub-title ">
										
									</div>
									
								</div>
								<a href="# "><img id="22" src="/abc/Public/Home/images/1.gif" /></a>						
						</div>

						<li>
						<div class="am-u-md-2 am-u-lg-2 text-three">
							<div class="boxLi"></div>
							<div class="outer-con ">
								<div class="title ">
									
								</div>								
								<div class="sub-title ">
									
								</div>
								
							</div>
							<a href="# "><img id="33" src="/abc/Public/Home/images/1.gif " /></a>
						</div>
						</li>
						<li>
						<div class="am-u-md-2 am-u-lg-2 text-three sug">
							<div class="boxLi"></div>
							<div class="outer-con ">
								<div class="title ">
									
								</div>
								<div class="sub-title ">
									
								</div>
								
							</div>
							<a href="# "><img id="44" src="/abc/Public/Home/images/1.gif " /></a>
						</div>
						</li>
						<li>
						<div class="am-u-sm-4 am-u-md-5 am-u-lg-4 text-five">
							<div class="boxLi"></div>
							<div class="outer-con ">
								<div class="title ">
									
								</div>								
								<div class="sub-title ">
									
								</div>
								
							</div>
							<a href="# "><img id="55" src="/abc/Public/Home/images/1.gif" /></a>
						</div>	
						</li>
						<li>
						<div class="am-u-sm-4 am-u-md-2 am-u-lg-2 text-six">
							<div class="boxLi"></div>
							<div class="outer-con ">
								<div class="title ">
									
								</div>
								<div class="sub-title ">
									
								</div>
								
							</div>
							<a href="# "><img id="66" src="/abc/Public/Home/images/1.gif" /></a>
						</div>	
						</li>
						<li>
						<div class="am-u-sm-4 am-u-md-2 am-u-lg-4 text-six">
							<div class="boxLi"></div>
							<div class="outer-con ">
								<div class="title ">
									
								</div>
								<div class="sub-title ">
									
								</div>
								
							</div>
							<a href="# "><img id="77" src="/abc/Public/Home/images/1.gif" /></a>
						</div>	
						</li>						
					</div>

					<div class="clear "></div>
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
<!-- 中部继承结束 -->

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
								<p class="avatar_imgbox "><img src="/abc/Public/Home/images/no-img_mid_.jpg " /></p>
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
							<span class="wdsc "><img src="/abc/Public/Home/images/wdsc.png " /></span>
						</a>
						<div class="mp_tooltip ">
							我的收藏
							<i class="icon_arrow_right_black "></i>
						</div>
					</div>

					<div id="broadcast " class="item ">
						<a href="# ">
							<span class="chongzhi "><img src="/abc/Public/Home/images/chongzhi.png " /></span>
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
							<div class="mp_qrcode " style="display:none; "><img src="/abc/Public/Home/images/weixin_code_145.png " /><i class="icon_arrow_white "></i></div>
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

			window.jQuery || document.write('<script src="/abc/Public/Home/basic/js/jquery.min.js "><\/script>');

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

		<script type="text/javascript " src="/abc/Public/Home/basic/js/quick_links.js "></script>
	
	<script type="text/javascript">
		function shui() {
			var one=new Array();
			var one = $('#title1').html('车厘子');
			var one = $('#content1').html('甜又甜,好吃又不贵,买一斤送一斤');
			var one = $('#image1').attr('src', '/abc/Public/Home/images/1.jpg');
			var one = $('#title2').html('玖原农珍');
			var one = $('#content2').html('广西百香果12个 单果60-85g 西番莲水果');
			var one = $('#image2').attr('src', '/abc/Public/Home/images/aa.jpg');
		}

		function yin1() {
				$('#title1').html(yin[0]);
				$('#content1').html(yin[1]);
				$('#image1').attr('src', '/abc/Public/Home/images/'+yin[2]);
				$('#title2').html(yin[3]);
				$('#content2').html(yin[4]);
				$('#image2').attr('src', '/abc/Public/Home/images/'+yin[5]);
		}

		function shu1() {
				$('#title1').html(shu[0]);
				$('#content1').html(shu[1]);
				$('#image1').attr('src', '/abc/Public/Home/images/'+shu[2]);
				$('#title2').html(shu[3]);
				$('#content2').html(shu[4]);
				$('#image2').attr('src', '/abc/Public/Home/images/'+shu[5]);
		}

		function jian1() {
				$('#title1').html(jian[0]);
				$('#content1').html(jian[1]);
				$('#image1').attr('src', '/abc/Public/Home/images/'+jian[2]);
				$('#title2').html(jian[3]);
				$('#content2').html(jian[4]);
				$('#image2').attr('src', '/abc/Public/Home/images/'+jian[5]);
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