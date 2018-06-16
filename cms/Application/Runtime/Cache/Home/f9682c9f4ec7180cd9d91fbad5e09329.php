<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>分类</title>
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
	<link rel="stylesheet" href="/Public/home/css/lib/mui.min.css">
	<link rel="stylesheet" type="text/css" href="/Public/home/css/common/common.css" />
	<link rel="stylesheet" href="/Public/home/css/Goods/classify.css">
	<!-- 引用阿里矢量图标库 -->
	<link rel="stylesheet" type="text/css" href="http://at.alicdn.com/t/font_524801_4a6j0gw6lecm1jor.css">
    <script>
        var cr_info_json = <?php echo ($cr_info_json); ?>; // 分类
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
    <header class="mui-bar mui-bar-nav header">
        <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
        <a href="/index.php/Home/Search/search" class="search"><i class="iconfont icon-sousuo"></i>请输入关键字</a>
    </header>
	<!--容器开始-->
    <div class="mui-content" id="control">

    	<!--<main>
    		<ul class="classify_nav">
    			<li v-for="(item,index) in titleMes" v-text="item" :class="{classify_active:index == oIndex}" @click="choice(index)"></li>
    		</ul>
    		<div class="content_right" id="content">
    			<div class="banner"><img :src="classifyMes.photo"></div>
    			<fieldset><legend v-text="classifyMes.name"></legend></fieldset>
    			<ul class="list">
    				<li v-for="item in classifyMes.subclass"><a :href="item.href">
    					<div class="photo"><img :src="item.img" /></div>
    					<p v-text="item.title"></p>
    				</a></li>
    			</ul>
    		</div>
    	</main>	-->
    		
    		<div class="mui-content mui-row mui-fullscreen">
				<div class="mui-col-xs-3 classify_nav">
					<div id="segmentedControls" class="mui-segmented-control mui-segmented-control-inverted mui-segmented-control-vertical">
						<a class="mui-control-item" :class="{'mui-active':index == oIndex}" v-for="(item,index) in titleMes" v-text="item.text" @click="choice(index,item.id)"></a>
					</div>
				</div>
				<div id="segmentedControlContents" class="mui-col-xs-9 content_right">
					<div id="content' + i + '" class="mui-control-content mui-active">
						<div class="banner"><img :src="classifyMes.photo"></div>
    					<fieldset><legend v-text="classifyMes.name"></legend></fieldset>
						<ul class="mui-table-view list">
		    				<li v-for="item in classifyMes.subclass"><a :href="item.href">
		    					<div class="photo"><img :src="item.img" /></div>
		    					<p v-text="item.title"></p>
		    				</a></li>
    					</ul>
					</div>
				</div>
			</div>
			<!--classify_active:index == oIndex, mui-active:index==oIndex-->
    		
    		
		
    </div>
    <!--容器结束-->
</body>

    <script src="/Public/home/js/lib/vue.min.js"></script>
    <script src="/Public/home/js/lib/mui.min.js"></script>
    <script src="/Public/home/js/lib/jquery.min.js"></script>
    <!--脚部-->
    <script src="/Public/home/js/lib/footer.js"></script>
    <!-- 主要JS -->
    <script type="text/javascript" src="/Public/home/js/Goods/classify.js"></script>

</html>