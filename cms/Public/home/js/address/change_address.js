//修改收获地址信息
console.log(addressListJson);
// var areaLoc = {"name":"李大爷","phone":"13612619558","loc":"上海 上海市 卢湾区","address":"华东远村南美大街18号4栋809"};
var areaLoc = {"name": addressListJson.name, "phone": addressListJson.phone, "loc": addressListJson.province + ' ' + addressListJson.city + ' ' + addressListJson.county, "address": addressListJson.detail, "id": addressListJson.id};
var vm = new Vue({
	el:"#control",
	data:{
		//修改收获地址信息
		areaLoc:areaLoc,
	},
	methods:{		
		//返回上一个页面
		backFun:function(){
//			window.history.back(-1);
		},
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
				$.ajax({ // 点击保存修改过的地址触发

					data: {name: vm.areaLoc.name.trim(), phone: vm.areaLoc.phone, loc: vm.areaLoc.loc.trim(), detail: vm.areaLoc.address.trim(), default: $('.mui-switch').hasClass('mui-active'), id: addressListJson.id},

					type: 'post',

					url: '/index.php/Home/Address/change_address',

					success: function(msg) {
						console.log(msg);
						if (msg.code == 1) {

							mui.toast(msg.msg);
							setTimeout(function() { // 两秒后跳转

								location.href = msg.url;

							}, 2000)

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