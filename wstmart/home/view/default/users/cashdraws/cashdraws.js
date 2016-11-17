$(function () {
	$('#tab').TabPanel({tab:0,callback:function(tab){
		switch(tab){
		   case 0:pageQuery(0);break;
		   case 1:pageConfigQuery(0);break;
		}	
	}})
});
var isSetPayPwd = 1;
function getUserMoney(){
	$.post(WST.U('home/users/getUserMoney'),{},function(data,textStatus){
		var json = WST.toJson(data);
		if(json.status==1){
			$('#userMoney').html('¥'+json.data.userMoney);
			$('#lockMoney').html('¥'+json.data.lockMoney);
			isSetPayPwd = json.data.isSetPayPwd;
		}
	});
}
function pageQuery(p){
	var tips = WST.msg('正在获取数据，请稍后...',{time:600000000});
	var params = {};
	params.page = p;
	$.post(WST.U('home/cashdraws/pageQuery'),params,function(data,textStatus){
		layer.close(tips);
	    var json = WST.toJson(data);
	    if(json.status==1){
	    	json = json.data;
		    var gettpl = document.getElementById('draw-list').innerHTML;
		    laytpl(gettpl).render(json.Rows, function(html){
		       	$('#draw-page-list').html(html);
		    });
		    if(json.TotalPage>1){
		       	laypage({
			        cont: 'draw-pager', 
			        pages:json.TotalPage, 
			        curr: json.CurrentPage,
			        skin: '#e23e3d',
			        groups: 3,
			        jump: function(e, first){
			        	if(!first){
			        		pageQuery(e.curr,type);
			        	}
			        } 
			    });
		     }else{
		       	 $('#draw-pager').empty();
		     }
	    }
	});
}
var w;
function toDrawMoney(){
	if(isSetPayPwd==0){
		WST.msg('您尚未设置支付密码，请先设置支付密码',{icon:2});
		return;
	}
    var tips = WST.msg('正在获取数据，请稍后...',{time:600000000});
	$.post(WST.U('home/cashdraws/toEdit'),{},function(data,textStatus){
		layer.close(tips);
		w = WST.open({
		    type: 1,
		    title:"申请提现",
		    shade: [0.6, '#000'],
		    border: [0],
		    content: data,
		    area: ['550px', '250px'],
		    offset: '100px'
		});
	});
}
function drawMoney(){
	$('#drawForm').isValid(function(v){
		if(v){
			var params = WST.getParams('.j-ipt');
			var tips = WST.msg('正在提交数据，请稍后...',{time:600000000});
			$.post(WST.U('home/cashdraws/drawMoney'),params,function(data,textStatus){
				layer.close(tips);
			    var json = WST.toJson(data);
			    if(json.status==1){
		            WST.msg(json.msg,{icon:1},function(){
		            	pageQuery(0);
		            	getUserMoney();
		            	layer.close(w);
		            });
			    }else{
			    	WST.msg(json.msg,{icon:2});
			    }
			});
		}
	});
}
function layerclose(){
  layer.close(w);
}

function pageConfigQuery(p){
	var tips = WST.msg('正在获取数据，请稍后...',{time:600000000});
	var params = {};
	params.page = p;
	$.post(WST.U('home/cashconfigs/pageQuery'),params,function(data,textStatus){
		layer.close(tips);
	    var json = WST.toJson(data);
	    if(json.status==1){
	    	json = json.data;
		    var gettpl = document.getElementById('config-list').innerHTML;
		    laytpl(gettpl).render(json.Rows, function(html){
		       	$('#config-page-list').html(html);
		    });
		    if(json.TotalPage>1){
		       	laypage({
			        cont: 'config-pager', 
			        pages:json.TotalPage, 
			        curr: json.CurrentPage,
			        skin: '#e23e3d',
			        groups: 3,
			        jump: function(e, first){
			        	if(!first){
			        		pageConfigQuery(e.curr);
			        	}
			        } 
			    });
		     }else{
		       	 $('#config-pager').empty();
		     }
	    }
	});
}

function toEditConfig(id){
	var tips = WST.msg('正在获取数据，请稍后...',{time:600000000});
	$.post(WST.U('home/cashconfigs/toEdit','id='+id),{},function(data,textStatus){
		layer.close(tips);
		w = WST.open({
		    type: 1,
		    title:((id>0)?"编辑":"新增")+"提现账号",
		    shade: [0.6, '#000'],
		    border: [0],
		    content: data,
		    area: ['600px', '250px'],
		    offset: '100px'
		});
	});
} 
function editConfig(){
	$('#configForm').isValid(function(v){
		if(v){
			var params = WST.getParams('.j-ipt');
			params.accAreaId = WST.ITGetAreaVal('j-areas');
			var tips = WST.msg('正在提交数据，请稍后...',{time:600000000});
			$.post(WST.U('home/cashconfigs/'+((params.id>0)?'edit':'add')),params,function(data,textStatus){
				layer.close(tips);
			    var json = WST.toJson(data);
			    if(json.status==1){
		            WST.msg(json.msg,{icon:1},function(){
		            	pageConfigQuery(0);
		            	layer.closeAll();
		            });
			    }else{
			    	WST.msg(json.msg,{icon:2});
			    }
			});
		}
	});
}
function delConfig(id){
    WST.confirm({content:'您确定要删除该提现账号吗？',yes:function(){
   	    var tips = WST.msg('正在提交数据，请稍后...',{time:600000000});
	    $.post(WST.U('home/cashconfigs/del'),{id:id},function(data,textStatus){
		    layer.close(tips);
			var json = WST.toJson(data);
			if(json.status==1){
		        WST.msg(json.msg,{icon:1},function(){
		            pageConfigQuery(0);
		        });
			}else{
			    WST.msg(json.msg,{icon:2});
			}
	  });
   }})
}