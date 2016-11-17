/*店长推荐*/
function gpanelOver2(obj){
var sid = $(obj).attr("id");
var ids = sid.split("_");
var preid = ids[0]+"_"+ids[1];

$("li[id^="+preid+"_]").removeClass("j-s-rec-selected");

$("#"+sid).addClass("j-s-rec-selected");

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


$(function(){
	WST.slides();
	WST.dropDownLayer(".wst-shop-code",".wst-shop-codes");
	var qr = qrcode(10, 'M');
	var url = window.location.href;
	qr.addData(url);
	qr.make();
	$('#qrcode').html(qr.createImgTag());
});
WST.slides = function(){
	var slide = $('#s-wst-slide'), li = slide.find("li");
	var slidecontrols = $('.s-wst-slide-controls').eq(0), 
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
}

function searchShopsGoods(){
	var params = new Array();
	params.push("shopId=" + $("#shopId").val());
	params.push("goodsName=" + $("#goodsName").val());
	document.location.href = WST.U('home/shops/home',params.join('&'));
}
$(function(){
	$(".s-goods").hover(function(){
		$(this).find(".s-add-cart").slideDown(100);
	},function(){
		$(this).find(".s-add-cart").slideUp(100);
	});
})


$(function(){
	$('.shop-cat1').hover(function(){
		$(this).addClass('ct1-hover');
		var cid = $(this).attr('cid');

		var h = 66.3*cid+'px';
		$('.cid'+cid).css('top',h);
		$('.cid'+cid).show();
	},function(){
		$(this).removeClass('ct1-hover');
		var cid = $(this).attr('cid');
		$('.cid'+cid).hide();
	})


	$('.shop-cat2').hover(function(){
		var cid = $(this).attr('cid');
		$('#ct1-'+cid).addClass('ct1-hover');
		$(this).show();
	},function(){
		var cid = $(this).attr('cid');
		$('#ct1-'+cid).removeClass('ct1-hover');
		$(this).hide();
	});



	$('.s-cat').hover(function(){
	  $('.s-cat-head').addClass('s-cat-head-hover');
	  $(this).show();
	},function(){
	  $('.s-cat-head').removeClass('s-cat-head-hover');
	  $(this).hide();
	});

	
	$('.s-cat-head').hover(function(){
	  $(this).addClass('s-cat-head-hover');
	  $('.s-cat').show();
	},function(){
	  $(this).removeClass('s-cat-head-hover');
	  $('.s-cat').hide();
	})


});