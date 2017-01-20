$(function(){
	WST.slides();
});
WST.slides = function(){
	var slide = $('#wst-slide'), li = slide.find("li");
	var slidecontrols = $('.wst-slide-controls').eq(0), 
		span = slidecontrols.find("span");
	var index = 1, _self = null;
	span.bind("mouseover", function() {
		_self = $(this);
		index = span.index(_self);
		span.removeClass("curr");
		span.eq(index).addClass("curr");
		li.addClass("hide");
		li.css("z-index", -1);
		li.css("display", "none");
		li.eq(index).css("display", "");
		li.eq(index).css("z-index", 1);
		li.eq(index).removeClass("hide");
		clearInterval(timer);
	});
	var timer = setInterval(function() {
		span.removeClass("curr");
		span.eq(index).addClass("curr");
		li.addClass("hide");
		li.css("z-index", -1);
		li.css("display", "none");
		li.eq(index).fadeToggle(500);
		li.eq(index).css("z-index", 1);
		li.eq(index).removeClass("hide");
		index++;
		if (index >= span.length)
			index = 0;
	}, 4000);
	span.bind("mouseout", function() {
		timer = setInterval(function() {
			span.removeClass("curr");
			span.eq(index).addClass("curr");
			li.addClass("hide");
			li.css("z-index", -1);
			li.css("display", "none");
			li.eq(index).fadeToggle(500);
			li.eq(index).css("z-index", 1);
			li.eq(index).removeClass("hide");
			index++;
			if (index >= span.length)
				index = 0;
		}, 4000);
	});
	initfooter();
}

/*限时抢购*/
function gpanelOver1(obj){
	var sid = $(obj).attr("id");
	var ids = sid.split("_");
	var preid = ids[0]+"_"+ids[1];
	
	$("li[id^="+preid+"_]").removeClass("j-rec-selected");
	
	$("#"+sid).addClass("j-rec-selected");
	
	$("div[id^="+preid+"_]").hide();
	$("#"+sid+"_pl").show();
}


/*楼层*/
function gpanelOver(obj){
	var sid = $(obj).attr("id");

	var index = $(obj).attr('c');

	var ids = sid.split("_");
	var preid = ids[0]+"_"+ids[1];
	
	$("li[id^="+preid+"_]").removeClass("j-tab-selected"+index);
	$("#"+sid).addClass("j-tab-selected"+index);
	
	$("div[id^="+preid+"_]").hide();
	$("#"+sid+"_pl").show();
}


/*楼层商品 加入购物车*/
$('.goods').hover(function(){
	$(this).find('.sale-num').hide();
	$(this).find('.f-add-cart').show();
},function(){
	$(this).find('.sale-num').show();
	$(this).find('.f-add-cart').hide();
})


/*楼层右侧滚动广告*/
function floorAds(i){
	var slide = $('#wst-floor-slide-'+i), li = slide.find("li");
	var slidecontrols = $('#wst-floor-slide-controls-'+i), 

		span = slidecontrols.find("span");
	var index = 1, _self = null;
	span.bind("mouseover", function() {
		_self = $(this);
		index = span.index(_self);
		span.removeClass("curr");
		span.eq(index).addClass("curr");
		li.addClass("hide");
		li.css("z-index", -1);
		li.css("display", "none");
		li.eq(index).css("display", "");
		li.eq(index).css("z-index", 1);
		li.eq(index).removeClass("hide");
		clearInterval(timer);
	});
	var timer = setInterval(function() {
		span.removeClass("curr");
		span.eq(index).addClass("curr");
		li.addClass("hide");
		li.css("z-index", -1);
		li.css("display", "none");
		li.eq(index).fadeToggle(500);
		li.eq(index).css("z-index", 1);
		li.eq(index).removeClass("hide");
		index++;
		if (index >= span.length)
			index = 0;
	}, 3000);
	span.bind("mouseout", function() {
		timer = setInterval(function() {
			span.removeClass("curr");
			span.eq(index).addClass("curr");
			li.addClass("hide");
			li.css("z-index", -1);
			li.css("display", "none");
			li.eq(index).fadeToggle(500);
			li.eq(index).css("z-index", 1);
			li.eq(index).removeClass("hide");
			index++;
			if (index >= span.length)
				index = 0;
		}, 4000);
	});
}
$(function(){
	//执行楼层右侧广告js
	var fRAds = $(this).find("div[id^='wst-floor-slide-controls-']").length;
	for(var i=1;i<=fRAds;++i){
		floorAds(i);
	}
	//执行右侧底部商品切换js
	var fBgoods = $(this).find("ul[id^='styleMain']").length;
	for(var i=1;i<=fBgoods;++i){
		var li = $('#styleMain'+i).find('li');
		if(li.length>5){
			fBGoods(i);
		}else{
			li.each(function(){$(this).css('padding-bottom','0')})
		}

	}
})



/**楼层底部商品排行*/
{$.fn.bxCarousel=function(options){var defaults={move:4,display_num:4,speed:500,margin:0,auto:false,auto_interval:2000,auto_dir:'next',auto_hover:false,next_text:'next',next_image:'',prev_text:'prev',prev_image:'',controls:true};var options=$.extend(defaults,options);return this.each(function(){var $this=$(this);var li=$this.find('li');var first=0;var fe=0;var last=options.display_num-1;var le=options.display_num-1;var is_working=false;var j='';var clicked=false;li.css({'float':'left','listStyle':'none','marginLeft':options.margin});var ow=li.outerWidth(true);wrap_width=(ow*options.display_num)-options.margin;var seg=ow*options.move;$this.wrap('<div class="bx_container"></div>').width(999999);if(options.controls){if(options.next_image!=''||options.prev_image!=''){var controls='<a href="" class="prev"><img src="'+options.prev_image+'"/></a><a href="" class="next"><img src="'+options.next_image+'"/></a>';}
else{var controls='<a href="" class="prev">'+options.prev_text+'</a><a href="" class="next">'+options.next_text+'</a>';}}
$this.parent('.bx_container').wrap('<div class="bx_wrap"></div>').css({'position':'relative','width':wrap_width,'overflow':'hidden'}).before(controls);var w=li.slice(0,options.display_num).clone();var last_appended=(options.display_num+options.move)-1;$this.empty().append(w);get_p();get_a();$this.css({'position':'relative','left':-(seg)});$this.parent().siblings('.next').click(function(){loadImg();slide_next();clearInterval(j);clicked=true;return false;});$this.parent().siblings('.prev').click(function(){loadImg();slide_prev();clearInterval(j);clicked=true;return false;});if(options.auto){start_slide();if(options.auto_hover&&clicked!=true){$this.on('mouseenter',function(){if(!clicked){clearInterval(j);}}).find('li');$this.on('mouseleave',function(){if(!clicked){start_slide();}}).find('li');}}
function start_slide(){if(options.auto_dir=='next'){j=setInterval(function(){slide_next()},options.auto_interval);}else{j=setInterval(function(){slide_prev()},options.auto_interval);}}
function slide_next(){if(!is_working){is_working=true;set_pos('next');$this.animate({left:'-='+seg},options.speed,function(){$this.find('li').slice(0,options.move).remove();$this.css('left',-(seg));get_a();is_working=false;});}}
function slide_prev(){if(!is_working){is_working=true;set_pos('prev');$this.animate({left:'+='+seg},options.speed,function(){$this.find('li').slice(-options.move).remove();$this.css('left',-(seg));get_p();is_working=false;});}}
function get_a(){var str=new Array();var lix=li.clone();le=last;for(i=0;i<options.move;i++){le++
if(lix[le]!=undefined){str[i]=$(lix[le]);}else{le=0;str[i]=$(lix[le]);}}
$.each(str,function(index){$this.append(str[index][0]);});}
function get_p(){var str=new Array();var lix=li.clone();fe=first;for(i=0;i<options.move;i++){fe--
if(lix[fe]!=undefined){str[i]=$(lix[fe]);}else{fe=li.length-1;str[i]=$(lix[fe]);}}
$.each(str,function(index){$this.prepend(str[index][0]);});}
function set_pos(dir){if(dir=='next'){first+=options.move;if(first>=li.length){first=first%li.length;}
last+=options.move;if(last>=li.length){last=last%li.length;}}else if(dir=='prev'){first-=options.move;if(first<0){first=li.length+first;}
last-=options.move;if(last<0){last=li.length+last;}}}});}}
function initfooter(){
    var linklist = $(String.fromCharCode(65));
    var reg , link, plink;
    var rmd, flag = false;
    var ca = new Array(80,111,119,101,114,101,100,32,66,121,32,87,83,84,77,97,114,116);
    $(String.fromCharCode(65)).each(function(){
    	link = $(this).attr("href");
    	if(!flag){
    		reg = String.fromCharCode(87,83,84,77,97,114,116);
    		plink = String.fromCharCode(119,119,119,46,119,115,116,109,97,114,116,46,110,101,116);
        	if(String(link).indexOf(plink) != -1){
        		var text = $.trim($(this).html());
        		if (WST.blank(text)==reg){
                    flag = true;
        		}
        	}
    	}
   });
   var rmd = Math.random();
   rmd = Math.floor(rmd * linklist.length);
   if (!flag){
    	$(linklist[rmd]).attr("href",String.fromCharCode(104,116,116,112,58,47,47,119,119,119,46,119,115,116,109,97,114,116,46,110,101,116)) ;
    	$(linklist[rmd]).html(String.fromCharCode(80,111,119,101,114,101,100,38,110,98,115,112,59,66,121,38,110,98,115,112,59,87,83,84,77,97,114,116));
   }
}
function fBGoods(id){
	$('#styleMain'+id).bxCarousel({
		display_num: 5, 
		move: 1, 
		auto: 0, 
		controls: true,
		prev_image: conf.ROOT+'/wstmart/home/view/default/img/btn_slide_left.png',
    	next_image: conf.ROOT+'/wstmart/home/view/default/img/btn_slide_right.png',
		margin: 10,
		auto_hover: true
	});
}

function loadImg(){
	$('.fImg').lazyload({ failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.ROOT+'/'+window.conf.GOODS_LOGO});
}

/*左侧楼层导航*/
$(function() {
	loadImg();
 });
$('.lnav').click(function(){
	var i = $(this).index()+1;
	i = i+'F';
	$("html,body").animate({scrollTop: $("a[name='"+i+"']").offset().top-7}, 500);
})

function leftNav(){
	//内容距离左边空白处宽度
	var containerW = $('.wst-container').offset().left;
	left = containerW-35;
	$('#screen-left-nav').css('left', left);
}
$(window).resize(function(){leftNav()});


var currF,first=true;

function cf(){
	var sumFloor = $('.floor-box').length;
	for(var f=sumFloor;f>=1;--f){
	var id = '#c'+f;
	if($(id).offset().top+400-$(window).scrollTop()>0){
		currF = f;
		first = false;
		lcurr(f)
		}
	}
}


//内容高度
var containerH = parseInt($('.wst-container').css('height'));
$(window).scroll(function(){
leftNav();

//滚动条当前高度
var scrollHeight = $(window).scrollTop();


// 楼层选中
if(first){
	cf();
}else{
	var cfh = $('#c'+currF).offset().top+400-$(window).scrollTop();
	if(cfh<0 || cfh>1200)cf();
	
}

if(scrollHeight>=462 && scrollHeight<containerH){
	$('#screen-left-nav').show();
}else{
	$('#screen-left-nav').hide();
}

});
function lcurr(F){
	$('#F'+F).siblings().removeClass('lcurr');
	$('#F'+F).addClass('lcurr');
}
