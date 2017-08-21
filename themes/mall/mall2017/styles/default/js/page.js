
$(window).scroll(function(){
    height = $(window).scrollTop();
    if(height > 200){
      $('.header_fixed').css({'animation':'fadeIn .3s ease-in both'}).fadeIn();
    }else{
      $('.header_fixed').fadeOut();
    };
})
$("#goTopBtn").click(function(){
   var sc=$(window).scrollTop();
   $('body,html').animate({scrollTop:0},200);
 });



jQuery("#header").slide({ type:"menu",  titCell:"dd", targetCell:"ul", delayTime:0,defaultPlay:false,returnDefault:true  });
