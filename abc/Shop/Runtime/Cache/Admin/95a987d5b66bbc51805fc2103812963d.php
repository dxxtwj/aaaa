<?php if (!defined('THINK_PATH')) exit();?><!-- 添加详情图 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>添加详情图</title>
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
 <link href="/Public/Admin/assets/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="/Public/Admin/css/style.css"/>       
        <link rel="stylesheet" href="/Public/Admin/assets/css/ace.min.css" />
        <link rel="stylesheet" href="/Public/Admin/assets/css/font-awesome.min.css" />
        <link href="/Public/Admin/Widget/icheck/icheck.css" rel="stylesheet" type="text/css" />
		<!--[if IE 7]>
		  <link rel="stylesheet" href="/Public/Admin/assets/css/font-awesome-ie7.min.css" />
		<![endif]-->
        <!--[if lte IE 8]>
		  <link rel="stylesheet" href="/Public/Admin/assets/css/ace-ie.min.css" />
		<![endif]-->
	    <script src="/Public/Admin/js/jquery-1.9.1.min.js"></script>
        <script src="/Public/Admin/assets/js/bootstrap.min.js"></script>
        <script src="/Public/Admin/assets/js/typeahead-bs2.min.js"></script>
         <script src="/Public/Admin/assets/layer/layer.js" type="text/javascript"></script>
        <script type="text/javascript" href="/Public/Admin/Widget/swfupload/swfupload.js"></script>
        <script type="text/javascript" href="/Public/Admin/Widget/swfupload/swfupload.queue.js"></script>
        <script type="text/javascript" href="/Public/Admin/Widget/swfupload/swfupload.speed.js"></script>
        <script type="text/javascript" href="/Public/Admin/Widget/swfupload/handlers.js"></script>
</head>

<body>
<div class=" clearfix">
 <div id="add_brand" class="clearfix" >
 <div class="left_add" style="width: 1500px">
   <div class="title_name" style="width: inherit;">添加详情图</div>

   <form id="files" action="<?php echo U('Shop/chulixiangqing', ['id' => $gid]);?>" method='post' enctype="multipart/form-data">

   <ul class="add_conent">
   <!-- name="pinpainame" 是因为用了字段映射 -->
    <li class=" clearfix"><label class="label_name">商品详情图：</label>
			<div style='display:inline;' class="demo l_f"><span style="display: inline-block; " class="btn btn-xs btn-info"><i class="icon-edit bigger-120"></i>点我上传logo</span>

		        <div id = 'img' style=" margin-top: 10px; position: relative; ">
	        		<input style="position:absolute;top: -36px; opacity: 0; " id='tijiao' type="file" multiple  name='logo[]' />
	       			<div class="prompt" style="margin-top: 10px;"><p>图片大小<b>120px*60px</b>图片大小小于5MB,</p><p>支持.jpg;.gif;.png;.jpeg格式的图片</p>
		        </div>
		       </div>
			</div>
    </li>
        
  <div class="button_brand" style='margin-left: -150px;'><button class="btn btn-warning" >提交</button>
  <span class="btn btn-warning" /><a style='color: #000' href="#">去修改</a></span></div>
 
 </div>
  <span style="margin-top: 129px; margin-left: 671px; position: relative; z-index: 500;" class="btn btn-warning" /><a style='color: #000' href="<?php echo U('Shop/index');?>">返回</a></span></div>
   </form>
</div>
</body>
</html>
<script type="text/javascript">
     
	function checkLength(which) {
	var maxChars = 500;
	if(which.value.length > maxChars){
	   layer.open({
	   icon:2,
	   title:'提示框',
	   content:'您出入的字数超多限制!',	
    });
		// 超过限制的字数了就将 文本框中的内容按规定的字数 截取
		which.value = which.value.substring(0,maxChars);
		return false;
	}else{
		var curr = maxChars - which.value.length; // 减去 当前输入的
		document.getElementById("sy").innerHTML = curr.toString();
		return true;
	}
}
//下拉框交换JQuery
$(function(){


    //移到右边
    $('#add').click(function() {
    //获取选中的选项，删除并追加给对方
        $('#select1 option:selected').appendTo('#select2');
    });
    //移到左边
    $('#remove').click(function() {
        $('#select2 option:selected').appendTo('#select1');


    });
    // //全部移到右边
    // $('#add_all').click(function() {
    //     //获取全部的选项,删除并追加给对方
    //     $('#select1 option').appendTo('#select2');
    // });
    // //全部移到左边
    // $('#remove_all').click(function() {
    //     $('#select2 option').appendTo('#select1');
    // });
    //双击选项
    $('#select1').dblclick(function(){ //绑定双击事件
        //获取全部的选项,删除并追加给对方
        $("option:selected",this).appendTo('#select2'); //追加给对方
    });
    //双击选项
    $('#select2').dblclick(function(){
       $("option:selected",this).appendTo('#select1');
    });
});
		
  
</script>
<script type="text/javascript">
	// window.onload = function () {
		/**
		 * 显示上传按钮
		 */
		// up = function up(a) {
		// 	// 显示或隐藏
		// 	$('#img').toggle('slow');
		// }

		// 定义存储用的数组
	// 	var arr = [];
	// 	$("#select1").change(function() {
	// 		var int = jQuery.inArray(this.value, arr)
			
	// 		if (int < 0) {
	// 			// 把值存进数组
	// 			var ss = arr.push(this.value);
	// 		}

	// 		// 为后面ajax做准备
	// 		var id = this.value;

	// 		//  追加到隐藏域，为form提交做准备
	// 		$('#hid').attr("value", arr);

	// 		// ajax
	// 		$.ajax({

	// 			// 请求地址
	// 			url: "<?php echo U('Brand/add');?>",

	// 			// 类型
	// 			type: 'get',

	// 			// 商品id
	// 			data: 'id='+ id,

	// 			// 返回数据并追加
	// 			success: function(msg) {
					
	// 				$('#select2').append("<span style='width: 220px; display: block; text-align: center; border: 1px solid #000; margin-top: 5px; value='" + msg.id + "''>"+ msg.name +"</span>");
	// 			}

	// 		});

	// 	});

	// 	// 改变触发
	// 	$('#tijiao').change(function() {
			
	// 		// 表单失去焦点触发
	// 		$('#tijiao').wrap('<form action="<?php echo U("Brand/scan");?>" method="post" enctype="multipart/form-data""></form>').parent().submit();
	// 	});
	// }
</script>