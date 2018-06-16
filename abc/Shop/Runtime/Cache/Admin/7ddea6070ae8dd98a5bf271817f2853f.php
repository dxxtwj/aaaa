<?php if (!defined('THINK_PATH')) exit();?><!-- 商品列表 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" /> 
        <link href="/Public/Admin/assets/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="/Public/Admin/css/style.css"/>       
        <link rel="stylesheet" href="/Public/Admin/assets/css/ace.min.css" />
        <link rel="stylesheet" href="/Public/Admin/assets/css/font-awesome.min.css" />
        <link rel="stylesheet" href="/Public/Admin/Widget/zTree/css/zTreeStyle/zTreeStyle.css" type="text/css">
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
		<!-- page specific plugin scripts -->
		<script src="/Public/Admin/assets/js/jquery.dataTables.min.js"></script>
		<script src="/Public/Admin/assets/js/jquery.dataTables.bootstrap.js"></script>
        <script type="text/javascript" src="/Public/Admin/js/H-ui.js"></script> 
        <script type="text/javascript" src="/Public/Admin/js/H-ui.admin.js"></script> 
        <script src="/Public/Admin/assets/layer/layer.js" type="text/javascript" ></script>
        <script src="/Public/Admin/assets/laydate/laydate.js" type="text/javascript"></script>
        <script type="text/javascript" src="/Public/Admin/Widget/zTree/js/jquery.ztree.all-3.5.min.js"></script> 
        <script src="/Public/Admin/js/lrtk.js" type="text/javascript" ></script>
<title>产品列表</title>
</head>
<body>
<div class=" page-content clearfix">
 <div id="products_style">
    <div class="search_style">
     
      <ul class="search_content clearfix">
       <li><label class="l_f">产品名称</label><input name="" type="text"  class="text_add" placeholder="输入品牌名称"  style=" width:250px"/></li>
       <li><label class="l_f">添加时间</label><input class="inline laydate-icon" id="start" style=" margin-left:10px;"></li>
       <li style="width:90px;"><button type="button" class="btn_search"><i class="icon-search"></i>查询</button></li>
      </ul>
    </div>
     <div class="border clearfix">
       <span class="l_f">
        <a href="<?php echo U('Shop/add');?>" title="添加商品" class="btn btn-warning Order_form"><i class="icon-plus"></i>添加商品</a>
        <a href="javascript:ovid()" class="btn btn-danger"><i class="icon-trash"></i>批量删除</a>
       </span>
       <span class="r_f">共：<b>2334</b>件商品</span>
     </div>
     <!--产品列表展示-->
    <!--  <div class="h_products_list clearfix" id="products_list">
       <div id="scrollsidebar" class="left_Treeview">
        <div class="show_btn" id="rightArrow"><span></span></div>
        <div class="widget-box side_content" >
         <div class="side_title"></div>
         <div class="side_list"><div class="widget-header header-color-green2"><h4 class="lighter smaller">产品类型列表</h4></div> -->

         <!-- <ul class="b_P_Sort_list"> -->

         <!-- <li><i class="orange  fa fa-list "></i><a href="<?php echo U('Shop/index');?>">所有分类</a></li> -->
      
      <!-- 遍历顶级分类 -->
      <!-- <?php if(is_array($arr)): foreach($arr as $key=>$v): ?>-->

        <!-- 判断是否为顶级分类    eq表示等于 -->
        <!-- <?php if($v['pid'] == 0): ?>-->

          <!-- 条件成立，遍历数据 -->
                  <!-- <li> -->
                  <!-- <i class="fa fa-shopping-bag pink "></i> <a onclick="erji(this, <?php echo ($v['id']); ?>)" href="javascript:void(0)"><?php echo ($v['name']); ?></a> -->
                <!-- </li> -->

        <!-- 否则空输出 -->
        <!-- <?php else: ?> -->
        
        <!-- 判断结束 -->
        <!--<?php endif; ?> -->

      <!-- 遍历结束 -->
      <!--<?php endforeach; endif; ?> -->
         <!-- </ul> -->

        <!--  <div class="widget-body">
          <div class="widget-main padding-8"><div id="treeDemo" class="ztree"></div></div>
        </div>
       </div>
      </div> -->
      <!-- 分页类 -->
      <!-- <div style='margin-top: 500px;'>
         <?php echo ($show); ?>
      </div>     -->
     <!-- </div> -->
         <div class="table_menu_list"  >
       <table class="table table-striped table-bordered table-hover" id="sample-table" style="width: 1500px;">
		<thead>
		 <tr>
				<th width="25px"><label><input type="checkbox" class="ace"><span class="lbl"></span></label></th>
        <th width="80px">商品ID</th>
				<th width="150px">商品图片</th>
				<th width="250px">商品名称</th>
				<th width="100px">库存</th>
				<th width="100px">现价</th>
                <th width="100px">描述</th>				
				<th width="200px">加入时间</th>
        <th width="120px">状态</th>                
				<th width="500px;">操作</th>                
			</tr>
		</thead>
	<tbody>
  <?php if(empty($list)): ?><tr><td colspan='10'>暂无数据</td></tr>
  <?php else: ?>
    <?php if(is_array($list)): foreach($list as $key=>$v): ?><tr>
          <td width="25px"><label><input type="checkbox" class="ace" ><span class="lbl"></span></label></td>
          <td width="80px"><?php echo ($v['id']); ?></td>               
          <td width="80px"><img width='150' src="/Public/<?php echo ($v['image0']); ?>" alt="" /></td>               
          <td width="250px"><?php echo ($v['name']); ?><u style="cursor:pointer" class="text-primary" onclick=""></u></td>
          <td width="100px"><?php echo ($v['num']); ?></td>
          <td width="100px"><?php echo ($v['money']); ?></td> 
          <td width="100px"><?php echo ($v['content']); ?></td>         
          <td width="180px"><?php echo ($v['atime']); ?></td>
          <td class="text-l"><?php echo ($v['state']); ?></td>
          <td class="td-manage">
          <a title="查看" href="<?php echo U('Shop/look', 'id='.$v['id']);?>"  class="btn btn-xs btn-info" ><i class="btn-info icon-eye-open"></i></a>
          <?php if(($v['state'] != '在售中')): ?><a title="开始销售" href="<?php echo U('Shop/xiaoshou', 'id='.$v['id']);?>"  class="btn btn-xs btn-info" ><i class=" icon-arrow-up"></i></a><?php endif; ?>
          <?php if(($v['state'] != '已下架')): ?><a title="下架" href="<?php echo U('Shop/xiajia', 'id='.$v['id']);?>"  class="btn btn-xs btn-info" ><i class=" icon-arrow-down"></i></a>
          <a title="添加详情图" href="<?php echo U('Shop/xiangqing', 'id='.$v['id']);?>"  class="btn btn-xs btn-info" ><i class=" icon-picture"></i></a><?php endif; ?>
          <a title="添加属性" href="<?php echo U('Shop/addshuxing', 'id='.$v['id']);?>"  class="btn btn-xs btn-info" ><i class="icon-edit bigger-120"></i></a> 
           
          <a title="编辑" href="<?php echo U('Shop/editgoods', 'id='.$v['id']);?>"  class="btn btn-xs btn-info" ><i class="icon-wrench"></i></a> 
          <a title="删除" href="<?php echo U('Shop/del', 'id='.$v['id']);?>"  class="btn btn-xs btn-warning" ><i class="icon-trash  bigger-120"></i></a>
         </td>
  	  </tr><?php endforeach; endif; endif; ?>
      <div class="show">
        <?php echo ($shows); ?>
      </div>
    </tbody>
    </table>
    </div>     
  </div>
 </div>
 
</div>
</body>

</html>
<script>

</script>
<script type="text/javascript">
//初始化宽度、高度                                    
 $(".widget-box").height($(window).height()-215); 
$(".table_menu_list").width($(window).width()-260);
 $(".table_menu_list").height($(window).height()-215);
  //当文档窗口发生改变时 触发  
    $(window).resize(function(){
	$(".widget-box").height($(window).height()-215);
	 $(".table_menu_list").width($(window).width()-260);
	  $(".table_menu_list").height($(window).height()-215);
	})
 

/*产品-停用*/
function member_stop(obj,id){
	layer.confirm('确认要停用吗？',function(index){
		$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs " onClick="member_start(this,id)" href="javascript:;" title="启用"><i class="icon-ok bigger-120"></i></a>');
		$(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已停用</span>');
		$(obj).remove();
		layer.msg('已停用!',{icon: 5,time:1000});
	});
}

/*产品-启用*/
function member_start(obj,id){
	layer.confirm('确认要启用吗？',function(index){
		$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs btn-success" onClick="member_stop(this,id)" href="javascript:;" title="停用"><i class="icon-ok bigger-120"></i></a>');
		$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
		$(obj).remove();
		layer.msg('已启用!',{icon: 6,time:1000});
	});
}
/*产品-编辑*/
function member_edit(title,url,id,w,h){
	layer_show(title,url,w,h);
}

/*产品-查看*/
function member_chakan(title,url,id,w,h){
  layer_show(title,url,w,h);
}


//面包屑返回值
var index = parent.layer.getFrameIndex(window.name);
parent.layer.iframeAuto(index);
$('.Order_form').on('click', function(){
	var cname = $(this).attr("title");
	var chref = $(this).attr("href");
	var cnames = parent.$('.Current_page').html();
	var herf = parent.$("#iframe").attr("src");
    parent.$('#parentIframe').html(cname);
    parent.$('#iframe').attr("src",chref).ready();;
	parent.$('#parentIframe').css("display","inline-block");
	parent.$('.Current_page').attr({"name":herf,"href":"javascript:void(0)"}).css({"color":"#4c8fbd","cursor":"pointer"});
	//parent.$('.Current_page').html("<a href='javascript:void(0)' name="+herf+" class='iframeurl'>" + cnames + "</a>");
    parent.layer.close(index);
	
});
</script>
<script>
  window.onload = function() {


    console.log($('.show a, .show span').unwrap('<div></div>').wrap("<li style='float: left; display: inline-block; margin-bottom: 20px;' class='btn btn-info'></li>"));

    /**
     * 为获取二级分类而定义的方法
     * @param  {[type]} a  [自己，为了后面添加分类的节点]
     * @param  {[type]} id [顶级分类的id]
     */
    // erji = function erji(a, id) {

    //   // 删除节点，为的是不让他重复创建
    //   $(a).nextAll().remove();

    //   // ajax请求
    //   $.ajax({

    //     // 地址
    //     url: "<?php echo U('Shop/erji');?>",

    //     // 类型
    //     type: "post",

    //     // 顶级分类id
    //     data: {id},

    //     // 回调函数
    //     success: function(msg) {
          
         
    //       // // 循环遍历添加id
    //       for (var i = 0; i < msg.length; i++) {

    //         // 接收id
    //         var cid = msg[i].id;

    //         // 添加节点
    //         $(a).after("<div><i class='icon-arrow-right'></i><a style='width: 200px; margin-top: 5px; cursor: pointer;' href='/Admin/Shop/index/cid/"+ cid + "'>" + msg[i].name + "</a> </div>");

    //       }
    //     }
    //   }, 'json');
    // }

  }
</script>