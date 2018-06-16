var uploadType = '';
var isMultiple = false;
var showContainer = '';
var year = 0;
var month = 0;
var page = 0;
var pagecount = 1;
var upload_return = new Array();
function init(){
    var uploadStr = "\n        <div class=\"showUploader\">\n            <div id=\"uploader\" class=\"wu-example\">\n                <div class=\"queueList\">\n                    <div id=\"dndArea\" class=\"placeholder\">\n                        <div id=\"filePicker\"></div>\n                        <p>或将照片拖到这里</p>\n                    </div>\n                </div>\n                <div class=\"statusBar\" style=\"display:none;\">\n                    <div class=\"progress\">\n                        <span class=\"text\">0%</span>\n                        <span class=\"percentage\"></span>\n                    </div>\n                    <div class=\"info\"></div>\n                    <div class=\"btns\">\n                        <div id=\"filePicker2\"></div>\n                        <div class=\"uploadBtn\">开始上传</div>\n                    </div>\n                </div>\n            </div>\n\n        </div>\n\n        <div class=\"get_url_photo\" style=\"display:none;\">\n            <form>\n                <div class=\"form-group\">\n                    <input type=\"url\" class=\"form-control\" id=\"networkurl\" placeholder=\"请输入网络图片地址\">\n                    <input type=\"hidden\" name=\"network_attachment\" value=\"\">\n                    <div id=\"network-img\" class=\"network-img\">\n                        <span class=\"network-img-sizeinfo\" id=\"network-img-sizeinfo\"></span>\n                    </div>\n                </div>\n            </form>\n            <div class=\"modal-footer\">\n                <button type=\"button\" class=\"btn btn-primary yes\">\n                确认\n            </button>\n                <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">取消\n            </button>\n            </div>\n        </div>\n\n        <div class=\"showphoto\" style=\"display:none;\">\n            <div id=\"select\">\n                <div id=\"select-year\" style=\"margin-bottom:10px;\">\n                    <div class=\"btn-group\">\n                        <a href=\"javascript:;\" data-id=\"0\" data-type=\"year\" class=\"btn btn-default btn-info btn-select\">不限</a>\n                        <a href=\"javascript:;\" data-id=\"\" data-type=\"year\" class=\"btn btn-default btn-select\">年</a>\n                        <a href=\"javascript:;\" data-id=\"\" data-type=\"year\" class=\"btn btn-default btn-select\">年</a>\n                        <a href=\"javascript:;\" data-id=\"\" data-type=\"year\" class=\"btn btn-default btn-select\">年</a>\n                        <a href=\"javascript:;\" data-id=\"\" data-type=\"year\" class=\"btn btn-default btn-select\">年</a>\n                    </div>\n                </div>\n                <div id=\"select-month\">\n                    <div class=\"btn-group\">\n                        <a href=\"javascript:;\" data-id=\"0\" data-type=\"month\" class=\"btn btn-default btn-info btn-select\">不限</a>\n                        <a href=\"javascript:;\" data-id=\"1\" data-type=\"month\" class=\"btn btn-default btn-select\">1</a>\n                        <a href=\"javascript:;\" data-id=\"2\" data-type=\"month\" class=\"btn btn-default btn-select\">2</a>\n                        <a href=\"javascript:;\" data-id=\"3\" data-type=\"month\" class=\"btn btn-default btn-select\">3</a>\n                        <a href=\"javascript:;\" data-id=\"4\" data-type=\"month\" class=\"btn btn-default btn-select\">4</a>\n                        <a href=\"javascript:;\" data-id=\"5\" data-type=\"month\" class=\"btn btn-default btn-select\">5</a>\n                        <a href=\"javascript:;\" data-id=\"6\" data-type=\"month\" class=\"btn btn-default btn-select\">6</a>\n                        <a href=\"javascript:;\" data-id=\"7\" data-type=\"month\" class=\"btn btn-default btn-select\">7</a>\n                        <a href=\"javascript:;\" data-id=\"8\" data-type=\"month\" class=\"btn btn-default btn-select\">8</a>\n                        <a href=\"javascript:;\" data-id=\"9\" data-type=\"month\" class=\"btn btn-default btn-select\">9</a>\n                        <a href=\"javascript:;\" data-id=\"10\" data-type=\"month\" class=\"btn btn-default btn-select\">10</a>\n                        <a href=\"javascript:;\" data-id=\"11\" data-type=\"month\" class=\"btn btn-default btn-select\">11</a>\n                        <a href=\"javascript:;\" data-id=\"12\" data-type=\"month\" class=\"btn btn-default btn-select\">12</a>\n                    </div>\n                </div>\n            </div>\n\n\n            <div role=\"tabpanel\" class=\"tab-pane history active\" id=\"history_image\">\n                <div class=\"history-content\" style=\"height: 310px; text-align: center;\">\n                    <ul class=\"img-list clearfix\" style=\"list-style: none;margin:0;padding:0;\">";
    page = 0;
    $.ajax({
        url:'/index.php/Admin/Images/getImages2',
        data:{uploadType:uploadType,page:page},
        async:false,
        success:function(data){
            var str = '';
            for(var i in data['data']){
                uploadStr+='<li class="img-item" attachid="565" title="" path="'+data['data'][i]['path']+data['data'][i]['img']+'">';
                uploadStr+='<div class="img-container" style="background-image: url('+data['data'][i]['path']+data['data'][i]['img']+');">';
                uploadStr+='<div class="select-status"><span></span></div>';
                uploadStr+='</div>';
                uploadStr+='<div class="btnClose" data-id="565">';
                uploadStr+='<a href="#" path="'+data['data'][i]['path']+data['data'][i]['img']+'"><i class="fa fa-times"></i></a>';
                uploadStr+='</div>';
                uploadStr+='</li>';
            }

        }
    });
    uploadStr += "</ul></div>\n            <div class=\"modal-footer\">\n                <div style=\"float: left;\">\n                    <nav id=\"image-list-pager\">\n                        <div id=\"myPage\" style=\"text-align: center;\" pagination=\"pagination_new\" pagenumber=\"1\" totalpage=\"15\" paginationMaxLength=\"5\" onlyOnePageIsShow=\"false\"></div>\n                    </nav>\n                </div>\n                <div style=\"float: right;\">\n                    <button type=\"button\" class=\"btn btn-primary showphoto_yes\" data-dismiss=\"modal\">\u786E\u8BA4</button>\n                    <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">\u53D6\u6D88</button>\n                </div>\n            </div>\n        </div>\n    </div>";
    $('.modal-body-more').html(uploadStr);
}

choosePhotoClick(); //点击选择图片
function choosePhotoClick(){
    $('.choose_photo').unbind('click').bind('click',function(){
        // $('.modal-body-more').html(uploadStr);
        uploadType = $(this).attr("uploadType");
        // alert(uploadType);
        showContainer = $(this).attr("showContainer");
        index = $(this).attr("index");
        // alert($(this).attr("isMultiple"));
        if($(this).attr("isMultiple") == "0"){
            isMultiple = false;
        }else if($(this).attr("isMultiple") == "1"){
            isMultiple = true;
        }
        init();
        $('.nav-pills li').removeClass('active');
        $('.nav-pills li').eq(0).addClass('active');

        uploadFun(); 
        editFun();
    });
}
//设置图片上传
function uploadFun(){
    //start
  // 添加全局站点信息
    var BASE_URL = '/webuploader';
    jQuery(function() {
        var $ = jQuery,    // just in case. Make sure it's not an other libaray.

            $wrap = $('#uploader'),

            // 图片容器
            $queue = $('<ul class="filelist"></ul>')
                .appendTo( $wrap.find('.queueList') ),

            // 状态栏，包括进度和控制按钮
            $statusBar = $wrap.find('.statusBar'),

            // 文件总体选择信息。
            $info = $statusBar.find('.info'),

            // 上传按钮
            $upload = $wrap.find('.uploadBtn'),

            // 没选择文件之前的内容。
            $placeHolder = $wrap.find('.placeholder'),

            // 总体进度条
            $progress = $statusBar.find('.progress').hide(),

            // 添加的文件数量
            fileCount = 0,

            // 添加的文件总大小
            fileSize = 0,

            // 优化retina, 在retina下这个值是2
            ratio = window.devicePixelRatio || 1,

            // 缩略图大小
            thumbnailWidth = 110 * ratio,
            thumbnailHeight = 110 * ratio,

            // 可能有pedding, ready, uploading, confirm, done.
            state = 'pedding',

            // 所有文件的进度信息，key为file id
            percentages = {},

            supportTransition = (function(){
                var s = document.createElement('p').style,
                    r = 'transition' in s ||
                          'WebkitTransition' in s ||
                          'MozTransition' in s ||
                          'msTransition' in s ||
                          'OTransition' in s;
                s = null;
                return r;
            })(),

            // WebUploader实例
            uploader;

        if ( !WebUploader.Uploader.support() ) {
            alert( 'Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
            throw new Error( 'WebUploader does not support the browser you are using.' );
        }

        // 实例化
        uploader = WebUploader.create({
            pick: {
                id: '#filePicker',
                label: '点击选择图片',
                multiple:isMultiple, //是否多选
            },
            // 选完文件后，是否自动上传。
            auto: false,

            dnd: '#uploader .queueList', //是否拖动

            disableGlobalDnd:true,
            paste: document.body,

            // 只请允许图片上传
            // accept: {
            //     title: 'Images',
            //     extensions: 'gif,jpg,jpeg,bmp,png',
            //     mimeTypes: 'image/*'
            // },
            method:'POST',  //后来加的

            // swf文件路径
            swf: BASE_URL + '/js/Uploader.swf',

            chunked: true,
            // server: 'http://webuploader.duapp.com/server/fileupload.php',
            // 文件接收服务端。
            server: '/index.php/Admin/Images/uploads',
            formData:{uploadType:uploadType},
            fileNumLimit: 300,
            fileSizeLimit: 10 * 1024 * 1024,    // 5 M 所有图片大小
            fileSingleSizeLimit: 2 * 1024 * 1024    // 1 M 单张图片
        });


        // 添加“添加文件”的按钮，
        uploader.addButton({
            id: '#filePicker2',
            label: '继续添加'
        });

        // 当有文件添加进来时执行，负责view的创建
        function addFile( file ) {
            upload_return = new Array();
            var $li = $( '<li id="' + file.id + '">' +
                    '<p class="title">' + file.name + '</p>' +
                    '<p class="imgWrap"></p>'+
                    '<p class="progress"><span></span></p>' +
                    '</li>' ),

                $btns = $('<div class="file-panel">' +
                    '<span class="cancel">删除</span>' +
                    '<span class="rotateRight">向右旋转</span>' +
                    '<span class="rotateLeft">向左旋转</span></div>').appendTo( $li ),
                $prgress = $li.find('p.progress span'),
                $wrap = $li.find( 'p.imgWrap' ),
                $info = $('<p class="error"></p>'),

                showError = function( code ) {
                    switch( code ) {
                        case 'exceed_size':
                            text = '文件大小超出';
                            break;

                        case 'interrupt':
                            text = '上传暂停';
                            break;

                        default:
                            text = '上传失败，请重试';
                            break;
                    }

                    $info.text( text ).appendTo( $li );
                };

            if ( file.getStatus() === 'invalid' ) {
                showError( file.statusText );
            } else {
                // @todo lazyload
                $wrap.text( '预览中' );
                uploader.makeThumb( file, function( error, src ) {
                    if ( error ) {
                        $wrap.text( '不能预览' );
                        return;
                    }

                    var img = $('<img src="'+src+'">');
                    $wrap.empty().append( img );
                }, thumbnailWidth, thumbnailHeight );

                percentages[ file.id ] = [ file.size, 0 ];
                file.rotation = 0;
            }

            file.on('statuschange', function( cur, prev ) {
                if ( prev === 'progress' ) {
                    $prgress.hide().width(0);
                } else if ( prev === 'queued' ) {
                    $li.off( 'mouseenter mouseleave' );
                    $btns.remove();
                }

                // 成功
                if ( cur === 'error' || cur === 'invalid' ) {
                    console.log( file.statusText );
                    showError( file.statusText );
                    percentages[ file.id ][ 1 ] = 1;
                } else if ( cur === 'interrupt' ) {
                    showError( 'interrupt' );
                } else if ( cur === 'queued' ) {
                    percentages[ file.id ][ 1 ] = 0;
                } else if ( cur === 'progress' ) {
                    $info.remove();
                    $prgress.css('display', 'block');
                } else if ( cur === 'complete' ) {
                    $li.append( '<span class="success"></span>' );
                }

                $li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
            });

            $li.on( 'mouseenter', function() {
                $btns.stop().animate({height: 30});
            });

            $li.on( 'mouseleave', function() {
                $btns.stop().animate({height: 0});
            });

            $btns.on( 'click', 'span', function() {
                var index = $(this).index(),
                    deg;

                switch ( index ) {
                    case 0:
                        uploader.removeFile( file );
                        return;

                    case 1:
                        file.rotation += 90;
                        break;

                    case 2:
                        file.rotation -= 90;
                        break;
                }

                if ( supportTransition ) {
                    deg = 'rotate(' + file.rotation + 'deg)';
                    $wrap.css({
                        '-webkit-transform': deg,
                        '-mos-transform': deg,
                        '-o-transform': deg,
                        'transform': deg
                    });
                } else {
                    $wrap.css( 'filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation='+ (~~((file.rotation/90)%4 + 4)%4) +')');
                    // use jquery animate to rotation
                    // $({
                    //     rotation: rotation
                    // }).animate({
                    //     rotation: file.rotation
                    // }, {
                    //     easing: 'linear',
                    //     step: function( now ) {
                    //         now = now * Math.PI / 180;

                    //         var cos = Math.cos( now ),
                    //             sin = Math.sin( now );

                    //         $wrap.css( 'filter', "progid:DXImageTransform.Microsoft.Matrix(M11=" + cos + ",M12=" + (-sin) + ",M21=" + sin + ",M22=" + cos + ",SizingMethod='auto expand')");
                    //     }
                    // });
                }


            });

            $li.appendTo( $queue );
        }

        // 负责view的销毁
        function removeFile( file ) {
            var $li = $('#'+file.id);

            delete percentages[ file.id ];
            updateTotalProgress();
            $li.off().find('.file-panel').off().end().remove();
            upload_return = new Array();
        }

        function updateTotalProgress() {
            var loaded = 0,
                total = 0,
                spans = $progress.children(),
                percent;

            $.each( percentages, function( k, v ) {
                total += v[ 0 ];
                loaded += v[ 0 ] * v[ 1 ];
            } );

            percent = total ? loaded / total : 0;

            spans.eq( 0 ).text( Math.round( percent * 100 ) + '%' );
            spans.eq( 1 ).css( 'width', Math.round( percent * 100 ) + '%' );
            updateStatus();
        }

        function updateStatus() {
            var text = '', stats;

            if ( state === 'ready' ) {
                text = '选中' + fileCount + '张图片，共' +
                        WebUploader.formatSize( fileSize ) + '。';
            } else if ( state === 'confirm' ) {
                stats = uploader.getStats();
                if ( stats.uploadFailNum ) {
                    text = '已成功上传' + stats.successNum+ '张照片至XX相册，'+
                        stats.uploadFailNum + '张照片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
                }

            } else {
                stats = uploader.getStats();
                text = '共' + fileCount + '张（' +
                        WebUploader.formatSize( fileSize )  +
                        '），已上传' + stats.successNum + '张';

                if ( stats.uploadFailNum ) {
                    text += '，失败' + stats.uploadFailNum + '张';
                }
            }

            $info.html( text );
        }

        function setState( val ) {
            var file, stats;

            if ( val === state ) {
                return;
            }

            $upload.removeClass( 'state-' + state );
            $upload.addClass( 'state-' + val );
            state = val;

            switch ( state ) {
                case 'pedding':
                    $placeHolder.removeClass( 'element-invisible' );
                    $queue.parent().removeClass('filled');
                    $queue.hide();
                    $statusBar.addClass( 'element-invisible' );
                    uploader.refresh();
                    break;

                case 'ready':
                    $placeHolder.addClass( 'element-invisible' );
                    $( '#filePicker2' ).removeClass( 'element-invisible');
                    $queue.parent().addClass('filled');
                    $queue.show();
                    $statusBar.removeClass('element-invisible');
                    uploader.refresh();
                    break;

                case 'uploading':
                    $( '#filePicker2' ).addClass( 'element-invisible' );
                    $progress.show();
                    $upload.text( '暂停上传' );
                    break;

                case 'paused':
                    $progress.show();
                    $upload.text( '继续上传' );
                    break;

                case 'confirm':
                    $progress.hide();
                    $upload.text( '开始上传' ).addClass( 'disabled' );

                    stats = uploader.getStats();
                    if ( stats.successNum && !stats.uploadFailNum ) {
                        setState( 'finish' );
                        return;
                    }
                    break;
                case 'finish':
                    stats = uploader.getStats();
                    if ( stats.successNum ) {
                        // alert( '上传成功' );
                        console.log(upload_return);
                        if(upload_return != null){
                            if(showContainer==".img_container"){
                                // alert(upload_return);
                                var str='';
                                str +='<div class="m_imgBox"><div class="m_img">';
                                str +='<input type="hidden" name="path" class="path" value="'+upload_return[0]+'"/>';
                                str +='<img src="'+upload_return[0]+'" class="imgs" style="width:100px;margin:5px;"/>';
                                str +='</div><i class="icon-remove m_imgDel" ></i></div>';
                                $(showContainer).html(str);
                            }else if(showContainer==".img_container_catebanner"){
                                // alert(upload_return);
                                var str='';
                                str +='<div class="m_imgBox"><div class="m_img">';
                                str +='<input type="hidden" name="path_banner" class="path_banner" value="'+upload_return[0]+'"/>';
                                str +='<img src="'+upload_return[0]+'" class="imgs_path_banner" style="width:100px;margin:5px;"/>';
                                str +='</div><i class="icon-remove m_imgDel" ></i></div>';
                                $(showContainer).html(str);
                            }else if(showContainer==".img_container_cate_PCtop"){
                                // alert(upload_return);
                                var str='';
                                str +='<div class="m_imgBox"><div class="m_img">';
                                str +='<input type="hidden" name="path_category_PCtop" class="path_category_PCtop" value="'+upload_return[0]+'"/>';
                                str +='<img src="'+upload_return[0]+'" class="imgs_PCtop" style="width:100px;margin:5px;"/>';
                                str +='</div><i class="icon-remove m_imgDel" ></i></div>';
                                $(showContainer).html(str);
                            }else if(showContainer==".img_container_cate_PCleft"){
                                // alert(upload_return);
                                var str='';
                                str +='<div class="m_imgBox"><div class="m_img">';
                                str +='<input type="hidden" name="path_category_PCleft" class="path_category_PCleft" value="'+upload_return[0]+'"/>';
                                str +='<img src="'+upload_return[0]+'" class="imgs_PCleft" style="width:100px;margin:5px;"/>';
                                str +='</div><i class="icon-remove m_imgDel" ></i></div>';
                                $(showContainer).html(str);
                            }else if(showContainer==".show_images_multiple"){
                                var str='';
                                for(var i in upload_return){
                                    str +='<div class="m_imgBox"><div class="m_img">';
                                    str +='<input type="hidden" name="other_path[]" class="other_path" value="'+upload_return[i]+'"/>';
                                    str +='<img src="'+upload_return[i]+'" class="other_imgs" style="width:100px;margin:5px;"/>';
                                    str +='</div><i class="icon-remove m_imgDel" ></i></div>';

                                }
                                $(showContainer).append(str);
                            }else if(showContainer==".option_container"){
                                var str='';
                                str +='<input name="option_path[]" type="hidden" class="form-control option_path" value="'+upload_return[0]+'"/>';
                                str +='<img class="option_img" src="'+upload_return[0]+'" style="width:50px;"/>';

                                $(showContainer+"[index='"+index+"']").html(str);
                            }

                            $('.close').click();
                        }
                              
                    } else {
                        // 没有成功的图片，重设
                        state = 'done';
                        location.reload();
                    }
                    break;
            }

            updateStatus();
        }

        uploader.onUploadProgress = function( file, percentage ) {
            var $li = $('#'+file.id),
                $percent = $li.find('.progress span');

            $percent.css( 'width', percentage * 100 + '%' );
            percentages[ file.id ][ 1 ] = percentage;
            updateTotalProgress();
        };

        uploader.onFileQueued = function( file ) {
            fileCount++;
            fileSize += file.size;

            if ( fileCount === 1 ) {
                $placeHolder.addClass( 'element-invisible' );
                $statusBar.show();
            }

            addFile( file );
            setState( 'ready' );
            updateTotalProgress();
        };

        uploader.onFileDequeued = function( file ) {
            fileCount--;
            fileSize -= file.size;

            if ( !fileCount ) {
                setState( 'pedding' );
            }

            removeFile( file );
            updateTotalProgress();

        };

        uploader.on( 'all', function( type ) {
            var stats;
            switch( type ) {
                case 'uploadFinished':
                    setState( 'confirm' );
                    break;

                case 'startUpload':
                    setState( 'uploading' );
                    break;

                case 'stopUpload':
                    setState( 'paused' );
                    break;

            }
        });
        uploader.on( 'uploadSuccess', function(file,response) {
            // console.log(response);
            if(response!=0){
                upload_return.push(response);
            }

        });

        uploader.onError = function( code ) {
            alert( 'Eroor: ' + code );
        };

        $upload.unbind('click').bind('click', function() {
            if ( $(this).hasClass( 'disabled' ) ) {
                return false;
            }

            if ( state === 'ready' ) {
                uploader.upload();
            } else if ( state === 'paused' ) {
                uploader.upload();
            } else if ( state === 'uploading' ) {
                uploader.stop();
            }
        });

        $info.on( 'click', '.retry', function() {
            uploader.retry();
        } );

        $info.on( 'click', '.ignore', function() {
            alert( 'todo' );
        } );

        $upload.addClass( 'state-' + state );
        updateTotalProgress();
    });
  //end
}

//操作数据
function editFun(){
    //点击上传图片
    $('#li_upload').unbind('click').bind('click',function(){
        $('.get_url_photo').hide();
        $('.showUploader').show();
        $('.showphoto').hide();
    });

    // 点击提取网络图片
    $('#li_network').unbind('click').bind('click',function(){
        $('.get_url_photo').show();
        $('.showUploader').hide();
         $('.showphoto').hide();
    });

    //点击浏览图片
    $('#li_history_image').unbind('click').bind('click',function(){
        $.ajax({
            url:'/index.php/Admin/Images/getImages2',
            data:{uploadType:uploadType,page:page},
            async:false,
            success:function(data){
                var str = '';
                for(var i in data['data']){
                    
                    str+='<li class="img-item" attachid="565" title="" path="'+data['data'][i]['path']+data['data'][i]['img']+'">';
                    str+='<div class="img-container" style="background-image: url('+data['data'][i]['path']+data['data'][i]['img']+');">';
                    str+='<div class="select-status"><span></span></div>';
                    str+='</div>';
                    str+='<div class="btnClose" data-id="565">';
                    str+='<a href="#" path="'+data['data'][i]['path']+data['data'][i]['img']+'"><i class="fa fa-times"></i></a>';
                    str+='</div>';
                    str+='</li>';
                }
                $(".img-list").html(str);
                pagecount = data['page']['pagecount'];
                uploadFun(); 
                editFun();
            }
        });
        
        $('.showphoto').show();
        $('.get_url_photo').hide();
        $('.showUploader').hide();
       
    });


    // 确定使用
    $(".yes").unbind('click').bind('click',function(){
        // alert($('#networkurl').val());
        var url = $('#networkurl').val();
        $.ajax({
            url:'/index.php/Admin/Images/get_image_byurl',
            data:{url:url,uploadType:uploadType},
            type:'post',
            success:function(data){

            }
        });
    });

        /*
        浏览图片事件
         */
        //1. 确定年份
        var nowYear = (new Date).getFullYear(); //当前年份
        $('#select-year a').eq(1).attr('data-id',nowYear).text(nowYear+'年');
        $('#select-year a').eq(2).attr('data-id',nowYear-1).text(nowYear-1 +'年');
        $('#select-year a').eq(3).attr('data-id',nowYear-2).text(nowYear-2 +'年');
        $('#select-year a').eq(4).attr('data-id',nowYear-3).text(nowYear-3 +'年');

        // function init(){
        //2.选择年份
        $('#select-year a').unbind('click').bind('click',function(){
            // alert($(this).attr('data-id'));
            year = $(this).attr('data-id');
            $('#select-year a').each(function(){
                $(this).removeClass('btn-info');
            });
            $(this).addClass('btn-info');
            if(year==0){
                month = 0;
                $('#select-month a').each(function(){
                    $(this).removeClass('btn-info');
                });
                $('#select-month a').eq(0).addClass('btn-info');
            }
            reloadImages();
        });

        // 3.选择月份
        $('#select-month a').unbind('click').bind('click',function(){
            if(year!=0){
                month = $(this).attr('data-id');
                $('#select-month a').each(function(){
                    $(this).removeClass('btn-info');
                });
                $(this).addClass('btn-info');
            // alert(year);
                reloadImages();
            }
            // alert($(this).attr('data-id'));
        });
        //4.选择图片
        $('.img-container').unbind('click').bind('click',function(){
            var that = $(this).parent();
            // if($('.webuploader-element-invisible').prop('multiple')==false){
            if(isMultiple == false){

                $(".img-item").removeClass('img-item-selected');
                that.addClass('img-item-selected');
            }else{
                if(that.hasClass('img-item-selected')){
                    that.removeClass('img-item-selected');
                }else{
                    that.addClass('img-item-selected');
                }
            }
                
        });
    
            
        
        //5.删除图片
        $('.btnClose a').unbind('click').bind('click',function(){
            var path = $(this).attr("path");
            var that = $(this);
            // alert(id);
            $.ajax({
                url:'/index.php/Admin/Images/removeImages',
                data:{path:path},
                async:false,
                success:function(data){
                    if(data==1){
                        that.parent().parent().remove();
                        alert('删除成功');
                    }
                }
            });
        });
        
        //6.浏览图片确定
        $('.showphoto_yes').unbind('click').bind('click',function(){


            // console.log($('.webuploader-element-invisible').prop('multiple'));  //true 多选 false 单选
            
            var path ='';
            var path_arr = new Array();
            $('.img-item').each(function(){
                if($(this).hasClass('img-item-selected')){
                    if(isMultiple == false){
                        path = $(this).attr('path');
                    }else{
                        path_arr.push($(this).attr('path'));
                        // $('.show_images').append('<img width="200px;" height="80px" src="'+$(this).attr('path')+'"/>');
                        // $('.nav-pills li').removeClass('active');
                        // $('.iid').val($(this).attr('iid'));
                        // return false;
                    }
                }
            });
            if(path==''&&path_arr[0]==null){
                return false;
            }

            if(showContainer==".img_container"){
                // alert(uploadType);
                var str='';
                str +='<div class="m_imgBox"><div class="m_img">';
                str +='<input type="hidden" name="path" class="path" value="'+path+'"/>';
                str +='<img src="'+path+'" class="imgs" style="width:100px;margin:5px;"/>';
                str +='</div><i class="icon-remove m_imgDel" ></i></div>';
                

                    
                $(showContainer).html(str);
            }else if(showContainer==".img_container_catebanner"){
                var str='';
                str +='<div class="m_imgBox"><div class="m_img">';
                str +='<input type="hidden" name="path_banner" class="path_banner" value="'+path+'"/>';
                str +='<img src="'+path+'" class="imgs_banner" style="width:100px;margin:5px;"/>';
                str +='</div><i class="icon-remove m_imgDel" ></i></div>';
                $(showContainer).html(str);
            }else if(showContainer==".img_container_cate_PCtop"){
                // alert(upload_return);
                var str='';
                str +='<div class="m_imgBox"><div class="m_img">';
                str +='<input type="hidden" name="path_category_PCtop" class="path_category_PCtop" value="'+path+'"/>';
                str +='<img src="'+path+'" class="imgs_PCtop" style="width:100px;margin:5px;"/>';
                str +='</div><i class="icon-remove m_imgDel" ></i></div>';
                $(showContainer).html(str);
            }else if(showContainer==".img_container_cate_PCleft"){
                // alert(upload_return);
                var str='';
                str +='<div class="m_imgBox"><div class="m_img">';
                str +='<input type="hidden" name="path_category_PCleft" class="path_category_PCleft" value="'+path+'"/>';
                str +='<img src="'+path+'" class="imgs_PCleft" style="width:100px;margin:5px;"/>';
                str +='</div><i class="icon-remove m_imgDel" ></i></div>';
                $(showContainer).html(str);
            }else if(showContainer==".show_images_multiple"){
                var str='';
                for(var i in path_arr){
                    str +='<div class="m_imgBox"><div class="m_img">';
                    str +='<input type="hidden" name="other_path[]" class="other_path" value="'+path_arr[i]+'"/>';
                    str +='<img src="'+path_arr[i]+'" class="other_imgs" style="width:100px;margin:5px;"/>';
                    str +='</div><i class="icon-remove m_imgDel" ></i></div>';

                }
                $(showContainer).html(str);
            }else if(showContainer==".option_container"){
                var str='';
                str +='<input name="option_path[]" type="hidden" class="form-control option_path" value="'+path+'"/>';
                str +='<img class="option_img" src="'+path+'" style="width:50px;"/>';

                $(showContainer+"[index='"+index+"']").html(str);
            }  
            delImg(); 
        });
        // }    
    /*
     * 页数操作
     * 1.确定一共有几页
     * 2.每页显示多少图片
     */
    //用户需要自己实现的点击事件，参数为分页容器的id  
    
    $(function(){  
        paginationInit(); 
        $('#myPage').attr('pagenumber',page+1);  //当前页数
           $('#myPage').attr('totalpage',pagecount);  //总页数
        initPagination($('#myPage'));      

    });
}
function reloadImages(){
    $.ajax({
        url:'/index.php/Admin/Images/getImages2',
        data:{uploadType:uploadType,year:year,month:month,page:page},
        async:false,
        success:function(data){
            var str = '';
            if(data!=null){
                for(var i in data['data']){
                    
                    str+='<li class="img-item" attachid="565" title="" path="'+data['data'][i]['path']+data['data'][i]['img']+'">';
                    str+='<div class="img-container" style="background-image: url('+data['data'][i]['path']+data['data'][i]['img']+');">';
                    str+='<div class="select-status"><span></span></div>';
                    str+='</div>';
                    str+='<div class="btnClose" data-id="565">';
                    str+='<a href="#" path="'+data['data'][i]['path']+data['data'][i]['img']+'"><i class="fa fa-times"></i></a>';
                    str+='</div>';
                    str+='</li>';
                }
                $(".img-list").html(str);
                $(function(){  
                    paginationInit(); 
                    $('#myPage').attr('pagenumber',parseInt(data['page']['nowpage'])+1);  //当前页数
                    $('#myPage').attr('totalpage',data['page']['pagecount']);  //总页数
                    initPagination($('#myPage'));      

                });
                editFun();
            }
                
        }
    });
}

function delImg(){
    $('.m_imgDel').unbind('click').bind('click',function (e){
        console.log(e.target);
        $(e.target).parent().remove();
    }) ;
}
delImg();
function paginationClick(pagination_id){  
  // var pagenumber = $('#'+pagination_id+'').attr('pagenumber');  
  // var totalpage = $('#'+pagination_id+'').attr('totalpage');  
  //   console.log('分页测试：当前id：'+pagination_id+' , pagenumber:'+pagenumber+' , totalpage:'+totalpage);
  page = parseInt($('#'+pagination_id+'').attr('pagenumber'))-1;
  reloadImages();
}  
    
                    