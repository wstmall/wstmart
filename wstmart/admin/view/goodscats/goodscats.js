var grid;
function initGrid(){	
	grid = $('#maingrid').WSTGridTree({
		url:WST.U('admin/goodscats/pageQuery'),
		pageSize:10000,
		pageSizeOptions:[10000],
		height:'99%',
        width:'100%',
        minColToggle:6,
        delayLoad :true,
        rownumbers:true,
        columns: [
	        { display: '分类名称', name: 'catName', id:'catId', align: 'left',isSort: false},
            { display: '推荐楼层', width: 100, name: 'isFloor',isSort: false,
                render: function (itemf)
                {
                    return '<span id="f_'+itemf["catId"]+'" v="'+itemf["isFloor"]+'" style="cursor:pointer;" onclick="toggleIsFloor(this,'+itemf["catId"]+');">'+((itemf["isFloor"]==1)?"推荐":"不推荐")+'</span>';
                }
            },
            { display: '是否显示', width: 100, name: 'isShow',isSort: false,
                render: function (item)
                {
                    return '<span id="sh_'+item["catId"]+'" v="'+item["isShow"]+'" style="cursor:pointer;" onclick="toggleIsShow(this,'+item["catId"]+');">'+((item["isShow"]==1)?"显示":"隐藏")+'</span>';
                }
            },
            { display: '佣金', width: 100, name: 'commissionRate',isSort: false,
                render: function (item)
                {
                    return item["commissionRate"]+'%';
                }
            },
	        { display: '排序号', name: 'catSort',width: 100,isSort: false},
	        { display: '操作', name: 'op',width: 150,isSort: false,
	        	render: function (rowdata){
		            var h = "";
			        if(WST.GRANT.SPFL_01)h += "<a href='javascript:toEdit("+rowdata["catId"]+",0)'>新增子分类</a> ";
		            if(WST.GRANT.SPFL_02)h += "<a href='javascript:toEdit("+rowdata["parentId"]+","+rowdata["catId"]+")'>修改</a> ";
		            if(WST.GRANT.SPFL_03)h += "<a href='javascript:toDel("+rowdata["parentId"]+","+rowdata["catId"]+")'>删除</a> "; 
		            return h;
	        	}}
        ]
    });
}

function toggleIsFloor(obj,id){
	if(!WST.GRANT.SPFL_02)return;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    var v = ($(obj).attr('v')=='1')?0:1;
	$.post(WST.U('admin/goodscats/editiIsFloor'),{id:id,isFloor:v},function(data,textStatus){
		  layer.close(loading);
		  var json = WST.toAdminJson(data);
		  if(json.status=='1'){
		    	WST.msg(json.msg,{icon:1});
		    	$('#f_'+id).attr('v',v).html((v==1)?"推荐":"不推荐");
				grid.reload(id);
		  }else{
		    	WST.msg(json.msg,{icon:2});
		  }
	});
}

function toggleIsShow(obj,id){
	if(!WST.GRANT.SPFL_02)return;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    var v = ($(obj).attr('v')=='1')?0:1;
    $.post(WST.U('admin/goodscats/editiIsShow'),{id:id,isShow:v},function(data,textStatus){
		layer.close(loading);
		var json = WST.toAdminJson(data);
		if(json.status=='1'){
			 WST.msg(json.msg,{icon:1});
			 $('#sh_'+id).attr('v',v).html((v==1)?"显示":"隐藏");
			 grid.reload(id);
		}else{
			 WST.msg(json.msg,{icon:2});
		}
	});
}

function toEdit(pid,id){
	$('#goodscatsForm')[0].reset();
	if(id>0){
		$.post(WST.U('admin/goodscats/get'),{id:id},function(data,textStatus){
			var json = WST.toAdminJson(data);
			if(json){
				WST.setValues(json);
				editsBox(id);
			}
		});
	}else{
		WST.setValues({parentId:pid,catName:'',isShow:1,isFloor:0,catSort:0});
		editsBox(id);
	}
}

function editsBox(id,v){
	var title =(id>0)?"修改商品分类":"新增商品分类";
	var box = WST.open({title:title,type:1,content:$('#goodscatsBox'),area: ['465px', '300px'],btn:['确定','取消'],yes:function(){
		$('#goodscatsForm').submit();
	          }});
	$('#goodscatsForm').validator({
	    fields: {
	    	catName: {
	    		tip: "请输入商品分类名称",
	    		rule: '商品分类名称:required;length[~10];'
	    	},
	    	commissionRate: {
	    		tip: "请输入分类的佣金",
	    		rule: '分类的佣金:required;'
	    	},
	    	catSort: {
            	tip: "请输入排序号",
            	rule: '排序号:required;length[~8];'
            },
	    },
	    valid: function(form){
	        var params = WST.getParams('.ipt');
	        params.id = id;
	        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    		$.post(WST.U('admin/goodscats/'+((id>0)?"edit":"add")),params,function(data,textStatus){
    			  layer.close(loading);
    			  var json = WST.toAdminJson(data);
    			  if(json.status=='1'){
    			    	WST.msg(json.msg,{icon:1});
    			    	layer.close(box);
    			    	grid.reload(params.parentId);
    			  }else{
    			        WST.msg(json.msg,{icon:2});
    			  }
    		});
	    }
	});
}

function toDel(pid,id){
	var box = WST.confirm({content:"您确定要删除该商品分类吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/goodscats/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			       WST.msg(json.msg,{icon:1});
	           			       layer.close(box);
	           		           grid.reload(pid);
	           			  }else{
	           			       WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}