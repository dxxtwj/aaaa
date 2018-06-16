$('.loading').remove();
// console.log(1);
console.log(orderDataJson);
var orderDataList = JSON.parse(orderDataJson);
// console.log(orderDataList);
function isWeiXin(){ // 判断是不是微信
    var ua = window.navigator.userAgent.toLowerCase();
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
        return true;
    }else{
        return false;
    }
}
var vm = new Vue({
	el:'#control',
	data:{
		//提现记录信息
		allOrderMes : [],
		//点击的是第几个分类
		oIndex:-1,
		//加载标志 变为false就是加载完毕
		loadBool:true,
	},
	methods:{
		//初始化
		init:function(){
			//模拟后台总的订单信息 
			// var allOrder = [
			// // //待收货
			// {"danhao":"SH20171215114927283504","status":"3","state":"待收货","total_price":'444.00',"href":"#1","array":[{"img":"/Public/home/images/goods_1.jpg","mes":"植物医家轻盈无痕明星四色散粉12g，美丽一整天","num":"2","price":"45.82","gid":"0"}]},
		 //   //待发货
			// {"danhao":"SH201602281404537489","status":"2","state":"待发货","total_price":"444.00","href":"#2","array":[{"img":"/Public/home/images/goods_1.jpg","mes":"植物医家轻盈无痕明星四色散粉12g，美丽一整天","num":"1","price":"45.82","gid":"0"}]},
		 //   //待付款
			// {"danhao":"SH201602281404537489","status":"1","state":"待付款","total_price":"444.00","href":"#3","array":[{"img":"/Public/home/images/goods_1.jpg","mes":"植物医家轻盈无痕明星四色散粉12g，美丽一整天","num":"1","price":"45.82","gid":"0"}]},
		 //   // 已完成
			// {"danhao":"SH201602281404537489","status":"0","state":"待评价","total_price":"444.00","href":"#4","array":[{"img":"/Public/home/images/goods_1.jpg","mes":"植物医家轻盈无痕明星四色散粉12g，美丽一整天","num":"1","price":"45.82","gid":"0"},{"img":"/Public/home/images/goods_1.jpg","mes":"植物医家轻盈无痕明星四色散粉12g，美丽一整天","num":"1","price":"45.82","gid":"0"}]}
			// ];
			if (orderDataJson != 0) {

				allOrder = orderDataList;
				$('.list').css('display','block');
				$('.no_data').css('display','none');
				vm.allOrderMes = allOrder;	
			} else {

				var allOrder = [];
				$('.list').css('display','none');
				$('.no_data').css('display','block');		
			}


		},
		//状态文字颜色
		showStatus:function(index){
			switch(index){
				case '1' ://待付款
					return 'unpaid';
					break;
				case '2' ://待发货
					return 'delivery';
					break;
				case '3' ://待收货
					return 'received';
					break;
				case '0' ://待评价
					return 'assess';
					break;
			}
		},
		//点击选择
		choice:function(index){
			//loading
			$('body').append('<div class="loading"><div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>');
			vm.oIndex = index;
			
			$(document).scrollTop('0');

			//上拉加载数据初始化
			vm.loadBool = true;
			$('.end').remove();
			count = 0;	//模拟数据   上拉加载次数初始化	
			// console.log(index);
			//相当于ajax的success 开始
			// var allOrder = [
		 //   		{"danhao":"SH20171215114927283504","status":"3","state":"待收货","total_price":'444.00',"href":"#5","array":[{"img":"/Public/home/images/goods_1.jpg","mes":"植物医家轻盈无痕明星四色散粉12g，美丽一整天","num":"2","price":"45.82","gid":"0"}]}
		 //   	]
		 $.ajax({

		 	data: {state: index, page: count},

		 	type: 'post',

		 	url: '/index.php/Home/MyOrder/getOrder_ajax',

		 	success: function(msg) {
		 		
		 		console.log(msg);

	 			if (msg != 0) {

	 				var allOrder = msg;

	 				$('.list').css('display','block');
					$('.no_data').css('display','none');
					vm.allOrderMes = allOrder;	

				} else {

					var allOrder = [];
					$('.list').css('display','none');
					$('.no_data').css('display','block');	
				}

		 	}


		 });
			
			//删除loading
			$('.loading').remove();
			//相当于ajax的success 结束
		},
		//确认收货或者去付款 oid 为订单id
		toMove:function(oid,index){
			if(vm.allOrderMes[index].move == '付款'){
				// alert('付款');
				// 传订单id
				var This = this;
                var URL = "/index.php/Home/Pay/pay";
                var temp = document.createElement("form");
                temp.action = URL;
                temp.method = "post";
                temp.style.display = "none";
                var PARAMS = new Array();
                PARAMS['oid'] = oid; //订单id
                PARAMS['type'] = 1; // 订单类型  可能是一折购  可能是其他类型的，现在是普通商品
                if(isWeiXin()){
                	PARAMS['isWeiXin'] = 1;
                }else{
                	PARAMS['isWeiXin'] = 0;
                }
                for (var x in PARAMS) {
                    var opt = document.createElement("textarea");
                    opt.name = x;
                    opt.value = PARAMS[x];
                    // alert(opt.name)
                    temp.appendChild(opt);
                }
	                document.body.appendChild(temp);
	                temp.submit();
	                return temp;

			}else if(vm.allOrderMes[index].move == '确认收货'){
				mui.confirm('确认收货？',function(e){
					if(e.index){
						$.ajax({

							data: {oid: oid},

							type: 'post',

							url: '/index.php/Home/MyOrder/affirmOrder',

							success: function(msg) {
								if (msg.code == 1) {

									mui.toast(msg.msg);
								} else {
									
									mui.toast(msg.msg);
								}
							}

						});
					
					
					}
				})
			}
			
		},
		//查看物流
		checkGood:function(){
			alert('查看物流');
		},
		//取消订单
		cancelOrder:function(id){
			mui.confirm('确定取消订单?',function(e){
				if(e.index==1){

					$.ajax({

						data: {oid: id},

						type: 'post',

						url: '/index.php/Home/MyOrder/exitOrder',

						success: function(msg) {
							
							if (msg.code == 1) {
								mui.toast(msg.msg);
							} else {
								mui.toast(msg.msg);
							}
						}

					})
				}
			})
		},
		// 查看详情
		// oid是订单id
		toOrder:function(oid){
			
			$.ajax({

				data: {oid: oid},

				url: '/index.php/Home/MyOrder/orderAjax',

				type: 'post',

				success: function(msg) {
					console.log(msg);
					if (msg.code == 1) {

						location.href = msg.url;
					}
				}
			});
		},
	},
});

vm.init();


//上拉加载

var count = 0;	//模拟数据 上拉次数
var mBool = true;
window.onscroll = function(){
//	console.log($(document).scrollTop());		//文档距顶部的偏移量
//	console.log($(document).height());		//文档的总高度
//	console.log($(window).height());		//窗口的高度
	
	var bodyHeight = $(document).height();
	var documentHeight = $(document).scrollTop()+$(window).height();
	
	if(parseInt(bodyHeight) <= parseInt(documentHeight)+10){
		count++;//模拟数据
		if($('#warn').length == 0  && $(document).scrollTop()>0 && vm.loadBool){
			if(mBool){
				mBool = false;
				$('.mui-content').append('<div class="spinner-yun" id="warn"><div class="spinner-yun-container container1"><div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div></div><div class="spinner-yun-container container2"><div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div></div><div class="spinner-yun-container container3"><div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div></div></div>');
				// console.log(vm.oIndex);	//当前是第几个分类
				//模拟上拉过程
	
					// alert(count);
						// vm.allOrderMes.push({"danhao":"SH20171215114927283504","status":"3","state":"待收货","total_price":'444.00',"href":"#6","array":[{"img":"/Public/home/images/goods_1.jpg","mes":"植物医家轻盈无痕明星四色散粉12g，美丽一整天","num":"2","price":"45.82","gid":"0"}]});

				$.ajax({

					data: {state: vm.oIndex, page: count},

					url: '/index.php/Home/MyOrder/getOrder_ajax',

					type: 'post',

					success: function(msg) {
						console.log(msg);
						if (msg != 0) {
							for (var i in msg) {
								vm.allOrderMes.push({"danhao": msg[i].danhao, "status":msg[i].status, "state": msg[i].state, "total_price": msg[i].total_price, href: "#", "move":msg[i].move, "id":msg[i].id, "array": msg[i].array});
							}

						} else {//加载完毕 无数据

							$(".mui-content").append('<p class="end" style="text-align: center;padding-top:10px;color:#999;"><i style="display:inline-block; width:3rem;height:1px;background-color:#ccc;position:relative;top:-4px;"></i>&nbsp;&nbsp;&nbsp;加载完毕&nbsp;&nbsp;&nbsp;<i style="display:inline-block;width:3rem;height:1px;background-color:#ccc;position:relative;top:-4px;"></i></p>');
						    vm.loadBool = false;
						}
						mBool = true;
						$("#warn").remove();
					}
				});
			}
		}
	}
};
