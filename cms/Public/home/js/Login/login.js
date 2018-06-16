var vm = new Vue({
	el:'#control',
	data:{
		//手机号
		phone:null,
		//密码框的type
		input_type:'password',
		//密码是否可见
		isEye:false,
		//登录按钮
		login_btn:false,
		//正则检测==》true：全对，false：错
		bool:true,
		photo:"/Public/home/images/login.png",
	},
	methods:{
		//判断登录按钮是否生效
		change:function(){
			var This = this;
			$('.input_box').bind('input propertychange', function() {
    			if(This.phone!=null && This.phone!='' && getPhoneBool(This.phone) && $("#pwd").val()!='' && $("#pwd").val() != null){
					This.login_btn = true;
				}
    			else{
    				This.login_btn = false;
    			}
			}); 
			
		},
		//眼睛开合
		changeEye:function(){
			if(!this.isEye){
				this.isEye = true;
				this.input_type  = 'text';
			}
			else{
				this.isEye = false;
				this.input_type = 'password';
			}
		},
		//格式正则检测
		check:function(){
			if(this.phone=='' ||this.phone == null){
				mui.toast('账号不能为空！',{ duration:'short', type:'div' });
				this.bool=false;			
			}
			else if(!getPhoneBool(this.phone)){
				mui.toast('请输入正确的手机号码！',{ duration:'short', type:'div' });
				this.bool=false;	
			}
			else if($("#pwd").val()=='' ||$("#pwd").val() == null){
				mui.toast('密码不能为空',{ duration:'short', type:'div' });
				this.bool=false;	
			}
			else if(!getPassBool($("#pwd").val())){
				mui.toast('密码错误',{ duration:'short', type:'div' });
				this.bool=false;	
			}
			else{
				this.bool=true;	
			}
		},
		//登录
		login:function(){
			//选项不为空时 登录按钮启用
			if(this.login_btn){
				//检测密码 手机号格式
				this.check();
				
				if(this.bool){
					$.ajax({

						data: {phone: vm.phone, password: $("#pwd").val(), url: json_url},

						type: 'post',

						url: '/index.php/Home/User/Login',

						success: function(msg) {
							console.log(msg);
							if (msg.code == 1) { // 登录成功
								mui.toast(msg.msg);
								location.href = msg.url; // 跳转
							} else {
								mui.toast(msg.msg);
							}
						}
					});
				}
			}
		},
	},
});
 
// 验证手机正则返回的布尔值
function getPhoneBool(obj){
    var  pattern = /^1[345789][\d]{9}$/;
    var bool = pattern.test(obj);
    return bool;
}
// 验证密码正则返回的布尔值
function getPassBool(obj){
    var  pattern = /^(?=.*[0-9].*)(?=.*[A-Za-z].*).{6,16}$/;
    var bool = pattern.test(obj);
    return bool;
}
 
