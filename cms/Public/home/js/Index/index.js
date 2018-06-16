$('.loading').remove();
console.log(nr_data_json);
console.log(gr_data_json);
//首页图片轮播
// var imgPh = [{"img":"/Public/home/images/banner.png","url":"#"},
// {"img":"/Public/home/images/banner2.png","url":"#1"},
// {"img":"/Public/home/images/banner.png","url":"#2"},]
// console.log(br_data_json);
// 首页轮播图遍历
var imgPh = new Array();
for (var i in br_data_json) {
  imgPh.push({"img":br_data_json[i]['img'], "url":""+br_data_json[i]['url']+""});
}

// 首页导航遍历
var navigation = new Array();
for (var d in nr_data_json) {
  navigation.push({"href":""+nr_data_json[d]['url']+"", "img":nr_data_json[d]['img'], "text": nr_data_json[d]['name']});
}

//  首页热销产品遍历
var productArr = new Array();
for (var g in gr_data_json) {
  productArr.push({"href":"/index.php/Home/Goods/goods_detail/gid/"+gr_data_json[g]['id'], "img":gr_data_json[g]['img'], "text":gr_data_json[g]['name'], "price":gr_data_json[g]['price'], "old_price":gr_data_json[g]['oldPrice'], "gid":gr_data_json[g]['gid']});
}

// //热销产品
// var productArr = [{"href":"#1","img":"/Public/home/images/good.png","text":"植物医家轻盈无痕明星四色散粉12g，美丽一整天","price":"88","old_price":"188","gid":0},
// {"href":"#2","img":"/Public/home/images/good.png","text":"植物医家轻盈无痕美丽一整天","price":"88","old_price":"188","gid":0},
// {"href":"#3","img":"/Public/home/images/good.png","text":"植物医家轻盈美丽一整天","price":"88","old_price":"188","gid":0},
// {"href":"#","img":"/Public/home/images/good.png","text":"植物医家轻盈无痕明一整天","price":"88","old_price":"188","gid":0},
// ];
// var navigation = [{"href":"#","img":"/Public/home/images/side1.png","text":"星星卷"},
// {"href":"#","img":"/Public/home/images/side2.png","text":"星星卷"},
// {"href":"#","img":"/Public/home/images/side1.png","text":"星星卷"},
// {"href":"#","img":"/Public/home/images/side2.png","text":"星星卷"},
// {"href":"#","img":"/Public/home/images/side1.png","text":"星星卷"},
// {"href":"#","img":"/Public/home/images/side2.png","text":"星星卷"},
// {"href":"#","img":"/Public/home/images/side1.png","text":"星星卷"},
// {"href":"#","img":"/Public/home/images/side2.png","text":"星星卷"},
// {"href":"#","img":"/Public/home/images/side1.png","text":"星星卷"},
// {"href":"#","img":"/Public/home/images/side2.png","text":"星星卷"},];

var count = 0;


var vm = new Vue({
    el:".mui-content",
    data:{
      oImg:imgPh,
      productArr:productArr,
      navigation:navigation,//分类栏
      loadBool:true, //加载标志 变为false就是加载完毕
      flag:0, //滚动标志
      record:[], //历史记录
      /** 规格 **/
      oTop:0, //禁止滑动标志,
    },
    methods:{
      init:function(){
        // Swiper控制轮播
        try{
            var swiper = new Swiper('.swiper-container', {
                autoplay: 3000, //每隔三秒自动轮播
                pagination: '.swiper-pagination',
                slidesPerView: 1,
                paginationClickable: true,
                // spaceBetween: 0, //轮播图片的外边距
                loop: true,//true就为无限轮播
                autoplayDisableOnInteraction : false, //自动手动一起
    //            direction: 'vertical' //轮播方向
                
        });
        }catch(e){

    }
      },
      searchShow:function(){
        window.location.href = "/index.php/Home/Search/search";
      }
    }
});

vm.init();

window.onscroll = function(){

  // 页面到底部加载数据
  var ScrollTop = $(document).scrollTop()+ $(window).height();    //滚动条到达底部的值；
    var BodyHeight = $(document).height();                          //文档的高度；
    if(parseInt(ScrollTop)+10 >= parseInt(BodyHeight)){
      count++;  // 页码加加
        // 加载Loading
        if($('#warn').length == 0  && $(document).scrollTop()>0 && vm.loadBool){
            $('.mui-content').append('<div class="spinner-yun" id="warn"><div class="spinner-yun-container container1"><div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div></div><div class="spinner-yun-container container2"><div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div></div><div class="spinner-yun-container container3"><div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div></div></div>');

              $.ajax({

                data: {page: count},

                url: "/index.php/Home/Index/index",

                type: 'get',
                success: function(msg) {
                  console.log(msg);
                    
                    if(msg.code == 1){ //有数据
                      // vm.productArr.push({"href":"","img":"/Public/home/images/good.png","text":"植物医家轻盈无痕明星四色散粉12g，美丽一整天","price":"88","old_price":"188","gid":0},
                        // {"href":"#","img":"/Public/home/images/good.png","text":"植物医家轻盈无痕明星四色散粉12g，美丽一整天","price":"88","old_price":"188","gid":0});

                        for (var g in msg.data) { //  遍历下一页的商品数据
                          vm.productArr.push({"href":"/index.php/Home/Goods/goods_detail/gid/"+msg.data[g].id, "img":msg.data[g].img, "text": msg.data[g].name, "price": msg.data[g].price, "old_price": msg.data[g].oldPrice, "gid": msg.data[g].id});
                        }
                      $('.getbtn').show();
                } else {//加载完毕 无数据
                    $(".mui-content").append('<p class="end" style="text-align: center;padding-top:10px;color:#999;"><i style="display:inline-block; width:3rem;height:1px;background-color:#ccc;position:relative;top:-4px;"></i>&nbsp;&nbsp;&nbsp;加载完毕&nbsp;&nbsp;&nbsp;<i style="display:inline-block;width:3rem;height:1px;background-color:#ccc;position:relative;top:-4px;"></i></p>');
                    vm.loadBool = false;
                }
                $("#warn").remove();
                  
                }

              });
          }
        }
  
};
