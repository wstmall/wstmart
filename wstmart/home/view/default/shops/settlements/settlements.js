$(function(){
	$('#tab').TabPanel({tab:0,callback:function(tab){
		switch(tab){
			case 0:getQueryPage(0);break;
			case 1:getUnSettledOrderPage(0);break;
			case 2:getSettleOrderPage(0);break;
		}
	}});
});
function view(val){
    location.href=WST.U('home/settlements/view','id='+val);
}
function getQueryPage(p){
	var params = {};
	params.page = p;
	params.settlementNo = $.trim($('#settlementNo_0').val());
	params.isFinish = $('#isFinish_0').val();
	$.post(WST.U('home/settlements/pageQuery'),params,function(data,textStatus){
		var json = WST.toJson(data);
	    if(json.status==1){
			var gettpl = document.getElementById('tblist0').innerHTML;
	       	laytpl(gettpl).render(json.data.Rows, function(html){
	       	    $('#tbody0').html(html);
	       	});
	       	if(json.data.totalPage>1){
	       		laypage({
			        cont: 'pager_0', 
			        pages:json.data.TotalPage, 
			        curr: json.CurrentPage,
			        skin: '#e23e3d',
			        groups: 3,
			        jump: function(e, first){
			        	if(!first){
			        		getQueryPage(e.curr);
			        	}
			        }
			    });
	       	}else{
	       		$('#pager_0').empty();
	       	}
	    }else{
	    	$('#pager_0').empty();
	    }
	});
}
function settlement(){
	var ids = WST.getChks('.chk_1');
	if(ids.length==0){
		WST.msg('请选择要结算的订单!',{icon:2});
		return;
	}
	var load = WST.load({msg:'正在提交申请，请稍后...'});
	WST.confirm({content:'您确定要申请结算这些订单吗？',yes:function(){
        $.post(WST.U('home/settlements/settlement'),{ids:ids.join(',')},function(data,textStatus){
			layer.close(load);
			var json = WST.toJson(data);
		    if(json.status==1){
	            WST.msg(json.msg,{icon:1},function(){
	            	getUnSettledOrderPage(0);
	            });
		    }else{
		    	WST.msg(json.msg);
		    }
		});
	}});
}
function getUnSettledOrderPage(p){
    var params = {};
	params.page = p;
	params.orderNo = $.trim($('#orderNo_1').val());
	$.post(WST.U('home/settlements/pageUnSettledQuery'),params,function(data,textStatus){
		var json = WST.toJson(data);
	    if(json.status==1){
			var gettpl = document.getElementById('tblist1').innerHTML;
	       	laytpl(gettpl).render(json.data.Rows, function(html){
	       	    $('#tbody1').html(html);
	       	});
	       	if(json.data.TotalPage>1){
	       		laypage({
			        cont: 'pager_1', 
			        pages:json.data.TotalPage, 
			        curr: json.CurrentPage,
			        skin: '#e23e3d',
			        groups: 3,
			        jump: function(e, first){
			        	if(!first){
			        		getUnSettledOrderPage(e.curr);
			        	}
			        }
			    });
	       	}else{
	       		$('#pager_1').empty();
	       	}
	    }else{
	    	$('#pager_1').empty();
	    }
	});
}
function getSettleOrderPage(p){
    var params = {};
	params.page = p;
	params.orderNo = $.trim($('#orderNo_2').val());
	params.settlementNo = $.trim($('#settlementNo_2').val());
	params.isFinish = $.trim($('#isFinish_2').val());
	$.post(WST.U('home/settlements/pageSettledQuery'),params,function(data,textStatus){
		var json = WST.toJson(data);
	    if(json.status==1){
			var gettpl = document.getElementById('tblist2').innerHTML;
	       	laytpl(gettpl).render(json.data.Rows, function(html){
	       	    $('#tbody2').html(html);
	       	});
	       	if(json.data.TotalPage>1){
	       		laypage({
			        cont: 'pager_2', 
			        pages:json.data.TotalPage, 
			        curr: json.data.CurrentPage,
			        skin: '#e23e3d',
			        groups: 3,
			        jump: function(e, first){
			        	if(!first){
			        		getSettleOrderPage(e.curr);
			        	}
			        }
			    });
	       	}else{
	       		$('#pager_2').empty();
	       	}
	    }else{
	    	$('#pager_2').empty();
	    }
	});
}