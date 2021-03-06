<?php if (!defined('THINK_PATH')) exit();?><!-- 修改商品详情资料 -->
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
	<script src="/Public/Admin/js/jquery.min.js"></script>
	
<title>新增图片</title>
</head>
<body>
<div class="clearfix" id="add_picture">
<div id="scrollsidebar" class="left_Treeview">
    
  </div>
   <div class="page_right_style" style="margin-left: -220px; width: 1800px;">
   <div class="type_title">修改商品</div>
	<form action="<?php echo U('Shop/editgoods');?>" method="post" class="form form-horizontal" id="form-article-add" enctype="multipart/form-data">
	<input type="hidden" name='id' value="<?php echo ($list['id']); ?>">
	
		<div class="clearfix cl">
         <label class="form-label col-2"><span class="c-red">*</span>商品标题：</label>
		 <div class="formControls col-10"><input type="text" class="input-text" value="<?php echo ($list['name']); ?>" placeholder="" id="" name="name"></div>
		</div>
<!-- 		<div class=" clearfix cl">
         <label class="form-label col-2">简略标题：</label>
	     <div class="formControls col-10"><input type="text" class="input-text" value="" placeholder="" id="" name=""></div>
		</div> -->
		<div class=" clearfix cl">
			<div class="clearfix cl">
			<div class="formControls col-10">
			<label class="form-label col-2">关键词：</label>
				<input type="text" class="input-text" value="<?php echo ($list['keyman']); ?>" placeholder="" id="" name="keyman" style='width: 1407px;'>
			</div>
		</div>
			<div class="Add_p_s">
             <label class="form-label col-2">市场价：</label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="<?php echo ($list['ymoney']); ?>" placeholder="" id="" name="ymoney"></div>
			</div>

			 <div class="Add_p_s">
             <label class="form-label col-2">列表显示价格:</label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="<?php echo ($list['money']); ?>" placeholder="" id="" name="money"></div>
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
				<select name="pinpai">
					<option value="" selected>--请选择品牌--</option>
						<?php if(is_array($pinpaiAll)): foreach($pinpaiAll as $key=>$val): ?><option value="<?php echo ($val['id']); ?>"><?php echo ($val['name']); ?></option><?php endforeach; endif; ?>
				</select>
				</span></div>
			</div>

		</div>
		<div class=" clearfix cl"  style="width: 1200px;">
			
			<div class="Add_p_s">
             <label class="form-label col-2">原料产地：</label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="<?php echo ($list['chandi']); ?>" placeholder="" id="" name="chandi"></div>
			</div>
            <div class="Add_p_s">
             <label class="form-label col-2">原　　　料：</label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="<?php echo ($list['material']); ?>" placeholder="" id="" name="material"></div>
			</div>
            <div class="Add_p_s">
             <label class="form-label col-2">生成许可证：</label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="<?php echo ($list['norm']); ?>" placeholder="" id="" name="norm"></div>
			</div>

             <div class="Add_p_s">
             <label class="form-label col-2">产品重量：</label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="<?php echo ($list['weight']); ?>" placeholder="" id="" name="weight" >kg</div>
			</div>
             <div class="Add_p_s">
             <label class="form-label col-2">保质期(月)</label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="<?php echo ($list['baozhiqi']); ?>" placeholder="" id="" name="baozhiqi" ></div>
			</div>
             <div class="Add_p_s">
             <label class="form-label col-2">商品的储存方式 </label>	
			 <div class="formControls col-2"><input type="text" class="input-text" value="<?php echo ($list['stockpile']); ?>" placeholder="" id="" name="stockpile" ></div>
			</div>


		<!-- 	<div class="Add_p_s">
             <label class="form-label col-2">更改商品状态</label>
			 <div class="formControls col-2">
				<select name="state">
					<option value="" selected >--请选择状态--</option>
						<option <?php echo ($list['state'] == 1 ? selected : ''); ?> value="1">新上架</option>
						<option <?php echo ($list['state'] == 2 ? selected : ''); ?> value="2">开启销售</option>
						<option <?php echo ($list['state'] == 3 ? selected : ''); ?> value="3">下架商品</option>
				</select>
				</div>
			</div>	 -->	

		 <div class="Add_p_s">
         <label class="form-label col-2">上传商品图片 </label>	
		 <div class="formControls col-2"><input type="file" class="input-text" name="pic[]" multiple ></div>
		</div>
		<!-- <div style="float: left; position: relative; z-index: 1; margin-left: 29px;"><span style="font-size: 15px;">修改属性：</span>
			<select name="dsx" id="dsx">
				<option value="#">--大属性--</option>
		      <?php if(is_array($daShuXingList)): foreach($daShuXingList as $key=>$a): ?><option name="dattrbute" value="<?php echo ($a['id']); ?>"><?php echo ($a['dattrbute']); ?></option><?php endforeach; endif; ?>
			</select>
			<select name="xsx" id="xsx" style="margin-left: 10px;">
				<option value="#">--小属性--</option>
			</select>

		</div>
		<div onclick="xg(this)" style="margin-left: 30px; float: left; position: relative; z-index: 1;"><span style=" display: inline-block; height: 32px;" class="btn btn-primary">确定修改</span></div> -->


		<!-- <div onclick="dianji(this)"  style="position: relative; z-index: 1; margin-left: -480px; float: left; margin-top: 80px; margin-bottom: 30px;"><h1><span class="btn">点我添加属性</span></h1></div>
		<div id="zhuijia" style="width: 1189px; height: auto; float: left;">
			<!-- 获取替换内容 -->
			<div id="divs"> -->

				<!-- <div style="float: left; "><span  style="font-size: 20px;">大属性：</span>
				<input style="position: relative; z-index: 1" type="text" name="dattrbu
				te[]" value="<?php echo ($v['dattrbute']); ?>" ></div>

				<div style="float: left; "><span  style="font-size: 20px;">小属性：</span>
				<input style="position: relative; z-index: 1" type="text" name="xattrbu
				te[]" value="<?php echo ($val['xattrbute']); ?>"></div>
				<div style="clear: both;"></div>

				<div style="float: left; "><span  style="font-size: 20px;">库　存：</span>
				<input value="<?php echo ($detailedArray['num']); ?>" style="position: relative; z-index: 1" type="text" name="num[]">
				</div>

				<div style="float: left;"><span  style="font-size: 20px; ">价　钱：</span>
				<input value="<?php echo ($detailedArray['price']); ?>" style="position: relative; z-index: 1" type="text" name="price[]
				"></div>

				<div onclick="del(this)" style="float: left; margin-top:2px; position:relative;z-index:1;"><span style="font-size: 12px; color: red; margin-left: 20px;" class="btn btn-danger">取消</span></div>
				<div style="clear: both; margin-bottom: 20px;"></div>
			</div>
		</div> -->
	<script>
		$('#dsx').change(function() {

			// 清空小属性
			$('select[name="xsx"]')[0].length = 1;

			// 获取大属性的ID
			var id = $(this).val();

			$.ajax({

				type: 'get',

				url: "<?php echo U('Shop/attribute');?>",

				data: {dtypeid: id},

				success: function(msg) {
					
					if (msg != 2) {
						$('#xsx').append("<option value="+msg[0].id+">"+msg[0].xattrbute+"</option>");
					}
				}

			});
		});
		// function xg(c) {
		// 	var xsx = $("select[name='xsx']").val();
		// 	var dsx = $("select[name='dsx']").val();
			
		// 	$.ajax({

		// 		type: 'get',

		// 		url: "",

		// 		data: {},

		// 		success: function() {}

		// 	});
		// }

	</script>
		<div class="clearfix cl">
			<div class="formControls col-10">
			<label class="form-label col-2">简单介绍：</label>
				<textarea name="content" cols="" rows="" class="textarea"  placeholder="说点什么...最少输入10个字符" datatype="*10-100" dragonfly="true" nullmsg="备注不能为空！" onKeyUp="textarealength(this,200)"><?php echo ($list['content']); ?></textarea>
			</label>
				<p class="textarea-numberbar"><em class="textarea-length">0</em>/200</p>
			</div>

		</div>
		</div>
		<div class="clearfix cl" '>
			<div class="Button_operation">
				<button style='margin-left: -230px;' class="btn btn-primary radius" type="submit"><i class="icon-hand-right "></i>下一步</button>
				<a href="<?php echo U('Shop/index');?>" class="btn btn-default radius" >&nbsp;&nbsp;返回&nbsp;&nbsp;</a>
			</div>
		</div>
	</form>
    </div>
</div>
</div>
<script src="/Public/Admin/js/jquery-1.9.1.min.js"></script>   
<script src="/Public/Admin/assets/js/bootstrap.min.js"></script>
<script src="/Public/Admin/assets/js/typeahead-bs2.min.js"></script>
<script src="/Public/Admin/assets/layer/layer.js" type="text/javascript" ></script>
<script src="/Public/Admin/assets/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript" src="/Public/Admin/Widget/My97DatePicker/WdatePicker.js"></script> 
<script type="text/javascript" src="/Public/Admin/Widget/icheck/jquery.icheck.min.js"></script> 
<script type="text/javascript" src="/Public/Admin/Widget/zTree/js/jquery.ztree.all-3.5.min.js"></script> 
<script type="text/javascript" src="/Public/Admin/Widget/Validform/5.3.2/Validform.min.js"></script> 
<script type="text/javascript" src="/Public/Admin/Widget/webuploader/0.1.5/webuploader.min.js"></script>
<script type="text/javascript" src="/Public/Admin/Widget/ueditor/1.4.3/ueditor.config.js"></script>
<script type="text/javascript" src="/Public/Admin/Widget/ueditor/1.4.3/ueditor.all.min.js"> </script>
<script type="text/javascript" src="/Public/Admin/Widget/ueditor/1.4.3/lang/zh-cn/zh-cn.js"></script> 
<script src="/Public/Admin/js/lrtk.js" type="text/javascript" ></script>
<script type="text/javascript" src="/Public/Admin/js/H-ui.js"></script> 
<script type="text/javascript" src="/Public/Admin/js/H-ui.admin.js"></script> 
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