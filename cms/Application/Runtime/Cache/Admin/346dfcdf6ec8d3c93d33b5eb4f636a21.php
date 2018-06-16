<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<!--[if lt IE 9]>
<script type="text/javascript" src="/Public/admin/js/html5.js"></script>
<script type="text/javascript" src="/Public/admin/js/respond.min.js"></script>
<script type="text/javascript" src="/Public/admin/js/PIE_IE6	78.js"></script>
<![endif]-->
<link href="/Public/admin/assets/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="/Public/admin/css/style.css"/>       
<link href="/Public/admin/assets/css/codemirror.css" rel="stylesheet">
 <link rel="stylesheet" href="/Public/admin/assets/css/ace.min.css" /> 
      <link rel="stylesheet" href="/Public/admin/Widget/zTree/css/zTreeStyle/zTreeStyle.css" type="text/css">
<link rel="stylesheet" href="/Public/admin/assets/css/font-awesome.min.css" />
<!--[if IE 7]>
		  <link rel="stylesheet" href="/Public/admin/assets/css/font-awesome-ie7.min.css" />
		<![endif]-->
<link href="/Public/admin/Widget/icheck/icheck.css" rel="stylesheet" type="text/css" />

<!-- 图片上传 -->

<link rel="stylesheet" href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/Public/admin/assets/webuploader/css/font-awesome.min.css"> 
<!-- <link rel="stylesheet" type="text/css" href="/Public/admin/assets/webuploader/css/style.css"> -->
<link rel="stylesheet" type="text/css" href="/Public/admin/assets/webuploader/css/webuploader.css">
<link rel="stylesheet" type="text/css" href="/Public/admin/assets/webuploader/css/demo.css">
<style type="text/css">
	.modal-dialog{left:0;}
	input[type="text"]{
    	margin-left:0;
  	}
</style>
<style type="text/css">
	.spectable td,.spectable th {border:1px solid #ccc; vertical-align: middle;text-align:center;}
	.spectable th { font-weight: bold;}
	.spectableinput { text-align: center;}
	.f {border-color: #b94a48;-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);}
	.table.table-bordered tr th,.table.table-bordered tr td{overflow:hidden; text-overflow:ellipsis;}
</style>

<style>
.btn-danger{
	margin-left:15px;
}
#specs, .panel, .panel-bo , .form-group {
	float: left;
	width:100%;
}
.panel{
	padding:20px 0;
}
table{
	table-layout:fixed;
}

/* 图片删除 */
.m_imgBox{
	float: left;
	position: relative;
	width:127px;
	height:110px;
	margin: 10px 10px 0 0;
}
.m_img{
	width:110px;
	height:110px;
	padding: 5px;
	border:1px solid #ddd;
	overflow: hidden;
}
.m_img img{
	width:100%;
	height:100%;
}
.m_imgDel{
	position: absolute;
	right: 0;
	top:0;
	font-size:18px;
	color: rgba(0,0,0,0.2);
}
</style>
<style>
	
    .multi-item { height:110px;float:left;position:relative;}
     .img-thumbnail { width:100px;height:100px}
     .img-nickname { position: absolute;bottom:0px;line-height:25px;height:25px;
                    color:#fff;text-align:center;width:100px;top-25px;background:rgba(0,0,0,0.8);}
     .multi-img-details { padding:5px;}




	.spectable td,.spectable th {border:1px solid #ccc; vertical-align: middle;text-align:center;}
	.spectable th { font-weight: bold;}
	.spectableinput { text-align: center;}
	.f {border-color: #b94a48;-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);}
	.table.table-bordered tr th,.table.table-bordered tr td{overflow:hidden; text-overflow:ellipsis;}



/*选项卡*/
.tabs{
	width: 120px;
	overflow: hidden;
    padding: 0;
    list-style: none;
    /*display: flex;*/
    /*width: 100%;*/
    border-right: 1px solid #e8e8e8;
    margin:0 40px 0 10px;
}
.tabs li{
	float: left;
    padding: 10px 15px;
    margin-right: 2px;
    cursor: pointer;
    width: 100%;
    text-align: right;
}
.change_col {
    color: #fff;
    background: #428bca;
}
.show1{
	display: block;
}
.show2{
	display: none;
}
.input_col{
	border:1px solid #43c2f7;
	box-shadow: 0 0 2px #43c2f7;
	-webkit-box-shadow:  0 0 2px #43c2f7;
}
#form-article-add .cl .Add_p_s .input-tex{
	border-radius: 5px;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	line-height: 2.5rem;
	height: 2.5rem;
}
input[name=recommend],input[name=freeshipping]{
	margin-top: -1px;
}
input[name=is_show]{
	margin:-1px 4px 0 0;
}
.show2 input:focus , input[type=text]:focus , select:focus{
	border:1px solid #43c2f7;
	box-shadow: 0 0 2px #43c2f7;
	-webkit-box-shadow:  0 0 2px #43c2f7;
}

/*修改*/
@media (min-width: 768px){
	.col-sm-3{
		width: 172px;
	}
}
@media (max-width: 769px){
	.col-sm-3{
		width: 172px;
		text-align: right;
	}
}

@media only screen and (max-width:861px) {
	.col-sm-9.col-xs-12{
		margin-left:30px;
	}
	.form-horizontal .control-label{
		margin-right: -30px;
	}
}
#form-article-add .cl{
	/*margin-bottom:0;*/
}
#form-article-add .formControls{
	/*width:18%;*/
}
#form-article-add .cl .Add_p_s{
	width:40%;
	margin-bottom:0;
}
#form-article-add .cl .Add_p_s .formControls{
	width: 80%;
}
#form-article-add .formControls.col-10{
	margin-left: 20px;
}
#form-article-add .cl .Add_p_s .attri{
	left:95px;
}
#form-article-add .cl .Add_p_s .gtype{
	width:70%;
}
#form-article-add .cset{
	width: auto;
}
.show1 input{
	border: 1px solid #d5d5d5;
	outline: 0;
}
.show1 input:focus{
	border: 1px solid #43c2f7;
}

#form-article-add .cl .Add_p_s .classify_f{
	position: initial;
	width: 180px;
	margin-left: 10px;
}
label{
	margin-bottom:0;
}
.notice3{
	float: left;width: 100%;margin:-5px 0 0 110px;
}
#form-article-add .cl .Add_p_s2{
	width: 25%;
}
#form-article-add .cl .Add_p_s .input-text{
	margin-right: 0;
	width:100%;
}
.unit{
	border:1px solid #d5d5d5;
	display:inline-block;
	line-height:2.2rem;
	width:15%;
	text-align:center;
}
#form-article-add .cl .Add_p_s2 .input-text{
	width: 85%;
}
.btn-xs{
	border-radius: initial;
	line-height: 2rem;
}
.btn-primary,.btn-primary:hover,.btn-primary:active,.btn-primary:focus, 
.btn-warning,.btn-warning:hover,.btn-warning:active,.btn-warning:focus{
	background-color:#fff!important;
	border: 1px solid #d5d5d5!important;
	color:#333!important;
}
.btn-warning,.btn-warning:active{
	position: relative;
	top:-2px;
}
button.btn-warning:active{
	top:-1px;
}
.cname{
	width:100%;
	border-bottom:1px solid #d5d5d5;
	margin-bottom:5px;
}
.cname span{
	display: inline-block;
	width: 65%;
	margin-left: 2%;
	font-size:16px;
}
.cname span:nth-of-type(1) , .parameterList input:nth-of-type(1){
	width: 20%;
}
.parameterList input{
	width: 65%;
	margin-left:2%;
	line-height: 1.2;
	padding: 5px 4px;
}
.removeParameter{
	margin-left: 10px;
}
.gap{
	margin-bottom:8px;
}
/*保存按钮颜色*/
.radius:first-of-type{
	background-color:#f78213!important;
	border-color:#f78213!important;
	color:#fff!important;
}
.form{
	width: 85%;
	overflow: hidden;
}
#form-article-add .btn_f{
	width: inherit;
	position: fixed;
	bottom: 0;
	right: 0px;
	margin-left: 140px;
	text-align: right;
}
.Button_operation{
	width: auto;
}
</style>

<title>添加商品</title>
</head>
<body>
<div class="clearfix" id="add_picture">

<div class="page_right_style" style="left:0px;width: 100%;">
	<div class="type_title">添加商品</div>
<div style="display: flex;">
	<ul class="tabs">
		<li class="change_col" onclick="changeCol(0)">基本设置</li>
		<li onclick="changeCol(1)">详情设置</li>
		<li onclick="changeCol(2)">参数设置</li>
		<li onclick="changeCol(3)">多规格设置</li>
	</ul>
	
		<form action="" method="post" class="form form-horizontal" id="form-article-add">
	
	<div class="show0"><!--show0开始-->
			<input type="hidden" name="gid" class="gid" value="<?php echo ($gr_info["GR_ID"]); ?>" />
			
			<div class=" clearfix cl">
				<div class="Add_p_s">
					<label class="form-label col-2">排序：</label>	
					<div class="formControls col-8"><input type="text" class="input-text" value="<?php echo ($gr_info["GR_Sort"]); ?>" placeholder="0" id="" name="goods_sort" /></div>
				</div>
				<p class="notice3">数字越大，排名越靠前</p>
			</div>
			<div class="clearfix cl">
				<div class="Add_p_s">
		     <label class="form-label col-2"><span class="c-red" style="color: red">*</span>商品名称：</label>
			 <div class="formControls col-10"><input type="text" class="input-text" value="<?php echo ($gr_info["GR_Name"]); ?>" placeholder="" id="" name="goods_name"></div>
			 </div>
			</div>
			<div class=" clearfix cl">
				<div class="Add_p_s" style="width: 75%;">
		        <label class="form-label col-2"><span class="c-red" style="color: red">*</span>分类：</label>
				<?php if(is_array($res)): foreach($res as $key=>$v): ?><div class="formControls col-2 categoryContainer gtype classify_f">
					<span class="select-box">
						<select class="select checkSoncategory classify"  name="goods_category[]" >
							<option value="0">请选择</option>
							<?php if(is_array($v)): foreach($v as $key=>$val): if($val["is_check"] == false): ?><option value="<?php echo ($val["CR_ID"]); ?>"><?php echo ($val["CR_Name"]); ?></option>
								<?php else: ?>
									<option value="<?php echo ($val["CR_ID"]); ?>" selected="selected"><?php echo ($val["CR_Name"]); ?></option><?php endif; endforeach; endif; ?>
						</select>
					</span>
				</div><?php endforeach; endif; ?>
				</div>
			</div>
			
			
			
			<div class=" clearfix cl">
				<div class="Add_p_s">
					<label class="form-label col-2">单位(件/个)：</label>	
					<div class="formControls col-8"><input type="text" class="input-text" value="<?php echo ($gr_info["GR_Unit"]); ?>" placeholder="" id="" name="goods_unit" ></div>
				</div>
			</div>
			

			<div class=" clearfix cl">
				<div class="Add_p_s">
					<label class="form-label col-2">产品重量：</label>	
					<div class="formControls col-8"><input type="text" class="input-text" value="<?php echo ($gr_info["GR_Weight"]); ?>" placeholder="0" id="" name="goods_weight" style="width: 90%;" ><span class="unit" style="width:10%;">克</span></div>
				</div>
			</div>
			
			<div class=" clearfix cl">
			    <div class="Add_p_s Add_p_s2">
			      <label class="form-label col-2">原价：</label>	
				  <div class="formControls col-2"><input type="text" class="input-text" value="<?php echo ($gr_info["GR_Old_Price"]); ?>" placeholder="" id="" name="goods_oldPrice"><span class="unit">元</span></div>
				</div>
			    <div class="Add_p_s Add_p_s2" style="margin-left: 60px;">
			      <label class="form-label col-2">成本价：</label>	
				  <div class="formControls col-2"><input type="text" class="input-text" value="<?php echo ($gr_info["GR_Cost_Price"]); ?>" placeholder="" id="" name="goods_costPrice"><span class="unit">元</span></div>
				</div>
				<div class="Add_p_s Add_p_s2" style="margin-left: 60px;">
		      	  <label class="form-label col-2">销售价：</label>	
				  <div class="formControls col-2"><input type="text" class="input-text" value="<?php echo ($gr_info["GR_Price"]); ?>" placeholder="" id="" name="goods_price"><span class="unit">元</span></div>
				</div>
			</div>
			<!--<div class=" clearfix cl">
		    

		         <div class="Add_p_s">
		       		<label>若设置多规格，以多规格为准</label>
		       	</div> 
			</div>-->
				
			<div class=" clearfix cl">
				<div class="Add_p_s">
					<label class="form-label col-2"><span class="c-red" style="color: red">*</span>虚拟销量：</label>	
					<div class="formControls col-8"><input type="text" class="input-text" value="<?php echo ($gr_info["GR_Sale"]); ?>" placeholder="0" id="" name="goods_sale" ></div>
				</div>
			</div>
			
			<div class=" clearfix cl">
				<div class="Add_p_s">
					<label class="form-label col-2"><span class="c-red" style="color: red">*</span>库存：</label>	
					<div class="formControls col-8"><input type="text" class="input-text" value="<?php echo ($gr_info["GR_Stock"]); ?>" placeholder="" id="" name="goods_stock" ></div>
				</div>
			</div>
			
			
			<div class="clearfix cl">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label" style="width:auto;">减库存方式</label>
				<div class="col-sm-9 col-xs-12">

					<label class="radio-inline"><input type="radio" name="less" value="0" checked>永不减库存</label>
					<label class="radio-inline"><input type="radio" name="less" value="1">下单后减库存</label>
					<label class="radio-inline"><input type="radio" name="less" value="2">付款后减库存</label>
					<script type="text/javascript">
						var less_arr = document.getElementsByName("less");
						var checked = '<?php echo ($gr_info["GR_Less"]); ?>';
						// alert(checked);
						for (var i = 0; i <= less_arr.length-1; i++) {
							if(less_arr[i].value == checked){
								document.getElementsByName("less")[i].setAttribute('checked','checked');
							}
						}
					</script>
				</div>
			</div>
			<!-- <div class="clearfix cl">
				<label class="form-label col-2">商品描述：</label>
				<div class="formControls col-10">
					<textarea name="" cols="" rows="" class="textarea"  placeholder="说点什么...最少输入10个字符" datatype="*10-100" dragonfly="true" nullmsg="备注不能为空！" onKeyUp="textarealength(this,200)"></textarea>
					<p class="textarea-numberbar"><em class="textarea-length">0</em>/200</p>
				</div>
			</div> -->
			<div class=" clearfix cl">
				<div class="Add_p_s" style="width:75%;">
					<label class="form-label col-2">属性：</label>	
					<div class="formControls col-8 attri" style="line-height: 30px;">
						<?php if($gr_info["GR_Is_Recommend"] == 1): ?><input type="checkbox" id="" name="recommend" value="1" checked="checked">&nbsp;&nbsp;猜你喜欢&nbsp;&nbsp;
						<?php elseif($gr_info["GR_Is_Recommend"] == 0 or $gr_info["GR_Is_Recommend"] == ''): ?>
							<input type="checkbox" id="" name="recommend" value="1" >&nbsp;&nbsp;猜你喜欢&nbsp;&nbsp;<?php endif; ?>

						<?php if($gr_info["GR_Is_Fine"] == 1): ?><input type="checkbox" id="" name="fine" value="1" checked="checked">&nbsp;&nbsp;精品推荐&nbsp;&nbsp;
						<?php elseif($gr_info["GR_Is_Fine"] == 0 or $gr_info["GR_Is_Fine"] == '' ): ?>
							<input type="checkbox" id="" name="fine" value="1">&nbsp;&nbsp;精品推荐&nbsp;&nbsp;<?php endif; ?>


						<?php if($gr_info["GR_Is_Hot"] == 1): ?><input type="checkbox" id="" name="hot" value="1" checked="checked">&nbsp;&nbsp;热销火爆&nbsp;&nbsp;
						<?php elseif($gr_info["GR_Is_Hot"] == 0 or $gr_info["GR_Is_Hot"] == '' ): ?>
							<input type="checkbox" id="" name="hot" value="1">&nbsp;&nbsp;热销火爆&nbsp;&nbsp;<?php endif; ?>
						<?php if($gr_info["GR_Is_Limit"] == 1): ?><input type="checkbox" id="" name="limit" value="1" checked="checked">&nbsp;&nbsp;限量抢购&nbsp;&nbsp;
						<?php elseif($gr_info["GR_Is_Limit"] == 0 or $gr_info["GR_Is_Limit"] == '' ): ?>
							<input type="checkbox" id="" name="limit" value="1">&nbsp;&nbsp;限量抢购&nbsp;&nbsp;<?php endif; ?>
						<?php if($gr_info["GR_Is_New"] == 1): ?><input type="checkbox" id="" name="new" value="1" checked="checked">&nbsp;&nbsp;新品推荐&nbsp;&nbsp;
						<?php elseif($gr_info["GR_Is_New"] == 0 or $gr_info["GR_Is_New"] == '' ): ?>
							<input type="checkbox" id="" name="new" value="1">&nbsp;&nbsp;新品推荐&nbsp;&nbsp;<?php endif; ?>

						<?php if($gr_info["GR_Is_Freeshipping"] == 1): ?><input type="checkbox" id="" name="freeshipping" value="1" checked="checked">&nbsp;&nbsp;免邮&nbsp;&nbsp;
						<?php elseif($gr_info["GR_Is_Freeshipping"] == 0 or $gr_info["GR_Is_Freeshipping"] == '' ): ?>
							<input type="checkbox" id="" name="freeshipping" value="1">&nbsp;&nbsp;免邮&nbsp;&nbsp;<?php endif; ?>
					</div>
					
				</div>
			</div>

			<div class="clearfix cl">
				<label class="form-label col-2"><span class="c-red" style="color: red">*</span>商品图片：</label>
				<div class="formControls col-8" style="line-height: 30px;width: 52%;">
					<input type="text" disabled="disabled" placeholder="上传商品图片" style="float: left;width: 75%;">
					<button data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-xs choose_photo" type="button" isMultiple='0' showContainer=".img_container" uploadType="goods" style="float: left;">点击选择图片</button>
					<div class="img_container">
						<?php if($gr_info["GR_IMG"] != '' ): ?><div class="m_imgBox">
							<div class="m_img">
								<input type="hidden" name="path" class="path" value="<?php echo ($gr_info["GR_IMG"]); ?>" />
								<img src="<?php echo ($gr_info["GR_IMG"]); ?>" class="goods_img" style="width:100px;"/>
							</div>
								<i class="icon-remove m_imgDel" ></i>
							</div><?php endif; ?>


						 <!--<div class="m_imgBox">
							<div class="m_img">
								<img src="/Public/admin/images/huawei-4.jpg" />
							</div>
							<i class="icon-remove m_imgDel" ></i>
						</div>
						<div class="m_imgBox">
							<div class="m_img">
								<img src="/Public/admin/images/pic1.jpg" />
							</div>
							<i class="icon-remove m_imgDel" ></i>
						</div> -->


					</div>
				</div>
			</div>
			<div class="clearfix cl">
				<label class="form-label col-2">其它图片：</label>
				<div class="formControls col-8" style="line-height: 30px;width: 52%;">
					<input type="text" disabled="disabled" placeholder="上传其他图片" style="float: left;width: 75%;">
					<button data-toggle="modal" data-target="#myModal" class="btn btn-warning btn-xs choose_photo" type="button"  isMultiple='1' showContainer=".show_images_multiple" uploadType="goods">点击选择图片</button>
					<div class="show_images_multiple">
						<?php if(is_array($gr_info["goods_other_imgs"])): foreach($gr_info["goods_other_imgs"] as $k=>$v): ?><div class="m_imgBox">
								<div class="m_img">
								<input type="hidden" name="other_path[]" class="other_path" value="<?php echo ($v); ?>"/>
	                        	<img src="<?php echo ($v); ?>" class="other_imgs" style="width:100px;"/>
	                        	</div>
								<i class="icon-remove m_imgDel" ></i>
							</div><?php endforeach; endif; ?>
						<!-- <input type="hidden" name="other_iid" class="other_iid"/>
						<img src=""/> -->

					</div>
				</div>
			</div>

			<div class="clearfix cl">
				<label class="form-label col-2">是否启用：</label>
				<div class="formControls col-8" style="line-height: 30px;">
					<?php if($gr_info["GR_Is_Show"] == 1 or $gr_info["GR_Is_Show"] == ''): ?><input type="radio" name="is_show" value="1" checked="checked">启用&nbsp;&nbsp;
						<input type="radio" name="is_show" value="0">禁用
					<?php elseif($gr_info["GR_Is_Show"] == 0): ?>
						<input type="radio" name="is_show" value="1">启用&nbsp;&nbsp;
						<input type="radio" name="is_show" value="0" checked="checked">禁用<?php endif; ?>
				</div>
			</div>
	</div>	<!--show0结束-->

	
	<div class="show1"><!--show1开始-->
		    <div class="clearfix cl">
		    <label class="form-label col-2">详细内容：</label>
				<div class="formControls col-10" style="width: 60%;">
					<textarea id="editor" style="width:100%;height:300px;"><?php echo ($gr_info["content"]); ?></textarea> 
		         </div>
		    </div>
	</div><!--show1结束-->


	<div class="show2">	<!--show2开始-->	
		    <div class="clearfix cl" style="width: 100%;">
		    <!--<label class="form-label col-2">参数设置：</label>-->
				<div class="formControls col-8 cset" style="width: 95%;">
					
					<div class="parameterList">
						<!-- <input type="" name="param_key" value=""> -
						<input type="" name="param_value" value="">
						<button type="button" class="btn btn-xs">移除</button> -->
						<div class="cname"><span>参数名称</span><span>参数值</span></div>
						<?php if(is_array($gr_info["GR_Parameter"])): foreach($gr_info["GR_Parameter"] as $k=>$v): ?><div class="gap">
							<input type="" name="param_key[]" placeholder="参数名" value="<?php echo ($v["key"]); ?>">
							<input type="" name="param_value[]" placeholder="参数值" value="<?php echo ($v["value"]); ?>"> 
							<button type="button" class="btn btn-xs removeParameter">移除</button><br/>
							</div><?php endforeach; endif; ?>
					</div>
					<hr style="height:2px;border:none;border-top:2px solid #d5d5d5;margin:5px 0"/>
					<button type="button" class="btn btn-xs btn-primary addParameter" style="margin-top:6px;">添加参数</button>
		        </div>
		    </div>
	</div><!--show2结束-->
	
	
	<div class="show3"><!--show3开始-->
		    <div class="clearfix cl">
				<!--是否启用多规格-->
				<div class="form-group">
				    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否启用商品规格</label>
				    <div class="col-sm-9 col-xs-12">
					    <label class="checkbox-inline">
					    	<?php if($gr_info["GR_Is_Options"] == 0 or $gr_info["GR_Is_Options"] == ''): ?><input type="checkbox" id="hasoption" value="2" name="hasoption" />
					    	<?php elseif($gr_info["GR_Is_Options"] == 1): ?>
								<input type="checkbox" id="hasoption" value="1" name="hasoption" checked="" /><?php endif; ?>
								启用商品规格
						</label>
					  	<span class="help-block">启用商品规格后，商品的价格及库存以商品规格为准,库存设置为0则不显示,-1为不限制</span>
					</div>
				</div>
				<!--是否启用多规格结束-->
				 
				<div id='tboption'>
					<!--规格-->
					<div id='specs'>
						<img width="100%" height="100%" />
					</div>
					<!--规格结束-->

					<!--添加规格-->
					<table class="table">
						<tr><td><h4>
							<a href="javascript:;" class='btn btn-primary' id='add-spec' onclick="addSpec()" style="margin-top:10px;margin-bottom:10px;" title="添加规格"><i class='fa fa-plus'></i> 添加规格</a> 
							<a href="javascript:;" onclick="calc()" title="刷新规格项目表" class="btn btn-primary"><i class="fa fa-refresh"></i> 刷新规格项目表</a>
						</h4></td></tr>
					</table>
					<!--添加规格结束-->

					<!--设置规格-->
					<div class="panel-body table-responsive" id="options" style="padding:0;">
					</div>
					<!--设置规格结束-->
				</div>
			</div>
	</div><!--show3结束-->
	
		    <br/><br/><br/>
			<div class="clearfix cl btn_f">
				<div class="Button_operation">
					<button class="btn btn-primary radius" onclick="addCheck()" type="button" id="send"><i class="icon-save"></i>保存</button>&nbsp;&nbsp;
					<!-- <button onClick="article_save();" class="btn btn-secondary  btn-warning" type="button"><i class="icon-save"></i>保存草稿</button> -->
					<button onClick="layer_close();" class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
				</div>
			</div>






		</form>
</div>
	</div>
</div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                &times;
            </button>

				<ul class="nav nav-pills" role="tablist">
					<li id="li_upload" class="active" role="presentation">
						<a href="#upload" aria-controls="upload" role="tab" data-toggle="tab">上传图片</a>
					</li>
					<li id="li_network" class="" role="presentation">
						<a href="#network" aria-controls="network" role="tab" data-toggle="tab">提取网络图片</a>
					</li>
					<li id="li_history_image" class="" role="presentation">
						<a href="#history_image" aria-controls="history_image" role="tab" data-toggle="tab">浏览图片</a>
					</li>
				</ul>

			</div>
			<div class="modal-body modal-body-more">

				
			</div>
			
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal -->
</div>

</body>
</html>

<script src="/Public/admin/js/jquery-1.9.1.min.js"></script>   
<script src="/Public/admin/assets/js/bootstrap.min.js"></script>
<script src="/Public/admin/assets/js/typeahead-bs2.min.js"></script>
<script src="/Public/admin/assets/layer/layer.js" type="text/javascript" ></script>
<script src="/Public/admin/assets/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript" src="/Public/admin/Widget/My97DatePicker/WdatePicker.js"></script> 
<script type="text/javascript" src="/Public/admin/Widget/icheck/jquery.icheck.min.js"></script> 
 <script type="text/javascript" src="/Public/admin/Widget/zTree/js/jquery.ztree.all-3.5.min.js"></script> 
<script type="text/javascript" src="/Public/admin/Widget/Validform/5.3.2/Validform.min.js"></script> 

<script type="text/javascript" src="/Public/admin/Widget/ueditor/1.4.3/ueditor.config.js"></script>
<script type="text/javascript" src="/Public/admin/Widget/ueditor/1.4.3/ueditor.all.min.js"> </script>
<script type="text/javascript" src="/Public/admin/Widget/ueditor/1.4.3/lang/zh-cn/zh-cn.js"></script> 
<script src="/Public/admin/js/lrtk.js" type="text/javascript" ></script>
<script type="text/javascript" src="/Public/admin/js/H-ui.js"></script> 
<script type="text/javascript" src="/Public/admin/js/H-ui.admin.js"></script>
<script>
categoryInit();
function categoryInit(){
	$('.checkSoncategory').unbind('change').bind('change',function(){ 
		var that = $(this).parent().parent();
		var cid =$(this).children('option:selected').val(); 
		// alert(cid);
		if(cid!=null && cid!=0){
			$.ajax({
				url:'/index.php/Admin/Goods/checkSoncategory',
				data:{cid:cid},
				type:'post',
				async:false,
				success:function(data){
					var str = '';
					if(data!=null && data['status'] == true){
						str +='<div class="formControls col-2 categoryContainer classify_f">';
						str +='<span class="select-box">';
						str +='<select class="select checkSoncategory"  name="goods_category[]" >';
						str +='<option value="0">请选择</option>';
						for(var i in data['info']){
							str +='<option value="'+data['info'][i]['CR_ID']+'">'+data['info'][i]['CR_Name']+'</option>';
						}
						str +='<select/>';
						str +='</span></div>';
						// that.after().remove();
						that.nextAll().remove();
						that.after(str);
					}else{
						that.nextAll().remove();
					}
					categoryInit();
				}
			});
		}else if(cid==0){
			that.nextAll().remove();
			categoryInit();
		}

	});
}
	

$(function(){
	var ue = UE.getEditor('editor');
});

$(".addParameter").on('click',function(){
	var str = '';
	str +='<div class="gap">';
	str +='<input type="" name="param_key[]" placeholder="参数名" value="">';
	str +='<input type="" name="param_value[]" placeholder="参数值" value=""> ';
	str +='<button type="button" class="btn btn-xs removeParameter">移除</button><br/>';
	str +='</div>';
	$(".parameterList").append(str);
	removeParameter();
});	
function removeParameter(){
	$(".removeParameter").unbind('click').bind('click',function(){
		$(this).parent().remove();
	});
}
	
</script>

<script type="text/javascript">
	//初始化
	changeCol(0);
	//选项卡
	function changeCol(index){
		// alert($('.tabs li').eq(index).html());
		$('.tabs li').removeClass('change_col');
		$('.tabs li').eq(index).addClass('change_col');
		switch(index){
			case 0 :
				$('.show1,.show2,.show3').css('display','none');
				$('.show0').css('display','block');
				break;
			case 1 :
				$('.show0, .show2, .show3').css('display','none');
				$('.show1').css('display','block');
				break;
			case 2 :
				$('.show0, .show1, .show3').css('display','none');
				$('.show2').css('display','block');
				break;
			case 3 :
				$('.show0, .show1, .show2').css('display','none');
				$('.show3').css('display','block');
				break;
		}
	}


////启用多规格，部分选项关闭
//$('#hasoption').on('click',function(){
//	if($(this).is(':checked')){
//		$('input[name=goods_weight] , input[name = goods_costPrice] , input[name=goods_price],input[name=goods_stock]').attr('disabled','true');
//	}
//	else{
//		$('input[name=goods_weight] , input[name = goods_costPrice] , input[name=goods_price],input[name=goods_stock]').removeAttr('disabled');
//	}
//});
////初始disabled
//if($('#hasoption').is(':checked')){
//		$('input[name=goods_weight] , input[name = goods_costPrice] , input[name=goods_price],input[name=goods_stock]').attr('disabled','true');
//	}
//	else{
//		$('input[name=goods_weight] , input[name = goods_costPrice] , input[name=goods_price],input[name=goods_stock]').removeAttr('disabled');
//	}


</script>
<script>
	$(document).ready(function(){
		// $(".addParameter").click();
	})
</script>

<script src="/Public/admin/assets/webuploader/js/pagination.js"></script>
<script type="text/javascript" src="/Public/admin/assets/webuploader/js/webuploader.js"></script>
<script type="text/javascript" src="/Public/admin/assets/webuploader/js/myupload.js"></script>
 
<script src="/Public/admin/js/gui.js"></script>
<!-- <script type="text/javascript" src="/Public/admin/assets/webuploader/js/demo.js"></script> -->