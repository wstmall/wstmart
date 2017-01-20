$(function() {
	WST.dropDownLayer(".wst-shop-code",".wst-shop-codes");
	$(".ck-slide-wrapper img").width(1200);
	$('.ck-slide').ckSlide({
		autoPlay: true,
		time:5000,
		isAnimate:true,
		dir: 'x'
	});
	$(".wst-shop-goimg").hover(function(){
		$(this).find(".js-cart").slideDown(100);
	},function(){
		$(this).find(".js-cart").slideUp(100);
	});
});
function dropDown(obj,id){
    if( $(obj).attr('class').indexOf('js-shop-plus') > -1 ){
    	$(obj).removeClass('js-shop-plus').addClass('js-shop-redu');
    	$('.tree_'+id).slideUp();
    }else{
    	$(obj).removeClass('js-shop-redu').addClass('js-shop-plus');
    	$('.tree_'+id).slideDown();
    }
}
function searchShopsGoods(obj){
	var mdesc = $('#mdesc').val();
	if($('#msort').val() != obj)mdesc = 0;
	var msort = obj;
	var params = new Array();
	params.push("shopId=" + $("#shopId").val());
	params.push("msort=" + obj);
	params.push("mdesc=" + ((mdesc=="0")?"1":"0"));
	params.push("sprice=" + $("#sprice").val());
	params.push("eprice=" + $("#eprice").val());
	params.push("ct1=" + $("#ct1").val());
	params.push("ct2=" + $("#ct2").val());
	params.push("goodsName=" + $("#goodsName").val());
	
	document.location.href = WST.U('home/shops/home',params.join('&'));
}