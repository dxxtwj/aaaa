<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Cache-Control" content="no-siteapp" />
 <link href="/abc/Public/Admin/assets/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="/abc/Public/Admin/css/style.css"/>       
        <link href="/abc/Public/Admin/assets/css/codemirror.css" rel="stylesheet">
        <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace.min.css" />
        <link rel="stylesheet" href="/abc/Public/Admin/font/css/font-awesome.min.css" />
        <!--[if lte IE 8]>
		  <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace-ie.min.css" />
		<![endif]-->
		<script src="/abc/Public/Admin/js/jquery-1.9.1.min.js"></script>
        <script src="/abc/Public/Admin/assets/js/bootstrap.min.js"></script>
		<script src="/abc/Public/Admin/assets/js/typeahead-bs2.min.js"></script>           	
		<script src="/abc/Public/Admin/assets/js/jquery.dataTables.min.js"></script>
		<script src="/abc/Public/Admin/assets/js/jquery.dataTables.bootstrap.js"></script>
        <script src="/abc/Public/Admin/assets/layer/layer.js" type="text/javascript" ></script>          
        <script src="/abc/Public/Admin/assets/laydate/laydate.js" type="text/javascript"></script>
        <script src="/abc/Public/Admin/js/lrtk.js" type="text/javascript" ></script>

        <link href="/abc/Public/Admin/assets/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="/abc/Public/Admin/css/style.css"/>       
        <link href="/abc/Public/Admin/assets/css/codemirror.css" rel="stylesheet">
        <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace.min.css" />
        <link rel="stylesheet" href="/abc/Public/Admin/font/css/font-awesome.min.css" />
        <!--[if lte IE 8]>
      <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace-ie.min.css" />
    <![endif]-->
        <script src="/abc/Public/Admin/js/jquery-1.9.1.min.js"></script>
          <script src="/abc/Public/Admin/assets/js/bootstrap.min.js"></script>
         <script src="/abc/Public/Admin/assets/js/typeahead-bs2.min.js"></script>             
        <script src="/abc/Public/Admin/assets/js/jquery.dataTables.min.js"></script>
        <script src="/abc/Public/Admin/assets/js/jquery.dataTables.bootstrap.js"></script>
        <script src="/abc/Public/Admin/assets/layer/layer.js" type="text/javascript" ></script>          
        <script src="/abc/Public/Admin/assets/laydate/laydate.js" type="text/javascript"></script>
        <script src="/abc/Public/Admin/js/lrtk.js" type="text/javascript" ></script>
        <script src="/abc/Public/Admin/assets/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="/abc/Public/Admin/css/style.css"/>       
        <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace.min.css" />
        <link rel="stylesheet" href="/abc/Public/Admin/assets/css/font-awesome.min.css" />
        <link rel="stylesheet" href="/abc/Public/Admin/Widget/zTree/css/zTreeStyle/zTreeStyle.css" type="text/css">
        <link href="/abc/Public/Admin/Widget/icheck/icheck.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
    .num{
        padding:4px;
        font-size: 18px;
        border:1px solid #ccc;
        color:#000;
    }
    .current{
        padding:4px;
        font-size: 18px;
        border:1px solid #ccc;
        color:#000;      
    }
    </style>
<title>订单处理</title>
</head>

<body>
<div class="clearfix">
 <div class="handling_style" id="order_hand">
      <div id="scrollsidebar" class="left_Treeview">
        <div class="show_btn" id="rightArrow"><span></span></div>
        <div class="widget-box side_content" >
         <div class="side_title"><a title="隐藏" class="close_btn"><span></span></a></div>
         <div class="side_list"><div class="widget-header header-color-green2"><h4 class="lighter smaller">订单操作</h4></div>
         <div class="widget-body">
          <ul class="b_P_Sort_list">
             <li><i class="orange  fa fa-reorder"></i><a href="<?php echo U('Order/index');?>">全部订单(<?php echo ($total['1']); ?>)</a></li>
             <li><i class="fa fa-sticky-note pink "></i> <a href="<?php echo U('Order/index', ['id'=>'1']);?>">等待付款(<?php echo ($total['2']); ?>)</a></li>
             <li><i class="fa fa-sticky-note pink "></i> <a href="<?php echo U('Order/index', ['id'=>'2']);?>">待发货(<?php echo ($total['3']); ?>)</a> </li>
             <li><i class="fa fa-sticky-note pink "></i> <a href="<?php echo U('Order/index', ['id'=>'3']);?>">已发货(<?php echo ($total['4']); ?>)</a></li>
             <li><i class="fa fa-sticky-note pink "></i> <a href="<?php echo U('Order/index', ['id'=>'4']);?>">订单完成(<?php echo ($total['5']); ?>)</a></li>
            </ul>
        </div>
       </div>
      </div>  
     </div>
 <div class="order_list_style" id="order_list_style">
  <div class="search_style">
     
      <ul class="search_content clearfix">
       <li><label class="l_f">订单编号</label><input name="" type="text"  class="text_add" placeholder="输入订单编号"  style=" width:250px"/></li>
       <li><label class="l_f">交易时间</label><input class="inline laydate-icon" id="start" style=" margin-left:10px;"></li>
       <li style="width:90px;"><button type="button" class="btn_search"><i class="fa fa-search"></i>查询</button></li>
      </ul>
    </div>
    <!--交易订单列表-->
    <div class="Orderform_list">
       <table class="table table-striped table-bordered table-hover" id="sample-table">
		<thead>
		 <tr>
				<th width="25px"><label><input type="checkbox" class="ace"><span class="lbl"></span></label></th>
				<th width="100px">订单编号</th>
				<th width="90px">创建时间</th>
				<th width="100px">收件人</th>				
                <th width="100px">联系电话</th>				
				<th width="180px">收件人地址</th>
                <th width="80px">订单金额</th>
				<th width="70px">状态</th>
                <th width="100px">配送方式</th>                
				<th width="200px">操作</th>
			</tr>
		</thead>
	<tbody>
  <?php if(is_array($arr)): foreach($arr as $key=>$v): ?><tr>
     <td><label><input type="checkbox" class="ace"><span class="lbl"></span></label></td>
     <td><?php echo ($v['id']); ?></td>
     <td class="order_product_name"><?php echo ($v['atime']); ?>
      <a href="#" class="product_Display"><!-- <img src="products/p_1.jpg"  title="产品名称"/> --></a>
      <!-- <i class="fa fa-plus"></i> -->
       <a href="#" class="product_Display"><!-- <img src="products/p_2.jpg"  title="产品名称"/> --></a>
     </td>
     <td><?php echo ($v['username']); ?></td>
     
     <td><?php echo ($v['userphone']); ?></td>
     <td><?php echo ($v['uaddress']); ?></td>
     <td><?php echo ($v['money']); ?></td>
      <td class="td-status"><span class="label label-success radius"><?php echo ($v['state']); ?></span></td>
     <td>
     <?php echo ($v['wlname']); ?>
     </td>
    <td>
    <?php if(($v['cuidan'] == 1 )): ?><a id="cuidan"  href="#" title="用户催单"  class="btn btn-primary"><i class=" icon-envelope"></i></a><?php endif; ?> 
     <?php if($v["state"] == 待发货): ?><!-- 123 -->
     <a onClick="Delivery_stop(this,'10001',<?php echo ($v['id']); ?>)"  href="javascript:;" title="发货"  class="btn btn-xs btn-success"><i class="fa fa-cubes bigger-120"></i></a>
     <a title="修改" href="javascript:;"  onclick="Delivery_edit(this,'10001',<?php echo ($v['id']); ?>)" class="btn btn-xs btn-warning" >∈</a>   
     <?php else: ?> 

     
      <!-- <a>123</a> --><?php endif; ?>
    <!--   <a title="订单详细"  href="<?php echo U('Order/details', ['id'=>$v['id']]);?>"  class="btn btn-xs btn-info order_success" ><i class="fa fa-list bigger-120"></i></a> -->

     <!-- <a title="修改" href="javascript:;"  onclick="Order_form_del(this,'1')" class="btn btn-xs btn-warning" ><i class="fa icon-edit  bigger-120">∈</i></a> -->    
     <IF /> 
     </td>
     </tr><?php endforeach; endif; ?>
    <div class="page" style="margin-top:380px;position:absolute;margin-left:30px;"><?php echo ($brr); ?></div>
     <!-- <tr>
     <td><label><input type="checkbox" class="ace"><span class="lbl"></span></label></td>
     <td>20160705445622</td>
     <td class="order_product_name">
      <a href="#" class="product_Display">
      <img src="products/p_1.jpg"  title="产品名称"/>
      </a>
      <i class="fa fa-plus"></i>
       <a href="#" class="product_Display">
       <img src="products/p_2.jpg"  title="产品名称"/>
       </a>
     </td>
     <td>456.5</td>    
     <td>2016-7-5</td>
     <td>食品</td>
     <td>2</td>
      <td class="td-status"><span class="label label-success radius">已发货</span></td>
      <td></td>
     <td>
     <a title="订单详细"  href="order_detailed.html"  class="btn btn-xs btn-info order_detailed" ><i class="fa fa-list bigger-120"></i></a> 
     <a title="删除" href="javascript:;"  onclick="Order_form_del(this,'1')" class="btn btn-xs btn-warning" ><i class="fa fa-trash  bigger-120"></i></a>    
     </td>
     </tr>
      <tr>
     <td><label><input type="checkbox" class="ace"><span class="lbl"></span></label></td>
     <td>20160705445622</td>
     <td class="order_product_name">
      <a href="#" class="product_Display"><img src="products/p_1.jpg"  title="产品名称"/>
     
     </td>
     <td>456.5</td>    
     <td>2016-7-5</td>
     <td>食品</td>
     <td>2</td>
      <td class="td-status"><span class="label label-success radius">失败</span></td>
      <td>支付失败</td>
     <td>
     <a title="订单详细"  href="order_detailed.html"  class="btn btn-xs btn-info order_detailed" ><i class="fa fa-list bigger-120"></i></a> 
     <a title="删除" href="javascript:;"  onclick="Order_form_del(this,'1')" class="btn btn-xs btn-warning" ><i class="fa fa-trash  bigger-120"></i></a>    
     </td>
     </tr> -->
    </tbody>
    </table>   
    </div>
 </div>
 </div>
</div>
<!--发货-->
 <div id="Delivery_stop" style=" display:none">
  <div class="">
    <div class="content_style">
  <div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1">快递公司 </label>
       <div class="col-sm-9"><select class="form-control" id="form-field-select-1" name="wlname">
			<option value="">--选择快递--</option>
			<option value="1">天天快递</option>
			<option value="2">圆通快递</option>
			<option value="3">中通快递</option>
			<option value="4">顺丰快递</option>
			<option value="5">申通快递</option>
			<option value="6">邮政EMS</option>
			<!-- <option value="7">邮政小包</option> -->
			<option value="7">韵达快递</option>
			</select></div>
	</div>
   <div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 快递单号 </label>
    <div class="col-sm-9"><input type="text" id="form-field-1" placeholder="快递单号" class="col-xs-10 col-sm-5" style="margin-left:0px;"></div>
	</div>
    <!-- <div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1">货到付款 </label>
     <div class="col-sm-9"><label><input name="checkbox" type="checkbox" class="ace" id="checkbox"><span class="lbl"></span></label></div>
	</div> -->
 </div>
  </div>
 </div>

 <div id="Delivery_edit" style=" display:none;"><input type="hidden" value="" class="hidden">
  　　收件人　<input class="username" style="border:0.8px solid #000;margin-top:10px;height:27px;" placeholder="收件人" value=""><br>
   　联系电话　<input class="userphone" style="border:0.8px solid #000;margin-top:10px;height:27px;" placeholder="联系电话" value=""><br>
    收件人地址　<input type="" class="uaddress" style="border:0.8px solid #000;margin-top:10px;width:400px;margin-bottom: 25px;height:27px;" placeholder="收件人地址" value="">
  </div>
</body>
</html>
<script>
$(function() { 
	$("#order_hand").fix({
		float : 'left',
		//minStatue : true,
		skin : 'green',	
		durationTime :false,
		spacingw:30,//设置隐藏时的距离
	    spacingh:250,//设置显示时间距
		table_menu:'.order_list_style',
	});  
    // var arr = $('.label-success').html();
    // if (arr == '已发货') {
    //   $('.btn-success').remove();
    //   $('.fa-cubes').remove();
    // }
    // // var arr = $('.label-success').html();
    // if (arr == '订单完成') {
    //   $('.btn-success').remove();
    //   $('.fa-cubes').remove();
    // }
    // if (arr == '等待付款') {
    //   $('.btn-success').remove();
    //   $('.fa-cubes').remove();
    // }
});


//时间
 laydate({
    elem: '#start',
    event: 'focus' 
});
//初始化宽度、高度  
 $(".widget-box").height($(window).height()); 
$(".order_list_style").width($(window).width()-220);
 $(".order_list_style").height($(window).height()-30);
  //当文档窗口发生改变时 触发  
    $(window).resize(function(){
	$(".widget-box").height($(window).height());
	 $(".order_list_style").width($(window).width()-234);
	  $(".order_list_style").height($(window).height()-30);
});
/**发货**/
function Delivery_stop(obj,id,a){
	layer.open({
        type: 1,
        title: '发货',
		maxmin: true, 
		shadeClose:false,
        area : ['500px' , ''],
        content:$('#Delivery_stop'),
		btn:['确定','取消'],
		yes: function(index, layero){		
		if($('#form-field-1').val()==""){
			layer.alert('快递号不能为空！',{
               title: '提示框',				
			  icon:0,		
			  }) 
		
			}else{			
			 // layer.confirm('提交成功！',function(index){
        var val = $("#form-field-select-1").val();
        var arr = $(".col-xs-10").val();
        // console.log(arr);
        $.ajax({
          type:'get',
          url : "<?php echo U('Order/save');?>",
          data : {id:+ a, wlname:+val, wlid:+arr},
          success: function(res) {
            if (res == '2') {
              alert('订单号填写错误,请重新填写！');
            } else {
              if (res == '1') {
                alert('该订单已发货！');
                location.reload(true); 
              } else {
              $(obj).parent().prev().html(res);    
    		$(obj).parents("tr").find(".td-manage").prepend('<a style=" display:none" class="btn btn-xs btn-success" onClick="member_stop(this,id)" href="javascript:;" title="已发货"><i class="fa fa-cubes bigger-120"></i></a>');
    		$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已发货</span>');
    		$(obj).remove();
    		layer.msg('已发货!',{icon: 6,time:1000});
              }
            }
      },
    })
	// });  
			layer.close(index);    		  
		  }
		
		}
	})
};
//订单列表
jQuery(function($) {
		var oTable1 = $('#sample-table').dataTable( {
		"aaSorting": [[ 1, "desc" ]],//默认第几个排序
		"bStateSave": true,//状态保存
		"aoColumnDefs": [
		  //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
		  {"orderable":false,"aTargets":[0,2,3,4,5,6,8,9]}// 制定列不参与排序
		] } );
                 //全选操作
				$('table th input:checkbox').on('click' , function(){
					var that = this;
					$(this).closest('table').find('tr > td:first-child input:checkbox')
					.each(function(){
						this.checked = that.checked;
						$(this).closest('tr').toggleClass('selected');
					});
						
				});
			});

$(function(){
  $('.row').last().remove();
})
// window.onmousedown = function() {
//     history.go(0);
// }

//修改用户收件信息
function Delivery_edit(obj,id,a){
    $.ajax({
          type:'get',
          url : "<?php echo U('Order/edit');?>",
          data : {id:+ a},
          success: function(res) {
            console.log(res);
            $('.username').val(res['username']);
            $('.userphone').val(res['userphone']);
            $('.uaddress').val(res['uaddress']);
            $('.hidden').val(res['id']);
          }
        });

  layer.open({
        type: 1,
        title: '修改收件人信息',
    maxmin: true, 
    shadeClose:false,
        area : ['500px' , ''],
        content:$('#Delivery_edit'),
    btn:['确定','取消'],
    yes: function(index, layero){   
    if($('.username').val()==""){
      layer.alert('收件人不能为空！',{
               title: '提示框',        
        icon:0,  
        })
      }else if ($('.userphone').val()==""){
        layer.alert('联系电话不能为空！',{
               title: '提示框',        
        icon:0,
        })
      } else if ($('.uaddress').val()=="") {
        layer.alert('地址不能为空！',{
               title: '提示框',        
        icon:0,
        })
      } else {      
        var crr = $(".hidden").val();
        $.ajax({
          type:'POST',
          url : "<?php echo U('Order/edit');?>",
          data : {id:+ crr, username:$(".username").val(), userphone:$(".userphone").val(), uaddress: $(".uaddress").val()},
          success: function(res) {
            if (res == '3') {
              alert('收件人填写错误,请重新填写');
            } else if (res == '2'){
              alert('手机号填写错误,请重新填写');
                // alert('该订单已发货！');
                // location.reload(true); 
            } else if(res == '1'){
                alert('信息修改成功！');
                location.reload(true);
        //       $(obj).parent().prev().html(res);    
        // $(obj).parents("tr").find(".td-manage").prepend('<a style=" display:none" class="btn btn-xs btn-success" onClick="member_stop(this,id)" href="javascript:;" title="已发货"><i class="fa fa-cubes bigger-120"></i></a>');
        // $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已发货</span>');
        // $(obj).remove();
        // layer.msg('已发货!',{icon: 6,time:1000});
            } else if(res == '0'){
              alert('信息修改失败');
            }

      },
    })
  // });  
      layer.close(index);         
      }
    
    }
  })
};
</script>