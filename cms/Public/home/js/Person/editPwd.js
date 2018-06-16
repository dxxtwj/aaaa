var vm = new Vue({
	el:'#control',
	data:{
		//正则检测==》true：对，false：错
		bool:true,
		//新密码==》密码框的type
		input_type1:'password',
		//新密码是否可见
		isEye1:false,
		//确认密码==》密码框的type
		input_type2:'password',
		//确认密码是否可见
		isEye2:false,
	},
	methods:{
		//眼睛开合
		//密码--眼睛开合1
		changeEye1:function(){
			if(!this.isEye1){
				this.isEye1 = true;
				this.input_type1  = 'text';
			}
			else{
				this.isEye1 = false;
				this.input_type1 = 'password';
			}
		},
		//确认密码--眼睛开合2
		changeEye2:function(){
			if(!this.isEye2){
				this.isEye2 = true;
				this.input_type2  = 'text';
			}
			else{
				this.isEye2 = false;
				this.input_type2 = 'password';
			}
		},
		//修改
		deit:function(){
			//检测密码 手机号格式
			this.check();
			if(this.bool){
				

				$.ajax({

					data: {jiuPwd: $("#oldpwd").val(), pwd1:$("#pwd1").val() , pwd2:$("#pwd2").val()},

					type: 'post',

					url: '/index.php/Home/Person/editPwd',

					success: function(msg) {

						if(msg.code == 1) {
							mui.toast(msg.msg);
							location.href = msg.url;
						} else {
							mui.toast(msg.msg);
						}
					}


				});



			}
		},
		//格式正则检测
		check:function(){
			if($("#oldpwd").val()=='' ||$("#oldpwd").val() == null){
				mui.toast('请输入旧密码！',{ duration:'short', type:'div' });
				this.bool=false;	
			}
			else if($("#pwd1").val()=='' ||$("#pwd1").val() == null){
				mui.toast('请输入新密码！',{ duration:'short', type:'div' });
				this.bool=false;	
			}
			else if(!getPassBool($("#pwd1").val())){
				mui.toast('新密码格式错误',{ duration:'short', type:'div' });
				this.bool=false;	
			}
			else if($("#pwd1").val()!= $("#pwd2").val()){
				mui.toast('两次输入的新密码不一致，请重新输入！',{ duration:'short', type:'div' });
				this.bool=false;	
			}
			else{
				this.bool=true;	
			}
		},
	},
});

// 验证密码正则返回的布尔值
function getPassBool(obj){
    var  pattern = /^(?=.*[0-9].*)(?=.*[A-Za-z].*).{6,20}$/;
    var bool = pattern.test(obj);
    return bool;
}
