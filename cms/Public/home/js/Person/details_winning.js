function isWeiXin(){

    var ua = window.navigator.userAgent.toLowerCase();

    if(ua.match(/MicroMessenger/i) == 'micromessenger'){

        return true;

    }else{

        return false;

    }

}

var oy_info = JSON.parse(oy_info_json);
var result = JSON.parse(result_json);

// console.log(result);
var vm = new Vue({
	el:'#control',
	data:{
		//中奖详细信息
		winningMes : {},
		//物流详细信息
		addMes:[],
	},
	methods:{
		//初始化
		init:function(){
			//loading
//			$('body').append('<div class="loading"><div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>');
			
			
			//相当于ajax的success 开始
			//gId：商品id，img：商品图片，title：商品标题，price：商品价格，join_date：参与日期时间，periods：参与期数，orderNum：抽奖序号，status：状态（未开奖、未中奖、已中奖），open_date：开奖日期时间，num：中奖号码，name：收货人，tel：手机号，loc：地址，nineStatus:1(1:未九折购买 ；2：已九折购买)，goods_status：货物状态（1：未发货；2：确认收货，3：已收货），share：1（1:未晒单；2：已晒单）
			var winning ={
				'id':oy_info['OY_ID'],
				'gid':oy_info['OY_GID'],
				'img':oy_info['GR_IMG'],
				'title':oy_info['GR_Name'],
				'price':oy_info['OY_GoodTotal'],
				'join_date':oy_info['OY_AddTime'],
				'periods':oy_info['OY_Qishu'],
				'orderNum':oy_info['OY_Number'],
				'status':oy_info['OY_Statename'],
				'open_date':oy_info['OY_EndTime'],
				'num':oy_info['OY_LuckNum'],
				'tobuy':oy_info['is2buy'],
				'nineStatus':oy_info['is2ninebuy'],
				'orderState':oy_info['OY_OrderState'],
				'isninebuy':oy_info['OY_Nine_Buy'],
				'state':oy_info['OY_State'],
				'share':oy_info['share'],
				'name':oy_info['OY_Link'],
				'tel':oy_info['OY_Phone'],
				'loc':oy_info['OY_Province']+' '+oy_info['OY_City']+' '+oy_info['OY_County']+' '+oy_info['OY_Detail'],
				"href":"/index.php/Home/LuckBuy/goods_detail/gid/"+oy_info['OY_GID']
			};
			
			//物流信息 时间越近越排在数组前面
			var address = new Array();
			if(result!=null){
				
				for(var i in result['data']){
					address.push({
						'title':result['data'][i]['location']+' '+result['data'][i]['context'],
						'time':result['data'][i]['ftime']
					});
				}
			}
			// console.log(address);
//			address = [];
			
			this.winningMes = winning;
			// this.choice();

			//无物流信息
			if(address.length == 0){
				$('.no_notice').css("display","block");
			}
			//有物流信息
			else{
				// alert('fffff');
				$('.no_notice').css("display","none");
				vm.addMes = address;
			}
			
			$('.loading').remove();
			//相当于ajax的success 结束
			
		},
		//提示和操作选择
//		choice:function(){
//			//未收货
//			if(vm.winningMes.goods_status == '1'){
//				//未中奖、已九折购买
//				if(vm.winningMes.status=='未中奖' && vm.winningMes.nineStatus == 2){
//					$('.note_l').html('未发货');
//					$('.note_r').html('<span class="plan">商家正在为您安排发货！<span>');
//				}
//				//未中奖、未九折购买
//				else if(vm.winningMes.status=='未中奖' && vm.winningMes.nineStatus == 1){
//					var oldtime = new Date(vm.winningMes.join_date).getTime();
//					var newtime = new Date().getTime();
//					var day=Math.floor((newtime-oldtime)/1000/24/3600);
//					
//					if(day>1){	//已过24h
//						$('.note_l').html('<span class="nine_pass">已超过24小时期限<span>');
//						$('.note_r').html('<button type="button" class="btn_invalid">9折购买</button>');
//					}else{	//未过24h
//						$('.note_l').html('');
//						$('.note_r').html('<button type="button" class="mui-btn btn-red mui-btn-danger" onclick="buy()">9折购买</button>');
//					}
//				}
//				//已中奖
//				else if(vm.winningMes.status=='已中奖'){
//					$('.note_l').html('未发货');
//					$('.note_r').html('<span class="plan">商家正在为您安排发货！<span>');
//				}
//			}
//			//确认收货
//			else if(vm.winningMes.goods_status == '2'){
//				$('.note_l').html('');
//				$('.note_r').html('<button type="button" class="mui-btn btn-red mui-btn-danger" onclick="sure()">确认收货</button>');
//			}
//			//已收货 如果是中奖 则有晒单，九折没有
//			else if(vm.winningMes.goods_status == '3' && vm.winningMes.status == '已中奖'){
//				//未晒单
//				if(vm.winningMes.share == '1'){
//					$('.note_l').html('');
//					$('.note_r').html('<button type="button" class="mui-btn btn-red mui-btn-danger" onclick="showPic()">我要晒单</button>');
//				}
//				else if(vm.winningMes.share == '2'){
//					$('.note_r').html('<span class="is_show">已晒单<span>');
//				}
//			}
//		},
		
		buy:function(id){

			mui.confirm('是否购买','提示',['确定', '取消'],function(e){

				if(e.index == 0){

					
					var This = this;

                    var URL = "/index.php/Home/Pay/pay";

                    var temp = document.createElement("form");

                    temp.action = URL;

                    temp.method = "post";

                    temp.style.display = "none";

                    var PARAMS = new Array();

                    PARAMS['oid'] = id;

                    PARAMS['type'] = 2;

                    if(isWeiXin()){

                        PARAMS['isWeiXin'] = 1;

                    }else{

                        PARAMS['isWeiXin'] = 0;

                    }

                    for (var x in PARAMS) {

                        var opt = document.createElement("textarea");

                        opt.name = x;

                        opt.value = PARAMS[x];

                        // alert(opt.name)

                        temp.appendChild(opt);

                    }

                    document.body.appendChild(temp);

                    temp.submit();

                    return temp;
					

				}

			});

		},
		//九折购买

		ninebuy:function(id){

			mui.confirm('是否九折购买','提示',['确定', '取消'],function(e){

				if(e.index == 0){

					// mui.alert('购买成功'+id);
					$.ajax({
						url:'/index.php/Home/Person/ordernine',
						type:'post',
						data:{oid:id},
						success:function(data){
							if(data == 1){
								var This = this;

		                        var URL = "/index.php/Home/Pay/pay";

		                        var temp = document.createElement("form");

		                        temp.action = URL;

		                        temp.method = "post";

		                        temp.style.display = "none";

		                        var PARAMS = new Array();

		                        PARAMS['oid'] = id;

		                        PARAMS['type'] = 2;

		                        if(isWeiXin()){

		                            PARAMS['isWeiXin'] = 1;

		                        }else{

		                            PARAMS['isWeiXin'] = 0;

		                        }

		                        for (var x in PARAMS) {

		                            var opt = document.createElement("textarea");

		                            opt.name = x;

		                            opt.value = PARAMS[x];

		                            // alert(opt.name)

		                            temp.appendChild(opt);

		                        }

		                        document.body.appendChild(temp);

		                        temp.submit();

		                        return temp;
							}else{
								mui.alert('操作失败');
							}
						}
					});

				}

			});

		},
		comment:function(id){
			window.location.href="/index.php/Home/LuckBuy/share/oid/"+id;
		},
		confirmOrder:function(id){
			mui.confirm('是否确认收货','提示',['确定', '取消'],function(e){

				if(e.index == 0){

					// mui.alert('购买成功'+id);
					$.ajax({
						url:'/index.php/Home/Person/confirm_order',
						type:'post',
						data:{oid:id},
						success:function(data){
							if(data == 1){
								mui.alert('收货成功',function(){
									window.location.reload();
								});
							}else{
								mui.alert('收货失败',function(){
									window.location.reload();
								});
							}
						}
					});

				}

			});
		}


	},
});
vm.init();
