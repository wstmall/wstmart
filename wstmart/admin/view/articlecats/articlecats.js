var grid;
function initGrid(){
	grid = $('#maingrid').WSTGridTree({
		url:WST.U('admin/articlecats/pageQuery'),
		rownumbers:true,
        columns: [
	        { display: '分类名称', name: 'catName', id:'catId', align: 'left',isSort: false},
            { display: '分类类型', width: 100, name: 'catType',isSort: false,
                render: function (item)
                {
                    if (parseInt(item.catType) == 1) return '<span>系统菜单</span>';
                    return '<span>普通类型</span>';
                }
            },
            { display: '是否显示', width: 100, name: 'isShow',isSort: false,
                render: function (item)
                {
                    return '<span id="sh_'+item['catId']+'" style="cursor:pointer;" v="'+item.isShow+'" onclick="toggleIsShow(this,'+item["catId"]+');">'+((item.isShow=='1')?"显示":"隐藏")+'</span>';
                }
            },
	        { display: '排序号', name: 'catSort',width: 100,isSort: false},
	        { display: '操作', name: 'op',width: 200,isSort: false,
	        	render: function (rowdata,e){
		            var h = "";
			        if(WST.GRANT.WZFL_01)h += "<a href='javascript:toEdit("+rowdata["catId"]+",0)'>新增子分类</a> ";
		            if(WST.GRANT.WZFL_02)h += "<a href='javascript:toEdit("+rowdata["parentId"]+","+rowdata["catId"]+")'>修改</a> ";
		            if(WST.GRANT.WZFL_03 && rowdata["catType"]==0)h += "<a href='javascript:toDel("+rowdata["parentId"]+","+rowdata["catId"]+","+rowdata["catType"]+")'>删除</a> "; 
		            return h;
	        	}}
        ]
	});
}
function toggleIsShow(obj,id){
	if(!WST.GRANT.WZFL_02)return;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    var v = ($(obj).attr('v')=='1')?0:1;
    $.post(WST.U('admin/articlecats/editiIsShow'),{id:id,isShow:v},function(data,textStatus){
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
	$('#articlecatForm')[0].reset();
	if(id>0){
		$.post(WST.U('admin/articlecats/get'),{id:id},function(data,textStatus){
			var json = WST.toAdminJson(data);
			if(json){
				WST.setValues(json);
				editsBox(id);
			}
		});
	}else{
		WST.setValues({parentId:pid,catName:'',isShow:1,catSort:0});
		editsBox(id);
	}
}

function editsBox(id){
	var title =(id>0)?"修改文章分类":"新增文章分类";
	var box = WST.open({title:title,type:1,content:$('#articlecatBox'),area: ['465px', '250px'],btn:['确定','取消'],yes:function(){
		          $('#articlecatForm').submit();
	          }});
	$('#articlecatForm').validator({
	    fields: {
	    	catName: {
	    		tip: "请输入分类名称",
	    		rule: '分类名称:required;length[~10];'
	    	},
	    	catSort: {
            	tip: "请输入排序号",
            	rule: '排序号:required;length[~8];'
            }
	    },
	    valid: function(form){
	        var params = WST.getParams('.ipt');
	        params.id = id;
	        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    		$.post(WST.U('admin/articlecats/'+((id>0)?"edit":"add")),params,function(data,textStatus){
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

function toDel(pid,id,type){
	var box = WST.confirm({content:"您确定要删除该分类以及其下的文章吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/articlecats/del'),{id:id,type:type},function(data,textStatus){
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