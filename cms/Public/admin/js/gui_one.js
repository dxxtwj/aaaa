var gid = $(".gid").val();
var data = new Array();
var data2 = new Array();
var bool = true;
var flag =0;
if(gid!='' && gid!=undefined && gid!=null){
	$.ajax({
		url:'/index.php/Admin/Goodsone/getFormatOption',
		data:{gid:gid},
		type:'post',
		async:false,
		success:function(res){
			if(res['is_options'] == true){
				$("#hasoption").val(1);
			}else if(res['is_options']==false){
				$("#hasoption").val(2);
			}else{
				return false;
			}

			if(res['format']!=null){
				for(var i in res['format']){
					data.push({
					'spec_id':parseInt(i)+1,		
					'id':res['format'][i]['FR_ID'],				//规格id
					'spec_name':res['format'][i]['FR_Name'],	//规格名
					'item':new Array(),
					});
					for(var j in res['format'][i]['item']){
						data[i]['item'].push({
						'item_id':res['format'][i]['item'][j]['ids'],
						'id':res['format'][i]['item'][j]['OR_ID'],
						'item_name':res['format'][i]['item'][j]['OR_Name'],
						'img_url':'',
						'item_img':'/Public/admin/images/nopic.jpg',
						});
					}
					
				}
			}


			if(res['format_option']!=null){
				for(var i in res['format_option']){
					data2.push({
						ids:res['format_option'][i]['ids'],
						id :  res['format_option'][i]['FO_ID'],
						// fids:res['format_option'][i]['FO_FIDS'],
						// oids:res['format_option'][i]['FO_OIDS'],
						stock : res['format_option'][i]['FO_Stock'],	//库存量
						CostPrice : res['format_option'][i]['FO_Cost_Price'],	//成本价格
						// productprice : res['format_option'][i]['FO_Price'],	//市场价格
						price : res['format_option'][i]['FO_Price'],	//销售价格
						weight:res['format_option'][i]['FO_Weight'],	//重量
						// iid:res['format_option'][i]['FO_IID'],
						iaddress:res['format_option'][i]['FO_IMG'],
						// sale:res['format_option'][i]['FO_Sale'],	//显示销量设定
						// goodssn:"bbb",	//商品编码
					});
				}
			}
		}
	});
}

//规格数据
// var data=[
// {   
// 	'spec_id':'17',		//规格id
// 	'spec_name':'颜色',	//规格名
// 	//规格项
// 	'item':[	//规格项id  规格项名称  图片地址  规格项图片
// 		{'item_id':'17_1','item_name':'香槟色','img_url':'','item_img':'/Public/admin/images/nopic.jpg'},		
// 		{'item_id':'17_2','item_name':'玫瑰金','img_url':'/Public/admin/images/huawei-4.jpg','item_img':'/Public/admin/images/huawei-4.jpg'}
// 	]
// },
// {   
// 	'spec_id':'18',
// 	'spec_name':'内存',
// 	'item':[
// 		{'item_id':'18_10','item_name':'46g','img_url':'/Public/admin/images/huawei-4.jpg','item_img':'/Public/admin/images/pic1.jpg'},
// 		{'item_id':'18_11','item_name':'16g','img_url':'/Public/admin/images/huawei-4.jpg','item_img':'/Public/admin/images/pic1.jpg'}
// 	]
// }
// ];
//商品信息
// var data2 = [{
// 	ids:'17_1_18_11',
// 	id : "100",
// //	title:'香槟色+16g', 
// 	stock : "100",	//库存量
// 	CostPrice : "1000",	//成本价格
// 	productprice : "1000",	//市场价格
// 	price : "2500",	//销售价格
// 	weight:"1",	//重量
// 	sale:"ttt",	//商品条码
// 	goodssn:"bbb",	//商品编码
// //	virtual:'0'
// },
// {ids:'17_1_18_10',id : "101",stock : "101",CostPrice : "1000",productprice : "1000",price : "2500",weight:"1",sale:"bbb",goodssn:"ttt",
// },
// {ids:'17_2_18_11',id : "102",stock : "102",CostPrice : "1000",productprice : "1000",price : "2500",weight:"1",sale:"bbb",goodssn:"ttt",
// },
// {ids:'17_2_18_10',id : "103",stock : "103",CostPrice : "1000",productprice : "1000",price : "2500",weight:"1",sale:"bbb",goodssn:"ttt",
// }];

$(document).ready(function(){
	//初始是否启用规格
	// if(data.length){
	// 	$("#hasoption").val(1);
	// 	choice();
	// }else{
	// 	$("#hasoption").val(2);
		choice();
	// }
	//点击是否启用
	$("#hasoption").click(function(){
		if($("#hasoption").val()==1){
			$("#hasoption").val(2);
			bool = true;
		}else if($("#hasoption").val()==2){
			$("#hasoption").val(1);
			bool=false;
			flag =1; //请添加规格
		}
		choice();
	});
	//判断是否启用多规格
function choice(){
	if($("#hasoption").val()==1){
		$('#hasoption').attr('checked','checked');
		$('#tboption').show();
		if(data.length){
			init();
		}
	}else if($("#hasoption").val()==2){
		$('#hasoption').prop('checked',false);

		$('#tboption').hide();
	}else{
		$('#tboption').hide();
	}
};
	
//初始
function init(){
	//初始化规格
	var html='';
	for(var i=0;i<data.length;i++){
		html+='<style>.multi-item { height:110px;float:left;position:relative;}.img-thumbnail { width:100px;height:100px}.img-nickname { position: absolute;bottom:0px;line-height:25px;height:25px;color:#fff;text-align:center;width:100px;top-25px;background:rgba(0,0,0,0.8);}.multi-img-details { padding:5px;}</style>';

		html += '<div class="panel panel-default spec_item" id="spec_'+data[i].spec_id+'" ><div class="panel-bo dy"><input name="spec_id[]" type="hidden" class="form-control spec_id" value="'+data[i].spec_id+'"/><div class="form-group"><label class="col-xs-12 col-sm-3 col-md-2 control-label">规格名</label><div class="col-sm-9 col-xs-12"><input name="spec_title['+data[i].spec_id+']" type="text" class="form-control  spec_title" onpropertychange="change(this)" oninput="change(this)" value="'+data[i].spec_name+'" placeholder="(比如: 颜色)"/></div></div><div class="form-group"><label class="col-xs-12 col-sm-3 col-md-2 control-label">规格项</label><div class="col-sm-9 col-xs-12"><div id="spec_item_'+data[i].spec_id+'" class="spec_item_items">';
	
		for(var j=0;j<data[i].item.length;j++){
			html+='<div class="spec_item_item" style="float:left;margin:0 5px 10px 0;width:250px;">';
			html+='<input type="hidden" class="form-control spec_item_show" name="spec_item_show_'+data[i].spec_id+'[]" VALUE="1" />';
			html+='<input type="hidden" class="form-control spec_item_id" name="spec_item_id_'+data[i].spec_id+'[]" VALUE="'+data[i].item[j].item_id+'" />';
			html+='<div class="input-group"  style="margin:10px 0;"><span class="input-group-addon"><label class="checkbox-inline" style="margin-top:-20px;">';
			html+='<input type="checkbox" checked value="1" onclick="showItem(this)"></label></span><input type="text" class="form-control spec_item_title error" onpropertychange="change(this)" oninput="change(this)" name="spec_item_title_'+data[i].spec_id+'[]" VALUE="'+data[i].item[j].item_name+'" /><span class="input-group-addon"><a href="javascript:;" onclick="removeSpecItem(this)" title="删除"><i class="fa fa-times"></i></a><a href="javascript:;" class="fa fa-arrows" title="拖动调整显示顺序" ></a></span></div>';
			// html+= '<div class="input-group choosetemp" style="margin-bottom: 10px;display:none"><input type="hidden" name="spec_item_virtual_'+data[i].spec_id+'[]" value="0" class="form-control spec_item_virtual"  id="temp_id_'+data[i].item[j].item_id+'"><input type="text" name="spec_item_virtualname_'+data[i].spec_id+'[]" value="未选择" class="form-control spec_item_virtualname" readonly="" id="temp_name_'+data[i].item[j].item_id+'"><div class="input-group-btn"><button class="btn btn-default" type="button" onclick="choosetemp('+data[i].item[j].item_id+')">选择虚拟物品</button></div></div><div><div class="input-group "><input type="text" name="spec_item_thumb_'+data[i].spec_id+'[]" value="'+data[i].item[j].img_url+'" class="form-control" autocomplete="off" readonly="readonly"><span class="input-group-btn"><button class="btn btn-default" type="button" onclick="showImageDialog(this);">选择图片</button></span></div>';
			// html+='<div class="input-group " style="margin-top:.5em;width:100px;height:100px;float:left;margin-right:25px;"><img src="'+data[i].item[j].item_img+'" onerror="this.src="/Public/admin/images/nopic.jpg"; this.title="图片未找到."" class="img-responsive img-thumbnail"  width="100%" height="100%" /><em class="close" style="position:absolute; top: 0px; right: -14px;" title="删除这张图片" onclick="deleteImage(this)">×</em></div>';

			html+='</div>';
		}	
		
		html+='</div></div></div>';
		html += '<div class="form-group"><label class="col-xs-12 col-sm-3 col-md-2 control-label"></label><div class="col-sm-9 col-xs-12"><a href="javascript:;" id="add-specitem-'+data[i].spec_id+'" specid="'+data[i].spec_id+'" class="btn btn-info add-specitem" onclick="addSpecItem(this,\''+data[i].spec_id+'\',\''+data[i].item[0].item_id+'\')"><i class="fa fa-plus"></i> 添加规格项</a><a href="javascript:void(0);" class="btn btn-danger" onclick="removeSpec(\''+data[i].spec_id+'\')"><i class="fa fa-plus"></i> 删除规格</a></div>';
		html+='</div></div></div>';
	}
	$('#specs').html(html);
	//初始化规格结束
	
	//初始化规格表
	if(data2){
		setForm('1');
	}
}
})



//添加规格
function addSpec(){
	//获得规格的长度
    var len = $(".spec_item").length;
    //得到新增规格的spec_id
    var specId = '';
    if(data[0]==null){
    	specId= 1+len;
    }else{
    	specId= data[0].spec_id-0+len;
    }
    $("#add-spec").html("正在处理...").attr("disabled", "true").toggleClass("btn-primary");{
		$("#add-spec").html('<i class="fa fa-plus"></i> 添加规格').removeAttr("disabled").toggleClass("btn-primary");
		var html='';
		html+='<style>.multi-item { height:110px;float:left;position:relative;}.img-thumbnail { width:100px;height:100px}.img-nickname { position: absolute;bottom:0px;line-height:25px;height:25px;color:#fff;text-align:center;width:100px;top-25px;background:rgba(0,0,0,0.8);}.multi-img-details { padding:5px;}</style>';
		html += '<div class="panel panel-default spec_item" id="spec_'+specId+'" ><div class="panel-body"><input name="spec_id[]" type="hidden" class="form-control spec_id" value="'+specId+'"/><div class="form-group"><label class="col-xs-12 col-sm-3 col-md-2 control-label">规格名</label><div class="col-sm-9 col-xs-12"><input name="spec_title['+specId+']" type="text" class="form-control  spec_title" onpropertychange="change(this)" oninput="change(this)" value="" placeholder="(比如: 颜色)"/></div></div><div class="form-group"><label class="col-xs-12 col-sm-3 col-md-2 control-label">规格项</label><div class="col-sm-9 col-xs-12"><div id="spec_item_'+specId+'" class="spec_item_items">';
		html+='</div></div></div>';
		html += '<div class="form-group"><label class="col-xs-12 col-sm-3 col-md-2 control-label"></label><div class="col-sm-9 col-xs-12"><a href="javascript:;" id="add-specitem-'+specId+'" specid="'+specId+'" class="btn btn-info add-specitem" onclick="addSpecItem(this,\''+specId+'\',\''+specId+'_1\')"><i class="fa fa-plus"></i> 添加规格项</a><a href="javascript:void(0);" class="btn btn-danger" onclick="removeSpec(\''+specId+'\')"><i class="fa fa-plus"></i> 删除规格</a></div>';
		html+='</div></div></div>'
		$('#specs').append(html);
		var len = $(".add-specitem").length -1;
		$(".add-specitem:eq(" +len+ ")").focus();
                                                                        
//		window.optionchanged = true;
	}
}
	
//删除单个规格项
function removeSpecItem(obj){
	$(obj).parent().parent().parent().remove();
	bool = false;
	flag = 2; //数据改动
}

//添加规格项
function addSpecItem(_this, specid, spec_item_id){
	// alert(specid);
	$("#add-specitem-" + specid).html("正在处理...").attr("disabled", "true");
	
	$("#add-specitem-" + specid).html('<i class="fa fa-plus"></i> 添加规格项').removeAttr("disabled");
	//获取该规格的规格项长度
	var len = $('#spec_'+specid+'').find('.spec_item_item').length;
	//获得item_id的第二个数字
	var lastNum=spec_item_id.split('_')[1];
	//新的第二个数字
	var new_lastNum= lastNum-0+len;
	//该规格新增规格项的item_id
	var itemId = specid+'_'+new_lastNum;
	var html='';
	html+='<div class="spec_item_item" style="float:left;margin:0 5px 10px 0;width:250px;"><input type="hidden" class="form-control spec_item_show" name="spec_item_show_'+specid+'[]" VALUE="1" /><input type="hidden" class="form-control spec_item_id" name="spec_item_id_'+specid+'[]" VALUE="'+itemId+'" /><div class="input-group"  style="margin:10px 0;"><span class="input-group-addon"><label class="checkbox-inline" style="margin-top:-20px;"><input type="checkbox" checked value="1" onclick="showItem(this)"></label></span><input type="text" class="form-control spec_item_title error" onpropertychange="change(this)" oninput="change(this)" name="spec_item_title_'+specid+'[]" VALUE="" /><span class="input-group-addon"><a href="javascript:;" onclick="removeSpecItem(this)" title="删除"><i class="fa fa-times"></i></a><a href="javascript:;" class="fa fa-arrows" title="拖动调整显示顺序" ></a></span></div>';
	// html+= '<div class="input-group choosetemp" style="margin-bottom: 10px;display:none"><input type="hidden" name="spec_item_virtual_'+specid+'[]" value="0" class="form-control spec_item_virtual"  id="temp_id_'+itemId+'"><input type="text" name="spec_item_virtualname_'+specid+'[]" value="未选择" class="form-control spec_item_virtualname" readonly="" id="temp_name_'+itemId+'"><div class="input-group-btn"><button class="btn btn-default" type="button" onclick="choosetemp('+itemId+')">选择虚拟物品</button></div></div><div><div class="input-group "><input type="text" name="spec_item_thumb_'+specid+'[]" value="" class="form-control" autocomplete="off" readonly="readonly"><span class="input-group-btn"><button class="btn btn-default" type="button" onclick="showImageDialog(this);">选择图片</button></span></div>';
	// html+='<div class="input-group " style="margin-top:.5em;width:100px;height:100px;"><img src="/Public/admin/images/nopic.jpg" onerror="this.src="/Public/admin/images/nopic.jpg"; this.title="图片未找到."" class="img-responsive img-thumbnail"  width="100%" height="100%" /><em class="close" style="position:absolute; top: 0px; right: -14px;" title="删除这张图片" onclick="deleteImage(this)">×</em></div>';
	html+='</div></div>';
	$('#spec_item_' + specid).append(html);
	var len = $("#spec_" + specid + " .spec_item_title").length -1;
	$("#spec_" + specid + " .spec_item_title:eq(" +len+ ")").focus();
//	window.optionchanged = true;

}

//删除规格
function removeSpec(specid){
	if (confirm('确认要删除此规格?')){
		$("#spec_" + specid).remove();
//		window.optionchanged = true;
		bool = false;
		flag = 2; //数据改动
	}
}

//建表
function setForm(num){
	bool = true;
//	window.optionchanged = false;
	var html = '<table class="table table-bordered table-condensed" style="width:auto; min-width:auto;"><thead><tr class="active">';
	var specs = [];
	//没有规格
    if($('.spec_item').length<=0){
        $("#options").html('');
//      bool = false;
        alert('请添加规格！');
        return;
    }
    //规格名为空时
    var stitle = $('.spec_title');
    for(var i=0;i<stitle.length;i++){
	    if(!stitle.eq(i).val()){
	    	$("#options").html('');
	    	alert('规格名不能为空');
	        return;
	    }
	    var item_count = stitle.eq(i).parent().parent().parent().find('.spec_item_items').children().length;
		if(!item_count){
			alert('请添加规格项！');
			return false;
		}
    }
    //规格项名称为空
    var ititle = $('.spec_item_title');
    for(var i=0;i<ititle.length;i++){
	    if(!ititle.eq(i).val()){
	    	// console.log('111');
	    	$("#options").html('');
	    	alert('规格项名称不能为空！');
	        return;
	    }
    }
    
	$(".spec_item").each(function(i){
		var _this = $(this);
		//规格id和规格名
		var spec = {
			id: _this.find(".spec_id").val(),
			title: _this.find(".spec_title").val()
		};
	
		var items = [];
		_this.find(".spec_item_item").each(function(){
			var __this = $(this);
			//规格项的id 名 
			var item = {
				id: __this.find(".spec_item_id").val(),
				title: __this.find(".spec_item_title").val(),
				virtual: __this.find(".spec_item_virtual").val(),
				show:__this.find(".spec_item_show").get(0).checked?"1":"0"
			}
			items.push(item);
		});
		spec.items = items;
		specs.push(spec);
	});
	
	//按规格项个数排序规格
	
	//规格个数
	var len = specs.length;
	var newlen = 1; 
	var h = new Array(len); 
	var rowspans = new Array(len); 
	//遍历规格名
	for(var i=0;i<len;i++){
		html+="<th style='width:160px;'>" + specs[i].title + "<input type='hidden' class='format' name='format[]' value='"+specs[i].title+"'/></th>";
		//规格项个数
		var itemlen = specs[i].items.length;
		if(itemlen<=0) { itemlen = 1 ;}
		//组合总长
		newlen*=itemlen;
		h[i] = new Array(newlen);
		for(var j=0;j<newlen;j++){
			h[i][j] = new Array();
		}
		var l = specs[i].items.length;
		rowspans[i] = 1;
		for(j=i+1;j<len;j++){
			rowspans[i]*= specs[j].items.length;
		}
	}
	html += '<th class="info" style="width:160px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">库存</div><div class="input-group"><input type="text" class="form-control option_stock_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></div></div></th>';
	// html+='<th class="warning" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">市场价格</div><div class="input-group"><input type="text" class="form-control option_productprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></div></div></th>';
	// html+='<th class="danger" style="width:160px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">成本价</div><div class="input-group"><input type="text" class="form-control option_CostPrice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_CostPrice\');"></a></span></div></div></th>';
	html += '<th class="success" style="width:160px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">一折购价</div><div class="input-group"><input type="text" class="form-control option_price_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_price\');"></a></span></div></div></th>';

	
    // html+='<th class="primary" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品编码</div><div class="input-group"><input type="text" class="form-control option_goodssn_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_goodssn\');"></a></span></div></div></th>';
    // html+='<th class="danger" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品条码</div><div class="input-group"><input type="text" class="form-control option_sale_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_sale\');"></a></span></div></div></th>';
	html+='<th class="info" style="width:160px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">重量（克）</div><div class="input-group"><input type="text" class="form-control option_weight_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></div></div></th>';
	html += '<th class="success" style="width:250px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">规格图片</div><div class="input-group"></div></div></th>';
	html+='</tr></thead>';
	
	for(var m=0;m<len;m++){
		var k = 0,kid = 0,n=0;
		for(var j=0;j<newlen;j++){
			var rowspan = rowspans[m]; 
			if( j % rowspan==0){
				h[m][j]={title: specs[m].items[kid].title, virtual: specs[m].items[kid].virtual,html: "<td rowspan='" +rowspan + "'>"+ specs[m].items[kid].title+"</td>\r\n",id: specs[m].items[kid].id};
			}
			else{
				h[m][j]={title:specs[m].items[kid].title,virtual: specs[m].items[kid].virtual, html: "",id: specs[m].items[kid].id};	
			}
			n++;
			if(n==rowspan){
				kid++; 
				if(kid>specs[m].items.length-1) { 
					kid=0; 
				}
				n=0;
			}
		}
	}
 
	var hh = "";
	for(var i=0;i<newlen;i++){
		hh+="<tr>";
		var ids = [];
		var titles = [];    
        var virtuals = [];
		for(var j=0;j<len;j++){
			hh+=h[j][i].html; 
			ids.push( h[j][i].id);
			titles.push( h[j][i].title); 
			virtuals.push( h[j][i].virtual);
		}
		ids =ids.join('_');
		titles= titles.join('@#');
		
		// var val ={ id : "",title:titles, stock : "",CostPrice : "",productprice : "",price : "",weight:"",sale:"",goodssn:"",virtual:virtuals };
		var val ={ id : "",title:titles, stock : "",CostPrice : "",price : "",weight:"",virtual:virtuals,iaddress:"" };
		//初始化
		if(num==1){
			for(var s=0;s<data2.length;s++){
				if(data2[s].ids==ids){
					val ={
						id : data2[s].id,
						title: titles,
						stock : data2[s].stock,
						CostPrice : data2[s].CostPrice,
						// productprice : data2[s].productprice,
						price : data2[s].price,
						// goodssn : data2[s].goodssn,
						// sale : data2[s].sale,
						weight : data2[s].weight,
		                virtual : virtuals,
		                // iid:data2[s].iid,
		                iaddress:data2[s].iaddress
					}
				}
			}
		}
		//点击刷新表
		else if(num == 2){
			if( $(".option_id_" + ids).length>0){
				val ={
					id : $(".option_id_" + ids+":eq(0)").val(),
					title: titles,
					stock : $(".option_stock_" + ids+":eq(0)").val(),
					CostPrice : $(".option_CostPrice_" + ids+":eq(0)").val(),
					// productprice : $(".option_productprice_" + ids+":eq(0)").val(),
					price : $(".option_price_" + ids +":eq(0)").val(),
					// goodssn : $(".option_goodssn_" + ids +":eq(0)").val(),
					// sale : $(".option_sale_" + ids +":eq(0)").val(),
					weight : $(".option_weight_" + ids+":eq(0)").val(),
	                virtual : virtuals,
	                // iid:"",
	                iaddress:""
				}
			}
		}
		if(val.title == undefined){
			alert('请添加规格项！');
		}
		// console.log(val.title);
		hh += '<td class="info">';
		hh += '<input name="option_stock[]" type="text" class="form-control option_stock option_stock_' + ids +'" value="' +(val.stock=='undefined'?'':val.stock )+'"/></td>';
		hh += '<input name="option_id[]" type="hidden" class="form-control option_id option_id_' + ids +'" value="' +(val.id=='undefined'?'':val.id )+'"/>';
		hh += '<input name="option_ids[]" type="hidden" class="form-control option_ids option_ids_' + ids +'" value="' + ids +'"/>';
		hh += '<input name="option_title[]" type="hidden" class="form-control option_title option_title_' + ids +'" value="' +(val.title=='undefined'?'':val.title )+'"/></td>';
        hh += '<input name="option_virtual[]" type="hidden" class="form-control option_title option_title_' + ids +'" value="' +(val.virtual=='undefined'?'':val.virtual )+'"/></td>';
		hh += '</td>';
		// hh += '<td class="warning"><input name="option_productprice_' + ids+'[]" type="text" class="form-control option_productprice option_productprice_' + ids +'" " value="' +(val.productprice=='undefined'?'':val.productprice )+'"/></td>';
		// hh += '<td class="danger"><input name="option_CostPrice[]" type="text" class="form-control option_CostPrice option_CostPrice_' + ids +'" " value="' +(val.CostPrice=='undefined'?'':val.CostPrice )+'"/></td>';
		hh += '<td class="success"><input name="option_price[]" type="text" class="form-control option_price option_price_' + ids +'" value="' +(val.price=='undefined'?'':val.price )+'"/></td>';
        // hh += '<td class="primary"><input name="option_goodssn_' +ids+'" type="text" class="form-control option_goodssn option_goodssn_' + ids +'" " value="' +(val.goodssn=='undefined'?'':val.goodssn )+'"/></td>';
        // hh += '<td class="danger"><input name="option_sale_' +ids+'[]" type="text" class="form-control option_sale option_sale_' + ids +'" " value="' +(val.sale=='undefined'?'':val.sale )+'"/></td>';
		hh += '<td class="info"><input name="option_weight[]" type="text" class="form-control option_weight option_weight_' + ids +'" " value="' +(val.weight=='undefined'?'':val.weight )+'"/></td>';
		hh += '<td class="info" style="line-height:50px;"><button data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-xs choose_photo" type="button" isMultiple="0" showContainer=".option_container" index="'+i+'" uploadType="goods">点击选择图片</button>';

		hh += '<div class="option_container" style="float:left;margin:5px;width:50px;height:50px;margin-left:20px;" index="'+i+'">';
		hh += '<input name="option_path[]" type="hidden" class="form-control option_path option_path_' + ids +'" " value="' +(val.iaddress=='0'?'':val.iaddress )+'"/>';
		hh += '<img class="option_img" src="'+val.iaddress+'" style="width:50px;height:50;"/>';
		hh += "</div></td>";

		hh += "</tr>";
	}
	html+=hh;
	html+="</table>";
	$("#options").html(html);
	choosePhotoClick();
	if(num == 2){
	// 	$(".format").val('');
		$(".option_id").val('');
	
	}
}	


//刷新规格项目表
function calc(){
	setForm('2');
}
//刷新规格项目表结束

//规格表批量设置
function setCol(cls){
	$("."+cls).val( $("."+cls+"_all").val());
}
function showItem(obj){
	var show = $(obj).get(0).checked?"1":"0"; 
	$(obj).next().val(show);
}
function nofind(){
	var img=event.srcElement;
	img.src="./resource/image/module-nopic-small.jpg";
	img.onerror=null; 
}
function choosetemp(id){
    $('#modal-module-chooestemp').modal();
    $('#modal-module-chooestemp').data("temp",id);
}
//选择图片 确定选择
function addtemp(){
    var id = $('#modal-module-chooestemp').data("temp");
    var temp_id = $('#modal-module-chooestemp').find("select").val();
    var temp_name = $('#modal-module-chooestemp option[value='+temp_id+']').text();
    //alert(temp_id+":"+temp_name);
    $("#temp_name_"+id).val(temp_name);
    $("#temp_id_"+id).val(temp_id);
    $('#modal-module-chooestemp .close').click();
//  window.optionchanged = true;
}

//删除图片
function deleteImage(elm){
	$(elm).prev().attr("src", "/Public/admin/images/nopic.jpg");
	$(elm).parent().prev().find("input").val("");
}

//选择图片
function showImageDialog(elm, opts, options) {
	alert('选择图片');		
}


//数据有改动
function change(This){ 
	bool = false;
	flag = 2;
}




//检测 商品管理
function addCheck(){
	var oBool = true;	//检测选项是否填写
//	console.log($('.img_container .m_imgBox').length);
		if($('input[name=goods_name]').val()==''){
			alert('请输入商品名称！');
			oBool = false;
		}
		else if($('.checkSoncategory:last option:selected').val() == 0){		//改
			alert('请选择分类！');
			oBool = false;
		}
		// else if($('input[name=goods_code]').val()!='' && !getNumBool($('input[name=goods_code]').val())){
		// 	alert('请输入正确的条形码！');
		// 	oBool = false;
		// }
//		else if(!getNumBool($('input[name=goods_sort]').val())){
//			alert('请输入正确的排序！');
//			oBool = false;
//		}
//		else if(!getNumBool($('input[name=goods_sale]').val())){
//			alert('请输入正确的销量！');
//			oBool = false;
//		}
//		else if(!getPointBool($('input[name=goods_weight]').val())  && !($('input[name=goods_weight]').attr('disabled'))){
//			alert('请输入正确的产品重量（最多保留两位小数）！');
//			oBool = false;
//		}
		// else if(!getPointBool($('input[name=goods_costPrice]').val()) && !($('input[name=goods_costPrice]').attr('disabled'))){
		// 	alert('请输入正确的成本价（最多保留两位小数）！');
		// 	oBool = false;
		// }
		else if(!getPointBool($('input[name=goods_price]').val()) && !($('input[name=goods_price]').attr('disabled'))){
			alert('请输入正确的销售价（最多保留两位小数）！');
			oBool = false;
		}
		else if(!getNumBool($('input[name=goods_stock]').val())  && !($('input[name=goods_stock]').attr('disabled'))){
			alert('请输入正确的库存量！');
			oBool = false;
		}
		// else if(!$('input[name=goods_unit]').val()){
		// 	alert('请输入单位！');
		// 	oBool = false;
		// }
//		else if($('input:radio[name=less]:checked').val()== undefined){
//			alert('请选择减库存方式！');
//			oBool = false;
//		}
		else if($('.img_container .path').val() == null){	//改
			alert('请添加商品图片！');
			oBool = false;
		}
		else{
			oBool = true;
			//启动了商品规格
			if($('#hasoption').is(':checked')){
				//判断有无规格
				if($("#hasoption").val()==1 && $('.spec_item').length<=0){
					alert('请添加规格！');
					return;
				}
				//规格名为空时
			    var stitle = $('.spec_title');
			    for(var i=0;i<stitle.length;i++){
				    if(!stitle.eq(i).val()){
				    	$("#options").html('');
				    	alert('规格名不能为空！');
				    	return;
				    }
				    var item_count = stitle.eq(i).parent().parent().parent().find('.spec_item_items').children().length;
					if(!item_count){
						alert('请添加规格项！');
						return false;
					}
			    }
			    //规格项名称为空
			    var ititle = $('.spec_item_title');
			    for(var i=0;i<ititle.length;i++){
				    if(!ititle.eq(i).val()){
				    	$("#options").html('');
				    	alert('规格项名称不能为空！');
				    	return;
				    }
			    }
			    if(!bool && flag ==2){
			    	alert("数据已改动，请按“刷新规格项目表”！");
			    	return;
			    }
			    else{
			    	//判断项目表数据
					var l=0; 
					var notNum = 0;
					console.log(flag);
				    $('.table tbody td input:not(.option_path)').each(function() {	
				    	if($(this).val()==''){
				    		l++
				    	}
				    	else if(!getPointBool($(this).val())){
				    		notNum++;
				    	}
				    });
				    if(l){
				    	bool =false;
				    	flag =3;
				    }
				    else if(notNum){
				    	bool = false;
				    	flag = 4;
				    }
				    else{
				    	bool = true;
				    }
			    }
		    
			    //
			    if(!bool){
			    	if(flag ==1){
			    		alert('请添加规格！');
			    	}else if(flag == 3){
			    		alert('请填写完整规格项目表！');
			    	}else if(flag == 4){
			    		alert('请正确填写规格项表单（最多包含两位小数的数字）！');
			    	}
					return false;
				}else if (bool && oBool){
					$('#send').attr('type','submit');
				}
			}
			//没有启用规格项
			else{
				$('#send').attr('type','submit');
			}
		}

}




////检测 一折购管理
//function addCheckY(){
//	var oBool = true;	//检测选项是否填写
//	console.log($('.sort').val());
//	// alert($('.checkSoncategory:last option:selected').val());
//		if($('input[name=goods_name]').val()==''){
//			alert('请输入商品名称！');
//			oBool = false;
//		}
//		else if($('.checkSoncategory:last option:selected').val() != 0){		//改
//			alert('请选择分类！');
//			oBool = false;
//		}
//		else if(!getNumBool($('input[name=goods_sort]').val())){
//			alert('请输入正确的排序！');
//			oBool = false;
//		}
//		else if(!getNumBool($('input[name=goods_sale]').val())){
//			alert('请输入正确的销量！');
//			oBool = false;
//		}
//		else if(!getNumBool($('input[name=goods_stock]').val())  && !($('input[name=goods_stock]').attr('disabled'))){
//			alert('请输入正确的库存量！');
//			oBool = false;
//		}
//		else if(!getPointBool($('input[name=goods_weight]').val())  && !($('input[name=goods_weight]').attr('disabled'))){
//			alert('请输入正确的产品重量（最多保留两位小数）！');
//			oBool = false;
//		}
//		else if(!getPointBool($('input[name=goods_costPrice]').val()) && !($('input[name=goods_costPrice]').attr('disabled'))){
//			alert('请输入正确的成本价（最多保留两位小数）！');
//			oBool = false;
//		}
//		else if(!getPointBool($('input[name=goods_price]').val()) && !($('input[name=goods_price]').attr('disabled'))){
//			alert('请输入正确的一折购价（最多保留两位小数）！');
//			oBool = false;
//		}
//		else if($('input:radio[name=less]:checked').val()== undefined){
//			alert('请选择减库存方式！');
//			oBool = false;
//		}
//		else if($('.img_container .path').val() ==null){	//改
//			alert('请添加商品图片！');
//			oBool = false;
//		}
//		else{
//			oBool = true;
//			//启动了商品规格
//			if($('#hasoption').is(':checked')){
//				//判断有无规格
//				if($("#hasoption").val()==1 && $('.spec_item').length<=0){
//					alert('请添加规格！');
//					return;
//				}
//				//规格名为空时
//			    var stitle = $('.spec_title');
//			    for(var i=0;i<stitle.length;i++){
//				    if(!stitle.eq(i).val()){
//				    	$("#options").html('');
//				    	alert('规格名不能为空！');
//				    	return;
//				    }
//				    var item_count = stitle.eq(i).parent().parent().parent().find('.spec_item_items').children().length;
//					if(!item_count){
//						alert('请添加规格项！');
//						return false;
//					}
//			    }
//			    //规格项名称为空
//			    var ititle = $('.spec_item_title');
//			    for(var i=0;i<ititle.length;i++){
//				    if(!ititle.eq(i).val()){
//				    	$("#options").html('');
//				    	alert('规格项名称不能为空！');
//				    	return;
//				    }
//			    }
//			    if(!bool && flag ==2){
//			    	alert("数据已改动，请按“刷新规格项目表”！");
//			    	return;
//			    }
//			    else{
//			    	//判断项目表数据
//					var l=0; 
//					var notNum = 0;
//					console.log(flag);
//				    $('.table tbody td input:not(.option_path)').each(function() {	
//				    	if($(this).val()==''){
//				    		l++
//				    	}
//				    	else if(!getPointBool($(this).val())){
//				    		notNum++;
//				    	}
//				    });
//				    if(l){
//				    	bool =false;
//				    	flag =3;
//				    }
//				    else if(notNum){
//				    	bool = false;
//				    	flag = 4;
//				    }
//				    else{
//				    	bool = true;
//				    }
//			    }
//		    
//			    //
//			    if(!bool){
//			    	if(flag ==1){
//			    		alert('请添加规格！');
//			    	}else if(flag == 3){
//			    		alert('请填写完整规格项目表！');
//			    	}else if(flag == 4){
//			    		alert('请正确填写规格项表单（最多包含两位小数的数字）！');
//			    	}
//					return false;
//				}else if (bool && oBool){
//					$('#send').attr('type','submit');
//				}
//			}
//			//没有启用规格项
//			else{
//				$('#send').attr('type','submit');
//			}
//		}
//
//}




//不带小数点数字
function getNumBool(obj){
    var  pattern = /^[0-9]+$/;
    var bool = pattern.test(obj);
    return bool;
}
//带小数点数字
function getPointBool(obj){
    var  pattern = /^(([1-9][0-9]*)|(([0]\.\d{0,2}|[1-9][0-9]*\.\d{0,2}))|(0))$/;
    var bool = pattern.test(obj);
    return bool;
}