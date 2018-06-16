$('.loading').remove();
var coupon = JSON.parse(info);
//console.log(info);
var vm = new Vue({
	el:'#control',
	data:{
		//提现记录信息
		couponMes : [],
		//点击的是第几个分类
		oIndex:0,
		//加载标志 变为false就是加载完毕
		loadBool:true,
	},
	methods:{
		//初始化
		init:function(){
//			status=>1:可使用；2：已使用；3：已过期
			if(coupon != -1){
				$('.list').css('display','block');
				$('.no_data').css('display','none');
				vm.couponMes = coupon;
			}else{
				$('.list').css('display','none');
				$('.no_data').css('display','block');
			}
//			var coupon = [{
//				'money':10,'full_money':'99','date_begin':'2017.12.25','date_end':'2017.12.28','status':1},{
//				'money':10,'full_money':'99','date_begin':'2017.12.25','date_end':'2017.12.28','status':1},{
//				'money':10,'full_money':'99','date_begin':'2017.12.25','date_end':'2017.12.28','status':1},{
//				'money':10,'full_money':'99','date_begin':'2017.12.25','date_end':'2017.12.28','status':1},{
//				'money':10,'full_money':'99','date_begin':'2017.12.25','date_end':'2017.12.28','status':1},{
//				'money':10,'full_money':'99','date_begin':'2017.12.25','date_end':'2017.12.28','status':1},{
//				'money':10,'full_money':'99','date_begin':'2017.12.25','date_end':'2017.12.28','status':2}];
////				coupon=[];
//			if(coupon.length){//有数据
//				$('.list').css('display','block');
//				$('.no_data').css('display','none');
//				vm.couponMes = coupon;
//			}else{//无数据
//				$('.list').css('display','none');
//				$('.no_data').css('display','block');
//			}
		},
		//点击选择
		choice:function(index){
			//loading
			$('body').append('<div class="loading"><div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>');
			vm.oIndex = index;
			//alert(index);
			$(document).scrollTop('0');

			//上拉加载数据初始化
			//vm.loadBool = true;
			//$('.end').remove();
			//count = 0;	//模拟数据   上拉加载次数初始化
			$.ajax({
				url:"/index.php/Home/Person/MyCouponAjax",
				type:'post',
				data:{state:index},
				success:function(data){
					if(data['state'] == -1){
						alert(data['msg']);
					}else if(data['state'] == -2){
						$('.list').css('display','none');
						$('.no_data').css('display','block');
					}else{
						$('.list').css('display','block');
						$('.no_data').css('display','none');
						vm.couponMes = data['data'];
					}
				}
			})
			//相当于ajax的success 开始
			//var coupon = [{
			//	'money':10,'full_money':'99','date_begin':'2017.12.25','date_end':'2017.12.28','status':1}];
			//
			//if(coupon.length){//有数据
			//	$('.list').css('display','block');
			//	$('.no_data').css('display','none');
			//	vm.couponMes = coupon;
			//}else{//无数据
			//	$('.list').css('display','none');
			//	$('.no_data').css('display','block');
			//}
			//删除loading
			$('.loading').remove();
			//相当于ajax的success 结束
		},
	},
});

vm.init();