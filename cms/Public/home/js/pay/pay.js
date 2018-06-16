console.log(wxstatus);
var wx_state = JSON.parse(wxstatus);
if(wx_state == -1){
    alert('微信支付已关闭');
}else{
    var or_info = JSON.parse(or_info_json);
    var openid = '';
    if(openid_json!=''){
        openid = JSON.parse(openid_json);
    }
    // alert(or_info['OR_Key']);
    // alert(or_info['OR_OrderTotal']);
    // console.log(or_info);
    // alert(openid);
}
function isWeiXin(){
    var ua = window.navigator.userAgent.toLowerCase();
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
        return true;
    }else{
        return false;
    }
}


//检测是否已调用过支付

var o_key = or_info['OR_Key'];

var vm = new Vue({
    el:".mui-content",
    data:{
        data:{},
        isWeiXin:isWeiXin()
    },

    methods:{
        init:function(){
            var data = {"num":or_info['OR_Key'],"price":or_info['OR_OrderTotal'],"type":or_info['type']};
            vm.data = data;
        },

        wePay:function(){
            var is2pay = vm.checkPaytype();
            if(is2pay == true){
            }else{
                return false;
            }
            var This = this;
            var URL = "";
            var PARAMS = new Array();
            PARAMS['WIDout_trade_no'] = vm.data.num;
            PARAMS['type'] = vm.data.type;
            if(isWeiXin()){
                PARAMS['openid'] = openid;
                URL = "/index.php/Home/Pay/wxpay";
                $.ajax({
                    url:URL,
                    type:'post',
                    data:{WIDout_trade_no:PARAMS['WIDout_trade_no'],type:PARAMS['type'],openid:PARAMS['openid']},
                    success:function(data){
                        if(data!=null){
                            function jsApiCall(){
                                WeixinJSBridge.invoke(
                                    'getBrandWCPayRequest',
                                    data,
                                    function(res){
                                        WeixinJSBridge.log(res.err_msg);
                                        // alert(res.err_code+res.err_desc+res.err_msg);
                                        if(res.err_msg == "get_brand_wcpay_request:ok"){
                                            // alert(PARAMS['type']);
                                            window.location.href="/index.php/Home/MyOrder/index/state/2";
                                            
                                        }
                                    }
                                );
                            }

                            function callpay(){
                                if (typeof WeixinJSBridge == "undefined"){
                                    if( document.addEventListener ){
                                        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                                    }else if (document.attachEvent){
                                        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
                                        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                                    }
                                }else{
                                    jsApiCall();
                                }
                            }
                            callpay();
                        }
                    }
                });
                return false;
            }else{
                URL = "/index.php/Home/Pay/wxpayH5";
                var temp = document.createElement("form");
                temp.action = URL;
                temp.method = "post";
                temp.style.display = "none";
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
            }
            

        },
        baoPay:function(){
            var URL = "";
            if(!isWeiXin()){
                URL = "/index.php/Home/Pay/ali_buy";
                var temp = document.createElement("form");
                temp.action = URL;
                temp.method = "post";
                temp.style.display = "none";
                var PARAMS = new Array();
                PARAMS['WIDout_trade_no'] = vm.data.num;
                PARAMS['type'] = vm.data.type;
                for (var x in PARAMS) {
                    var opt = document.createElement("textarea");
                    opt.name = x;
                    opt.value = PARAMS[x];
                    temp.appendChild(opt);
                }

                document.body.appendChild(temp);
                temp.submit();
                return temp;
            }
        },

        checkPaytype:function(){
            if(isWeiXin() == true){
                var wx_type = 2;
            }else{
                var wx_type = 3;
            }
            // alert('type:'+wx_type);
            var state = false;
            $.ajax({

                url:'/index.php/Home/Pay/SelectPayType',

                type:'post',

                async:false,

                data:{key:o_key,wx_type:wx_type},

                success:function(data){
                    // alert('return_type:'+data['state']);
                    if(data['state'] == 1){
                        state = false;
                    }else if(data['state'] == 2){
                        state = true;
                    }else{
                        state = false;
                    }

                }

            })
            return state;
        }

    }

});



vm.init();