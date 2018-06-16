$(".loading").remove();
console.log(result);
var vm = new Vue({
	el:"#control",
	data:{
		//购物车数组
		cartMes:[],
		//购物车合计
		price:'0.00',
		//购物车结算数量
		choiceGoodsNum:0,
		//删除按钮
		edit:'编辑',
		//购物车状态(0：删除，1：结算)
		status:1,
	},
	methods:{
        init:function(){
            //购物车数组
            // var cart = [{"img":"/Public/home/images/goods_1.jpg","mes":"粉墨东西PinKo牛仔+棕色打底针织长裙套装韩仔粉墨东西PinKo牛仔+棕色打底针织长裙套装韩仔","selected":'"灰色" "S"',"num":"1","price":"1.11","href":"#0","stock":'5'},
            // {"img":"/Public/home/images/goods_1.jpg","mes":"粉墨东西PinKo牛仔+","selected":'',"num":"1","price":"2.22","href":"#1","stock":'5'},
            // {"img":"/Public/home/images/goods_1.jpg","mes":"粉墨东西PinKo牛仔+棕色打底针织长裙套装韩仔粉墨东西PinKo牛仔+棕色打底针织长裙套装韩仔","selected":'"灰色" "S"',"num":"1","price":"3.33","href":"#0","stock":'5'},
            // {"img":"/Public/home/images/goods_1.jpg","mes":"粉墨东西PinKo牛仔+","selected":'',"num":"1","price":"4.44","href":"#1","stock":'5'},{"img":"/Public/home/images/goods_1.jpg","mes":"粉墨东西PinKo牛仔+棕色打底针织长裙套装韩仔粉墨东西PinKo牛仔+棕色打底针织长裙套装韩仔","selected":'"灰色" "S"',"num":"1","price":"1.11","href":"#0","stock":'5'},
            // {"img":"/Public/home/images/goods_1.jpg","mes":"粉墨东西PinKo牛仔+","selected":'',"num":"1","price":"2.22","href":"#1","stock":'5'},
            // {"img":"/Public/home/images/goods_1.jpg","mes":"粉墨东西PinKo牛仔+棕色打底针织长裙套装韩仔粉墨东西PinKo牛仔+棕色打底针织长裙套装韩仔","selected":'"灰色" "S"',"num":"1","price":"3.33","href":"#0","stock":'5'},
            // {"img":"/Public/home/images/goods_1.jpg","mes":"粉墨东西PinKo牛仔+","selected":'',"num":"1","price":"4.44","href":"#1","stock":'5'},];

			var cart = new Array();
			for (var i in result) {

				var cartList = {"img": result[i].img, "mes":result[i].mes, "selected": result[i].selected, "num": result[i].num, "price": result[i].price, "href": "/index.php/Home/Goods/goods_detail/gid/" + result[i].goods_id, "stock": result[i].num, "id": result[i].goods_id, "crid": result[i].id, "oid": result[i].oid, "weight": result[i].weight}
				cart.push(cartList);
			}
//          cart = [];
			if(cart.length){//有数据
				vm.cartMes = cart;
				$('.content').css('display','block');
				$('.car-none').css('display','none');
			}else{
				$('.content').css('display','none');
				$('.car-none').css('display','block');
			}
            
		},
		//更改购物车状态
		changeStatus:function(){
			if(vm.status==1){
				vm.status = 0;
				vm.edit='完成';
			}else{
				vm.status = 1;
				vm.edit='编辑';
			}			
		},
		//增加商品数量
		addNum:function(index){
			vm.cartMes[index].num++;
			// console.log(vm.cartMes[index]);
			if (vm.cartMes[index].oid != undefined) { // 多规格区域
				
				oid = vm.cartMes[index].oid;

			} else { // 没有多规格区域
				
				oid = null;
			}

			// 没登录传商品id 登录传购物车id
			if (vm.cartMes[index].crid != undefined) { // 已登录

				subId = vm.cartMes[index].crid; // 商品id

			} else {// 购物车id为空表示没登录

				subId = vm.cartMes[index].id; // 购物车id

			}

			$.ajax({
				data: {subId: subId, number: vm.cartMes[index].num, oid: oid},

				url: '/index.php/Home/Cart/Add_Subtract',

				type: 'get',

				success: function(msg) {

					console.log(msg);
				}


			});
		},
		//减少商品数量
		reduceNum:function(index){
			if(vm.cartMes[index].num>1){
				vm.cartMes[index].num--;
				if (vm.cartMes[index].oid != undefined) { // 多规格区域
					
					oid = vm.cartMes[index].oid;

				} else { // 没有多规格区域
					
					oid = null;
				}

				// 没登录传商品id 登录传购物车id
				if (vm.cartMes[index].crid != undefined) { // 已登录

					subId = vm.cartMes[index].crid; // 商品id

				} else {// 购物车id为空表示没登录

					subId = vm.cartMes[index].id; // 购物车id

				}
				
				$.ajax({
					data: {subId: subId, number: vm.cartMes[index].num, oid: oid},

					url: '/index.php/Home/Cart/Add_Subtract',

					type: 'get',

					success: function(msg) {

						console.log(msg);
					}


				});
			}
		},
		//选择商品
		choiceGoods:function(e){
			var that = e.currentTarget;
			if($(that).hasClass('radio-choose')){
				$(that).removeClass('radio-choose');
				vm.choiceGoodsNum--;
			}else{
				$(that).addClass('radio-choose');
				vm.choiceGoodsNum++;
			}
			var cBool = true;
			$('.radio').each(function(){
				if(!$(this).hasClass('radio-choose')){
					cBool = false;
					return false;
				}
			})
			if(cBool){
				$('.settlement-radio').addClass('settlement-radio-choose');
			}else{
				$('.settlement-radio').removeClass('settlement-radio-choose');
			}
			vm.totalPrice();
		},
		//全选
		choiceAll:function(){
			if($('.settlement-radio').hasClass('settlement-radio-choose')){
				$('.settlement-radio').removeClass('settlement-radio-choose');
				$('.radio').removeClass('radio-choose');
				vm.choiceGoodsNum=0;
			}else{
				$('.settlement-radio').addClass('settlement-radio-choose');
				$('.radio').addClass('radio-choose');
				vm.choiceGoodsNum = vm.cartMes.length;
			}
			vm.totalPrice();
		},
		//总计
		totalPrice:function(){
			var tPrice = 0;
			$('.content li').each(function(){
				if($(this).find('.radio').hasClass('radio-choose')){
					var i = $(this).attr('oIndex');
					tPrice+= vm.cartMes[i].num*vm.cartMes[i].price;
				}
			});
			vm.price = tPrice.toFixed(2);
		},
		//结算
		account:function(){
			if(!$('.radio').hasClass('radio-choose')){
				mui.toast('请选择商品！',{ duration:'short', type:'div' }); 
				return false;
			}
			var arr=new Array;
			$('.radio').each(function(){
				if($(this).hasClass('radio-choose')){
					var index = $(this).parent().attr('oIndex');
					arr.push(vm.cartMes[index]);
				}
			})
			// mui.alert('共'+vm.price+'元');
			crId = new Array();
			for (var i in arr) { // 获取购物车id

				crId.push(arr[i]['crid']);
			}
			$.ajax({// 购物车点击去结算
				// statr 1表示是在购物车点击的 
				// price 总价
				// crid 购物车id
				data: {state: 1, price: vm.price, crId: crId},

				type: 'post',

				url: '/index.php/Home/Cart/finish',

				success: function(msg) {
					console.log(msg);
					if (msg.code == 0) {
						mui.toast(msg.msg,{ duration:'short', type:'div' });
						if (msg.url != null) {
							setTimeout(function() { // 两秒后跳转

								location.href = msg.url;

							}, 2000)
						}
					} else {

						location.href = msg.url;

					}
				}

			});
			
		},
		//删除商品
		delGoods:function(){
			
			if($('.radio-choose').length==0){
				mui.toast('请选择商品！',{ duration:'short', type:'div' }); 
			}else{
				var arr=new Array;
				var crid = new Array();
				$('.radio').each(function(){
				if($(this).hasClass('radio-choose')){
						var index = $(this).parent().attr('oIndex');
					}
				})
				mui.confirm("确定删除这"+vm.choiceGoodsNum+"件商品",vm.sureDel);
			}
		},
		sureDel:function(data){
			if(data.index){
				var arr = new Array;
				$('.radio-choose').each(function(){
					var index = $(this).parent().attr('oIndex');
					
					if (login_json.login == true) { // 登录传递购物车id

							arr.push(vm.cartMes[index]['crid']);//传递crid

						} else { // 未登录传递多规格id和商品id
							if(vm.cartMes[index]['oid']){
								arr.push(vm.cartMes[index]['id']+"@#"+vm.cartMes[index]['oid']);//多规格
							}else{
								arr.push(vm.cartMes[index]['id']);// 无规格
							}
							

						}
					$(this).parent().remove();
				});

				$.ajax({ // 删除

					data: {id: arr},

					url: '/index.php/Home/Cart/cart_delete',

					type: 'get',

					success: function(msg) {
						
						console.log(msg);
					}

				});

				if($('.content li').length == 0){
					$('.content').css('display','none');
					$('.car-none').css('display','block');
				}
				//重置相关值
				vm.choiceGoodsNum=0;
				vm.price = '0.00';
				$('.settlement-radio').removeClass('settlement-radio-choose');
				}
			
		},  
	},//methods结束
	
	//计算属性
	 computed: {
		cartNum:function(){
			return this.cartMes.length;
		}
	},

});

vm.init(); //初始化