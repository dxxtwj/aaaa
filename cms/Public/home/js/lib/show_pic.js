
  
  
      //图片上传预览    IE是用了滤镜。
        function previewImage(file)
        {
          //图片大小不符合（大于5M）
          var fileSize = 0;
          var isIE = /msie/i.test(navigator.userAgent) && !window.opera;      
          if (isIE && !file.files) {     
             var filePath = file.value;      
             var fileSystem = new ActiveXfileect("Scripting.FileSystemfileect");  
             var file = fileSystem.GetFile (filePath);        
             fileSize = file.Size;     
          }else { 
             fileSize = file.files[0].size;   
          } 
          fileSize=Math.round(fileSize/1024*100)/100; //单位为KB
          if(fileSize>5000){
//            alert("照片最大尺寸为5M，请重新选择图片!");
              //弹框
              (function(){
                var flag = true;
            //增加弹框节点
            $("body").prepend('<div><div><div id="yes" style="position:fixed;width:8rem;background-color:#fff;z-index: 1108;border-radius:8px;text-align: center;padding-top:.3rem;left:50%;transform: translateX(-50%);top:4rem;"><p style="color:#111;font-size:20px;">温馨提示</p><p style="color:#333;font-size:16px;padding:.133333rem 0 0.4rem 0;">图片最大尺寸为5MB，请重新选择!<div style="width:%100;height:1px;background-color:#eee;"></div><p><div class="btn" style="color:#e9a64f;height: 1.3rem;line-height: 1.3rem;font-size:20px;font-weight: 800;">确定</div></div>');
            //增加遮罩层
            $("body").append('<div id="mk" style="position:fixed;width:10rem;height:100%;background-color:rgba(0,0,0,.6);top:0;left:50%;transform:translateX(-50%);z-index:1102;"></div>');
            //禁止滑动
            $('body').on("touchmove",function(e) {
                if(flag){
                e.preventDefault();
                }

             });
            //确定
            $('.btn').on('click',function(){
                $("#mk,#yes").remove();
                    //取消禁止滑动
                    flag = false;
            })
              })();
              
            return false;
          }
          //图片大小符合
          else{
            var MAXWIDTH  = 100; 
          var MAXHEIGHT = 100;
          var div = document.getElementById('preview');
          if (file.files && file.files[0])
          {
              div.innerHTML ='<img id=imghead>';
              var img = document.getElementById('imghead');
              img.onload = function(){
                var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
                img.width  =  rect.width;
                img.height =  rect.height;
//                 img.style.marginLeft = rect.left+'px';
//              img.style.marginTop = rect.top+'px';
              }
              var reader = new FileReader();
              reader.onload = function(evt){img.src = evt.target.result;}
              reader.readAsDataURL(file.files[0]);
          }
          else //兼容IE
          {
            var sFilter='filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="';
            file.select();
            var src = document.selection.createRange().text;
            div.innerHTML = '<img id=imghead>';
            var img = document.getElementById('imghead');
            img.filters.item('DXImageTransform.Microsoft.AlphaImageLoader').src = src;
            var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
            status =('rect:'+rect.top+','+rect.left+','+rect.width+','+rect.height);
            div.innerHTML = "<div id=divhead style='width:"+rect.width+"px;height:"+rect.height+"px;margin-top:"+rect.top+"px;"+sFilter+src+"\"'></div>";
          }
        }
        function clacImgZoomParam( maxWidth, maxHeight, width, height ){
            var param = {top:0, left:0, width:width, height:height};
            if( width>maxWidth || height>maxHeight )
            {
                rateWidth = width / maxWidth;
                rateHeight = height / maxHeight;
                 
                if( rateWidth > rateHeight )
                {
                    param.width =  maxWidth;
                    param.height = Math.round(height / rateWidth);
                }else
                {
                    param.width = Math.round(width / rateHeight);
                    param.height = maxHeight;
                }
            }
            param.left = Math.round((maxWidth - param.width) / 2);
            param.top = Math.round((maxHeight - param.height) / 2);
            return param;
          }
            
        }
