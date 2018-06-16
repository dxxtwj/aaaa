$('body').append('<nav class="nava mui-bar mui-bar-tab">\n            <a class="nav-index mui-tab-item" href="/index.php/Home/Index/index">\n                <span class="mui-icon iconfont icon-shouye"></span>\n                <span class="mui-tab-label">首页</span>\n            </a><a class="nav-classify mui-tab-item" href="/index.php/Home/Goods/classify">\n                <span class="mui-icon iconfont icon-fenlei"></span>\n                <span class="mui-tab-label">全部分类</span>\n            </a>\n                       <a class="nav-car mui-tab-item" href="/index.php/Home/Cart/index">\n                <span class="mui-icon iconfont icon-gouwuche2"></span>\n                <span class="mui-tab-label">购物车</span>\n            </a>\n            <a class="nav-person mui-tab-item" href="/index.php/Home/Person/person">\n                <span class="mui-icon iconfont icon-wode"></span>\n                <span class="mui-tab-label">我的</span>\n            </a>\n        </nav>');
    $('.nava').on('tap','a',function(){document.location.href=this.href;});
    var loc = window.location.href;

    if(loc.indexOf('Index/index') > 0){
        $(".nav-index").addClass('mui-active');
    }else if(loc.indexOf('Cart/index') > 0){
        $(".nav-car").addClass('mui-active');
    }else if(loc.indexOf('person') > 0){
        $(".nav-person").addClass('mui-active');
    }else if(loc.indexOf('Goods/classify') > 0 || loc.indexOf('Goods/hot_sale') > 0){
        $(".nav-classify").addClass('mui-active');
    }else{
        $(".nav-index").addClass('mui-active');
    }