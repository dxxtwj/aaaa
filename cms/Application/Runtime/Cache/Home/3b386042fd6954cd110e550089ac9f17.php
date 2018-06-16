<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>我的</title>
	<!-- <link rel="shortcut icon" type="image/x-icon" href="/Public/home/images/yflogo.ico" media="screen" /> -->
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,minimal-ui"/><!-- viewport 后面加上 minimal-ui 在safri 体现效果 -->
    <meta name="apple-mobile-web-app-capable" content="yes" />      <!-- iphone safri 全屏 -->
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />   <!-- iphone safri 状态栏的背景颜色 -->
    <meta name="apple-mobile-web-app-title" content="YUKI">       <!-- iphone safri 添加到主屏界面的显示标题 -->
    <meta name="format-detection" content="telphone=no, email=no" />    <!-- 禁止数字自动识别为电话号码 -->
    <meta name="renderer" content="webkit">             <!-- 启用360浏览器的极速模式(webkit) -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">   
    <meta name="HandheldFriendly" content="true">       <!-- 是针对一些老的不识别viewport的浏览器，列如黑莓 -->
    <meta http-equiv="Cache-Control" content="no-siteapp" />    <!-- 禁止百度转码 -->
    <meta name="screen-orientation" content="portrait"> <!-- uc强制竖屏 -->
    <meta name="browsermode" content="application">     <!-- UC应用模式 -->
    <meta name="full-screen" content="yes">             <!-- UC强制全屏 -->
    <meta name="x5-orientation" content="portrait">     <!-- QQ强制竖屏 -->
    <meta name="x5-fullscreen" content="true">          <!-- QQ强制全屏 -->
    <meta name="x5-page-mode" content="app">            <!-- QQ应用模式 -->
    <meta name="format-detection" content="telephone=no"> <!--禁用iPhone手机浏览器上给电话号码自动加上的link样式-->
    <!--加载阿里flexible库-->
    <script src="/Public/home/js/lib/flexible.js"></script>
    <!--<link rel="stylesheet" href="/Public/home/css/lib/swiper.min.css">-->
	<link rel="stylesheet" href="/Public/home/css/lib/mui.min.css">
	<link rel="stylesheet" type="text/css" href="/Public/home/css/common/common.css" />
	<link rel="stylesheet" href="/Public/home/css/Person/person.css">
	<!-- 引用阿里矢量图标库 -->
	<link rel="stylesheet" type="text/css" href="http://at.alicdn.com/t/font_524801_bija2xg1xlkxogvi.css">
	<script>
		var userList_json = <?php echo ($userList_json); ?>; // 用户信息和订单之类的统计信息
	</script>
</head>

<body>
	<!--loading-->
	<div class="loading">
		<div class="spinner">
			<div class="bounce1"></div>
			<div class="bounce2"></div>
			<div class="bounce3"></div>
		</div>
	</div>
	<!--容器开始-->
    <div class="mui-content" id="control">
    	<main>
	    	<div class="mui-card-header mui-card-media">
	    		<div class="photo">
		    		<div class="perimg">
		    			<img :src='perMes.perImg' />
		    		</div>
		    		<!--<div class="level" v-if="perMes.perName"><span>2</span></div>-->
	    		</div>
	    		<!--头部卡片 已登录-->
				<div class="mui-media-body" v-if="perMes.perName">
					<p>
						<span v-text="perMes.perName">小M</span>
						<!--<a href="javascropt:;" class="promote"><i class="iconfont icon-shengji"></i>升级</a>-->
					</p>
					<p>ID：<span v-text="perMes.uId"></span></p>
					<!--<p><i class="iconfont icon-user"></i><span v-text="perMes.perPhone"></span></p>-->
				</div>
				<!--头部卡片 未登录-->
				<div class="mui-media-body" v-if="!perMes.perName">
					<p><span>
						<a class="login" href="<?php echo U('User/Login');?>">登录</a>&nbsp;|
						<a class="register" href="<?php echo U('User/Register');?>">注册</a>
					</span></p>
				</div>
				<a href="../Person/info.html" class="set"><i class="iconfont icon-shezhi1"></i></a>
			</div>
			
			
			
			<!--订单-->
			<div class="order">
				<div class="all">我的订单<a href="/index.php/Home/MyOrder/index/state/-1"><span>查看全部订单<i class="iconfont icon-xiangyou"></i></span></a></div>
				<ul class="order_x">
					<li><a href="/index.php/Home/MyOrder/index/state/0">
						<i class="iconfont icon-daifukuandingdanshu">
							<span class="num" id="daiFuKuan" v-if="perMes.unpaid_num" v-text="perMes.unpaid_num"></span>
						</i>
						<p>待付款</p>
					</a></li>
					<li><a href="/index.php/Home/MyOrder/index/state/1">
						<i class="iconfont icon-daifahuo">
							<span class="num" id="daiFaHua" v-if="perMes.wfh_num" v-text="perMes.wfh_num"></span>
						</i>
						<p>待发货</p>
					</a></li>
					<li><a href="/index.php/Home/MyOrder/index/state/2">
						<i class="iconfont icon-daishouhuo">
							<span class="num" id="daiShouHuo" v-if="perMes.dsh_num" v-text="perMes.dsh_num"></span>
						</i>
						<p>待收货</p>
					</a></li>
					<li><a href="/index.php/Home/MyOrder/index/state/3">
						<i class="iconfont icon-pingjia">
							<span class="num" id="daiPingJia" v-if="perMes.comment_num" v-text="perMes.comment_num"></span>
						</i>
						<p>待评价</p>
					</a></li>
				</ul>
			</div>
			
			<!--选项列表-->
			<div class="list">
				<!--表1-->
				<ul class="mui-table-view">
					<li class="mui-table-view-cell">
				        <a class="mui-navigate-right" href="/index.php/Home/Cart/index">
				        	<i class="iconfont icon-gouwuche2"></i>
				        	<span>我的购物车</span>
				        	<i class="iconfont icon-xiangyou"></i>
				        	<span class="acount" id="cartNum" v-text="perMes.cart_num" v-if="perMes.cart_num"></span>
				        </a>
				    </li>
				    <li class="mui-table-view-cell">
				        <a class="mui-navigate-right" href="/index.php/Home/Person/my_coupon">
				        	<i class="iconfont icon-youhuiquan"></i>
				        	<span>我的优惠券</span>
				        	<i class="iconfont icon-xiangyou"></i>
				        	<span class="acount" id="youHuiQuan" v-text="perMes.coupon_num" v-if="perMes.coupon_num"></span>
				        </a>
				    </li>
				</ul>
				
				<!--表2-->
				<ul class="mui-table-view">
				    <li class="mui-table-view-cell">
				        <a class="mui-navigate-right" href="/index.php/Home/Address/address_management">
				        	<i class="iconfont icon-shouhuodizhi"></i>
				        	<span>收货地址管理</span>
				        	<i class="iconfont icon-xiangyou"></i>
				        </a>
				    </li>
				    <li class="mui-table-view-cell">
				        <a class="mui-navigate-right" href="/index.php/Home/Person/editPwd">
				        	<i class="iconfont icon-shezhi1"></i>
				        	<span>修改密码</span>
				        	<i class="iconfont icon-xiangyou"></i>
				        </a>
				    </li>
				    <li class="mui-table-view-cell">
				        <a class="mui-navigate-right" href="/index.php/Home/Person/service">
				        	<i class="iconfont icon-kefuzixunhui"></i>
				        	<span>客服中心</span>
				        	<i class="iconfont icon-xiangyou"></i>
				        </a>
				    </li>
				</ul>
			</div>
			<!--选项列表结束-->
			<button type="button" v-if="perMes.perName" class="mui-btn quit_btn" @click="quit">退出登录</button>
		</main>
    </div>
    <!--容器结束-->
</body>

    <script src="/Public/home/js/lib/vue.min.js"></script>
    <script src="/Public/home/js/lib/mui.min.js"></script>
    <script src="/Public/home/js/lib/jquery.min.js"></script>
    <!--脚部-->
    <script src="/Public/home/js/lib/footer.js"></script>
    <!-- Swiper JS -->
    <!--<script src="/Public/home/js/lib/swiper.jquery.min.js"></script>-->
    <!-- 主要JS -->
    <script type="text/javascript" src="/Public/home/js/Person/person.js"></script>
    
</html>