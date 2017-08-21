$(function(){
	$('#gotop').click(function(){
		$('html,body').animate({scrollTop: '0px'}, 0);
	});
	
	$(window).scroll(function(){
		if($(window).scrollTop()>0){
			$("#gotop").css('display', 'block');
		}else{
			$("#gotop").css('display', 'none');
		}
	});
	
	$('.mall-nav .allcategory').hover(function(){
	   $(this).find('.allcategory-list').show();
   },function(){
	   $(this).find('.allcategory-list').hide();
   });
   
   $('.mini-cart').hover(function(){
	   $(this).children('.mini-cart-content').toggle();
   },function(){
	   $(this).children('.mini-cart-content').hide();
   });

   $('.city-select').hover(function(){
	   $(this).children('.menu-bd').show();
   },function(){
	   $(this).children('.menu-bd').hide();
   });

   $(".top-search .search-type").hover(function(){
      $(this).addClass("hover");      
   },function(){
      $(this).removeClass("hover");
   });

   $(".top-search .search-type li:eq(1)").click(function(){
      var act0=$(".top-search .search-type li:eq(0)").attr("act");
	  var text0=$(".top-search .search-type li:eq(0)").html();
	  $("#form-search-act").attr("value",$(this).attr("act"));
	  $(".top-search .search-type li:eq(0)").attr("act",$(this).attr("act"));
      $(".top-search .search-type li:eq(0)").html($(this).html());
	  $(".top-search .search-type li:eq(1)").attr("act",act0);
      $(".top-search .search-type li:eq(1)").html(text0);   
   });
		
   $(".top-search .search-type li").click(function(){
	 /*
	   $(".top-search li").each(function(){
		   $(this).removeClass("current");
	   });
	   $(this).addClass("current");
	   $(".top-search-box input[name='act']").val(this.id);
	 */
	  
	   if($.trim($(".top-search-box input[name='keyword']").val())==""){
		   $(".top-search-box input[name='keyword']").attr("class","");
		   $(".top-search-box input[name='keyword']").addClass($("#form-search-act").val()+"_bj kw_bj keyword");
	   }
   }); 
   
   $(".top-search-box input[name='keyword']").focus(function(){
	   $(this).attr("class","keyword");
   }).blur(function(){
	   if($.trim($(this).val())=="") {
		   $(this).attr("class",$("#form-search-act").val()+"_bj kw_bj keyword");
	   }
   });
   
   $('.login-register .form .input').focus(function(){
		$(this).removeClass('hover');
		$(this).addClass('focus');
	});
	$('.login-register .form .input').hover(function(){
		$(this).removeClass('hover');
		$(this).addClass('hover');
	},function(){
		$(this).removeClass('hover');
	});
	$('.login-register .form .input').blur(function(){
		$(this).removeClass('hover');
		$(this).removeClass('focus');
	});	
})

function poshytip_message(obj,className,showOn,alignTo,alignX,offsetX,offsetY){
	if(obj==undefined) return;
	if(className==undefined) className = 'tip-yellowsimple';
	if(showOn==undefined) showOn = 'focus';
	if(alignTo==undefined) alignTo = 'target';
	if(alignX==undefined) alignX = 'inner-left';
	if(offsetX==undefined) offsetX = 0;
	if(offsetY==undefined) offsetY = 5;
		
	obj.poshytip({
		className: className,
		showOn: showOn,
		alignTo: alignTo,
		alignX: alignX,
		offsetX: offsetX,
		offsetY: offsetY
	});
}
 
//解决图片变形问题
function showImg(img,imgParent){
	// 图片父级的宽高
	var imgParentWidth = $(imgParent).width(),
		imgParentHeight = $(imgParent).height(),
		imgArr = [];//用来存放图片宽高比属性
	//console.log(imgParentWidth+'------'+imgParentHeight);
	//console.log($(img).length)
	$(img).each(function(i){
		var imgWidth = $(this).width(),
			imgHeight = $(this).height();
		if(imgWidth >= imgHeight){
			//console.log(imgWidth+'-----'+imgHeight)
			$(this).css({
				'width':imgParentWidth,
				'height':imgParentWidth/imgWidth*imgHeight,
				'margin-top':(imgParentWidth-(imgParentWidth/imgWidth*imgHeight))/2
			});
			//imgArr[i] = {width:1,height:imgHeight/imgWidth,imgW:imgWidth,imgH:imgHeight};
		}else{
			//console.log(imgWidth+'-----'+imgHeight)
			$(this).css({
				'width':imgParentHeight/imgHeight*imgWidth,
				'height':imgParentHeight,
				'margin-left':(imgParentHeight-(imgParentHeight/imgHeight*imgWidth))/2
			});
			//imgArr[i] = {width:imgWidth/imgHeight,height:1,imgW:imgWidth,imgH:imgHeight};
			console.log((imgParentHeight-(imgParentHeight/imgHeight*imgWidth))/2)
		}
	})
}








