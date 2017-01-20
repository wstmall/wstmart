//筛选分类
function screenCat(id){
	location.href=WST.U('home/shops/shopStreet','id='+id);
}
$(function(){
	var goodsNum = $(this).find("div[class^='wst-shopstr-shopl']").length;
	for(var i=1;i<=goodsNum;++i){
    	$("#js-goods"+i).als({
    		visible_items: 6,
    		scrolling_items: 1,
    		orientation: "horizontal",
    		circular: "yes",
    		autoscroll: "no",
    		start_from: 2
    	});
	}
	WST.dropDownLayer(".j-score",".j-scores");
});