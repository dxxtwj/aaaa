console.log(addressInfoJson);
//地址信息数组 isDefaultLoc->true为默认地址 false就不是
// var locArr = [{"name":"龙虾","phone":"18718425825","loc":"广东省佛山市南海桂城天安数码城三期714","isDefaultLoc":true},
// {"name":"成贝贝","phone":"18718425825","loc":"广东省佛山市南海桂城天安数码城三期714","isDefaultLoc":false},
// {"name":"张先生","phone":"18718425825","loc":"广东省佛山市南海桂城天安数码城三期714广东省佛山市","isDefaultLoc":false}];
//locArr=[];
var locArr = new Array();
for (var i in addressInfoJson) {
	if (addressInfoJson[i].isDefault == 1) { //默认地址

		addressInfoJson[i].isDefault = true;
	} else {// 非默认地址

		addressInfoJson[i].isDefault = false;
	}
	locArr.push({"name": addressInfoJson[i].name, "phone": addressInfoJson[i].phone, "loc": addressInfoJson[i].province + addressInfoJson[i].city + addressInfoJson[i].county, "isDefaultLoc": addressInfoJson[i].isDefault, "id": addressInfoJson[i].id});
}

var vm = new Vue({
	el:"#control",
	data:{
		//地址信息数组
		oLoc:locArr||[],
		//删除地址下标标志
		removeIndex:null,
	},
	methods:{
		//选择默认地址
		editBtn:function(index){
//			for(var i=0;i<vm.oLoc.length;i++){
//				vm.oLoc[i].isDefaultLoc = false;
//			}
//			vm.oLoc[index].isDefaultLoc = true;
			
			$('.edit-btn i')
			.removeClass('icon-gouxuankuang')
			.addClass('icon-yuansu_gouxuankuang_yigouxuan')
			.eq(index)
			.removeClass('icon-yuansu_gouxuankuang_yigouxuan')
			.addClass('icon-gouxuankuang');
			
		},
		//删除地址
		editRemove:function(index){
			mui.confirm("确定删除地址？",function(e){
				if(e.index){
					
					console.log(vm.oLoc[index].id);
					$.ajax({ // 点击确定删除地址触发

						data: {id: vm.oLoc[index].id},

						type: 'post',

						url: '/index.php/Home/Address/addressDel',

						success: function(msg) {
							mui.toast(msg.msg);
							vm.oLoc.splice(index,1);
						}
					});
				}
			})
			vm.removeIndex = index;
		},
		//编辑地址
		editAlter:function(index){
			console.log(index);
			console.log(vm.oLoc[index].id); // 获取id写法
			window.location.href='/index.php/Home/Address/change_address/id/'+vm.oLoc[index].id;
		},
		//选择地址
		locChoose:function(index){
			$('.radio').removeClass('radioChoose');
			var $that = $('.radio').eq(index);
			$that.addClass('radioChoose');

			// alert(vm.oLoc[index].id);
			$.ajax({
				// vm.oLoc[index].id 为地址ID，即选择此地址作为本次订单的收货地址
				data: {addressId: vm.oLoc[index].id},

				type: 'post',

				url: '/index.php/Home/Order/putAddressToSession',

				success: function(msg) {
					console.log(msg);

					if (msg.code == 1) {
						mui.toast(msg.msg);
						location.href = msg.url;
					} else {
						mui.toast(msg.msg);
					}
				}
			});
		},
	}
});