//点击选项卡
function choice(i){
	$(document).scrollTop('0');
	alert(i);
	//改变颜色
	$('.mui-control-item').removeClass('mui-active');
	$('.mui-control-item').eq(i).addClass('mui-active');
	
	//有数据
	if(true){
		$('.list').css('display','block');
		$('.no_data').css('display','none');
	}
	//无数据
	else{
		$('.list').css('display','none');
		$('.no_data').css('display','block');		
	}
}

//初始化
choice(0);
