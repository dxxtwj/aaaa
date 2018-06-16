$('body').append('<ul class="floating">\n            <li id="tg">\n                <span class="iconfont icon-zhuan"></span>\n            </li>\n            <li>\n                <a href="/index.php/Home/Cart/index"><span class="iconfont icon-gouwuche1"><i class="floating_i"></i></span></a>\n            </li>\n            <li id="toTop">\n                <span class="iconfont icon-huidaodingbu"></span>\n            </li>\n        </ul>');
$("#toTop").on("click",function(){
  $('body,html').animate({scrollTop:0},200);
});
//购物车有数据
$.ajax({
    url:'/index.php/Home/Cart/is_hasData',
    type:'post',
    data:{},
    success:function(data){
    	if(data == 1){
			$(".floating_i").addClass('hasdata');
    	}else if(data == 0){

    	}
	}
});
//点击赚弹出推广框
var flag = 0;
var ran = Math.random()*10;
$('#tg').on('click',function(){
	flag = 1;
	$('.mui-content').append('<div id="mk" style="position: fixed; top:0;left: 0;right: 0;bottom: 0; background: #000;opacity:0;z-index:999;"></div>');
	var html = '';
	html+='<div class="zoom">';
	html+='<div class="zoom_main">';
	html+='<p class="title">点击以下二维码，获取推广素材</p>';
	html+='<div class="gengxin_box"><i class="iconfont icon-gengxin1"></i></div>';
	html+='<div class="main_box">';
	html+='<div class="options">';
	html+='<a href="/index.php/Home/Index/index?'+ran+'"><div class="img">';
	html+='<img src="/Public/home/images/service_QR.jpg">';
	html+='<div class="point"><img src="/Public/home/images/point.png"></div>';
	html+='</div></a>';
	html+='<p>获取推广素材</p>';
	html+='<button type="button" class="mui-btn btn-green mui-btn-green toIndex">我的小店</button>';
	html+='</div>';
	html+='<div class="options">';
	html+='<a href="/index.php/Home/Twitter/index"><div class="img">';
	html+='<img src="/Public/home/images/service_QR.jpg">';
	html+='<div class="point"><img src="/Public/home/images/point.png"></div>';
	html+='</div></a>';
	html+='<p>获取关注素材</p>';
	html+='<button type="button" class="mui-btn btn-red mui-btn-danger toTweeter">推客中心</button>';
	html+='</div>';
	html+='</div>';
	html+='</div>';
	html+='<button type="button" class="mui-btn btn-cancel">取消</button>';
	html+='</div>';
	$('.mui-content').append(html);
		
		$('#mk').animate({opacity:0.7},300)
			.on(
				'click',function(){
					$(this).css('opacity','0');
					$(this).remove();
					$('.zoom').remove();
					flag = 0;
				}
			);
			$('.mui-content').on('touchmove',function(e){
				if(flag == 1){
					e.preventDefault();
				}
			});
			//取消
			$('.btn-cancel').on('click',function(){
				$('#mk').remove();
				$('.zoom').remove();
				flag = 0;
			});
			//更新
			$('.gengxin_box').on('click',function(){
				//模拟更新
				setTimeout(function(){		
					mui.toast('更新成功！',{ duration:'short', type:'div' });
				},1000);
			});
			$('.toIndex').on('click',function(){
				window.location.href = '/index.php/Home/Index/index?'+ran;
			})
			$('.toTweeter').on('click',function(){
				window.location.href = '/index.php/Home/Twitter/index';
			})
			
});
// window.onscroll = function(){ //写在对应页面的JS里面
//     if($(document).scrollTop() > $(window).height()/2){
//         $("#toTop").show(50);
//     }else{
//         $("#toTop").hide(50);
//     }
// }