$(function(){
	$('.h-cat>span').click(function(t){
		var li = $(this).parent();
		if( li.find('ul').is(":hidden") ){
			li.addClass('h-show').removeClass('h-hide');
			li.siblings().addClass('h-hide').removeClass('h-show');
		}else{
			li.addClass('h-hide').removeClass('h-show');
		}
		
	});
});
function solve(status){
	$.post(WST.U('home/helpcenter/recordSolve'),{'status':status,'id':$('#artId').val()},function(data,dataStatus){
		var json = WST.toJson(data);
		if(json.status==1){
			if(status==1)
				$('.h-record').html('感谢您的评价!');
			else{
				$('.h-record').html('请联系客服!');
			}
		}
	});
}