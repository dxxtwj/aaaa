<?php if (!defined('THINK_PATH')) exit();?><!-- 商品属性列表的添加和编辑 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" /> 
        <link href="/abc/Public/Admin/assets/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="/abc/Public/Admin/css/style.css"/>       
        <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace.min.css" />
        <link rel="stylesheet" href="/abc/Public/Admin/assets/css/font-awesome.min.css" />
        <link rel="stylesheet" href="/abc/Public/Admin/Widget/zTree/css/zTreeStyle/zTreeStyle.css" type="text/css">
        <link href="/abc/Public/Admin/Widget/icheck/icheck.css" rel="stylesheet" type="text/css" />   
    <!--[if IE 7]>
      <link rel="stylesheet" href="/abc/Public/Admin/assets/css/font-awesome-ie7.min.css" />
    <![endif]-->
        <!--[if lte IE 8]>
      <link rel="stylesheet" href="/abc/Public/Admin/assets/css/ace-ie.min.css" />
    <![endif]-->
      <script src="/abc/Public/Admin/js/jquery-1.9.1.min.js"></script>   
        <script src="/abc/Public/Admin/assets/js/bootstrap.min.js"></script>
        <script src="/abc/Public/Admin/assets/js/typeahead-bs2.min.js"></script>
    <!-- page specific plugin scripts -->
    <script src="/abc/Public/Admin/assets/js/jquery.dataTables.min.js"></script>
    <script src="/abc/Public/Admin/assets/js/jquery.dataTables.bootstrap.js"></script>
        <script type="text/javascript" src="/abc/Public/Admin/js/H-ui.js"></script> 
        <script type="text/javascript" src="/abc/Public/Admin/js/H-ui.admin.js"></script> 
        <script src="/abc/Public/Admin/assets/layer/layer.js" type="text/javascript" ></script>
        <script src="/abc/Public/Admin/assets/laydate/laydate.js" type="text/javascript"></script>
        <script type="text/javascript" src="/abc/Public/Admin/Widget/zTree/js/jquery.ztree.all-3.5.min.js"></script> 
        <script src="/abc/Public/Admin/js/lrtk.js" type="text/javascript" ></script>
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
       <span class="r_f">共：<b>2334</b>件商品</span>
     </div>
         <div class="table_menu_list"  >
       <table class="table table-striped table-bordered table-hover" id="sample-table" style="width: 1500px;">
    <thead>
     <tr>
        <th width="25px"><label><input type="checkbox" class="ace"><span class="lbl"></span></label></th>
        <th width="80px">商品属性ID</th>
        <th width="150px">商品图片</th>
        <th width="250px">商品名称</th>
        <th width="250px">商品属性1</th>
        <th width="250px">商品属性2</th>
        <th width="500px;">操作</th>                
      </tr>
    </thead>
  <tbody>
    <?php if(is_array($list)): foreach($list as $key=>$v): ?><tr>
          <td width="25px"><label><input type="checkbox" class="ace" ><span class="lbl"></span></label></td>
          <td width="80px"><?php echo ($v['id']); ?></td>               
          <td width="80px"><img width='150' src="/abc/Public/<?php echo ($v['image']); ?>" alt="" /></td>               
          <td width="250px"><?php echo ($goodsName['name']); ?><u style="cursor:pointer" class="text-primary" onclick=""></u></td>
          <td width="250px"><?php echo ($v['baozhuang']); ?><u style="cursor:pointer" class="text-primary" onclick=""></u></td>
          <td width="250px"><?php echo ($v['kouwei']); ?><u style="cursor:pointer" class="text-primary" onclick=""></u></td>
          <td class="td-manage">
          <a title="编辑" href="<?php echo U('Shop/editshuxing', 'id='.$v['id']);?>"  class="btn btn-xs btn-info" ><i class="icon-edit bigger-120"></i></a> 
          <a title="删除" href="<?php echo U('Shop/delshuxing',['gid' => $goodsName['id'], 'id' => $v['id']]);?>"  class="btn btn-xs btn-warning" ><i class="icon-trash  bigger-120"></i></a>
         </td>
      </tr><?php endforeach; endif; ?>
  </empty>
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
  
</script>