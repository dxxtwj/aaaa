<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="/Public/admin/assets/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="/Public/admin/css/style.css"/>
<link rel="stylesheet" href="/Public/admin/assets/css/font-awesome.min.css" />
<link href="/Public/admin/assets/css/codemirror.css" rel="stylesheet">
<!--[if IE 7]>
<link rel="stylesheet" href="/Public/admin/assets/css/font-awesome-ie7.min.css" />
<![endif]-->
<!--[if lte IE 8]>
<link rel="stylesheet" href="/Public/admin/assets/css/ace-ie.min.css" />
<![endif]-->
<script src="/Public/admin/assets/js/ace-extra.min.js"></script>
<!--[if lt IE 9]>
<script src="/Public/admin/assets/js/html5shiv.js"></script>
<script src="/Public/admin/assets/js/respond.min.js"></script>
<![endif]-->
<!--[if !IE]> -->
<script src="/Public/admin/assets/js/jquery.min.js"></script>        
<!-- <![endif]-->
<script src="/Public/admin/assets/dist/echarts.js"></script>
<script src="/Public/admin/assets/js/bootstrap.min.js"></script>


<title>无标题文档</title>
</head>

<style>
@media (min-width: 768px){
.col-sm-6{width: 25%;}
}
.state-overview .symbol{padding: 15px 0; width: 30%;}
.state-overview .value{width: 70%; padding-top:10px;}
.state-overview .symbol i{font-size: 36px;}
.state-overview .value h1{font-size: 26px;}
</style>

<body>
<div class="page-content clearfix">
 <!-- <div class="alert alert-block alert-success">
  <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
  <i class="icon-ok green"></i>欢迎使用<strong class="green">后台管理系统<small>(v1.2)</small></strong>,你本次登陆时间为2016年7月12日13时34分，登陆IP:192.168.1.110.	
 </div> -->
 <div class="state-overview clearfix">
          <div class="col-lg-3 col-sm-6">
              <section class="panel">
              <a href="#" title="交易金额">
                  <div class="symbol terques">
                     <i class="icon-user"></i>
                  </div>
                  <div class="value">
                      <h1><?php echo ($order[0]); ?></h1>
                      <p>交易金额</p>
                  </div>
                  </a>
              </section>
          </div>
          <div class="col-lg-3 col-sm-6">
              <section class="panel">
                  <div class="symbol red">
                      <i class="icon-tags"></i>
                  </div>
                  <div class="value">
                      <h1><?php echo ($order[1]); ?></h1>
                      <p>订单数量</p>
                  </div>
              </section>
          </div>
          <div class="col-lg-3 col-sm-6">
              <section class="panel">
                  <div class="symbol yellow">
                      <i class="icon-shopping-cart"></i>
                  </div>
                  <div class="value">
                      <h1><?php echo ($order[2]); ?></h1>
                      <p>交易关闭</p>
                  </div>
              </section>
          </div>
          <div class="col-lg-3 col-sm-6">
              <section class="panel">
                  <div class="symbol blue">
                      <i class="icon-bar-chart"></i>
                  </div>
                  <div class="value">
                      <h1><?php echo ($order[3]); ?></h1>
                      <p>订单回收站</p>
                  </div>
              </section>
          </div>
           <div class="col-lg-3 col-sm-6">
              <section class="panel">
                  <div class="symbol blue">
                      <i class="icon-bar-chart"></i>
                  </div>
                  <div class="value">
                      <h1><?php echo ($order[4]); ?></h1>
                      <p>售后订单</p>
                  </div>
              </section>
          </div>
           <div class="col-lg-3 col-sm-6">
              <section class="panel">
                  <div class="symbol blue">
                      <i class="icon-bar-chart"></i>
                  </div>
                  <div class="value">
                      <h1><?php echo ($goods_sale_count); ?></h1>
                      <p>上架商品</p>
                  </div>
              </section>
          </div>
           <div class="col-lg-3 col-sm-6">
              <section class="panel">
                  <div class="symbol blue">
                      <i class="icon-bar-chart"></i>
                  </div>
                  <div class="value">
                      <h1><?php echo ($goods_notsale_count); ?></h1>
                      <p>下架商品</p>
                  </div>
              </section>
          </div>
           <div class="col-lg-3 col-sm-6">
              <section class="panel">
                  <div class="symbol blue">
                      <i class="icon-bar-chart"></i>
                  </div>
                  <div class="value">
                      <h1><?php echo ($goods_emptyStcok_count); ?></h1>
                      <p>告罄商品</p>
                  </div>
              </section>
          </div>
      </div>
             <!--实时交易记录-->
             <div class="clearfix">
             <div class="t_Record">
               <div id="main" style="height:300px; overflow:hidden; width:100%; overflow:auto" ></div>     
              </div> 
         <!-- <div class="news_style">
          <div class="title_name">最新消息</div>
          <ul class="list">
           <li><i class="icon-bell red"></i><a href="#">后台系统找那个是开通了。</a></li>
           <li><i class="icon-bell red"></i><a href="#">6月共处理订单3451比，作废为...</a></li>
           <li><i class="icon-bell red"></i><a href="#">后台系统找那个是开通了。</a></li>
           <li><i class="icon-bell red"></i><a href="#">后台系统找那个是开通了。</a></li>
           <li><i class="icon-bell red"></i><a href="#">后台系统找那个是开通了。</a></li>
          </ul>
         </div> --> 
         </div>
<div id="order_all" style="display:none"><?php echo ($order_all); ?></div>
<div id="notpay_order" style="display:none"><?php echo ($notpay_order); ?></div>
<div id="pay_order" style="display:none"><?php echo ($pay_order); ?></div>
<div id="send_order" style="display:none"><?php echo ($send_order); ?></div>
<script type="text/javascript">
var order_all = $('#order_all').html();
var notpay_order = $('#notpay_order').html();
var pay_order = $('#pay_order').html();
var send_order = $('#send_order').html();

var order_all_s = order_all.split(",");
var notpay_order_s = notpay_order.split(",");
var pay_order_s = pay_order.split(",");
var send_order_s = send_order.split(",");

var order_all_l = new Array();
var notpay_order_l = new Array();
var pay_order_l = new Array();
var send_order_l = new Array();

for(var i in order_all_s){
    all = parseInt(order_all_s[i]);
    order_all_l.push(all);
}
for(var i in notpay_order_s){
    notp = parseInt(notpay_order_s[i]);
    notpay_order_l.push(notp);
}
for(var i in pay_order_s){
    pay = parseInt(pay_order_s[i]);
    pay_order_l.push(pay);
}
for(var i in send_order_s){
    send = parseInt(send_order_s[i]);
    send_order_l.push(send);
}

$(document).ready(function(){

    $(".t_Record").width($(window).width()-320);
    //当文档窗口发生改变时 触发  
    $(window).resize(function(){
      $(".t_Record").width($(window).width()-320);
    });
});
	 
	 
        require.config({
            paths: {
                echarts: '/Public/admin/assets/dist'
            }
        });
        require(
            [
                'echarts',
				'echarts/theme/macarons',
                'echarts/chart/line',   // 按需加载所需图表，如需动态类型切换功能，别忘了同时加载相应图表
                'echarts/chart/bar'
            ],
            function (ec,theme) {
                var myChart = ec.init(document.getElementById('main'),theme);
               option = {
                  title : {
                      text: '月购买订单交易记录',
                      subtext: '实时获取用户订单购买记录'
                  },
                  tooltip : {
                      trigger: 'axis'
                  },
                  legend: {
                      data:['所有订单','待付款','已付款','代发货']
                  },
                  toolbox: {
                      show : true,
                      feature : {
                          mark : {show: true},
                          dataView : {show: true, readOnly: false},
                          magicType : {show: true, type: ['line', 'bar']},
                          restore : {show: true},
                          saveAsImage : {show: true}
                      }
                  },
                  calculable : true,
                  xAxis : [
                      {
                          type : 'category',
                          data : ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月']
                      }
                  ],
                  yAxis : [
                      {
                          type : 'value'
                      }
                  ],
                  series : [
                      {
                          name:'所有订单',
                          type:'bar',
                          data:[120, 49, 70, 232, 256, 767, 1356, 1622, 326, 200,164, 133],
                          markPoint : {
                              data : [
                                  {type : 'max', name: '最大值'},
                                  {type : 'min', name: '最小值'}
                              ]
                          }           
                      },
                      {
                          name:'待付款',
                          type:'bar',
                          data:[26, 59, 30, 84, 27, 77, 176, 1182, 487, 188, 60, 23],
                          markPoint : {
                              data : [
                                  {type : 'max', name: '最大值'},
                                  {type : 'max', name: '最大值'}
                              ]
                          },
                         
              			
                      }
              		, {
                          name:'已付款',
                          type:'bar',
                          data:[26, 59, 60, 264, 287, 77, 176, 122, 247, 148, 60, 23],
                          markPoint : {
                              data : [
                                  {type : 'max', name: '最大值'},
                                  {type : 'max', name: '最大值'}
                              ]
                          },
                         
              		}
              		, {
                          name:'代发货',
                          type:'bar',
                          data:[26, 59, 80, 24, 87, 70, 175, 1072, 48, 18, 69, 63],
                          markPoint : {
                              data : [
                                  {type : 'max', name: '最大值'},
                                  {type : 'max', name: '最大值'}
                              ]
                          },
                         
              		}
                  ]
              };
                    
                option.series[0].data = order_all_l;
                option.series[1].data = notpay_order_l;
                option.series[2].data = pay_order_s;
                option.series[3].data = send_order_s;
                myChart.setOption(option);
            }
        );
    </script> 
     </div>
</body>
</html>