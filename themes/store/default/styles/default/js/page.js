
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


 $(function(){
   var bd=$('#bd');
   var bd_li = bd.find('li');
   var bdlength = $("#bdlength");
   var bd_li_length = bd_li.length;
   var hdprev=$('#hdprev');
   var hdnum=$('#hdnum');
   var hdnext = $('#hdnext');
   var j=0;
   var timer=null;
   bdlength.get(0).innerHTML = bd_li_length;
   for(var i=0;i<bd_li_length; i++){
     bd_li[i].index = i;
   }
   hdprev.click(function(){
     j--;
     if(j==-1){
       j=1
     }
     hdnum.get(0).innerHTML=j+1;
     chang(j);
   });
   hdnext.click(function(){
     j++;
     if(j==2){j=0};
     hdnum.get(0).innerHTML=j+1;
     chang(j);
   });
   function autoplay(){
     j++;
     if(j>bd_li_length-1){
       j = 0;
     }
     chang(j);
   }
   function chang(j){
     hdnum.get(0).innerHTML=j+1;
     bd_li.css({"display":"none","zIndex":0,"-webkit-transition": 0.5+"s"});
     bd_li.stop(true);
     bd_li.eq(j).css({"display":"block","zIndex":5,"-webkit-transition":0.5+"s"});
   }
   timer = setInterval(autoplay,4000);
   bd.hover(function(){clearInterval(timer)},function(){timer = setInterval(autoplay,4000);});
 });
