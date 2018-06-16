var vm = new Vue({
	el:'#control',
	data:{
		//第一页
		//手机号
		phone:null,
		//验证码
		note:null,
		//正则检测==》true：对，false：错
		bool:true,
		//下一步按钮
		next_btn:false,
		//完成按钮
		finish_btn:false,
		//验证码发送框文本
		sendMes:'发送验证码',
		//输入的图形验证码
		verifyNum:'',
		//手机是否注册过==》 true:已注册，false：未注册
		isReg:true,
		
		//第二页
		//密码==》密码框的type
		input_type1:'password',
		//密码是否可见
		isEye1:false,
		//确认密码==》密码框的type
		input_type2:'password',
		//确认密码是否可见
		isEye2:false,
	},
	methods:{
		//第一页
		//input框失去焦点 检测是否改变按钮颜色
		change:function(){
			var This = this;
			$('.page1 .input_box').bind('input propertychange', function() {
    			if(This.phone!=null && This.phone!='' && This.note!=null && This.note!=''){
					This.next_btn = true;
				}
    			else{
    				This.next_btn = false;
    			}
			}); 
		},
		//检测手机号格式
		checkPhone:function(){
			if(this.phone =='' || this.phone == null){
				mui.toast('手机号码不能为空！',{ duration:'short', type:'div' });
				this.bool=false;
           	}
			else if(!getPhoneBool(this.phone)){
				mui.toast('请输入正确的手机号！',{ duration:'short', type:'div' });
				this.bool=false;
			}
			else{
				this.bool = true;
			}
		},
		//发送验证码
		send:function(){
			var that = $(this);
			//检测手机号格式
			this.checkPhone();
			if(this.bool){
				this.verifyNum = '';
				$('.stage').css('display','block');
			}
		},
        // 输入验证码提交
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
//                   $.ajax({
//                  	type:"post",
//                  	url:"",
//                  	data:{},
//                  	success:function(){
//                  		
//                  	}
//                  });
        },
		//下一步
		next:function(){
			//按钮生效
			
			if(this.next_btn){
				this.checkPhone();
				if(this.bool){
					$.ajax({

						data: {note: vm.note, phone: vm.phone},

						url: '/index.php/Home/User/forget',

						type: 'post',

						success: function(msg) {

							if (msg.code == 1) {

								$('.page1').addClass('hidden');
								$('.page2').removeClass('hidden');

							} else {

								mui.toast(msg.msg,{ duration:'short', type:'div' });

							}
						}

					});
					
				}
			}
			
		},
		//第二页
		//完成按钮改变颜色
		change2:function(){
			var This = this;
			$('.page2 .input_box').bind('input propertychange', function() {
    			if($('#pwd1').val()!=null && $('#pwd1').val()!='' && $('#pwd2').val()!='' && $('#pwd2').val()!=null){
					This.finish_btn = true;
				}
    			else{
    				This.finish_btn = false;
    			}
			}); 
		},
		//格式正则检测
		check:function(){
			if($("#pwd1").val()=='' ||$("#pwd1").val() == null){
				mui.toast('密码不能为空',{ duration:'short', type:'div' });
				this.bool=false;	
			}
			else if(!getPassBool($("#pwd1").val())){
				mui.toast('密码格式错误',{ duration:'short', type:'div' });
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
		//改变眼睛状态
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
		
		//完成
		finish:function(){
			//完成按钮生效
			if(this.finish_btn){
				this.check();
				//密码格式正确
				if(this.bool){
					// alert('finish');
					// window.location.href="Login.html";

					$.ajax({

						data: {phone: vm.phone, password1: $("#pwd1").val(), password2: $("#pwd2").val()},

						url: '/index.php/Home/User/finish',

						type: 'post',

						success: function(msg) {

							if (msg.code == 1) {

								mui.toast('修改密码成功');
								location.href = '/index.php/Home/User/Login';
							} else {

								mui.toast('修改密码失败');
						
							}
						}

					});

				}
				else{
					return false;
				}
			}
			
		}		
	},
});
 

// 验证手机正则返回的布尔值
function getPhoneBool(obj){
    var  pattern = /^1[34578][\d]{9}$/;
    var bool = pattern.test(obj);
    return bool;
}
// 验证密码正则返回的布尔值
function getPassBool(obj){
    var  pattern = /^(?=.*[0-9].*)(?=.*[A-Za-z].*).{6,20}$/;
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
            // 忘记密码短信接口
        	$.ajax({

        		data: {phone: vm.phone, state: 2},

        		type: 'post',

        		url: '/index.php/Home/User/send_phone',

        		success: function(msg) {
        			if (msg != 1) {

             			alert('发送手机号失败');

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
            vm.pass();
        },300);
			
        }else{
            oBtn.style.left = 0;
            oTrack.style.width= 0;
        }
    },false);