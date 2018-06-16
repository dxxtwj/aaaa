var vm = new Vue({
	el:'#control',
	data:{
		name:null,//用户名
		//手机号
		phone:null,
		//验证码
		note:null,
		//密码==》密码框的type
		input_type1:'password',
		//密码是否可见
		isEye1:false,
		//确认密码==》密码框的type
		input_type2:'password',
		//确认密码是否可见
		isEye2:false,
		//登录按钮
		register_btn:false,
		//正则检测==》true：对，false：错
		bool:true,
		//输入的图形验证码
		verifyNum:null,
		//验证码按钮文字
        sendMes:'发送验证码',
        //省
        oProvince:null,
        //市
        oCity:null,
        //区
        oArea:null,
	},
	methods:{
		//input框失去焦点
		change:function(){
			var This = this;
			$('.input_box').bind('input propertychange', function() {
    			if(This.name!=null && This.name!='' && This.phone!=null && This.phone!='' && $("#pwd1").val()!='' && $("#pwd1").val() != null && $("#pwd2").val()!='' && $("#pwd2").val() != null && This.note!='' && This.note!=null){
					This.register_btn = true;
				}
    			else{
    				This.register_btn = false;
    			}
			}); 
			
		},

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
		//发送验证码
		send:function(){
			var that = $(this);
			//检测密码 手机号格式
			if(this.phone=='' ||this.phone == null){
				mui.toast('手机号不能为空！',{ duration:'short', type:'div' });
				this.bool=false;			
			}
			else if(!getPhoneBool(this.phone)){
				mui.toast('请输入正确的手机号码！',{ duration:'short', type:'div' });
				this.bool=false;	
			}
			else{
                this.verifyNum = '';
				$('.stage').css('display','block');
           }
		},
        // 验证通过发送验证码
        pass:function(){
            	$(".stage").css({"display":"none"});
        		mui.toast('验证码已发送，请稍后！',{ duration:'short', type:'div' });
                	var count=60;
                    var timer = null;
                    //设置button效果，开始计时
                    $('.send').attr("disabled", "true").addClass('active');
                    this.sendMes = "重新发送"+count+"s";
                    timer = setInterval(setTime, 1000); //启动计时器，1秒执行一次
                    //timer处理函数
                    function setTime() {
                        if (count == 0) {
                            clearInterval(timer);//停止计时器
                            $('.send').removeAttr("disabled");//启用按钮
                            $('.send').val("发送验证码").removeClass('active');
                        }
                        else {
                            count--;
                            $('.send').val("重新发送"+count+"s");
                        }
               		}
               	
           		
        },
        //格式正则检测
		check:function(){
			if(this.phone=='' ||this.phone == null){
				mui.toast('请输入手机号！',{ duration:'short', type:'div' });
				this.bool=false;			
			}
			else if(!getPhoneBool(this.phone)){
				mui.toast('请输入正确的手机号码！',{ duration:'short', type:'div' });
				this.bool=false;	
			}
			else if($("#pwd1").val()=='' ||$("#pwd1").val() == null){
				mui.toast('密码不能为空！',{ duration:'short', type:'div' });
				this.bool=false;	
			}
			else if(!getPassBool($("#pwd1").val())){
				mui.toast('密码格式错误！',{ duration:'short', type:'div' });
				this.bool=false;	
			}
			else if($("#pwd2").val()=='' ||$("#pwd2").val() == null){
				mui.toast('请再次输入您的登录密码！',{ duration:'short', type:'div' });
				this.bool=false;
			}
			else if($("#pwd1").val()!=$("#pwd2").val()){
				mui.toast('两次输入的密码不一致，请重新输入！',{ duration:'short', type:'div' });
				this.bool=false;	
			}
			else{
				this.bool=true;	
				
			}
		},
		//注册
		register:function(){
			//注册按钮启用
			if(this.register_btn){
				this.check();
				//选项不为空时 
				if(this.bool && this.note!='' && this.note!=null){
					$.ajax({

						data: {name: vm.name, phone: vm.phone, note: vm.note, pwd1: $("#pwd1").val(), pwd2: $("#pwd2").val()},

						type: 'post',

						url: '/index.php/Home/User/Register',

						success: function(msg) {

							if (msg.code == 1) {

								mui.toast(msg.msg);
								location.href = '/index.php/Home/Index/index'; // 注册并登录成功跳转到首页
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



//滑动验证
var oBtn = document.getElementById('btn');
    var oW,oLeft;
    var oSlider=document.getElementById('slider');
    var oTrack=document.getElementById('track');
    var oIcon=document.getElementById('icon');
    var oSpinner=document.getElementById('spinner');
	var flag=1;
 
    oBtn.addEventListener('touchstart',function(e){
		if(flag==1){
			console.log(e);
			var touches = e.touches[0];
			oW = touches.clientX - oBtn.offsetLeft;
			oBtn.className="button";
			oTrack.className="track";
		}
        
    },false);
 
    oBtn.addEventListener("touchmove", function(e) {
		if(flag==1){
			var touches = e.touches[0];
			oLeft = touches.clientX - oW;
			if(oLeft < 0) {
				oLeft = 0;
			}else if(oLeft > $('.stage').width() - oBtn.offsetWidth) {
				oLeft = ($('.stage').width() - oBtn.offsetWidth);
			}
			oBtn.style.left = oLeft + "px";
			oTrack.style.width=oLeft+ 'px';			
		}
        
    },false);
 
    oBtn.addEventListener("touchend",function() {
        if(oLeft>=(oSlider.clientWidth-oBtn.clientWidth)){
            //滑动验证成功
             $.ajax({

             	type: "post",

             	url: "/index.php/Home/User/send_phone",

             	
             	// state 1: 注册 2:忘记密码
             	data: {phone: phone.value, state: 1},

             	success: function (msg) {
             		
             		if (msg != 1) {

             			mui.toast('发送手机号失败');
             		}
             	}
             });

            oBtn.style.left = ($('.stage').width() - oBtn.offsetWidth-30);
            oTrack.style.width= ($('.stage').width() - oBtn.offsetWidth-30);
            oIcon.style.display='none';
            oSpinner.style.display='block';				
			flag=0;	
			
        oBtn.className="button-on";
        oTrack.className="track-on";
        //设置回初始值
        setTimeout(function(){
        	oBtn.style.left = 0;
            oTrack.style.width= 0;
            oBtn.className="button";
			oTrack.className="track";
			oIcon.style.display='block';
            oSpinner.style.display='none';
            flag=1;
            oW=0,oLeft=0;
            vm.pass()
        },300);
			
        }else{
            oBtn.style.left = 0;
            oTrack.style.width= 0;
        }
    },false);