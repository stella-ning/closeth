$("#goTopBtn").click(function(){
   var sc=$(window).scrollTop();
   $('body,html').animate({scrollTop:0},200);
 });

jQuery(".mod_tab").slide({ titCell:".mod_tab_head li", mainCell:".mod_tab_info",delayTime:0 });
jQuery("#header").slide({ type:"menu",  titCell:"dd", targetCell:"ul", delayTime:0,defaultPlay:false,returnDefault:true  });

$(window).scroll(function(){
    height = $(window).scrollTop();
    if(height > 550){
      $('.header_fixed').fadeIn();
    }else{
      $('.header_fixed').fadeOut();
    };
})


/**
 * Created by liusongjin on 15-4-4.
 */
$(function () {
    $(window).scroll(function () {
        var menus = $("#menus");
        var scrollTop = $(document).scrollTop();
        var documentHeight = $(document).height();
        var windowHeight = $(window).height();
        var contentItems = $("#contents").find(".items");
        var currentItem = "";
        if(scrollTop > 750){
          menus.show();
        }else{
          menus.hide();
        }
        if (scrollTop+windowHeight==documentHeight) {
            currentItem= "#" + contentItems.last().attr("id");
            console.log(currentItem)
        }else{
            contentItems.each(function () {
                var contentItem = $(this);
                var offsetTop = contentItem.offset().top;
                console.log(offsetTop);
                if (scrollTop > offsetTop - 100) {//此处的200视具体情况自行设定，因为如果不减去一个数值，在刚好滚动到一个div的边缘时，菜单的选中状态会出错，比如，页面刚好滚动到第一个div的底部的时候，页面已经显示出第二个div，而菜单中还是第一个选项处于选中状态
                    currentItem = "#" + contentItem.attr("id");
                }
            });
        }
        if (currentItem != $("#menus").find(".current").attr("href")) {
            $("#menus").find(".current").removeClass("current");
            $("#menus").find("[href=" + currentItem + "]").addClass("current");
        }
    });
});
