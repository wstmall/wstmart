var grid;
function initGrid(){
	var parentId=$('#h_areaId').val();
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/areas/pageQuery','parentId='+parentId),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
	        { display: '地区名称', name: 'areaName', align: 'left',isSort: false},
            { display: '是否显示', width: 100, name: 'isShow',isSort: false,
                render: function (item)
                {
                    if (parseInt(item.isShow) == 1) return '<span style="cursor:pointer;" onclick="toggleIsShow('+item["isShow"]+','+item["areaId"]+');">显示</span>';
                    return '<span style="cursor:pointer;" onclick="toggleIsShow('+item["isShow"]+','+item["areaId"]+');">隐藏</span>';
                }
            },
            { display: '排序字母', width: 100, name: 'areaKey',isSort: false},
	        { display: '排序号', name: 'areaSort',width: 100,isSort: false},
	        { display: '操作', name: 'op',width: 100,isSort: false,
	        	render: function (rowdata){
		            var h = "";
		            if(rowdata["areaType"] < 3){
			            h += "<a href='"+WST.U('admin/areas/index','parentId='+rowdata["areaId"])+"'>查看</a> ";
		            }
		            if(WST.GRANT.DQGL_02)h += "<a href='javascript:toEdit("+rowdata["areaId"]+","+rowdata["parentId"]+")'>修改</a> ";
		            if(WST.GRANT.DQGL_03)h += "<a href='javascript:toDel("+rowdata["areaId"]+")'>删除</a> "; 
		            return h;
	        	}}
        ]
    });
}

function toggleIsShow(t,v){
	if(!WST.GRANT.DQGL_02)return;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    	$.post(WST.U('admin/areas/editiIsShow'),{id:v,isShow:t},function(data,textStatus){
			  layer.close(loading);
			  var json = WST.toAdminJson(data);
			  if(json.status=='1'){
			    	WST.msg(json.msg,{icon:1});
		            grid.reload();
			  }else{
			    	WST.msg(json.msg,{icon:2});
			  }
		});
}

function toReturn(){
	location.href=WST.U('admin/areas/index','parentId='+$('#h_parentId').val());
}

function letterOnblur(obj){
	if($.trim(obj.value)=='')return;
	if($('#areaKey').val()!=='')return;
	var loading = WST.msg('正在生成排序字母，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('admin/areas/letterObtain'),{code:obj.value},function(data,textStatus){
		layer.close(loading);
		var json = WST.toAdminJson(data);
		if(json.status == 1){
			$('#areaKey').val(json.msg);
		}
	});
}

function toEdit(id,pid){
	$('#areaForm')[0].reset();
	if(id>0){
		var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
		$.post(WST.U('admin/areas/get'),{id:id},function(data,textStatus){
			layer.close(loading);
			var json = WST.toAdminJson(data);
			if(json){
				WST.setValues(json);
				editsBox(id);
			}
		});
	}else{
		WST.setValues({parentId:pid,areaId:0});
		editsBox(id);
	}
}

function editsBox(id){
	var box = WST.open({title:(id>0)?'修改地区':"新增地区",type:1,content:$('#areasBox'),area: ['460px', '260px'],btn:['确定','取消'],yes:function(){
		$('#areaForm').submit();
	          }});
	$('#areaForm').validator({
	    fields: {
	    	areaName: {
	    		tip: "请输入地区名称",
	    		rule: '地区名称:required;length[~10];'
	    	},
		    areaKey: {
	    		tip: "请输入排序字母",
	    		rule: '排序字母:required;length[~1];'
	    	},
	    	areaSort: {
            	tip: "请输入排序号",
            	rule: '排序号:required;length[~8];'
            }
	    },
	    valid: function(form){
	        var params = WST.getParams('.ipt');
	        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	    		$.post(WST.U('admin/areas/'+((id>0)?"edit":"add")),params,function(data,textStatus){
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
	    }
	});
}

function toDel(id){
	var box = WST.confirm({content:"您确定要删除该地区吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/areas/del'),{id:id},function(data,textStatus){
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