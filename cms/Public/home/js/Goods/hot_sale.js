$('.loading').remove();
if (searchJson != '') {

	var searchJsonObject = JSON.parse(searchJson);
}
console.log(searchJsonObject);
//热销产品
// var productArr = [{"href":"#1","img":"/Public/home/images/goods_1.jpg","text":"植物医家轻盈无痕明星四色散粉12g，美丽一整天","price":"88","old_price":"188","gid":0},
// {"href":"#2","img":"/Public/home/images/goods_1.jpg","text":"植物医家轻盈无痕美丽一整天","price":"88","old_price":"188","gid":0},
// {"href":"#3","img":"/Public/home/images/goods_1.jpg","text":"植物医家轻盈美丽一整天","price":"88","old_price":"188","gid":0},
// {"href":"#","img":"/Public/home/images/goods_1.jpg","text":"植物医家轻盈无痕明一整天","price":"88","old_price":"188","gid":0},
// ];
var goodsIds = new Array(); // 存储商品id,为之后的搜索用懒加载，neq这些id的商品搜索出来

for (var k in gr_info_json) {
	goodsIds.push(gr_info_json[k].id);
}
console.log(goodsIds);
// 热销产品遍历
var productArr = new Array();
for (var i in gr_info_json) {
	productArr.push({"href": "/index.php/Home/Goods/goods_detail/gid/" + gr_info_json[i].id, "img": gr_info_json[i].img, "text": gr_info_json[i].name, "price": gr_info_json[i].price, "old_price": gr_info_json[i].oldPrice, "gid": gr_info_json[i].id});
}

//productArr=[];
var vm = new Vue({
	el:'#control',
	data:{
      productArr:[],
      loadBool:true, //加载标志 变为false就是加载完毕
      flag:0, //滚动标志
      //排序的名称
	  sortName:null,
	  //排序方向==》top:箭头向上，bottom:箭头向下
	  sortDir:null,
	  loadBool:true, //加载标志 变为false就是加载完毕
	},
	methods:{
		//初始化
		init:function(){
			if(productArr.length!=0){
				$('.list').css('display','block');
				$('.no_data').css('display','none');
				vm.productArr = productArr;
			}else{
				$('.list').css('display','none');
				$('.no_data').css('display','block');
			}
		},
		//选项卡
		choice:function(e){
			$(document).scrollTop('0');
			//上拉加载数据初始化
			vm.loadBool = true;
			$('.end').remove();
			count = 0;	//模拟数据   上拉加载次数初始化	
			
			$('body').append('<div class="loading"><div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>');
			
//			console.log(e.currentTarget);
			var that = e.currentTarget;
			vm.sortName = $(that).find('span').text();	//获得排序的名称
			// alert(vm.sortName);
			
			var cate = get_info_json.cate //顶级分类id 为多条件搜索用
			
			//改变文字样式
			$('.sort_list li').removeClass('active');
			$(that).addClass('active');
			
			var oIcos = $(that).find('.dir').children();
			var oLength = oIcos.length;
			if(oLength!=0){
//				alert(oLength);
				var bool = $(that).find('.icon-up').hasClass('active-icon');
//				alert(bool);
				
				if(!bool){
					$('.icon-up').removeClass('active-icon');
					$('.icon-down').removeClass('active-icon');
					$(that).find('.icon-up').addClass('active-icon');
					this.sortDir = 'top';
					// 调用ajax
					// 参数1： top|bottom|null
					// 参数2： 顶级分类id
					// 参数3： 综合|价格|销量|新品
					ajaxOrder(this.sortDir, cate, vm.sortName, searchJsonObject); 
				}
				else{
					$('.icon-up').removeClass('active-icon');
					$('.icon-down').removeClass('active-icon');
					$(that).find('.icon-down').addClass('active-icon');
					this.sortDir='bottom';
					// 调用ajax
					// 参数1： top|bottom|null
					// 参数2： 顶级分类id
					// 参数3： 综合|价格|销量|新品
					ajaxOrder(this.sortDir, cate, vm.sortName, searchJsonObject); 
				}
			}
			else{
				$('.icon-up').removeClass('active-icon');
				$('.icon-down').removeClass('active-icon');
				this.sortDir=null;
				// 调用ajax
				// 参数1： top|bottom|null
				// 参数2： 顶级分类id
				// 参数3： 综合|价格|销量|新品
				ajaxOrder(this.sortDir, cate, vm.sortName, searchJsonObject); 
			}
			
		}

		
	},
});
vm.init();


	/**
	 * 用户点击按条件搜索时候触发的一个方法
	 * @param  {string} sortDir  top|bottom|null
	 * @param  {int} cate     顶级分类id
	 * @param  {string} sortName 综合|价格|销量|新品
	 * @param {string} searchJsonObject 搜索的商品名字
	 */	
	function ajaxOrder(sortDir, cate=0, sortName, searchJsonObject) {

		$.ajax({

			data: {cate: cate, sortDir: sortDir, sortName: sortName, search: searchJsonObject},

			type: 'get',

			url: '/index.php/Home/Goods/ajaxOrder',

			success: function(msg) {
				console.log(msg);
				// vm.productArr.push({"href":"#1","img":"/Public/home/images/goods_1.jpg","text":"ssssss","price":"88","old_price":"188","gid":0});
				if (msg.code == 1) {
					vm.productArr = new Array();
					for (var g in msg.data) {
						vm.productArr.push({"href": "/index.php/Home/Goods/goods_detail/gid/" + msg.data[g].id, "img": msg.data[g].img, "text": msg.data[g].name, "price": msg.data[g].price, "old_price": msg.data[g].oldPrice, "gid": msg.data[g].id
						});
					}
				}
				$('.loading').remove();
			}

		});
	}

var count = 0;
window.onscroll = function(){	
//	console.log($(document).scrollTop());	//文档偏移高度
//	console.log($(window).height());	//窗口高度
//	console.log($(document).height());	//文档高度 
	
	var ScrollTop = $(document).scrollTop()+ $(window).height();
	var BodyHeight = $(document).height();
	
	if(parseInt(ScrollTop)+10 >= parseInt(BodyHeight)){
		count++; //模拟数据用
		

		if($('#warn').length == 0  && $(document).scrollTop()>0 && vm.loadBool){
			$('.mui-content').append('<div class="spinner-yun" id="warn"><div class="spinner-yun-container container1"><div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div></div><div class="spinner-yun-container container2"><div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div></div><div class="spinner-yun-container container3"><div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div></div></div>');

			$.ajax({
				// 搜索内容searchJsonObject
				data: {page: count, search: searchJsonObject, goodsIds: goodsIds},

				url: "/index.php/Home/Goods/hot_sale/",

				type: "get",

				success: function(msg) {
					console.log(msg);
				 if(msg.code == 1){ //有数据
					// vm.productArr.push({"href":"#1","img":"/Public/home/images/goods_1.jpg","text":"ssssss","price":"88","old_price":"188","gid":0});
					var productArr = new Array();
					for (var g in msg.data) {
						vm.productArr.push({"href": "/index.php/Home/Goods/goods_detail/gid/" + msg.data[g].id, "img": msg.data[g].img, "text": msg.data[g].name, "price": msg.data[g].price, "old_price": msg.data[g].oldPrice, "gid": msg.data[g].id
						});
					}
	            }
	            else{//加载完毕 无数据
	                $(".mui-content").append('<p class="end" style="text-align: center;padding-top:10px;color:#999;"><i style="display:inline-block; width:3rem;height:1px;background-color:#ccc;position:relative;top:-4px;"></i>&nbsp;&nbsp;&nbsp;加载完毕&nbsp;&nbsp;&nbsp;<i style="display:inline-block;width:3rem;height:1px;background-color:#ccc;position:relative;top:-4px;"></i></p>');
	                vm.loadBool = false;
	            }
	            $("#warn").remove();
				}

			});

	           
		}
	}
};
