$('.loading').remove();
console.log(cr_info_json);
//有数据
// var title = [{"text":"aa","id":0},{"text":"aa","id":10},{"text":"aa","id":20},{"text":"aa","id":5540},{"text":"aa","id":30}];

// 遍历推荐区
var title = new Array();

for (var k in cr_info_json) {

	var type = {"text": cr_info_json[k].text, "id": cr_info_json[k].id};

	title.push(type);

}

// var mes = {
// 	'photo':'/Public/home/images/banner.jpg',
// 	'name':'推荐区分类',
// 	'subclass':[{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'},{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'},{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'},{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'},{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'},{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'},{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'},{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'},{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'},{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'},{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'},{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'},{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'},{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'},]
// };

// console.log(cr_info_json);
// 遍历推荐分类区


for (var k in cr_info_json) {
			
		var mes = {
			"photo": cr_info_json[k].img,
			"name": cr_info_json[0].text,
			"subclass": cr_info_json[0].son
		}

	

}

var vm = new Vue({
	el:'#control',
	data:{
		//导航栏标题
		titleMes:[],
		//分类信息
		classifyMes:[],
		oIndex:0,
	},
	methods:{
		//初始化
		init:function(){
			vm.titleMes = title;
			vm.classifyMes = mes;
		},
		//选项卡选择
		choice:function(index,id){
			vm.oIndex = index;
			

			$.ajax({ // ajax请求分类
 
				data: {id: id},

				type: 'get',

				url: '/index.php/Home/Goods/type',

				success: function(msg) {
					var mes = {
						'photo': msg.maxType.photo, // 一级分类
						'name': msg.maxType.name, // 一级分类
						'subclass': msg.minType  // 二级分类

					};
					// mes = {
					// 	'photo':'/Public/home/images/banner.jpg',
					// 	'name':'推荐区分类',
					// 	'subclass':[{'img':'/Uploads/category_imgs/2017-11-18/5a0fb53de5eb6.jpg','title':'星星卷','href':'#'}]

					// };
					vm.classifyMes = mes;
				}

			});

		},
		
		
	},
});
vm.init();



//		mui.init({
//				swipeBack: false //启用右滑关闭功能
//			});
//			var controls = document.getElementById("segmentedControls");
//			var contents = document.getElementById("segmentedControlContents");
//			var html = [];
//			var i = 1,
//				j = 1,
//				m = 16, //左侧选项卡数量+1
//				n = 21; //每个选项卡列表数量+1
//			for (; i < m; i++) {
//				html.push('<a class="mui-control-item" href="#content' + i + '">选项' + i + '</a>');
//			}
//			controls.innerHTML = html.join('');
//			html = [];
//			for (i = 1; i < m; i++) {
//				html.push('<div id="content' + i + '" class="mui-control-content"><ul class="mui-table-view">');
//				for (j = 1; j < n; j++) {
//					html.push('<li class="mui-table-view-cell">第' + i + '个选项卡子项-' + j + '</li>');
//				}
//				html.push('</ul></div>');
//			}
//			contents.innerHTML = html.join('');
			//默认选中第一个
//			$('.mui-control-item').eq(1).addClass('mui-active');
//			$('.mui-control-content').addClass('mui-active');