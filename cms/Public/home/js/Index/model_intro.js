$(window).ready(function(){
	// 左侧悬浮框事件
	$('.black').on('tap',function(){
        $(".blackul").slideDown(200);
        $(".black-backdrop").fadeIn(200);
    });

    $(".black-backdrop").on('touchstart',function(){
        $(".blackul").slideUp(200);
        $(".black-backdrop").fadeOut(200);
    });
});

