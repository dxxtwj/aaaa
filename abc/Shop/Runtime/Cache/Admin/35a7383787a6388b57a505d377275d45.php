<?php if (!defined('THINK_PATH')) exit();?><!-- 角色列表 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Cache-Control" content="no-siteapp" />
        <link href="/Public/Admin/assets/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="/Public/Admin/css/style.css"/>       
        <link href="/Public/Admin/assets/css/codemirror.css" rel="stylesheet">
        <link rel="stylesheet" href="/Public/Admin/assets/css/ace.min.css" />
        <link rel="stylesheet" href="/Public/Admin/font/css/font-awesome.min.css" />
        <!--[if lte IE 8]>
		  <link rel="stylesheet" href="/Public/Admin/assets/css/ace-ie.min.css" />
		<![endif]-->
		<script src="/Public/Admin/js/jquery-1.9.1.min.js"></script>
        <script src="/Public/Admin/assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/Public/Admin/Widget/Validform/5.3.2/Validform.min.js"></script>
		<script src="/Public/Admin/assets/js/typeahead-bs2.min.js"></script>           	
		<script src="/Public/Admin/assets/js/jquery.dataTables.min.js"></script>
		<script src="/Public/Admin/assets/js/jquery.dataTables.bootstrap.js"></script>
        <script src="/Public/Admin/assets/layer/layer.js" type="text/javascript" ></script>          
		<script src="/Public/Admin/js/lrtk.js" type="text/javascript" ></script>
         <script src="/Public/Admin/assets/layer/layer.js" type="text/javascript"></script>	
        <script src="/Public/Admin/assets/laydate/laydate.js" type="text/javascript"></script>
<title>管理权限</title>
</head>

<body>
 <div class="margin clearfix">
   <div class="border clearfix">
       <span class="l_f">
        <a href="<?php echo U('Adminuser/authority_add');?>" id="Competence_add" class="btn btn-warning" title="添加权限"><i class="fa fa-plus"></i> 添加角色</a>
        <a href="<?php echo U('Node/node_list');?>" id="Competence_add" class="btn btn-danger radius" title="节点管理"><i class="fa fa-edit"></i> 节点管理 > > </a>
       </span>
       <span class="r_f">共：<b>5</b>类</span>
     </div>
     <div class="compete_list">
       <table id="sample-table-1" class="table table-striped table-bordered table-hover">
		 <thead>
			<tr>
			  <th class="center">权限ID</th>
			  <th>角色名称</th>
              <th>所属管理员</th>
			  <th>管理人数</th>
			  <th class="hidden-480">描述</th>             
			  <th class="hidden-480">操作</th>
             </tr>
		    </thead>

             <tbody>
	<!-- 遍历开始 -->
	<?php if(is_array($role)): foreach($role as $key=>$v): ?><tr>
			  	<td class="center"><?php echo ($v["id"]); ?></td>
				<td><?php echo ($v["rolename"]); ?></td>
				<td class="hidden-480">
			<!-- 成员名开始 -->
					<!--遍历关联表 -->
					<?php $a = 0; ?>
					<?php if(is_array($ule)): foreach($ule as $key=>$ue): if($v['id']==$ue['rid']){ foreach ($user as $vx) { if($vx['id']==$ue['aid']){ $a+=1; echo $vx['username'].'　'; } } } endforeach; endif; ?>
					<!-- 遍历关联表结束 -->
			<!-- 成员名结束 -->
				<!-- <a href="#" class="btn btn-xs btn-info"><i class="fa fa-edit bigger-120" ></i></a></td> -->
				<td>
					<?php echo $a; ?>
				 </td>
				<td><?php echo ($v["rolebody"]); ?></td>
				<td>
            		<a title="权限分配 > <?php echo ($v["rolename"]); ?>"  href="<?php echo U('Node/compile_node');?>?id=<?php echo ($v["id"]); ?>"  class="btn btn-xs btn-info"  id="Competence_add" ><i class="fa fa-edit bigger-120">权限分配</i></a> 
            		 <a title="删除" href="javascript:;"  onclick="Competence_del(this,<?php echo ($v["id"]); ?>)" class="btn btn-xs btn-warning"><i class="fa fa-trash  bigger-120">删除</i></a>       
                	<!-- <a  onclick="Competence_del(this,<?php echo ($v['id']); ?>)" class="btn btn-xs btn-warning" >删除</a> -->
				</td>
			   </tr><?php endforeach; endif; ?>
	<!-- 遍历结束 -->
	      </tbody>
	        </table>
     </div>
 </div>
 <!-- 
 <div style="background:#999;width:300px;height:100px;">
 	<?php if(is_array($user)): foreach($user as $key=>$v2): ?><label class="middle"><input type="checkbox"  id=""><?php echo ($v2["username"]); ?></label><?php endforeach; endif; ?>	
 </div>
  -->
</body>
</html>
<script type="text/javascript">
 /*权限-删除*/
function Competence_del(obj,id){
	layer.confirm('删除后无法恢复,确认要删除吗',function(){
		//$.ajax({
				//type:'post',
				//url:"<?php echo U('Adminuser/authority_delete');?>",
				//data:"zid="+id,
				//success:function(k1){
				$.ajax({
					type:'post',
					url:"<?php echo U('Adminuser/authority_delete');?>",
					data:"id="+id,
					success:function(s1){
						if(s1==1){	
							$(obj).parents("tr").remove();
				 			layer.msg('已删除!',{icon:1,time:1000});
						}else{
							layer.msg('未知错误!',{icon:1,time:1000});
						}
					}

				})
				
				
	     });

}

/*修改权限*/
function Competence_modify(id){
		window.location.href ="Competence.html?="+id;
};

/*字数限制*/
function checkLength(which) {
	var maxChars = 200; //
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
		var curr = maxChars - which.value.length; //250 减去 当前输入的
		document.getElementById("sy").innerHTML = curr.toString();
		return true;
	}
};
//面包屑返回值
var index = parent.layer.getFrameIndex(window.name);
parent.layer.iframeAuto(index);
$('.Order_form ,#Competence_add').on('click', function(){
	var cname = $(this).attr("title");
	var cnames = parent.$('.Current_page').html();
	var herf = parent.$("#iframe").attr("src");
    parent.$('#parentIframe span').html(cname);
	parent.$('#parentIframe').css("display","inline-block");
    parent.$('.Current_page').attr("name",herf).css({"color":"#4c8fbd","cursor":"pointer"});
	//parent.$('.Current_page').html("<a href='javascript:void(0)' name="+herf+">" + cnames + "</a>");
    parent.layer.close(index);
	
});
</script>