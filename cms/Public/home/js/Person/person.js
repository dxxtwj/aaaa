$('.loading').remove();
console.log(userList_json);
//有数据
// var mes = {
// 	"perImg":"/Public/home/images/login.png",	//头像
// 	"perName":'花无缺与小鱼儿',		//名字
// 	'uId':11302890,
// 	'unpaid_num':0,	//未付款订单数
// 	'wfh_num':1,		//未发货订单数
// 	'dsh_num':0,		//待收货订单数
// 	'comment_num':1,		//待评价订单数
// 	'cart_num':0,	//购物车数量
// 	'coupon_num':2,	//优惠券数量
// };
if (userList_json.unpaid_num != null) {
	
	if (userList_json.unpaid_num == 0) { // 未付款订单数
		
		$('#daiFuKuan').css('display', 'none');
	}
	if (userList_json.wfh_num == 0) { // 未发货订单数
		$('#daiFaHua').css('display', 'none');
		
	}
	if (userList_json.dsh_num == 0) { // 待收货订单数

		$('#daiShouHuo').css('display', 'none');
		
	}
	if (userList_json.comment_num == 0) { // 待评价订单数
		
		$('#daiPingJia').css('display', 'none');
	}
	if (userList_json.cart_num == 0) { // 购物车数量
		
		$('#cartNum').css('display', 'none');
	}
	if (userList_json.coupon_num == 0) { // 优惠券数量
		
		$('#youHuiQuan').css('display', 'none');
	}
	if (userList_json.userInfo.perImg == null) {//头像

		userList_json.userInfo.perImg = '/Uploads/defaultimg/defaultimg.png';
	}
}

if (userList_json != 0) { // 用户登录了

	var mes = {
		"perImg": userList_json.userInfo.perImg,
		"perName": userList_json.userInfo.perName,
		"uId": userList_json.userInfo.uId,
		"unpaid_num": userList_json.unpaid_num,
		"wfh_num": userList_json.wfh_num,
		"dsh_num": userList_json.dsh_num,
		"comment_num": userList_json.comment_num,
		"cart_num": userList_json.cart_num,
		"coupon_num": userList_json.coupon_num,
	}

} else { // 用户没有登录
	
	mes = {"perImg":"/Public/home/images/login.png",}	//头像;
}

//mes={};
var vm = new Vue({
	el:'#control',
	data:{
		perMes:{"perImg":"/Public/home/images/login.png"},
	},
	methods:{
		//初始化
		init:function(){
			if(true){	//已登录
				vm.perMes = mes;
			}else{	//未登录 mes未空
				vm.mes = [];
				return;
			}
		},
		//退出登录
		quit:function(){
			$.ajax({

				type: 'get',

				url: '/index.php/Home/User/logout',

				success: function(msg) {

					if (msg == 1) {

						mui.toast('已退出该账号');
					} else {
						
						mui.toast('未能成功退出登录');
					}

				}
			})
			vm.perMes={"perImg":"/Public/home/images/login.png"};
		}
		
	},
});
vm.init();

