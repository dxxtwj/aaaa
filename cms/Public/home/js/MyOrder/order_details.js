$('.loading').remove();
console.log(11);
console.log(youHuiJson);
orderDataObject = JSON.parse(orderRecListJson);//订单和商品的信息
console.log(orderWuLiuJson);
addressObject = JSON.parse(addressJson);//地址信息
orderTimeObject = JSON.parse(orderTimeJson);//订单时间
orderWuLiuObject = JSON.parse(orderWuLiuJson);//物流信息
youHuiJsonObject = JSON.parse(youHuiJson);//优惠

console.log(orderDataObject);
// console.log(orderTimeObject);
//订单详情数据 开始
//订单信息
//num：订单号，status：订单状态，state：状态文字，move：操作(确认收货或者去付款)，goodNum：商品个数，goodPrice：总价，takeMoney:运费,preferential:优惠方式（若无 返回无）
// var orderdArr = {"danhao":"SH201602281404537489","status":"3","state":"待收货","move":"确认收货","goodNum":"2","goodPrice":"91.64","takeMoney":"668.22","preferential":"省10元:组合优惠","mes":[{"img":"/Public/home/images/goods_1.jpg","mes":"粉墨东西PinKo牛仔+棕色打底针织长裙套装韩版东大门","selected":'"灰色" "S"',"num":"1","price":"45.82","href":"0"},{"img":"/Public/home/images/goods_1.jpg","mes":"粉墨东西PinKo牛仔+棕色打底针织长裙套装韩版东大门","selected":'',"num":"1","price":"45.82","href":"0"}]};
	if (orderDataObject != 0) {

		var orderdArr = orderDataObject;
	} else {

		var orderDataObject = [];
	}
//地址信息
// var locObj = {"name":"龙虾","phone":"18718425825","loc":"广东省佛山市南海桂城天安数码城三期714"};
var locObj = addressObject;

//详细信息
// var detailsObj = {"company":"顺丰快递","wuliu":"9890733856318"};
var detailsObj = orderDataObject;
//detailsObj=[];
// console.log(orderWuLiuObject.wuliu.data);
//物流信息
// var logisticsArr = [{"loc":"佛山","context":"桂城分局派件员正在为您派件","time":"2016-09-05 11:52:13","flag":true},{"loc":"佛山","context":"桂城分局派件员正在为您派件","time":"2016-09-05 11:52:13","flag":false},{"loc":"佛山","context":"桂城分局派件员正在为您派件","time":"2016-09-05 11:52:13","flag":false}];
if (orderWuLiuObject.wuliu != 0) {

	var logisticsArr = orderWuLiuObject.wuliu.data;
} else {

	logisticsArr = [];
}

// 时间
// var detTime = {"orderTime":"2017-11-05 10:06",
// "payTime":"2017-11-05 10:07",
// "goodTime":"2017-11-05 10:08",
// "completeTime":"2017-11-05 10:16"};
var detTime = orderTimeObject;
var vm = new Vue({
	el:"#control",
	data:{
		//订单详情数据
		//订单信息数组
		oOrderDetails:orderdArr,
		//地址信息
		oLoc:locObj,
		//详细信息
		oDetails:detailsObj,
		//物流信息
		oLogistics:logisticsArr,
		detTime:detTime
	},
	methods:{
		//显示订单状态对应的颜色
		showStatus:function(status){
			switch(status){
				case '1': //待付款
					return 'state_topay';
					break;
				case '2': //待发货
					return 'state_take';
					break;
				case '3': //待收货
					return 'state_collect';
					break;
			}
		},
		//确认收货或者去付款
		toMove:function(mes){
			mui.confirm(mes,function(e){
				if(e.index){
					alert("确定");
				}
			})
		},
		//查看物流
		checkGood:function(){
			alert('查看物流');
		},
		//取消订单
		cancelOrder:function(){
			mui.confirm('确定取消订单?',function(e){
				if(e.index==1){
					alert("确定");
				}
			})
		},
	},
});

