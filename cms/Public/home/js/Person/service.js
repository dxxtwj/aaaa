var vm = new Vue({
	el:'#control',
	data:{
		showPic:false,
	},
	methods:{
		//发达二位码
		show:function(){
			vm.showPic = true;
			this.mark();
		},
		//遮罩
		mark:function(){
			$('#mk').animate({opacity:0.7},300)
			.on({
				click:function(){
					vm.showPic = false;
				},
				touchmove:function(e){
					e.preventDefault();
				},
			})
		},
		//拨打电话
		call:function(){
            
		},
	},
});