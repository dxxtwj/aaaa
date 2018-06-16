<?php if (!defined('THINK_PATH')) exit();?><!-- 权限管理 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
        <script type="text/javascript" src="/abc/Public/Admin/Widget/Validform/5.3.2/Validform.min.js"></script>
		<script src="/abc/Public/Admin/assets/js/typeahead-bs2.min.js"></script>           	
		<script src="/abc/Public/Admin/assets/js/jquery.dataTables.min.js"></script>
		<script src="/abc/Public/Admin/assets/js/jquery.dataTables.bootstrap.js"></script>
        <script src="/abc/Public/Admin/assets/layer/layer.js" type="text/javascript" ></script>          
		<script src="/abc/Public/Admin/js/lrtk.js" type="text/javascript" ></script>
         <script src="/abc/Public/Admin/assets/layer/layer.js" type="text/javascript"></script>	
        <script src="/abc/Public/Admin/assets/laydate/laydate.js" type="text/javascript"></script>
<title>管理权限</title>
</head>

<body>
 <div class="margin clearfix">
   <div class="border clearfix">
       <span class="l_f">
        <a href="<?php echo U('Tuiguang/add');?>" id="Competence_add" class="btn btn-warning" title="添加商品推广"><i class="fa fa-plus"></i> 添加商品推广</a>
       </span>
       <span class="r_f">共：<b>5</b>类</span>
     </div>
     <div class="compete_list">
       <table id="sample-table-1" class="table table-striped table-bordered table-hover">
		 <thead>
			<tr>
			  <th class="center">商品ID</th>
			  <th>商品名</th>
			  <th>商品图片</th>
              <th>商品价钱</th>
			  <th>商品销量</th>
			  <th>商品推广结束时间</th>
			  <th class="hidden-480">操作</th>
             </tr>
		    </thead>

             <tbody>
	<!-- 遍历开始 -->
	<?php if(empty($tuiGuang)): ?><tr><td colspan="7"><h1>亲，暂无要推广的商品</h1></td></tr>
	<?php else: ?>
	<?php if(is_array($tuiGuang)): foreach($tuiGuang as $key=>$v): ?><tr>
			  	<td class="center"><?php echo ($v['id']); ?></td>
				<td><?php echo ($v['name']); ?></td>
				<td><img width=150 height=150 src="/abc/Public/<?php echo ($v['image']); ?>" alt="" /></td>
				<td class="hidden-480"><?php echo ($v['money']); ?></td>
				<td><?php echo ($v['sellnum']); ?></td>
				<td><?php echo ($v['dtime']); ?>暂时未弄</td>
				<?php if(($v['state'] == '2')): ?><td><a href="#">未在推广</a></td>
				<?php else: ?>
					<td><a href="<?php echo U('Tuiguang/del', 'gid='.$v['gid']);?>">取消推广</a></td><?php endif; ?>
		   </tr><?php endforeach; endif; endif; ?>
	      </tbody>
	        </table>
     </div>
 </div>
</body>
</html>