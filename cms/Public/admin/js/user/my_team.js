var team = JSON.parse(info);
console.log(team[1][0]['img']);
$('.team_num').html('我的团队（'+team[0]['teamNum']+'）');
$('.guang').html('逛客（'+team[0]['touristsNum']+'）');
$('.chuang').html('创客（'+team[0]['makerNum']+'）');
$('.manager').html('市场经理（'+team[0]['managerNum']+'）');
$('.quyu').html('区域合伙人（'+team[0]['partnerNum']+'）');

if(team[1] != -1){
	$('.list').css('display','block');
	$('.no_data').css('display','none');
		var list = '';
	for(var i in team){
		console.log(i);
			list += '<li>'
			list += '<div class="img">'
			list += '<img src="'+team[1][i].img+'" />'
			list += '</div>'
			list += '<div class="mes">'
			list += '<p>'+team[1][i]["name"]+'</p>'
			list += '<p>加入时间：<span>'+team[1][i]["joinTime"]+'</span></p>'
			list += '</div>'
			list += '</li>'
	}
		$('.list').html(list);

}else{
	$('.list').css('display','none');
	$('.no_data').css('display','block');	
}
function GetQueryString(name){
	var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
	var r = window.location.search.substr(1).match(reg);
	if(r!=null)return  decodeURIComponent(r[2]); return null;

}
var user_id=GetQueryString("uid");
// alert(user_id);
//点击选项卡
function choice(i){
	$(document).scrollTop('0');
	// alert(i);
	//改变颜色
	$('.mui-control-item').removeClass('mui-active');
	$('.mui-control-item').eq(i).addClass('mui-active');
	$.ajax({
		url:'/index.php/Admin/User/my_team',
		type:'post',
		data:{state:i,uid:user_id},
		success:function(data){
			if(data[1] != -1){
				$('.list').css('display','block');
				$('.no_data').css('display','none');
					var list = '';
				for(var i in data){
						list += '<li>'
						list += '<div class="img">'
						list += '<img src="'+data[1][i]['img']+'" />'
						list += '</div>'
						list += '<div class="mes">'
						list += '<p>'+data[1][i]['name']+'</p>'
						list += '<p>加入时间：<span>'+data[1][i]['joinTime']+'</span></p>'
						list += '</div>'
						list += '</li>'
				}
				$('.list').html(list);


			}else{
				$('.list').css('display','none');
				$('.no_data').css('display','block');	
			}
		}
	})
	//有成员时
	if(true){
		$('.list').css('display','block');
		$('.no_data').css('display','none');	
	}else{
		$('.list').css('display','none');
		$('.no_data').css('display','block');	

	}
}

//初始化
choice(0);
