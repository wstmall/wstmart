var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/brands/pageQuery'),
		pageSize:100,
		pageSizeOptions:[100],
		height:'99%',
        width:'100%',
        minColToggle:6,
        rowHeight:100,
        rownumbers:true,
        columns: [
	        { display: '品牌名称', name: 'brandName',width: 200,align: 'left',isSort: false},
            { display: '品牌介绍', name: 'brandDesc',isSort: false},
            { display: '品牌图标', name: 'img',width: 300,isSort: false,
	        	render: function (imgs){
		            var h = '<span><img style="max-height:100%;" src="'+WST.conf.ROOT+"/"+imgs["brandImg"]+'" /></span>';
		            return h;
	        	}
	        },
	        { display: '操作', name: 'op',width: 200,isSort: false,
	        	render: function (rowdata){
		            var h = "";
		            if(WST.GRANT.PPGL_02)h += "<a href='javascript:toEdit("+rowdata["brandId"]+")'>修改</a> ";
		            if(WST.GRANT.PPGL_03)h += "<a href='javascript:toDel("+rowdata["brandId"]+")'>删除</a> "; 
		            return h;
	        	}
	        }
        ],
    });
}

function loadGrid(){
	grid.set('url',WST.U('admin/brands/pageQuery','key='+$('#key').val())+'&id='+$('#catId').val());
}

function toEdit(id){
	location.href=WST.U('admin/brands/toEdit','id='+id);
}

function toEdits(id){
    var params = WST.getParams('.ipt');
    params.id = id;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('admin/brands/'+((id>0)?"edit":"add")),params,function(data,textStatus){
		  layer.close(loading);
		  var json = WST.toAdminJson(data);
		  if(json.status=='1'){
		    	WST.msg(json.msg,{icon:1});
		        setTimeout(function(){ 
			    	location.href=WST.U('admin/brands/index');
		        },1000);
		  }else{
		        WST.msg(json.msg,{icon:2});
		  }
	});
}

function toDel(id){
	var box = WST.confirm({content:"您确定要删除该品牌吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/brands/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg(json.msg,{icon:1});
	           			    	layer.close(box);
	           		            grid.reload();
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}