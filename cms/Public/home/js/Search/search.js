// var hot = [{"id":"1","text":"巧克力"},
// {"id":"1","text":"口红"},
// {"id":"1","text":"面膜"},
// {"id":"1","text":"爽肤水"},
// {"id":"1","text":"粉底液"}
// ];
var hot = ["巧克力","口红","面膜","爽肤水","粉底液"];
//var historyMes = ["饼干","面包","蛋糕","牛肉干","猪肉干"];
var vm = new Vue({
	el:'#main',
	data:{
		hot:[],//热搜
		historyMes:[],//历史搜索数组
		searchKey:'',//搜索关键字
		storageName:"historyKey_search",//localStorge中的key
	},
	methods:{
		//初始化
		init:function(){
			if(hot.length){//有数据
				vm.hot=hot;
			}else{//无数据
				return false;
			}
		},
		//搜索
		search:function(type,value){

			value = $.trim(value);//消除前后空格
			if(value == ''||value == null || value == undefined){
				mui.toast("请输入搜索关键字！",{ duration:'short', type:'div' })
				
				return false;
			}
			if(type ==2){

				vm.searchKey = value;
			}

			location.href = '/index.php/Home/Goods/hot_sale/search/'+value;//搜索内容
			// $.ajax({ // 搜索

			// 	data: {},

			// 	url: '',

			// 	type: '',

			// 	success: function(msg) {
			// 		console.log(msg);
			// 	}


			// });
			vm.setHistory(vm.storageName,value);
		},
		//存储历史记录
		setHistory:function(key,value){
			var result = $.inArray(value , vm.historyMes);//如果arry数组里面存在"C#" 这个字符串则返回该字符串的数组下标，否则返回(不包含在数组中) -1
			console.log(result);
			if(result != -1){
				vm.historyMes.splice(result,1);
			}
			vm.historyMes.unshift(value);
			//判断localStorage中是否有key
//			if(localStorage.hasOwnProperty(key)){//有
//				
//			}else{//无
//				localStorage.hasOwnProperty(key)
//			}
			var str = JSON.stringify(vm.historyMes);
			localStorage.setItem(key,str);
		},
		//获取历史记录
		getHistory:function(key){
			var arry=localStorage.getItem(key);  
			vm.historyMes = JSON.parse(arry)||[];
		},
		//清空历史搜索
		clearHistory:function(){
			localStorage.removeItem(vm.storageName);
			vm.getHistory();
		}
	},
});
vm.init();
vm.getHistory(vm.storageName);
