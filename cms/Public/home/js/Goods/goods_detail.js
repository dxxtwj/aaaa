$('.loading').remove();
console.log(gr_info_json);
// console.log(format_json);
// console.log(coupon);
var coupon_res = JSON.parse(coupon);
//有数据
//商品信息
// var mes = {
// 'small_img':'/Public/home/images/goods_1.jpg',//规格小图片
// 'img':['/Public/home/images/person_bg.png','/Public/home/images/person_bg.png'],
// 'title':'植物医家轻盈无痕明星四色散粉12g',
// 'cur_price':'288.00',//现价
// 'old_price':'388.00',//原价
// 'sale':'0',//销量
// 'stock':'23',//库存
// };

// 遍历商品信息
var mes = {
	'small_img': gr_info_json.img,//规格小图片
	'img': gr_info_json.banner_img,
	'title': gr_info_json.name,
	'cur_price': gr_info_json.price,//现价
	'old_price': gr_info_json.oldPrice,//原价
	'sale': gr_info_json.sale,//销量
	'stock': gr_info_json.stock,//库存
};
Function.prototype.method=function(name,fun){
    if(!this.prototype[name])
        this.prototype[name]=fun;
}

String.method('deentityify',function(){
    var entity={
        quot:'"',
        lt:'<',
        gt:'>'
    }
   
    return function(){
        return this.replace(/&([^&;]+);/g,function(a,b){
            var ret=entity[b];
            return typeof ret==='string'?ret:a
        })
    }
}());
//详情和基本信息
// var info = {
// 	details:['/Public/home/images/banner.jpg','/Public/home/images/person_bg.png'],
// 	base:[{'key':'适用人群','value':'女士'},{'key':'刷毛材质','value':'人造纤维'}]
// };
// 遍历详情和基本信息
var imgs = gr_info_json.descImg.deentityify(); // 图片

// imgs.push(); 

var info = {
	details: imgs, 
	base: gr_info_json.parameter
}

//优惠券
//1:未领取，0：已领取
//var coupon = [
//{'cid':'1','price':'5','rulePrice':'39','date_begin':'2017.12.30','date_end':'2018.01.01','status':'1'},{'cid':'1','price':'6','condition':'39','date_begin':'2017.12.30','date_end':'2018.01.01','status':'1'},{'cid':'1','price':'7','condition':'39','date_begin':'2017.12.30','date_end':'2018.01.01','status':'1'}
//];
if(coupon_res != -1){
	var coupon = coupon_res;
}else{
	//无优惠券
	coupon = [];
}




// coupon=[];
// var specs = [
// 	{'title':'规格',spec:['128g','64g']},
// 	{'title':'型号',spec:['11862632','11862633','11862632','11862633','11862632','11862633','11862632','11862633']}
// ];

// 遍历商品规格
var fr_Name = new Array(); // 规格

var specs = new Array(); 

for (var k in format_json) {
	var fo_Option = new Array(); // 型号

	fr_Name.push(format_json[k]['name']); // 压进规格的数组

	for (var i in format_json[k].option) { 
	
		
		fo_Option.push(format_json[k].option[i]['orName']); // 压进型号的数组
	}

	specs.push({'title': fr_Name[k], spec: fo_Option}); // 每循环一次就压进

}

// specs=[];

var vm = new Vue({
	el:'#control',
	data:{
		cartNum:'',//购物车数量
		goodsMsg : mes,//商品信息
		infoMsg : info,//详情和基本信息
		couponMsg:coupon||[],//优惠券
		isSpec:(specs.length==0?false:true),//是否有多规格，
		sBool:false,//标识规格是否选择完整（false:不完整，true：完整）
		aCount:false,//标识是否选择了数量（false:未选，true：已选）
		specs:specs||[],//规格
		specArray:[],//存储规格的数组
		specString:'',//存储规格的字符串
		showString:'',//显示存储规格的字符串
		acount:1,//选择的商品数量
		mask:null,//遮罩
		showAcount:'请选择规格',
		sureBtn:1,//为2时，表示已选择了规格，可立即加入购物车
		bubblingId:'', // 规格id
		},
	watch:{
		acount:function(){
			if(vm.acount!=''){
				if(vm.acount<=1){
					vm.acount=1;
					$('.reduce').addClass('amount-active');
				}
				else if(vm.acount>=vm.goodsMsg.stock){
					vm.acount = vm.goodsMsg.stock;
					$('.add').addClass('amount-active');
				}
				else{
					$('.reduce').removeClass('amount-active');
					$('.add').removeClass('amount-active');
				}
			}
		}
	},
	methods:{
		init:function(){
	        //初始化存储规格数组
	        if(vm.isSpec){
	        	var sArray = new Array(specs.length);
	        	for(var i=0;i<sArray.length;i++){
	        		sArray[i] = '';
	        	}
	        	vm.specArray = sArray;
	        }
	        //初始化遮罩层
			vm.mask = mui.createMask(function(){
				$('.main2').hide().animate({bottom:"-10rem"},"fast");
			});
			if(vm.couponMsg.length){
				try{
					var couponSwiper = new Swiper('.swiper-container2',{
						slidesPerView: 2.1,
						spaceBetween: 0,
						pagination: {
							el: '.swiper-pagination',
							clickable: true,
						},
					})
				}catch(e){};
			}
		},
		//轮播初始化
		imgScroll:function(){
//			// Swiper控制轮播
	        try{
	            var swiper = new Swiper('.swiper-container1', {
	                autoplay: 3000, //每隔三秒自动轮播
	                pagination: '.swiper-pagination',
	                slidesPerView: 1,
	                paginationClickable: true,
	                // spaceBetween: 0, //轮播图片的外边距
	                loop: true,//true就为无限轮播
	                autoplayDisableOnInteraction : false, //自动手动一起
	    //          direction: 'vertical' //轮播方向   
	    			calculateHeight: false
	        	});
	        }catch(e){};
		},
		//选项卡
		choice:function(index){
			$('.details .details-title').removeClass('details-active');
			$('.details .details-title').eq(index).addClass('details-active');
			
			if(index==0){
				$('.goods-details').show();
				$('.goods-base').hide();
			}else if(index==1){
				$('.goods-details').hide();
				$('.goods-base').show();
			}
		},
		//点击领取优惠券
		receice:function(id,status,index){
			//if(status == 0){//已领取
			//	mui.alert('您已领取过该优惠券了哦！');
			//}else if(status ==1){
			//	mui.alert('您已成功领取优惠券！',function(){
			//		$('.coupon_box').eq(index).addClass('received');
			//	});
			//}
			$.ajax({
				url:'/index.php/Home/Person/GetCoupon',
				type:'post',
				data:{crid:id},
				success:function(data){
					if(data['state'] == -2){
						mui.alert(data['msg'],function(){   
							window.location.href='/index.php/Home/User/Login';
						});
					}else{
						mui.alert(data['msg'],function(){
							window.location.reload();
						});
					}
				}
			})
		},
		//显示规格数量选择框   1:加入购物车
		showBox:function(state){
			vm.sureBtn = state;
			vm.mask.show();
			$('.main2').show().animate({bottom:0},"fast");

		},
		//选择规格
		choiceSpec:function(index,subIndex){
			var that = $('.specs-list').eq(index);
			var subThat = that.find('.spec li').eq(subIndex);
			that.find('.spec li').removeClass('spec-active');
			subThat.addClass('spec-active');
			vm.specArray[index]=subThat.text();//获取所选规格的值
			var sString='';
			var sString2='';
			var sbool=true;
			for(var i=0;i<vm.specArray.length;i++){
				if(vm.specArray[i]==''){
					sbool = false;
				}
				if(i!=vm.specArray.length-1){
					sString+=vm.specArray[i]+'@#';
					sString2+=vm.specArray[i]+'，';
				}else{
					sString+=vm.specArray[i];
					sString2+=vm.specArray[i];
				}
			}
			vm.sBool = sbool;
			vm.specString = sString;
			vm.showString = sString2;
			if(vm.sBool){ // 选中多规格变更价格
				// alert(vm.showString);
				// vm.goodsMsg.small_img="";
				// vm.goodsMsg.cur_price="1111";
				// vm.goodsMsg.stock="1111";
				$.ajax({

					data: {showString: vm.showString, gid: gr_info_json.id},

					url: '/index.php/Home/Goods/ajaxFormatsOption',

					type: 'get',

					success: function(msg) {
						if (msg.code == 1) {
							vm.bubblingId = msg.data.fid; // 规格id
							vm.goodsMsg.small_img = msg.data.img; // 组合图片
							vm.goodsMsg.cur_price = msg.data.price; // 组合价格
							vm.goodsMsg.stock = msg.data.stock; // 组合库存
							
						}
					}


				});
			}
		},
		//数量减少
		reduce:function(){
			if(vm.acount>1){
				vm.acount--;
			}
		},
		//增加数量
		add:function(){
			if(vm.acount<vm.goodsMsg.stock){
				vm.acount++;
			}
		},
		//取消
		cancel:function(){
			$('.main2').hide().animate({bottom:"-10rem"},"fast");
			vm.mask.close();
		},
		//确认
		sure:function(){
			if(vm.isSpec && !vm.sBool){//有多规格但选择不完整
				mui.toast('请选择商品规格！',{ duration:'short', type:'div' })
			}else{
				if(vm.isSpec && vm.sBool){//有多规格且选择完整
					vm.showAcount = '已选：'+ vm.showString;
					
				}else if(!vm.isSpec){//无多规格
					vm.showAcount = vm.acount+"件";
					vm.aCount = true;
				}
				if(vm.sureBtn==1){
					vm.mask.close();
					$('.main2').hide().animate({bottom:"-10rem"},"fast");
				}else if(vm.sureBtn==3){
					vm.buyNow();
				}
				else if(vm.sureBtn == 2){
					$('.main2').hide().animate({bottom:"-10rem"},"fast");
					vm.mask.close();
					vm.joinCart();
				}
			}
			
		},
		//加入购物车
		joinCart:function(){
			$('.num').animate({"top":"-0.05rem","left":"0.32rem"},200,function(){
				$(this).animate({"top":"-0.03rem","left":"0.3rem"},200,function(){
					$('.addone').show().animate({"top":"-0.7rem"},500,function(){
						$(this).fadeOut(200,function(){
							$(this).animate({"top":"-0.3rem"});
							vm.cartNum++;
						})
					})
				});
			});
			// console.log('是否有多规格(false:无,true:有)：'+vm.isSpec+'，规格：'+vm.specString+'，数量：'+vm.acount);

			if (vm.isSpec) {
				// fo_id 规格id  number  商品数量  gid：商品id vm.bubblingId 规格id 
				$.ajax({ // 添加购物车 有多规格的

					data: {gid: gr_info_json.id, fo_id: vm.bubblingId, number: vm.acount},

					type: 'get',

					url: '/index.php/Home/Cart/CartAdd1',

					success: function(msg) {
						
						if (msg.code != 1) {
							
							mui.toast(msg.msg);

						}
					}
				});
			} else {
				
				$.ajax({ // 添加购物车 没有多规格的

					data: {gid: gr_info_json.id, number: vm.acount},

					type: 'get',

					url: '/index.php/Home/Cart/CartAdd2',

					success: function(msg) {
						
						if (msg.code != 1) {

							mui.toast(msg.msg);
						}
					}
				});
			}

		},
		//立即购买
		buyNow:function(){
			if((!vm.isSpec&&vm.aCount) || (vm.isSpec && vm.sBool)){//已选
				// console.log('是否有多规格(false:无,true:有)：'+vm.isSpec+'，规格：'+vm.specString+'，数量：'+vm.acount);
				
				if (!vm.isSpec) { // 没有多规格区间 不用传规格id

					$.ajax({
						//  state 1:购物车 2:立即购买
						data: {number: vm.acount, state: 2,gid: gr_info_json.id, price: vm.goodsMsg.cur_price},

						type: 'post',

						url: '/index.php/Home/Cart/finish',

						success: function(msg) {
							console.log(msg);
							
							if (msg.code == 0) {
//								mui.toast(msg.msg);
								setTimeout(function() { // 两秒后跳转

									location.href = msg.url;

								}, 2000)
							} else {

								location.href = msg.url;

							}
						}
					});

				} else { // 有多规格区间  要传规格id
					
					$.ajax({
						// fo_id 规格id state 1:购物车 2:立即购买
						data: {number: vm.acount, state: 2, fo_id: vm.bubblingId, gid: gr_info_json.id, price: vm.goodsMsg.cur_price},

						type: 'post',

						url: '/index.php/Home/Cart/finish',

						success: function(msg) {
							console.log(msg);
							if (msg.code == 0) {
//								mui.toast(msg.msg);
								setTimeout(function() { // 两秒后跳转

									location.href = msg.url;

								}, 2000)
							} else {

								location.href = msg.url;

							}
						}
					});

				}
			}else{
				vm.showBox(3);
			}
		},
		
	},
});

vm.init();
vm.imgScroll();

