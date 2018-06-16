//新建收获地址信息
var areaLoc = {"name":"","phone":"","loc":"","address":""};

var vm = new Vue({
	el:"#control",
	data:{
		//新建收获地址信息
		areaLoc:areaLoc,
	},
	methods:{
		//保存
		changeSave:function(){
			if(vm.areaLoc.name.trim() == ''){
				 mui.toast('请输入收货人姓名！',{ duration:'short', type:'div' });
			}else if(vm.areaLoc.phone.trim() == ''){
				mui.toast('请输入收货人联系方式！',{ duration:'short', type:'div' });
			}else if(!/^1[34578][\d]{9}$/.test(vm.areaLoc.phone)){
				mui.toast('请输入正确的手机号！',{ duration:'short', type:'div' });
			}else if($('.address_input1').val() == ''){
				mui.toast('请选择地址！',{ duration:'short', type:'div' });
			}
			else if(vm.areaLoc.address.trim() == ''){
				mui.toast('请输入详细地址！',{ duration:'short', type:'div' });
			}
			else{
				// console.log(vm.areaLoc.name.trim(),vm.areaLoc.phone,vm.areaLoc.loc.trim(),vm.areaLoc.address.trim());
				// console.log($('.mui-switch').hasClass('mui-active'));//true 设为默认地址，false则没有
				// console.log("传递参数到后台 姓名->vm.areaLoc.name 手机->vm.areaLoc.phone 地址->vm.areaLoc.loc.trim() 详细地址->vm.areaLoc.address.trim()");

				$.ajax({ // 添加收货地址

					data: {name: vm.areaLoc.name, phone: vm.areaLoc.phone, address: vm.areaLoc.loc.trim(), addressDetail: vm.areaLoc.address.trim(), moRen: $('.mui-switch').hasClass('mui-active')},

					type: 'post',

					url: '/index.php/Home/Address/new_address',

					success: function(msg) {
						console.log(msg);
						if (msg.code == 1) { // 保存成功

							mui.toast(msg.msg);
							location.href = msg.url; // 跳转到显示订单页面

						} else {
							mui.toast(msg.msg);
						}
					}
				});

			}	
		},
		//选择地区返回
		backLoc:function(){
			$('.dqld_div,.back-loc').hide();
			$('.up-loc').show();
		}
	}
});

//setInterval(function(){
//console.log(vm.areaLoc.loc);
//},500);