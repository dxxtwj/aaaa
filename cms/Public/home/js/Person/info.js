//photo:头像，name:昵称，sex:性别，birthday：出生日期
var userinfo = JSON.parse(info);
if(userinfo['UI_ImgUrl'] != -1){
	var u_photo = userinfo['UI_ImgUrl'];
}else{
	var u_photo = '/Public/home/images/login.png';
}
if(userinfo['UI_Address'] != -1){
	var u_address = userinfo['UI_Address'];
}else{
	var u_address = '';
}
if(userinfo['UI_Birthday'] != -1){
	var u_birth = userinfo['UI_Birthday'];
}else{
	var u_birth = '1998-10-25';
}
if(userinfo['UI_Sex'] == 1){
	var u_sex = '男';
}else{
	var u_sex = '女';
}
var areaLoc={'photo':u_photo,'name':userinfo['UI_Name'],'sex':u_sex,'birthday':u_birth,'loc':u_address};
console.log(userinfo);
var vm = new Vue({
	el:'#control',
	data:{
		//默认头像
		defPhoto:'/Public/home/images/default_photo.png',
		//用户信息
		areaLoc:areaLoc,
		//修改昵称
		name:'',
		//遮罩
		mask:null,
	},
	methods:{
		//初始化
		init:function(){
			vm.mask = mui.createMask(function(){
				$('.edit-name').hide();
			});
		},
		//修改昵称
		editName:function(){
			vm.name='';
			$('.edit-name').show();
			vm.mask.show();
		},
		//关闭修改昵称
		closeName:function(){
			$('.edit-name').hide();
			vm.mask.close();
		},
		//确定修改昵称
		sureName:function(){
			if(vm.name == ''||vm.name == null){
				mui.toast('请输入昵称！',{ duration:'short', type:'div' });
			}else{
				vm.areaLoc.name = vm.name;
				$('.edit-name').hide();
				vm.mask.close();
			}
		},
		//选择性别
		sexPick:function(){
			var roadPick = new mui.PopPicker();//获取弹出列表组建，假如是二联则在括号里面加入{layer:2}
                roadPick.setData([{//设置弹出列表的信息，假如是二联，还需要一个children属性
                    value: "男",
                    text: "男"
                }, {
                    value: "女",
                    text: "女"
                }]);
            roadPick.pickers[0].setSelectedValue(vm.areaLoc.sex, 300);
			roadPick.show(function(item) {//弹出列表并在里面写业务代码
				var itemCallback=roadPick.getSelectedItems();  
				vm.areaLoc.sex = itemCallback[0].text;
			});
		},
		//选择出生日期
		birPick:function(){
			var dtpicker = new mui.DtPicker({
			    "type": "date",
			    "beginDate": new Date(1880, 01, 01),//设置开始日期
			    "value": vm.areaLoc.birthday,
			})
			
			dtpicker.show(function(e) { 
				vm.areaLoc.birthday = e.text;
			}) 
		},
		//修改
		edit:function(){
			vm.areaLoc.loc = $('#shengshi').val();
			console.log(vm.areaLoc.loc+'&&'+vm.areaLoc.name+'&&'+vm.areaLoc.birthday+'&&'+vm.areaLoc.sex);
			//创建空对象方式
			var formData = new FormData();

			formData.append('photo',$("#imgfile")[0].files[0]);//头像
			formData.append('name',vm.areaLoc.name);//名字
			formData.append('sex',vm.areaLoc.sex);//性别
			formData.append('birthday',vm.areaLoc.birthday);//出生日期
			formData.append('address',vm.areaLoc.loc);

			$.ajax({
				url: '/index.php/Home/Person/info',
				type: 'POST',
				data: formData,
				async: false,
				cache: false,
				contentType: false,
				processData: false,
				success: function (returndata) {
					//console.log(returndata);
					if(returndata == 1){
						mui.alert('修改成功',function(){
							window.location.href="/index.php/Home/Person/person";
						});
					}else{
						mui.alert('无修改',function(){
							window.location.reload();
						});

					}


				},
				error: function (returndata) {
					// alert(returndata);
				}
			});
		},
	},
});
vm.init();


//选择地区返回
function back(){
//	$('.back-loc').css('display','none');
//	$('.dqld_div').css('display','none');
	$('.dqld_div,.back-loc').hide();
	$('.up-loc').show();
}
