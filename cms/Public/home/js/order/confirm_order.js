$('.loading').remove();
// console.log(cartDataJson);
// console.log(cartListJson);
// console.log(addressJson);
// console.log(youFeiJson);
console.log(manJianJson);
// console.log(couponArrayJson);

if (cartDataJson.code == 0) { // 判断地址是否存在错误
	mui.toast(cartDataJson.msg);
	// setTimeout(function() { // 两秒后跳转
		// location.href = cartDataJson.url;
	// }, 2000)
}


function isWeiXin(){ // 判断是不是微信
    var ua = window.navigator.userAgent.toLowerCase();
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
        return true;
    }else{
        return false;
    }
}
//订单信息数组
// var gowuche = [{"img":"/Public/home/images/goods_1.jpg","mes":"粉墨东西PinKo牛仔+棕色打底针织长裙套装韩版东大门","selected":'"灰色" "S"',"num":"2","price":"1.11","gid":"0"},{"img":"/Public/home/images/goods_1.jpg","mes":"蓝色性感露背公主新娘结婚敬酒服婚礼晚宴 年会拖尾婚纱礼服","selected":'',"num":"1","price":"2.22","gid":"1"}];
var gowuche =  new Array();
for (var i in cartListJson) {
	gowuche.push({"img": cartListJson[i].img, "mes": cartListJson[i].name, "selected": cartListJson[i].guiGe, "num": cartListJson[i].number, "price": cartListJson[i].price, "gid": cartListJson[i].gid, "oid": cartListJson[i].oid});
}

if (manJianJson.price != null) { // 满减价格
	manBool = true;
} else {
	manBool = false;

}

 //地址信息
// var oLoc = {"name":"龙虾","phone":"18718425825","loc":"广东省佛山市南海桂城天安数码城三期7147"};
if (addressJson.code != 0) {

	var oLoc = {"name": addressJson.link, "phone": addressJson.phone, "loc": addressJson.province + addressJson.city + addressJson.county + addressJson.detail, "id": addressJson.id};
} else {
	var oLoc = {};

}

//oLoc = {};//无收获地址
// 优惠券
// var oCoupon = [{'cid':'3','price':'5','text':'省5元:组合优惠'},
// {'cid':'2','price':'10','text':'省10元:组合优惠'},
// {'cid':'1','price':'0','text':'不使用优惠'}];
if (couponArrayJson != 0) {
// console.log(couponArrayJson);
	var oCoupon = new Array();
	for (var i in couponArrayJson) { // 优惠券
		oCoupon.push({"cid": couponArrayJson[i].id, "price": couponArrayJson[i].minus, "text": couponArrayJson[i].name+'：满'+couponArrayJson[i].man+'减'+couponArrayJson[i].minus});
	}
} else {
	var oCoupon = [{'cid':'1','price':'0','text':'暂无优惠券'}];
}

var manJian = manJianJson.full_recude;//减多少
var mane = manJianJson.full_money;//满多少
//oCoupon=[];
// 运费
// var takeMoney = '10.00';

var takeMoney = youFeiJson.weight_price;



var vm = new Vue({
	el:".mui-content",
	data:{
        mane:mane,
		//订单信息数组
		gowuche:gowuche||[],
		//地址信息对象
		oLoc:oLoc||{},
		oCoupon:oCoupon||[],
        takeMoney:takeMoney||'0.00',// 运费
        mes:'', //留言
        mask:null,
        couponString:'请选择',//所选优惠
        couponId:0,//所选优惠id
        couponPrice:0,//所选优惠券折扣
        oTop:0, //禁止滑动标志,
        manJian:manJian,
	},
	methods:{
		//初始化
		init:function(){
			vm.mask = mui.createMask(function(){
				$('.coupon').animate({'bottom':'-8.5rem'},300,function(){
					$(this).css('display','none');
//					$('body').css({'position':'fixed','top':0,'left':0,'right':0,'bottom':0,'margin':'auto'});
	                $('body').css('position','');
	                $(document).scrollTop(vm.oTop);
				});
			});
		},
        //点击确认订单
        confirmOrder:function(){
        	// console.log(vm.couponString);
        	// console.log(vm.couponId);
        	// console.log(vm.couponPrice);
        	
			if(!this.oLoc.name){
                mui.toast("请先设置收获地址！",{duration:'short'});
            }else{
        		var cartIds = new Array();
            	if (cartListJson[0].id != undefined) { //购物车进来的
            		

	        		for (var i in cartListJson) {

        				cartIds.push(cartListJson[i].id); // 购物车id
	        		}
	        		// console.log(vm.mes);
	        		$.ajax({ // 购物车进来的
	        			// price 实付价格 message买家留言 addressId 地址id
	        			data: {cartIds: cartIds, state: 1, addressId: this.oLoc.id, price: price, message: vm.mes, id: vm.couponId, youHuiNum: vm.couponPrice},

	        			url: '/index.php/Home/Order/SureOrder',

	        			type: 'post',

	        			success: function(msg) {
	        				console.log(msg);
	        				if(msg.code  == 1){

	        					mui.alert(msg.msg, function(){
	        						var This = this;
					                var URL = "/index.php/Home/Pay/pay";
					                var temp = document.createElement("form");
					                temp.action = URL;
					                temp.method = "post";
					                temp.style.display = "none";
					                var PARAMS = new Array();
					                PARAMS['oid'] = msg.oid; //订单id
					                PARAMS['type'] = msg.type; // 订单类型  可能是一折购  可能是其他类型的，现在是普通商品
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
	        					});
	        				}else{
	        					mui.alert(msg.msg);
	        				}
	        				
    					}
	        		});
            	} else {// 立即购买进来的
					var liJiGouMai = new Array();
    				if (cartListJson[0].oid == undefined) { // 无多规格

    					liJiGouMai.push({"gid": cartListJson[0].gid, "number": cartListJson[0].number});

    				} else {

    					liJiGouMai.push({"gid": cartListJson[0].gid, "oid": cartListJson[0].oid, "number": cartListJson[0].number});
    				}
	                $.ajax({

	                	data: {state: 2, addressId: this.oLoc.id, message: vm.mes, liJiGouMai: liJiGouMai,  price: price, id: vm.couponId, youHuiNum: vm.couponPrice}, 

	                	url: '/index.php/Home/Order/SureOrder',

	                	type: 'post',

	                	success: function(msg) {
	                		console.log(msg);
	        				if(msg.code  == 1){

	        					mui.alert(msg.msg, function(){
	        						var This = this;
					                var URL = "/index.php/Home/Pay/pay";
					                var temp = document.createElement("form");
					                temp.action = URL;
					                temp.method = "post";
					                temp.style.display = "none";
					                var PARAMS = new Array();
					                PARAMS['oid'] = msg.oid; //订单id
					                PARAMS['type'] = msg.type; // 订单类型  可能是一折购  可能是其他类型的，现在是普通商品
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
	        					});
	        				}else{
	        					mui.alert(msg.msg);
	        				}
	                	}

	                });
            	}

                // alert("确认订单");
                // console.log("留言："+vm.mes);
                // console.log(liJiGouMai);

            }
      	},
      	//打开优惠券选框
		openCoupon:function(){
			vm.mask.show();
			$('.coupon').css('display','block').animate({'bottom':'0'},300);
			//禁止滑动
            this.oTop = $(document).scrollTop();
            $('body').css({'position':'fixed','top':'-'+vm.oTop+ 'px','left':0,'right':0,'bottom':0,'margin':'auto'});
		},
		//关闭优惠券
		closeCoupon:function(){
			$('.coupon').animate({'bottom':'-8.5rem'},300,function(){
				$(this).css('display','none');
			});
			vm.mask.close();
		},
		//选择优惠券
		choice:function(index,cId){
			// console.log(vm.oCoupon[index].cid);
			vm.couponString = vm.oCoupon[index].text;
			vm.couponId = vm.oCoupon[index].cid;
			vm.couponPrice = vm.oCoupon[index].price;
			// console.log(vm.couponString,cId);
			//关闭选项框
			vm.closeCoupon();
		}
	},//methods结束

	//计算属性
	computed:{
		//商品数量总计
		goodNum:function(){
			var total = 0;
			for(var i = 0;i<this.gowuche.length;i++){
				total += parseInt(this.gowuche[i].num);
			}
			return total;
		},
		//商品总价
		goodPrice:function(){

			if (manBool) { // 这里是满减的小计区间

				var total = manJianJson.priceTotalArray; // 小计; // 商品小计

			} else { // 这里是没有满减的小计区间

				var total = manJianJson; // 商品小计
				for(var i = 0;i<this.gowuche.length;i++){
					total += this.gowuche[i].num*this.gowuche[i].price;
				}

	            // total += Number(this.takeMoney)-Number(this.couponPrice);
			}
            return total.toFixed(3).replace(/([0-9]+\.[0-9]{2})[0-9]*/,"$1");//保留两位小数，不四舍五入 
		},

		//实付
		goodsTotal:function(){
			if (manBool) { // 这里是满减的小计区间

				var total = manJianJson.price; // 这个是满减后的价格

			} else { // 这里是没有满减的小计区间
				var total = manJianJson; // 商品小计
				for(var i = 0;i<this.gowuche.length;i++){
					total += this.gowuche[i].num*this.gowuche[i].price;
				}
			}
            total += Number(this.takeMoney)-Number(this.couponPrice);
            price = total.toFixed(3);
            return total.toFixed(3).replace(/([0-9]+\.[0-9]{2})[0-9]*/,"$1");//保留两位小数，不四舍五入 
		},
	}
});

vm.init();
