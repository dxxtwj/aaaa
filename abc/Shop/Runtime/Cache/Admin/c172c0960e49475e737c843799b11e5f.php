<?php if (!defined('THINK_PATH')) exit();?><!-- 角色添加 -->
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
        <link rel="stylesheet"href="/abc/Public/Admin/font/css/font-awesome.min.css" />
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
        <script src="/abc/Public/Admin/js/dragDivResize.js" type="text/javascript"></script>
<title>添加权限</title>
</head>

<body>
<div class="Competence_add_style clearfix">
  <div class="left_Competence_add">
   <div class="title_name">添加权限</div>
    <div class="Competence_add">
    <form action="<?php echo U('Adminuser/authority_add');?>" method="post">   	
     <div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 权限名称 </label>
       <div class="col-sm-9"><input type="text" id="form-field-1" placeholder=""  name="rolename" class="col-xs-10 col-sm-5"></div>
	</div>
     <div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 权限描述 </label>
      <div class="col-sm-9"><textarea name="rolebody" class="form-control" id="form_textarea" placeholder="" onkeyup="checkLength(this);"></textarea><span class="wordage">剩余字数：<span id="sy" style="color:Red;">200</span>字</span></div>

	</div>
    <div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 用户选择 </label>
       <div class="col-sm-9">

       <!-- 用户选择遍历 -->
            <?php if(is_array($user)): foreach($user as $key=>$vo): ?><label class="middle"><input class="ace" type="checkbox" id="id-disable-check" value="<?php echo ($vo["id"]); ?>" name="<?php echo ($vo["username"]); ?>"><span class="lbl"><?php echo ($vo["username"]); ?></span></label><?php endforeach; endif; ?>
        <!-- 用户选择遍历结束 -->
	    </div>   
   </div>

   <!--按钮操作-->
   <div class="Button_operation">
				<button onclick="javascript:location.href='<?php echo U('Adminuser/authority_add');?>'" class="btn btn-primary radius" type="submit"><i class="fa fa-save "></i> 保存并提交</button>
    	</form>
				<a href="<?php echo U('Adminuser/authority_list');?>"><button  class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button></a>
			</div>
    </div>
   </div>
   <!--权限分配-->

   <div class="Assign_style" > 
   	<div class="Select_Competence">
 
   <hr />
   <h3>新添加的管理员只有登录权限,需要超级管理员赋值权利</h3>
   <hr />
   <hr />
   <hr />
   <pre>《中国人民合国网络安全法》第一章
 第一条  为了保障网络安全，维护网络空间主权和国家安全、社会公共利益，保护公民、法人和其他组织的合法权益，促进经济社会信息化健康发展，制定本法。
第二条  在中华人民共和国境内建设、运营、维护和使用网络，以及网络安全的监督管理，适用本法。
第三条  国家坚持网络安全与信息化发展并重，遵循积极利用、科学发展、依法管理、确保安全的方针，推进网络基础设施建设和互联互通，鼓励网络技术创新和应用，支持培养网络安全人才，建立健全网络安全保障体系，提高网络安全保护能力。
第四条  国家制定并不断完善网络安全战略，明确保障网络安全的基本要求和主要目标，提出重点领域的网络安全政策、工作任务和措施。
第五条  国家采取措施，监测、防御、处置来源于中华人民共和国境内外的网络安全风险和威胁，保护关键信息基础设施免受攻击、侵入、干扰和破坏，依法惩治网络违法犯罪活动，维护网络空间安全和秩序。

第六条  国家倡导诚实守信、健康文明的网络行为，推动传播社会主义核心价值观，采取措施提高全社会的网络安全意识和水平，形成全社会共同参与促进网络安全的良好环境。
第七条  国家积极开展网络空间治理、网络技术研发和标准制定、打击网络违法犯罪等方面的国际交流与合作，推动构建和平、安全、开放、合作的网络空间，建立多边、民主、透明的网络治理体系。
第八条  国家网信部门负责统筹协调网络安全工作和相关监督管理工作。国务院电信主管部门、公安部门和其他有关机关依照本法和有关法律、行政法规的规定，在各自职责范围内负责网络安全保护和监督管理工作。
县级以上地方人民政府有关部门的网络安全保护和监督管理职责，按照国家有关规定确定。
第九条  网络运营者开展经营和服务活动，必须遵守法律、行政法规，尊重社会公德，遵守商业道德，诚实信用，履行网络安全保护义务，接受政府和社会的监督，承担社会责任。
第十条  建设、运营网络或者通过网络提供服务，应当依照法律、行政法规的规定和国家标准的强制性要求，采取技术措施和其他必要措施，保障网络安全、稳定运行，有效应对网络安全事件，防范网络违法犯罪活动，维护网络数据的完整性、保密性和可用性。
第十一条  网络相关行业组织按照章程，加强行业自律，制定网络安全行为规范，指导会员加强网络安全保护，提高网络安全保护水平，促进行业健康发展。
第十二条  国家保护公民、法人和其他组织依法使用网络的权利，促进网络接入普及，提升网络服务水平，为社会提供安全、便利的网络服务，保障网络信息依法有序自由流动。
任何个人和组织使用网络应当遵守宪法法律，遵守公共秩序，尊重社会公德，不得危害网络安全，不得利用网络从事危害国家安全、荣誉和利益，煽动颠覆国家政权、推翻社会主义制度，煽动分裂国家、破坏国家统一，宣扬恐怖主义、极端主义，宣扬民族仇恨、民族歧视，传播暴力、淫秽色情信息，编造、传播虚假信息扰乱经济秩序和社会秩序，以及侵害他人名誉、隐私、知识产权和其他合法权益等活动。
第十三条  国家支持研究开发有利于未成年人健康成长的网络产品和服务，依法惩治利用网络从事危害未成年人身心健康的活动，为未成年人提供安全、健康的网络环境。
第十四条  任何个人和组织有权对危害网络安全的行为向网信、电信、公安等部门举报。收到举报的部门应当及时依法作出处理；不属于本部门职责的，应当及时移送有权处理的部门。
有关部门应当对举报人的相关信息予以保密，保护举报人的合法权益。如对以上规定存在意见和建议可向<a href="http://www.npc.gov.cn/npc/bmzz/falv/node_1622.htm">中国法律委员会</a>及<a href="http://www.npc.gov.cn/npc/xinwen/index.htm">中国人大</a>提出</pre> 
 <h6>网站使用须知:详情参看<br />
   		<a href="https://www.baidu.com/s?wd=中国人民合国宪法">《中国人民合国宪法》</a><br />
   		<a href="https://www.baidu.com/s?wd=中国人民合国刑法">《中国人民合国刑法》</a><br />
    	<a href="https://www.baidu.com/s?wd=中国人民合国网络安全法">《中国人民合国网络安全法》</a><br />
    	<a href="https://www.baidu.com/s?wd=中国人民合国劳动法">《中国人民合国劳动法》</a><br />
    	<a href="https://www.baidu.com/s?wd=中国人民合国个人信息保护法">《中国人民合国个人信息保护法》</a><br/>
    	<a href="https://www.legalinfo.gov.cn/">《中国普法网》</a><br />

   </h6>
      
		
    	　<label class="middle" style="background:#444;" ><input name="user-Character-0" class="ace" type="checkbox" id="id-disable-check">
    		<h1>本网站合作伙伴 特别鸣谢<p><a href="http://www.wangzherongyao.cn">王者荣耀团队技术支持</a></p></h1>
 	　　　　<p><a href="http://www.360.cn">360安全卫士技术支持</a></p>
 	　　　　<p><a href="http://www.qq.com">腾讯技术支持</a></p>
 	　　　　<p><a href="http://www.w3c.com">万维网技术支持</a></p>
 	　　　　<p><a href="http://www.Think.com">ThinkPHP技术支持</a></p>
  　 　　　	<p><a href="">bootstrap技术支持</a></p>
   	　　　　<p><a href="">JQuery技术支持</a></p>
   	　　　　<p><a href="">PHP技术支持</a></p>
   	　　　　<p><a href="">Mysql技术支持</a></p>
   	　　　　<p><a href="">apache技术支持</a></p>
   　　　	<p><a href="">谷歌浏览器技术支持</a></p>
   	　　　　<p><a href="">甲骨文公司技术支持</a></p>
   	　　　　<p><a href="">微软公司技术支持</a></p>
   	　　　　<p><a href="">因特尔技术支持</a></p>
   	　　　　<p><a href="">百度搜索技术支持</a></p>
   	　　　　<p><a href="">搜狗输入法技术支持</a></p>
   	　　　　<p><a href="">阿里云技术支持</a></p>
   	　　　　<p><a href="">CSS技术支持</a></p>
   	　　　　<p><a href="">javascript技术支持</a></p>
   	　　　　<p><a href="">H5技术支持</a></p>


      </label>
     <!--  <div class="title_name">权限分配　　　　<button onclick="javascript:location.href='<?php echo U('Node/node_list');?>'" class="btn btn-danger radius">权限节点管理> > >非专业人士请勿点击!!! </button></div>
		
     
      <dl class="permission-list">
		 <dt>第一层
			 <span class="lbl">产品管理</span>
         </dt>

		 <dd>
		    <dl class="cl permission-list2">
		    <dt>第二层
		         <label class="middle"><input type="checkbox" value="" class="ace"  name="user-Character-0-0" id="id-disable-check"><span class="lbl">产品列表</span></label>
		    </dt>

            <dd>第三层
		        <label class="middle"><input type="checkbox" value="" class="ace" name="user-Character-0-0-0" id="user-Character-0-0-0"><span class="lbl">添加</span></label>
		        <label class="middle"><input type="checkbox" value="" class="ace" name="user-Character-0-0-0" id="user-Character-0-0-1"><span class="lbl">修改</span></label>
		        <label class="middle"><input type="checkbox" value="" class="ace" name="user-Character-0-0-0" id="user-Character-0-0-2"><span class="lbl">删除</span></label>
		        <label class="middle"><input type="checkbox" value="" class="ace" name="user-Character-0-0-0" id="user-Character-0-0-3"><span class="lbl">查看</span></label>
		        <label class="middle"><input type="checkbox" value="" class="ace" name="user-Character-0-0-0" id="user-Character-0-0-4"><span class="lbl">审核</span></label>
		    </dd>
		</dl>

     
  </div>-->
    </div> 
</div> 
</body>
</html>
<script type="text/javascript">
//初始化宽度、高度  
 $(".left_Competence_add,.Competence_add_style").height($(window).height()).val();; 
 $(".Assign_style").width($(window).width()-500).height($(window).height()).val();
 $(".Select_Competence").width($(window).width()-500).height($(window).height()-40).val();
  //当文档窗口发生改变时 触发  
    $(window).resize(function(){
	$(".Assign_style").width($(window).width()-500).height($(window).height()).val();
	$(".Select_Competence").width($(window).width()-500).height($(window).height()-40).val();
	$(".left_Competence_add,.Competence_add_style").height($(window).height()).val();;
	});
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
/*按钮选择*/
$(function(){
	$(".permission-list dt input:checkbox").click(function(){
		$(this).closest("dl").find("dd input:checkbox").prop("checked",$(this).prop("checked"));
	});
	$(".permission-list2 dd input:checkbox").click(function(){
		var l =$(this).parent().parent().find("input:checked").length;
		var l2=$(this).parents(".permission-list").find(".permission-list2 dd").find("input:checked").length;
		if($(this).prop("checked")){
			$(this).closest("dl").find("dt input:checkbox").prop("checked",true);
			$(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked",true);
		}
		else{
			if(l==0){
				$(this).closest("dl").find("dt input:checkbox").prop("checked",false);
			}
			if(l2==0){
				$(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked",false);
			}
		}
		
	});
});

</script>