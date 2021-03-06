<?php if (!defined('THINK_PATH')) exit();?><!-- 添加商品详情资料 -->
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<!--[if lt IE 9]>
<script type="text/javascript" src="/Public/Admin/js/html5.js"></script>
<script type="text/javascript" src="/Public/Admin/js/respond.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/PIE_IE678.js"></script>
<![endif]-->
<link href="/Public/Admin/assets/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="/Public/Admin/css/style.css"/>       
<link href="/Public/Admin/assets/css/codemirror.css" rel="stylesheet">
<link rel="stylesheet" href="/Public/Admin/assets/css/ace.min.css" />
      <link rel="stylesheet" href="/Public/Admin/Widget/zTree/css/zTreeStyle/zTreeStyle.css" type="text/css">
<link rel="stylesheet" href="/Public/Admin/assets/css/font-awesome.min.css" />
<!--[if IE 7]>
		  <link rel="stylesheet" href="/Public/Admin/assets/css/font-awesome-ie7.min.css" />
		<![endif]-->
<link href="/Public/Admin/Widget/icheck/icheck.css" rel="stylesheet" type="text/css" />
<link href="/Public/Admin/Widget/webuploader/0.1.5/webuploader.css" rel="stylesheet" type="text/css" />
<!-- 引入JQ -->
	<script src="/Public/Admin/js/jquery.min.js"></script>
<title>新增图片</title>
</head>
<body>
<div class="clearfix" id="add_picture">
<div id="scrollsidebar" class="left_Treeview">
    
  </div>
   <div class="page_right_style" style="margin-left: -220px; width: 1800px;">
   <div class="type_title">添加商品</div>
	<form action="<?php echo U('Shop/chuliadd');?>" method="post" class="form form-horizontal" id="form-article-add" enctype="multipart/form-data">
		<div class="clearfix cl">
         <label class="form-label col-2"><span class="c-red">*</span>商品标题：</label>
		 <div class="formControls col-10"><input type="text" class="input-text" value="" placeholder="" id="" name="name"></div>
		</div>
<!-- 		<div class=" clearfix cl">
         <label class="form-label col-2">简略标题：</label>
	     <div class="formControls col-10"><input type="text" class="input-text" value="" placeholder="" id="" name=""></div>
		</div> -->
		<div class=" clearfix cl">
			<div class="clearfix cl">
			<div class="formControls col-10">
			<label class="form-label col-2">关键词：</label>
				<input type="text" class="input-text" value="" placeholder="" id="" name="keyman" style='width: 1407px;'>
			</div>
		</div>
			<div class="Add_p_s">
             <label class="form-label col-2">市场价：</label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="" placeholder="" id="" name="ymoney"></div>
			</div>
            <div class="Add_p_s">
             <label class="form-label col-2">列表显示价格:</label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="" placeholder="" id="" name="money"></div>
			</div>

             <div class="Add_p_s">
             <label class="form-label col-2">所属分类：</label>
			 <div class="formControls col-2"><span class="select-box">
				<select name="cid">
					<option value="" selected>--请选择分类--</option>
					<?php
 foreach ($arr as $v): $num = substr_count($v['path'], ','); $dis = ($num>2)?'':'disabled'; $str = str_repeat('　　', $num-1); ?>
                            <option 
                            <?=$dis?> value="<?=$v['id']?>">
                            <?=$str.'┕ '.$v['name']?>
                            </option>
					?>
					<?php
 endforeach;?>
				</select>
				</span></div>
			</div>
             <div class="Add_p_s">
             <label class="form-label col-2">所属品牌：</label>
			 <div class="formControls col-2"><span class="select-box">
				<select name="pid">
					<option value="" selected>--请选择品牌--</option>
						<?php if(is_array($list)): foreach($list as $key=>$val): ?><option value="<?php echo ($val['id']); ?>"><?php echo ($val['name']); ?></option><?php endforeach; endif; ?>
				</select>
				</span></div>
			</div>
			
			<!-- <div class="Add_p_s">
            <label class="form-label col-2">库　　　存</label>
			<div class="formControls col-2"><input type="text" class="input-text" value="" placeholder="" id="" name="num"></div>
            </div> -->
			<div class="Add_p_s">
             <label class="form-label col-2">原料产地：</label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="" placeholder="" id="" name="chandi"></div>
			</div>
		</div>
		<div class=" clearfix cl"  style="width: 1200px;">
			
            <div class="Add_p_s">
             <label class="form-label col-2">原　　　料：</label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="" placeholder="" id="" name="material"></div>
			</div>
            <div class="Add_p_s">
             <label class="form-label col-2">生成许可证：</label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="" placeholder="" id="" name="norm"></div>
			</div>

             <div class="Add_p_s">
             <label class="form-label col-2">产品重量：</label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="" placeholder="" id="" name="weight" >kg</div>
			</div>
             <div class="Add_p_s">
             <label class="form-label col-2">保质期(月)</label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="" placeholder="" id="" name="baozhiqi" ></div>
			</div>
             <div class="Add_p_s">
             <label class="form-label col-2">商品的储存方式 </label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="" placeholder="" id="" name="stockpile" ></div>
			</div>


             <!-- <div class="Add_p_s">
             <label class="form-label col-2">口味 </label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="" placeholder="" id="" name="kouwei" ></div>
			</div>


             <div class="Add_p_s">
             <label class="form-label col-2">包装 </label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="" placeholder="" id="" name="baozhuang" ></div>
			</div> -->

		 <div class="Add_p_s">
         <label class="form-label col-2">上传商品图片 </label>	
		 <div class="formControls col-2"><input type="file" class="input-text" multiple name="pic[]" ></div>
		</div>
		<!-- <div onclick="dianji(this)"  style="position: relative; z-index: 1; margin-left: 228px; float: left; margin-top: 30px; margin-bottom: 30px;"><h1><span class="btn">点我添加属性</span></h1></div>
		<div id="zhuijia" style="width: 1189px; height: auto; float: left;"> -->
			<!-- 获取替换内容 -->
			<!-- <div id="divs">
				<div style="float: left; "><span  style="font-size: 20px;">大属性：</span><input style="position: relative; z-index: 1" type="text" name="dattrbute[]"></div>
				<div style="float: left; "><span  style="font-size: 20px;">小属性：</span><input style="position: relative; z-index: 1" type="text" name="xattrbute[]"></div>
				
				<div style="clear: both;"></div>

				<div style="float: left;  "><span  style="font-size: 20px;">大属性：</span><input style="position: relative; z-index: 1" type="text" name="dattrbute[]"></div>
				<div style="float: left;  "><span  style="font-size: 20px;">小属性：</span><input style="position: relative; z-index: 1" type="text" name="xattrbute[]"></div>
				<div style="clear: both;"></div>
				<div style="float: left;  "><span  style="font-size: 20px;">库　存：</span><input style="position: relative; z-index: 1" type="text" name="num[]"></div>
				<div style="float: left;"><span  style="font-size: 20px; ">价　钱：</span><input style="position: relative; z-index: 1" type="text" name="price[]"></div>
				<div onclick="del(this)" style="float: left; margin-top:2px; position:relative;z-index:1;"><span style="font-size: 12px; color: red; margin-left: 20px;" class="btn btn-danger">取消</span></div>
				<div style="clear: both; margin-bottom: 20px;"></div>
			</div>
		</div> -->
		<div class="clearfix cl">
			<div class="formControls col-10">
			<label class="form-label col-2">简单介绍：</label>
				<textarea name="content" cols="" rows="" class="textarea"  placeholder="说点什么...最少输入10个字符" datatype="*10-100" dragonfly="true" nullmsg="备注不能为空！" onKeyUp="textarealength(this,200)"></textarea>
			</label>
				<p class="textarea-numberbar"><em class="textarea-length">0</em>/200</p>
			</div>

		</div>
		</div>
		<div class="clearfix cl" '>
			<div class="Button_operation">
				<button style='margin-left: -230px;' class="btn btn-primary radius" type="submit"><i class="icon-hand-right "></i>下一步</button>
				<a class="btn btn-default radius" >&nbsp;&nbsp;返回&nbsp;&nbsp;</a>
			</div>
		</div>
	</form>
    </div>
</div>
</div>

</body>

<script>
 /**
  * $a "是点我添加属性"
  */
 function dianji(a) {

 	// 获取要添加的内容
	var str = $('#zhuijia').html();
	
	// 追加要添加的内容
 	$('#zhuijia').after(str);
 }
/**
 * 这是取消按钮
 * $b 是"取消"
 */
 function del(b) {

 	$(b).parent().remove();
 }
</script>
</html>